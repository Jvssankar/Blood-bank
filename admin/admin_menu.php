<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['username'])) {
    header("Location: admin_in.php");
    exit();
}

$username = $_SESSION['username']; // Get admin name

// Database connection details
$servername = "localhost";
$db_username = "root";
$password = "";
$dbname = "bloodbank";

// Create a connection
$conn = new mysqli($servername, $db_username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$adminResult = $conn->query("SELECT name FROM admins WHERE email = '$username'");
$adminName = ($adminResult && $adminResult->num_rows > 0) ? $adminResult->fetch_assoc()['name'] : 'Admin';

// Fetch total blood stock
$stockResult = $conn->query("SELECT SUM(unit) AS total_stock FROM blood_stock");
$totalStock = ($stockResult && $stockResult->num_rows > 0) ? $stockResult->fetch_assoc()['total_stock'] : 0;

// Fetch total blood requests
$requestResult = $conn->query("SELECT COUNT(*) AS total_requests FROM blood_request");
$totalRequests = ($requestResult && $requestResult->num_rows > 0) ? $requestResult->fetch_assoc()['total_requests'] : 0;

// Fetch total donation requests
$donationResult = $conn->query("SELECT COUNT(*) AS total_donations FROM donation_history");
$totalDonations = ($donationResult && $donationResult->num_rows > 0) ? $donationResult->fetch_assoc()['total_donations'] : 0;

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Menu</title>

    <!-- Google Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

    <!-- Custom CSS -->
    <link rel="stylesheet" href="admin_menu.css" />
</head>

<body>

    <!-- Top Navigation Bar -->
    <div class="top-nav">
        <div class="logo">BB</div>
        <div class="dropdown">
            <button class="dropbtn">
                <span class="material-symbols-outlined">person</span>
                <span id="username-display"><?php echo htmlspecialchars($username); ?></span>
                <span class="material-symbols-outlined">arrow_drop_down</span>
            </button>
            <div class="dropdown-content">
                <a href="admin_in.php">
                    <span class="material-symbols-outlined">logout</span> Logout
                </a>
            </div>
        </div>
    </div>

    <!-- Sidebar Menu -->
    <ul class="menu-content">
        <li><a href="admin_menu.php"><div class="menu-icon"><i class="fas fa-home"></i></div><span>Home</span></a></li>
        <li><a href="donor_list.php"><div class="menu-icon"><i class="fas fa-user"></i></div><span>User</span></a></li>
        <li><a href="admin_donations.php"><div class="menu-icon"><i class="far fa-heart"></i></div><span>Donations</span></a></li>
        <li><a href="admin_bloodrequest.php"><div class="menu-icon"><i class="fas fa-sync-alt"></i></div><span>Blood Request</span></a></li>
        <li><a href="admin_stock.php"><div class="menu-icon"><i class="fas fa-ambulance"></i></div><span>Blood Stock</span></a></li>
    </ul>

    <!-- Main Content Section -->
    <div class="main-content">
        <h2>Admin Dashboard</h2>
        <div class="dashboard-stats">
            <div class="stat-card">
                <h4>Total Blood Stock</h4>
                <p><?php echo $totalStock; ?> Units</p>
            </div>
            <div class="stat-card">
                <h4>Total Blood Requests</h4>
                <p><?php echo $totalRequests; ?> Requests</p>
            </div>
            <div class="stat-card">
                <h4>Total Donation Requests</h4>
                <p><?php echo $totalDonations; ?> Requests</p>
            </div>
        </div>
    </div>

</body>

</html>
