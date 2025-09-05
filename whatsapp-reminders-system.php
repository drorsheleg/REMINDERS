<?php 
include_once __DIR__ . '/../shared/unified_header.php'; 
?>
<!DOCTYPE html>
<html lang="he" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>מערכת תזכורות WhatsApp - קאר וושר</title>
    
    <link href="https://fonts.googleapis.com/css2?family=Assistant:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        /* ============================================= */
        /* =========== DESIGN SYSTEM - V1.0 ============ */
        /* ============================================= */

        /* --- 1. Global Variables & Base Styles --- */
        :root {
            --primary-color: #05bbff;
            --primary-gradient: linear-gradient(135deg, #05bbff 0%, #04a5e1 100%);
            --primary-hover: #04a5e1;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --danger-hover: #c82333;
            --accent-color: #7c3aed;
            
            --background-page: #f5f7fa;
            --background-card: #ffffff;
            --background-secondary: #f4f8fb;
            
            --text-primary: #212529;
            --text-secondary: #6c757d;
            --text-on-primary: #ffffff;

            --border-color: #dee2e6;
            --shadow-sm: 0 2px 6px rgba(0, 0, 0, 0.04);
            --shadow-md: 0 4px 12px rgba(0,0,0,0.1);

            --radius-sm: 6px;
            --radius-md: 8px;
            --radius-lg: 12px;

            --spacing-md: 24px;
            --spacing-sm: 16px;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Assistant', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            background-color: var(--background-page);
            color: var(--text-primary);
            line-height: 1.6;
            scroll-behavior: smooth;
        }

        /* --- 2. Layout & Structure --- */
        .page-wrapper {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .main-container {
            width: 100%;
            max-width: 1400px;
            margin: 0 auto;
            padding: var(--spacing-md);
            flex: 1;
        }
        .content-wrapper {
            display: grid;
            grid-template-columns: 1fr 340px;
            gap: var(--spacing-md);
            align-items: flex-start;
        }
        .main-content { min-width: 0; }
        .sidebar { position: sticky; top: var(--spacing-md); }

        /* --- 3. Core Components --- */

        /* Page Hero */
        .page-hero {
            background: var(--primary-gradient);
            color: var(--text-on-primary);
            padding: 24px 32px;
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
            border-radius: var(--radius-md);
            margin-bottom: var(--spacing-md);
            box-shadow: 0 2px 6px rgba(5, 187, 255, 0.2);
        }
        .page-hero-icon { font-size: 32px; }
        .page-hero h1 { font-size: 28px; font-weight: 700; margin: 0; }
        .page-hero p { font-size: 15px; opacity: 0.85; margin: 4px 0 0 0; }

        /* Cards */
        .card {
            background: var(--background-card);
            border-radius: var(--radius-md);
            padding: var(--spacing-md);
            box-shadow: var(--shadow-sm);
            border: 1px solid transparent;
            transition: background-color 0.3s, border-color 0.3s;
        }
        .card-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: var(--spacing-sm);
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--text-primary);
        }

        /* Buttons */
        .btn {
            padding: 10px 16px;
            border: 1px solid transparent;
            border-radius: var(--radius-sm);
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            font-family: 'Assistant', sans-serif;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            text-decoration: none;
        }
        .btn-primary {
            background-color: var(--primary-color);
            color: var(--text-on-primary);
        }
        .btn-primary:hover { background-color: var(--primary-hover); }
        
        .btn-secondary {
            background-color: var(--background-secondary);
            color: var(--text-primary);
            border-color: var(--border-color);
        }
        .btn-secondary:hover { background-color: #e8f3f8; }

        .btn-danger {
            background-color: var(--danger-color);
            color: var(--text-on-primary);
        }
        .btn-danger:hover { background-color: var(--danger-hover); }

        .btn:disabled { opacity: 0.6; cursor: not-allowed; }
        .btn.btn-block { display: flex; width: 100%; }

        /* Forms */
        .form-group { margin-bottom: 20px; }
        .form-group:last-child { margin-bottom: 0; }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: var(--text-primary);
            font-size: 14px;
        }
        .form-control {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid var(--border-color);
            border-radius: var(--radius-sm);
            font-size: 15px;
            font-family: 'Assistant', sans-serif;
            transition: border-color 0.2s, box-shadow 0.2s;
            background-color: var(--background-card);
            color: var(--text-primary);
        }
        .form-control:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(5, 187, 255, 0.1);
        }

        /* Toggle Switch */
        .toggle-group {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .toggle-switch {
            position: relative;
            width: 50px;
            height: 26px;
        }
        .toggle-switch input { opacity: 0; width: 0; height: 0; }
        .toggle-switch .slider {
            position: absolute;
            cursor: pointer;
            top: 0; left: 0; right: 0; bottom: 0;
            background-color: #ccc;
            border-radius: 13px;
            transition: all 0.3s ease;
        }
        .toggle-switch .slider::after {
            content: '';
            position: absolute;
            top: 3px;
            right: 3px; /* Changed from left for RTL */
            width: 20px;
            height: 20px;
            background: white;
            border-radius: 50%;
            transition: all 0.3s ease;
            box-shadow: 0 1px 3px rgba(0,0,0,0.2);
        }
        .toggle-switch input:checked + .slider { background-color: var(--success-color); }
        .toggle-switch input:checked + .slider::after { transform: translateX(-24px); }

        /* Tabs */
        .tabs-container {
            display: flex;
            gap: 6px;
            flex-wrap: wrap;
            margin-bottom: var(--spacing-sm);
        }
        .tab-btn {
            background: var(--background-secondary);
            border: 1px solid var(--border-color);
            color: var(--text-secondary);
            padding: 4px 10px;
            border-radius: 15px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
        }
        .tab-btn:hover { background: #ced4da; }
        .tab-btn.active {
            background: var(--primary-color);
            color: var(--text-on-primary);
            border-color: var(--primary-color);
        }

        /* Utility Classes */
        .mb-24 { margin-bottom: 24px; }
        .text-center { text-align: center; }
        
        /* Specific Component Styles for this page */
        .metric-card .value { font-size: 28px; font-weight: 700; color: var(--primary-color); }
        .metric-card .label { font-size: 14px; color: var(--text-secondary); margin-top: 4px; }
        .metric-card.danger .value { color: var(--danger-color); }
        .metric-card.success .value { color: var(--success-color); }
        
        .list-container { min-height: 150px; max-height: 400px; overflow-y: auto; }
        .reminder-item {
            background-color: var(--background-secondary);
            border-right: 4px solid var(--primary-color);
            padding: var(--spacing-sm);
            margin-bottom: 12px;
            border-radius: var(--radius-sm);
        }
        .item-header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px; }
        .item-client { font-weight: 600; font-size: 16px; display: flex; align-items: center; gap: 8px; }
        .item-time { font-size: 13px; color: var(--text-secondary); }
        .item-details { display: flex; flex-direction: column; gap: 8px; font-size: 14px; color: var(--text-secondary); }
        .reminder-item.is-cancelled { opacity: 0.6; background-color: var(--background-card); }
        .no-data { text-align: center; padding: 40px; color: var(--text-secondary); }
        .no-data i { font-size: 40px; margin-bottom: 16px; opacity: 0.5; }

        .spinner { 
            width: 18px; height: 18px; 
            border: 2px solid rgba(255,255,255,0.3); 
            border-top-color: var(--text-on-primary); 
            border-radius: 50%; 
            animation: spin 1s linear infinite; 
        }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* --- 4. Dark Mode --- */
        body.dark-mode {
            --background-page: #1a1a1a;
            --background-card: #2d2d30;
            --background-secondary: #1e1e1e;
            --text-primary: #e0e0e0;
            --text-secondary: #b0b0b0;
            --border-color: #404040;
            --shadow-sm: none;
        }
        body.dark-mode .card { border-color: var(--border-color); }
        body.dark-mode .btn-secondary { background-color: #404040; color: var(--text-primary); }
        body.dark-mode .btn-secondary:hover { background-color: #505050; }
        body.dark-mode .form-control { background-color: #1e1e1e; border-color: #404040; color: #e0e0e0; }
        body.dark-mode .tab-btn { background-color: #404040; color: var(--text-primary); }
        body.dark-mode .tab-btn:hover { background-color: #505050; }
        body.dark-mode .tab-btn.active { background: var(--primary-color); border-color: var(--primary-color); color: var(--text-on-primary); }
        body.dark-mode .reminder-item { background-color: #1e1e1e; }
        body.dark-mode .reminder-item.is-cancelled { background-color: var(--background-card); }

        /* --- 5. Responsiveness --- */
        @media (max-width: 992px) {
            .content-wrapper {
                grid-template-columns: 1fr;
            }
            .sidebar {
                position: static;
            }
        }
        @media (max-width: 768px) {
            .main-container { padding: 0; }
            .page-hero { border-radius: 0; padding: 20px; }
            .card { border-radius: 0; box-shadow: none; }
            .page-hero h1 { font-size: 24px; }
            .inner-container { padding: var(--spacing-sm); }
        }

        /* Floating Dark Mode Toggle */
        #globalDarkModeToggle { position: fixed; bottom: 30px; left: 30px; width: 60px; height: 60px; border-radius: 50%; border: none; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; cursor: pointer; z-index: 99999; box-shadow: 0 8px 20px rgba(0,0,0,0.3); transition: all 0.3s ease; display: flex; align-items: center; justify-content: center; overflow: hidden; }
        #globalDarkModeToggle:hover { transform: translateY(-3px); box-shadow: 0 12px 25px rgba(0,0,0,0.4); }
        .global-toggle-icon { position: relative; width: 30px; height: 30px; transition: transform 0.3s ease; }
        .global-sun-icon, .global-moon-icon { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); transition: all 0.3s ease; font-size: 20px; }
        .global-moon-icon { opacity: 0; transform: translate(-50%, -50%) rotate(180deg); }
        body.dark-mode #globalDarkModeToggle { background: linear-gradient(135deg, #ff9800 0%, #f57c00 100%); }
        body.dark-mode .global-sun-icon { opacity: 0; transform: translate(-50%, -50%) rotate(-180deg); }
        body.dark-mode .global-moon-icon { opacity: 1; transform: translate(-50%, -50%) rotate(0deg); }

    </style>
</head>
<body>
    <div class="page-wrapper">
        <div class="main-container">
            <div class="inner-container">
                <header class="page-hero">
                    <i class="page-hero-icon fas fa-bell"></i>
                    <div>
                        <h1>מערכת תזכורות WhatsApp</h1>
                        <p>ניהול וניטור תזכורות אוטומטיות ללקוחות</p>
                    </div>
                </header>

                <div class="grid-container" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: var(--spacing-md); margin-bottom: var(--spacing-md);">
                    <div class="card metric-card">
                        <div class="value" id="metric-upcoming">0</div>
                        <div class="label">תזכורות צפויות (24 ש')</div>
                    </div>
                    <div class="card metric-card success">
                        <div class="value" id="metric-sent">0</div>
                        <div class="label">נשלחו בהצלחה היום</div>
                    </div>
                    <div class="card metric-card danger">
                        <div class="value" id="metric-failed">0</div>
                        <div class="label">שגיאות שליחה היום</div>
                    </div>
                </div>

                <div class="content-wrapper">
                    <main class="main-content">
                        <div class="card mb-24">
                            <h3 class="card-title"><i class="fas fa-clock"></i> תזכורות צפויות להישלח</h3>
                            <div class="tabs-container" id="upcoming-filters">
                                <button class="tab-btn active" data-filter="all">הכל</button>
                                <button class="tab-btn" data-filter="daily">תזכורת יומית</button>
                                <button class="tab-btn" data-filter="hourly">תזכורת שעתית</button>
                            </div>
                            <div class="list-container" id="upcoming-list">
                                </div>
                        </div>
                        <div class="card">
                            <h3 class="card-title"><i class="fas fa-history"></i> היסטוריית תזכורות (48 שעות)</h3>
                            <div class="tabs-container" id="history-filters">
                                <button class="tab-btn active" data-filter="all">הכל</button>
                                <button class="tab-btn" data-filter="daily">תזכורת יומית</button>
                                <button class="tab-btn" data-filter="hourly">תזכורת שעתית</button>
                            </div>
                            <div class="list-container" id="history-list">
                                </div>
                        </div>
                    </main>

                    <aside class="sidebar">
                        <div class="card mb-24">
                            <h3 class="card-title"><i class="fas fa-power-off"></i> סטטוס מערכת</h3>
                            <div id="status-list-container">
                                </div>
                        </div>
                        <div class="card">
                            <h3 class="card-title"><i class="fas fa-cogs"></i> הגדרות ובקרה</h3>
                            
                            <div class="form-group toggle-group">
                                <label for="reminders-enabled-toggle">הפעלת שליחה</label>
                                <label class="toggle-switch">
                                    <input type="checkbox" id="reminders-enabled-toggle">
                                    <span class="slider"></span>
                                </label>
                            </div>
                            
                            <div class="form-group">
                                <button class="btn btn-primary btn-block" id="btn-refresh-data"><i class="fas fa-sync-alt"></i> רענון נתונים</button>
                            </div>
                            <div class="form-group">
                                <button class="btn btn-secondary btn-block" id="btn-send-test"><i class="fas fa-paper-plane"></i> שליחת בדיקה</button>
                            </div>
                        
                            <div class="form-group">
                                <label for="setting-daily-time">שעת תזכורת יומית</label>
                                <input class="form-control" type="time" id="setting-daily-time">
                            </div>
                            <div class="form-group">
                                <label for="setting-hourly-minutes">תזכורת לפני (דקות)</label>
                                <input class="form-control" type="number" id="setting-hourly-minutes">
                            </div>
                            <div class="form-group">
                                <label for="setting-auto-refresh">רענון אוטומטי</label>
                                <select class="form-control" id="setting-auto-refresh"></select>
                            </div>
                        </div>
                    </aside>
                </div>
            </div> </div>

        <button id="globalDarkModeToggle" aria-label="Toggle Dark Mode">
            <div class="global-toggle-icon">
                <div class="global-sun-icon">☀️</div>
                <div class="global-moon-icon">🌙</div>
            </div>
        </button>
    </div>

    <script>
        // DARK MODE TOGGLE LOGIC
        document.addEventListener('DOMContentLoaded', () => {
            const darkModeToggle = document.getElementById('globalDarkModeToggle');
            if (localStorage.getItem('darkMode') === 'true') {
                document.body.classList.add('dark-mode');
            }
            darkModeToggle.addEventListener('click', () => {
                document.body.classList.toggle('dark-mode');
                localStorage.setItem('darkMode', document.body.classList.contains('dark-mode'));
            });
        });

        // =============================================
        // ORIGINAL PAGE JAVASCRIPT - UNCHANGED
        // =============================================
        const API_ENDPOINT = '/app/reminders/api.php';
        const WHATSAPP_API_ENDPOINT = '/app/reminders/whatsapp-api.php';

        const appState = {
            settings: { remindersEnabled: true, dailyReminderTime: '21:00', hourlyReminderMinutes: 60, autoRefreshMinutes: 10 },
            ui: { upcomingFilter: 'all', historyFilter: 'all' },
            status: { system: {}, whatsapp: {}, database: {} },
            upcomingAppointments: [],
            sentHistory: [],
            cancelledReminders: new Set(),
            scheduler: { mainInterval: null, refreshInterval: null, sentLog: new Set(), clockInterval: null }
        };

        const api = {
            async request(endpoint, action, method = 'GET', body = null) {
                const url = new URL(endpoint, window.location.origin);
                url.searchParams.append('action', action);
                const options = { method, headers: { 'Content-Type': 'application/json' } };
                if (body) { options.body = JSON.stringify(body); }
                const response = await fetch(url.toString(), options);
                const result = await response.json();
                if (!response.ok) { throw new Error(result.error?.message || `שגיאת שרת (${response.status})`); }
                return result;
            },
            sendWhatsApp(p, m) { return this.request(WHATSAPP_API_ENDPOINT, 'send_custom', 'POST', { phone: p, message: m }); },
            testWhatsApp() { return this.request(WHATSAPP_API_ENDPOINT, 'test'); },
            testDatabase() { return this.request(API_ENDPOINT, 'test_connection'); },
            getUpcomingAppointments() { return this.request(API_ENDPOINT, 'get_upcoming_appointments'); },
            getSentHistory() { return this.request(API_ENDPOINT, 'get_sent_reminders'); },
            logSentReminder(appt, type, success, details) {
                const payload = { client_name: appt.client_name, phone: appt.phone, reminder_type: type, status: success ? 'success' : 'failed', details };
                return this.request(API_ENDPOINT, 'log_sent_reminder', 'POST', payload);
            }
        };

        document.addEventListener('DOMContentLoaded', () => {
            loadState();
            initializeEventListeners();
            runInitialLoad();
            startSchedulers();
        });

        function loadState() {
            const defaults = { dailyReminderTime: '21:00', hourlyReminderMinutes: 60, autoRefreshMinutes: 10, remindersEnabled: true };
            const savedSettings = JSON.parse(localStorage.getItem('carwash_reminder_settings')) || {};
            appState.settings = { ...defaults, ...savedSettings };
            
            const savedCancellations = JSON.parse(localStorage.getItem('carwash_cancelled_reminders')) || [];
            appState.cancelledReminders = new Set(savedCancellations);

            document.getElementById('reminders-enabled-toggle').checked = appState.settings.remindersEnabled;
            document.getElementById('setting-daily-time').value = appState.settings.dailyReminderTime;
            document.getElementById('setting-hourly-minutes').value = appState.settings.hourlyReminderMinutes;
            const autoRefreshSelect = document.getElementById('setting-auto-refresh');
            autoRefreshSelect.innerHTML = `<option value="5">5 דקות</option><option value="10">10 דקות</option><option value="30">30 דקות</option>`;
            autoRefreshSelect.value = appState.settings.autoRefreshMinutes;
        }

        function saveSettings() {
            localStorage.setItem('carwash_reminder_settings', JSON.stringify(appState.settings));
        }

        function saveCancellations() {
            localStorage.setItem('carwash_cancelled_reminders', JSON.stringify(Array.from(appState.cancelledReminders)));
        }

        async function runInitialLoad() {
            await checkAllStatuses();
            await Promise.all([ fetchUpcomingAppointments(), fetchSentHistory() ]);
            renderAll();
        }
        
        function startSchedulers() {
            clearInterval(appState.scheduler.mainInterval);
            appState.scheduler.mainInterval = setInterval(schedulerTick, 60 * 1000);
            startRefreshScheduler();
        }

        function startRefreshScheduler() {
            clearInterval(appState.scheduler.refreshInterval);
            const refreshMs = appState.settings.autoRefreshMinutes * 60 * 1000;
            appState.scheduler.refreshInterval = setInterval(async () => {
                await Promise.all([ fetchUpcomingAppointments(), fetchSentHistory() ]);
                renderLists();
            }, refreshMs);
        }
        
        function schedulerTick() {
            if (!appState.settings.remindersEnabled) { return; }
            const now = new Date();
            appState.upcomingAppointments.forEach(appt => {
                const apptTime = new Date(appt.appointment_time);
                
                // Daily Reminder
                const dailySendTime = new Date(apptTime);
                dailySendTime.setDate(dailySendTime.getDate() - 1);
                const [h, m] = appState.settings.dailyReminderTime.split(':');
                dailySendTime.setHours(parseInt(h), parseInt(m), 0, 0);
                const dailyId = `${appt.id}-daily`;
                if (now >= dailySendTime && now < new Date(dailySendTime.getTime() + 60000)) {
                    if (!appState.scheduler.sentLog.has(dailyId) && !appState.cancelledReminders.has(dailyId)) { 
                        sendReminder(appt, 'daily'); 
                        appState.scheduler.sentLog.add(dailyId); 
                    }
                }
                
                // Hourly Reminder
                const hourlySendTime = new Date(apptTime.getTime() - appState.settings.hourlyReminderMinutes * 60 * 1000);
                const hourlyId = `${appt.id}-hourly`;
                if (now >= hourlySendTime && now < apptTime) {
                    if (!appState.scheduler.sentLog.has(hourlyId) && !appState.cancelledReminders.has(hourlyId)) { 
                        sendReminder(appt, 'hourly'); 
                        appState.scheduler.sentLog.add(hourlyId); 
                    }
                }
            });
        }

        async function sendReminder(appointment, type) {
            console.log(`TRIGGER: ${type} reminder for ${appointment.client_name}`);
            const hebrewTime = new Date(appointment.appointment_time).toLocaleTimeString('he-IL', { hour: '2-digit', minute: '2-digit' });
            const message = type === 'daily' ? `שלום ${appointment.client_name}, תזכורת מקאר וושר! 🚗✨\nקבעת תור למחר, בשעה ${hebrewTime}.\nנשמח לראותך!` : `היי ${appointment.client_name}, תזכורת ידידותית מקאר וושר! 🚗✨\nהתור שלך מתחיל בקרוב, בשעה ${hebrewTime}.\nאנחנו מחכים לך!`;
            try {
                const result = await api.sendWhatsApp(appointment.phone, message);
                if (!result.success) throw new Error(result.error);
                await api.logSentReminder(appointment, type, true, "נשלח בהצלחה");
            } catch (error) {
                console.error(`Failed to send reminder:`, error);
                await api.logSentReminder(appointment, type, false, error.message);
            }
            await fetchSentHistory();
            renderHistoryList();
        }

        function renderAll() { renderStatusCards(); renderLists(); }
        function renderLists() { renderUpcomingList(); renderHistoryList(); updateMetrics(); }

        function renderStatusCards() {
            const statuses = [ { id: 'status-system', data: appState.status.system, label: 'מערכת כללית' }, { id: 'status-whatsapp', data: appState.status.whatsapp, label: 'חיבור WhatsApp' }, { id: 'status-database', data: appState.status.database, label: 'חיבור נתונים' } ];
            document.getElementById('status-list-container').innerHTML = statuses.map(s => `<div style="display: flex; align-items: center; gap: 12px; padding: 12px 0; border-bottom: 1px solid var(--border-color);"><div style="width:10px; height:10px; border-radius:50%; background-color: var(--${s.data?.state || 'warning'}-color);"></div><div><p style="font-weight: 600; margin: 0;">${s.label}</p><small style="color: var(--text-secondary);">${s.data?.message || 'בודק...'}</small></div></div>`).join('');
        }

        function renderUpcomingList() {
            const container = document.getElementById('upcoming-list');
            const now = new Date();
            const in24Hours = new Date(now.getTime() + 24 * 60 * 60 * 1000);
            let upcomingReminders = [];
            appState.upcomingAppointments.forEach(appt => {
                const apptTime = new Date(appt.appointment_time);
                const dailySendTime = new Date(apptTime);
                dailySendTime.setDate(dailySendTime.getDate() - 1);
                const [h, m] = appState.settings.dailyReminderTime.split(':');
                dailySendTime.setHours(parseInt(h), parseInt(m), 0, 0);
                if (dailySendTime > now && dailySendTime <= in24Hours) { upcomingReminders.push({ ...appt, type: 'daily', sendTime: dailySendTime }); }
                const hourlySendTime = new Date(apptTime.getTime() - appState.settings.hourlyReminderMinutes * 60 * 1000);
                if (hourlySendTime > now && hourlySendTime <= in24Hours) { upcomingReminders.push({ ...appt, type: 'hourly', sendTime: hourlySendTime }); }
            });
            upcomingReminders.sort((a, b) => a.sendTime - b.sendTime);
            
            const filteredReminders = upcomingReminders.filter(rem => appState.ui.upcomingFilter === 'all' || rem.type === appState.ui.upcomingFilter);
            
            if (filteredReminders.length === 0) { container.innerHTML = `<div class="no-data"><i class="fas fa-calendar-check"></i><p>אין תזכורות צפויות</p></div>`; return; }
            
            container.innerHTML = filteredReminders.map(rem => {
                const reminderId = `${rem.id}-${rem.type}`;
                const isCancelled = appState.cancelledReminders.has(reminderId);
                const isDaily = rem.type === 'daily';
                const icon = isDaily ? 'fa-calendar-day' : 'fa-clock';
                const title = isDaily ? 'תזכורת יומית' : 'תזכורת שעתית';
                return `<div class="reminder-item ${isCancelled ? 'is-cancelled' : ''}">
                    <div class="item-header">
                        <div class="item-client"><i class="fas fa-user"></i> ${rem.client_name}</div>
                        <div class="item-time">תישלח ${timeUntil(rem.sendTime)}</div>
                    </div>
                    <div class="item-details">
                        <div><i class="fas ${icon}"></i> <strong>${title}</strong></div>
                        <div><i class="fas fa-paper-plane"></i> לשליחה ב: ${rem.sendTime.toLocaleDateString('he-IL')} ${rem.sendTime.toLocaleTimeString('he-IL', {hour:'2-digit', minute:'2-digit'})}</div>
                    </div>
                    <div style="margin-top: 12px; text-align: left;">
                        <button class="btn btn-danger" style="font-size:12px; padding: 4px 10px;" data-reminder-id="${reminderId}" ${isCancelled ? 'disabled' : ''}>
                            <i class="fas fa-ban"></i> ${isCancelled ? 'בוטל' : 'בטל שליחה'}
                        </button>
                    </div>
                </div>`;
            }).join('');
        }
        
        function renderHistoryList() {
            const container = document.getElementById('history-list');
            const filteredHistory = appState.sentHistory.filter(log => appState.ui.historyFilter === 'all' || log.reminder_type === appState.ui.historyFilter);
            if (filteredHistory.length === 0) { container.innerHTML = `<div class="no-data"><i class="fas fa-inbox"></i><p>אין היסטוריה להצגה</p></div>`; return; }
            container.innerHTML = filteredHistory.map(log => {
                const isDaily = log.reminder_type === 'daily';
                const icon = isDaily ? 'fa-calendar-day' : 'fa-clock';
                const title = isDaily ? 'תזכורת יומית' : 'תзכורת שעתית';
                const statusBadge = log.status === 'success' ? `<span style="background-color: #e6f8ef; color: var(--success-color); padding: 2px 8px; border-radius: 12px; font-size: 12px;">הצלחה</span>` : `<span style="background-color: #fbeaea; color: var(--danger-color); padding: 2px 8px; border-radius: 12px; font-size: 12px;">כישלון</span>`;
                return `<div class="reminder-item"><div class="item-header"><div class="item-client"><i class="fas fa-user"></i> ${log.client_name}</div><div class="item-time">נשלחה ${timeAgo(new Date(log.sent_at))}</div></div><div class="item-details"><div><i class="fas ${icon}"></i> <strong>${title}</strong> ${statusBadge}</div><div><i class="fas fa-info-circle"></i><small>${log.details}</small></div></div></div>`;
            }).join('');
        }

        function updateMetrics() {
            document.getElementById('metric-upcoming').textContent = document.querySelectorAll('#upcoming-list .reminder-item:not(.is-cancelled)').length;
            const today = new Date().toISOString().slice(0, 10);
            const sentToday = appState.sentHistory.filter(log => log.sent_at.startsWith(today) && log.status === 'success').length;
            const failedToday = appState.sentHistory.filter(log => log.sent_at.startsWith(today) && log.status === 'failed').length;
            document.getElementById('metric-sent').textContent = sentToday;
            document.getElementById('metric-failed').textContent = failedToday;
        }
        
        function initializeEventListeners() {
            document.getElementById('reminders-enabled-toggle').addEventListener('change', e => { appState.settings.remindersEnabled = e.target.checked; saveSettings(); });
            document.getElementById('setting-daily-time').addEventListener('change', e => { appState.settings.dailyReminderTime = e.target.value; saveSettings(); });
            document.getElementById('setting-hourly-minutes').addEventListener('change', e => { appState.settings.hourlyReminderMinutes = parseInt(e.target.value, 10); saveSettings(); });
            document.getElementById('setting-auto-refresh').addEventListener('change', e => { appState.settings.autoRefreshMinutes = parseInt(e.target.value, 10); saveSettings(); startRefreshScheduler(); });
            document.getElementById('btn-refresh-data').addEventListener('click', handleRefreshClick);
            document.getElementById('btn-send-test').addEventListener('click', handleSendTestClick);
            document.getElementById('upcoming-filters').addEventListener('click', (e) => { if (e.target.classList.contains('tab-btn')) { appState.ui.upcomingFilter = e.target.dataset.filter; document.querySelectorAll('#upcoming-filters .tab-btn').forEach(btn => btn.classList.remove('active')); e.target.classList.add('active'); renderUpcomingList(); updateMetrics(); } });
            document.getElementById('history-filters').addEventListener('click', (e) => { if (e.target.classList.contains('tab-btn')) { appState.ui.historyFilter = e.target.dataset.filter; document.querySelectorAll('#history-filters .tab-btn').forEach(btn => btn.classList.remove('active')); e.target.classList.add('active'); renderHistoryList(); } });
            document.getElementById('upcoming-list').addEventListener('click', e => {
                const button = e.target.closest('button');
                if (button && button.dataset.reminderId) {
                    const reminderId = button.dataset.reminderId;
                    if (confirm('האם אתה בטוח שברצונך לבטל את שליחת התזכורת הספציפית הזו?')) {
                        appState.cancelledReminders.add(reminderId);
                        saveCancellations();
                        renderUpcomingList();
                        updateMetrics();
                    }
                }
            });
        }

        async function handleRefreshClick(event) {
            const btn = event.currentTarget;
            btn.disabled = true;
            const originalHTML = btn.innerHTML;
            btn.innerHTML = `<span class="spinner"></span> מרענן...`;
            await runInitialLoad();
            btn.innerHTML = originalHTML;
            btn.disabled = false;
        }

        async function handleSendTestClick(event) {
            const btn = event.currentTarget;
            const phone = prompt("הזן מספר טלפון לבדיקה (פורמט בינלאומי, לדוגמה: 972501234567):");
            if (!phone) return;
            const originalContent = btn.innerHTML;
            btn.disabled = true; btn.innerHTML = `<span class="spinner" style="border-top-color: var(--text-primary);"></span> שולח...`;
            try {
                const result = await api.sendWhatsApp(phone, `🔔 תזכורת בדיקה ממערכת קאר וושר.`);
                if(!result.success) throw new Error(result.error);
                alert("הודעת בדיקה נשלחה בהצלחה!");
            } catch(error) { alert(`שגיאה בשליחה: ${error.message}`); }
            btn.innerHTML = originalContent; btn.disabled = false;
        }
        
        async function checkAllStatuses() {
            appState.status.system = { state: 'success', message: 'המערכת פועלת' };
            try { const res = await api.testWhatsApp(); if(!res.success) throw new Error(res.error); appState.status.whatsapp = { state: 'success', message: 'מחובר' }; } catch (e) { appState.status.whatsapp = { state: 'danger', message: e.message }; appState.status.system = { state: 'danger', message: 'שגיאת WhatsApp' }; }
            try { const res = await api.testDatabase(); if(!res.success) throw new Error(res.error); appState.status.database = { state: 'success', message: 'מחובר' }; } catch (e) { appState.status.database = { state: 'danger', message: e.message }; appState.status.system = { state: 'danger', message: 'שגיאת נתונים' }; }
            renderStatusCards();
        }
        
        async function fetchUpcomingAppointments() { try { appState.upcomingAppointments = (await api.getUpcomingAppointments()).appointments || []; } catch (error) { console.error("Failed to fetch upcoming appointments:", error); appState.upcomingAppointments = []; } }
        async function fetchSentHistory() { try { appState.sentHistory = (await api.getSentHistory()).history || []; } catch (error) { console.error("Failed to fetch sent history:", error); appState.sentHistory = []; } }

        const timeAgo = date => { const seconds = Math.floor((new Date() - date) / 1000); if (seconds < 60) return `ממש עכשיו`; const minutes = Math.floor(seconds / 60); if (minutes < 60) return `לפני ${minutes} דקות`; const hours = Math.floor(minutes / 60); return `לפני ${hours} שעות`; };
        const timeUntil = date => { const seconds = Math.floor((date - new Date()) / 1000); if (seconds < 0) return "בעבר"; if (seconds < 60) return `בעוד פחות מדקה`; const minutes = Math.floor(seconds / 60); if (minutes < 60) return `בעוד ${minutes} דקות`; const hours = Math.floor(minutes / 60); return `בעוד ${hours} שעות ו-${minutes % 60} דקות`; };
    </script>
</body>
</html>
<?php 
include_once __DIR__ . '/../shared/unified_footer.php'; 
?>