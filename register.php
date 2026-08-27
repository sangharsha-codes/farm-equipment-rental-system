<?php
include 'config/db_connect.php';

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    $phone = $_POST['phone'];
    $district = $_POST['district'];
    $exact_location = $_POST['exact_location'];

    // Hash the password before storing it
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Check if email already exists
    $check_email = $conn->prepare("SELECT user_id FROM users WHERE email = ?");
    $check_email->bind_param("s", $email);
    $check_email->execute();
    $check_email->store_result();

    if ($check_email->num_rows > 0) {
        $error = "This email is already registered. Please log in instead.";
    } else {
        // Insert new user using a prepared statement (prevents SQL injection)
        $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, role, phone, district, exact_location) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssss", $full_name, $email, $hashed_password, $role, $phone, $district, $exact_location);

        if ($stmt->execute()) {
            $success = "Registration successful! You can now log in.";
        } else {
            $error = "Something went wrong. Please try again.";
        }
        $stmt->close();
    }
    $check_email->close();
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Register - Farm Equipment Rental System</title>
</head>
<body>
    <h2>Register</h2>

    <?php if ($error): ?>
        <p style="color:red;"><?php echo $error; ?></p>
    <?php endif; ?>

    <?php if ($success): ?>
        <p style="color:green;"><?php echo $success; ?></p>
    <?php endif; ?>

    <form method="POST" action="register.php">
        <label>Full Name:</label><br>
        <input type="text" name="full_name" required><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" required><br><br>

        <label>Password:</label><br>
        <input type="password" name="password" required><br><br>

        <label>Role:</label><br>
        <select name="role" required>
            <option value="">-- Select Role --</option>
            <option value="Farmer">Farmer</option>
            <option value="Equipment Owner">Equipment Owner</option>
        </select><br><br>

        <label>Phone:</label><br>
        <input type="text" name="phone" required><br><br>

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

        <button type="submit">Register</button>
    </form>
</body>
</html>