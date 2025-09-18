<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
  <link rel="icon" type="image/svg+xml" href="../images/logo.svg">
  <style>
    body {
      font-family: "Segoe UI", "Arial", sans-serif;
      background: linear-gradient(rgba(254, 237, 185, 0.9), rgba(158, 227, 240, 0.5));
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      margin: 0;
    }

    .login-card {
      background-color: #fff;
      width: 100%;
      max-width: 420px;
      padding: 40px 30px;
      border-radius: 15px;
      box-shadow: 0 8px 20px rgba(0,0,0,0.15);
      text-align: center;
    }

    .logo {
      width: 100px;
      margin-bottom: 15px;
    }

    h1 {
      font-size: 1.8em;
      font-weight: bold;
      color: #333;
      margin-bottom: 5px;
    }

    .tagline {
      font-size: 1em;
      color: #555;
      margin-bottom: 20px;
    }

    label {
      font-weight: bold;
      color: #444;
    }

    .form-control {
      border-radius: 8px;
      padding: 10px;
      border: 2px solid #FEEDB9;
    }

    .btn-custom {
      background-color: #FEEDB9;
      border: none;
      color: #000;
      font-weight: bold;
      width: 100%;
      height: 45px;
      border-radius: 8px;
      transition: all 0.3s ease;
    }

    .btn-custom:hover {
      background-color: #fde59b;
      color: #fff;
    }

    .register-text {
      margin-top: 20px;
      font-size: 0.9em;
      color: #555;
    }

    .register-text a {
      color: #007bff;
      font-weight: bold;
    }

    .register-text a:hover {
      color: #0056b3;
      text-decoration: underline;
    }
  </style>
</head>
<body>
  <div class="login-card">
      <img src="../images/writer_1.jpg" alt="logo" class="logo">
      <h1>Writer</h1>
      <p class="tagline">Login to your account</p>

      <?php  
      if (isset($_SESSION['message']) && isset($_SESSION['status'])) {
          $color = $_SESSION['status'] == "200" ? "green" : "red";
          echo "<h5 style='color: $color;'>{$_SESSION['message']}</h5>";
          unset($_SESSION['message']);
          unset($_SESSION['status']);
      }
      ?>

      <form method="POST" action="core/handleForms.php">
          <div class="form-group text-left">
              <label for="email">Email</label>
              <input type="text" class="form-control" name="email" required>
          </div>
          <div class="form-group text-left">
              <label for="password">Password</label>
              <input type="password" class="form-control" name="password" required>
          </div>
          <button type="submit" name="loginUserBtn" class="btn btn-custom mt-3">Login</button>
      </form>

      <p class="register-text">Don't have an account? <a href="register.php">Register here!</a></p>
  </div>
</body>
</html>