<?php
session_start();
include '../config/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "Farmer") {
    header("Location: ../login.php");
    exit();
}

$farmer_id = $_SESSION['user_id'];
$error = "";
$success = "";
$equipment_id = $_GET['equipment_id'] ?? $_POST['equipment_id'] ?? null;

if (!$equipment_id) {
    die("No equipment selected.");
}

// Get equipment details
$stmt = $conn->prepare("SELECT * FROM equipment WHERE equipment_id = ?");
$stmt->bind_param("i", $equipment_id);
$stmt->execute();
$equipment = $stmt->get_result()->fetch_assoc();

if (!$equipment) {
    die("Equipment not found.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    $stmt = $conn->prepare("INSERT INTO bookings (equipment_id, farmer_id, start_date, end_date, status) VALUES (?, ?, ?, ?, 'Pending')");
    $stmt->bind_param("iiss", $equipment_id, $farmer_id, $start_date, $end_date);

    if ($stmt->execute()) {
        $success = "Booking request submitted! Status: Pending approval.";
    } else {
        $error = "Something went wrong. Please try again.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html>
<head><title>Book Equipment - FERS</title></head>
<body>
    <h2>Book: <?php echo htmlspecialchars($equipment['equipment_name']); ?></h2>
    <p>Type: <?php echo $equipment['equipment_type']; ?> | Price: Rs. <?php echo $equipment['rental_price_per_day']; ?>/day</p>

    <?php if ($error): ?><p style="color:red;"><?php echo $error; ?></p><?php endif; ?>
    <?php if ($success): ?><p style="color:green;"><?php echo $success; ?></p><?php endif; ?>

    <?php if (!$success): ?>
    <form method="POST" action="book_equipment.php">
        <input type="hidden" name="equipment_id" value="<?php echo $equipment_id; ?>">
        <label>Start Date:</label><br>
        <input type="date" name="start_date" required><br><br>
        <label>End Date:</label><br>
        <input type="date" name="end_date" required><br><br>
        <button type="submit">Submit Booking Request</button>
    </form>
    <?php endif; ?>

    <p><a href="search_equipment.php">Back to Search</a></p>
</body>
</html>