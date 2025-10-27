<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Login Form</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    body {
      background-color: #f8f9fa;
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
      margin: 0;
    }
    .login-card {
      width: 100%;
      max-width: 400px;
      padding: 30px;
      border: 1px solid #ddd;
      border-radius: 10px;
      background-color: #ffffff;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .login-card h3 {
      margin-bottom: 20px;
      font-weight: bold;
      color: #343a40;
    }
    .form-control {
      border-radius: 5px;
    }
    .btn-primary {
      background-color: #007bff;
      border-color: #007bff;
    }
    .btn-primary:hover {
      background-color: #0056b3;
      border-color: #0056b3;
    }
    .btn-parent {
      background-color: #6c757d;
      border-color: #6c757d;
      color: white;
      display: block;
      text-align: center;
      padding: 10px;
      border-radius: 5px;
      text-decoration: none;
      margin-top: 10px;
    }
    .btn-parent:hover {
      background-color: #5a6268;
      color: white;
    }
  </style>
</head>
<body>
  <div class="login-card">
    <h3 class="text-center">Login</h3>
    <form action="proses_login.php" method="POST">
      <div class="mb-3">
        <label for="username" class="form-label">Username</label>
        <input type="text" class="form-control" id="username" name="username" placeholder="Enter your username" required>
      </div>
      <div class="mb-3">
        <label for="password" class="form-label">Password</label>
        <input type="password" class="form-control" id="password" name="password" placeholder="Enter your password" required>
      </div>
      <button type="submit" class="btn btn-primary w-100">Login</button>
      <div class="text-center mt-3">
        <a href="#" class="text-decoration-none">Forgot your password?</a>
      </div>
    </form>

    <!-- Tombol Login Sebagai Orang Tua -->
    <a href="login_ortu.php" class="btn-parent">Login sebagai Orang Tua</a>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
