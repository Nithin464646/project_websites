<?php
include "db.php";

$sql = "SELECT item_name, category, location, description, date_lost FROM lost_items WHERE status='Lost'";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()) {
    echo "<div>";
    echo "<h3>" . $row['item_name'] . "</h3>";
    echo "<p>Category: " . $row['category'] . "</p>";
    echo "<p>Location: " . $row['location'] . "</p>";
    echo "<p>Description: " . $row['description'] . "</p>";
    echo "<p>Date Lost: " . $row['date_lost'] . "</p>";
    echo "</div><hr>";
}
?>
