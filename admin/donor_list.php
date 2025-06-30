<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['username'])) {
    header("Location: admin_in.php");
    exit();
}

$username = $_SESSION['username']; // Get admin name
// Connect to the database
$host = "localhost";
$db_user = "root";
$db_pass = ""; // Replace with your DB password
$db_name = "bloodbank"; // Replace with your DB name

$conn = new mysqli($host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$adminResult = $conn->query("SELECT name FROM admins WHERE email = '$username'");
$adminName = ($adminResult && $adminResult->num_rows > 0) ? $adminResult->fetch_assoc()['name'] : 'Admin';

$sql = "SELECT * FROM donors";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>User List</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="donor_list.css" />
</head>

<body>
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

    <ul class="menu-content">
        <li><a href="admin_menu.php">
                <div class="menu-icon"><i class="fas fa-home"></i></div><span>Home</span>
            </a></li>
        <li><a href="donor_list.php">
                <div class="menu-icon"><i class="fas fa-user"></i></div><span>User</span>
            </a></li>
        <li><a href="admin_donations.php">
                <div class="menu-icon"><i class="far fa-heart"></i></div><span>Donations</span>
            </a></li>
        <li><a href="admin_bloodrequest.php">
                <div class="menu-icon"><i class="fas fa-sync-alt"></i></div><span>Blood Request</span>
            </a></li>
        <li><a href="admin_stock.php">
                <div class="menu-icon"><i class="fas fa-ambulance"></i></div><span>Blood Stock</span>
            </a></li>
    </ul>

    <div class="content">
        <h2 class="page-title">User List</h2>
        <table>
            <thead>
                <tr>
                    <th>S.NO</th>
                    <th>Name</th>
                    <th>Profile (Email)</th>
                    <th>Blood Group</th>
                    <th>Address</th>
                    <th>Mobile</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    $sn = 1;
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>
                            <td>" . $sn++ . "</td>
                            <td>" . htmlspecialchars($row['name']) . "</td>
                            <td>" . htmlspecialchars($row['email']) . "</td>
                            <td>" . htmlspecialchars($row['blood_group']) . "</td>
                            <td>" . htmlspecialchars($row['address']) . "</td>
                            <td>" . htmlspecialchars($row['phone_number']) . "</td>
                            <td>
                                <button class='edit-btn'>Edit</button>
                                <button class='delete-btn'>Delete</button>
                            </td>
                        </tr>";
                    }
                } else {
                    echo "<tr><td colspan='7' style='text-align: center; padding: 20px; color: #ccc;'>No entries found</td></tr>";
                }
                $conn->close();
                ?>
            </tbody>
        </table>
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