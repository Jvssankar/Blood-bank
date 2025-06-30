<?php
session_start();

// Redirect to login if not logged in
if (!isset($_SESSION['username'])) {
    header("Location: admin_in.php");
    exit();
}

$username = $_SESSION['username'];

// Connect to the database
$conn = new mysqli("localhost", "root", "", "bloodbank");

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all blood requests
$sql = "SELECT * FROM blood_request";
$result = $conn->query($sql);

// Handle Approve and Reject actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = $_GET['id'];
    $action = $_GET['action'];

    // Reconnect to perform actions
    $conn = new mysqli("localhost", "root", "", "bloodbank");
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Update status
    if ($action == 'approve') {
        $updateStatus = "UPDATE blood_request SET status='Approved' WHERE id=?";
    } elseif ($action == 'reject') {
        $updateStatus = "UPDATE blood_request SET status='Rejected' WHERE id=?";
    }

    $stmt = $conn->prepare($updateStatus);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: admin_bloodrequest.php");
        exit();
    } else {
        echo "<script>alert('Error: " . $stmt->error . "');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Blood Requests Details</title>

    <!-- Google Fonts for Material Symbols -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

    <!-- Custom Admin CSS -->
    <link rel="stylesheet" href="admin_bloodrequest.css" />
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
        <li><a href="admin_menu.php">
                <div class="menu-icon"><i class="fas fa-home"></i></div><span>Home</span>
            </a></li>
        <li><a href="donor_list.php">
                <div class="menu-icon"><i class="fas fa-user"></i></div><span>User</span>
            </a></li>
        <li><a href="admin_donations.php">
                <div class="menu-icon"><i class="far fa-heart"></i></div><span>Donations</span>
            </a></li>
        <li><a href="admin_bloodrequest.php" class="active">
                <div class="menu-icon"><i class="fas fa-sync-alt"></i></div><span>Blood Request</span>
            </a></li>
        <li><a href="admin_stock.php">
                <div class="menu-icon"><i class="fas fa-ambulance"></i></div><span>Blood Stock</span>
            </a></li>
    </ul>

    <!-- Main Content -->
    <div class="content">
        <h2 class="page-title">Blood Request Details</h2>
        <table>
            <thead>
                <tr>
                    <th>Patient Name</th>
                    <th>Age</th>
                    <th>Reason</th>
                    <th>Blood Group</th>
                    <th>Unit(s)</th>
                    <th>Request Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row["patient_name"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["patient_age"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["reason"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["blood_group"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["units"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["created_at"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["status"]) . "</td>";
                        echo "<td>
                                <a href='admin_bloodrequest.php?action=approve&id=" . $row["id"] . "'>Approve</a> | 
                                <a href='admin_bloodrequest.php?action=reject&id=" . $row["id"] . "'>Reject</a>
                              </td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='8' style='text-align: center; padding: 20px; color: #ccc;'>No blood requests found.</td></tr>";
                }

                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
</body>

</html>
