<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "Farmer") {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head><title>Farmer Dashboard</title></head>
<body>
    <h2>Welcome, <?php echo $_SESSION['full_name']; ?> (Farmer)</h2>
    <p><a href="search_equipment.php">Search Equipment</a></p>
   <p><a href="my_bookings.php">My Bookings</a></p>
    <a href="../logout.php">Logout</a>
</body>
</html>