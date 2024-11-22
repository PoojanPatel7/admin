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

// Handle form submission to insert data into storage table
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $device_id = $_POST['device_id'];
    $internal_memory = $conn->real_escape_string($_POST['internal_memory']);
    $expandable_memory = $conn->real_escape_string($_POST['expandable_memory']);
    $user_available_storage = $conn->real_escape_string($_POST['user_available_storage']);
    $storage_type = $conn->real_escape_string($_POST['storage_type']);
    $usb_otg = isset($_POST['usb_otg']) ? 1 : 0;

    $insert_query = "
        INSERT INTO storage (internal_memory, expandable_memory, user_available_storage, storage_type, usb_otg, device_id)
        VALUES ('$internal_memory', '$expandable_memory', '$user_available_storage', '$storage_type', '$usb_otg', '$device_id')
    ";

    if ($conn->query($insert_query)) {
        $message = "Storage data added successfully!";
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

// Check if device is selected for storage form
$device_id = isset($_GET['device_id']) ? $_GET['device_id'] : 0;
$device_result = $conn->query("SELECT * FROM device WHERE device_id = $device_id");
$device = $device_result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Device Selection and Storage Data</title>
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
        <!-- Storage Data Form -->
        <h1>Enter Storage Data for <?= $device['model_name'] ?></h1>

        <?php if (isset($message)): ?>
            <p class="<?= strpos($message, 'success') !== false ? 'message' : 'error' ?>"><?= $message ?></p>
        <?php endif; ?>

        <form method="post">
            <input type="hidden" name="device_id" value="<?= $device_id ?>">

            <label for="internal_memory">Internal Memory:</label>
            <input type="text" name="internal_memory" id="internal_memory" required>

            <label for="expandable_memory">Expandable Memory:</label>
            <input type="text" name="expandable_memory" id="expandable_memory">

            <label for="user_available_storage">User Available Storage:</label>
            <input type="text" name="user_available_storage" id="user_available_storage">

            <label for="storage_type">Storage Type:</label>
            <input type="text" name="storage_type" id="storage_type">

            <label for="usb_otg">USB OTG Supported:</label>
            <input type="checkbox" name="usb_otg" id="usb_otg" value="1">

            <button type="submit">Submit</button>
            <p>
            <button onclick="window.location.href='insert_data.php'">Admin Home</button></p>
        </form>
    <?php endif; ?>

</body>
</html>

<?php $conn->close(); ?>
