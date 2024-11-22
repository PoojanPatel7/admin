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

// Fetch performance data and join with the device table to get model_name
$sql = "SELECT performance.*, device.model_name 
        FROM performance
        INNER JOIN device ON performance.device_id = device.device_id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Performance Data</title>
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
        <h2>Performance Data</h2>
        <table>
            <thead>
                <tr>
                    <th>Performance ID</th>
                    <th>Model Name</th>
                    <th>Chipset</th>
                    <th>CPU</th>
                    <th>Architecture</th>
                    <th>Fabrication</th>
                    <th>Graphics</th>
                    <th>RAM</th>
                    <th>RAM Type</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0):
                    while ($row = $result->fetch_assoc()):
                ?>
                        <tr>
                            <td><?= $row['performance_id']; ?></td>
                            <td><?= $row['model_name']; ?></td>
                            <td><?= $row['chipset']; ?></td>
                            <td><?= $row['CPU']; ?></td>
                            <td><?= $row['architecture']; ?></td>
                            <td><?= $row['fabrication']; ?></td>
                            <td><?= $row['graphics']; ?></td>
                            <td><?= $row['RAM']; ?></td>
                            <td><?= $row['RAM_type']; ?></td>
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
    </div>
    <p>
    <button onclick="window.location.href='insert_data.php'">Admin Home</button></p>
    <footer>
        &copy; <?= date("Y"); ?> Performance Data Management
    </footer>
</body>
</html>

<?php
$conn->close();
?>
