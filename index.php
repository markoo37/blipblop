<?php
session_start();
$is_logged_in = isset($_SESSION['user_id']);
$username = $is_logged_in ? $_SESSION['username'] : '';
$user_type = $is_logged_in ? $_SESSION['user_type'] : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>blipblop</title>
    <link rel="stylesheet" href="/style.css">
    <!-- Font Awesome for icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
<header>
    <div class="container">
        <div class="header-container">
            <a href="index.php" class="logo">blip<span>blop</span></a>
            <nav class="nav-links">



                <?php if ($is_logged_in): ?>
                    <?php if ($user_type != 'admin'): ?>

                        <form method="GET" action="index.php" class="search-form">
                            <input type="hidden" name="page" value="search">
                            <input type="search" name="q" placeholder="Keresés" required>
                            <button type="submit"><i class="fas fa-search"></i></button>
                        </form>

                        <a href="index.php?page=upload" class="btn page-link" data-page="upload">Feltöltés</a>
                    <?php endif; ?>
                <?php else: ?>
                    <?php if ($user_type != 'admin'): ?>

                        <form method="GET" action="index.php" class="search-form">
                            <input type="hidden" name="page" value="search">
                            <input type="search" name="q" placeholder="Keresés">
                            <button type="submit"><i class="fas fa-search"></i></button>
                        </form>

                        <a href="index.php?page=login" class="btn page-link" data-page="upload">Feltöltés</a>
                    <?php endif; ?>
                <?php endif; ?>

                <a href="index.php" class="page-link" data-page="home">Kezdőlap</a>
                <?php if ($is_logged_in): ?>
                    <div class="dropdown">
                        <a href="#"><?php echo htmlspecialchars($username); ?><span class="arrow">&#9662;</span></a>
                        <div class="dropdown-content">

                            <?php if ($user_type != 'admin'): ?>
                                <a href="index.php?page=account" class="page-link" data-page="account">Profil</a>
                            <?php endif; ?>

                            <a href="index.php?page=logout">Kijelentkezés</a>
                        </div>
                    </div>

                <?php else: ?>
                    <a href="index.php?page=login" class="page-link" data-page="login">Bejelentkezés</a>
                    <a href="index.php?page=register" class="page-link" data-page="register">Regisztráció</a>
                <?php endif; ?>
            </nav>
        </div>
    </div>
</header>

<main>
    <div class="container" id="content">
        <?php

        if ($user_type == "admin"){
            $page = $_GET['page'] ?? 'home';

            match ($page) {
                'home' => include 'pages/admin/home.php',
                'login' => include 'pages/login.php',
                'register' => include 'pages/register.php',
                'account' => include 'pages/admin/account.php',
                'logout' => include 'pages/logout.php',
                'upload' => include 'pages/upload.php',
                'edituser' => include 'pages/admin/edituser.php',
                'editvideo' => include 'pages/admin/editvideo.php',
                default => include 'pages/404.php',
            };

        }
        else{
            $page = $_GET['page'] ?? 'home';
            $category = $_GET['category'] ?? 'all';

            if ($page == 'home' && $category == 'all') {
                include 'pages/home.php';
            }
            else if ($page == 'home' && $category == '1') {
                include 'pages/hirek.php';
            }
            else if ($page == 'home' && $category == '2') {
                include 'pages/jatekok.php';
            }
            else if ($page == 'home' && $category == '3') {
                include 'pages/sport.php';
            }
            else if ($page == 'home' && $category == '4') {
                include 'pages/tech.php';
            }
            else if ($page == 'home' && $category == '5') {
                include 'pages/podcastok.php';
            }

            match ($page) {
                'login' => include 'pages/login.php',
                'register' => include 'pages/register.php',
                'account' => include 'pages/account.php',
                'logout' => include 'pages/logout.php',
                'upload' => include 'pages/upload.php',
                'about' => include 'pages/about.php',
                'contact' => include 'pages/contact.php',
                'search' => include 'pages/searchresult.php',
                'watch' => include "pages/watch.php",
                default => include 'pages/404.php'
            };
        }




        ?>
    </div>
</main>

<footer>
    <div class="container">
        <div class="footer-links">
            <a href="index.php?page=about" class="page-link" data-page="about">Rólunk</a>
            <a href="index.php?page=contact" class="page-link" data-page="contact">Kapcsolat</a>
            <a href="index.php?page=terms" class="page-link" data-page="terms">Terms</a>
            <a href="index.php?page=privacy" class="page-link" data-page="privacy">Privacy</a>
        </div>
        <p>&copy; 2025 blipblop</p>
    </div>
</footer>

<script src="js/script.js"></script>
</body>
</html>