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

// Fetch all battery records joined with the device table
$batteries = $conn->query("
    SELECT b.battery_id, b.capacity, b.device_id, d.model_name 
    FROM battery b 
    JOIN device d ON b.device_id = d.device_id
");

// Handle the update operation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $battery_id = $_POST['battery_id'];
    $capacity = $conn->real_escape_string($_POST['capacity']);
    $type = $conn->real_escape_string($_POST['type']);
    $removable = isset($_POST['removable']) ? 1 : 0;
    $talk_time = $conn->real_escape_string($_POST['talk_time']);
    $wireless_charging = isset($_POST['wireless_charging']) ? 1 : 0;
    $quick_charging = isset($_POST['quick_charging']) ? 1 : 0;
    $usb_type_c = isset($_POST['usb_type_c']) ? 1 : 0;

    $update_query = "
        UPDATE battery 
        SET 
            capacity = '$capacity', 
            type = '$type', 
            removable = $removable, 
            talk_time = '$talk_time', 
            wireless_charging = $wireless_charging, 
            quick_charging = $quick_charging, 
            usb_type_c = $usb_type_c
        WHERE battery_id = $battery_id
    ";

    if ($conn->query($update_query)) {
        $message = "Battery updated successfully!";
    } else {
        $message = "Error updating battery: " . $conn->error;
    }
}

// Fetch details of the selected battery for pre-filling the form
$selected_battery = null;
if (isset($_GET['battery_id'])) {
    $battery_id = $_GET['battery_id'];
    $selected_battery_result = $conn->query("
        SELECT b.*, d.model_name 
        FROM battery b 
        JOIN device d ON b.device_id = d.device_id 
        WHERE b.battery_id = $battery_id
    ");
    $selected_battery = $selected_battery_result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Battery</title>
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
    <h1>Update Battery</h1>

    <?php if (isset($message)): ?>
        <p class="<?= strpos($message, 'success') !== false ? 'message' : 'error' ?>">
            <?= $message ?>
        </p>
    <?php endif; ?>

    <!-- Form to select a battery -->
    <form method="get">
        <label for="battery_id">Select a Battery to Update:</label>
        <select name="battery_id" id="battery_id" required onchange="this.form.submit()">
            <option value="">-- Select a Battery --</option>
            <?php while ($row = $batteries->fetch_assoc()): ?>
                <option value="<?= $row['battery_id'] ?>" <?= isset($battery_id) && $battery_id == $row['battery_id'] ? 'selected' : '' ?>>
                    <?= "Battery ID: " . $row['battery_id'] . " (Model: " . $row['model_name'] . ")" ?>
                </option>
            <?php endwhile; ?>
        </select>
    </form>

    <?php if ($selected_battery): ?>
        <!-- Form to update the selected battery -->
        <form method="post">
            <input type="hidden" name="battery_id" value="<?= $selected_battery['battery_id'] ?>">

            <label for="capacity">Capacity:</label>
            <input type="text" name="capacity" id="capacity" value="<?= $selected_battery['capacity'] ?>" required>

            <label for="type">Type:</label>
            <input type="text" name="type" id="type" value="<?= $selected_battery['type'] ?>" required>

            <label for="removable">
                <input type="checkbox" name="removable" id="removable" <?= $selected_battery['removable'] ? 'checked' : '' ?>>
                Removable
            </label>

            <label for="talk_time">Talk Time:</label>
            <input type="text" name="talk_time" id="talk_time" value="<?= $selected_battery['talk_time'] ?>">

            <label for="wireless_charging">
                <input type="checkbox" name="wireless_charging" id="wireless_charging" <?= $selected_battery['wireless_charging'] ? 'checked' : '' ?>>
                Wireless Charging
            </label>

            <label for="quick_charging">
                <input type="checkbox" name="quick_charging" id="quick_charging" <?= $selected_battery['quick_charging'] ? 'checked' : '' ?>>
                Quick Charging
            </label>

            <label for="usb_type_c">
                <input type="checkbox" name="usb_type_c" id="usb_type_c" <?= $selected_battery['usb_type_c'] ? 'checked' : '' ?>>
                USB Type-C
            </label>

            <p>Associated Device Model: <strong><?= $selected_battery['model_name'] ?></strong></p>

            <button type="submit">Update Battery</button>
        </form>
        <p>
    <button onclick="window.location.href='insert_data.php'">Admin Home</button></p>
    <?php endif; ?>
</body>
</html>
