<?php
$password = "admin123"; // change this to any password you want
$hash = password_hash($password, PASSWORD_DEFAULT);
echo "Hashed Password: " . $hash;
?>
