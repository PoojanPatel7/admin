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

// Fetch all design records joined with the device table
$designs = $conn->query("
    SELECT d.design_id, d.height, d.device_id, dev.model_name 
    FROM design d 
    JOIN device dev ON d.device_id = dev.device_id
");

// Handle the update operation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $design_id = $_POST['design_id'];
    $height = $conn->real_escape_string($_POST['height']);
    $width = $conn->real_escape_string($_POST['width']);
    $thickness = $conn->real_escape_string($_POST['thickness']);
    $weight = $conn->real_escape_string($_POST['weight']);
    $build_material = $conn->real_escape_string($_POST['build_material']);
    $colours = $conn->real_escape_string($_POST['colours']);
    $waterproof = isset($_POST['waterproof']) ? 1 : 0;
    $ruggedness = isset($_POST['ruggedness']) ? 1 : 0;

    $update_query = "
        UPDATE design 
        SET 
            height = '$height', 
            width = '$width', 
            thickness = '$thickness', 
            weight = '$weight', 
            build_material = '$build_material', 
            colours = '$colours', 
            waterproof = $waterproof, 
            ruggedness = $ruggedness
        WHERE design_id = $design_id
    ";

    if ($conn->query($update_query)) {
        $message = "Design updated successfully!";
    } else {
        $message = "Error updating design: " . $conn->error;
    }
}

// Fetch details of the selected design for pre-filling the form
$selected_design = null;
if (isset($_GET['design_id'])) {
    $design_id = $_GET['design_id'];
    $selected_design_result = $conn->query("
        SELECT d.*, dev.model_name 
        FROM design d 
        JOIN device dev ON d.device_id = dev.device_id 
        WHERE d.design_id = $design_id
    ");
    $selected_design = $selected_design_result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Design</title>
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
    <h1>Update Design</h1>

    <?php if (isset($message)): ?>
        <p class="<?= strpos($message, 'success') !== false ? 'message' : 'error' ?>">
            <?= $message ?>
        </p>
    <?php endif; ?>

    <!-- Form to select a design -->
    <form method="get">
        <label for="design_id">Select a Design to Update:</label>
        <select name="design_id" id="design_id" required onchange="this.form.submit()">
            <option value="">-- Select a Design --</option>
            <?php while ($row = $designs->fetch_assoc()): ?>
                <option value="<?= $row['design_id'] ?>" <?= isset($design_id) && $design_id == $row['design_id'] ? 'selected' : '' ?>>
                    <?= "Design ID: " . $row['design_id'] . " (Model: " . $row['model_name'] . ")" ?>
                </option>
            <?php endwhile; ?>
        </select>
    </form>

    <?php if ($selected_design): ?>
        <!-- Form to update the selected design -->
        <form method="post">
            <input type="hidden" name="design_id" value="<?= $selected_design['design_id'] ?>">

            <label for="height">Height:</label>
            <input type="text" name="height" id="height" value="<?= $selected_design['height'] ?>" required>

            <label for="width">Width:</label>
            <input type="text" name="width" id="width" value="<?= $selected_design['width'] ?>" required>

            <label for="thickness">Thickness:</label>
            <input type="text" name="thickness" id="thickness" value="<?= $selected_design['thickness'] ?>" required>

            <label for="weight">Weight:</label>
            <input type="text" name="weight" id="weight" value="<?= $selected_design['weight'] ?>" required>

            <label for="build_material">Build Material:</label>
            <input type="text" name="build_material" id="build_material" value="<?= $selected_design['build_material'] ?>">

            <label for="colours">Colours:</label>
            <input type="text" name="colours" id="colours" value="<?= $selected_design['colours'] ?>">

            <label for="waterproof">
                <input type="checkbox" name="waterproof" id="waterproof" <?= $selected_design['waterproof'] ? 'checked' : '' ?>>
                Waterproof
            </label>

            <label for="ruggedness">
                <input type="checkbox" name="ruggedness" id="ruggedness" <?= $selected_design['ruggedness'] ? 'checked' : '' ?>>
                Ruggedness
            </label>

            <p>Associated Device Model: <strong><?= $selected_design['model_name'] ?></strong></p>

            <button type="submit">Update Design</button>
        </form>
        <p>
    <button onclick="window.location.href='insert_data.php'">Admin Home</button></p>
    <?php endif; ?>
</body>
</html>
