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

// Fetch device data
$sql = "SELECT * FROM device";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Device Data</title>
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
        <h2>Device Data</h2>
        <table>
            <thead>
                <tr>
                    <th>Device ID</th>
                    <th>Brand</th>
                    <th>Model Name</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0):
                    while ($row = $result->fetch_assoc()):
                ?>
                        <tr>
                            <td><?= $row['device_id']; ?></td>
                            <td><?= $row['brand']; ?></td>
                            <td><?= $row['model_name']; ?></td>
                        </tr>
                <?php
                    endwhile;
                else:
                ?>
                    <tr>
                        <td colspan="5" class="center">No data found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
    <p>
    <button onclick="window.location.href='insert_data.php'">Admin Home</button></p>
    <footer>
        &copy; <?= date("Y"); ?> Device Management
    </footer>
</body>
</html>

<?php
$conn->close();
?>
