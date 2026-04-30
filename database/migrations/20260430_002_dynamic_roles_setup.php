<?php
$cols=$db->query("SHOW COLUMNS FROM users LIKE 'role_id'")->fetchAll();
if(!$cols) $db->exec("ALTER TABLE users ADD role_id INT NULL AFTER department_id");
$db->exec("ALTER TABLE users MODIFY role VARCHAR(100) DEFAULT 'staff'");
$db->exec("CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    department_id INT NULL,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) NOT NULL UNIQUE,
    description TEXT,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
$st=$db->prepare("INSERT IGNORE INTO departments(name,description,is_active) VALUES(?,?,1)");
$st->execute(['IT','Information technology and system maintenance']);
$dept=function($name) use($db) {
    $s=$db->prepare("SELECT id FROM departments WHERE name=?");
    $s->execute([$name]);
    return (int)$s->fetchColumn();
};
$role=$db->prepare("INSERT IGNORE INTO roles(department_id,name,slug,description,is_active) VALUES(?,?,?,?,1)");
$role->execute([$dept('Super Admin'),'Super Admin','superadmin','Full system access']);
$role->execute([$dept('Administration'),'Admin','admin','Administration access']);
$role->execute([$dept('Administration'),'Staff','staff','Standard staff access']);
$role->execute([$dept('IT'),'IT','it','IT department access']);
$roles=[];
foreach($db->query("SELECT id,slug FROM roles") as $r) $roles[$r['slug']]=(int)$r['id'];
$up=$db->prepare("UPDATE users SET role_id=? WHERE id=?");
foreach($db->query("SELECT id,role FROM users") as $u) {
    if(isset($roles[$u['role']])) $up->execute([$roles[$u['role']],$u['id']]);
}
$db->exec("INSERT IGNORE INTO site_settings(setting_key,setting_value) VALUES ('timezone','Asia/Kolkata')");
