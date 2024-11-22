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

// Fetch all display records joined with the device table
$displays = $conn->query("
    SELECT dis.display_id, dis.display_type, dis.device_id, dev.model_name 
    FROM display dis 
    JOIN device dev ON dis.device_id = dev.device_id
");

// Handle the update operation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $display_id = $_POST['display_id'];
    $display_type = $conn->real_escape_string($_POST['display_type']);
    $screen_size = $conn->real_escape_string($_POST['screen_size']);
    $resolution = $conn->real_escape_string($_POST['resolution']);
    $peak_brightness = $conn->real_escape_string($_POST['peak_brightness']);
    $refresh_rate = $conn->real_escape_string($_POST['refresh_rate']);
    $aspect_ratio = $conn->real_escape_string($_POST['aspect_ratio']);
    $pixel_density = $conn->real_escape_string($_POST['pixel_density']);
    $screen_to_body_ratio = $conn->real_escape_string($_POST['screen_to_body_ratio']);
    $screen_protection = $conn->real_escape_string($_POST['screen_protection']);
    $bezel_less_display = isset($_POST['bezel_less_display']) ? 1 : 0;
    $touch_screen = isset($_POST['touch_screen']) ? 1 : 0;
    $hdr_support = isset($_POST['hdr_support']) ? 1 : 0;

    $update_query = "
        UPDATE display 
        SET 
            display_type = '$display_type', 
            screen_size = '$screen_size', 
            resolution = '$resolution', 
            peak_brightness = '$peak_brightness', 
            refresh_rate = '$refresh_rate', 
            aspect_ratio = '$aspect_ratio', 
            pixel_density = '$pixel_density', 
            screen_to_body_ratio = '$screen_to_body_ratio', 
            screen_protection = '$screen_protection', 
            bezel_less_display = $bezel_less_display, 
            touch_screen = $touch_screen, 
            hdr_support = $hdr_support
        WHERE display_id = $display_id
    ";

    if ($conn->query($update_query)) {
        $message = "Display updated successfully!";
    } else {
        $message = "Error updating display: " . $conn->error;
    }
}

// Fetch details of the selected display for pre-filling the form
$selected_display = null;
if (isset($_GET['display_id'])) {
    $display_id = $_GET['display_id'];
    $selected_display_result = $conn->query("
        SELECT dis.*, dev.model_name 
        FROM display dis 
        JOIN device dev ON dis.device_id = dev.device_id 
        WHERE dis.display_id = $display_id
    ");
    $selected_display = $selected_display_result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Display</title>
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
    <h1>Update Display</h1>

    <?php if (isset($message)): ?>
        <p class="<?= strpos($message, 'success') !== false ? 'message' : 'error' ?>">
            <?= $message ?>
        </p>
    <?php endif; ?>

    <!-- Form to select a display -->
    <form method="get">
        <label for="display_id">Select a Display to Update:</label>
        <select name="display_id" id="display_id" required onchange="this.form.submit()">
            <option value="">-- Select a Display --</option>
            <?php while ($row = $displays->fetch_assoc()): ?>
                <option value="<?= $row['display_id'] ?>" <?= isset($display_id) && $display_id == $row['display_id'] ? 'selected' : '' ?>>
                    <?= "Display ID: " . $row['display_id'] . " (Model: " . $row['model_name'] . ")" ?>
                </option>
            <?php endwhile; ?>
        </select>
    </form>

    <?php if ($selected_display): ?>
        <!-- Form to update the selected display -->
        <form method="post">
            <input type="hidden" name="display_id" value="<?= $selected_display['display_id'] ?>">

            <label for="display_type">Display Type:</label>
            <input type="text" name="display_type" id="display_type" value="<?= $selected_display['display_type'] ?>" required>

            <label for="screen_size">Screen Size:</label>
            <input type="text" name="screen_size" id="screen_size" value="<?= $selected_display['screen_size'] ?>" required>

            <label for="resolution">Resolution:</label>
            <input type="text" name="resolution" id="resolution" value="<?= $selected_display['resolution'] ?>" required>

            <label for="peak_brightness">Peak Brightness:</label>
            <input type="text" name="peak_brightness" id="peak_brightness" value="<?= $selected_display['peak_brightness'] ?>">

            <label for="refresh_rate">Refresh Rate:</label>
            <input type="text" name="refresh_rate" id="refresh_rate" value="<?= $selected_display['refresh_rate'] ?>">

            <label for="aspect_ratio">Aspect Ratio:</label>
            <input type="text" name="aspect_ratio" id="aspect_ratio" value="<?= $selected_display['aspect_ratio'] ?>">

            <label for="pixel_density">Pixel Density:</label>
            <input type="text" name="pixel_density" id="pixel_density" value="<?= $selected_display['pixel_density'] ?>">

            <label for="screen_to_body_ratio">Screen-to-Body Ratio:</label>
            <input type="text" name="screen_to_body_ratio" id="screen_to_body_ratio" value="<?= $selected_display['screen_to_body_ratio'] ?>">

            <label for="screen_protection">Screen Protection:</label>
            <input type="text" name="screen_protection" id="screen_protection" value="<?= $selected_display['screen_protection'] ?>">

            <label for="bezel_less_display">
                <input type="checkbox" name="bezel_less_display" id="bezel_less_display" <?= $selected_display['bezel_less_display'] ? 'checked' : '' ?>>
                Bezel-less Display
            </label>

            <label for="touch_screen">
                <input type="checkbox" name="touch_screen" id="touch_screen" <?= $selected_display['touch_screen'] ? 'checked' : '' ?>>
                Touch Screen
            </label>

            <label for="hdr_support">
                <input type="checkbox" name="hdr_support" id="hdr_support" <?= $selected_display['hdr_support'] ? 'checked' : '' ?>>
                HDR Support
            </label>

            <p>Associated Device Model: <strong><?= $selected_display['model_name'] ?></strong></p>

            <button type="submit">Update Display</button>
        </form>
        <p>
    <button onclick="window.location.href='insert_data.php'">Admin Home</button></p>
    <?php endif; ?>
</body>
</html>
