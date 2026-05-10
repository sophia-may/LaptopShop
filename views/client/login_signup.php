<?php
require_once __DIR__ . '/../../controllers/AuthController.php';
$auth = new AuthController();

// Handle form submissions
$loginError = '';
$signupError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Login
  if (isset($_POST['login_submit'])) {
      $username = $_POST['login_username']; 
      $password = $_POST['login_password'];

      unset($_SESSION['is_guest']);

      $user = $auth->login($username, $password);

      if ($user) {
          if ($user['is_admin'] == 1) {
              header("Location: index.php?page=admin_dashboard");
          } else {
              header("Location: index.php?page=home");
          }
          exit();
      } else {
          $loginError = "Invalid username or password.";
      }
  }

  // Signup
  if (isset($_POST['signup_submit'])) {
      $username = $_POST['signup_username'];
      $password = $_POST['signup_password'];
      $confirmPassword = $_POST['signup_confirm_password'];

      if ($password !== $confirmPassword) {
          $signupError = "Passwords do not match.";
      } else {
          $success = $auth->signup($username, $password);
          if ($success) {
              $user = $auth->login($username, $password);
              if ($user) {
                  unset($_SESSION['is_guest']);
                  header("Location: index.php?page=home");
                  exit();
              }
          } else {
              $signupError = "This account already exists.";
          }
      }
  }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CellphoneS - Login / Signup</title>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <style>
    body {
        margin: 0;
        padding: 0;
        font-family: 'Inter', sans-serif;
        background-color: #f9f9f9;
        height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .auth-container {
        display: flex;
        width: 1000px;
        height: 600px;
        background: #fff;
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        border: 1px solid #e7bdb8;
    }
    .auth-left {
        flex: 1;
        background-color: #b70013;
        color: #ffffff;
        padding: 60px 40px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        position: relative;
        overflow: hidden;
    }
    .auth-left::before {
        content: "";
        position: absolute;
        top: 0; left: 0; right: 0; bottom: 0;
        background: url('assets/img/hero-bg.png') center/cover;
        opacity: 0.2;
        mix-blend-mode: overlay;
    }
    .auth-left-content {
        position: relative;
        z-index: 1;
    }
    .auth-left .logo {
        background: white;
        color: #b70013;
        font-weight: 800;
        display: inline-block;
        padding: 5px 15px;
        border-radius: 4px;
        margin-bottom: 40px;
        font-size: 20px;
    }
    .auth-left h1 {
        font-size: 36px;
        font-weight: 800;
        line-height: 1.2;
        margin-bottom: 20px;
    }
    .auth-left p {
        font-size: 14px;
        opacity: 0.9;
        line-height: 1.6;
        margin-bottom: 40px;
    }
    .auth-stats {
        display: flex;
        gap: 20px;
    }
    .stat-box {
        background: rgba(255, 255, 255, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        border-radius: 8px;
        padding: 15px 20px;
        flex: 1;
    }
    .stat-box .stat-label {
        font-size: 11px;
        text-transform: uppercase;
        font-weight: 700;
        opacity: 0.8;
        margin-bottom: 5px;
    }
    .stat-box .stat-value {
        font-size: 20px;
        font-weight: 700;
    }

    .auth-right {
        flex: 1;
        padding: 60px 50px;
        display: flex;
        flex-direction: column;
        justify-content: center;
        background: #ffffff;
    }
    .auth-right h2 {
        font-size: 24px;
        font-weight: 700;
        color: #1a1c1c;
        margin-bottom: 5px;
    }
    .auth-right .subtitle {
        color: #5d3f3c;
        font-size: 13px;
        margin-bottom: 25px;
    }
    
    .auth-toggle {
        display: flex;
        background: #f3f3f3;
        border-radius: 6px;
        padding: 4px;
        margin-bottom: 20px;
    }
    .auth-toggle button {
        flex: 1;
        border: none;
        background: transparent;
        padding: 8px;
        font-size: 13px;
        font-weight: 600;
        color: #5d3f3c;
        border-radius: 4px;
        transition: 0.3s;
    }
    .auth-toggle button.active {
        background: #ffffff;
        color: #b70013;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
    }
    
    .sso-buttons {
        display: flex;
        gap: 15px;
        margin-bottom: 25px;
    }
    .sso-btn {
        flex: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        border: 1px solid #e7bdb8;
        background: #ffffff;
        color: #5d3f3c;
        padding: 10px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        transition: 0.3s;
    }
    .sso-btn:hover {
        background: #f9f9f9;
    }

    .divider {
        display: flex;
        align-items: center;
        text-align: center;
        color: #926e6b;
        font-size: 11px;
        text-transform: uppercase;
        font-weight: 600;
        margin-bottom: 20px;
    }
    .divider::before, .divider::after {
        content: '';
        flex: 1;
        border-bottom: 1px solid #e7bdb8;
    }
    .divider::before { margin-right: 15px; }
    .divider::after { margin-left: 15px; }

    .form-group {
        margin-bottom: 15px;
    }
    .form-group label {
        display: block;
        font-size: 12px;
        font-weight: 700;
        color: #1a1c1c;
        margin-bottom: 5px;
    }
    .form-control {
        border: 1px solid #e7bdb8;
        border-radius: 6px;
        padding: 10px 15px;
        font-size: 14px;
        color: #1a1c1c;
        transition: 0.3s;
    }
    .form-control:focus {
        border-color: #b70013;
        box-shadow: 0 0 0 3px rgba(183, 0, 19, 0.1);
    }
    
    .btn-submit {
        background: #b70013;
        color: #ffffff;
        border: none;
        border-radius: 6px;
        padding: 12px;
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        width: 100%;
        margin-top: 10px;
        transition: 0.3s;
    }
    .btn-submit:hover {
        background: #93000a;
    }
    .terms {
        font-size: 11px;
        color: #5d3f3c;
        text-align: center;
        margin-top: 20px;
        line-height: 1.5;
    }
    .terms a {
        color: #b70013;
        font-weight: 600;
        text-decoration: none;
    }
    .form-section {
        display: none;
    }
    .form-section.active {
        display: block;
    }
  </style>
</head>
<body>

<div class="auth-container">
    <!-- Left Side -->
    <div class="auth-left">
        <div class="auth-left-content">
            <div class="logo">CellphoneS</div>
            <h1>The pulse of the digital retail era.</h1>
            <p>Access our full inventory of high-performance components, next-gen laptops, and exclusive deals. Manage your orders with precision and speed.</p>
            
            <div class="auth-stats">
                <div class="stat-box">
                    <div class="stat-label">Live Inventory</div>
                    <div class="stat-value">12k+ Items</div>
                </div>
                <div class="stat-box">
                    <div class="stat-label">Ship Time</div>
                    <div class="stat-value">< 24h</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Side -->
    <div class="auth-right">
        <h2>Welcome Back</h2>
        <div class="subtitle">Sign in to your account or create a new one to continue.</div>

        <div class="auth-toggle">
            <button class="active" id="btn-login" type="button" onclick="switchForm('login')">Sign In</button>
            <button id="btn-signup" type="button" onclick="switchForm('signup')">Create Account</button>
        </div>

        <div class="sso-buttons">
            <button type="button" class="sso-btn"><i class="bi bi-google"></i> Google</button>
            <button type="button" class="sso-btn"><i class="bi bi-apple"></i> Apple</button>
        </div>

        <div class="divider">Or continue with email</div>

        <!-- Login Form -->
        <form id="form-login" class="form-section active" method="POST">
            <?php if ($loginError): ?>
                <div class="alert alert-danger py-2 px-3" style="font-size: 13px;"><?= $loginError ?></div>
            <?php endif; ?>
            <div class="form-group">
                <label>Email Address / Username</label>
                <input type="text" name="login_username" class="form-control" placeholder="name@company.com" required>
            </div>
            <div class="form-group">
                <div class="d-flex justify-content-between">
                    <label>Password</label>
                    <a href="#" style="font-size: 11px; color: #b70013; text-decoration: none; font-weight: 600;">Forgot password?</a>
                </div>
                <input type="password" name="login_password" class="form-control" placeholder="********" required>
            </div>
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="remember">
                <label class="form-check-label" for="remember" style="font-weight: 400; color: #5d3f3c; margin-top: 1px;">
                    Remember this device for 30 days
                </label>
            </div>
            <button type="submit" name="login_submit" class="btn-submit">Secure Sign In</button>
        </form>

        <!-- Signup Form -->
        <form id="form-signup" class="form-section" method="POST">
            <?php if ($signupError): ?>
                <div class="alert alert-danger py-2 px-3" style="font-size: 13px;"><?= $signupError ?></div>
            <?php endif; ?>
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="signup_username" class="form-control" placeholder="Choose a username" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="signup_password" class="form-control" placeholder="********" required>
            </div>
            <div class="form-group">
                <label>Confirm Password</label>
                <input type="password" name="signup_confirm_password" class="form-control" placeholder="********" required>
            </div>
            <button type="submit" name="signup_submit" class="btn-submit">Create Account</button>
        </form>

        <div class="terms">
            By continuing, you agree to CellphoneS <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>.
            <br>
            <a href="index.php?page=home" style="display:inline-block; margin-top:10px;"><i class="bi bi-arrow-left"></i> Back to Home</a>
        </div>
    </div>
</div>

<script>
    function switchForm(formName) {
        document.getElementById('btn-login').classList.remove('active');
        document.getElementById('btn-signup').classList.remove('active');
        document.getElementById('form-login').classList.remove('active');
        document.getElementById('form-signup').classList.remove('active');

        document.getElementById('btn-' + formName).classList.add('active');
        document.getElementById('form-' + formName).classList.add('active');
    }

    // Keep signup form active if there's a signup error
    <?php if ($signupError): ?>
        switchForm('signup');
    <?php endif; ?>
</script>
</body>
</html>
