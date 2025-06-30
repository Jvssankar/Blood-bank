<?php
session_start();

// Redirect to login if session is not set
if (!isset($_SESSION['username'])) {
    header("Location: donor_in.php");
    exit();
}

$display_name = htmlspecialchars($_SESSION['username']);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Establishing a connection to the database
    $conn = new mysqli("localhost", "root", "", "bloodbank");

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Collect form data
    $p_name = $_POST['p_name'];
    $p_age = $_POST['p_age'];
    $reason = $_POST['reason'];
    $p_bgroup = $_POST['p_bgroup'];
    $p_unit = $_POST['p_unit'];
    $status = "Pending";

    // Check if all form fields are filled
    if (empty($p_name) || empty($p_age) || empty($reason) || empty($p_bgroup) || empty($p_unit)) {
        echo "<script>alert('Please fill in all fields.');</script>";
        exit();
    }

    // Prepare and bind the SQL statement
    $username = $_SESSION['username']; // Add this line

    $stmt = $conn->prepare("INSERT INTO blood_request (username, patient_name, patient_age, reason, blood_group, units, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssissis", $username, $p_name, $p_age, $reason, $p_bgroup, $p_unit, $status);

    // Execute the statement and check for errors
    if ($stmt->execute()) {
        header("Location: request_history.php");
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
    <title>Blood Request</title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <link rel="stylesheet" href="blood_request.css" />
</head>
<body>

<!-- Top Navigation Bar -->
<div class="top-nav">
    <div class="logo">BB</div>
    <div class="dropdown">
        <button class="dropbtn">
            <span class="material-symbols-outlined">person</span>
            <span id="username-display"><?php echo $display_name; ?></span>
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
        <div class="menu-icon"><i class="fas fa-home"></i></div><span>Home</span></a></li>
    <li><a href="donate_blood.php">
        <div class="menu-icon"><i class="fas fa-hand-holding-heart"></i></div><span>Donate Blood</span></a></li>
    <li><a href="donation_history.php">
        <div class="menu-icon"><i class="fas fa-history"></i></div><span>Donation History</span></a></li>
    <li><a href="blood_request.php">
        <div class="menu-icon"><i class="fas fa-tint"></i></div><span>Blood Request</span></a></li>
    <li><a href="request_history.php">
        <div class="menu-icon"><i class="fas fa-clock"></i></div><span>Request History</span></a></li>
</ul>

<!-- Form Section -->
<div class="main">
    <div class="content">
        <div class="card-cont">
            <h1>Blood Request</h1>
            <form method="post" action="blood_request.php">
                <div class="form-body">
                    <div class="in">
                        <label for="p_name">Patient Name</label>
                        <input type="text" name="p_name" placeholder="Name" required />
                    </div>
                    <div class="in">
                        <label for="p_age">Patient Age</label>
                        <input type="number" name="p_age" placeholder="Age" required />
                    </div>
                    <div class="in">
                        <label for="reason">Reason</label>
                        <input type="text" name="reason" placeholder="Reason" required />
                    </div>
                    <div class="in">
                        <label for="p_bgroup">Blood Group</label>
                        <select name="p_bgroup" required>
                            <option value="">Select Blood Group</option>
                            <option value="A+">A+</option>
                            <option value="B+">B+</option>
                            <option value="AB+">AB+</option>
                            <option value="O+">O+</option>
                            <option value="A-">A-</option>
                            <option value="B-">B-</option>
                            <option value="AB-">AB-</option>
                            <option value="O-">O-</option>
                        </select>
                    </div>
                    <div class="in">
                        <label for="p_unit">Unit</label>
                        <input type="number" name="p_unit" placeholder="Number of Units" required />
                    </div>
                    <div class="in">
                        <input class="btn" type="submit" value="Request" />
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

</body>
</html>
