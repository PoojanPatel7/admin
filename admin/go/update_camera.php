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

// Fetch all camera records joined with the device table
$cameras = $conn->query("
    SELECT c.camera_id, c.camera, c.device_id, d.model_name 
    FROM camera c 
    JOIN device d ON c.device_id = d.device_id
");

// Handle the update operation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $camera_id = $_POST['camera_id'];
    $camera = $conn->real_escape_string($_POST['camera']);
    $resolution = $conn->real_escape_string($_POST['resolution']);
    $autofocus = $conn->real_escape_string($_POST['autofocus']);
    $ois = isset($_POST['ois']) ? 1 : 0;
    $flash = isset($_POST['flash']) ? 1 : 0;
    $image_resolution = $conn->real_escape_string($_POST['image_resolution']);
    $settings = $conn->real_escape_string($_POST['settings']);
    $shooting_modes = $conn->real_escape_string($_POST['shooting_modes']);
    $camera_features = $conn->real_escape_string($_POST['camera_features']);
    $video_recording = isset($_POST['video_recording']) ? 1 : 0;
    $video_recording_features = $conn->real_escape_string($_POST['video_recording_features']);
    $camera_setup = $conn->real_escape_string($_POST['camera_setup']);

    $update_query = "
        UPDATE camera 
        SET 
            camera = '$camera', 
            resolution = '$resolution', 
            autofocus = '$autofocus', 
            ois = $ois, 
            flash = $flash, 
            image_resolution = '$image_resolution', 
            settings = '$settings', 
            shooting_modes = '$shooting_modes', 
            camera_features = '$camera_features', 
            video_recording = $video_recording, 
            video_recording_features = '$video_recording_features', 
            camera_setup = '$camera_setup'
        WHERE camera_id = $camera_id
    ";

    if ($conn->query($update_query)) {
        $message = "Camera updated successfully!";
    } else {
        $message = "Error updating camera: " . $conn->error;
    }
}

// Fetch details of the selected camera for pre-filling the form
$selected_camera = null;
if (isset($_GET['camera_id'])) {
    $camera_id = $_GET['camera_id'];
    $selected_camera_result = $conn->query("
        SELECT c.*, d.model_name 
        FROM camera c 
        JOIN device d ON c.device_id = d.device_id 
        WHERE c.camera_id = $camera_id
    ");
    $selected_camera = $selected_camera_result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Camera</title>
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
    <h1>Update Camera</h1>

    <?php if (isset($message)): ?>
        <p class="<?= strpos($message, 'success') !== false ? 'message' : 'error' ?>">
            <?= $message ?>
        </p>
    <?php endif; ?>

    <!-- Form to select a camera -->
    <form method="get">
        <label for="camera_id">Select a Camera to Update:</label>
        <select name="camera_id" id="camera_id" required onchange="this.form.submit()">
            <option value="">-- Select a Camera --</option>
            <?php while ($row = $cameras->fetch_assoc()): ?>
                <option value="<?= $row['camera_id'] ?>" <?= isset($camera_id) && $camera_id == $row['camera_id'] ? 'selected' : '' ?>>
                    <?= "Camera ID: " . $row['camera_id'] . " (Model: " . $row['model_name'] . ")" ?>
                </option>
            <?php endwhile; ?>
        </select>
    </form>

    <?php if ($selected_camera): ?>
        <!-- Form to update the selected camera -->
        <form method="post">
            <input type="hidden" name="camera_id" value="<?= $selected_camera['camera_id'] ?>">

            <label for="camera">Camera:</label>
            <input type="text" name="camera" id="camera" value="<?= $selected_camera['camera'] ?>" required>

            <label for="resolution">Resolution:</label>
            <input type="text" name="resolution" id="resolution" value="<?= $selected_camera['resolution'] ?>" required>

            <label for="autofocus">Autofocus:</label>
            <input type="text" name="autofocus" id="autofocus" value="<?= $selected_camera['autofocus'] ?>">

            <label for="ois">
                <input type="checkbox" name="ois" id="ois" <?= $selected_camera['ois'] ? 'checked' : '' ?>>
                OIS (Optical Image Stabilization)
            </label>

            <label for="flash">
                <input type="checkbox" name="flash" id="flash" <?= $selected_camera['flash'] ? 'checked' : '' ?>>
                Flash
            </label>

            <label for="image_resolution">Image Resolution:</label>
            <input type="text" name="image_resolution" id="image_resolution" value="<?= $selected_camera['image_resolution'] ?>">

            <label for="settings">Settings:</label>
            <textarea name="settings" id="settings"><?= $selected_camera['settings'] ?></textarea>

            <label for="shooting_modes">Shooting Modes:</label>
            <textarea name="shooting_modes" id="shooting_modes"><?= $selected_camera['shooting_modes'] ?></textarea>

            <label for="camera_features">Camera Features:</label>
            <textarea name="camera_features" id="camera_features"><?= $selected_camera['camera_features'] ?></textarea>

            <label for="video_recording">
                <input type="checkbox" name="video_recording" id="video_recording" <?= $selected_camera['video_recording'] ? 'checked' : '' ?>>
                Video Recording
            </label>

            <label for="video_recording_features">Video Recording Features:</label>
            <textarea name="video_recording_features" id="video_recording_features"><?= $selected_camera['video_recording_features'] ?></textarea>

            <label for="camera_setup">Camera Setup:</label>
            <input type="text" name="camera_setup" id="camera_setup" value="<?= $selected_camera['camera_setup'] ?>">

            <p>Associated Device Model: <strong><?= $selected_camera['model_name'] ?></strong></p>

            <button type="submit">Update Camera</button>
        </form>
        <p>
    <button onclick="window.location.href='insert_data.php'">Admin Home</button></p>
    <?php endif; ?>
</body>
</html>
