import serial
import time
import requests
import datetime

# ========== CONFIGURATION ==========
SERIAL_PORT = '/dev/cu.usbmodem141011'  # Update this for your Arduino port
BAUD_RATE = 9600

# API URLs
API_BASE = "http://127.0.0.1:8000/api"

# Feeder endpoints
URL_FEEDER_STATUS = f"{API_BASE}/feeder/check-status"
URL_FEEDER_LOG = f"{API_BASE}/feeder/log"

# Temperature endpoints
URL_TEMP_LOG = f"{API_BASE}/temperature/log"
URL_TEMP_REALTIME = f"{API_BASE}/temperature/realtime"
URL_FAN_STATUS = f"{API_BASE}/temperature/fan-status"

# Light endpoints
URL_LIGHT_STATUS = f"{API_BASE}/light/status"
URL_LED_STATUS = f"{API_BASE}/light/led-status"  # Get calculated LED status (like fan)

# Track fed times to avoid duplicate feeds
fed_times = set()

# Track processed commands to avoid repeats
processed_commands = set()

last_sensor_log = datetime.datetime.now()
SENSOR_LOG_INTERVAL = 120  # Log sensor data every 2 minutes  

# Track last fan and light states to avoid redundant commands
last_fan_state = None
last_light_state = None

print("=" * 50)
print("🐔 SMART POULTRY SYSTEM STARTING")
print("=" * 50)

# ========== ARDUINO CONNECTION ==========
try:
    arduino = serial.Serial(SERIAL_PORT, BAUD_RATE, timeout=1)
    time.sleep(2)
    print("✅ Arduino Connected")
except Exception as e:
    print(f"❌ Arduino Connection Failed: {e}")
    exit()

# ========== COMMAND FUNCTIONS ==========
def activate_feeder():
    """Send feed command to Arduino (Servo motor)"""
    arduino.write(b'F')
    print("🍗 Feeder activated (Servo motor)")

def set_fan(state):
    """Control DC fan via transistor"""
    if state == 'ON':
        arduino.write(b'H')  # High - Fan ON
        print("💨 Fan ON")
    else:
        arduino.write(b'L')  # Low - Fan OFF
        print("💨 Fan OFF")

def set_light(state):
    """Control LED light via photoresistor circuit"""
    if state:
        arduino.write(b'N')  # Light ON
        print("💡 Light ON")
    else:
        arduino.write(b'M')  # Light OFF
        print("💡 Light OFF")

# ========== MAIN LOOP ==========
print("\n🔄 Main loop started...\n")

while True:
    try:
        # ========== READ SENSOR DATA FROM ARDUINO ==========
        if arduino.in_waiting > 0:
            line = arduino.readline().decode('utf-8').strip()
            
            if line.startswith("DATA:"):
                # Parse "DATA:28.50,60.00,350,1" (temp, humidity, light_level, led_state)
                parts = line.replace("DATA:", "").split(",")
                
                # Check for 4 parts now (Temp, Hum, Light, LED State)
                if len(parts) >= 3:
                    temp = float(parts[0])
                    hum = float(parts[1])
                    light_val = int(parts[2])
                    led_state = int(parts[3]) if len(parts) >= 4 else None
                    
                    led_status = "ON" if led_state == 1 else "OFF" if led_state == 0 else "?"
                    print(f"🌡  Sensor: {temp}°C | {hum}% | ☀️ Light: {light_val} | 💡 LED: {led_status}")
                    
                    # Send real-time data immediately (for live display)
                    try:
                        requests.post(
                            URL_TEMP_REALTIME,
                            json={
                                'temperature': temp,
                                'humidity': hum,
                                'light_level': light_val,
                                'led_state': led_state == 1 if led_state is not None else None
                            },
                            timeout=1
                        )
                    except:
                        pass
                    
                    # Lo
                    now = datetime.datetime.now()
                    time_diff = (now - last_sensor_log).total_seconds()
                    
                    if time_diff >= SENSOR_LOG_INTERVAL:
                        try:
                            payload = {
                                'temperature': temp, 
                                'humidity': hum
                            }
                            
                            response = requests.post(
                                URL_TEMP_LOG,
                                json=payload,
                                timeout=2
                            )
                            if response.status_code == 201:
                                print("✅ Sensor data logged to database")
                                last_sensor_log = now
                        except Exception as e:
                            print(f"❌ Failed to log sensor data: {e}")

        # ========== CHECK LARAVEL API FOR COMMANDS ==========
        try:
            # 1. Check Feeder Status
            feeder_response = requests.get(URL_FEEDER_STATUS, timeout=2).json()
            
            # Handle manual feed command (ONLY ONCE per command)
            if feeder_response.get('feed_command') == 'FEED':
                command_id = feeder_response.get('command_id')
                
                # Only process if we haven't seen this command before
                if command_id and command_id not in processed_commands:
                    print(f"🍗 Manual feed command received (ID: {command_id})")
                    activate_feeder()
                    
                    # Mark command as processed immediately
                    processed_commands.add(command_id)
                    
                    # Log the feed action
                    try:
                        requests.post(
                            URL_FEEDER_LOG,
                            json={'command_id': command_id},
                            timeout=2
                        )
                        print("✅ Feed logged")
                    except:
                        pass
                    
                    # Clean up old processed commands (keep only last 100)
                    if len(processed_commands) > 100:
                        processed_commands.clear()
            
            # Handle scheduled feeds
            schedules = feeder_response.get('schedules', [])
            now = datetime.datetime.now()
            current_time = now.strftime("%H:%M")
            
            for schedule_time in schedules:
                feed_key = f"{now.date()}_{schedule_time}"
                
                if current_time == schedule_time and feed_key not in fed_times:
                    print(f"⏰ Scheduled feed at {schedule_time}")
                    activate_feeder()
                    
                    try:
                        # Send with trigger_type to indicate scheduled feed
                        requests.post(
                            URL_FEEDER_LOG, 
                            json={'trigger_type': 'scheduled'}, 
                            timeout=2
                        )
                        print("✅ Scheduled feed logged")
                    except:
                        pass
                    
                    fed_times.add(feed_key)
            
            # Clear old entries at midnight
            if now.hour == 0 and now.minute == 0:
                fed_times.clear()
                print("🔄 Feed history cleared for new day")

            # 2. Check Fan Status (only send command if state changed)
            fan_response = requests.get(URL_FAN_STATUS, timeout=2).json()
            fan_status = fan_response.get('fan_status', 'OFF')
            
            if fan_status != last_fan_state:
                set_fan(fan_status)
                last_fan_state = fan_status

            # 3. Check LED Status (SAME LOGIC AS FAN)
            led_response = requests.get(URL_LED_STATUS, timeout=2).json()
            led_status = led_response.get('led_status', 'OFF')
            
            if led_status != last_light_state:
                set_light(led_status == 'ON')
                last_light_state = led_status

        except requests.exceptions.RequestException as e:
            print(f"⚠️  API Connection Error: {e}")
        except Exception as e:
            print(f"⚠️  Error: {e}")

        time.sleep(1)

    except KeyboardInterrupt:
        print("\n\n🛑 System shutting down...")
        arduino.close()
        print("✅ Arduino connection closed")
        break
    except Exception as e:
        print(f"❌ Unexpected error: {e}")
        time.sleep(1)
