<?php
    $host = 'localhost';
    $db = 'license_module';
    $user = 'root';
    $pass = 'NewPassword!';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
    ?>

