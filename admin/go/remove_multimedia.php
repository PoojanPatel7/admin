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
    $multimedia_id = $_POST['multimedia_id'];
    $sql_delete = "DELETE FROM multimedia WHERE multimedia_id = ?";
    $stmt = $conn->prepare($sql_delete);
    $stmt->bind_param("i", $multimedia_id);
    $stmt->execute();
    $stmt->close();
}

// Fetch data with pagination
$sql = "SELECT m.*, d.model_name 
        FROM multimedia m 
        JOIN device d ON m.device_id = d.device_id 
        LIMIT $limit OFFSET $offset";
$result = $conn->query($sql);

// Get total number of records for pagination
$sql_count = "SELECT COUNT(*) FROM multimedia";
$count_result = $conn->query($sql_count);
$total_rows = $count_result->fetch_row()[0];
$total_pages = ceil($total_rows / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Multimedia Records</title>
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
            width: 90%;
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

    <h2>Manage Multimedia Records</h2>

    <!-- Multimedia Deletion Form -->
    <form method="POST">
        <label for="multimedia_id">Select Multimedia Record:</label>
        <select name="multimedia_id" id="multimedia_id">
            <?php
            // Reset result pointer for dropdown
            $result->data_seek(0);
            while ($row = $result->fetch_assoc()): ?>
                <option value="<?= $row['multimedia_id']; ?>">
                    <?= $row['model_name']; ?> - 
                    <?= $row['fm_radio'] ? "FM Radio" : "No FM Radio"; ?>
                </option>
            <?php endwhile; ?>
        </select>
        <button type="submit" name="delete">Remove Selected Record</button>
    </form>

    <h3>Multimedia Records</h3>
    <table>
        <thead>
            <tr>
                <th>Multimedia ID</th>
                <th>Model Name</th>
                <th>FM Radio</th>
                <th>Stereo Speakers</th>
                <th>Loudspeaker</th>
                <th>Audio Jack</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Reset result pointer for table display
            $result->data_seek(0);
            while ($row = $result->fetch_assoc()):
            ?>
                <tr>
                    <td><?= $row['multimedia_id']; ?></td>
                    <td><?= $row['model_name']; ?></td>
                    <td><?= $row['fm_radio'] ? "Yes" : "No"; ?></td>
                    <td><?= $row['stereo_speakers'] ? "Yes" : "No"; ?></td>
                    <td><?= $row['loudspeaker'] ? "Yes" : "No"; ?></td>
                    <td><?= $row['audio_jack'] ?: "N/A"; ?></td>
                    <td>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="multimedia_id" value="<?= $row['multimedia_id']; ?>">
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
