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

// Fetch full info for each device using joins
function get_device_full_info($device_id) {
    global $conn;

    // Query to fetch all details related to the device
    $query = "SELECT 
        d.device_id, d.brand, d.model_name, 
        b.capacity, b.type as battery_type, b.removable, 
        c.camera, c.resolution, c.autofocus, c.ois, 
        de.height, de.weight, de.build_material, de.colours, 
        di.display_type, di.screen_size, di.resolution as display_resolution, 
        ge.launch_date, ge.operating_system, 
        ks.RAM, ks.Processor, ks.Rear_Camera, ks.Battery as key_battery, 
        mu.fm_radio, mu.stereo_speakers 
    FROM device d
    LEFT JOIN battery b ON d.device_id = b.device_id
    LEFT JOIN camera c ON d.device_id = c.device_id
    LEFT JOIN design de ON d.device_id = de.device_id
    LEFT JOIN display di ON d.device_id = di.device_id
    LEFT JOIN general ge ON d.device_id = ge.device_id
    LEFT JOIN key_specs ks ON d.device_id = ks.device_id
    LEFT JOIN multimedia mu ON d.device_id = mu.device_id
    WHERE d.device_id = $device_id";

    return $conn->query($query)->fetch_assoc();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Device Details</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            flex-direction: column;
        }
        .device-box {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
        }
        .device-card {
            background-color: #fff;
            border: 2px solid #ddd;
            border-radius: 10px;
            padding: 20px;
            width: 250px;
            height: 300px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            opacity: 0;
            animation: fadeIn 0.5s forwards;
            cursor: pointer;
            transition: transform 0.3s ease;
        }
        .device-card:hover {
            transform: translateY(-10px);
        }
        .device-card h3 {
            font-size: 20px;
            margin-bottom: 10px;
        }
        .device-card p {
            font-size: 14px;
            color: #555;
            margin: 5px 0;
        }
        .device-card button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 14px;
            transition: background-color 0.3s ease;
        }
        .device-card button:hover {
            background-color: #45a049;
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .full-info {
            display: none;
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            width: 80%;
            max-width: 600px;
            z-index: 100;
        }
        .full-info button {
            background-color: #ff0000;
            margin-top: 20px;
        }
    </style>
</head>
<body>

    <h1>Device Details</h1>
    <div class="device-box">
        <?php while ($device = $devices_result->fetch_assoc()): ?>
            <div class="device-card" onclick="showFullInfo(<?= $device['device_id'] ?>)">
                <h3><?= $device['model_name'] ?></h3>
                <p><strong>Brand:</strong> <?= $device['brand'] ?></p>
                <button>Full Info</button>
            </div>
        <?php endwhile; ?>
    </div>

    <div id="full-info-modal" class="full-info">
        <h2 id="device-name"></h2>
        <p><strong>Brand:</strong> <span id="device-brand"></span></p>
        <p><strong>Launch Date:</strong> <span id="launch-date"></span></p>
        <p><strong>Operating System:</strong> <span id="os"></span></p>
        <p><strong>RAM:</strong> <span id="ram"></span></p>
        <p><strong>Processor:</strong> <span id="processor"></span></p>
        <p><strong>Battery Capacity:</strong> <span id="battery-capacity"></span></p>
        <p><strong>Camera:</strong> <span id="camera"></span></p>
        <button onclick="closeModal()">Close</button>
    </div>

    <script>
        function showFullInfo(deviceId) {
            fetch('get_device_full_info.php?device_id=' + deviceId)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('device-name').innerText = data.model_name;
                    document.getElementById('device-brand').innerText = data.brand;
                    document.getElementById('launch-date').innerText = data.launch_date;
                    document.getElementById('os').innerText = data.operating_system;
                    document.getElementById('ram').innerText = data.RAM;
                    document.getElementById('processor').innerText = data.Processor;
                    document.getElementById('battery-capacity').innerText = data.capacity;
                    document.getElementById('camera').innerText = data.camera;
                    document.getElementById('full-info-modal').style.display = 'block';
                })
                .catch(error => console.error('Error fetching full device info:', error));
        }

        function closeModal() {
            document.getElementById('full-info-modal').style.display = 'none';
        }
    </script>

</body>
</html>

<?php
$conn->close();
