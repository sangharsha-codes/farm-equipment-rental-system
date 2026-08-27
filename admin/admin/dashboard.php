<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "Administrator") {
    header("Location: ../login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
</head>
<body>
    <h2>Welcome, <?php echo $_SESSION['full_name']; ?> (Administrator)</h2>
    <p>This is your dashboard. User and listing management will appear here.</p>
    <a href="../logout.php">Logout</a>
</body>
</html>