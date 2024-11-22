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

// Fetch all network connectivity records joined with the device table
$network_connectivity_records = $conn->query("
    SELECT n.network_connectivity_id, n.sim_slots, n.sim_size, n.network_support, n.volte, n.sim_1, n.sim_2, n.sar_value, n.wifi, 
           n.wifi_features, n.wifi_calling, n.bluetooth, n.gps, n.nfc, n.usb_connectivity, n.device_id, d.model_name
    FROM network_connectivity n 
    JOIN device d ON n.device_id = d.device_id
");

// Handle the update operation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $network_connectivity_id = $_POST['network_connectivity_id'];
    $sim_slots = $conn->real_escape_string($_POST['sim_slots']);
    $sim_size = $conn->real_escape_string($_POST['sim_size']);
    $network_support = $conn->real_escape_string($_POST['network_support']);
    $volte = isset($_POST['volte']) ? 1 : 0;
    $sim_1 = $conn->real_escape_string($_POST['sim_1']);
    $sim_2 = $conn->real_escape_string($_POST['sim_2']);
    $sar_value = $conn->real_escape_string($_POST['sar_value']);
    $wifi = isset($_POST['wifi']) ? 1 : 0;
    $wifi_features = $conn->real_escape_string($_POST['wifi_features']);
    $wifi_calling = isset($_POST['wifi_calling']) ? 1 : 0;
    $bluetooth = $conn->real_escape_string($_POST['bluetooth']);
    $gps = isset($_POST['gps']) ? 1 : 0;
    $nfc = isset($_POST['nfc']) ? 1 : 0;
    $usb_connectivity = $conn->real_escape_string($_POST['usb_connectivity']);

    $update_query = "
        UPDATE network_connectivity 
        SET 
            sim_slots = '$sim_slots', 
            sim_size = '$sim_size',
            network_support = '$network_support', 
            volte = $volte, 
            sim_1 = '$sim_1',
            sim_2 = '$sim_2',
            sar_value = '$sar_value',
            wifi = $wifi, 
            wifi_features = '$wifi_features',
            wifi_calling = $wifi_calling,
            bluetooth = '$bluetooth', 
            gps = $gps,
            nfc = $nfc,
            usb_connectivity = '$usb_connectivity'
        WHERE network_connectivity_id = $network_connectivity_id
    ";

    if ($conn->query($update_query)) {
        $message = "Network connectivity data updated successfully!";
    } else {
        $message = "Error updating data: " . $conn->error;
    }
}

// Fetch details of the selected network connectivity record for pre-filling the form
$selected_network_connectivity = null;
if (isset($_GET['network_connectivity_id'])) {
    $network_connectivity_id = $_GET['network_connectivity_id'];
    $selected_network_connectivity_result = $conn->query("
        SELECT n.*, d.model_name 
        FROM network_connectivity n 
        JOIN device d ON n.device_id = d.device_id 
        WHERE n.network_connectivity_id = $network_connectivity_id
    ");
    $selected_network_connectivity = $selected_network_connectivity_result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Network Connectivity Data</title>
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
    <h1>Update Network Connectivity Data</h1>

    <?php if (isset($message)): ?>
        <p class="<?= strpos($message, 'success') !== false ? 'message' : 'error' ?>">
            <?= $message ?>
        </p>
    <?php endif; ?>

    <!-- Form to select a network connectivity record -->
    <form method="get">
        <label for="network_connectivity_id">Select a Record to Update:</label>
        <select name="network_connectivity_id" id="network_connectivity_id" required onchange="this.form.submit()">
            <option value="">-- Select a Record --</option>
            <?php while ($row = $network_connectivity_records->fetch_assoc()): ?>
                <option value="<?= $row['network_connectivity_id'] ?>" <?= isset($network_connectivity_id) && $network_connectivity_id == $row['network_connectivity_id'] ? 'selected' : '' ?>>
                    <?= "Network Connectivity ID: " . $row['network_connectivity_id'] . " (Model: " . $row['model_name'] . ")" ?>
                </option>
            <?php endwhile; ?>
        </select>
    </form>

    <?php if ($selected_network_connectivity): ?>
        <!-- Form to update the selected network connectivity record -->
        <form method="post">
            <input type="hidden" name="network_connectivity_id" value="<?= $selected_network_connectivity['network_connectivity_id'] ?>">

            <label for="sim_slots">SIM Slots:</label>
            <input type="text" name="sim_slots" id="sim_slots" value="<?= $selected_network_connectivity['sim_slots'] ?>" required>

            <label for="sim_size">SIM Size:</label>
            <input type="text" name="sim_size" id="sim_size" value="<?= $selected_network_connectivity['sim_size'] ?>" required>

            <label for="network_support">Network Support:</label>
            <input type="text" name="network_support" id="network_support" value="<?= $selected_network_connectivity['network_support'] ?>" required>

            <label for="volte">
                <input type="checkbox" name="volte" id="volte" <?= $selected_network_connectivity['volte'] ? 'checked' : '' ?>>
                VoLTE
            </label>

            <label for="sim_1">SIM 1:</label>
            <textarea name="sim_1" id="sim_1"><?= $selected_network_connectivity['sim_1'] ?></textarea>

            <label for="sim_2">SIM 2:</label>
            <textarea name="sim_2" id="sim_2"><?= $selected_network_connectivity['sim_2'] ?></textarea>

            <label for="sar_value">SAR Value:</label>
            <input type="text" name="sar_value" id="sar_value" value="<?= $selected_network_connectivity['sar_value'] ?>">

            <label for="wifi">
                <input type="checkbox" name="wifi" id="wifi" <?= $selected_network_connectivity['wifi'] ? 'checked' : '' ?>>
                WiFi
            </label>

            <label for="wifi_features">WiFi Features:</label>
            <textarea name="wifi_features" id="wifi_features"><?= $selected_network_connectivity['wifi_features'] ?></textarea>

            <label for="wifi_calling">
                <input type="checkbox" name="wifi_calling" id="wifi_calling" <?= $selected_network_connectivity['wifi_calling'] ? 'checked' : '' ?>>
                WiFi Calling
            </label>

            <label for="bluetooth">Bluetooth:</label>
            <input type="text" name="bluetooth" id="bluetooth" value="<?= $selected_network_connectivity['bluetooth'] ?>">

            <label for="gps">
                <input type="checkbox" name="gps" id="gps" <?= $selected_network_connectivity['gps'] ? 'checked' : '' ?>>
                GPS
            </label>

            <label for="nfc">
                <input type="checkbox" name="nfc" id="nfc" <?= $selected_network_connectivity['nfc'] ? 'checked' : '' ?>>
                NFC
            </label>

            <label for="usb_connectivity">USB Connectivity:</label>
            <input type="text" name="usb_connectivity" id="usb_connectivity" value="<?= $selected_network_connectivity['usb_connectivity'] ?>">

            <p>Associated Device Model: <strong><?= $selected_network_connectivity['model_name'] ?></strong></p>

            <button type="submit">Update Network Connectivity Data</button>
        </form>
        <p>
    <button onclick="window.location.href='insert_data.php'">Admin Home</button></p>
    <?php endif; ?>
</body>
</html>
