<?php
include './config.php';
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="barista cafe is a free Bootstrap 5 HTML template for coffee shops, cafes, and restaurants.">
    <meta name="keywords" content="barista, cafe, coffee, restaurant, bootstrap, html, template">
    <meta name="author" content="Mahdi Saleh">
    <title>Barista Café</title>
    <!-- CSS FILES -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200;0,400;0,600;0,700;1,200;1,700&display=swap"
        rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap-icons.css" rel="stylesheet">
    <link href="css/vegas.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="./images/icon.png" type="image/x-icon"> <!--ICON-->
    <link href="css/style.css" rel="stylesheet">
</head>

<body>
    <main>
        <nav class="navbar navbar-expand-lg">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="index.php">
                    <img src="images/coffee-beans.png" class="navbar-brand-image img-fluid" alt="Barista Cafe Template">
                    Barista Café
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-lg-auto">
                        <li class="nav-item">
                            <a class="nav-link click-scroll" href="#section_1">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link click-scroll" href="#section_2">About</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link click-scroll" href="#section_3">Our Menu</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link click-scroll" href="#section_4">Reviews</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link click-scroll" href="#section_5">Contact</a>
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

        <section class="hero-section d-flex justify-content-center align-items-center" id="section_1">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6 col-12 mx-auto">
                        <em class="small-text">welcome to Barista Café</em>
                        <h1>Cafe Klang</h1>
                        <p class="text-white mb-4 pb-lg-2">
                            Your <em>favourite</em> coffee daily lives.
                        </p>
                        <a class="btn custom-btn custom-border-btn smoothscroll me-3" href="#section_2">
                            Our Story
                        </a>
                        <a class="btn custom-btn smoothscroll me-2 mb-2" href="#section_3"><strong>Check
                                Menu</strong></a>
                    </div>
                </div>
            </div>
            <div class="hero-slides"></div>
        </section>


        <section class="about-section section-padding" id="section_2">
            <div class="section-overlay"></div>
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-6 col-12">
                        <div class="ratio ratio-1x1">
                            <video autoplay="" loop="" muted="" class="custom-video" poster="">
                                <source src="videos/pexels-mike-jones-9046237.mp4" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                            <div class="about-video-info d-flex flex-column">
                                <h4 class="mt-auto">We Started Since 2009.</h4>
                                <h4>Best Cafe in Klang.</h4>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-5 col-12 mt-4 mt-lg-0 mx-auto">
                        <em class="text-white">Barista Café</em>
                        <h2 class="text-white mb-3">Cafe KL</h2>
                        <p class="text-white">The café had been in the town for as long as anyone could remember, and it
                            had become a beloved institution among the locals.</p>
                        <p class="text-white">The café was run by a friendly and hospitable couple, Mr. and Mrs.
                            Johnson. Barista Cafe is free Bootstrap 5 HTML layout provided by <a rel="nofollow"
                                href="http://mahdisaleh.ct.ws" target="_blank">Mahdi Saleh</a>.</p>
                        <a href="#barista-team" class="smoothscroll btn custom-btn custom-border-btn mt-3 mb-4">Meet
                            Baristas</a>
                    </div>
                </div>
            </div>
        </section>


        <section class="barista-section section-padding section-bg" id="barista-team">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-12 col-12 text-center mb-4 pb-lg-2">
                        <em class="text-white">Creative Baristas</em>
                        <h2 class="text-white">Meet People</h2>
                    </div>
                    <div class="col-lg-3 col-md-6 col-12 mb-4">
                        <div class="team-block-wrap">
                            <div class="team-block-info d-flex flex-column">
                                <div class="d-flex mt-auto mb-3">
                                    <h4 class="text-white mb-0">Steve</h4>
                                    <p class="badge ms-4"><em>Boss</em></p>
                                </div>
                                <p class="text-white mb-0">your favourite coffee daily lives tempor.</p>
                            </div>
                            <div class="team-block-image-wrap">
                                <img src="images/team/portrait-elegant-old-man-wearing-suit.jpg"
                                    class="team-block-image img-fluid" alt="">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-12 mb-4">
                        <div class="team-block-wrap">
                            <div class="team-block-info d-flex flex-column">
                                <div class="d-flex mt-auto mb-3">
                                    <h4 class="text-white mb-0">Sandra</h4>
                                    <p class="badge ms-4"><em>Manager</em></p>
                                </div>
                                <p class="text-white mb-0">your favourite coffee daily lives.</p>
                            </div>
                            <div class="team-block-image-wrap">
                                <img src="images/team/cute-korean-barista-girl-pouring-coffee-prepare-filter-batch-brew-pour-working-cafe.jpg"
                                    class="team-block-image img-fluid" alt="">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-12 mb-4">
                        <div class="team-block-wrap">
                            <div class="team-block-info d-flex flex-column">
                                <div class="d-flex mt-auto mb-3">
                                    <h4 class="text-white mb-0">Jackson</h4>
                                    <p class="badge ms-4"><em>Senior</em></p>
                                </div>
                                <p class="text-white mb-0">your favourite coffee daily lives.</p>
                            </div>
                            <div class="team-block-image-wrap">
                                <img src="images/team/small-business-owner-drinking-coffee.jpg"
                                    class="team-block-image img-fluid" alt="">
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-12">
                        <div class="team-block-wrap">
                            <div class="team-block-info d-flex flex-column">
                                <div class="d-flex mt-auto mb-3">
                                    <h4 class="text-white mb-0">Michelle</h4>
                                    <p class="badge ms-4"><em>Barista</em></p>
                                </div>
                                <p class="text-white mb-0">your favourite coffee daily consectetur.</p>
                            </div>
                            <div class="team-block-image-wrap">
                                <img src="images/team/smiley-business-woman-working-cashier.jpg"
                                    class="team-block-image img-fluid" alt="">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <section class="menu-section section-padding" id="section_3">
            <div class="container">
                <a href="./menu.php" class="menubtn">Check The Full Menu</a>
                <div class="row">
                    <?php
                    // Use the existing DB connection from config.php ($conn or $mysqli)
                    if (!isset($con)) {
                        // fallback for variable name
                        if (isset($mysqli)) {
                            $con = $mysqli;
                        } else {
                            echo "<div class='alert alert-danger'>Database connection not found.</div>";
                            exit();
                        }
                    }

                    // Define the categories to display
                    $categories = ['breakfast', 'desserts'];
                    $category_titles = [
                        'breakfast' => ['Delicious Menu', 'Breakfast'],
                        'desserts' => ['Favourite Menu', 'Desserts']
                    ];

                    // Prepare to fetch category IDs
                    $category_ids = [];
                    $placeholders = implode(',', array_fill(0, count($categories), '?'));
                    $cat_stmt = $con->prepare("SELECT id, name FROM menu_categories WHERE LOWER(name) IN ($placeholders)");
                    $cat_stmt->bind_param(str_repeat('s', count($categories)), ...$categories);
                    $cat_stmt->execute();
                    $cat_result = $cat_stmt->get_result();
                    while ($row = $cat_result->fetch_assoc()) {
                        $category_ids[strtolower($row['name'])] = $row['id'];
                    }
                    $cat_stmt->close();

                    // Loop through each category and display up to 4 items
                    $col_classes = ['col-lg-6 col-12 mb-4 mb-lg-0', 'col-lg-6 col-12'];
                    $col_index = 0;
                    foreach ($categories as $cat) {
                        $cat_id = isset($category_ids[$cat]) ? $category_ids[$cat] : null;
                        if (!$cat_id) continue;

                        // Fetch up to 4 menu items for this category
                        $item_stmt = $con->prepare("SELECT * FROM menu_items WHERE category_id = ? ORDER BY id ASC LIMIT 4");
                        $item_stmt->bind_param("i", $cat_id);
                        $item_stmt->execute();
                        $item_result = $item_stmt->get_result();

                        echo '<div class="' . $col_classes[$col_index++] . '">';
                        echo '<div class="menu-block-wrap">';
                        echo '<div class="text-center mb-4 pb-lg-2">';
                        echo '<em class="text-white">' . htmlspecialchars($category_titles[$cat][0]) . '</em>';
                        echo '<h4 class="text-white">' . htmlspecialchars($category_titles[$cat][1]) . '</h4>';
                        echo '</div>';

                        while ($item = $item_result->fetch_assoc()) {
                            echo '<div class="menu-block my-4">';
                            echo '<div class="d-flex">';
                            echo '<h6>' . htmlspecialchars($item['name']);
                            if (!empty($item['badge'])) {
                                echo ' <span class="badge ms-3">' . htmlspecialchars($item['badge']) . '</span>';
                            }
                            echo '</h6>';
                            echo '<span class="underline"></span>';
                            if (!empty($item['old_price'])) {
                                echo '<strong class="text-white ms-auto"><del>$' . htmlspecialchars($item['old_price']) . '</del></strong>';
                                echo '<strong class="ms-2">$' . htmlspecialchars($item['price']) . '</strong>';
                            } else {
                                echo '<strong class="ms-auto">$' . htmlspecialchars($item['price']) . '</strong>';
                            }
                            echo '</div>';
                            if (!empty($item['description'])) {
                                echo '<div class="border-top mt-2 pt-2">';
                                echo '<small>' . htmlspecialchars($item['description']) . '</small>';
                                echo '</div>';
                            }
                            echo '</div>';
                        }

                        echo '</div></div>';
                        $item_stmt->close();
                    }
                    ?>
                </div>

            </div>
        </section>


        <section class="reviews-section section-padding section-bg" id="section_4">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-lg-12 col-12 text-center mb-4 pb-lg-2">
                        <em class="text-white">Reviews by Customers</em>
                        <h2 class="text-white">Testimonials</h2>
                    </div>
                    <div class="timeline">
                        <div class="timeline-container timeline-container-left">
                            <div class="timeline-content">
                                <div class="reviews-block">
                                    <div class="reviews-block-image-wrap d-flex align-items-center">
                                        <img src="images/reviews/young-woman-with-round-glasses-yellow-sweater.jpg"
                                            class="reviews-block-image img-fluid" alt="">
                                        <div class="">
                                            <h6 class="text-white mb-0">Lea</h6>
                                            <em class="text-white"> Customers</em>
                                        </div>
                                    </div>
                                    <div class="reviews-block-info">
                                        <p>The ambiance is amazing, and the staff is super friendly. Definitely my favorite café in town!</p>
                                        <div class="d-flex border-top pt-3 mt-4">
                                            <strong class="text-white">4.5 <small class="ms-2">Rating</small></strong>
                                            <div class="reviews-group ms-auto">
                                                <i class="bi-star-fill"></i>
                                                <i class="bi-star-fill"></i>
                                                <i class="bi-star-fill"></i>
                                                <i class="bi-star-fill"></i>
                                                <i class="bi-star"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="timeline-container timeline-container-right">
                            <div class="timeline-content">
                                <div class="reviews-block">
                                    <div class="reviews-block-image-wrap d-flex align-items-center">
                                        <img src="images/reviews/senior-man-white-sweater-eyeglasses.jpg"
                                            class="reviews-block-image img-fluid" alt="">
                                        <div class="">
                                            <h6 class="text-white mb-0">Tarek</h6>
                                            <em class="text-white"> Customers</em>
                                        </div>
                                    </div>
                                    <div class="reviews-block-info">
                                        <p>Clean, comfortable seating and a great playlist. I love studying here with a coffee.</p>
                                        <div class="d-flex border-top pt-3 mt-4">
                                            <strong class="text-white">4.5 <small class="ms-2">Rating</small></strong>
                                            <div class="reviews-group ms-auto">
                                                <i class="bi-star-fill"></i>
                                                <i class="bi-star-fill"></i>
                                                <i class="bi-star-fill"></i>
                                                <i class="bi-star-fill"></i>
                                                <i class="bi-star"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="timeline-container timeline-container-left">
                            <div class="timeline-content">
                                <div class="reviews-block">
                                    <div class="reviews-block-image-wrap d-flex align-items-center">
                                        <img src="images/reviews/young-beautiful-woman-pink-warm-sweater-natural-look-smiling-portrait-isolated-long-hair.jpg"
                                            class="reviews-block-image img-fluid" alt="">
                                        <div class="">
                                            <h6 class="text-white mb-0">Sarah</h6>
                                            <em class="text-white"> Customers</em>
                                        </div>
                                    </div>
                                    <div class="reviews-block-info">
                                        <p>Fast service, quality food, and the decor is beautiful. Feels like home!</p>
                                        <div class="d-flex border-top pt-3 mt-4">
                                            <strong class="text-white">4.5 <small class="ms-2">Rating</small></strong>
                                            <div class="reviews-group ms-auto">
                                                <i class="bi-star-fill"></i>
                                                <i class="bi-star-fill"></i>
                                                <i class="bi-star-fill"></i>
                                                <i class="bi-star-fill"></i>
                                                <i class="bi-star"></i>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <section class="contact-section section-padding" id="section_5">
            <div class="container">
                <div class="row">
                    <div class="col-lg-12 col-12">
                        <em class="text-white">Say Hello</em>
                        <h2 class="text-white mb-4 pb-lg-2">Contact</h2>
                    </div>
                    <div class="col-lg-6 col-12">
                        <form action="#" method="post" class="custom-form contact-form" role="form">
                            <div class="row">
                                <div class="col-lg-6 col-12">
                                    <label for="name" class="form-label">Name <sup class="text-danger">*</sup></label>
                                    <input type="text" name="name" id="name" class="form-control" placeholder="Example"
                                        required="">
                                </div>
                                <div class="col-lg-6 col-12">
                                    <label for="email" class="form-label">Email Address</label>
                                    <input type="email" name="email" id="email" pattern="[^ @]*@[^ @]*"
                                        class="form-control" placeholder="example@gmail.com" required="">
                                </div>
                                <div class="col-12">
                                    <label for="message" class="form-label">How can we help?</label>
                                    <textarea name="message" rows="4" class="form-control" id="message"
                                        placeholder="Message" required=""></textarea>
                                </div>
                            </div>
                            <div class="col-lg-5 col-12 mx-auto mt-3">
                                <button type="submit" class="form-control">Send Message</button>
                            </div>
                        </form>
                    </div>
                    <div class="col-lg-6 col-12 mx-auto mt-5 mt-lg-0 ps-lg-5">
                        <iframe class="google-map"
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d26653.371930766334!2d35.46732295317009!3d33.379641498696145!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x151e94a5d7a1ac15%3A0x23629441e0514c4c!2sNabatieh!5e0!3m2!1sen!2slb!4v1741612492083!5m2!1sen!2slb"
                            width="100%" height="300" style="border:0;" allowfullscreen="" loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"></iframe>
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

                        <p class="copyright-text mb-0"><a href="./login_auth.php">Login || </a> Copyright © Barista Cafe 2025
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
    <script src="js/click-scroll.js"></script>
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
<style>
    .menubtn {
        background: var(--custom-btn-bg-color);
        border: 2px solid transparent;
        border-radius: var(--border-radius-large);
        color: var(--white-color);
        font-size: var(--btn-font-size);
        font-weight: var(--font-weight-bold);
        line-height: normal;
        transition: all 0.3s;
        padding: 12px 28px;
        margin: 20px auto;
        /* center horizontally and add vertical space */
        display: block;
        /* required for auto margins to take effect */
        text-align: center;
    }
</style>

</html>