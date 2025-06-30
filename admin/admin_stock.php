<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['username'])) {
    header("Location: admin_in.php");
    exit();
}

$username = $_SESSION['username']; // Get admin name

// Database connection
$host = "localhost";
$user = "root";
$password = "";
$db = "bloodbank"; // Change this to your DB name

$conn = new mysqli($host, $user, $password, $db);
if ($conn->connect_error) {
    die("Connection Failed: " . $conn->connect_error);
}

$adminResult = $conn->query("SELECT name FROM admins WHERE email = '$username'");
$adminName = ($adminResult && $adminResult->num_rows > 0) ? $adminResult->fetch_assoc()['name'] : 'Admin';

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update'])) {
    $bgroup = $_POST['bgroup'];
    $unit = intval($_POST['unit']);

    $sql = "UPDATE blood_stock SET unit = unit + $unit WHERE blood_group = '$bgroup'";
    $conn->query($sql);
}

// Fetch updated blood stock data
$result = $conn->query("SELECT * FROM blood_stock");
$blood_stock = [];
while ($row = $result->fetch_assoc()) {
    $blood_stock[$row['blood_group']] = $row['unit'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Blood Inventory</title>

    <!-- Google Fonts for Material Symbols -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined"/>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <link href="admin_stock.css" rel="stylesheet" />
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
                <a href="admin_in.php" onclick="logout()">
                    <span class="material-symbols-outlined">logout</span> Logout
                </a>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <ul class="menu-content">
        <li><a href="admin_menu.php"><div class="menu-icon"><i class="fas fa-home"></i></div><span>Home</span></a></li>
        <li><a href="donor_list.php"><div class="menu-icon"><i class="fas fa-user"></i></div><span>User</span></a></li>
        <li><a href="admin_donations.php"><div class="menu-icon"><i class="far fa-heart"></i></div><span>Donations</span></a></li>
        <li><a href="admin_bloodrequest.php"><div class="menu-icon"><i class="fas fa-sync-alt"></i></div><span>Blood Request</span></a></li>
        <li><a href="admin_stock.php"><div class="menu-icon"><i class="fas fa-ambulance"></i></div><span>Blood Stock</span></a></li>
    </ul>

    <!-- Main Content -->
    <div class="main">
        <div class="cont-box">
            <div class="row">
                <?php
                $groups = ['A+', 'B+', 'AB+', 'O+', 'A-', 'B-', 'AB-', 'O-'];
                foreach ($groups as $group) {
                    $val = isset($blood_stock[$group]) ? $blood_stock[$group] : 0;
                    echo "
                        <div class='col'>
                            <div class='card-border'>
                                <div class='blood'><h2>$group <i class='fas fa-tint' style='color:red;'></i></h2></div>
                                <div class='val'>$val</div>
                            </div>
                        </div>
                    ";
                }
                ?>
            </div>
        </div>

        <h2 >Update Blood Unit</h2>
        <form method="post">
            <select name="bgroup" required>
                <option value="" selected disabled>Select Blood Group</option>
                <?php foreach ($groups as $group) echo "<option value=\"$group\">$group</option>"; ?>
            </select>
            <input type="number" name="unit" placeholder="Unit" required>
            <input type="submit" name="update" value="Update">
        </form>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            let username = localStorage.getItem("loggedInUser");
            if (username) {
                document.getElementById("username-display").textContent = username;
            }
        });

        function logout() {
            localStorage.removeItem("loggedInUser");
            window.location.href = "admin_in.html";
        }
    </script>
</body>
</html>
