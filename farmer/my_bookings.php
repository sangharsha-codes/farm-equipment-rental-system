<?php
session_start();
include '../config/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "Farmer") {
    header("Location: ../login.php");
    exit();
}

$farmer_id = $_SESSION['user_id'];

$stmt = $conn->prepare("SELECT b.*, e.equipment_name, e.equipment_type, e.rental_price_per_day 
                         FROM bookings b JOIN equipment e ON b.equipment_id = e.equipment_id 
                         WHERE b.farmer_id = ? ORDER BY b.requested_at DESC");
$stmt->bind_param("i", $farmer_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head><title>My Bookings - FERS</title></head>
<body>
    <h2>My Bookings</h2>
    <table border="1" cellpadding="8">
        <tr>
            <th>Equipment</th><th>Type</th><th>Start Date</th><th>End Date</th>
            <th>Price/Day</th><th>Status</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['equipment_name']); ?></td>
            <td><?php echo $row['equipment_type']; ?></td>
            <td><?php echo $row['start_date']; ?></td>
            <td><?php echo $row['end_date']; ?></td>
            <td>Rs. <?php echo $row['rental_price_per_day']; ?></td>
            <td><b><?php echo $row['status']; ?></b></td>
        </tr>
        <?php endwhile; ?>
    </table>
    <p><a href="dashboard.php">Back to Dashboard</a></p>
</body>
</html>