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

// Handle form submission to update status
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["action"]) && isset($_POST["id"])) {
    $conn = new mysqli("localhost", "root", "", "bloodbank");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    $id = $_POST["id"];
    $status = $_POST["action"];

    $stmt = $conn->prepare("UPDATE donation_history SET status=? WHERE id=?");
    $stmt->bind_param("si", $status, $id);
    $stmt->execute();
    $stmt->close();
    $conn->close();

    // Redirect to avoid form resubmission
    header("Location: admin_donations.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Blood Donation Details</title>

    <!-- Google Fonts for Material Symbols -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

    <!-- Custom Admin CSS -->
    <link rel="stylesheet" href="admin_donations.css" />

    <style>
        .btn-approve {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
        }

        .btn-reject {
            background-color: #f44336;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
            margin-left: 5px;
        }

        .btn-approve:hover,
        .btn-reject:hover {
            opacity: 0.9;
        }
    </style>
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

    <!-- Sidebar Menu -->
    <ul class="menu-content">
        <li><a href="admin_menu.php"><div class="menu-icon"><i class="fas fa-home"></i></div><span>Home</span></a></li>
        <li><a href="donor_list.php"><div class="menu-icon"><i class="fas fa-user"></i></div><span>User</span></a></li>
        <li><a href="admin_donations.php" class="active"><div class="menu-icon"><i class="far fa-heart"></i></div><span>Donations</span></a></li>
        <li><a href="admin_bloodrequest.php"><div class="menu-icon"><i class="fas fa-sync-alt"></i></div><span>Blood Request</span></a></li>
        <li><a href="admin_stock.php"><div class="menu-icon"><i class="fas fa-ambulance"></i></div><span>Blood Stock</span></a></li>
    </ul>

    <!-- Main Content -->
    <div class="content">
        <h2 class="page-title">Blood Donation Details</h2>
        <table>
            <thead>
                <tr>
                    <th>Donor Name</th>
                    <th>Disease</th>
                    <th>Age</th>
                    <th>Blood Group</th>
                    <th>Unit</th>
                    <th>Request Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Fetch and display donation history
                $conn = new mysqli("localhost", "root", "", "bloodbank");

                if ($conn->connect_error) {
                    die("Connection failed: " . $conn->connect_error);
                }

                $sql = "SELECT * FROM donation_history ORDER BY id DESC";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['donor_name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['disease']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['donor_age']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['blood_group']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['unit']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['date_time']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['status']) . "</td>";
                        echo "<td>
                                <form method='POST' action='admin_donations.php' style='display:inline;'>
                                    <input type='hidden' name='id' value='" . $row['id'] . "'>
                                    <button type='submit' name='action' value='Approved' class='btn-approve'>Approve</button>
                                    <button type='submit' name='action' value='Rejected' class='btn-reject'>Reject</button>
                                </form>
                              </td>";
                        echo "</tr>";
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
            window.location.href = "admin_in.php";
        }
    </script>
</body>

</html>
