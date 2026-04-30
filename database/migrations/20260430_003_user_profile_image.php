<?php
$cols=$db->query("SHOW COLUMNS FROM users LIKE 'profile_image'")->fetchAll();
if(!$cols) {
    $db->exec("ALTER TABLE users ADD profile_image VARCHAR(255) DEFAULT NULL AFTER role");
}
