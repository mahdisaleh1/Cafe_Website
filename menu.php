<?php

include 'config.php'; // Include your database configuration file

// Fetch categories
$categories = [];
$cat_sql = "SELECT id, name FROM menu_categories ORDER BY id ASC";
$cat_result = $con->query($cat_sql);
if ($cat_result && $cat_result->num_rows > 0) {
    while ($row = $cat_result->fetch_assoc()) {
        $categories[$row['id']] = [
            'name' => $row['name'],
            'items' => []
        ];
    }
}

// Fetch items and assign to categories
$item_sql = "SELECT * FROM menu_items ORDER BY category_id, id ASC";
$item_result = $con->query($item_sql);
if ($item_result && $item_result->num_rows > 0) {
    while ($row = $item_result->fetch_assoc()) {
        if (isset($categories[$row['category_id']])) {
            $categories[$row['category_id']]['items'][] = $row;
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
    <title>Menu - Barista Café</title>
    <!-- CSS FILES -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,200;0,400;0,600;0,700;1,200;1,700&display=swap"
        rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/bootstrap-icons.css" rel="stylesheet">
    <link href="css/vegas.min.css" rel="stylesheet">
    <link rel="shortcut icon" href="./images/icon.png" type="image/x-icon">
    <link href="css/style.css" rel="stylesheet">
</head>

<body>
    <main>
        <nav class="navbar navbar-expand-lg">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="index.php">
                    <img src="images/coffee-beans.png" class="navbar-brand-image img-fluid" alt="Barista Cafe Template">
                    Menu - Barista Café
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                    aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-lg-auto">
                        <li class="nav-item">
                            <a class="nav-link click-scroll not-active" href="./index.php">Home</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link click-scroll" href="./index.php#section_2">About</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link click-scroll active" href="./menu.php">Our Menu</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link click-scroll" href="./index.php#section_4">Reviews</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link click-scroll" href="./index.php#section_5">Contact</a>
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

        <section class="menu-section section-padding" id="section_3">
            <div class="container">
                <div class="row">
                    <?php
                    // Split categories into two columns
                    $cat_chunks = array_chunk($categories, ceil(count($categories) / 2), true);
                    foreach ($cat_chunks as $col_idx => $cat_group):
                    ?>
                        <div class="col-lg-6 col-12<?php if ($col_idx == 0) echo ' mb-4 mb-lg-0'; ?>">
                            <?php foreach ($cat_group as $cat): ?>
                                <div class="menu-block-wrap mb-5">
                                    <div class="text-center mb-4 pb-lg-2">
                                        <?php if (!empty($cat['description'])): ?>
                                            <em class="text-white"><?= htmlspecialchars($cat['description']) ?></em>
                                        <?php endif; ?>
                                        <h4 class="text-white"><?= htmlspecialchars($cat['name']) ?></h4>
                                    </div>
                                    <?php foreach ($cat['items'] as $item): ?>
                                        <div class="menu-block my-4">
                                            <div class="d-flex">
                                                <h6>
                                                    <?= htmlspecialchars($item['name']) ?>
                                                    <?php if (!empty($item['is_recommended'])): ?>
                                                        <span class="badge ms-3">Recommend</span>
                                                    <?php endif; ?>
                                                </h6>
                                                <span class="underline"></span>
                                                <?php if (!empty($item['old_price']) && $item['old_price'] > 0): ?>
                                                    <strong class="text-white ms-auto"><del>$<?= number_format($item['old_price'], 2) ?></del></strong>
                                                    <strong class="ms-2">$<?= number_format($item['price'], 2) ?></strong>
                                                <?php else: ?>
                                                    <strong class="ms-auto">$<?= number_format($item['price'], 2) ?></strong>
                                                <?php endif; ?>
                                            </div>
                                            <?php if (!empty($item['description'])): ?>
                                                <div class="border-top mt-2 pt-2">
                                                    <small><?= htmlspecialchars($item['description']) ?></small>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>

        <!-- The rest of your page remains unchanged -->
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
    .navbar-nav .nav-link.not-active {
        color: #fff !important;
    }

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
        display: block;
        text-align: center;
    }

    .menu-section .container {
        margin-top: 40px;
    }

    @media screen and (max-width: 767px) {
        .menu-section .container {
            margin-top: 100px;
        }
    }
</style>

</html>
<?php $con->close(); ?>