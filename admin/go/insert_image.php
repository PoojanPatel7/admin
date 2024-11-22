<?php
// Database connection
$host = 'localhost';  // Database host
$username = 'root';   // Database username
$password = '';       // Database password
$dbname = 'go';  // Database name

$conn = new mysqli($host, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the 'model_name' is set in the URL for displaying images or uploading images
if (isset($_GET['model_name'])) {
    $model_name = $_GET['model_name'];

    // Fetch the device_id based on the selected model_name
    $query_device = "SELECT device_id FROM device WHERE model_name = '$model_name' LIMIT 1";
    $result_device = $conn->query($query_device);
    $device = $result_device->fetch_assoc();
    $device_id = $device['device_id'];

    // Check if the form for uploading an image has been submitted
    if (isset($_POST['upload'])) {
        // Handle the image upload
        if ($_FILES['image']['error'] == UPLOAD_ERR_OK) {
            $image_name = $_FILES['image']['name'];
            $image_data = file_get_contents($_FILES['image']['tmp_name']);
            $image_type = $_FILES['image']['type'];

            // Insert image into the database
            $query_insert_image = "INSERT INTO images (image_name, image_data, device_id) VALUES ('$image_name', ?, $device_id)";
            $stmt = $conn->prepare($query_insert_image);
            $stmt->bind_param('s', $image_data);
            $stmt->execute();
            echo "Image uploaded successfully!";
        } else {
            echo "Error uploading image!";
        }
    }

    // Pagination settings for displaying images
    $limit = 5;
    $page = isset($_GET['page']) ? $_GET['page'] : 1;
    $offset = ($page - 1) * $limit;

    // Fetch the images associated with the selected device
    $query_images = "SELECT * FROM images WHERE device_id = $device_id LIMIT $limit OFFSET $offset";
    $result_images = $conn->query($query_images);

    // Fetch the total number of images for pagination
    $query_count = "SELECT COUNT(*) AS total FROM images WHERE device_id = $device_id";
    $result_count = $conn->query($query_count);
    $row_count = $result_count->fetch_assoc();
    $total_images = $row_count['total'];
    $total_pages = ceil($total_images / $limit);

    // Display the images page
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Images for ' . $model_name . '</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f4;
                margin: 0;
                padding: 0;
                color: #333;
            }
            h1, h2 {
                color: #007BFF;
            }
            form {
                background-color: #fff;
                padding: 20px;
                margin: 20px;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                max-width: 500px;
                margin-left: auto;
                margin-right: auto;
            }
            input[type="file"] {
                margin: 10px 0;
            }
            button {
                background-color: #007BFF;
                color: #fff;
                padding: 10px 20px;
                border: none;
                border-radius: 5px;
                cursor: pointer;
            }
            button:hover {
                background-color: #0056b3;
            }
            .image-container {
                margin-top: 20px;
            }
            .image-container img {
                margin: 10px;
                border: 1px solid #ddd;
                border-radius: 5px;
            }
            .pagination {
                margin-top: 20px;
            }
            .pagination a {
                padding: 8px 16px;
                text-decoration: none;
                background-color: #007BFF;
                color: white;
                border-radius: 5px;
                margin: 0 5px;
            }
            .pagination a:hover {
                background-color: #0056b3;
            }
        </style>
    </head>
    <body>
        <h1>Images for ' . $model_name . '</h1>
        
        <h2>Upload Image</h2>
        <form action="" method="POST" enctype="multipart/form-data">
            <label for="image">Choose an image to upload: </label>
            <input type="file" name="image" id="image" required>
            <button type="submit" name="upload">Upload Image</button>
        </form>';

    while ($image = $result_images->fetch_assoc()) {
        echo '<div class="image-container">
                <h3>' . $image['image_name'] . '</h3>
                <img src="data:image/jpeg;base64,' . base64_encode($image['image_data']) . '" alt="' . $image['image_name'] . '" width="200">
              </div>';
    }

    echo '<div class="pagination">
            <h3>Pagination</h3>';

    if ($page > 1) {
        echo '<a href="?model_name=' . $model_name . '&page=' . ($page - 1) . '">Previous</a>';
    }

    if ($page < $total_pages) {
        echo '<a href="?model_name=' . $model_name . '&page=' . ($page + 1) . '">Next</a>';
    }

    echo '</div>
    </body>
    </html>';
} else {
    // Fetch the model names from the device table for the selection page
    $query = "SELECT DISTINCT model_name FROM device";
    $result = $conn->query($query);

    // Display the selection page
    echo '<!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Select Model</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f4;
                margin: 0;
                padding: 0;
                color: #333;
            }
            h1 {
                color: #007BFF;
            }
            form {
                background-color: #fff;
                padding: 20px;
                margin: 20px;
                border-radius: 8px;
                box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                max-width: 500px;
                margin-left: auto;
                margin-right: auto;
            }
            select, button {
                padding: 10px;
                margin-top: 10px;
                width: 100%;
            }
            button {
                background-color: #007BFF;
                color: #fff;
                border: none;
                border-radius: 5px;
                cursor: pointer;
            }
            button:hover {
                background-color: #0056b3;
            }
        </style>
    </head>
    <body>
        <h1>Select Device Model</h1>
        <form action="" method="GET">
            <label for="model_name">Select Model: </label>
            <select name="model_name" id="model_name" required>';

    while ($row = $result->fetch_assoc()) {
        echo '<option value="' . $row['model_name'] . '">' . $row['model_name'] . '</option>';
    }

    echo '</select>
            <button type="submit">Submit</button>
        </form>
    </body>
    </html>';
}

$conn->close();
?>
