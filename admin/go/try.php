<?php
// Database connection
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'go'; // Replace with your actual database name
$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch device records from the device table
$devices_result = $conn->query("SELECT * FROM device");

// Function to fetch the image for a device
function getDeviceImage($device_id, $conn) {
    $image_query = "SELECT image_name, image_data FROM images WHERE device_id = $device_id LIMIT 1"; // Limit to 1 image for simplicity
    $image_result = $conn->query($image_query);
    
    if ($image_result->num_rows > 0) {
        $image = $image_result->fetch_assoc();
        return 'data:image/jpeg;base64,' . base64_encode($image['image_data']);
    }
    return null;
}

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['device_id'])) {
    $device_id = $_GET['device_id'];
    $device_info = getDeviceFullInfo($device_id, $conn);
    echo json_encode($device_info);
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Device Details</title>
    <style>
        /* Reset default browser styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

/* Body styling */
body {
    font-family: 'Arial', sans-serif;
    background-color: #f8f9fa;
    display: flex;
    justify-content: center;
    align-items: center;
    flex-direction: column;
    height: 100vh;
    color: #333;
    padding: 20px;
}

/* Main header */
h1 {
    font-size: 2.5rem;
    margin-bottom: 30px;
    color: #2c3e50;
    text-align: center;
}

/* Container for device cards */
.device-box {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: center;
    padding: 0 20px;
}

/* Device card styling */
.device-card {
    background-color: #fff;
    border: 1px solid #ddd;
    border-radius: 10px;
    padding: 15px;
    width: 260px;
    height: auto;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    transition: transform 0.3s ease, box-shadow 0.3s ease;
    cursor: pointer;
    text-align: center;
    overflow: hidden;
}

.device-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
}

.device-card img {
    width: 100%;
    height: 160px;
    object-fit: cover;
    border-radius: 8px;
    margin-bottom: 15px;
}

.device-card h3 {
    font-size: 1.4rem;
    margin-bottom: 10px;
    color: #2c3e50;
    font-weight: bold;
}

.device-card p {
    font-size: 0.9rem;
    color: #555;
    margin: 5px 0;
}

.device-card button {
    background-color: #3498db;
    color: white;
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
    transition: background-color 0.3s ease;
    margin-top: 15px;
}

.device-card button:hover {
    background-color: #2980b9;
}

/* Modal Styling */
.modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.7);
    justify-content: center;
    align-items: center;
    z-index: 1000;
    padding: 20px;
}

.modal-content {
    background-color: #fff;
    padding: 30px;
    border-radius: 10px;
    width: 80%;
    max-height: 80%;
    overflow-y: auto;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}

.close {
    color: #aaa;
    float: right;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
}

.close:hover,
.close:focus {
    color: black;
}

h2 {
    font-size: 1.8rem;
    margin-bottom: 20px;
    color: #2c3e50;
}

/* Device info list */
ul {
    margin: 10px 0;
    list-style-type: none;
}

li {
    font-size: 1rem;
    color: #555;
    margin-bottom: 8px;
}

strong {
    color: #3498db;
}

/* Footer Styling */
footer {
    margin-top: 40px;
    text-align: center;
    font-size: 0.9rem;
    color: #777;
}

/* Mobile responsiveness */
@media (max-width: 768px) {
    .device-card {
        width: 100%;
        max-width: 300px;
    }

    h1 {
        font-size: 2rem;
    }

    .modal-content {
        width: 90%;
        padding: 20px;
    }

    footer {
        font-size: 0.8rem;
    }
}

@media (max-width: 480px) {
    .device-card {
        width: 100%;
        max-width: 260px;
    }

    .device-card h3 {
        font-size: 1.2rem;
    }

    .device-card p {
        font-size: 0.8rem;
    }

    footer {
        font-size: 0.7rem;
    }
}

    </style>
</head>
<body>

    <h1>Device Details</h1>
    <div class="device-box">
        <?php while ($device = $devices_result->fetch_assoc()): ?>
            <?php 
                // Get the device image
                $device_image = getDeviceImage($device['device_id'], $conn);
            ?>
            <div class="device-card" onclick="showFullInfo(<?= $device['device_id'] ?>)">
                <?php if ($device_image): ?>
                    <img src="<?= $device_image ?>" alt="Device Image">
                <?php else: ?>
                    <img src="default-image.jpg" alt="No Image Available"> <!-- Default image if none exists -->
                <?php endif; ?>
                <h3><?= $device['model_name'] ?></h3>
                <p><strong>Brand:</strong> <?= $device['brand'] ?></p>
                <button>Full Info</button>
            </div>
        <?php endwhile; ?>
    </div>

    <!-- Modal for displaying full info -->
    <div id="deviceModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeModal()">&times;</span>
            <h2>Full Device Information</h2>
            <div id="deviceInfo"></div>
        </div>
    </div>

    <script>
        function showFullInfo(device_id) {
            // Fetch device full information using AJAX
            fetch('?device_id=' + device_id)
                .then(response => response.json())
                .then(data => {
                    let deviceInfo = document.getElementById('deviceInfo');
                    deviceInfo.innerHTML = '';
                    for (let table in data) {
                        let section = document.createElement('div');
                        section.innerHTML = `<h3>${table.charAt(0).toUpperCase() + table.slice(1)}</h3>`;
                        let details = '<ul>';
                        for (let key in data[table]) {
                            details += `<li><strong>${key}:</strong> ${data[table][key]}</li>`;
                        }
                        details += '</ul>';
                        section.innerHTML += details;
                        deviceInfo.appendChild(section);
                    }
                    document.getElementById('deviceModal').style.display = 'flex';
                })
                .catch(error => {
                    console.error('Error fetching device info:', error);
                });
        }

        function closeModal() {
            document.getElementById('deviceModal').style.display = 'none';
        }
    </script>


</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
