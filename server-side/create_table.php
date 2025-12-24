<?php
require_once __DIR__ . '/connection.php';


function createAdminTable($conn){
    $query = "CREATE TABLE IF NOT EXISTS admins (
            id INT NOT NULL AUTO_INCREMENT,
            adminname  VARCHAR(50) NOT NULL UNIQUE,
            password  VARCHAR(255) NOT NULL,
            role ENUM('admin', 'employee') NOT NULL DEFAULT 'employee',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id))";

    try{
        $conn->exec($query);

        $check_admin = $conn->prepare("SELECT COUNT(*) FROM `admins` WHERE role = ?");
        $check_admin->execute(['admin']);
        $admin_exists = $check_admin->fetchColumn();

        if ($admin_exists == 0) {
            $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
            $insert_data = $conn->prepare("INSERT INTO `admins` (adminname, password, role) VALUES (?, ?, ?)");
            $insert_data->execute(['admin', $hashedPassword, 'admin']);
            echo "Table created and default admin added successfully!";
        } else {
            echo "Default admin already exists.";
        }
    } catch (PDOException $e) {
        echo "Error Creating table: " . $e->getMessage();
    }
}

function createHallTable($conn){
    $query = "CREATE TABLE IF NOT EXISTS halls (
            id INT NOT NULL AUTO_INCREMENT,
            number INT NOT NULL,
            name  VARCHAR(50) NOT NULL,
            number_of_tables INT NOT NULL,
            capacity INT NOT NULL,
            details VARCHAR(256),
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id))";

    try{
        $conn->exec($query);
        echo "Table Created Successfully!";
    } catch (PDOException $e) {
        echo "Error Creating table: " . $e->getMessage();
    }
}

function createBeveragesTable($conn){
    $query = "CREATE TABLE IF NOT EXISTS beverages (
            id INT NOT NULL AUTO_INCREMENT,
            name  VARCHAR(50) NOT NULL,
            per_glass_price DECIMAL(10,2) NOT NULL,
            max_glass INT NOT NULL,
            max_bottle INT NOT NULL,
            per_bottle_price DECIMAL(10,2) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id))";

    try{
        $conn->exec($query);
        echo "Table Created Successfully!";
    } catch (PDOException $e) {
        echo "Error Creating table: " . $e->getMessage();
    }
}

function createFoodsTable($conn){
    $query = "CREATE TABLE IF NOT EXISTS foods (
            id INT NOT NULL AUTO_INCREMENT,
            name  VARCHAR(50) NOT NULL,
            per_plate_price DECIMAL(10,2) NOT NULL,
            max_plates INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id))";

    try{
        $conn->exec($query);
        echo "Table Created Successfully!";
    } catch (PDOException $e) {
        echo "Error Creating table: " . $e->getMessage();
    }
}

function createPaymentsTable($conn){
    $query = "CREATE TABLE IF NOT EXISTS payments (
            id CHAR(36) NOT NULL,
            amount DECIMAL(20,2) NOT NULL,
            client_id INT NULL,
            status ENUM('failed', 'pass', 'processing') NOT NULL DEFAULT 'failed',
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            FOREIGN KEY (client_id) REFERENCES clients(id))";
    try{
        $conn->exec($query);
        echo "Table Created Successfully!";
    } catch (PDOException $e) {
        echo "Error Creating table: " . $e->getMessage();
    }
}

function createClientsTable($conn){
    $query = "CREATE TABLE IF NOT EXISTS clients (
            id INT NOT NULL AUTO_INCREMENT,
            full_name  VARCHAR(100) NOT NULL,
            phone_number  VARCHAR(11) NOT NULL,
            email  VARCHAR(320) NOT NULL,
            address  VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id))";

    try{
        $conn->exec($query);
        echo "Table Created Successfully!";
    } catch (PDOException $e) {
        echo "Error Creating table: " . $e->getMessage();
    }
}

function createEventsTable($conn){
    $query = "CREATE TABLE IF NOT EXISTS events (
            id INT NOT NULL AUTO_INCREMENT,
            event_name  VARCHAR(50) NOT NULL,
            event_date  DATETIME NULL,
            num_participants INT NULL,
            location  VARCHAR(255) NULL,
            is_confirm BOOLEAN NOT NULL DEFAULT 0,
            status ENUM('paid', 'unpaid', 'processing') NOT NULL DEFAULT 'unpaid',
            event_image_url VARCHAR(255),
            event_fee DECIMAL(20,2) NOT NULL,
            details VARCHAR(256),
            hall_id INT NULL,
            client_id INT NULL,
            food_id INT NULL,
            beverages_id INT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            FOREIGN KEY (hall_id) REFERENCES halls(id),
            FOREIGN KEY (client_id) REFERENCES clients(id),
            FOREIGN KEY (food_id) REFERENCES foods(id),
            FOREIGN KEY (beverages_id) REFERENCES beverages(id))";

    try{
        $conn->exec($query);
        echo "Table Created Successfully!";
    } catch (PDOException $e) {
        echo "Error Creating table: " . $e->getMessage();
    }
}


function createShowEventsTable($conn){
    $query = "CREATE TABLE IF NOT EXISTS events (
            id INT NOT NULL AUTO_INCREMENT,
            event_name  VARCHAR(50) NOT NULL,
            event_date  DATETIME NOT NULL,
            num_participants INT NOT NULL,
            location  VARCHAR(255) NULL,
            is_confirm BOOLEAN NOT NULL DEFAULT 0,
            status ENUM('paid', 'unpaid', 'processing') NOT NULL DEFAULT 'unpaid',
            event_image_url VARCHAR(255),
            hall_id INT NOT NULL,
            client_id INT NOT NULL,
            food_id INT NOT NULL,
            beverages_id INT NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY (id),
            FOREIGN KEY (hall_id) REFERENCES halls(id),
            FOREIGN KEY (client_id) REFERENCES clients(id),
            FOREIGN KEY (food_id) REFERENCES foods(id),
            FOREIGN KEY (beverages_id) REFERENCES beverages(id))";

    try{
        $conn->exec($query);
        echo "Table Created Successfully!";
    } catch (PDOException $e) {
        echo "Error Creating table: " . $e->getMessage();
    }
}



function createAllTables(){
    createHallTable(connection());
    createBeveragesTable(connection());
    createFoodsTable(connection());
    createClientsTable(connection());
    createEventsTable(connection());
    createAdminTable(connection());
    createPaymentsTable(connection());
}