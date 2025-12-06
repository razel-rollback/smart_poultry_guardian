#!/bin/bash

# Smart Poultry System - Quick Start Script
# Run this from the project root directory

echo "================================================"
echo "🐔 SMART POULTRY SYSTEM - SETUP WIZARD"
echo "================================================"
echo ""

# Check if we're in the right directory
if [ ! -d "laravel_app" ] || [ ! -d "python_bridge" ] || [ ! -d "arduino_code" ]; then
    echo "❌ Error: Please run this script from the project root directory"
    exit 1
fi

echo "📋 Pre-flight Checklist:"
echo "  ✓ Laravel app found"
echo "  ✓ Python bridge found"
echo "  ✓ Arduino code found"
echo ""

# Step 1: Database Setup
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "Step 1: Database Setup"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
read -p "Run database migrations? (y/n) " -n 1 -r
echo ""
if [[ $REPLY =~ ^[Yy]$ ]]; then
    cd laravel_app
    echo "🔧 Running migrations..."
    php artisan migrate
    echo "✅ Database setup complete!"
    cd ..
else
    echo "⏭  Skipped database setup"
fi
echo ""

# Step 2: Check Python dependencies
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "Step 2: Python Dependencies"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
read -p "Install Python dependencies? (y/n) " -n 1 -r
echo ""
if [[ $REPLY =~ ^[Yy]$ ]]; then
    echo "📦 Installing pyserial and requests..."
    pip install pyserial requests
    echo "✅ Python dependencies installed!"
else
    echo "⏭  Skipped Python dependencies"
fi
echo ""

# Step 3: Configure Serial Port
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "Step 3: Arduino Serial Port Configuration"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "Available serial ports:"
ls /dev/cu.* 2>/dev/null || ls /dev/ttyACM* /dev/ttyUSB* 2>/dev/null || echo "No ports found"
echo ""
echo "Current port in bridge.py:"
grep "SERIAL_PORT" python_bridge/bridge.py
echo ""
read -p "Update serial port? (y/n) " -n 1 -r
echo ""
if [[ $REPLY =~ ^[Yy]$ ]]; then
    read -p "Enter port (e.g., /dev/cu.usbmodem141011): " PORT
    sed -i.bak "s|SERIAL_PORT = .*|SERIAL_PORT = '$PORT'|" python_bridge/bridge.py
    echo "✅ Serial port updated to: $PORT"
else
    echo "⏭  Keeping current serial port"
fi
echo ""

# Step 4: Arduino Upload Reminder
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "Step 4: Arduino Upload"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "⚠️  Don't forget to upload the Arduino sketch!"
echo ""
echo "1. Open Arduino IDE"
echo "2. File > Open > arduino_code/smart_poultry.ino"
echo "3. Install libraries:"
echo "   - DHT sensor library (by Adafruit)"
echo "4. Select your board and port"
echo "5. Click Upload"
echo ""
read -p "Press Enter when Arduino is uploaded..." 
echo ""

# Step 5: Start the System
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "Step 5: Launch System"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo ""
echo "The system needs 2 terminals:"
echo ""
echo "Terminal 1 (Laravel):"
echo "  cd laravel_app && php artisan serve"
echo ""
echo "Terminal 2 (Python Bridge):"
echo "  cd python_bridge && python bridge.py"
echo ""
read -p "Start Laravel server now? (y/n) " -n 1 -r
echo ""
if [[ $REPLY =~ ^[Yy]$ ]]; then
    echo "🚀 Starting Laravel server..."
    cd laravel_app
    php artisan serve &
    LARAVEL_PID=$!
    cd ..
    echo "✅ Laravel running on http://localhost:8000 (PID: $LARAVEL_PID)"
    echo ""
    
    sleep 2
    
    read -p "Start Python bridge now? (y/n) " -n 1 -r
    echo ""
    if [[ $REPLY =~ ^[Yy]$ ]]; then
        echo "🐍 Starting Python bridge..."
        cd python_bridge
        python bridge.py
    else
        echo "⏭  Skipped Python bridge"
        echo "💡 To start manually: cd python_bridge && python bridge.py"
    fi
else
    echo "⏭  Skipped auto-start"
    echo ""
    echo "📝 Manual Start Commands:"
    echo "  Terminal 1: cd laravel_app && php artisan serve"
    echo "  Terminal 2: cd python_bridge && python bridge.py"
fi

echo ""
echo "================================================"
echo "✅ Setup Complete!"
echo "================================================"
echo ""
echo "🌐 Open your browser: http://localhost:8000"
echo ""
echo "📖 For more details, see README.md"
echo ""
