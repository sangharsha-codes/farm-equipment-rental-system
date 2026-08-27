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
    <p>This is your dashboard. Equipment listing management will appear here.</p>
    <a href="../logout.php">Logout</a>
</body>
</html>