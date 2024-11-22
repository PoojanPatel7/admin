<?php
// Database connection
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'go'; // Replace with your database name
$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch existing device data for the dropdown
$devices = $conn->query("SELECT * FROM device");

// Handle update form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_device'])) {
    $device_id = $_POST['device_id'];
    $brand = $conn->real_escape_string($_POST['brand']);
    $model_name = $conn->real_escape_string($_POST['model_name']);

    $update_query = "UPDATE device SET brand = '$brand', model_name = '$model_name' WHERE device_id = $device_id";
    if ($conn->query($update_query)) {
        echo "<p style='color: green;'>Device updated successfully!</p>";
    } else {
        echo "<p style='color: red;'>Error updating device: " . $conn->error . "</p>";
    }
}

// Fetch the selected device's details for pre-filling the form
$selected_device = null;
if (isset($_GET['device_id'])) {
    $device_id = $_GET['device_id'];
    $selected_device_result = $conn->query("SELECT * FROM device WHERE device_id = $device_id");
    $selected_device = $selected_device_result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Device</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(to right, #141e30, #243b55);
            color: #fff;
            line-height: 1.6;
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
            color: #f3a683;
        }
        form {
            max-width: 600px;
            margin: 20px auto;
            background: rgba(255, 255, 255, 0.1);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        label {
            display: block;
            margin-bottom: 10px;
            font-weight: bold;
            color: #f8a5c2;
        }
        select, input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        input[type="checkbox"] {
            margin-right: 10px;
        }
        button {
            display: block;
            width: 100%;
            background: #f3a683;
            color: #fff;
            border: none;
            padding: 10px;
            cursor: pointer;
            font-size: 16px;
            border-radius: 5px;
            transition: background 0.3s;
        }
        button:hover {
            background: #e66767;
        }
        .pagination {
            text-align: center;
            margin-top: 20px;
        }
        .pagination a {
            margin: 0 5px;
            text-decoration: none;
            padding: 10px 15px;
            background: #f3a683;
            color: #fff;
            border-radius: 5px;
        }
        .pagination a:hover {
            background: #e66767;
        }
        .message, .error {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin-top: 20px;
        }
        .message { color: #2ecc71; }
        .error { color: #e74c3c; }
    </style>
</head>
<body>
    <h1>Update Device</h1>

    <!-- Form to select a device to update -->
    <form method="get">
        <label for="device_id">Select Device to Update:</label>
        <select name="device_id" id="device_id" required onchange="this.form.submit()">
            <option value="">-- Select a device --</option>
            <?php while ($row = $devices->fetch_assoc()): ?>
                <option value="<?= $row['device_id'] ?>" <?= isset($device_id) && $device_id == $row['device_id'] ? 'selected' : '' ?>>
                    <?= $row['brand'] . " " . $row['model_name'] ?>
                </option>
            <?php endwhile; ?>
        </select>
    </form>

    <?php if ($selected_device): ?>
        <!-- Form to update device details -->
        <form method="post">
            <input type="hidden" name="device_id" value="<?= $selected_device['device_id'] ?>">
            <label for="brand">Brand:</label>
            <input type="text" name="brand" id="brand" value="<?= $selected_device['brand'] ?>" required>

            <label for="model_name">Model Name:</label>
            <input type="text" name="model_name" id="model_name" value="<?= $selected_device['model_name'] ?>" required>

            <button type="submit" name="update_device">Update Device</button>
    
        </form>
        <p>
    <button onclick="window.location.href='insert_data.php'">Admin Home</button></p>
    <?php endif; ?>
</body>
</html>
