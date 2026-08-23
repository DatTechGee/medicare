<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306', 'root', '', [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    echo "DB OK\n";
} catch(Exception $e) {
    echo "DB ERROR: " . $e->getMessage() . "\n";
}
