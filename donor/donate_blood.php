<?php
session_start();

// Database configuration
$host = "localhost";
$user = "root";
$password = "";
$db = "bloodbank";

// Create connection
$conn = new mysqli($host, $user, $password, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle form submission
if (isset($_POST['sub'])) {
    $unit = (int) $_POST['unit'];
    $disease = $_POST['dise'];
    $donor_age = (int) $_POST['donor_age'];
    $blood_group = $_POST['blood_group'];

    // Get logged-in username
    $username = $_SESSION['username'] ?? $_COOKIE['loggedInUser'] ?? 'Unknown';
    $donor_name = $username; // Assuming donor_name is same as username

    $status = "Pending";
    //$date_time = date("Y-m-d H:i:s");

    // Prepare and execute insert query
    $stmt = $conn->prepare("INSERT INTO donation_history (donor_name, donor_username, donor_age, disease, blood_group, unit, status) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssissis", $donor_name, $username, $donor_age, $disease, $blood_group, $unit, $status);

    if ($stmt->execute()) {
        echo "<script>alert('Donation recorded successfully!');</script>";
    } else {
        echo "<script>alert('Error saving donation: " . $stmt->error . "');</script>";
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Donate Blood</title>

    <!-- Google Fonts for Material Symbols -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" />

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />

    <!-- Custom CSS -->
    <link rel="stylesheet" href="donate blood.css" />
</head>

<body>

    <!-- Top Navigation Bar -->
    <div class="top-nav">
        <div class="logo">BB</div>
        <div class="dropdown">
            <button class="dropbtn">
                <span class="material-symbols-outlined">person</span>
                <span id="username-display">User</span>
                <span class="material-symbols-outlined">arrow_drop_down</span>
            </button>
            <div class="dropdown-content">
                <a href="donor_in.php" onclick="logout()">
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

    <!-- Main Content -->
    <div class="main">
        <div class="conten">
            <div class="card-cont">
                <div class="card-heading">
                    <h1>Donate Blood</h1>
                </div>
                <div class="card-body">

                    <form method="post" action="">
                        <div class="form-body">
                            <div class="in">
                                <label for="unit" style="font-size: 18px">Unit</label>
                                <input type="number" name="unit" required id="unit" placeholder="Number of Units"
                                    class="in-sty">
                            </div>

                            <div class="in">
                                <label for="dise" style="font-size: 18px">Disease (if any)</label>
                                <input type="text" name="dise" required value="Nothing" class="in-sty">
                            </div>

                            <div class="in">
                                <label for="donor_age" style="font-size: 18px">Age</label>
                                <input type="number" name="donor_age" required placeholder="Age" class="in-sty">
                            </div>

                            <div class="in">
                                <label for="blood_group" style="font-size: 18px">Blood Group</label>
                                <select name="blood_group" class="in-sty" required>
                                    <option value="" disabled selected>Select Blood Group</option>
                                    <option value="A+">A+</option>
                                    <option value="A-">A-</option>
                                    <option value="B+">B+</option>
                                    <option value="B-">B-</option>
                                    <option value="AB+">AB+</option>
                                    <option value="AB-">AB-</option>
                                    <option value="O+">O+</option>
                                    <option value="O-">O-</option>
                                </select>
                            </div>

                            <div style="align-items: center; margin-top: 20px;">
                                <input class="btn btn--radius-2 btn-danger" type="submit" value="Donate" name="sub"
                                    style="width: 94px; height: 47px;">
                            </div>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    <script>
        const phpUsername = "<?php echo $_SESSION['username'] ?? $_COOKIE['loggedInUser'] ?? 'User'; ?>";
        document.addEventListener("DOMContentLoaded", function () {
            if (phpUsername) {
                document.getElementById("username-display").textContent = phpUsername;
            }
        });

        function logout() {
            localStorage.removeItem("loggedInUser");
            window.location.href = "donor_in.php";
        }
    </script>
</body>
</html>
