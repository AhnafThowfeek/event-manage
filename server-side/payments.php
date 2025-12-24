<?php 
require_once __DIR__ . '/connection.php';
require_once __DIR__ . '/helperfunction.php';

function createNewPayment($amount, $status, $client_id) {
    $db = connection();
    $uuid = generateUUID();
    try {
        $insert_data = $db->prepare(
            "INSERT INTO `payments` (id, amount, status, client_id) VALUES (?, ?, ?, ?)"
        );
        $insert_data->execute([$uuid, $amount, $status, $client_id]);
        return "Create New payment Success!";
    } catch (PDOException $e) {
        return "Error When Creating Payment: " . $e->getMessage();
    }
}

function updateNewPayment($payment_id, $amount, $status, $client_id) {
    $db = connection();
    try {
        if($amount){
            $insert_amount = $db->prepare("UPDATE `payments` SET amount=? WHERE id=?");
            $insert_amount->execute([$amount, $payment_id]);
        }
        if($status){
            $insert_status = $db->prepare("UPDATE `payments` SET status=? WHERE id=?");
            $insert_status->execute([$status, $payment_id]);
        }
        if($client_id){
            $insert_client_id = $db->prepare("UPDATE `payments` SET client_id=? WHERE id=?");
            $insert_client_id->execute([ $client_id, $payment_id]);
        }
        return "Updated Payment Success!";
    } catch (PDOException $e) {
        return "Error When Updating Payment: " . $e->getMessage();
    }
}

function getAllPayment() {
    $db = connection();
    try {
        $query = $db->prepare(" SELECT
            payments.id, payments.amount, payments.status, payments.created_at, payments.updated_at, clients.full_name AS client_name, clients.phone_number AS client_phone 
            FROM payments
            LEFT JOIN clients ON payments.client_id = clients.id
        ");
        $query->execute();
        $payment = $query->fetchAll(PDO::FETCH_ASSOC);
        return $payment;
    } catch (PDOException $e) {
        echo("Error When Fetching payment: " . $e->getMessage());
        return;
    }
}

function getPaymentById($payment_id) {
    $db = connection();
    try {
        $query = $db->prepare("SELECT * FROM `payments` WHERE `id` = ?");
        $query->execute([$payment_id]);
        $payment = $query->fetchAll(PDO::FETCH_ASSOC);
        return $payment[0];
    } catch (PDOException $e) {
        return "Error When Get Payment: " . $e->getMessage();
    }
}

function sendPaymentMail($payment_id) {
    $db = connection();

    try {
        // Fetch client details based on payment_id
        $query = $db->prepare("
            SELECT c.id, c.full_name, c.email 
            FROM payments p 
            JOIN clients c ON p.client_id = c.id 
            WHERE p.id = ?
        ");
        $query->execute([$payment_id]);
        $clientData = $query->fetch(PDO::FETCH_ASSOC);

        // Check if client data exists
        if ($clientData) {
            $client_id = $clientData['id'];
            $client_name = $clientData['full_name'];
            $client_email = $clientData['email'];

            // Call the helper function to send the email
            if (sendMailByPayments($client_name, $client_email, "http://eventmanage.local/paymentgateway?c=$client_id&p=$payment_id")) {
                return "Mail sent successfully to {$client_email}.";
            } else {
                return "Failed to send mail to {$client_email}.";
            }
        } else {
            return "No client found for the provided payment ID.";
        }
    } catch (PDOException $e) {
        return "Error: " . $e->getMessage();
    }
}


function isPaymentVerify($client_id, $payment_id) {
    $db = connection();
    try {
        $query = $db->prepare("SELECT p.id FROM payments p JOIN clients c ON p.client_id = c.id WHERE p.id = ? AND c.id = ? AND p.status = 'processing'");
        $query->execute([$payment_id, $client_id]);

        if ($query->fetch()) {
            return true;
        } else {
            return false;
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return false;
    }
}

function isPaymentUpdateSubmition($client_id, $payment_id) {
    $db = connection();
    try {
        $query = $db->prepare("SELECT p.id FROM payments p JOIN clients c ON p.client_id = c.id WHERE p.id = ? AND c.id = ?");
        $query->execute([$payment_id, $client_id]);

        if ($query->fetch()) {
            $eventCheckQuery = $db->prepare("SELECT id FROM events WHERE client_id = ?");
            $eventCheckQuery->execute([$client_id]);

            if ($eventCheckQuery->fetch()) {
                $updatePaymentStatus = $db->prepare("UPDATE payments SET status = 'pass' WHERE id = ?");
                $updatePaymentStatus->execute([$payment_id]);

                $updateEventStatus = $db->prepare("UPDATE events SET status = 'paid' WHERE client_id = ?");
                $updateEventStatus->execute([$client_id]);

                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
        return false;
    }
}


