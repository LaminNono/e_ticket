<?php

require 'config.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')");
    $stmt->execute([$name, $email, $password]);

    header("Location: login.php");
    exit();
}

  
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'user')");
    $stmt->execute([$name, $email, $password]);
    header("Location: login.php");
    exit();
}
?>




<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Register</title>
  <link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
  <style>
    body {
      background: #e9e9e9;
      font-family: 'Roboto', sans-serif;
    }
    .form-container {
      max-width: 400px;
      margin: 80px auto;
      padding: 30px;
      background: white;
      border-top: 5px solid #33b5e5;
      box-shadow: 0 0 5px rgba(0,0,0,0.1);
    }
    h2 {
      text-align: center;
      color: #33b5e5;
    }
    input {
      width: 100%;
      padding: 12px;
      margin-bottom: 15px;
      border: 1px solid #ccc;
    }
    button {
      width: 100%;
      padding: 12px;
      background: #33b5e5;
      color: white;
      border: none;
      cursor: pointer;
    }
    button:hover {
      background: #0d8ddb;
    }
    .cta {
      text-align: center;
      margin-top: 10px;
    }
  </style>
</head>
<body>
  <div class="form-container">
    <h2>Create an Account</h2>
    <form method="POST">
      <input type="text" name="name" placeholder="Full Name" required>
      <input type="email" name="email" placeholder="Email" required>
      <input type="password" name="password" placeholder="Password" required>
      <button type="submit">Register</button>
    </form>
    <div class="cta">
      <a href="#">Forgot your password?</a>
    </div>
  </div>
</body>
</html>
