<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "Equipment Owner") {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Equipment Owner Dashboard</title>
</head>
<body>
    <h2>Welcome, <?php echo $_SESSION['full_name']; ?> (Equipment Owner)</h2>
    <p>This is your dashboard.</p>
<p><a href="add_equipment.php">+ Add New Equipment</a></p>
    <p><a href="manage_equipment.php">Manage My Equipment</a></p>
    <p><a href="manage_bookings.php">Manage Booking Requests</a></p>
    <p><a href="../logout.php">Logout</a></p>
</body>
</html>