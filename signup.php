<?php
session_start();

include('classes/connect.php');
include('classes/user.php');

$page_name="Signup";


$error="";
if($_SERVER['REQUEST_METHOD']=="POST"){
    $User=new User();
    $result=$User->create($_POST);
    $error=$result['error'];

    if($error==""){
        $_SESSION['webtoon_userid']=$result['user']['id'];
        header('Location:index.php');
    }

}


?>


<!DOCTYPE html>
<html lang="zxx">

<head>
    <?php include('layouts/head.php'); ?>
</head>

<body>
    <!-- Page Preloder -->
    <div id="preloder">
        <div class="loader"></div>
    </div>

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
                        <h2>Sign Up</h2>
                        <p>Welcome to the official Anime blog.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Normal Breadcrumb End -->

    <!-- Signup Section Begin -->
    <section class="signup spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="login__form">
                        <h3>Sign Up</h3>
                        <form action="" method="POST">
                            <div class="input__item">
                                <input type="text" placeholder="Email address or Phone" name="email">
                                <span class="icon_mail"></span>
                            </div>
                            <div class="input__item">
                                <input type="text" placeholder="First Name" name="first_name">
                                <span class="icon_profile"></span>
                            </div>
                            <div class="input__item">
                                <input type="text" placeholder="Last Name" name="last_name">
                                <span class="icon_profile"></span>
                            </div>
                            <div class="input__item">
                                <input type="password" placeholder="Password" name="password">
                                <span class="icon_lock"></span>
                            </div>
                            <?php ?>
                            <?php if($error!="") {?>
                                <div style="padding:5px; border-radius:3px;width:370px;margin-bottom:20px;color:red">
                                    <?php echo $error ?>
                                </div>
                            <?php }?>
                            <button type="submit" class="site-btn">Register Now</button>
                        </form>
                         
                    </div>
                </div>
                 <div class="col-lg-6">
                    <div class="login__register">
                        <h3>Already have an account?</h3>
                        <a href="login.php" class="primary-btn">Log In</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Signup Section End -->

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