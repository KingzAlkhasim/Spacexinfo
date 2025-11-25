<?php
/*
Template Name: Spacexinfo Sign In
Template Post Type: page
*/

session_start();

// 1. DATABASE CONNECTION
$servername = "localhost";
$dbusername = "spacenet_spacexinfo"; 
$dbpassword = "@#passNet"; 
$dbname = "spacenet_spacexinfo_userdb";

$login_error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Connect to DB
    $conn = new mysqli($servername, $dbusername, $dbpassword, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $email = $_POST['email'];
    $password = $_POST['password'];

    // 2. CHECK IF USER EXISTS
    // IMPORTANT: We added 'role' and 'email' to this line so we can use them later
    $stmt = $conn->prepare("SELECT id, username, email, password, role FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $row = $result->fetch_assoc();
        
        // 3. VERIFY PASSWORD
        if (password_verify($password, $row['password'])) {
            
            // Set Session Variables
            $_SESSION['user_id'] = $row['id'];
            $_SESSION['username'] = $row['username'];
            $_SESSION['email']    = $row['email'];
            $_SESSION['role']     = $row['role']; // This is what differentiates Admin vs User
            $_SESSION['logged_in'] = true;

            // 4. ROLE BASED REDIRECT
            // If the database says they are 'admin', send to admin panel
            if (isset($row['role']) && $row['role'] === 'admin') {
                echo "<script>window.location.href = '/admin-panel/';</script>";
            } else {
                // Otherwise, send to normal dashboard
                echo "<script>window.location.href = '/dashboard/';</script>";
            }
            exit();

        } else {
            $login_error = "Invalid password.";
        }
    } else {
        $login_error = "No account found with that email.";
    }

    $stmt->close();
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en"> 
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Spacexinfo - Sign In</title>
    <style>
        /* CSS STYLES - SAME AS YOUR ORIGINAL */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #0a0e27;
            color: #ffffff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }
        body::before {
            content: ''; position: absolute; width: 500px; height: 500px;
            background: radial-gradient(circle, rgba(0, 255, 135, 0.15), transparent);
            top: -200px; right: -200px; border-radius: 50%;
            animation: pulse 4s ease-in-out infinite;
        }
        body::after {
            content: ''; position: absolute; width: 400px; height: 400px;
            background: radial-gradient(circle, rgba(0, 212, 255, 0.15), transparent);
            bottom: -150px; left: -150px; border-radius: 50%;
            animation: pulse 5s ease-in-out infinite;
        }
        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.5; }
            50% { transform: scale(1.1); opacity: 0.8; }
        }
        .container {
            position: relative; z-index: 1; max-width: 1100px; width: 100%;
            padding: 2rem; display: flex; gap: 2rem;
        }
        .auth-card {
            background: linear-gradient(135deg, rgba(255, 255, 255, 0.05), rgba(255, 255, 255, 0.02));
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px; padding: 3rem; flex: 1;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.5); transition: all 0.3s;
        }
        .logo {
            font-size: 2rem; font-weight: bold;
            background: linear-gradient(135deg, #00ff87, #00d4ff);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent;
            background-clip: text; margin-bottom: 0.5rem;
        }
        .auth-card h2 { font-size: 1.8rem; margin-bottom: 0.5rem; color: #fff; }
        .auth-card p { color: #b8bcc8; margin-bottom: 2rem; }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label { display: block; margin-bottom: 0.5rem; color: #fff; font-weight: 500; }
        .form-group input {
            width: 100%; padding: 0.9rem 1.2rem;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 10px; color: #fff; font-size: 1rem; transition: all 0.3s;
        }
        .form-group input:focus { outline: none; border-color: #00ff87; background: rgba(255, 255, 255, 0.08); }
        .password-wrapper { position: relative; }
        .password-toggle {
            position: absolute; right: 1rem; top: 50%; transform: translateY(-50%);
            background: none; border: none; color: #b8bcc8; cursor: pointer; font-size: 1.2rem;
        }
        .checkbox-group { display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1.5rem; }
        .checkbox-group label { color: #b8bcc8; cursor: pointer; }
        .btn {
            width: 100%; padding: 1rem; border: none; border-radius: 10px;
            font-weight: 600; font-size: 1rem; cursor: pointer; transition: all 0.3s; margin-bottom: 1rem;
        }
        .btn-primary { background: linear-gradient(135deg, #00ff87, #00d4ff); color: #0a0e27; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 10px 30px rgba(0, 255, 135, 0.3); }
        .social-login { display: flex; gap: 1rem; margin-bottom: 1.5rem; }
        .social-btn {
            flex: 1; padding: 0.9rem; background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 10px; color: #fff;
            cursor: pointer; display: flex; align-items: center; justify-content: center; gap: 0.5rem;
        }
        .forgot-password { text-align: right; margin-top: -0.5rem; margin-bottom: 1.5rem; }
        .forgot-password a { color: #00ff87; text-decoration: none; cursor: pointer; }
        .features-sidebar {
            flex: 1; display: flex; flex-direction: column; justify-content: center; padding: 2rem;
        }
        .features-sidebar h3 {
            font-size: 2rem; margin-bottom: 1.5rem;
            background: linear-gradient(135deg, #ffffff, #00ff87);
            -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;
        }
        .feature-item { display: flex; gap: 1rem; margin-bottom: 2rem; align-items: start; }
        .feature-icon { font-size: 2rem; flex-shrink: 0; }
        .feature-content h4 { font-size: 1.2rem; margin-bottom: 0.5rem; color: #fff; }
        .feature-content p { color: #b8bcc8; line-height: 1.6; }
        
        .error-message {
            background: rgba(255, 71, 87, 0.1); border: 1px solid rgba(255, 71, 87, 0.3);
            color: #ff4757; padding: 0.8rem; border-radius: 8px; margin-bottom: 1rem;
            font-size: 0.9rem; display: block; text-align: center;
        }
        
        @media (max-width: 968px) {
            .container { flex-direction: column; }
            .features-sidebar { display: none; }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="auth-card" id="loginCard">
            <div class="logo">Spacexinfo</div>
            <h2>Welcome Back</h2>
            <p>Sign in to continue trading</p>

            <?php if(!empty($login_error)): ?>
                <div class="error-message"><?php echo $login_error; ?></div>
            <?php endif; ?>
            
            <form id="loginForm" method="POST" action="">
                <div class="form-group">
                    <label>Email Address</label>
                    <input type="email" name="email" placeholder="your.email@example.com" required>
                </div>

                <div class="form-group">
                    <label>Password</label>
                    <div class="password-wrapper">
                        <input type="password" name="password" id="loginPassword" placeholder="Enter your password" required>
                        <button type="button" class="password-toggle" onclick="togglePassword('loginPassword')">👁️</button>
                    </div>
                </div>

                <div class="checkbox-group">
                    <input type="checkbox" id="remember" name="remember">
                    <label for="remember">Remember me</label>
                </div>

                <div class="forgot-password">
                    <a onclick="showForgotPassword()">Forgot Password?</a>
                </div>

                <button type="submit" class="btn btn-primary">Sign In</button>
            </form>

            <div class="social-login">
                <button class="social-btn" onclick="socialLogin('Google')">
                    <span>🔍</span> <span>Google</span>
                </button>
                <button class="social-btn" onclick="socialLogin('Apple')">
                    <span>🍎</span> <span>Apple</span>
                </button>
            </div>
        </div>

        <div class="features-sidebar">
            <h3>Join Millions of Traders Worldwide</h3>
            <div class="feature-item">
                <div class="feature-icon">⚡</div>
                <div class="feature-content">
                    <h4>Lightning Fast Trading</h4>
                    <p>Execute trades in milliseconds with our advanced infrastructure</p>
                </div>
            </div>
            <div class="feature-item">
                <div class="feature-icon">🛡️</div>
                <div class="feature-content">
                    <h4>Bank-Level Security</h4>
                    <p>Your funds are protected with 256-bit encryption and 2FA</p>
                </div>
            </div>
            <div class="feature-item">
                <div class="feature-icon">💰</div>
                <div class="feature-content">
                    <h4>Zero Commission</h4>
                    <p>Trade stocks and ETFs without paying any commission fees</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle password visibility
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            input.type = input.type === 'password' ? 'text' : 'password';
        }

        // Removed the previous JS submit handler because we want the form 
        // to submit naturally to the PHP code at the top of the page.

        // Social login handler (Visual only)
        function socialLogin(provider) {
            alert(`Signing in with ${provider}... (Requires API Setup)`);
        }

        // Forgot password handler (Visual only)
        function showForgotPassword() {
            const email = prompt('Enter your email address to reset your password:');
            if (email) {
                alert('Password reset link has been sent to ' + email);
            }
        }
    </script>
    <?php wp_footer(); ?> 
</body>

</html>
