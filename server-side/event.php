<?php

require_once __DIR__ .'/connection.php';

function createNewFood($name, $per_plate_price, $max_plates) {
    $db = connection();
    try {
        $insert_data = $db->prepare(
            "INSERT INTO `foods` (name, per_plate_price, max_plates) VALUES (?, ?, ?)"
        );
        $insert_data->execute([$name, $per_plate_price, $max_plates]);
        return "New Food Item Created Successfully!";
    } catch (PDOException $e) {
        return "Error When Creating Food Item: " . $e->getMessage();
    }
}
function getAllFoods() {
    $db = connection();
    try {
        $query = $db->prepare("SELECT * FROM `foods`");
        $query->execute();
        $foods = $query->fetchAll(PDO::FETCH_ASSOC);
        return $foods;
    } catch (PDOException $e) {
        return "Error When Fetching Food Items: " . $e->getMessage();
    }
}
function deleteFoodsById($foods_id) {
    $db = connection();
    try {
        $query = $db->prepare("DELETE FROM `foods` WHERE `id` = ?");
        $query->execute([$foods_id]);
        return "Foods deleted successfully!";
    } catch (PDOException $e) {
        return "Error When Deleting Foods: " . $e->getMessage();
    }
}
function getFoodsById($foods_id) {
    $db = connection();
    try {
        $query = $db->prepare("SELECT * FROM `foods` WHERE `id` = ?");
        $query->execute([$foods_id]);
        $events = $query->fetchAll(PDO::FETCH_ASSOC);
        return $events[0];
    } catch (PDOException $e) {
        return "Error When Get Event: " . $e->getMessage();
    }
}
function updateFood($food_id, $name, $per_plate_price, $max_plates) {
    $db = connection();
    try {
        if($name){
            $insert_name = $db->prepare("UPDATE `foods` SET name=? WHERE id=?");
            $insert_name->execute([$name, $food_id]);
        }
        if($per_plate_price){
            $insert_per_plate_price = $db->prepare("UPDATE `foods` SET per_plate_price=? WHERE id=?");
            $insert_per_plate_price->execute([$per_plate_price, $food_id]);
        }
        if($max_plates){
            $insert_max_plates = $db->prepare("UPDATE `foods` SET max_plates=? WHERE id=?");
            $insert_max_plates->execute([$max_plates, $food_id]);
        }
        return "Food Updated Successfully!";
    } catch (PDOException $e) {
        return "Error When Get Food: " . $e->getMessage();
    }
}


function createNewBeverage($name, $per_glass_price, $max_glass, $max_bottle, $per_bottle_price) {
    $db = connection();
    try {
        $insert_data = $db->prepare(
            "INSERT INTO `beverages` (name, per_glass_price, max_glass, max_bottle, per_bottle_price) VALUES (?, ?, ?, ?, ?)"
        );
        $insert_data->execute([$name, $per_glass_price, $max_glass, $max_bottle, $per_bottle_price]);
        return "New Beverage Created Successfully!";
    } catch (PDOException $e) {
        return "Error When Creating Beverage: " . $e->getMessage();
    }
}
function getAllBeverages() {
    $db = connection();
    try {
        $query = $db->prepare("SELECT * FROM `beverages`");
        $query->execute();
        $beverages = $query->fetchAll(PDO::FETCH_ASSOC);
        return $beverages;
    } catch (PDOException $e) {
        return "Error When Fetching Beverages: " . $e->getMessage();
    }
}
function deleteBeveragesById($beverages_id) {
    $db = connection();
    try {
        $query = $db->prepare("DELETE FROM `beverages` WHERE `id` = ?");
        $query->execute([$beverages_id]);
        return "Beverages deleted successfully!";
    } catch (PDOException $e) {
        return "Error When Deleting Beverages: " . $e->getMessage();
    }
}
function getBeveragesById($beverage_id) {
    $db = connection();
    try {
        $query = $db->prepare("SELECT * FROM `beverages` WHERE `id` = ?");
        $query->execute([$beverage_id]);
        $events = $query->fetchAll(PDO::FETCH_ASSOC);
        return $events[0];
    } catch (PDOException $e) {
        return "Error When Get beverages: " . $e->getMessage();
    }
}
function updateBeverage($beverage_id, $name, $per_glass_price, $max_glass, $max_bottle, $per_bottle_price) {
    $db = connection();
    try {
        if($name){
            $insert_name = $db->prepare("UPDATE `beverages` SET name=? WHERE id=?");
            $insert_name->execute([$name, $beverage_id]);
        }
        if($per_glass_price){
            $insert_per_glass_price = $db->prepare("UPDATE `beverages` SET per_glass_price=? WHERE id=?");
            $insert_per_glass_price->execute([$per_glass_price, $beverage_id]);
        }
        if($max_glass){
            $insert_max_glass = $db->prepare("UPDATE `beverages` SET max_glass=? WHERE id=?");
            $insert_max_glass->execute([$max_glass, $beverage_id]);
        }
        if($max_bottle){
            $insert_max_bottle = $db->prepare("UPDATE `beverages` SET max_bottle=? WHERE id=?");
            $insert_max_bottle->execute([$max_bottle, $beverage_id]);
        }
        if($per_bottle_price){
            $insert_per_bottle_price = $db->prepare("UPDATE `beverages` SET per_bottle_price=? WHERE id=?");
            $insert_per_bottle_price->execute([$per_bottle_price, $beverage_id]);
        }
        return "Beverage Updated Successfully!";
    } catch (PDOException $e) {
        return "Error When Updating Beverage: " . $e->getMessage();
    }
}

function createNewEvent($event_name, $location, $is_confirm, $status, $details, $event_image, $hall_id, $food_id = null, $beverages_id = null, $event_fee){
    $db = connection();

    try {
        $upload_dir = 'assets/events/';
        $image_path_url = $upload_dir . uniqid() . '_' . basename($event_image['name']);
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        if (!move_uploaded_file($event_image['tmp_name'], $image_path_url)) {
            return  "Failed to save the event image.";
        }

        if(!$beverages_id && !$food_id){
            $insert_data = $db->prepare(
                "INSERT INTO `events` (event_name, location, is_confirm, status, event_fee, details, event_image_url, hall_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?)"
            );
            $insert_data->execute([
                $event_name,
                $location,
                $is_confirm,
                $status,
                $event_fee,
                $details,
                $image_path_url,
                $hall_id
            ]);
        } elseif(!$food_id){
            $insert_data = $db->prepare(
                "INSERT INTO `events` (event_name, location, is_confirm, status, event_fee, details, event_image_url, hall_id, beverages_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );
            $insert_data->execute([
                $event_name,
                $location,
                $is_confirm,
                $status,
                $event_fee,
                $details,
                $image_path_url,
                $hall_id,
                $beverages_id
            ]);
        } elseif(!$beverages_id){
            $insert_data = $db->prepare(
                "INSERT INTO `events` (event_name, location, is_confirm, status, event_fee, details, event_image_url, hall_id, food_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );
            $insert_data->execute([
                $event_name,
                $location,
                $is_confirm,
                $status,
                $event_fee,
                $details,
                $image_path_url,
                $hall_id,
                $food_id
            ]);
        } else {
            $insert_data = $db->prepare(
                "INSERT INTO `events` (event_name, location, is_confirm, status, event_fee, details, event_image_url, hall_id, food_id, beverages_id) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );
            $insert_data->execute([
                $event_name,
                $location,
                $is_confirm,
                $status,
                $event_fee,
                $details,
                $image_path_url,
                $hall_id,
                $food_id,
                $beverages_id,
            ]);
        }

        return "New Event Created Successfully!";
    } catch (PDOException $e) {
        return "Error When Creating Event: " . $e->getMessage();
    } catch (Exception $e) {
        return "Error: " . $e->getMessage();
    }
}
function updateEvent($event_id, $event_name, $location, $is_confirm, $status, $details, $event_image = null, $hall_id, $food_id = null, $beverages_id = null, $event_fee){
    $db = connection();

    try {

        if($event_name){
            $insert_event_name = $db->prepare("UPDATE `events` SET event_name=? WHERE id=?");
            $insert_event_name->execute([$event_name, $event_id]);
        }
        if($location){
            $insert_location = $db->prepare("UPDATE `events` SET location=? WHERE id=?");
            $insert_location->execute([$location, $event_id]);
        }
        if($is_confirm){
            $insert_is_confirm = $db->prepare("UPDATE `events` SET is_confirm=? WHERE id=?");
            $insert_is_confirm->execute([$is_confirm, $event_id]);
        }
        if($status){
            $insert_status = $db->prepare("UPDATE `events` SET status=? WHERE id=?");
            $insert_status->execute([$status, $event_id]);
        }
        if($details){
            $insert_details = $db->prepare("UPDATE `events` SET details=? WHERE id=?");
            $insert_details->execute([$details, $event_id]);
        }
        print_r($event_image);
        if($event_image['size'] > 0){
            try{
                $upload_dir = 'assets/events/';
                $image_path_url = $upload_dir . uniqid() . '_' . basename($event_image['name']);
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true);
                }
                move_uploaded_file($event_image['tmp_name'], $image_path_url);
                $insert_event_image = $db->prepare("UPDATE `events` SET event_image_url=? WHERE id=?");
                $insert_event_image->execute([$image_path_url, $event_id]);
            } catch (Exception $e) {
                echo("Image Update Error: " . $e->getMessage());
            }
        }
        if($hall_id){
            $insert_hall_id = $db->prepare("UPDATE `events` SET hall_id=? WHERE id=?");
            $insert_hall_id->execute([$hall_id, $event_id]);
        }
        if($food_id){
            $insert_food_id = $db->prepare("UPDATE `events` SET food_id=? WHERE id=?");
            $insert_food_id->execute([$food_id, $event_id]);
        }
        if($beverages_id){
            $insert_beverages_id = $db->prepare("UPDATE `events` SET beverages_id=? WHERE id=?");
            $insert_beverages_id->execute([$beverages_id, $event_id]);
        }
        if($event_fee){
            $insert_food_id = $db->prepare("UPDATE `events` SET event_fee=? WHERE id=?");
            $insert_food_id->execute([$event_fee, $event_id]);
        }

        return "Event Updated Successfully!";
    } catch (PDOException $e) {
        return "Error When Updating Event: " . $e->getMessage();
    } catch (Exception $e) {
        return "Error: " . $e->getMessage();
    }
}

function getAllEvents($section) {
    $db = connection();
    $query = null;
    try {
        if($section === 'booked-events'){
            $query = $db->prepare("SELECT * FROM `events` WHERE `client_id` IS NOT NULL");
           
        } elseif($section === 'events') {
            $query = $db->prepare("SELECT * FROM `events` WHERE `client_id` IS NULL");
        }

        if ($query === null) {
            throw new Exception("Invalid section parameter");
        }
        $query->execute();
        $events = $query->fetchAll(PDO::FETCH_ASSOC);
        return $events;
    } catch (Exception $e) {
        return [];
    }
}


function getEventsCreatedList() {
    $db = connection();
    try {
        $query = $db->prepare("SELECT * FROM `events` WHERE `client_id` IS NULL");
        $query->execute();
        $events = $query->fetchAll(PDO::FETCH_ASSOC);
        return $events;
    } catch (PDOException $e) {
        return "Error When Fetching Events with NULL client_id: " . $e->getMessage();
    }
}

function deleteEventById($event_id) {
    $db = connection();
    try {
        $query = $db->prepare("DELETE FROM `events` WHERE `id` = ?");
        $query->execute([$event_id]);

        return "Event deleted successfully!";
    } catch (PDOException $e) {
        return "Error When Deleting Event: " . $e->getMessage();
    }
}

function getEventById($event_id) {
    $db = connection();
    try {
        $query = $db->prepare("SELECT * FROM `events` WHERE `id` = ?");
        $query->execute([$event_id]);
        $events = $query->fetchAll(PDO::FETCH_ASSOC);
        return $events[0];
    } catch (PDOException $e) {
        return "Error When Get Event: " . $e->getMessage();
    }
}

function createClientEvent($client_id, $select_event, $event_date, $num_participants, $event_location, $is_confirm, $status, $hall_id, $food_id = null, $beverages_id = null) {
    $db = connection();
    try {
        $base_query = "INSERT INTO `events` (client_id, event_name, event_date, num_participants, location, is_confirm, status, hall_id";
        $values_placeholders = "?, ?, ?, ?, ?, ?, ?, ?";
        $values = [ $client_id, $select_event, $event_date, $num_participants, $event_location, $is_confirm, $status, $hall_id];

        if ($food_id !== null) {
            $base_query .= ", food_id";
            $values_placeholders .= ", ?";
            $values[] = $food_id;
        }
        if ($beverages_id !== null) {
            $base_query .= ", beverages_id";
            $values_placeholders .= ", ?";
            $values[] = $beverages_id;
        }

        $base_query .= ") VALUES ($values_placeholders)";
        $insert_data = $db->prepare($base_query);

        $insert_data->execute($values);

        return "Client Event Created Successfully!";
    } catch (PDOException $e) {
        error_log("Error When Creating Client Event: " . $e->getMessage(), 3, __DIR__ . "/error.log");
        return "Error When Creating Client Event: " . $e->getMessage();
    } catch (Exception $e) {
        error_log("Error: " . $e->getMessage(), 3, __DIR__ . "/error.log");
        return "Error: " . $e->getMessage();
    }
}






