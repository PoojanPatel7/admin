<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "go"; // Replace with your database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch camera data with model_name from device table
$sql = "SELECT c.*, d.model_name 
        FROM camera c 
        JOIN device d ON c.device_id = d.device_id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Camera Table</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 20px;
        }

        h2 {
            color: #333;
            text-align: center;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }

        th, td {
            padding: 12px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            background-color: #007bff;
            color: #fff;
        }

        tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .center {
            text-align: center;
        }

        .container {
            text-align: center;
        }

        footer {
            text-align: center;
            margin-top: 20px;
            color: #777;
        }
        button {
            display: block;
            width: 100%;
            background: #007bff;
            color: #fff;
            border: none;
            padding: 10px;
            cursor: pointer;
            font-size: 16px;
            border-radius: 5px;
            transition: background 0.3s;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Camera Data</h2>
        <table>
            <thead>
                <tr>
                    <th>Camera ID</th>
                    <th>Model Name</th>
                    <th>Camera</th>
                    <th>Resolution</th>
                    <th>Autofocus</th>
                    <th>OIS</th>
                    <th>Flash</th>
                    <th>Image Resolution</th>
                    <th>Settings</th>
                    <th>Shooting Modes</th>
                    <th>Camera Features</th>
                    <th>Video Recording</th>
                    <th>Video Recording Features</th>
                    <th>Camera Setup</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0):
                    while ($row = $result->fetch_assoc()):
                ?>
                        <tr>
                            <td><?= $row['camera_id']; ?></td>
                            <td><?= $row['model_name']; ?></td>
                            <td><?= $row['camera']; ?></td>
                            <td><?= $row['resolution']; ?></td>
                            <td><?= $row['autofocus'] ?: 'N/A'; ?></td>
                            <td class="center"><?= $row['ois'] ? 'Yes' : 'No'; ?></td>
                            <td class="center"><?= $row['flash'] ? 'Yes' : 'No'; ?></td>
                            <td><?= $row['image_resolution'] ?: 'N/A'; ?></td>
                            <td><?= $row['settings'] ?: 'N/A'; ?></td>
                            <td><?= $row['shooting_modes'] ?: 'N/A'; ?></td>
                            <td><?= $row['camera_features'] ?: 'N/A'; ?></td>
                            <td class="center"><?= $row['video_recording'] ? 'Yes' : 'No'; ?></td>
                            <td><?= $row['video_recording_features'] ?: 'N/A'; ?></td>
                            <td><?= $row['camera_setup'] ?: 'N/A'; ?></td>
                        </tr>
                <?php
                    endwhile;
                else:
                ?>
                    <tr>
                        <td colspan="16" class="center">No data found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>  
    <p>
    <button onclick="window.location.href='insert_data.php'">Admin Home</button></p>
    <footer>
        &copy; <?= date("Y"); ?> Camera Management
    </footer>
</body>
</html>

<?php
$conn->close();
?>
