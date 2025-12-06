<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Smart Poultry Control Panel</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen p-6">
        <div class="max-w-7xl mx-auto">
            <!-- Header -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h1 class="text-3xl font-bold text-gray-800">
                    <i class="fas fa-drumstick-bite text-orange-500"></i> Smart Poultry Control Panel
                </h1>
                <p class="text-gray-600 mt-2">Monitor and control your poultry farm remotely</p>
            </div>

            <!-- Main Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <!-- ========== FEEDER CONTROL ========== -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">
                        <i class="fas fa-utensils text-green-500"></i> Feeder Control
                    </h2>

                    <!-- Manual Feed Button -->
                    <div class="mb-6">
                        <button onclick="feedNow()" 
                                class="w-full bg-green-500 hover:bg-green-600 text-white font-bold py-4 px-6 rounded-lg transition duration-200 transform hover:scale-105">
                            <i class="fas fa-play-circle"></i> FEED NOW
                        </button>
                    </div>

                    <!-- Schedule Management -->
                    <div class="mb-4">
                        <h3 class="text-lg font-semibold mb-3">Feed Schedules</h3>
                        <div class="flex gap-2 mb-3">
                            <input type="time" id="newScheduleTime" 
                                   class="flex-1 border rounded-lg px-3 py-2">
                            <button onclick="addSchedule()" 
                                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                                <i class="fas fa-plus"></i> Add
                            </button>
                        </div>
                        <div id="scheduleList" class="space-y-2"></div>
                    </div>

                    <!-- Feed History -->
                    <div class="mt-6">
                        <h3 class="text-lg font-semibold mb-3">Recent Feeds</h3>
                        <div id="feedHistory" class="space-y-2 max-h-60 overflow-y-auto"></div>
                    </div>
                </div>

                <!-- ========== TEMPERATURE CONTROL ========== -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">
                        <i class="fas fa-thermometer-half text-red-500"></i> Temperature Control
                    </h2>

                    <!-- Real-time Sensor Data -->
                    <div class="bg-gradient-to-r from-purple-50 to-pink-50 rounded-lg p-4 mb-6 border border-purple-200">
                        <h3 class="text-sm font-semibold text-purple-700 mb-2">
                            <i class="fas fa-broadcast-tower"></i> Live Sensor Reading
                        </h3>
                        <div class="grid grid-cols-2 gap-3 text-sm">
                            <div class="flex items-center">
                                <i class="fas fa-temperature-high text-red-500 mr-2"></i>
                                <span class="text-gray-700">Temp: <strong id="liveTemp" class="text-red-600">--</strong>°C</span>
                            </div>
                            <div class="flex items-center">
                                <i class="fas fa-tint text-blue-500 mr-2"></i>
                                <span class="text-gray-700">Humidity: <strong id="liveHumidity" class="text-blue-600">--</strong>%</span>
                            </div>
                        </div>
                        <div class="mt-2 text-xs text-gray-500 flex items-center">
                            <span class="inline-block w-2 h-2 bg-green-500 rounded-full mr-2 animate-pulse"></span>
                            <span id="liveUpdateTime">Waiting for data...</span>
                        </div>
                    </div>

                    <!-- Threshold Setting -->
                    <div class="mb-4">
                        <label class="block text-sm font-semibold mb-2">Temperature Threshold (°C)</label>
                        <div class="flex gap-2">
                            <input type="number" id="thresholdInput" 
                                   step="0.5" min="0" max="50"
                                   class="flex-1 border rounded-lg px-3 py-2"
                                   onfocus="isUpdatingThreshold = true"
                                   onblur="setTimeout(() => isUpdatingThreshold = false, 500)">
                            <button onclick="updateThreshold()" 
                                    class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg">
                                Set
                            </button>
                        </div>
                        <p class="text-sm text-gray-500 mt-1">Fan will auto-start when temp exceeds this value</p>
                    </div>

                    <!-- Fan Manual Override -->
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-semibold">Fan Manual Override</h3>
                                <p class="text-sm text-gray-500">Force fan ON regardless of temperature</p>
                            </div>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" id="fanOverride" class="sr-only peer" onchange="toggleFanOverride()">
                                <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-blue-600"></div>
                            </label>
                        </div>
                        <div id="fanStatus" class="mt-2 text-sm font-semibold"></div>
                    </div>
                </div>

                <!-- ========== LIGHT CONTROL ========== -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">
                        <i class="fas fa-lightbulb text-yellow-500"></i> Light Control
                    </h2>

                    <div class="text-center">
                        <button onclick="toggleLight()" id="lightButton"
                                class="w-full py-12 rounded-lg transition duration-200 transform hover:scale-105 text-3xl font-bold">
                            <i class="fas fa-lightbulb text-6xl mb-4"></i>
                            <div id="lightStatus">Loading...</div>
                        </button>
                    </div>
                </div>

                <!-- ========== SYSTEM STATUS ========== -->
                <div class="bg-white rounded-lg shadow-md p-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">
                        <i class="fas fa-info-circle text-blue-500"></i> System Status
                    </h2>

                    <div class="space-y-3">
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="font-semibold">Arduino Connection</span>
                            <span class="text-green-600"><i class="fas fa-check-circle"></i> Connected</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="font-semibold">Last Sensor Update</span>
                            <span id="lastUpdate">--</span>
                        </div>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <span class="font-semibold">Total Feeds Today</span>
                            <span id="feedsToday" class="font-bold text-blue-600">0</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // ========== API CONFIGURATION ==========
        const API_BASE = '/api';
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').content;

        // ========== FEEDER FUNCTIONS ==========
        async function feedNow() {
            try {
                const response = await fetch(`${API_BASE}/feeder/feed-now`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    }
                });
                
                if (response.ok) {
                    alert('✅ Feed command sent!');
                    loadFeedHistory();
                }
            } catch (error) {
                console.error('Error:', error);
                alert('❌ Failed to send feed command');
            }
        }

        async function loadSchedules() {
            try {
                const response = await fetch(`${API_BASE}/feeder/schedules`);
                const schedules = await response.json();
                
                const list = document.getElementById('scheduleList');
                list.innerHTML = schedules.map(s => `
                    <div class="flex items-center justify-between p-2 bg-gray-50 rounded">
                        <span class="font-semibold">${s.feed_time.substring(0, 5)}</span>
                        <div>
                            <label class="mr-2">
                                <input type="checkbox" ${s.is_active ? 'checked' : ''} 
                                       onchange="toggleSchedule(${s.id})">
                                Active
                            </label>
                            <button onclick="deleteSchedule(${s.id})" 
                                    class="text-red-500 hover:text-red-700">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `).join('');
            } catch (error) {
                console.error('Error loading schedules:', error);
            }
        }

        async function addSchedule() {
            const time = document.getElementById('newScheduleTime').value;
            if (!time) {
                alert('Please select a time');
                return;
            }

            try {
                const response = await fetch(`${API_BASE}/feeder/schedules`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                    body: JSON.stringify({ feed_time: time })
                });

                if (response.ok) {
                    document.getElementById('newScheduleTime').value = '';
                    loadSchedules();
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }

        async function toggleSchedule(id) {
            try {
                await fetch(`${API_BASE}/feeder/schedules/${id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                    body: JSON.stringify({ is_active: event.target.checked })
                });
            } catch (error) {
                console.error('Error:', error);
            }
        }

        async function deleteSchedule(id) {
            if (confirm('Delete this schedule?')) {
                try {
                    await fetch(`${API_BASE}/feeder/schedules/${id}`, {
                        method: 'DELETE',
                        headers: { 'X-CSRF-TOKEN': CSRF_TOKEN }
                    });
                    loadSchedules();
                } catch (error) {
                    console.error('Error:', error);
                }
            }
        }

        async function loadFeedHistory() {
            try {
                const response = await fetch(`${API_BASE}/feeder/history?days=1`);
                const history = await response.json();
                
                const list = document.getElementById('feedHistory');
                list.innerHTML = history.slice(0, 5).map(h => {
                    const time = new Date(h.fed_at).toLocaleTimeString();
                    const type = h.trigger_type === 'manual' ? '👆 Manual' : '⏰ Scheduled';
                    return `
                        <div class="p-2 bg-gray-50 rounded text-sm">
                            <span class="font-semibold">${time}</span> - ${type}
                        </div>
                    `;
                }).join('');

                document.getElementById('feedsToday').textContent = history.length;
            } catch (error) {
                console.error('Error loading history:', error);
            }
        }

        // ========== TEMPERATURE FUNCTIONS ==========
        let isUpdatingThreshold = false; // Prevent overwriting user input
        
        async function loadTemperatureData() {
            try {
                const [settingsRes, readingRes] = await Promise.all([
                    fetch(`${API_BASE}/temperature/settings`),
                    fetch(`${API_BASE}/temperature/latest`)
                ]);

                const settings = await settingsRes.json();
                const reading = await readingRes.json();

                if (reading) {
                    // Update live reading section
                    document.getElementById('liveTemp').textContent = reading.temperature.toFixed(1);
                    document.getElementById('liveHumidity').textContent = reading.humidity.toFixed(1);
                    document.getElementById('liveUpdateTime').textContent = 'Updated: ' + new Date(reading.recorded_at).toLocaleTimeString();
                    
                    document.getElementById('lastUpdate').textContent = new Date(reading.recorded_at).toLocaleTimeString();
                }

                // Only update threshold input if user is not currently editing it
                const thresholdInput = document.getElementById('thresholdInput');
                if (!isUpdatingThreshold && document.activeElement !== thresholdInput) {
                    thresholdInput.value = settings.threshold_temperature;
                }
                
                document.getElementById('fanOverride').checked = settings.fan_override;
                
                updateFanStatus();
            } catch (error) {
                console.error('Error loading temperature data:', error);
            }
        }

        async function updateThreshold() {
            const threshold = document.getElementById('thresholdInput').value;
            
            try {
                const response = await fetch(`${API_BASE}/temperature/settings`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                    body: JSON.stringify({ threshold_temperature: parseFloat(threshold) })
                });
                
                if (response.ok) {
                    alert('✅ Threshold updated to ' + threshold + '°C');
                    // Don't reload settings immediately to prevent overwriting
                }
            } catch (error) {
                console.error('Error:', error);
                alert('❌ Failed to update threshold');
            }
        }

        async function toggleFanOverride() {
            const override = document.getElementById('fanOverride').checked;
            
            try {
                await fetch(`${API_BASE}/temperature/settings`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                    body: JSON.stringify({ fan_override: override })
                });
                
                updateFanStatus();
            } catch (error) {
                console.error('Error:', error);
            }
        }

        async function updateFanStatus() {
            try {
                const response = await fetch(`${API_BASE}/temperature/fan-status`);
                const data = await response.json();
                
                const statusDiv = document.getElementById('fanStatus');
                if (data.fan_status === 'ON') {
                    statusDiv.innerHTML = '<span class="text-green-600"><i class="fas fa-fan fa-spin"></i> Fan is ON</span>';
                } else {
                    statusDiv.innerHTML = '<span class="text-gray-600"><i class="fas fa-fan"></i> Fan is OFF</span>';
                }
            } catch (error) {
                console.error('Error:', error);
            }
        }

        // ========== LIGHT FUNCTIONS ==========
        async function loadLightStatus() {
            try {
                const response = await fetch(`${API_BASE}/light/status`);
                const data = await response.json();
                
                updateLightUI(data.is_on);
            } catch (error) {
                console.error('Error loading light status:', error);
            }
        }

        async function toggleLight() {
            try {
                const response = await fetch(`${API_BASE}/light/toggle`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    }
                });
                
                const data = await response.json();
                updateLightUI(data.is_on);
            } catch (error) {
                console.error('Error:', error);
            }
        }

        function updateLightUI(isOn) {
            const button = document.getElementById('lightButton');
            const status = document.getElementById('lightStatus');
            
            if (isOn) {
                button.className = 'w-full py-12 rounded-lg transition duration-200 transform hover:scale-105 text-3xl font-bold bg-yellow-400 text-white';
                status.textContent = 'LIGHT ON';
            } else {
                button.className = 'w-full py-12 rounded-lg transition duration-200 transform hover:scale-105 text-3xl font-bold bg-gray-300 text-gray-700';
                status.textContent = 'LIGHT OFF';
            }
        }

        // ========== INITIALIZATION ==========
        function init() {
            loadSchedules();
            loadFeedHistory();
            loadTemperatureData();
            loadLightStatus();

            // Refresh data every 2 seconds for more responsive updates
            setInterval(() => {
                loadFeedHistory();
                loadTemperatureData();
                loadLightStatus();
                updateFanStatus();
            }, 2000);

            // Refresh feed history every 30 seconds
            setInterval(loadFeedHistory, 30000);
        }

        // Start on page load
        init();
    </script>
</body>
</html>
