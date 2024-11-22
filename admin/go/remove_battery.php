<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "go"; // Replace with your actual database name

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Pagination logic
$limit = 5; // Number of items per page
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Delete functionality
if (isset($_POST['delete'])) {
    $device_id = $_POST['device_id'];
    $sql_delete = "DELETE FROM battery WHERE device_id = ?";
    $stmt = $conn->prepare($sql_delete);
    $stmt->bind_param("i", $device_id);
    $stmt->execute();
    $stmt->close();
}

// Fetch model names and device_ids from the 'device' table joined with 'battery'
$sql_modal = "SELECT DISTINCT b.device_id, d.model_name 
              FROM battery b
              JOIN device d ON b.device_id = d.device_id";
$result_modal = $conn->query($sql_modal);

// Fetch data with pagination, including model_name
$sql = "SELECT b.*, d.model_name 
        FROM battery b
        JOIN device d ON b.device_id = d.device_id
        LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

// Get total number of records for pagination
$sql_count = "SELECT COUNT(*) FROM battery";
$count_result = $conn->query($sql_count);
$total_rows = $count_result->fetch_row()[0];
$total_pages = ceil($total_rows / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Remove Data from Database</title>
    <style>
        /* General Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7fc;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        h2, h3 {
            color: #333;
            margin-bottom: 20px;
        }

        form {
            background-color: #fff;
            padding: 20px;
            margin-bottom: 40px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 600px;
        }

        label {
            font-weight: bold;
            margin-right: 10px;
        }

        select {
            padding: 8px;
            font-size: 16px;
            margin-right: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        button {
            background-color: #007bff;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #0056b3;
        }

        /* Table Styles */
        table {
            width: 80%;
            max-width: 1000px;
            margin-top: 20px;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 12px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #007bff;
            color: white;
        }

        td {
            background-color: #f9f9f9;
        }

        tr:hover td {
            background-color: #f1f1f1;
        }

        /* Pagination Styles */
        .pagination {
            margin-top: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .pagination a {
            padding: 8px 16px;
            margin: 0 5px;
            text-decoration: none;
            background-color: #f1f1f1;
            border-radius: 4px;
            border: 1px solid #ddd;
            color: #007bff;
            font-size: 16px;
            transition: background-color 0.3s, color 0.3s;
        }

        .pagination a:hover {
            background-color: #007bff;
            color: white;
        }

        .pagination a.active {
            background-color: #007bff;
            color: white;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            table {
                width: 100%;
                margin-top: 10px;
            }

            form {
                width: 90%;
                padding: 15px;
            }

            select, button {
                width: 100%;
                margin-bottom: 10px;
            }
        }
    </style>
</head>
<body>

    <h2>Remove Data from Database</h2>

    <form method="POST">
        <label for="device_id">Select Model Name:</label>
        <select name="device_id" id="device_id">
            <?php while($row = $result_modal->fetch_assoc()): ?>
                <option value="<?= $row['device_id']; ?>"><?= $row['model_name']; ?></option>
            <?php endwhile; ?>
        </select>
        <button type="submit" name="delete">Remove Selected Device</button>
    </form>

    <h3>Battery Records</h3>
    <table>
        <thead>
            <tr>
                <th>Battery ID</th>
                <th>Model Name</th> <!-- Added model_name column -->
                <th>Capacity</th>
                <th>Type</th>
                <th>Removable</th>
                <th>Talk Time</th>
                <th>Wireless Charging</th>
                <th>Quick Charging</th>
                <th>USB Type C</th>
                <th>Device ID</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['battery_id']; ?></td>
                    <td><?= $row['model_name']; ?></td> <!-- Display model_name -->
                    <td><?= $row['capacity']; ?></td>
                    <td><?= $row['type']; ?></td>
                    <td><?= $row['removable'] ? 'Yes' : 'No'; ?></td>
                    <td><?= $row['talk_time']; ?></td>
                    <td><?= $row['wireless_charging'] ? 'Yes' : 'No'; ?></td>
                    <td><?= $row['quick_charging'] ? 'Yes' : 'No'; ?></td>
                    <td><?= $row['usb_type_c'] ? 'Yes' : 'No'; ?></td>
                    <td><?= $row['device_id']; ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="device_id" value="<?= $row['device_id']; ?>">
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
