<?php

require_once __DIR__ .'/connection.php';

function createNewClient($full_name, $phone_number, $email, $address){
    $db = connection();
    try {
        $insert_data = $db->prepare(
            "INSERT INTO `clients` (full_name, phone_number, email, address) VALUES (?, ?, ?, ?)"
        );
        $insert_data->execute([$full_name, $phone_number, $email, $address]);
        return "New Client Created Successfully!";
    } catch (PDOException $e) {
        return "Error When Creating Client: " . $e->getMessage();
    }
}


function getAllClients(){
    $db = connection();
    try {
        $query = $db->prepare("SELECT * FROM `clients`");
        $query->execute();
        $events = $query->fetchAll(PDO::FETCH_ASSOC);
        return $events;
    } catch (PDOException $e) {
        return "Error When Fetching Events: " . $e->getMessage();
    }
}

function registerNewClientFromEvents($client_full_name, $client_phone_number, $client_email, $client_address){
    $db = connection();

    try {
        $insert_data = $db->prepare(
            "INSERT INTO `clients` (full_name, phone_number, email, address) VALUES (?, ?, ?, ?)"
        );

        $insert_data->execute([$client_full_name, $client_phone_number, $client_email, $client_address]);

        $client_id = $db->lastInsertId();

        return $client_id;
    } catch (PDOException $e) {
        return "Error When Creating Client: " . $e->getMessage();
    }
}


