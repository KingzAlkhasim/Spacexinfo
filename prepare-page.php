<?php
/*
Template Name: Spacexinfo Prepare Page
Template Post Type: page
*/

session_start();

// Check if user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    header("Location: /log-in/"); // Update this to your actual login URL
    exit;
}

// ===========================================
// 💥 START BALANCE FETCH LOGIC 
// ===========================================
$servername = "localhost";
$dbusername = "spacenet_spacexinfo"; 
$dbpassword = "@#passNet"; 
$dbname = "spacenet_spacexinfo_userdb";

$conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);

// Initialize balance variable
$current_balance = 0.00;
$user_id = $_SESSION['user_id']; 

// Fetch the latest balance from the database
$stmt = $conn->prepare("SELECT balance FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user_data = $result->fetch_assoc();
    $current_balance = $user_data['balance'];
}

$stmt->close();
$conn->close(); 
// ===========================================
// 💥 END BALANCE FETCH LOGIC 
// ===========================================
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spacexinfo - Dashboard</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #0a0e27;
            color: #ffffff;
            overflow-x: hidden; /* Prevent horizontal scroll */
        }

        /* Sidebar */
        .sidebar {
            position: fixed;
            left: 0;
            top: 0;
            width: 280px;
            height: 100vh;
            background: linear-gradient(180deg, #0d1128, #0a0e27);
            border-right: 1px solid rgba(255, 255, 255, 0.1);
            padding: 2rem 0;
            z-index: 1000;
            transition: transform 0.3s ease-in-out;
        }

        .logo {
            font-size: 1.8rem;
            font-weight: bold;
            background: linear-gradient(135deg, #00ff87, #00d4ff);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            padding: 0 2rem;
            margin-bottom: 3rem;
        }

        .nav-menu {
            list-style: none;
        }

        .nav-item {
            margin-bottom: 0.5rem;
        }

        .nav-link {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem 2rem;
            color: #b8bcc8;
            text-decoration: none;
            transition: all 0.3s;
            position: relative;
        }

        .nav-link:hover,
        .nav-link.active {
            color: #fff;
            background: rgba(0, 255, 135, 0.1);
        }

        .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 3px;
            background: linear-gradient(180deg, #00ff87, #00d4ff);
        }

        .nav-icon { font-size: 1.3rem; }

        .sidebar-footer {
            position: absolute;
            bottom: 2rem;
            left: 0;
            right: 0;
            padding: 0 2rem;
        }

        .user-profile {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 10px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .user-profile:hover { background: rgba(255, 255, 255, 0.08); }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #00ff87, #00d4ff);
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #0a0e27;
            flex-shrink: 0;
        }

        .user-info { flex: 1; overflow: hidden; }
        .user-name { font-weight: 600; font-size: 0.95rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .user-email { font-size: 0.8rem; color: #b8bcc8; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }

        /* Main Content */
        .main-content {
            margin-left: 280px;
            min-height: 100vh;
            transition: margin-left 0.3s ease;
        }

        .topbar {
            background: rgba(10, 14, 39, 0.95);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            padding: 1.5rem 3rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .search-bar {
            flex: 1;
            max-width: 400px;
            position: relative;
        }

        .search-bar input {
            width: 100%;
            padding: 0.8rem 1rem 0.8rem 3rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            color: #fff;
            font-size: 0.95rem;
        }

        .search-bar input:focus { outline: none; border-color: #00ff87; }
        .search-icon { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #b8bcc8; }

        .topbar-actions { display: flex; gap: 1rem; align-items: center; }

        .icon-btn {
            width: 45px; height: 45px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            display: flex; align-items: center; justify-content: center;
            cursor: pointer; transition: all 0.3s; position: relative; color: #fff;
        }

        .icon-btn:hover { background: rgba(255, 255, 255, 0.1); border-color: #00ff87; }

        .notification-badge {
            position: absolute; top: -5px; right: -5px;
            width: 18px; height: 18px;
            background: #ff4757; border-radius: 50%;
            font-size: 0.7rem; display: flex; align-items: center; justify-content: center;
        }

        .content-wrapper { padding: 3rem; }

        .page-header { margin-bottom: 2rem; }
        .page-title { font-size: 2rem; margin-bottom: 0.5rem; }
        .page-subtitle { color: #b8bcc8; }

        /* Balance Cards */
        .balance-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .balance-card {
            background: linear-gradient(135deg, rgba(0, 255, 135, 0.1), rgba(0, 212, 255, 0.05));
            border: 1px solid rgba(0, 255, 135, 0.2);
            border-radius: 15px;
            padding: 2rem;
            position: relative;
            overflow: hidden;
        }

        .balance-card::before {
            content: '';
            position: absolute;
            top: -50%; right: -50%;
            width: 200px; height: 200px;
            background: radial-gradient(circle, rgba(0, 255, 135, 0.2), transparent);
            border-radius: 50%;
        }

        .balance-card-header { display: flex; justify-content: space-between; align-items: start; margin-bottom: 1.5rem; }
        .balance-label { color: #b8bcc8; font-size: 0.9rem; }
        .balance-icon { font-size: 1.8rem; }
        .balance-amount { font-size: 2.5rem; font-weight: bold; margin-bottom: 0.5rem; }
        .balance-change { font-size: 0.9rem; display: flex; align-items: center; gap: 0.3rem; }
        .positive { color: #00ff87; }
        .negative { color: #ff4757; }

        .card-actions { display: flex; gap: 1rem; margin-top: 1.5rem; }

        .btn-small {
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
            border: none;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 0.9rem;
        }

        .btn-primary { background: linear-gradient(135deg, #00ff87, #00d4ff); color: #0a0e27; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(0, 255, 135, 0.3); }
        .btn-secondary { background: rgba(255, 255, 255, 0.05); color: #fff; border: 1px solid rgba(255, 255, 255, 0.1); }
        .btn-secondary:hover { background: rgba(255, 255, 255, 0.1); }

        /* Grid Layout */
        .dashboard-grid {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        /* Chart Card */
        .chart-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.05), rgba(255, 255, 255, 0.02));
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 15px;
            padding: 2rem;
        }

        .card-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem; }
        .card-title { font-size: 1.3rem; font-weight: 600; }

        .time-filter { display: flex; gap: 0.5rem; flex-wrap: wrap; }
        .time-btn {
            padding: 0.5rem 1rem;
            background: transparent;
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px;
            color: #b8bcc8;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 0.85rem;
        }
        .time-btn:hover, .time-btn.active { background: rgba(0, 255, 135, 0.1); border-color: #00ff87; color: #00ff87; }

        /* Quick Actions */
        .quick-actions { display: grid; grid-template-columns: repeat(2, 1fr); gap: 1rem; }
        .action-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.05), rgba(255, 255, 255, 0.02));
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            padding: 1.5rem;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
        }
        .action-card:hover { border-color: #00ff87; transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0, 255, 135, 0.2); }
        .action-icon { font-size: 2.5rem; margin-bottom: 1rem; }
        .action-title { font-size: 1rem; font-weight: 600; margin-bottom: 0.3rem; }
        .action-desc { font-size: 0.85rem; color: #b8bcc8; }

        /* Transactions & Watchlist */
        .transactions-card { grid-column: 1 / -1; }
        .watchlist { display: flex; flex-direction: column; gap: 1rem; }
        .watchlist-item {
            display: flex; justify-content: space-between; align-items: center;
            padding: 1rem; background: rgba(255, 255, 255, 0.03);
            border-radius: 10px; cursor: pointer; transition: all 0.3s;
        }
        .watchlist-item:hover { background: rgba(255, 255, 255, 0.05); }
        .watchlist-info h4 { font-size: 0.95rem; margin-bottom: 0.2rem; }
        .watchlist-info p { font-size: 0.8rem; color: #b8bcc8; }
        .watchlist-price { text-align: right; }
        .watchlist-amount { font-size: 1rem; font-weight: 600; margin-bottom: 0.2rem; }
        .watchlist-change { font-size: 0.85rem; }

        /* Mobile Menu Toggle */
        .menu-toggle {
            display: none;
            position: fixed;
            bottom: 2rem;
            right: 2rem;
            width: 60px; height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #00ff87, #00d4ff);
            border: none;
            color: #0a0e27;
            font-size: 1.5rem;
            cursor: pointer;
            box-shadow: 0 10px 30px rgba(0, 255, 135, 0.3);
            z-index: 1001;
        }

        /* --- RESPONSIVE FIXES START HERE --- */
        @media (max-width: 1200px) {
            .dashboard-grid { grid-template-columns: 1fr; }
        }

        @media (max-width: 968px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.active { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .menu-toggle { display: flex; align-items: center; justify-content: center; }
            .topbar { padding: 1rem 1.5rem; }
            .content-wrapper { padding: 1.5rem; } /* Reduced padding */
            .search-bar { display: none; }
        }

        @media (max-width: 600px) {
            .balance-cards {
                /* FIX: Use 100% instead of minmax 280px to prevent horizontal scroll */
                grid-template-columns: 1fr;
                gap: 1rem;
            }
            .quick-actions { grid-template-columns: 1fr; }
            .card-actions { flex-direction: column; }
            
            /* Reduce internal padding on small screens */
            .balance-card, .chart-card {
                padding: 1.5rem;
            }
            .page-title {
                font-size: 1.5rem;
            }
        }
        /* --- RESPONSIVE FIXES END --- */
    </style>
</head>
<body>
    <aside class="sidebar" id="sidebar">
        <div class="logo">Spacexinfo</div>
        
        <ul class="nav-menu">
            <li class="nav-item">
                <a href="#" class="nav-link active" onclick="navigate('dashboard')">
                    <span class="nav-icon">📊</span>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link" onclick="navigate('portfolio')">
                    <span class="nav-icon">💼</span>
                    <span>Portfolio</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link" onclick="navigate('markets')">
                    <span class="nav-icon">📈</span>
                    <span>Markets</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link" onclick="navigate('transactions')">
                    <span class="nav-icon">🔄</span>
                    <span>Transactions</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link" onclick="navigate('watchlist')">
                    <span class="nav-icon">⭐</span>
                    <span>Watchlist</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="#" class="nav-link" onclick="logoutUser()">
                    <span class="nav-icon">🚪</span>
                    <span>Logout</span>
                </a>
            </li>
        </ul>

        <div class="sidebar-footer">
            <div class="user-profile">
                <div class="user-avatar"><?php echo strtoupper(substr($_SESSION['username'], 0, 1)); ?></div>
                <div class="user-info">
                    <div class="user-name"><?php echo htmlspecialchars($_SESSION['username']); ?></div>
                    <div class="user-email">Account Active</div>
                </div>
            </div>
        </div>
    </aside>

    <main class="main-content" id="mainContent">
        <div class="topbar">
            <div class="search-bar">
                <span class="search-icon">🔍</span>
                <input type="text" id="searchInput" placeholder="Search stocks, crypto, or news...">
            </div>
            
            <div class="topbar-actions">
                <button class="icon-btn" onclick="toggleNotifications()">
                    <span>🔔</span>
                    <span class="notification-badge">3</span>
                </button>
                <button class="icon-btn" onclick="openSettings()">
                    <span>⚙️</span>
                </button>
            </div>
        </div>

        <div class="content-wrapper">
            <div class="page-header">
                <h1 class="page-title">Welcome back, <?php echo htmlspecialchars($_SESSION['username']); ?>! 👋</h1>
                <p class="page-subtitle">Here's what's happening with your trading today</p>
            </div>

            <div class="balance-cards">
                <div class="balance-card">
                    <div class="balance-card-header">
                        <div><div class="balance-label">Total Balance</div></div>
                        <div class="balance-icon">💰</div>
                    </div>
                    <div class="balance-amount">$<?php echo number_format($current_balance, 2); ?></div>
                    <div class="balance-change" style="color: #b8bcc8;"><span>No change yet</span></div>
                    <div class="card-actions">
                        <button class="btn-small btn-primary" onclick="openAction('deposit')">Deposit Funds</button>
                        <button class="btn-small btn-secondary" onclick="openAction('learn')">Learn More</button>
                    </div>
                </div>

                <div class="balance-card">
                    <div class="balance-card-header">
                        <div><div class="balance-label">Available Cash</div></div>
                        <div class="balance-icon">💵</div>
                    </div>
                    <div class="balance-amount">$<?php echo number_format($current_balance, 2); ?></div>
                    <div class="balance-change" style="color: #b8bcc8;"><span>Ready to start</span></div>
                    <div class="card-actions">
                        <button class="btn-small btn-primary" onclick="openAction('deposit')">Add Funds</button>
                    </div>
                </div>

                <div class="balance-card">
                    <div class="balance-card-header">
                        <div><div class="balance-label">Today's P&L</div></div>
                        <div class="balance-icon">📈</div>
                    </div>
                    <div class="balance-amount" style="color: #b8bcc8;">$0.00</div>
                    <div class="balance-change" style="color: #b8bcc8;"><span>Start trading today</span></div>
                </div>
            </div>

            <div class="dashboard-grid">
                <div class="chart-card">
                    <div class="card-header">
                        <h3 class="card-title">Portfolio Performance</h3>
                        <div class="time-filter" id="timeFilters">
                            <button class="time-btn" onclick="filterTime(this, '1D')">1D</button>
                            <button class="time-btn active" onclick="filterTime(this, '1W')">1W</button>
                            <button class="time-btn" onclick="filterTime(this, '1M')">1M</button>
                            <button class="time-btn" onclick="filterTime(this, '1Y')">1Y</button>
                            <button class="time-btn" onclick="filterTime(this, 'ALL')">ALL</button>
                        </div>
                    </div>
                    <div style="text-align: center; padding: 3rem 2rem;">
                        <div style="font-size: 4rem; margin-bottom: 1.5rem; opacity: 0.3;">📊</div>
                        <h3 style="font-size: 1.3rem; margin-bottom: 0.8rem; color: #fff;">Start Your Trading Journey</h3>
                        <p style="color: #b8bcc8; margin-bottom: 2rem;">Your portfolio performance will be displayed here once you make your first trade</p>
                        <button class="btn-small btn-primary" style="padding: 0.8rem 2rem;" onclick="openAction('markets')">Explore Markets</button>
                    </div>
                </div>

                <div class="chart-card">
                    <div class="card-header">
                        <h3 class="card-title">Quick Actions</h3>
                    </div>
                    <div class="quick-actions">
                        <div class="action-card" onclick="openAction('deposit')">
                            <div class="action-icon">💳</div>
                            <div class="action-title">Deposit</div>
                            <div class="action-desc">Add funds</div>
                        </div>
                        <div class="action-card" onclick="openAction('withdraw')">
                            <div class="action-icon">💸</div>
                            <div class="action-title">Withdraw</div>
                            <div class="action-desc">Transfer out</div>
                        </div>
                        <div class="action-card" onclick="openAction('transfer')">
                            <div class="action-icon">🔄</div>
                            <div class="action-title">Transfer</div>
                            <div class="action-desc">Move funds</div>
                        </div>
                        <div class="action-card" onclick="openAction('reports')">
                            <div class="action-icon">📊</div>
                            <div class="action-title">Reports</div>
                            <div class="action-desc">View stats</div>
                        </div>
                    </div>

                    <div style="margin-top: 2rem;">
                        <div class="card-header" style="margin-bottom: 1rem;">
                            <h3 class="card-title">Watchlist</h3>
                        </div>
                        <div class="watchlist">
                            <div class="watchlist-item" onclick="openAction('stock-aapl')">
                                <div class="watchlist-info">
                                    <h4>AAPL</h4><p>Apple Inc.</p>
                                </div>
                                <div class="watchlist-price">
                                    <div class="watchlist-amount">$178.23</div>
                                    <div class="watchlist-change positive">+1.45%</div>
                                </div>
                            </div>
                            <div class="watchlist-item" onclick="openAction('stock-tsla')">
                                <div class="watchlist-info">
                                    <h4>TSLA</h4><p>Tesla Inc.</p>
                                </div>
                                <div class="watchlist-price">
                                    <div class="watchlist-amount">$242.84</div>
                                    <div class="watchlist-change positive">+3.21%</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="chart-card transactions-card">
                <div class="card-header">
                    <h3 class="card-title">Recent Transactions</h3>
                </div>
                <div style="text-align: center; padding: 4rem 2rem;">
                    <div style="font-size: 4rem; margin-bottom: 1.5rem; opacity: 0.3;">📋</div>
                    <h3 style="font-size: 1.3rem; margin-bottom: 0.8rem; color: #fff;">No Transactions Yet</h3>
                    <p style="color: #b8bcc8; margin-bottom: 2rem;">Your transaction history will appear here once you start trading</p>
                    <button class="btn-small btn-primary" style="padding: 0.8rem 2rem;" onclick="openAction('markets')">Start Trading</button>
                </div>
            </div>
        </div>
    </main>

    <button class="menu-toggle" id="menuToggle" onclick="toggleMenu()">☰</button>

    <script>
        // 1. Mobile Menu Toggle
        function toggleMenu() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            sidebar.classList.toggle('active');
            
            // Close menu if clicking outside on mobile
            if (sidebar.classList.contains('active')) {
                document.addEventListener('click', closeMenuOutside);
            }
        }

        function closeMenuOutside(event) {
            const sidebar = document.getElementById('sidebar');
            const toggle = document.getElementById('menuToggle');
            
            if (!sidebar.contains(event.target) && !toggle.contains(event.target)) {
                sidebar.classList.remove('active');
                document.removeEventListener('click', closeMenuOutside);
            }
        }

        // 2. Time Filters (1D, 1W, 1M...)
        function filterTime(btn, period) {
            // Remove active class from all buttons
            const buttons = document.querySelectorAll('.time-btn');
            buttons.forEach(b => b.classList.remove('active'));
            
            // Add active class to clicked button
            btn.classList.add('active');
            
            // For now, just show a message. Later you can fetch data here.
            console.log("Switching view to: " + period);
        }

        // 3. Action Buttons Handler (Deposit, Withdraw, etc)
        function openAction(actionType) {
            switch(actionType) {
                case 'deposit':
                    alert("Opening Deposit Gateway...");
                    // window.location.href = '/deposit/'; // Uncomment to use real link
                    break;
                case 'withdraw':
                    alert("Opening Withdrawal Form...");
                    // window.location.href = '/withdraw/';
                    break;
                case 'markets':
                    alert("Redirecting to Markets...");
                    break;
                case 'learn':
                    alert("Opening Educational Resources...");
                    break;
                default:
                    if(actionType.includes('stock-')) {
                        alert("Viewing details for: " + actionType.replace('stock-', '').toUpperCase());
                    } else {
                        alert("Action: " + actionType);
                    }
            }
        }

        // 4. Navigation Handler
        function navigate(page) {
            // Remove active class from all links
            const links = document.querySelectorAll('.nav-link');
            links.forEach(l => l.classList.remove('active'));
            
            // Add active to clicked (this assumes simple click, normally handled by URL)
            event.currentTarget.classList.add('active');
            
            // Close mobile menu if open
            const sidebar = document.getElementById('sidebar');
            if(window.innerWidth <= 968) {
                sidebar.classList.remove('active');
            }
        }

        // 5. Search Function
        const searchInput = document.getElementById('searchInput');
        searchInput.addEventListener('keypress', function (e) {
            if (e.key === 'Enter') {
                alert('Searching for: ' + this.value);
            }
        });

        // 6. Logout
        function logoutUser() {
            if(confirm("Are you sure you want to logout?")) {
                // Redirect to a PHP logout script
                window.location.href = '/log-in/'; // Or create a logout.php file
            }
        }
        
        // 7. Toggle Notifications
        function toggleNotifications() {
            alert("You have 3 unread notifications:\n1. Welcome to Spacexinfo\n2. Profile Verified\n3. Market Alert: BTC up 5%");
        }
        
        function openSettings() {
            alert("Opening Account Settings...");
        }
    </script>
</body>
</html>