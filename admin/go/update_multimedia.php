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

// Fetch all multimedia records joined with the device table
$multimedia_records = $conn->query("
    SELECT m.multimedia_id, m.fm_radio, m.stereo_speakers, m.loudspeaker, m.audio_jack, m.device_id, d.model_name
    FROM multimedia m 
    JOIN device d ON m.device_id = d.device_id
");

// Handle the update operation
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $multimedia_id = $_POST['multimedia_id'];
    $fm_radio = isset($_POST['fm_radio']) ? 1 : 0;
    $stereo_speakers = isset($_POST['stereo_speakers']) ? 1 : 0;
    $loudspeaker = isset($_POST['loudspeaker']) ? 1 : 0;
    $audio_jack = $conn->real_escape_string($_POST['audio_jack']);

    $update_query = "
        UPDATE multimedia 
        SET 
            fm_radio = $fm_radio, 
            stereo_speakers = $stereo_speakers, 
            loudspeaker = $loudspeaker, 
            audio_jack = '$audio_jack'
        WHERE multimedia_id = $multimedia_id
    ";

    if ($conn->query($update_query)) {
        $message = "Multimedia data updated successfully!";
    } else {
        $message = "Error updating multimedia data: " . $conn->error;
    }
}

// Fetch details of the selected multimedia record for pre-filling the form
$selected_multimedia = null;
if (isset($_GET['multimedia_id'])) {
    $multimedia_id = $_GET['multimedia_id'];
    $selected_multimedia_result = $conn->query("
        SELECT m.*, d.model_name 
        FROM multimedia m 
        JOIN device d ON m.device_id = d.device_id 
        WHERE m.multimedia_id = $multimedia_id
    ");
    $selected_multimedia = $selected_multimedia_result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Multimedia Data</title>
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
    <h1>Update Multimedia Data</h1>

    <?php if (isset($message)): ?>
        <p class="<?= strpos($message, 'success') !== false ? 'message' : 'error' ?>">
            <?= $message ?>
        </p>
    <?php endif; ?>

    <!-- Form to select a multimedia record -->
    <form method="get">
        <label for="multimedia_id">Select a Record to Update:</label>
        <select name="multimedia_id" id="multimedia_id" required onchange="this.form.submit()">
            <option value="">-- Select a Record --</option>
            <?php while ($row = $multimedia_records->fetch_assoc()): ?>
                <option value="<?= $row['multimedia_id'] ?>" <?= isset($multimedia_id) && $multimedia_id == $row['multimedia_id'] ? 'selected' : '' ?>>
                    <?= "Multimedia ID: " . $row['multimedia_id'] . " (Model: " . $row['model_name'] . ")" ?>
                </option>
            <?php endwhile; ?>
        </select>
    </form>

    <?php if ($selected_multimedia): ?>
        <!-- Form to update the selected multimedia record -->
        <form method="post">
            <input type="hidden" name="multimedia_id" value="<?= $selected_multimedia['multimedia_id'] ?>">

            <label for="fm_radio">
                <input type="checkbox" name="fm_radio" id="fm_radio" <?= $selected_multimedia['fm_radio'] ? 'checked' : '' ?>>
                FM Radio
            </label>

            <label for="stereo_speakers">
                <input type="checkbox" name="stereo_speakers" id="stereo_speakers" <?= $selected_multimedia['stereo_speakers'] ? 'checked' : '' ?>>
                Stereo Speakers
            </label>

            <label for="loudspeaker">
                <input type="checkbox" name="loudspeaker" id="loudspeaker" <?= $selected_multimedia['loudspeaker'] ? 'checked' : '' ?>>
                Loudspeaker
            </label>

            <label for="audio_jack">Audio Jack:</label>
            <input type="text" name="audio_jack" id="audio_jack" value="<?= $selected_multimedia['audio_jack'] ?>">

            <p>Associated Device Model: <strong><?= $selected_multimedia['model_name'] ?></strong></p>

            <button type="submit">Update Multimedia Data</button>
        </form>
        <p>
    <button onclick="window.location.href='insert_data.php'">Admin Home</button></p>
    <?php endif; ?>
</body>
</html>
