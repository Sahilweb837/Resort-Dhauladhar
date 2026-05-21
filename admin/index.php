<?php
require_once __DIR__ . '/../includes/functions.php';

// If already logged in, go to dashboard
if (isLoggedIn()) {
    header('Location: dashboard.php');
    exit();
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($email) || empty($password)) {
        $error = 'Please enter email and password';
    } else {
        // Check in database
        try {
            $pdo = getDB();
            $stmt = $pdo->prepare("SELECT * FROM admins WHERE email = ? OR username = ?");
            $stmt->execute([$email, $email]);
            $admin = $stmt->fetch();
            
            if ($admin) {
                // Support both plain-text and hashed passwords
                $passwordMatch = false;
                if (password_get_info($admin['password'])['algo'] !== null && password_get_info($admin['password'])['algo'] !== 0) {
                    $passwordMatch = password_verify($password, $admin['password']);
                } else {
                    $passwordMatch = ($password === $admin['password']);
                }

                if ($passwordMatch) {
                    $_SESSION['admin_logged_in'] = true;
                    $_SESSION['admin_id'] = $admin['id'];
                    $_SESSION['admin_name'] = $admin['name'];
                    $_SESSION['admin_email'] = $admin['email'];
                    header('Location: dashboard.php');
                    exit();
                } else {
                    $error = 'Invalid password';
                }
            } else {
                $error = 'User not found';
            }
        } catch (PDOException $e) {
            error_log("Admin login database error: " . $e->getMessage());
            $error = 'Database connection failed. Please try again later.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login — Dhauladhar Heights Resort</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=Montserrat:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        :root {
            --resort-primary: #1D285C;
            --resort-secondary: #5DC5E3;
            --resort-gold: #cf9a2c;
            --resort-dark: #0B162C;
            --resort-white: #ffffff;
            --resort-text: #404650;
        }

        body {
            font-family: 'Montserrat', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--resort-dark);
            position: relative;
            overflow: hidden;
        }

        /* Background Image with Overlay */
        body::before {
            content: '';
            position: fixed;
            inset: 0;
            background: url('../images/aboutbanner - Copy.jpg') center/cover no-repeat;
            filter: blur(3px);
            z-index: 0;
        }
        body::after {
            content: '';
            position: fixed;
            inset: 0;
            background: linear-gradient(135deg, rgba(11,22,44,0.92) 0%, rgba(29,40,92,0.88) 50%, rgba(11,22,44,0.95) 100%);
            z-index: 1;
        }

        .login-wrapper {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 440px;
            padding: 20px;
        }

        .login-container {
            background: rgba(255,255,255,0.03);
            backdrop-filter: blur(24px);
            -webkit-backdrop-filter: blur(24px);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 20px;
            padding: 48px 40px;
            box-shadow: 0 25px 80px rgba(0,0,0,0.4);
            animation: slideUp 0.6s ease;
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Logo Section */
        .login-logo {
            text-align: center;
            margin-bottom: 32px;
        }

        .login-logo img {
            height: 70px;
            margin-bottom: 16px;
            filter: brightness(1.1);
        }

        .login-logo h2 {
            font-family: 'Cormorant Garamond', serif;
            font-size: 22px;
            font-weight: 600;
            color: var(--resort-white);
            letter-spacing: 1px;
        }

        .login-logo small {
            display: block;
            font-size: 11px;
            color: var(--resort-secondary);
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-top: 6px;
            font-weight: 500;
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 22px;
            position: relative;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: rgba(255,255,255,0.7);
            font-size: 12px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .input-wrapper {
            position: relative;
        }

        .input-wrapper i {
            position: absolute;
            left: 16px;
            top: 50%;
            transform: translateY(-50%);
            color: var(--resort-secondary);
            font-size: 14px;
        }

        .form-group input {
            width: 100%;
            padding: 14px 16px 14px 44px;
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 10px;
            font-size: 14px;
            color: var(--resort-white);
            font-family: 'Montserrat', sans-serif;
            transition: all 0.3s ease;
            outline: none;
        }

        .form-group input::placeholder {
            color: rgba(255,255,255,0.3);
        }

        .form-group input:focus {
            border-color: var(--resort-secondary);
            background: rgba(93,197,227,0.06);
            box-shadow: 0 0 0 3px rgba(93,197,227,0.1);
        }

        /* Submit Button */
        .login-btn {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, var(--resort-primary) 0%, var(--resort-secondary) 100%);
            color: var(--resort-white);
            border: none;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            font-family: 'Montserrat', sans-serif;
            cursor: pointer;
            letter-spacing: 1px;
            text-transform: uppercase;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 30px rgba(93,197,227,0.3);
        }

        .login-btn:active {
            transform: translateY(0);
        }

        /* Error Message */
        .error-msg {
            padding: 14px 16px;
            border-radius: 10px;
            margin-bottom: 22px;
            background: rgba(225,112,85,0.12);
            color: #ff6b6b;
            border: 1px solid rgba(225,112,85,0.2);
            font-size: 13px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: shake 0.4s ease;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-6px); }
            75% { transform: translateX(6px); }
        }

        /* Footer */
        .login-footer {
            text-align: center;
            margin-top: 24px;
            padding-top: 20px;
            border-top: 1px solid rgba(255,255,255,0.06);
        }

        .login-footer a {
            color: var(--resort-secondary);
            text-decoration: none;
            font-size: 13px;
            font-weight: 500;
            transition: color 0.2s;
        }

        .login-footer a:hover {
            color: var(--resort-white);
        }

        /* Decorative Elements */
        .deco-circle {
            position: fixed;
            border-radius: 50%;
            border: 1px solid rgba(93,197,227,0.08);
            pointer-events: none;
            z-index: 2;
        }
        .deco-circle.c1 { width: 400px; height: 400px; top: -100px; right: -100px; }
        .deco-circle.c2 { width: 300px; height: 300px; bottom: -80px; left: -80px; }

        @media (max-width: 480px) {
            .login-container { padding: 36px 24px; }
            .login-logo img { height: 55px; }
        }
    </style>
</head>
<body>
    <div class="deco-circle c1"></div>
    <div class="deco-circle c2"></div>

    <div class="login-wrapper">
        <div class="login-container">
            <!-- Resort Logo -->
            <div class="login-logo">
                <img src="../images/dhr_logo_full_white_720.png" alt="Dhauladhar Heights Resort">
                <small>Admin Panel</small>
            </div>
            
            <?php if ($error): ?>
                <div class="error-msg">
                    <i class="fas fa-exclamation-circle"></i>
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" autocomplete="off">
                <div class="form-group">
                    <label>Email or Username</label>
                    <div class="input-wrapper">
                        <i class="fas fa-user"></i>
                        <input type="text" name="email" required placeholder="Enter your email or username"
                               value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>">
                    </div>
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <div class="input-wrapper">
                        <i class="fas fa-lock"></i>
                        <input type="password" name="password" required placeholder="Enter your password">
                    </div>
                </div>
                <button type="submit" class="login-btn">
                    <i class="fas fa-sign-in-alt"></i>&nbsp; Sign In
                </button>
            </form>

            <div class="login-footer">
                <a href="../index.php"><i class="fas fa-arrow-left"></i>&nbsp; Back to Website</a>
            </div>
        </div>
    </div>
</body>
</html>
