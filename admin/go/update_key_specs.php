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

// Fetch all key_specs records joined with the device table
$key_specs_records = $conn->query("
    SELECT ks.spec_id, ks.RAM, ks.Processor, ks.Rear_Camera, ks.Front_Camera, 
           ks.Battery, ks.Display, ks.device_id, d.model_name 
    FROM key_specs ks 
    JOIN device d ON ks.device_id = d.device_id
");

// Handle the update operation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $spec_id = $_POST['spec_id'];
    $RAM = $conn->real_escape_string($_POST['RAM']);
    $Processor = $conn->real_escape_string($_POST['Processor']);
    $Rear_Camera = $conn->real_escape_string($_POST['Rear_Camera']);
    $Front_Camera = $conn->real_escape_string($_POST['Front_Camera']);
    $Battery = $conn->real_escape_string($_POST['Battery']);
    $Display = $conn->real_escape_string($_POST['Display']);

    $update_query = "
        UPDATE key_specs 
        SET 
            RAM = '$RAM', 
            Processor = '$Processor', 
            Rear_Camera = '$Rear_Camera', 
            Front_Camera = '$Front_Camera', 
            Battery = '$Battery', 
            Display = '$Display'
        WHERE spec_id = $spec_id
    ";

    if ($conn->query($update_query)) {
        $message = "Key Specifications updated successfully!";
    } else {
        $message = "Error updating Key Specifications: " . $conn->error;
    }
}

// Fetch details of the selected key_specs record for pre-filling the form
$selected_key_specs = null;
if (isset($_GET['spec_id'])) {
    $spec_id = $_GET['spec_id'];
    $selected_key_specs_result = $conn->query("
        SELECT ks.*, d.model_name 
        FROM key_specs ks 
        JOIN device d ON ks.device_id = d.device_id 
        WHERE ks.spec_id = $spec_id
    ");
    $selected_key_specs = $selected_key_specs_result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Key Specifications</title>
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
    <h1>Update Key Specifications</h1>

    <?php if (isset($message)): ?>
        <p class="<?= strpos($message, 'success') !== false ? 'message' : 'error' ?>">
            <?= $message ?>
        </p>
    <?php endif; ?>

    <!-- Form to select a key_specs record -->
    <form method="get">
        <label for="spec_id">Select a Record to Update:</label>
        <select name="spec_id" id="spec_id" required onchange="this.form.submit()">
            <option value="">-- Select a Record --</option>
            <?php while ($row = $key_specs_records->fetch_assoc()): ?>
                <option value="<?= $row['spec_id'] ?>" <?= isset($spec_id) && $spec_id == $row['spec_id'] ? 'selected' : '' ?>>
                    <?= "Spec ID: " . $row['spec_id'] . " (Model: " . $row['model_name'] . ")" ?>
                </option>
            <?php endwhile; ?>
        </select>
    </form>

    <?php if ($selected_key_specs): ?>
        <!-- Form to update the selected key_specs record -->
        <form method="post">
            <input type="hidden" name="spec_id" value="<?= $selected_key_specs['spec_id'] ?>">

            <label for="RAM">RAM:</label>
            <input type="text" name="RAM" id="RAM" value="<?= $selected_key_specs['RAM'] ?>" required>

            <label for="Processor">Processor:</label>
            <input type="text" name="Processor" id="Processor" value="<?= $selected_key_specs['Processor'] ?>" required>

            <label for="Rear_Camera">Rear Camera:</label>
            <input type="text" name="Rear_Camera" id="Rear_Camera" value="<?= $selected_key_specs['Rear_Camera'] ?>" required>

            <label for="Front_Camera">Front Camera:</label>
            <input type="text" name="Front_Camera" id="Front_Camera" value="<?= $selected_key_specs['Front_Camera'] ?>" required>

            <label for="Battery">Battery:</label>
            <input type="text" name="Battery" id="Battery" value="<?= $selected_key_specs['Battery'] ?>" required>

            <label for="Display">Display:</label>
            <input type="text" name="Display" id="Display" value="<?= $selected_key_specs['Display'] ?>" required>

            <p>Associated Device Model: <strong><?= $selected_key_specs['model_name'] ?></strong></p>

            <button type="submit">Update Key Specifications</button>
        </form>
        <p>
    <button onclick="window.location.href='insert_data.php'">Admin Home</button></p>
    <?php endif; ?>
</body>
</html>
