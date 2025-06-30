<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: donor_in.php");
    exit();
}

// Connect to the database
$conn = new mysqli("localhost", "root", "", "bloodbank");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$username = $_SESSION['username'];

// Fetch request history of the logged-in user
$stmt = $conn->prepare("SELECT id, patient_name, blood_group, units, created_at, status FROM blood_request WHERE username = ? ORDER BY created_at DESC");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Request History</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"/>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
    <link rel="stylesheet" href="request_history.css"/>
</head>
<body>

<div class="top-nav">
    <div class="logo">BB</div>
    <div class="dropdown">
        <button class="dropbtn">
            <i class="fas fa-user"></i>
            <span><?= htmlspecialchars($username) ?></span>
            <i class="fas fa-caret-down"></i>
        </button>
        <div class="dropdown-content">
            <a href="donor_in.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
</div>

<ul class="menu-content">
    <li><a href="donor_menu.php"><div class="menu-icon"><i class="fas fa-home"></i></div><span>Home</span></a></li>
    <li><a href="donate_blood.php"><div class="menu-icon"><i class="fas fa-hand-holding-heart"></i></div><span>Donate Blood</span></a></li>
    <li><a href="donation_history.php"><div class="menu-icon"><i class="fas fa-history"></i></div><span>Donation History</span></a></li>
    <li><a href="blood_request.php"><div class="menu-icon"><i class="fas fa-tint"></i></div><span>Blood Request</span></a></li>
    <li><a href="request_history.php"><div class="menu-icon"><i class="fas fa-clock"></i></div><span>Request History</span></a></li>
</ul>

<div class="cont-card">
    <h2 class="text-center mb-4">YOUR REQUEST HISTORY</h2>
    <table class="table table-bordered table-striped text-center align-middle">
        <thead>
            <tr>
                <th>ID</th>
                <th>Patient Name</th>
                <th>Blood Group</th>
                <th>Unit</th>
                <th>Request Date</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['patient_name']) ?></td>
                    <td><?= htmlspecialchars($row['blood_group']) ?></td>
                    <td><?= htmlspecialchars($row['units']) ?></td>
                    <td><?= date("d M Y, h:i A", strtotime($row['created_at'])) ?></td>
                    <td>
                        <span class="status status-<?= strtolower($row['status']) ?>">
                            <?= htmlspecialchars($row['status']) ?>
                        </span>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="6">No request history found.</td></tr>
        <?php endif; ?>
        </tbody>
    </table>
</div>

</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
