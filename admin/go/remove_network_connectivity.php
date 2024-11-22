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

// Pagination setup
$limit = 5; // Items per page
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Delete functionality
if (isset($_POST['delete'])) {
    $network_connectivity_id = $_POST['network_connectivity_id'];
    $sql_delete = "DELETE FROM network_connectivity WHERE network_connectivity_id = ?";
    $stmt = $conn->prepare($sql_delete);
    $stmt->bind_param("i", $network_connectivity_id);
    $stmt->execute();
    $stmt->close();
}

// Fetch records with pagination
$sql = "SELECT nc.*, d.model_name 
        FROM network_connectivity nc 
        JOIN device d ON nc.device_id = d.device_id 
        LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

// Count total records for pagination
$sql_count = "SELECT COUNT(*) FROM network_connectivity";
$count_result = $conn->query($sql_count);
$total_rows = $count_result->fetch_row()[0];
$total_pages = ceil($total_rows / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Network Connectivity</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f9f9f9;
            padding: 20px;
        }

        h2, h3 {
            color: #333;
        }

        form {
            margin-bottom: 20px;
            background: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        label {
            font-weight: bold;
            margin-right: 10px;
        }

        select, button {
            padding: 10px;
            font-size: 16px;
            margin-right: 10px;
        }

        button {
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
        }

        button:hover {
            background-color: #0056b3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th, td {
            padding: 12px;
            border: 1px solid #ddd;
        }

        th {
            background-color: #007bff;
            color: #fff;
        }

        .pagination {
            margin-top: 20px;
            text-align: center;
        }

        .pagination a {
            margin: 0 5px;
            padding: 8px 12px;
            text-decoration: none;
            background: #f1f1f1;
            color: #007bff;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .pagination a.active {
            background: #007bff;
            color: #fff;
        }
    </style>
</head>
<body>
    <h2>Manage Network Connectivity Records</h2>

    <!-- Delete Form -->
    <form method="POST">
        <label for="network_connectivity_id">Select Record:</label>
        <select name="network_connectivity_id" id="network_connectivity_id">
            <?php
            // Reset result pointer for dropdown
            $result->data_seek(0);
            while ($row = $result->fetch_assoc()): ?>
                <option value="<?= $row['network_connectivity_id']; ?>">
                    <?= $row['model_name']; ?> - SIM Slots: <?= $row['sim_slots']; ?>
                </option>
            <?php endwhile; ?>
        </select>
        <button type="submit" name="delete">Delete</button>
    </form>

    <h3>Network Connectivity Records</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Model Name</th>
                <th>SIM Slots</th>
                <th>SIM Size</th>
                <th>Network Support</th>
                <th>VoLTE</th>
                <th>WiFi</th>
                <th>Bluetooth</th>
                <th>GPS</th>
                <th>NFC</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Reset result pointer for table
            $result->data_seek(0);
            while ($row = $result->fetch_assoc()):
            ?>
                <tr>
                    <td><?= $row['network_connectivity_id']; ?></td>
                    <td><?= $row['model_name']; ?></td>
                    <td><?= $row['sim_slots']; ?></td>
                    <td><?= $row['sim_size']; ?></td>
                    <td><?= $row['network_support']; ?></td>
                    <td><?= $row['volte'] ? 'Yes' : 'No'; ?></td>
                    <td><?= $row['wifi'] ? 'Yes' : 'No'; ?></td>
                    <td><?= $row['bluetooth'] ?: 'N/A'; ?></td>
                    <td><?= $row['gps'] ? 'Yes' : 'No'; ?></td>
                    <td><?= $row['nfc'] ? 'Yes' : 'No'; ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="network_connectivity_id" value="<?= $row['network_connectivity_id']; ?>">
                            <button type="submit" name="delete">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <div class="pagination">
        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="?page=<?= $i; ?>" class="<?= ($page == $i) ? 'active' : ''; ?>"><?= $i; ?></a>
        <?php endfor; ?>
    </div>
    <p>
    <button onclick="window.location.href='insert_data.php'">Admin Home</button></p>
</body>
</html>

<?php
$conn->close();
?>
