<?php
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: admin_login.php");
    exit();
}

// Get admin username
$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Insert Data</title>
    <style>
        /* Global Styles */
        body {
            font-family: 'Roboto', Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f7f9fc;
            color: #333;
            line-height: 1.6;
        }

        /* Header Section */
        h1 {
            text-align: center;
            margin-top: 20px;
            font-size: 2.5rem;
            color: #0a3d62;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        /* Welcome Message */
        .welcome-message {
            text-align: center;
            font-size: 1.25rem;
            color: #555;
            margin-bottom: 40px;
            font-weight: 400;
        }

        /* Table Layout */
        table {
            width: 80%;
            margin: 0 auto;
            border-collapse: collapse;
            margin-bottom: 50px;
        }

        th {
            padding: 12px 20px;
            background-color: #3498db;
            color: #fff;
            text-align: left;
            font-weight: bold;
        }

        td {
            padding: 12px 20px;
            text-align: center;
            vertical-align: middle;
            border-bottom: 1px solid #ddd;
        }

        /* Button Styles */
        button {
            padding: 12px 30px;
            font-size: 1rem;
            font-weight: 600;
            color: #fff;
            background: linear-gradient(to right, #3498db, #2c82c9);
            border: none;
            border-radius: 8px;
            cursor: pointer;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease-in-out;
        }

        button:hover {
            background: linear-gradient(to right, #2c82c9, #3498db);
            transform: translateY(-3px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
        }

        button:active {
            transform: translateY(2px);
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        button:focus {
            outline: none;
            box-shadow: 0 0 8px rgba(52, 152, 219, 0.8);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            h1 {
                font-size: 2rem;
            }

            .welcome-message {
                font-size: 1rem;
            }

            table {
                width: 100%;
            }

            button {
                font-size: 0.9rem;
                padding: 10px 20px;
            }
        }
    </style>
</head>
<body>

<h1>Insert Data Into Tables</h1>

<div class="welcome-message">
    <p>Welcome, Admin: <?php echo $username; ?>!</p>
</div>

<!-- Table Layout for Buttons -->
<table>
    <thead>
        <tr>
            <th colspan="4">Device Management</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><button onclick="window.location.href='insert_device.php'">Insert Device</button></td>
            <td><button onclick="window.location.href='update_device.php'">Update Device</button></td>
            <td><button onclick="window.location.href='show_device.php'">Show Device</button></td>
            <td><button onclick="window.location.href='remove_device.php'">Remove Device</button></td>
        </tr>
    </tbody>
    <thead>
        <tr>
            <th colspan="4">Battery Management</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><button onclick="window.location.href='insert_battery.php'">Insert Battery</button></td>
            <td><button onclick="window.location.href='update_battery.php'">Update Battery</button></td>
            <td><button onclick="window.location.href='show_battery.php'">Show Battery</button></td>
            <td><button onclick="window.location.href='remove_battery.php'">Remove Battery</button></td>
        </tr>
    </tbody>
    <thead>
        <tr>
            <th colspan="4">Camera Management</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><button onclick="window.location.href='insert_camera.php'">Insert Camera</button></td>
            <td><button onclick="window.location.href='update_camera.php'">Update Camera</button></td>
            <td><button onclick="window.location.href='show_camera.php'">Show Camera</button></td>
            <td><button onclick="window.location.href='remove_camera.php'">Remove Camera</button></td>
        </tr>
    </tbody>
    <thead>
        <tr>
            <th colspan="4">Design Management</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><button onclick="window.location.href='insert_design.php'">Insert Design</button></td>
            <td><button onclick="window.location.href='update_design.php'">Update Design</button></td>
            <td><button onclick="window.location.href='show_design.php'">Show Design</button></td>
            <td><button onclick="window.location.href='remove_design.php'">Remove Design</button></td>
        </tr>
    </tbody>
    <thead>
        <tr>
            <th colspan="4">Display Management</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><button onclick="window.location.href='insert_display.php'">Insert Display</button></td>
            <td><button onclick="window.location.href='update_display.php'">Update Display</button></td>
            <td><button onclick="window.location.href='show_display.php'">Show Display</button></td>
            <td><button onclick="window.location.href='remove_display.php'">Remove Display</button></td>
        </tr>
    </tbody>
    <thead>
        <tr>
            <th colspan="4">General Management</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><button onclick="window.location.href='insert_general.php'">Insert General</button></td>
            <td><button onclick="window.location.href='update_general.php'">Update General</button></td>
            <td><button onclick="window.location.href='show_general.php'">Show General</button></td>
            <td><button onclick="window.location.href='remove_general.php'">Remove General</button></td>
        </tr>
    </tbody>
    <thead>
        <tr>
            <th colspan="4">Key Specs Management</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><button onclick="window.location.href='insert_key_specs.php'">Insert Key Specs</button></td>
            <td><button onclick="window.location.href='update_key_specs.php'">Update Key Specs</button></td>
            <td><button onclick="window.location.href='show_key_specs.php'">Show Key Specs</button></td>
            <td><button onclick="window.location.href='remove_key_specs.php'">Remove Key Specs</button></td>
        </tr>
    </tbody>
    <thead>
        <tr>
            <th colspan="4">Multimedia Management</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><button onclick="window.location.href='insert_multimedia.php'">Insert Multimedia</button></td>
            <td><button onclick="window.location.href='update_multimedia.php'">Update Multimedia</button></td>
            <td><button onclick="window.location.href='show_multimedia.php'">Show Multimedia</button></td>
            <td><button onclick="window.location.href='remove_multimedia.php'">Remove Multimedia</button></td>
        </tr>
    </tbody>
    <thead>
        <tr>
            <th colspan="4">Network Connectivity Management</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><button onclick="window.location.href='insert_network_connectivity.php'">Insert Network Connectivity</button></td>
            <td><button onclick="window.location.href='update_network_connectivity.php'">Update Network Connectivity</button></td>
            <td><button onclick="window.location.href='show_network_connectivity.php'">Show Network Connectivity</button></td>
            <td><button onclick="window.location.href='remove_network_connectivity.php'">Remove Network Connectivity</button></td>
        </tr>
    </tbody>
    <thead>
        <tr>
            <th colspan="4">Performance Management</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><button onclick="window.location.href='insert_performance.php'">Insert Performance</button></td>
            <td><button onclick="window.location.href='update_performance.php'">Update Performance</button></td>
            <td><button onclick="window.location.href='show_performance.php'">Show Performance</button></td>
            <td><button onclick="window.location.href='remove_performance.php'">Remove Performance</button></td>
        </tr>
    </tbody><thead>
        <tr>
            <th colspan="4">Sensors Management</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><button onclick="window.location.href='insert_sensors.php'">Insert Sensors</button></td>
            <td><button onclick="window.location.href='update_sensors.php'">Update Sensors</button></td>
            <td><button onclick="window.location.href='show_sensors.php'">Show Sensors</button></td>
            <td><button onclick="window.location.href='remove_sensors.php'">Remove Sensors</button></td>
        </tr>
    </tbody><thead>
        <tr>
            <th colspan="4">Storage Management</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><button onclick="window.location.href='insert_storage.php'">Insert Storage</button></td>
            <td><button onclick="window.location.href='update_storage.php'">Update Storage</button></td>
            <td><button onclick="window.location.href='show_storage.php'">Show Storage</button></td>
            <td><button onclick="window.location.href='remove_storage.php'">Remove Storage</button></td>
        </tr>
    </tbody>
    <tr>
            <th colspan="4">Image Management</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td><button onclick="window.location.href='insert_image.php'">Insert Image</button></td>
            <td><button onclick="window.location.href='update_image.php'">Update Image</button></td>
            <td><button onclick="window.location.href='show_image.php'">Show Image</button></td>
            <td><button onclick="window.location.href='remove_image.php'">Remove Image</button></td>
        </tr>
    </tbody>
    
</table>

</body>
</html>
