<?php
session_start();

// If user is not logged in, redirect to login page
if (!isset($_SESSION['username'])) {
    header("Location: donor_in.php");
    exit();
}

$username = $_SESSION['username'];
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Donor Menu</title>

    <!-- Google Fonts for Material Symbols -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

    <!-- Custom CSS -->
    <link rel="stylesheet" href="donor_menu.css" />
</head>

<body>
    <!-- Background Image -->
    <div class="background-image"></div>
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
                <a href="donor_in.php">
                    <span class="material-symbols-outlined">logout</span> Logout
                </a>
            </div>
        </div>
    </div>

    <!-- Sidebar Menu -->
    <ul class="menu-content">
        <li><a href="donor_menu.php">
                <div class="menu-icon"><i class="fas fa-home"></i></div><span>Home</span>
            </a></li>
        <li><a href="donate_blood.php">
                <div class="menu-icon"><i class="fas fa-hand-holding-heart"></i></div><span>Donate Blood</span>
            </a></li>
        <li><a href="donation_history.php">
                <div class="menu-icon"><i class="fas fa-history"></i></div><span>Donation History</span>
            </a></li>
        <li><a href="blood_request.php">
                <div class="menu-icon"><i class="fas fa-tint"></i></div><span>Blood Request</span>
            </a></li>
        <li><a href="request_history.php">
                <div class="menu-icon"><i class="fas fa-clock"></i></div><span>Request History</span>
            </a></li>
    </ul>

</body>

</html>
