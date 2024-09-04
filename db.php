<?php
    $host = 'localhost';
    $db = 'license_module';
    $user = 'licensing';
    $pass = 'Str0ng@pass!';

    try {
        $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("Connection failed: " . $e->getMessage());
    }
    ?>

