<?php
session_start();

include('classes/connect.php');
include('classes/auth.php');
include('classes/util.php');
include('classes/user.php');

$page_name="Contact";
$Auth=new Auth();
$error="";


if($_SERVER['REQUEST_METHOD']=="POST"){
    $error=$Auth->login($_POST);
    if($error=="") header('Location:index.php');
}


$User=new User();
if(isset($_SESSION['webtoon_userid'])){
    $user=$User->details($_SESSION['webtoon_userid']);

}


$Util=new Util();

?>


<!DOCTYPE html>
<html lang="zxx">

<head>
    <?php include('layouts/head.php'); ?>
</head>

<body>
    <!-- Page Preloder -->
    <!-- <div id="preloder">
        <div class="loader"></div>
    </div> -->

    <!-- Header Section Begin -->
     <?php 
        include('layouts/header.php');
    ?>
    <!-- Header End -->

    <!-- Normal Breadcrumb Begin -->
    <section class="normal-breadcrumb set-bg" data-setbg="img/normal-breadcrumb.jpg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="normal__breadcrumb__text">
                        <h2>Contact Us</h2>
                        <p>Warmly welcome! Feel free to contact us.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Normal Breadcrumb End -->

    <!-- Login Section Begin -->
    <section class="login spad">
        <div class="container">
            <div class="login__social">
                <div class="row d-flex justify-content-center">
                    <div class="col-lg-6">
                        <div class="login__social__links">
                            
                            <ul>
                                <li><a href="https://www.facebook.com/profile.php?id=61556031550376" class="facebook"><i class="fa fa-facebook"></i>Facebook</a></li>
                                <li><a href="https://t.me/aplaceforromancemyanmartrans" class="twitter"><i class="fa fa-telegram"></i>Telegram</a>
                                <li><a href="https://www.youtube.com/@WorldOfWebtoonMMSub-9918" class="facebook" style="background:red"><i class="fa fa-youtube"></i>Youtube</a>
                                <li><a href="https://www.tiktok.com/@worldofwebtoonmyanmarsub?_t=8hNhHGmoCRO&_r=1" class="facebook" style="background:#333"><i class="fa fa-tik-tok"></i>Tik Tok</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Login Section End -->

<!-- Footer Section Begin -->
    <?php 
        include('layouts/footer.php');
    ?>
  <!-- Footer Section End -->

      <!-- Search model Begin -->
      <div class="search-model">
        <div class="h-100 d-flex align-items-center justify-content-center">
            <div class="search-close-switch"><i class="icon_close"></i></div>
            <form class="search-model-form">
                <input type="text" id="search-input" placeholder="Search here.....">
            </form>
        </div>
    </div>
    <!-- Search model end -->

    <!-- Js Plugins -->
    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/player.js"></script>
    <script src="js/jquery.nice-select.min.js"></script>
    <script src="js/mixitup.min.js"></script>
    <script src="js/jquery.slicknav.js"></script>
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/main.js"></script>


</body>

</html>