<?php
session_start();

// Protect this page - only logged-in Farmers can access it
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "Farmer") {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Farmer Dashboard</title>
</head>
<body>
    <h2>Welcome, <?php echo $_SESSION['full_name']; ?> (Farmer)</h2>
    <p>This is your dashboard. Equipment search and booking features will appear here.</p>
    <a href="../logout.php">Logout</a>
</body>
</html>