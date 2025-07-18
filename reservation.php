<?php
include './config.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['booking-form-name'];
    $phone = $_POST['booking-form-phone'];
    $date = $_POST['booking-form-date'];
    $time = $_POST['booking-form-time'];
    $number = $_POST['booking-form-number'];
    $message = $_POST['booking-form-message'];

    // Insert into database
    $sql = "INSERT INTO reservations (client_name, phone, reservation_date, reservation_time, guest_count, client_message) VALUES ('$name', '$phone', '$date', '$time', '$number', '$message')";
    if (mysqli_query($con, $sql)) {
        echo "<script>alert('Reservation set successfully!');</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($con) . "');</script>";
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
    <title>Barista Café - Reservation</title>
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
                                    <form class="custom-form booking-form" action="./reservation.php" method="post" role="form">
                                        <div class="text-center mb-4 pb-lg-2">
                                            <em class="text-white">Fill out the booking form</em>
                                            <h2 class="text-white">Book a table</h2>
                                            <p><a href="./cancel_reservation.php">Cancel reservation?</a></p>
                                        </div>
                                        <div class="booking-form-body">
                                            <div class="row">
                                                <div class="col-lg-6 col-12">
                                                    <input type="text" name="booking-form-name" id="booking-form-name" class="form-control" placeholder="Full Name" required>
                                                </div>
                                                <div class="col-lg-6 col-12">
                                                    <input type="tel" class="form-control" name="booking-form-phone" placeholder="Phone: 71234567" required="">
                                                </div>
                                                <div class="col-lg-6 col-12">
                                                    <input class="form-control" type="time" name="booking-form-time">
                                                </div>
                                                <div class="col-lg-6 col-12">
                                                    <input type="date" name="booking-form-date" id="booking-form-date" class="form-control" required="">
                                                </div>
                                                <div class="col-lg-12 col-12">
                                                    <input type="number" name="booking-form-number" id="booking-form-number" class="form-control" placeholder="Number of People (e.g.: 10)" required="">
                                                    <textarea name="booking-form-message" rows="3" class="form-control" id="booking-form-message" placeholder="Comment (Optional)"></textarea>
                                                </div>
                                                <div class="col-lg-4 col-md-10 col-8 mx-auto mt-2">
                                                    <button type="submit" class="form-control">Submit</button>
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
    <style>
        #goTopBtn {
            display: none;
            position: fixed;
            bottom: 32px;
            right: 32px;
            z-index: 999;
            background: var(--custom-btn-bg-color, #6c4f3d);
            color: #fff;
            border: none;
            border-radius: 50%;
            width: 48px;
            height: 48px;
            font-size: 24px;
            cursor: pointer;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
            transition: background 0.2s;
        }

        #goTopBtn:hover {
            background: #4b3222;
        }
    </style>
    <script>
        window.addEventListener('DOMContentLoaded', function() {
            var btn = document.createElement('button');
            btn.id = 'goTopBtn';
            btn.title = 'Go to top';
            btn.innerHTML = '<i class="bi bi-arrow-up"></i>';
            document.body.appendChild(btn);

            window.onscroll = function() {
                btn.style.display = (document.documentElement.scrollTop > 200 || document.body.scrollTop > 200) ? 'block' : 'none';
            };

            btn.onclick = function() {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            };
        });
    </script>
</body>

</html>