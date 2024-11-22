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

// Fetch all performance records joined with the device table
$performance_records = $conn->query("
    SELECT p.performance_id, p.chipset, p.CPU, p.architecture, p.fabrication, p.graphics, p.RAM, p.RAM_type, p.device_id, d.model_name
    FROM performance p
    JOIN device d ON p.device_id = d.device_id
");

// Handle the update operation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $performance_id = $_POST['performance_id'];
    $chipset = $conn->real_escape_string($_POST['chipset']);
    $CPU = $conn->real_escape_string($_POST['CPU']);
    $architecture = $conn->real_escape_string($_POST['architecture']);
    $fabrication = $conn->real_escape_string($_POST['fabrication']);
    $graphics = $conn->real_escape_string($_POST['graphics']);
    $RAM = $conn->real_escape_string($_POST['RAM']);
    $RAM_type = $conn->real_escape_string($_POST['RAM_type']);

    $update_query = "
        UPDATE performance 
        SET 
            chipset = '$chipset', 
            CPU = '$CPU',
            architecture = '$architecture',
            fabrication = '$fabrication', 
            graphics = '$graphics',
            RAM = '$RAM', 
            RAM_type = '$RAM_type'
        WHERE performance_id = $performance_id
    ";

    if ($conn->query($update_query)) {
        $message = "Performance data updated successfully!";
    } else {
        $message = "Error updating data: " . $conn->error;
    }
}

// Fetch details of the selected performance record for pre-filling the form
$selected_performance = null;
if (isset($_GET['performance_id'])) {
    $performance_id = $_GET['performance_id'];
    $selected_performance_result = $conn->query("
        SELECT p.*, d.model_name 
        FROM performance p 
        JOIN device d ON p.device_id = d.device_id 
        WHERE p.performance_id = $performance_id
    ");
    $selected_performance = $selected_performance_result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Performance Data</title>
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
    <h1>Update Performance Data</h1>

    <?php if (isset($message)): ?>
        <p class="<?= strpos($message, 'success') !== false ? 'message' : 'error' ?>">
            <?= $message ?>
        </p>
    <?php endif; ?>

    <!-- Form to select a performance record -->
    <form method="get">
        <label for="performance_id">Select a Record to Update:</label>
        <select name="performance_id" id="performance_id" required onchange="this.form.submit()">
            <option value="">-- Select a Record --</option>
            <?php while ($row = $performance_records->fetch_assoc()): ?>
                <option value="<?= $row['performance_id'] ?>" <?= isset($performance_id) && $performance_id == $row['performance_id'] ? 'selected' : '' ?>>
                    <?= "Performance ID: " . $row['performance_id'] . " (Model: " . $row['model_name'] . ")" ?>
                </option>
            <?php endwhile; ?>
        </select>
    </form>

    <?php if ($selected_performance): ?>
        <!-- Form to update the selected performance record -->
        <form method="post">
            <input type="hidden" name="performance_id" value="<?= $selected_performance['performance_id'] ?>">

            <label for="chipset">Chipset:</label>
            <input type="text" name="chipset" id="chipset" value="<?= $selected_performance['chipset'] ?>" required>

            <label for="CPU">CPU:</label>
            <input type="text" name="CPU" id="CPU" value="<?= $selected_performance['CPU'] ?>" required>

            <label for="architecture">Architecture:</label>
            <input type="text" name="architecture" id="architecture" value="<?= $selected_performance['architecture'] ?>" required>

            <label for="fabrication">Fabrication (nm):</label>
            <input type="text" name="fabrication" id="fabrication" value="<?= $selected_performance['fabrication'] ?>" required>

            <label for="graphics">Graphics:</label>
            <input type="text" name="graphics" id="graphics" value="<?= $selected_performance['graphics'] ?>" required>

            <label for="RAM">RAM:</label>
            <input type="text" name="RAM" id="RAM" value="<?= $selected_performance['RAM'] ?>" required>

            <label for="RAM_type">RAM Type:</label>
            <input type="text" name="RAM_type" id="RAM_type" value="<?= $selected_performance['RAM_type'] ?>" required>

            <p>Associated Device Model: <strong><?= $selected_performance['model_name'] ?></strong></p>

            <button type="submit">Update Performance Data</button>
        </form>
        <p>
    <button onclick="window.location.href='insert_data.php'">Admin Home</button></p>
    <?php endif; ?>
</body>
</html>
