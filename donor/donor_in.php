<?php
session_start();

$host = "localhost";
$username = "root";
$password = "";
$database = "bloodbank";

$conn = new mysqli($host, $username, $password, $database);

$loginMessage = "";
$signupMessage = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['login'])) {
        $email = $_POST['logemail'];
        $pass = $_POST['logpass'];

        $stmt = $conn->prepare("SELECT name, password FROM donors WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->bind_result($name, $hashedPass);
            $stmt->fetch();
            if ($pass === $hashedPass) {
                $_SESSION['username'] = $name;
                header("Location: donor_menu.php");
                exit();
            } else {
                $loginMessage = "Invalid password.";
            }
        } else {
            $loginMessage = "Email not found.";
        }
        $stmt->close();
    }

    if (isset($_POST['signup'])) {
        $name = $_POST['signupName'];
        $email = $_POST['signupEmail'];
        $password = $_POST['signupPass'];
        $blood_group = $_POST['signupBlood'];
        $address = $_POST['signupAddress'];
        $phone_number = $_POST['signupMobile'];

        $stmt = $conn->prepare("INSERT INTO donors (name, email, password, blood_group, address, phone_number) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssss", $name, $email, $password, $blood_group, $address, $phone_number);

        if ($stmt->execute()) {
            $signupMessage = "Signup successful! Please log in.";
        } else {
            $signupMessage = "Email already exists or something went wrong.";
        }
        $stmt->close();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>User - Login/Signup</title>
    <link rel='stylesheet' href='https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.5.0/css/bootstrap.min.css'>
    <link rel='stylesheet' href='https://unicons.iconscout.com/release/v2.1.9/css/unicons.css'>
    <link rel="stylesheet" href="donor_in.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <a class="navbar-brand" href="#">BB</a>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item"><a class="nav-link" href="#">Home</a></li>
                <li class="nav-item"><a class="nav-link" href="donor_in.php">User</a></li>
                <li class="nav-item"><a class="nav-link" href="/bloodbank/admin/admin_in.php">Admin</a></li>
            </ul>
        </div>
    </nav>

    <div class="section">
        <div class="container">
            <div class="row full-height justify-content-center">
                <div class="col-12 text-center align-self-center py-5">
                    <div class="section pb-5 pt-5 pt-sm-2 text-center">
                        <h6 class="mb-0 pb-3"><span>Log In </span><span>Sign Up</span></h6>
                        <input class="checkbox" type="checkbox" id="reg-log" name="reg-log" />
                        <label for="reg-log"></label>
                        <div class="card-3d-wrap mx-auto">
                            <div class="card-3d-wrapper">

                                <!-- LOGIN -->
                                <form method="post" class="card-front">
                                    <div class="center-wrap">
                                        <div class="section text-center">
                                            <h4 class="mb-4 pb-3">User - Log In</h4>
                                            <div class="form-group">
                                                <input type="email" name="logemail" class="form-style"
                                                    placeholder="Your Email" required>
                                                <i class="input-icon uil uil-at"></i>
                                            </div>
                                            <div class="form-group mt-2">
                                                <input type="password" name="logpass" class="form-style"
                                                    placeholder="Your Password" required>
                                                <i class="input-icon uil uil-lock-alt"></i>
                                            </div>
                                            <input type="submit" name="login" class="btn mt-4" value="Submit">
                                            <p class="text-danger mt-2"><?= $loginMessage ?></p>
                                            <p class="mb-0 mt-4 text-center">
                                                <a href="fp.html" class="link">Forgot your password?</a>
                                            </p>
                                        </div>
                                    </div>
                                </form>

                                <!-- SIGNUP -->
                                <form method="post" class="card-back">
                                    <div class="center-wrap">
                                        <div class="section text-center">
                                            <h4 class="mb-4 pb-3">User - Sign Up</h4>
                                            <div class="form-group">
                                                <input type="text" name="signupName" class="form-style"
                                                    placeholder="Your Full Name" required>
                                                <i class="input-icon uil uil-user"></i>
                                            </div>
                                            <div class="form-group mt-2">
                                                <input type="email" name="signupEmail" class="form-style"
                                                    placeholder="Your Email" required>
                                                <i class="input-icon uil uil-at"></i>
                                            </div>
                                            <div class="form-group mt-2">
                                                <input type="password" name="signupPass" class="form-style"
                                                    placeholder="Your Password" required>
                                                <i class="input-icon uil uil-lock-alt"></i>
                                            </div>
                                            <div class="form-group mt-2">
                                                <input type="text" name="signupBlood" class="form-style"
                                                    placeholder="Your Blood Group (e.g., A+)" required>
                                                <i class="input-icon uil uil-droplet"></i>
                                            </div>
                                            <div class="form-group mt-2">
                                                <input type="text" name="signupAddress" class="form-style"
                                                    placeholder="Your Address" required>
                                                <i class="input-icon uil uil-location-point"></i>
                                            </div>
                                            <div class="form-group mt-2">
                                                <input type="text" name="signupMobile" class="form-style"
                                                    placeholder="Your Mobile Number" required>
                                                <i class="input-icon uil uil-phone"></i>
                                            </div>
                                            <input type="submit" name="signup" class="btn mt-4" value="Submit">
                                            <p class="text-success mt-2"><?= $signupMessage ?></p>
                                        </div>
                                    </div>
                                </form>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</body>
</html>
