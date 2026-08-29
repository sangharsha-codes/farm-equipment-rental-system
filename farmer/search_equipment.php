<?php
session_start();
include '../config/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "Farmer") {
    header("Location: ../login.php");
    exit();
}

$where = "WHERE availability_status = 'available'";
$params = [];
$types = "";

if (!empty($_GET['type'])) {
    $where .= " AND equipment_type = ?";
    $params[] = $_GET['type'];
    $types .= "s";
}
if (!empty($_GET['district'])) {
    $where .= " AND district = ?";
    $params[] = $_GET['district'];
    $types .= "s";
}
if (!empty($_GET['max_price'])) {
    $where .= " AND rental_price_per_day <= ?";
    $params[] = $_GET['max_price'];
    $types .= "d";
}

$sql = "SELECT * FROM equipment $where";
$stmt = $conn->prepare($sql);
if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head><title>Search Equipment - FERS</title></head>
<body>
    <h2>Search Equipment</h2>

    <form method="GET" action="search_equipment.php">
        <select name="type">
            <option value="">-- Any Type --</option>
            <option value="Tractor">Tractor</option>
            <option value="Thresher">Thresher</option>
            <option value="Irrigation Pump">Irrigation Pump</option>
            <option value="Plow">Plow</option>
        </select>

        <select name="district">
            <option value="">-- Any District --</option>
            <option value="Kathmandu">Kathmandu</option>
            <option value="Lalitpur">Lalitpur</option>
            <option value="Bhaktapur">Bhaktapur</option>
            <option value="Nuwakot">Nuwakot</option>
            <option value="Dhading">Dhading</option>
            <option value="Kavrepalanchok">Kavrepalanchok</option>
            <option value="Sindhupalchok">Sindhupalchok</option>
        </select>

        <input type="number" step="0.01" name="max_price" placeholder="Max price/day">
        <button type="submit">Search</button>
    </form>

    <table border="1" cellpadding="8">
        <tr>
            <th>Name</th><th>Type</th><th>District</th><th>Location</th>
            <th>Price/Day</th><th>Description</th><th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['equipment_name']); ?></td>
            <td><?php echo $row['equipment_type']; ?></td>
            <td><?php echo $row['district']; ?></td>
            <td><?php echo htmlspecialchars($row['exact_location']); ?></td>
            <td>Rs. <?php echo $row['rental_price_per_day']; ?></td>
            <td><?php echo htmlspecialchars($row['description']); ?></td>
            <td><a href="book_equipment.php?equipment_id=<?php echo $row['equipment_id']; ?>">Book</a></td>
        </tr>
        <?php endwhile; ?>
    </table>

    <p><a href="dashboard.php">Back to Dashboard</a></p>
</body>
</html>