<?php
session_start();
include '../config/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "Equipment Owner") {
    header("Location: ../login.php");
    exit();
}

$owner_id = $_SESSION['user_id'];
$message = "";

// Handle DELETE request
if (isset($_GET['delete'])) {
    $equipment_id = $_GET['delete'];
    $stmt = $conn->prepare("DELETE FROM equipment WHERE equipment_id = ? AND owner_id = ?");
    $stmt->bind_param("ii", $equipment_id, $owner_id);
    if ($stmt->execute()) {
        $message = "Equipment deleted successfully.";
    }
    $stmt->close();
}

// Handle UPDATE request
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_id'])) {
    $equipment_id = $_POST['update_id'];
    $equipment_name = $_POST['equipment_name'];
    $equipment_type = $_POST['equipment_type'];
    $district = $_POST['district'];
    $exact_location = $_POST['exact_location'];
    $rental_price_per_day = $_POST['rental_price_per_day'];
    $availability_status = $_POST['availability_status'];
    $description = $_POST['description'];

    $stmt = $conn->prepare("UPDATE equipment SET equipment_name=?, equipment_type=?, district=?, exact_location=?, rental_price_per_day=?, availability_status=?, description=? WHERE equipment_id=? AND owner_id=?");
    $stmt->bind_param("ssssdssii", $equipment_name, $equipment_type, $district, $exact_location, $rental_price_per_day, $availability_status, $description, $equipment_id, $owner_id);

    if ($stmt->execute()) {
        $message = "Equipment updated successfully.";
    }
    $stmt->close();
}

// Fetch all equipment belonging to this owner
$stmt = $conn->prepare("SELECT * FROM equipment WHERE owner_id = ?");
$stmt->bind_param("i", $owner_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Manage Equipment - FERS</title>
</head>
<body>
    <h2>Manage My Equipment</h2>

    <?php if ($message): ?>
        <p style="color:green;"><?php echo $message; ?></p>
    <?php endif; ?>

    <table border="1" cellpadding="8">
        <tr>
            <th>Name</th>
            <th>Type</th>
            <th>District</th>
            <th>Location</th>
            <th>Price/Day</th>
            <th>Status</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <form method="POST" action="manage_equipment.php">
                <input type="hidden" name="update_id" value="<?php echo $row['equipment_id']; ?>">
                <td><input type="text" name="equipment_name" value="<?php echo htmlspecialchars($row['equipment_name']); ?>"></td>
                <td>
                    <select name="equipment_type">
                        <?php foreach (['Tractor','Thresher','Irrigation Pump','Plow'] as $type): ?>
                            <option value="<?php echo $type; ?>" <?php if ($row['equipment_type'] == $type) echo 'selected'; ?>><?php echo $type; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td>
                    <select name="district">
                        <?php foreach (['Kathmandu','Lalitpur','Bhaktapur','Nuwakot','Dhading','Kavrepalanchok','Sindhupalchok'] as $dist): ?>
                            <option value="<?php echo $dist; ?>" <?php if ($row['district'] == $dist) echo 'selected'; ?>><?php echo $dist; ?></option>
                        <?php endforeach; ?>
                    </select>
                </td>
                <td><input type="text" name="exact_location" value="<?php echo htmlspecialchars($row['exact_location']); ?>"></td>
                <td><input type="number" step="0.01" name="rental_price_per_day" value="<?php echo $row['rental_price_per_day']; ?>"></td>
                <td>
                    <select name="availability_status">
                        <option value="available" <?php if ($row['availability_status'] == 'available') echo 'selected'; ?>>Available</option>
                        <option value="unavailable" <?php if ($row['availability_status'] == 'unavailable') echo 'selected'; ?>>Unavailable</option>
                    </select>
                </td>
                <td>
                    <input type="hidden" name="description" value="<?php echo htmlspecialchars($row['description']); ?>">
                    <button type="submit">Update</button>
                </td>
            </form>
            <td>
                <a href="manage_equipment.php?delete=<?php echo $row['equipment_id']; ?>" onclick="return confirm('Are you sure you want to delete this?');">Delete</a>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>

    <p><a href="dashboard.php">Back to Dashboard</a></p>
</body>
</html>