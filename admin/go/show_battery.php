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

// Fetch battery data with model_name from device table
$sql = "SELECT b.*, d.model_name 
        FROM battery b 
        JOIN device d ON b.device_id = d.device_id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Battery Table</title>
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
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
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
        <h2>Battery Data</h2>
        <table>
            <thead>
                <tr>
                    <th>Battery ID</th>
                    <th>Model Name</th>
                    <th>Capacity</th>
                    <th>Type</th>
                    <th>Removable</th>
                    <th>Talk Time</th>
                    <th>Wireless Charging</th>
                    <th>Quick Charging</th>
                    <th>USB Type-C</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0):
                    while ($row = $result->fetch_assoc()):
                ?>
                        <tr>
                            <td><?= $row['battery_id']; ?></td>
                            <td><?= $row['model_name']; ?></td>
                            <td><?= $row['capacity']; ?></td>
                            <td><?= $row['type']; ?></td>
                            <td class="center"><?= $row['removable'] ? 'Yes' : 'No'; ?></td>
                            <td><?= $row['talk_time'] ?: 'N/A'; ?></td>
                            <td class="center"><?= $row['wireless_charging'] ? 'Yes' : 'No'; ?></td>
                            <td class="center"><?= $row['quick_charging'] ? 'Yes' : 'No'; ?></td>
                            <td class="center"><?= $row['usb_type_c'] ? 'Yes' : 'No'; ?></td>
                        </tr>
                <?php
                    endwhile;
                else:
                ?>
                    <tr>
                        <td colspan="11" class="center">No data found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <p>
    <button onclick="window.location.href='insert_data.php'">Admin Home</button></p>
    </div>
    
    <footer>
        &copy; <?= date("Y"); ?> Battery Management
    </footer>
</body>
</html>

<?php
$conn->close();
?>
