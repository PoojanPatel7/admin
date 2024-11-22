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

// Fetch all storage records joined with the device table
$storage_records = $conn->query("
    SELECT s.storage_id, s.internal_memory, s.expandable_memory, s.user_available_storage, s.storage_type, s.usb_otg, s.device_id, d.model_name
    FROM storage s
    JOIN device d ON s.device_id = d.device_id
");

// Handle the update operation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $storage_id = $_POST['storage_id'];
    $internal_memory = $conn->real_escape_string($_POST['internal_memory']);
    $expandable_memory = $conn->real_escape_string($_POST['expandable_memory']);
    $user_available_storage = $conn->real_escape_string($_POST['user_available_storage']);
    $storage_type = $conn->real_escape_string($_POST['storage_type']);
    $usb_otg = isset($_POST['usb_otg']) ? 1 : 0;

    $update_query = "
        UPDATE storage 
        SET 
            internal_memory = '$internal_memory',
            expandable_memory = '$expandable_memory',
            user_available_storage = '$user_available_storage',
            storage_type = '$storage_type',
            usb_otg = '$usb_otg'
        WHERE storage_id = $storage_id
    ";

    if ($conn->query($update_query)) {
        $message = "Storage data updated successfully!";
    } else {
        $message = "Error updating data: " . $conn->error;
    }
}

// Fetch details of the selected storage record for pre-filling the form
$selected_storage = null;
if (isset($_GET['storage_id'])) {
    $storage_id = $_GET['storage_id'];
    $selected_storage_result = $conn->query("
        SELECT s.*, d.model_name 
        FROM storage s 
        JOIN device d ON s.device_id = d.device_id 
        WHERE s.storage_id = $storage_id
    ");
    $selected_storage = $selected_storage_result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Storage Data</title>
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
    <h1>Update Storage Data</h1>

    <?php if (isset($message)): ?>
        <p class="<?= strpos($message, 'success') !== false ? 'message' : 'error' ?>">
            <?= $message ?>
        </p>
    <?php endif; ?>

    <!-- Form to select a storage record -->
    <form method="get">
        <label for="storage_id">Select a Record to Update:</label>
        <select name="storage_id" id="storage_id" required onchange="this.form.submit()">
            <option value="">-- Select a Record --</option>
            <?php while ($row = $storage_records->fetch_assoc()): ?>
                <option value="<?= $row['storage_id'] ?>" <?= isset($storage_id) && $storage_id == $row['storage_id'] ? 'selected' : '' ?>>
                    <?= "Storage ID: " . $row['storage_id'] . " (Model: " . $row['model_name'] . ")" ?>
                </option>
            <?php endwhile; ?>
        </select>
    </form>

    <?php if ($selected_storage): ?>
        <!-- Form to update the selected storage record -->
        <form method="post">
            <input type="hidden" name="storage_id" value="<?= $selected_storage['storage_id'] ?>">

            <label for="internal_memory">Internal Memory:</label>
            <input type="text" name="internal_memory" id="internal_memory" value="<?= $selected_storage['internal_memory'] ?>" required>

            <label for="expandable_memory">Expandable Memory:</label>
            <input type="text" name="expandable_memory" id="expandable_memory" value="<?= $selected_storage['expandable_memory'] ?>">

            <label for="user_available_storage">User Available Storage:</label>
            <input type="text" name="user_available_storage" id="user_available_storage" value="<?= $selected_storage['user_available_storage'] ?>">

            <label for="storage_type">Storage Type:</label>
            <input type="text" name="storage_type" id="storage_type" value="<?= $selected_storage['storage_type'] ?>">

            <label for="usb_otg">USB OTG:</label>
            <input type="checkbox" name="usb_otg" id="usb_otg" value="1" <?= $selected_storage['usb_otg'] == 1 ? 'checked' : '' ?>>

            <p>Associated Device Model: <strong><?= $selected_storage['model_name'] ?></strong></p>

            <button type="submit">Update Storage Data</button>
        </form>
        <p>
    <button onclick="window.location.href='insert_data.php'">Admin Home</button></p>
    <?php endif; ?>
</body>
</html>
