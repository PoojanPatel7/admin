<?php
// Database connection
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'go'; // Replace with your actual database name
$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all sensors records joined with the device table
$sensor_records = $conn->query("
    SELECT s.sensor_id, s.fingerprint_sensor, s.fingerprint_sensor_position, s.fingerprint_sensor_type, s.other_sensors, s.device_id, d.model_name
    FROM sensors s
    JOIN device d ON s.device_id = d.device_id
");

// Handle the update operation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sensor_id = $_POST['sensor_id'];
    $fingerprint_sensor = isset($_POST['fingerprint_sensor']) ? 1 : 0;
    $fingerprint_sensor_position = $conn->real_escape_string($_POST['fingerprint_sensor_position']);
    $fingerprint_sensor_type = $conn->real_escape_string($_POST['fingerprint_sensor_type']);
    $other_sensors = $conn->real_escape_string($_POST['other_sensors']);

    $update_query = "
        UPDATE sensors 
        SET 
            fingerprint_sensor = '$fingerprint_sensor', 
            fingerprint_sensor_position = '$fingerprint_sensor_position', 
            fingerprint_sensor_type = '$fingerprint_sensor_type', 
            other_sensors = '$other_sensors'
        WHERE sensor_id = $sensor_id
    ";

    if ($conn->query($update_query)) {
        $message = "Sensor data updated successfully!";
    } else {
        $message = "Error updating data: " . $conn->error;
    }
}

// Fetch details of the selected sensor record for pre-filling the form
$selected_sensor = null;
if (isset($_GET['sensor_id'])) {
    $sensor_id = $_GET['sensor_id'];
    $selected_sensor_result = $conn->query("
        SELECT s.*, d.model_name 
        FROM sensors s 
        JOIN device d ON s.device_id = d.device_id 
        WHERE s.sensor_id = $sensor_id
    ");
    $selected_sensor = $selected_sensor_result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Sensor Data</title>
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
    <h1>Update Sensor Data</h1>

    <?php if (isset($message)): ?>
        <p class="<?= strpos($message, 'success') !== false ? 'message' : 'error' ?>">
            <?= $message ?>
        </p>
    <?php endif; ?>

    <!-- Form to select a sensor record -->
    <form method="get">
        <label for="sensor_id">Select a Record to Update:</label>
        <select name="sensor_id" id="sensor_id" required onchange="this.form.submit()">
            <option value="">-- Select a Record --</option>
            <?php while ($row = $sensor_records->fetch_assoc()): ?>
                <option value="<?= $row['sensor_id'] ?>" <?= isset($sensor_id) && $sensor_id == $row['sensor_id'] ? 'selected' : '' ?>>
                    <?= "Sensor ID: " . $row['sensor_id'] . " (Model: " . $row['model_name'] . ")" ?>
                </option>
            <?php endwhile; ?>
        </select>
    </form>

    <?php if ($selected_sensor): ?>
        <!-- Form to update the selected sensor record -->
        <form method="post">
            <input type="hidden" name="sensor_id" value="<?= $selected_sensor['sensor_id'] ?>">

            <label for="fingerprint_sensor">Fingerprint Sensor:</label>
            <input type="checkbox" name="fingerprint_sensor" id="fingerprint_sensor" value="1" <?= $selected_sensor['fingerprint_sensor'] == 1 ? 'checked' : '' ?>>

            <label for="fingerprint_sensor_position">Fingerprint Sensor Position:</label>
            <input type="text" name="fingerprint_sensor_position" id="fingerprint_sensor_position" value="<?= $selected_sensor['fingerprint_sensor_position'] ?>">

            <label for="fingerprint_sensor_type">Fingerprint Sensor Type:</label>
            <input type="text" name="fingerprint_sensor_type" id="fingerprint_sensor_type" value="<?= $selected_sensor['fingerprint_sensor_type'] ?>">

            <label for="other_sensors">Other Sensors:</label>
            <textarea name="other_sensors" id="other_sensors" rows="4"><?= $selected_sensor['other_sensors'] ?></textarea>

            <p>Associated Device Model: <strong><?= $selected_sensor['model_name'] ?></strong></p>

            <button type="submit">Update Sensor Data</button>
        </form>
        <p>
    <button onclick="window.location.href='insert_data.php'">Admin Home</button></p>
    <?php endif; ?>
</body>
</html>
