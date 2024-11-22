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

// Fetch storage data and join with the device table to get model_name
$sql = "SELECT storage.*, device.model_name 
        FROM storage
        INNER JOIN device ON storage.device_id = device.device_id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Storage Data</title>
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
        <h2>Storage Data</h2>
        <table>
            <thead>
                <tr>
                    <th>Storage ID</th>
                    <th>Model Name</th>
                    <th>Internal Memory</th>
                    <th>Expandable Memory</th>
                    <th>User Available Storage</th>
                    <th>Storage Type</th>
                    <th>USB OTG</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0):
                    while ($row = $result->fetch_assoc()):
                ?>
                        <tr>
                            <td><?= $row['storage_id']; ?></td>
                            <td><?= $row['model_name']; ?></td>
                            <td><?= $row['internal_memory']; ?></td>
                            <td><?= $row['expandable_memory']; ?></td>
                            <td><?= $row['user_available_storage']; ?></td>
                            <td><?= $row['storage_type']; ?></td>
                            <td><?= $row['usb_otg'] ? 'Yes' : 'No'; ?></td>
                        </tr>
                <?php
                    endwhile;
                else:
                ?>
                    <tr>
                        <td colspan="9" class="center">No data found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <p>
    <button onclick="window.location.href='insert_data.php'">Admin Home</button></p>
    <footer>
        &copy; <?= date("Y"); ?> Storage Data Management
    </footer>
</body>
</html>

<?php
$conn->close();
?>
