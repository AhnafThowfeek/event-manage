<?php
require_once __DIR__ .'/connection.php';

function getCountsList() {
    $db = connection();
    $listOfCounts = [
        'Booked Events' => 0,
        'Events' => 0,
        'Pending Payments' => 0,
        'Halls' => 0,
        'Earnings' => 0
    ];
    
    try {
        $query = $db->prepare("SELECT COUNT(*) as count FROM `events` WHERE `client_id` IS NOT NULL");
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);
        $listOfCounts['Booked Events'] = $result['count'] ?? 0;

        $query = $db->prepare("SELECT COUNT(*) as count FROM `events` WHERE `client_id` IS NULL");
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);
        $listOfCounts['Events'] = $result['count'] ?? 0;

        $query = $db->prepare("SELECT COUNT(*) as count FROM `events` WHERE `status` = 'processing'");
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);
        $listOfCounts['Pending Payments'] = $result['count'] ?? 0;

        $query = $db->prepare("SELECT COUNT(*) as count FROM `halls`");
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);
        $listOfCounts['Halls'] = $result['count'] ?? 0;

        $query = $db->prepare("SELECT SUM(amount) as total FROM `payments` WHERE `status` = 'paid'");
        $query->execute();
        $result = $query->fetch(PDO::FETCH_ASSOC);
        $listOfCounts['Earnings'] = $result['total'] ?? 0;

        return $listOfCounts;
    } catch (Exception $e) {
        error_log("Error fetching counts: " . $e->getMessage());

        return $listOfCounts;
    }
}
