<?php
require_once __DIR__ . '/connection.php';

function adminLogin($adminname, $password){
    $db = connection();
    try {
        $stmt = $db->prepare("SELECT id, adminname, password, role FROM admins WHERE adminname = :adminname");
        $stmt->bindParam(':adminname', $adminname, PDO::PARAM_STR);
        $stmt->execute();

        $admin = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            $_SESSION['adminname'] = $admin['adminname'];
            $_SESSION['role'] = $admin['role'];

            return "Admin Login Success!";
        } else {
            return "Invalid admin name or password.";
        }
        return "New Food Item Created Successfully!";
    } catch (PDOException $e) {
        return "Error When Creating Food Item: " . $e->getMessage();
    }
}
