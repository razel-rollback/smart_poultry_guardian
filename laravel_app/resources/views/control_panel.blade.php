<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Smart Poultry Guardian</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700;800&display=swap');
        
        * {
            font-family: 'Poppins', sans-serif;
        }
        
        body {
            background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
            min-height: 100vh;
        }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.98);
            backdrop-filter: blur(10px);
            border-radius: 24px;
            box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
            border: 1px solid rgba(220, 38, 38, 0.1);
            transition: all 0.3s ease;
        }
        
        .glass-card:hover {
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.12);
            border-color: rgba(220, 38, 38, 0.2);
        }
        
        .gradient-header {
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(220, 38, 38, 0.4);
        }
        
        .stat-card {
            background: rgba(255, 255, 255, 0.05);
            border-radius: 20px;
            padding: 28px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }
        
        .stat-card:hover {
            background: rgba(255, 255, 255, 0.08);
            border-color: rgba(220, 38, 38, 0.4);
            transform: translateY(-2px);
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            transition: all 0.3s ease;
        }
        
        .btn-primary:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 30px rgba(220, 38, 38, 0.4);
        }
        
        .toggle-switch {
            position: relative;
            width: 60px;
            height: 32px;
        }
        
        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }
        
        .toggle-slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: #cbd5e1;
            border-radius: 32px;
            transition: 0.4s;
        }
        
        .toggle-slider:before {
            position: absolute;
            content: "";
            height: 24px;
            width: 24px;
            left: 4px;
            bottom: 4px;
            background: white;
            border-radius: 50%;
            transition: 0.4s;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }
        
        input:checked + .toggle-slider {
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
        }
        
        input:checked + .toggle-slider:before {
            transform: translateX(28px);
        }
        
        .pulse-animation {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: .5;
            }
        }
        
        .icon-gradient {
            background: linear-gradient(135deg, #dc2626 0%, #991b1b 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        .notification-panel {
            position: fixed;
            top: 80px;
            right: 20px;
            width: 380px;
            max-height: 600px;
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            display: none;
            overflow: hidden;
        }
        
        .notification-panel.show {
            display: block;
            animation: slideIn 0.3s ease;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .notification-item {
            padding: 16px;
            border-bottom: 1px solid #f3f4f6;
            cursor: pointer;
            transition: background 0.2s;
        }
        
        .notification-item:hover {
            background: #f9fafb;
        }
        
        .notification-item.unread {
            background: #fef2f2;
        }
        
        .notification-badge {
            position: absolute;
            top: -4px;
            right: -4px;
            background: #dc2626;
            color: white;
            font-size: 11px;
            font-weight: bold;
            padding: 2px 6px;
            border-radius: 10px;
            min-width: 18px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="min-h-screen p-4 md:p-8">
        <div class="max-w-7xl mx-auto">
            <!-- Modern Header -->
            <div class="gradient-header p-8 mb-8 flex items-center justify-between">
                <div class="flex items-center gap-5">
                    <div class="bg-white/10 backdrop-blur-lg rounded-2xl p-4">
                        <i class="fas fa-shield-halved text-white text-4xl"></i>
                    </div>
                    <div>
                        <h1 class="text-4xl font-bold text-white tracking-tight">
                            Smart Poultry Guardian
                        </h1>
                        <p class="text-white/70 text-sm mt-2">Real-time Monitoring & Control System</p>
                    </div>
                </div>
                <div class="flex items-center gap-3">
                    <button onclick="toggleNotificationPanel()" class="relative bg-white/10 backdrop-blur-lg hover:bg-white/20 rounded-full w-12 h-12 flex items-center justify-center transition-all">
                        <i class="fas fa-bell text-white text-lg"></i>
                        <span id="notificationBadge" class="notification-badge" style="display: none;">0</span>
                    </button>
                    <button class="bg-white/10 backdrop-blur-lg hover:bg-white/20 rounded-full w-12 h-12 flex items-center justify-center transition-all">
                        <i class="fas fa-cog text-white text-lg"></i>
                    </button>
                </div>
            </div>

            <!-- Notification Panel -->
            <div id="notificationPanel" class="notification-panel">
                <div class="bg-gradient-to-r from-red-600 to-red-800 p-4 text-white">
                    <div class="flex items-center justify-between">
                        <h3 class="font-bold text-lg">Notifications</h3>
                        <button onclick="clearAllNotifications()" class="text-white/80 hover:text-white text-sm">
                            Clear All
                        </button>
                    </div>
                </div>
                <div id="notificationList" class="overflow-y-auto" style="max-height: 500px;">
                    <div class="p-8 text-center text-gray-400">
                        <i class="fas fa-bell-slash text-4xl mb-2"></i>
                        <p>No notifications</p>
                    </div>
                </div>
            </div>

            <!-- Stats Overview -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Temperature with Chart -->
                <div class="stat-card">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="bg-red-500/10 rounded-xl p-3">
                                <i class="fas fa-temperature-high text-red-500 text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="text-white/60 text-xs font-medium uppercase tracking-wider">Temperature</h3>
                                <p class="text-3xl font-bold text-white mt-1" id="tempDisplay">--°C</p>
                            </div>
                        </div>
                        <div class="bg-red-500 px-3 py-1 rounded-full">
                            <span class="text-xs font-bold text-white">LIVE</span>
                        </div>
                    </div>
                    <div class="bg-black/20 rounded-2xl p-4 border border-white/5">
                        <canvas id="tempChart" height="100"></canvas>
                    </div>
                </div>

                <!-- Humidity with Chart -->
                <div class="stat-card">
                    <div class="flex items-center justify-between mb-4">
                        <div class="flex items-center gap-3">
                            <div class="bg-red-500/10 rounded-xl p-3">
                                <i class="fas fa-tint text-red-500 text-2xl"></i>
                            </div>
                            <div>
                                <h3 class="text-white/60 text-xs font-medium uppercase tracking-wider">Humidity</h3>
                                <p class="text-3xl font-bold text-white mt-1" id="humidityDisplay">--%</p>
                            </div>
                        </div>
                        <div class="bg-red-500 px-3 py-1 rounded-full">
                            <span class="text-xs font-bold text-white">LIVE</span>
                        </div>
                    </div>
                    <div class="bg-black/20 rounded-2xl p-4 border border-white/5">
                        <canvas id="humidityChart" height="100"></canvas>
                    </div>
                </div>

                <!-- Fan Status -->
                <div class="stat-card">
                    <div class="flex items-center gap-3">
                        <div class="bg-red-500/10 rounded-xl p-3">
                            <i class="fas fa-fan text-red-500 text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-white/60 text-xs font-medium uppercase tracking-wider">Fan Status</h3>
                            <p class="text-2xl font-bold text-white mt-1" id="fanStatusDisplay">Checking...</p>
                        </div>
                    </div>
                </div>

                <!-- Light Status -->
                <div class="stat-card">
                    <div class="flex items-center gap-3">
                        <div class="bg-red-500/10 rounded-xl p-3">
                            <i class="fas fa-lightbulb text-red-500 text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="text-white/60 text-xs font-medium uppercase tracking-wider">Light Status</h3>
                            <p class="text-2xl font-bold text-white mt-1" id="lightStatusDisplay">Checking...</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main Control Grid -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                
                <!-- Feeder Control Card -->
                <div class="glass-card p-8">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="bg-red-500 rounded-2xl p-4">
                            <i class="fas fa-utensils text-white text-3xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">Feeder Control</h2>
                            <p class="text-gray-500 text-sm mt-1">Automated feeding system</p>
                        </div>
                    </div>

                    <!-- Manual Feed Button -->
                    <button onclick="feedNow()" 
                            class="w-full btn-primary text-white font-bold py-5 px-6 rounded-2xl mb-8 shadow-md hover:shadow-lg transition text-lg">
                        <i class="fas fa-play-circle mr-2"></i> FEED NOW
                    </button>

                    <!-- Schedule Management -->
                    <div class="mb-8">
                        <h3 class="text-base font-bold mb-4 text-gray-900 uppercase tracking-wide">Feed Schedules</h3>
                        <div class="flex gap-2 mb-4">
                            <input type="time" id="newScheduleTime" 
                                   class="flex-1 border-2 border-gray-200 rounded-xl px-4 py-3 focus:border-red-500 focus:outline-none transition">
                            <button onclick="addSchedule()" 
                                    class="bg-gradient-to-r from-red-600 to-red-800 hover:from-red-700 hover:to-red-900 text-white px-6 py-3 rounded-xl font-medium transition">
                                <i class="fas fa-plus mr-1"></i> Add
                            </button>
                        </div>
                        <div id="scheduleList" class="space-y-2 max-h-48 overflow-y-auto"></div>
                    </div>

                    <!-- Feed History -->
                    <div>
                        <h3 class="text-base font-bold mb-4 text-gray-900 uppercase tracking-wide">Recent Activity</h3>
                        <div id="feedHistory" class="space-y-3 max-h-48 overflow-y-auto pr-2" style="scrollbar-width: thin; scrollbar-color: #dc2626 transparent;"></div>
                    </div>
                </div>

                <!-- Temperature Control Card -->
                <div class="glass-card p-8">
                    <div class="flex items-center gap-4 mb-8">
                        <div class="bg-red-500 rounded-2xl p-4">
                            <i class="fas fa-thermometer-half text-white text-3xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-900">Climate Control</h2>
                            <p class="text-gray-500 text-sm mt-1">Temperature & humidity management</p>
                        </div>
                    </div>

                    <!-- Live Sensor Reading -->
                    <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-2xl p-6 mb-8 border border-red-200">
                        <div class="flex items-center gap-2 mb-4">
                            <span class="inline-block w-2.5 h-2.5 bg-red-500 rounded-full pulse-animation"></span>
                            <h3 class="text-xs font-bold text-red-700 uppercase tracking-wider">Live Sensor Reading</h3>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-white rounded-xl p-4">
                                <div class="flex items-center gap-2 mb-1">
                                    <i class="fas fa-temperature-high text-red-500"></i>
                                    <span class="text-xs text-gray-600 font-medium">Temperature</span>
                                </div>
                                <p class="text-2xl font-bold text-red-600" id="liveTemp">--°C</p>
                            </div>
                            <div class="bg-white rounded-xl p-4">
                                <div class="flex items-center gap-2 mb-1">
                                    <i class="fas fa-tint text-red-500"></i>
                                    <span class="text-xs text-gray-600 font-medium">Humidity</span>
                                </div>
                                <p class="text-2xl font-bold text-red-600" id="liveHumidity">--%</p>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-3" id="liveUpdateTime">Waiting for data...</p>
                    </div>

                    <!-- Temperature Threshold -->
                    <div class="mb-6">
                        <label class="block text-sm font-bold mb-3 text-gray-700">Temperature Threshold</label>
                        <div class="flex gap-2">
                            <input type="number" id="thresholdInput" 
                                   step="0.5" min="0" max="50"
                                   class="flex-1 border-2 border-gray-200 rounded-xl px-4 py-3 focus:border-red-500 focus:outline-none transition"
                                   placeholder="Enter threshold"
                                   onfocus="isUpdatingThreshold = true"
                                   onblur="setTimeout(() => isUpdatingThreshold = false, 500)">
                            <button onclick="updateThreshold()" 
                                    class="bg-gradient-to-r from-red-600 to-red-800 hover:from-red-700 hover:to-red-900 text-white px-6 py-3 rounded-xl font-medium transition">
                                Set
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">🌡️ Fan will activate when temperature exceeds this value</p>
                    </div>

                    <!-- Humidity Threshold -->
                    <div class="mb-6">
                        <label class="block text-sm font-bold mb-3 text-gray-700">Humidity Threshold</label>
                        <div class="flex gap-2">
                            <input type="number" id="humidityThresholdInput" 
                                   step="1" min="0" max="100"
                                   class="flex-1 border-2 border-gray-200 rounded-xl px-4 py-3 focus:border-red-500 focus:outline-none transition"
                                   placeholder="Enter humidity threshold"
                                   onfocus="isUpdatingHumidityThreshold = true"
                                   onblur="setTimeout(() => isUpdatingHumidityThreshold = false, 500)">
                            <button onclick="updateHumidityThreshold()" 
                                    class="bg-gradient-to-r from-red-600 to-red-800 hover:from-red-700 hover:to-red-900 text-white px-6 py-3 rounded-xl font-medium transition">
                                Set
                            </button>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">💧 Alert when humidity exceeds this value</p>
                    </div>

                    <!-- Fan Manual Override -->
                    <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-2xl p-5 border-2 border-gray-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="font-bold text-gray-800 mb-1">Fan Manual Override</h3>
                                <p class="text-sm text-gray-600">Force fan ON regardless of temperature</p>
                            </div>
                            <label class="toggle-switch">
                                <input type="checkbox" id="fanOverride" onchange="toggleFanOverride()">
                                <span class="toggle-slider"></span>
                            </label>
                        </div>
                        <div id="fanStatus" class="mt-4 text-sm font-bold"></div>
                    </div>
                </div>

                <!-- Light Control Card -->
                <div class="glass-card p-6 md:p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="bg-gradient-to-br from-red-600 to-red-800 rounded-xl p-3">
                            <i class="fas fa-lightbulb text-white text-2xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800">Light Control</h2>
                            <p class="text-gray-500 text-sm">Poultry house illumination</p>
                        </div>
                    </div>

                    <button onclick="toggleLight()" id="lightButton"
                            class="w-full py-16 rounded-2xl transition-all duration-300 transform hover:scale-[1.02] text-4xl font-bold shadow-lg">
                        <i class="fas fa-lightbulb text-7xl mb-4 block"></i>
                        <div id="lightStatus" class="text-2xl">Loading...</div>
                    </button>
                </div>

                <!-- System Status Card -->
                <div class="glass-card p-6 md:p-8">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="bg-gradient-to-br from-red-600 to-red-800 rounded-xl p-3">
                            <i class="fas fa-chart-line text-white text-2xl"></i>
                        </div>
                        <div>
                            <h2 class="text-2xl font-bold text-gray-800">System Status</h2>
                            <p class="text-gray-500 text-sm">Real-time system monitoring</p>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl p-4 border-2 border-red-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="bg-red-600 rounded-full w-10 h-10 flex items-center justify-center">
                                        <i class="fas fa-wifi text-white"></i>
                                    </div>
                                    <span class="font-bold text-gray-800">Arduino Connection</span>
                                </div>
                                <span class="text-red-600 font-bold flex items-center gap-2">
                                    <span class="w-2 h-2 bg-red-500 rounded-full pulse-animation"></span>
                                    Connected
                                </span>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-4 border-2 border-gray-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="bg-gray-800 rounded-full w-10 h-10 flex items-center justify-center">
                                        <i class="fas fa-clock text-white"></i>
                                    </div>
                                    <span class="font-bold text-gray-800">Last Sensor Update</span>
                                </div>
                                <span class="font-bold text-gray-800" id="lastUpdate">--</span>
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-red-50 to-red-100 rounded-xl p-4 border-2 border-red-200">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <div class="bg-red-600 rounded-full w-10 h-10 flex items-center justify-center">
                                        <i class="fas fa-utensils text-white"></i>
                                    </div>
                                    <span class="font-bold text-gray-800">Total Feeds Today</span>
                                </div>
                                <span class="font-bold text-red-600 text-2xl" id="feedsToday">0</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // ========== API CONFIGURATION ==========
        const API_BASE = '/api';
        const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"').content;

        // ========== NOTIFICATION SYSTEM ==========
        let notifications = [];
        let autoActionTimers = {};
        let lastCheckedValues = {
            temperature: null,
            humidity: null,
            lightStatus: null
        };

        function toggleNotificationPanel() {
            const panel = document.getElementById('notificationPanel');
            panel.classList.toggle('show');
            if (panel.classList.contains('show')) {
                markAllAsRead();
            }
        }

        function addNotification(type, message, action, targetElement) {
            const notification = {
                id: Date.now(),
                type: type,
                message: message,
                action: action,
                targetElement: targetElement,
                timestamp: new Date(),
                read: false
            };
            
            notifications.unshift(notification);
            updateNotificationUI();
            
            // Show browser notification if permitted
            if (Notification.permission === 'granted') {
                new Notification('Smart Poultry Guardian', {
                    body: message,
                    icon: '/favicon.ico',
                    badge: '/favicon.ico'
                });
            }
        }

        function updateNotificationUI() {
            const badge = document.getElementById('notificationBadge');
            const list = document.getElementById('notificationList');
            
            const unreadCount = notifications.filter(n => !n.read).length;
            
            if (unreadCount > 0) {
                badge.textContent = unreadCount;
                badge.style.display = 'flex';
            } else {
                badge.style.display = 'none';
            }
            
            if (notifications.length === 0) {
                list.innerHTML = `
                    <div class="p-8 text-center text-gray-400">
                        <i class="fas fa-bell-slash text-4xl mb-2"></i>
                        <p>No notifications</p>
                    </div>
                `;
            } else {
                list.innerHTML = notifications.map(n => {
                    const timeAgo = getTimeAgo(n.timestamp);
                    const iconMap = {
                        'feeder': 'utensils',
                        'temperature': 'temperature-high',
                        'humidity': 'tint',
                        'light': 'lightbulb',
                        'fan': 'fan'
                    };
                    const icon = iconMap[n.type] || 'bell';
                    
                    return `
                        <div class="notification-item ${n.read ? '' : 'unread'}" onclick="handleNotificationClick('${n.targetElement}', ${n.id})">
                            <div class="flex gap-3">
                                <div class="bg-red-100 rounded-full w-10 h-10 flex items-center justify-center flex-shrink-0">
                                    <i class="fas fa-${icon} text-red-600"></i>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm text-gray-800 font-medium">${n.message}</p>
                                    <p class="text-xs text-gray-500 mt-1">${timeAgo}</p>
                                </div>
                            </div>
                        </div>
                    `;
                }).join('');
            }
        }

        function handleNotificationClick(targetElement, notificationId) {
            // Mark as read
            const notification = notifications.find(n => n.id === notificationId);
            if (notification) notification.read = true;
            updateNotificationUI();
            
            // Close notification panel
            document.getElementById('notificationPanel').classList.remove('show');
            
            // Scroll to target element
            const element = document.getElementById(targetElement);
            if (element) {
                element.scrollIntoView({ behavior: 'smooth', block: 'center' });
                element.classList.add('ring-4', 'ring-red-500', 'ring-opacity-50');
                setTimeout(() => {
                    element.classList.remove('ring-4', 'ring-red-500', 'ring-opacity-50');
                }, 2000);
            }
        }

        function markAllAsRead() {
            notifications.forEach(n => n.read = true);
            updateNotificationUI();
        }

        function clearAllNotifications() {
            notifications = [];
            updateNotificationUI();
        }

        function getTimeAgo(date) {
            const seconds = Math.floor((new Date() - date) / 1000);
            if (seconds < 60) return 'Just now';
            const minutes = Math.floor(seconds / 60);
            if (minutes < 60) return `${minutes}m ago`;
            const hours = Math.floor(minutes / 60);
            if (hours < 24) return `${hours}h ago`;
            return `${Math.floor(hours / 24)}d ago`;
        }

        function scheduleAutoAction(actionKey, callback, delayMs = 60000) {
            // Cancel existing timer if any
            if (autoActionTimers[actionKey]) {
                clearTimeout(autoActionTimers[actionKey]);
            }
            
            // Schedule new action
            autoActionTimers[actionKey] = setTimeout(() => {
                callback();
                delete autoActionTimers[actionKey];
            }, delayMs);
        }

        function cancelAutoAction(actionKey) {
            if (autoActionTimers[actionKey]) {
                clearTimeout(autoActionTimers[actionKey]);
                delete autoActionTimers[actionKey];
            }
        }

        // Request notification permission on load
        if ('Notification' in window && Notification.permission === 'default') {
            Notification.requestPermission();
        }

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
                    addNotification('feeder', '🍗 Feed has been released successfully!', null, 'feedHistory');
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
                    <div class="flex items-center justify-between p-3 bg-gradient-to-r from-red-50 to-red-100 rounded-xl border border-red-200">
                        <span class="font-bold text-gray-800">${s.feed_time.substring(0, 5)}</span>
                        <div class="flex items-center gap-3">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" ${s.is_active ? 'checked' : ''} 
                                       onchange="toggleSchedule(${s.id})"
                                       class="w-4 h-4 text-red-600 rounded focus:ring-2 focus:ring-red-500">
                                <span class="text-sm font-medium text-gray-700">Active</span>
                            </label>
                            <button onclick="deleteSchedule(${s.id})" 
                                    class="text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg p-2 transition">
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
                    const bgColor = h.trigger_type === 'manual' ? 'from-red-50 to-red-100 border-red-200' : 'from-gray-50 to-gray-100 border-gray-200';
                    return `
                        <div class="p-3 bg-gradient-to-r ${bgColor} rounded-xl text-sm border">
                            <div class="flex items-center justify-between">
                                <span class="font-bold text-gray-800">${time}</span>
                                <span class="text-gray-600">${type}</span>
                            </div>
                        </div>
                    `;
                }).join('');

                document.getElementById('feedsToday').textContent = history.length;
            } catch (error) {
                console.error('Error loading history:', error);
            }
        }

        // ========== TEMPERATURE FUNCTIONS ==========
        let isUpdatingThreshold = false;
        let isUpdatingHumidityThreshold = false;
        
        async function loadTemperatureData() {
            try {
                const [settingsRes, readingRes] = await Promise.all([
                    fetch(`${API_BASE}/temperature/settings`),
                    fetch(`${API_BASE}/temperature/latest`)
                ]);

                const settings = await settingsRes.json();
                const reading = await readingRes.json();

                if (reading) {
                    // Update stats display
                    document.getElementById('tempDisplay').textContent = reading.temperature.toFixed(1) + '°C';
                    document.getElementById('humidityDisplay').textContent = reading.humidity.toFixed(1) + '%';
                    
                    // Update live reading section
                    document.getElementById('liveTemp').textContent = reading.temperature.toFixed(1) + '°C';
                    document.getElementById('liveHumidity').textContent = reading.humidity.toFixed(1) + '%';
                    document.getElementById('liveUpdateTime').textContent = '🕐 Updated: ' + new Date(reading.recorded_at).toLocaleTimeString();
                    
                    document.getElementById('lastUpdate').textContent = new Date(reading.recorded_at).toLocaleTimeString();
                    
                    // Update charts
                    updateChartData(reading.temperature, reading.humidity);
                }

                const thresholdInput = document.getElementById('thresholdInput');
                if (!isUpdatingThreshold && document.activeElement !== thresholdInput) {
                    thresholdInput.value = settings.threshold_temperature;
                }

                const humidityThresholdInput = document.getElementById('humidityThresholdInput');
                if (!isUpdatingHumidityThreshold && document.activeElement !== humidityThresholdInput) {
                    humidityThresholdInput.value = settings.humidity_threshold || 70;
                }
                
                document.getElementById('fanOverride').checked = settings.fan_override;
                
                // Check temperature threshold
                if (reading && settings.threshold_temperature) {
                    if (reading.temperature > settings.threshold_temperature && 
                        (lastCheckedValues.temperature === null || lastCheckedValues.temperature <= settings.threshold_temperature)) {
                        
                        addNotification('temperature', 
                            `⚠️ Temperature (${reading.temperature.toFixed(1)}°C) exceeded threshold (${settings.threshold_temperature}°C)! Turn on the fan or it will auto-activate in 1 minute.`,
                            'fan',
                            'fanOverride'
                        );
                        
                        // Schedule auto fan activation
                        scheduleAutoAction('fan', async () => {
                            if (!document.getElementById('fanOverride').checked) {
                                document.getElementById('fanOverride').checked = true;
                                await toggleFanOverride();
                                addNotification('fan', '🔄 Fan automatically activated due to high temperature!', null, 'fanOverride');
                            }
                        }, 60000);
                    }
                }
                
                // Check humidity threshold
                if (reading && settings.humidity_threshold) {
                    if (reading.humidity > settings.humidity_threshold && 
                        (lastCheckedValues.humidity === null || lastCheckedValues.humidity <= settings.humidity_threshold)) {
                        
                        addNotification('humidity', 
                            `⚠️ Humidity (${reading.humidity.toFixed(1)}%) exceeded threshold (${settings.humidity_threshold}%)! Turn on the fan or it will auto-activate in 1 minute.`,
                            'fan',
                            'fanOverride'
                        );
                        
                        // Schedule auto fan activation
                        scheduleAutoAction('fan', async () => {
                            if (!document.getElementById('fanOverride').checked) {
                                document.getElementById('fanOverride').checked = true;
                                await toggleFanOverride();
                                addNotification('fan', '🔄 Fan automatically activated due to high humidity!', null, 'fanOverride');
                            }
                        }, 60000);
                    }
                }
                
                // Update last checked values
                if (reading) {
                    lastCheckedValues.temperature = reading.temperature;
                    lastCheckedValues.humidity = reading.humidity;
                }
                
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
                }
            } catch (error) {
                console.error('Error:', error);
                alert('❌ Failed to update threshold');
            }
        }

        async function updateHumidityThreshold() {
            const humidityThreshold = document.getElementById('humidityThresholdInput').value;
            
            try {
                const response = await fetch(`${API_BASE}/temperature/settings`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': CSRF_TOKEN
                    },
                    body: JSON.stringify({ humidity_threshold: parseFloat(humidityThreshold) })
                });
                
                if (response.ok) {
                    alert('✅ Humidity threshold updated to ' + humidityThreshold + '%');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('❌ Failed to update humidity threshold');
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
                
                // Cancel auto-action if user manually turns on fan
                if (override) {
                    cancelAutoAction('fan');
                }
                
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
                const statusDisplay = document.getElementById('fanStatusDisplay');
                
                if (data.fan_status === 'ON') {
                    statusDiv.innerHTML = '<div class="flex items-center gap-2"><i class="fas fa-fan fa-spin text-green-600"></i><span class="text-green-600">Fan is RUNNING</span></div>';
                    statusDisplay.innerHTML = '<span class="text-green-600">🔄 Running</span>';
                } else {
                    statusDiv.innerHTML = '<div class="flex items-center gap-2"><i class="fas fa-fan text-gray-400"></i><span class="text-gray-600">Fan is OFF</span></div>';
                    statusDisplay.innerHTML = '<span class="text-gray-500">⭕ Off</span>';
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
                
                // Check if darkness is detected and light is off
                if (data.darkness_detected && !data.is_on && 
                    (lastCheckedValues.lightStatus === null || !lastCheckedValues.lightStatus.darkness_detected)) {
                    
                    addNotification('light', 
                        '🌙 Darkness detected! Turn on the light or it will auto-activate in 1 minute.',
                        'light',
                        'lightButton'
                    );
                    
                    // Schedule auto light activation
                    scheduleAutoAction('light', async () => {
                        const currentStatus = await fetch(`${API_BASE}/light/status`);
                        const currentData = await currentStatus.json();
                        
                        if (currentData.darkness_detected && !currentData.is_on) {
                            await toggleLight();
                            addNotification('light', '💡 Light automatically activated due to darkness!', null, 'lightButton');
                        }
                    }, 60000);
                }
                
                lastCheckedValues.lightStatus = data;
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
                
                // Cancel auto-action if user manually turns on light
                if (data.is_on) {
                    cancelAutoAction('light');
                }
                
                updateLightUI(data.is_on);
            } catch (error) {
                console.error('Error:', error);
            }
        }

        function updateLightUI(isOn) {
            const button = document.getElementById('lightButton');
            const status = document.getElementById('lightStatus');
            const statusDisplay = document.getElementById('lightStatusDisplay');
            
            if (isOn) {
                button.className = 'w-full py-16 rounded-2xl transition-all duration-300 transform hover:scale-[1.02] text-4xl font-bold shadow-lg bg-gradient-to-br from-yellow-400 to-yellow-500 text-white';
                status.innerHTML = '💡 LIGHT ON';
                statusDisplay.innerHTML = '<span class="text-yellow-400">💡 On</span>';
            } else {
                button.className = 'w-full py-16 rounded-2xl transition-all duration-300 transform hover:scale-[1.02] text-4xl font-bold shadow-lg bg-gradient-to-br from-gray-600 to-gray-800 text-white';
                status.innerHTML = '🌙 LIGHT OFF';
                statusDisplay.innerHTML = '<span class="text-gray-400">🌙 Off</span>';
            }
        }

        // ========== CHART INITIALIZATION ==========
        let tempChart, humidityChart;
        let tempData = Array(20).fill(25);
        let humidityData = Array(20).fill(65);

        function initCharts() {
            // Temperature Chart
            const tempCtx = document.getElementById('tempChart').getContext('2d');
            tempChart = new Chart(tempCtx, {
                type: 'line',
                data: {
                    labels: Array(20).fill(''),
                    datasets: [{
                        label: 'Temperature',
                        data: tempData,
                        borderColor: '#dc2626',
                        backgroundColor: 'rgba(220, 38, 38, 0.1)',
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        pointRadius: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: false,
                            grid: { color: 'rgba(255, 255, 255, 0.1)' },
                            ticks: { color: '#ffffff' }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { display: false }
                        }
                    }
                }
            });

            // Humidity Chart
            const humidityCtx = document.getElementById('humidityChart').getContext('2d');
            humidityChart = new Chart(humidityCtx, {
                type: 'line',
                data: {
                    labels: Array(20).fill(''),
                    datasets: [{
                        label: 'Humidity',
                        data: humidityData,
                        borderColor: '#dc2626',
                        backgroundColor: 'rgba(220, 38, 38, 0.1)',
                        borderWidth: 3,
                        tension: 0.4,
                        fill: true,
                        pointRadius: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false }
                    },
                    scales: {
                        y: {
                            beginAtZero: false,
                            grid: { color: 'rgba(255, 255, 255, 0.1)' },
                            ticks: { color: '#ffffff' }
                        },
                        x: {
                            grid: { display: false },
                            ticks: { display: false }
                        }
                    }
                }
            });
        }

        function updateChartData(temp, humidity) {
            // Update temperature chart
            tempData.shift();
            tempData.push(temp);
            tempChart.data.datasets[0].data = tempData;
            tempChart.update('none');

            // Update humidity chart
            humidityData.shift();
            humidityData.push(humidity);
            humidityChart.data.datasets[0].data = humidityData;
            humidityChart.update('none');
        }

        // ========== INITIALIZATION ==========
        function init() {
            initCharts();
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
