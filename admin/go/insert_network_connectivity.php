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

// Handle form submission to insert data into network_connectivity table
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $device_id = $_POST['device_id'];
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

    $insert_query = "
        INSERT INTO network_connectivity (sim_slots, sim_size, network_support, volte, sim_1, sim_2, sar_value, wifi, wifi_features, wifi_calling, bluetooth, gps, nfc, usb_connectivity, device_id)
        VALUES ('$sim_slots', '$sim_size', '$network_support', '$volte', '$sim_1', '$sim_2', '$sar_value', '$wifi', '$wifi_features', '$wifi_calling', '$bluetooth', '$gps', '$nfc', '$usb_connectivity', '$device_id')
    ";

    if ($conn->query($insert_query)) {
        $message = "Network connectivity data added successfully!";
    } else {
        $message = "Error adding data: " . $conn->error;
    }
}

// Pagination variables
$items_per_page = 50;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $items_per_page;

// Fetch device records for pagination
$devices_result = $conn->query("SELECT * FROM device LIMIT $offset, $items_per_page");
$total_devices_result = $conn->query("SELECT COUNT(*) AS total FROM device");
$total_devices = $total_devices_result->fetch_assoc()['total'];
$total_pages = ceil($total_devices / $items_per_page);

// Check if device is selected for network connectivity form
$device_id = isset($_GET['device_id']) ? $_GET['device_id'] : 0;
$device_result = $conn->query("SELECT * FROM device WHERE device_id = $device_id");
$device = $device_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Device Selection and Network Connectivity</title>
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

    <?php if ($device_id == 0): ?>
        <!-- Device Selection Page -->
        <h1>Select Device</h1>
        <form action="" method="get">
            <label for="device_id">Select Device:</label>
            <select name="device_id" id="device_id" required>
                <option value="">-- Select a Device --</option>
                <?php while ($device = $devices_result->fetch_assoc()): ?>
                    <option value="<?= $device['device_id'] ?>"><?= $device['model_name'] ?></option>
                <?php endwhile; ?>
            </select>
            <button type="submit">Next</button>
        </form>

        <!-- Pagination links -->
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="?page=<?= $page - 1 ?>">Previous</a>
            <?php endif; ?>
            <?php if ($page < $total_pages): ?>
                <a href="?page=<?= $page + 1 ?>">Next</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <!-- Network Connectivity Form -->
        <h1>Enter Network Connectivity Features for <?= $device['model_name'] ?></h1>

        <?php if (isset($message)): ?>
            <p class="<?= strpos($message, 'success') !== false ? 'message' : 'error' ?>"><?= $message ?></p>
        <?php endif; ?>

        <form method="post">
            <input type="hidden" name="device_id" value="<?= $device_id ?>">

            <label for="sim_slots">SIM Slots:</label>
            <input type="text" name="sim_slots" id="sim_slots" required>

            <label for="sim_size">SIM Size:</label>
            <input type="text" name="sim_size" id="sim_size" required>

            <label for="network_support">Network Support:</label>
            <input type="text" name="network_support" id="network_support" required>

            <label for="volte">VoLTE:</label>
            <input type="checkbox" name="volte" id="volte">

            <label for="sim_1">SIM 1:</label>
            <input type="text" name="sim_1" id="sim_1">

            <label for="sim_2">SIM 2:</label>
            <input type="text" name="sim_2" id="sim_2">

            <label for="sar_value">SAR Value:</label>
            <input type="text" name="sar_value" id="sar_value">

            <label for="wifi">WiFi:</label>
            <input type="checkbox" name="wifi" id="wifi">

            <label for="wifi_features">WiFi Features:</label>
            <textarea name="wifi_features" id="wifi_features"></textarea>

            <label for="wifi_calling">WiFi Calling:</label>
            <input type="checkbox" name="wifi_calling" id="wifi_calling">

            <label for="bluetooth">Bluetooth:</label>
            <input type="text" name="bluetooth" id="bluetooth">

            <label for="gps">GPS:</label>
            <input type="checkbox" name="gps" id="gps">

            <label for="nfc">NFC:</label>
            <input type="checkbox" name="nfc" id="nfc">

            <label for="usb_connectivity">USB Connectivity:</label>
            <input type="text" name="usb_connectivity" id="usb_connectivity">

            <button type="submit">Submit</button>
            <p>
            <button onclick="window.location.href='insert_data.php'">Admin Home</button></p>
        </form>
    <?php endif; ?>

</body>
</html>

<?php $conn->close(); ?>
