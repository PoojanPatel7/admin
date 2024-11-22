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

// Fetch display data and join with the device table to get model_name
$sql = "SELECT display.*, device.model_name 
        FROM display
        INNER JOIN device ON display.device_id = device.device_id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display Data</title>
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
        footer {
            text-align: center;
            margin-top: 20px;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Display Data</h2>
        <table>
            <thead>
                <tr>
                    <th>Display ID</th>
                    <th>Model Name</th>
                    <th>Display Type</th>
                    <th>Screen Size</th>
                    <th>Resolution</th>
                    <th>Peak Brightness</th>
                    <th>Refresh Rate</th>
                    <th>Aspect Ratio</th>
                    <th>Pixel Density</th>
                    <th>Screen to Body Ratio</th>
                    <th>Screen Protection</th>
                    <th>Bezel Less Display</th>
                    <th>Touch Screen</th>
                    <th>HDR Support</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0):
                    while ($row = $result->fetch_assoc()):
                ?>
                        <tr>
                            <td><?= $row['display_id']; ?></td>
                            <td><?= $row['model_name']; ?></td>
                            <td><?= $row['display_type']; ?></td>
                            <td><?= $row['screen_size']; ?></td>
                            <td><?= $row['resolution']; ?></td>
                            <td><?= $row['peak_brightness']; ?></td>
                            <td><?= $row['refresh_rate']; ?></td>
                            <td><?= $row['aspect_ratio']; ?></td>
                            <td><?= $row['pixel_density']; ?></td>
                            <td><?= $row['screen_to_body_ratio']; ?></td>
                            <td><?= $row['screen_protection']; ?></td>
                            <td><?= $row['bezel_less_display'] ? 'Yes' : 'No'; ?></td>
                            <td><?= $row['touch_screen'] ? 'Yes' : 'No'; ?></td>
                            <td><?= $row['hdr_support'] ? 'Yes' : 'No'; ?></td>
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
        &copy; <?= date("Y"); ?> Display Management
    </footer>
</body>
</html>

<?php
$conn->close();
?>
