<?php
include "db.php";
session_start();

if (!isset($_SESSION['user_id'])) {
    die("Unauthorized Access");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $item_name = $_POST['item_name'];
    $category = $_POST['category'];
    $location = $_POST['location'];
    $description = $_POST['description'];
    $date_lost = $_POST['date_lost'];

    $stmt = $conn->prepare("INSERT INTO lost_items (user_id, item_name, category, location, description, date_lost) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssss", $user_id, $item_name, $category, $location, $description, $date_lost);

    if ($stmt->execute()) {
        echo "Lost item reported!";
    } else {
        echo "Error: " . $stmt->error;
    }

    $stmt->close();
}
?>
