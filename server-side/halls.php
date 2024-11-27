<?php 
require_once __DIR__ . '/connection.php';

function createNewHall($number, $name, $number_of_tables, $capacity, $details) {
    $db = connection();
    try {
        $insert_data = $db->prepare(
            "INSERT INTO `halls` (number, name, number_of_tables, capacity, details) VALUES (?, ?, ?, ?, ?)"
        );
        $insert_data->execute([$number, $name, $number_of_tables, $capacity, $details]);
        return "Create New Hall Success!";
    } catch (PDOException $e) {
        return "Error When Creating Hall: " . $e->getMessage();
    }
}

function updateNewHall($hall_id, $number, $name, $number_of_tables, $capacity, $details) {
    $db = connection();
    try {
        if($number){
            $insert_number = $db->prepare("UPDATE `halls` SET number=? WHERE id=?");
            $insert_number->execute([$number, $hall_id]);
        }
        if($name){
            $insert_name = $db->prepare("UPDATE `halls` SET name=? WHERE id=?");
            $insert_name->execute([$name, $hall_id]);
        }
        if($number_of_tables){
            $insert_number_of_tables = $db->prepare("UPDATE `halls` SET number_of_tables=? WHERE id=?");
            $insert_number_of_tables->execute([$number_of_tables, $hall_id]);
        }
        if($capacity){
            $insert_capacity = $db->prepare("UPDATE `halls` SET capacity=? WHERE id=?");
            $insert_capacity->execute([$capacity, $hall_id]);
        }
        if($details){
            $insert_details = $db->prepare("UPDATE `halls` SET details=? WHERE id=?");
            $insert_details->execute([$details, $hall_id]);
        }
        return "Updated Hall Success!";
    } catch (PDOException $e) {
        return "Error When Updating Hall: " . $e->getMessage();
    }
}

function getAllHalls() {
    $db = connection();
    try {
        $query = $db->prepare("SELECT * FROM `halls`");
        $query->execute();
        $halls = $query->fetchAll(PDO::FETCH_ASSOC);
        return $halls;
    } catch (PDOException $e) {
        echo("Error When Fetching Halls: " . $e->getMessage());
        return;
    }
}

function deleteHallById($hall_id) {
    $db = connection();
    try {
        $query = $db->prepare("DELETE FROM `halls` WHERE `id` = ?");
        $query->execute([$hall_id]);
        return "Hall with ID $hall_id deleted successfully!";
    } catch (PDOException $e) {
        return "Error When Deleting Hall: " . $e->getMessage();
    }
}

function getHallById($hall_id) {
    $db = connection();
    try {
        $query = $db->prepare("SELECT * FROM `halls` WHERE `id` = ?");
        $query->execute([$hall_id]);
        $halls = $query->fetchAll(PDO::FETCH_ASSOC);
        return $halls[0];
    } catch (PDOException $e) {
        return "Error When Get Event: " . $e->getMessage();
    }
}



