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

// Fetch all general records joined with the device table
$general_records = $conn->query("
    SELECT gen.general_id, gen.launch_date, gen.operating_system, gen.custom_ui, gen.device_id, dev.model_name 
    FROM general gen 
    JOIN device dev ON gen.device_id = dev.device_id
");

// Handle the update operation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $general_id = $_POST['general_id'];
    $launch_date = $conn->real_escape_string($_POST['launch_date']);
    $operating_system = $conn->real_escape_string($_POST['operating_system']);
    $custom_ui = $conn->real_escape_string($_POST['custom_ui']);

    $update_query = "
        UPDATE general 
        SET 
            launch_date = '$launch_date', 
            operating_system = '$operating_system', 
            custom_ui = '$custom_ui'
        WHERE general_id = $general_id
    ";

    if ($conn->query($update_query)) {
        $message = "General information updated successfully!";
    } else {
        $message = "Error updating general information: " . $conn->error;
    }
}

// Fetch details of the selected general record for pre-filling the form
$selected_general = null;
if (isset($_GET['general_id'])) {
    $general_id = $_GET['general_id'];
    $selected_general_result = $conn->query("
        SELECT gen.*, dev.model_name 
        FROM general gen 
        JOIN device dev ON gen.device_id = dev.device_id 
        WHERE gen.general_id = $general_id
    ");
    $selected_general = $selected_general_result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update General Information</title>
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
    <h1>Update General Information</h1>

    <?php if (isset($message)): ?>
        <p class="<?= strpos($message, 'success') !== false ? 'message' : 'error' ?>">
            <?= $message ?>
        </p>
    <?php endif; ?>

    <!-- Form to select a general record -->
    <form method="get">
        <label for="general_id">Select a Record to Update:</label>
        <select name="general_id" id="general_id" required onchange="this.form.submit()">
            <option value="">-- Select a Record --</option>
            <?php while ($row = $general_records->fetch_assoc()): ?>
                <option value="<?= $row['general_id'] ?>" <?= isset($general_id) && $general_id == $row['general_id'] ? 'selected' : '' ?>>
                    <?= "General ID: " . $row['general_id'] . " (Model: " . $row['model_name'] . ")" ?>
                </option>
            <?php endwhile; ?>
        </select>
    </form>

    <?php if ($selected_general): ?>
        <!-- Form to update the selected general record -->
        <form method="post">
            <input type="hidden" name="general_id" value="<?= $selected_general['general_id'] ?>">

            <label for="launch_date">Launch Date:</label>
            <input type="date" name="launch_date" id="launch_date" value="<?= $selected_general['launch_date'] ?>" required>

            <label for="operating_system">Operating System:</label>
            <input type="text" name="operating_system" id="operating_system" value="<?= $selected_general['operating_system'] ?>" required>

            <label for="custom_ui">Custom UI:</label>
            <input type="text" name="custom_ui" id="custom_ui" value="<?= $selected_general['custom_ui'] ?>">

            <p>Associated Device Model: <strong><?= $selected_general['model_name'] ?></strong></p>

            <button type="submit">Update General Information</button>
        </form>
        <p>
    <button onclick="window.location.href='insert_data.php'">Admin Home</button></p>
    <?php endif; ?>
</body>
</html>
