<?php
include './config.php';
session_start();
if (isset($_SESSION['user_id'])) {
    $user_idd = $_SESSION['user_id'];
    $stmt = "SELECT * FROM users WHERE id = '$user_idd' AND status = 'active'";
    $result = mysqli_query($con, $stmt);
    if ($result) {
        if (mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);
            $_SESSION['user_role'] = $user['role'];
            if ($user['role'] === 'admin') {
                header("Refresh:0.11; url=./admin/admin_dashboard.php");
            } else if ($user['role'] === 'waiter') {
                header("Refresh:0.11; url=./waiter/waiter_dashboard.php");
            } else if ($user['role'] === 'callcenter') {
                header("Refresh:0.11; url=./callcenter/callcenter_dashboard.php");
            }
        }
    }
    // User is not logged in, redirect to login page
    //header("Location: ../patient/patientdashboard.php");
    exit();
}

if (isset($_POST['login_to_system'])) {
    $email = $_POST['emailadd'];
    $pass = $_POST['password'];
    $stmt = "SELECT * FROM users WHERE email = '$email'";
    $result = mysqli_query($con, $stmt);
    if ($result) {
        if (mysqli_num_rows($result) === 1) {
            $user = mysqli_fetch_assoc($result);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_username'] = $user['username'];
            $_SESSION['user_password'] = $user['password'];
            $_SESSION['user_phone'] = $user['fullname'];
            $_SESSION['user_role'] = $user['role'];
            if ($user['status'] !== 'active') {
                echo '<script>alert("Your account has been de-activated. Please check with your admin!")</script>';
                header("Refresh:0.11; url=./login_auth.php");
                session_unset();
                session_destroy();
            } else {
                if ($pass !== $user['password']) {
                    //AND password = '$password'
                    echo '<script>alert("Incorrect password entered.")</script>';
                    header("Refresh:0.11; url=./login_auth.php");
                    session_unset();
                    session_destroy();
                } else if ($user) {
                    if ($user['role'] === 'admin') {
                        echo '<script>alert("Login using admin account! You will be redirected.")</script>';
                        header("Location: ./admin/admin_dashboard.php");
                    } else if ($user['role'] === 'waiter'){
                        echo '<script>alert("Login using waiter account! You will be redirected.")</script>';
                        header("Location: ./waiter/waiter_dashboard.php");
                    } 
                    else if ($user['role'] === 'callcenter') {
                        echo '<script>alert("Login using call center account! You will be redirected.")</script>';
                        header("Location: ./callcenter/callcenter_dashboard.php");
                    }
                }
            }
        } else {
            echo '<script>alert("Incorrect Email Address")</script>';
            header("Refresh:0.11; url=./login_auth.php");
            session_unset();
            session_destroy();
        }
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="barista cafe is a free Bootstrap 5 HTML template for coffee shops, cafes, and restaurants.">
    <meta name="keywords" content="barista, cafe, coffee, restaurant, bootstrap, html, template">
    <meta name="author" content="Mahdi Saleh">
    <title>Login - Barista Café</title>
    <!-- CSS FILES -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200;0,400;0,600;0,700;1,200;1,700&display=swap" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap-icons.css" rel="stylesheet">
    <link href="css/vegas.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <link rel="shortcut icon" href="./images/icon.png" type="image/x-icon"> <!--ICON-->
</head>

<body class="reservation-page">
    <main>
        <nav class="navbar navbar-expand-lg">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="index.php">
                    <img src="images/coffee-beans.png" class="navbar-brand-image img-fluid" alt="">
                    Barista Café
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-lg-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="index.php#section_1">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php#section_2">About us</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php#section_3">Our Menu</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php#section_4">Testimonials</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="index.php#section_5">Contact</a>
                        </li>
                    </ul>
                    <div class="ms-lg-3">
                        <a class="btn custom-btn custom-border-btn" href="reservation.php">
                            Reservation
                            <i class="bi-arrow-up-right ms-2"></i>
                        </a>
                    </div>
                </div>
            </div>
        </nav>


        <section class="booking-section section-padding">
            <div class="container">
                <div class="row">
                    <div class="col-lg-10 col-12 mx-auto">
                        <div class="booking-form-wrap">
                            <div class="row">
                                <div class="col-lg-7 col-12 p-0">
                                    <form class="custom-form booking-form" action="login_auth.php" method="post" role="form">
                                        <div class="text-center mb-4 pb-lg-2">
                                            <em class="text-white">Fill out the booking form</em>
                                            <h2 class="text-white">Login to System</h2>
                                        </div>
                                        <div class="booking-form-body">
                                            <div class="row">

                                                <div class="col-lg-12 col-12">
                                                    <input type="email" name="emailadd" id="booking-form-number" class="form-control" placeholder="Email address" required="">
                                                    <input type="password" name="password" id="booking-form-password" class="form-control mt-2" placeholder="Password" required="">
                                                </div>
                                                <div class="col-lg-4 col-md-10 col-8 mx-auto mt-2">
                                                    <button type="submit" name="login_to_system" class="form-control">Login</button>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-lg-5 col-12 p-0">
                                    <div class="booking-form-image-wrap">
                                        <img src="images/barman-with-fruits.jpg" class="booking-form-image img-fluid" alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
        </section>


        <footer class="site-footer">
            <div class="container">
                <div class="row">
                    <div class="col-lg-4 col-12 me-auto">
                        <em class="text-white d-block mb-4">Where to find us?</em>
                        <strong class="text-white">
                            <i class="bi-geo-alt me-2"></i>
                            Nabatieh, Lebanon
                        </strong>
                        <ul class="social-icon mt-4">
                            <li class="social-icon-item">
                                <a href="#" class="social-icon-link bi-facebook">
                                </a>
                            </li>
                            <li class="social-icon-item">
                                <a href="#" target="_new" class="social-icon-link bi-twitter">
                                </a>
                            </li>
                            <li class="social-icon-item">
                                <a href="#" class="social-icon-link bi-whatsapp">
                                </a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-lg-3 col-12 mt-4 mb-3 mt-lg-0 mb-lg-0">
                        <em class="text-white d-block mb-4">Contact</em>
                        <p class="d-flex mb-1">
                            <strong class="me-2">Phone:</strong>
                            <a href="" class="site-footer-link">
                                +961 71 123 456
                            </a>
                        </p>
                        <p class="d-flex">
                            <strong class="me-2">Email:</strong>
                            <a href="mailto:info@yourgmail.com" class="site-footer-link">
                                baristacafe@gmail.com
                            </a>
                        </p>
                    </div>
                    <div class="col-lg-5 col-12">
                        <em class="text-white d-block mb-4">Opening Hours.</em>
                        <ul class="opening-hours-list">
                            <li class="d-flex">
                                Monday - Friday
                                <span class="underline"></span>
                                <strong>9:00 - 18:00</strong>
                            </li>
                            <li class="d-flex">
                                Saturday
                                <span class="underline"></span>
                                <strong>11:00 - 16:30</strong>
                            </li>
                            <li class="d-flex">
                                Sunday
                                <span class="underline"></span>
                                <strong>Closed</strong>
                            </li>
                        </ul>
                    </div>
                    <div class="col-lg-8 col-12 mt-4">
                        <p class="copyright-text mb-0">Copyright © Barista Cafe 2025
                            - Design: <a rel="sponsored" href="http://mahdisaleh.ct.ws" target="_blank">Mahdi Saleh</a>
                        </p>
                    </div>
                </div>
        </footer>
    </main>


    <!-- JAVASCRIPT FILES -->
    <script src="js/jquery.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/jquery.sticky.js"></script>
    <script src="js/vegas.min.js"></script>
    <script src="js/custom.js"></script>

</body>

</html>