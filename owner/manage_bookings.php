<?php
session_start();
include '../config/db_connect.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != "Equipment Owner") {
    header("Location: ../login.php");
    exit();
}

$owner_id = $_SESSION['user_id'];
$message = "";

if (isset($_GET['action']) && isset($_GET['booking_id'])) {
    $booking_id = $_GET['booking_id'];
    $new_status = "";
    if ($_GET['action'] == "approve") $new_status = "Approved";
    elseif ($_GET['action'] == "reject") $new_status = "Cancelled";
    elseif ($_GET['action'] == "activate") $new_status = "Active";
    elseif ($_GET['action'] == "complete") $new_status = "Completed";

    if ($new_status) {
        $stmt = $conn->prepare("UPDATE bookings b 
                                 JOIN equipment e ON b.equipment_id = e.equipment_id 
                                 SET b.status = ? 
                                 WHERE b.booking_id = ? AND e.owner_id = ?");
        $stmt->bind_param("sii", $new_status, $booking_id, $owner_id);
        $stmt->execute();
        $message = "Booking status updated to $new_status.";
    }
}

$stmt = $conn->prepare("SELECT b.*, e.equipment_name, u.full_name AS farmer_name 
                         FROM bookings b 
                         JOIN equipment e ON b.equipment_id = e.equipment_id 
                         JOIN users u ON b.farmer_id = u.user_id 
                         WHERE e.owner_id = ? ORDER BY b.requested_at DESC");
$stmt->bind_param("i", $owner_id);
$stmt->execute();
$result = $stmt->get_result();
?>
<!DOCTYPE html>
<html>
<head><title>Manage Bookings - FERS</title></head>
<body>
    <h2>Manage Booking Requests</h2>
    <?php if ($message): ?><p style="color:green;"><?php echo $message; ?></p><?php endif; ?>

    <table border="1" cellpadding="8">
        <tr>
            <th>Farmer</th><th>Equipment</th><th>Start</th><th>End</th><th>Status</th><th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['farmer_name']); ?></td>
            <td><?php echo htmlspecialchars($row['equipment_name']); ?></td>
            <td><?php echo $row['start_date']; ?></td>
            <td><?php echo $row['end_date']; ?></td>
            <td><b><?php echo $row['status']; ?></b></td>
            <td>
                <?php if ($row['status'] == 'Pending'): ?>
                    <a href="?action=approve&booking_id=<?php echo $row['booking_id']; ?>">Approve</a> |
                    <a href="?action=reject&booking_id=<?php echo $row['booking_id']; ?>">Reject</a>
                <?php elseif ($row['status'] == 'Approved'): ?>
                    <a href="?action=activate&booking_id=<?php echo $row['booking_id']; ?>">Mark Active</a>
                <?php elseif ($row['status'] == 'Active'): ?>
                    <a href="?action=complete&booking_id=<?php echo $row['booking_id']; ?>">Mark Completed</a>
                <?php else: ?>
                    —
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
    <p><a href="dashboard.php">Back to Dashboard</a></p>
</body>
</html>