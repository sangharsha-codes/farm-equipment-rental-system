<?php
session_start();
include '../config/db_connect.php';

// Protect this page - only logged-in Equipment Owners can access it
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "Equipment Owner") {
    header("Location: ../login.php");
    exit();
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $owner_id = $_SESSION['user_id'];
    $equipment_name = $_POST['equipment_name'];
    $equipment_type = $_POST['equipment_type'];
    $district = $_POST['district'];
    $exact_location = $_POST['exact_location'];
    $rental_price_per_day = $_POST['rental_price_per_day'];
    $description = $_POST['description'];

    // Insert new equipment listing using a prepared statement
    $stmt = $conn->prepare("INSERT INTO equipment (owner_id, equipment_name, equipment_type, district, exact_location, rental_price_per_day, description) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("issssds", $owner_id, $equipment_name, $equipment_type, $district, $exact_location, $rental_price_per_day, $description);

    if ($stmt->execute()) {
        $success = "Equipment listed successfully!";
    } else {
        $error = "Something went wrong. Please try again.";
    }
    $stmt->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Equipment - FERS</title>
</head>
<body>
    <h2>Add New Equipment</h2>

    <?php if ($error): ?>
        <p style="color:red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p style="color:green;"><?php echo $success; ?></p>
    <?php endif; ?>

    <form method="POST" action="add_equipment.php">
        <label>Equipment Name:</label><br>
        <input type="text" name="equipment_name" required><br><br>

        <label>Equipment Type:</label><br>
        <select name="equipment_type" required>
            <option value="">-- Select Type --</option>
            <option value="Tractor">Tractor</option>
            <option value="Thresher">Thresher</option>
            <option value="Irrigation Pump">Irrigation Pump</option>
            <option value="Plow">Plow</option>
        </select><br><br>

        <label>District:</label><br>
        <select name="district" required>
            <option value="">-- Select District --</option>
            <option value="Kathmandu">Kathmandu</option>
            <option value="Lalitpur">Lalitpur</option>
            <option value="Bhaktapur">Bhaktapur</option>
            <option value="Nuwakot">Nuwakot</option>
            <option value="Dhading">Dhading</option>
            <option value="Kavrepalanchok">Kavrepalanchok</option>
            <option value="Sindhupalchok">Sindhupalchok</option>
        </select><br><br>

        <label>Exact Location:</label><br>
        <input type="text" name="exact_location" required><br><br>

        <label>Rental Price per Day (Rs.):</label><br>
        <input type="number" step="0.01" name="rental_price_per_day" required><br><br>

        <label>Description (optional):</label><br>
        <textarea name="description" rows="3"></textarea><br><br>

        <button type="submit">Add Equipment</button>
    </form>

    <p><a href="dashboard.php">Back to Dashboard</a></p>
</body>
</html>