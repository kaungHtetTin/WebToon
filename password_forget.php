<?php
session_start();

include('classes/connect.php');
include('classes/auth.php');
include('classes/util.php');
include('classes/user.php');

$page_name="Login";
$Auth=new Auth();
$error="";

$User=new User();
if(isset($_SESSION['webtoon_userid'])){
    $user=$User->details($_SESSION['webtoon_userid']);
    header('Location:index.php');
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
                        <h2>Forget Password</h2>
                        <p>Welcome to the official Anime blog.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Normal Breadcrumb End -->

    <!-- Login Section Begin -->
    <section class="login spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="login__form" id="login_form">
                        <div id="pb_loading" style="padding:5px; border-radius:3px;width:370px;margin-bottom:20px;color:white;display:none">
                            Please wait
                        </div>
                        <div id="error" style="padding:5px; border-radius:3px;width:370px;margin-bottom:20px;color:red;display:none">
                            This is error!
                        </div>
                        <div id="searching_layout">
                            <h3>Search your account</h3>
                            <form>
                                <div class="input__item">
                                    <input type="text" placeholder="Email address" id="input_email">
                                    <span class="icon_mail"></span>
                                </div>

                                
                            
                            </form>
                            <button id="btn_search" class="site-btn">Search</button>
                        </div>


                        <div id="profile_layout" style="display:none">
                            <img id="profile_img" src="img/placeholder.jpg" alt="" style="width:100px;height:100px; border-radius:50px;"> <br><br>
                            
                            <h4 id="name" style="color:white">Kaung Htet Tin</h4>
                            <h6 id="gmail" style="color:white">kaung@gmail.com</h6>
                            
                            <br><br>
                            <p style="color:white">Get OPT to reset your password</p>
                            <button id="btn_getotp" class="site-btn">Get Now</button>
                        </div>

                         <div id="otp_layout" style="display:none">
                             <br>
                             <p style="color:white">We have sent OTP to your email and please enter the code to verity.</p>
                            <form action="">
                                <div class="input__item">
                                <input type="text" placeholder="Enter your OTP" id="input_opt">
                                <span class="icon_mail"></span>
                            </div>
                            </form>
                                <br>
                            
                            <button id="btn_confirm" class="site-btn">Confirm</button>
                        </div>

                        <div id="reset_layout" style="display:none">
                             <br>
                             <p style="color:white">Please enter your new password</p>
                            <form action="">
                                <div class="input__item">
                                <input type="password" placeholder="Enter your password" id="input_password">
                                 <span class="icon_lock"></span>
                            </div>
                            </form>
                            <br>
                            
                            <button id="btn_reset" class="site-btn">Reset</button>
                        </div>

                    </div>

                    
                </div>
                
            </div>
            <!-- <div class="login__social">
                <div class="row d-flex justify-content-center">
                    <div class="col-lg-6">
                        <div class="login__social__links">
                            <span>or</span>
                            <ul>
                                <li><a href="#" class="facebook"><i class="fa fa-facebook"></i> Sign in With
                                Facebook</a></li>
                                <li><a href="#" class="google"><i class="fa fa-google"></i> Sign in With Google</a></li>
                                <li><a href="#" class="twitter"><i class="fa fa-twitter"></i> Sign in With Twitter</a>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div> -->
        </div>
    </section>
    <!-- Login Section End -->

<!-- Footer Section Begin -->
    <?php 
        include('layouts/footer.php');
    ?>
  <!-- Footer Section End -->


    <!-- Js Plugins -->
    <script src="js/jquery-3.3.1.min.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/player.js"></script>
    <script src="js/jquery.nice-select.min.js"></script>
    <script src="js/mixitup.min.js"></script>
    <script src="js/jquery.slicknav.js"></script>
    <script src="js/owl.carousel.min.js"></script>
    <script src="js/main.js"></script>

    <script>

        $(document).ready(()=>{
            
            let user,code;
            $('#btn_search').click(()=>{

                $('#pb_loading').hide();
                $('#error').hide();

                let email = $('#input_email').val();

                if(email==""){
                    $('#error').html("Please enter your email address");
                    $('#error').show();
                    return;
                }

                $('#pb_loading').html('Searching ...');
                $('#pb_loading').show();

                $.get(`api/users/search.php?email=${email}`,(data,status)=>{
                    $('#pb_loading').hide();
                    data = JSON.parse(data);
                    console.log(data);
                    if(data.status=='success'){
                        $('#profile_layout').show();
                        $('#searching_layout').hide();
                        user = data.user
                        $('#name').html(user.first_name + user.last_name);
                        $('#gmail').html(user.email);
                        $('#profile_img').attr('src',user.image_url)

                    }else{
                        $('#error').html(data.msg);
                        $('#error').show();
                    }
                     
                });


            })

            $('#btn_getotp').click(()=>{
                $('#pb_loading').html('Please wait ...');
                $('#pb_loading').show();

                $.get(`api/users/get-otp.php?email=${user.email}`,(data,status)=>{
                    $('#pb_loading').hide();
                    $('#profile_layout').hide();
                    $('#otp_layout').show();               
                });
            })

            $('#btn_confirm').click(()=>{
                $('#pb_loading').html('Please wait ...');
                $('#pb_loading').show();
                $('#error').hide();
                code = $('#input_opt').val();
                $.get(`api/users/confirm-otp.php?email=${user.email}&code=${code}`,(data,status)=>{
                    console.log(data);
                    data = JSON.parse(data);
                     $('#pb_loading').hide();
                    if(data.status=="success"){
                       
                        $('#otp_layout').hide();
                        $('#reset_layout').show();

                    }else{
                        $('#error').html("Wrong OTP. Please try again.");
                        $('#error').show();
                    }

                    
                });

            })

            $('#btn_reset').click(()=>{
    
                $('#pb_loading').html('Please wait ...');
                $('#pb_loading').show();
                $('#error').hide();

                let req = {};
                req.email = user.email;
                req.password = $('#input_password').val();
                req.code = code;

                $.post(`api/users/password-reset.php`,req,(data,status)=>{
                    console.log(data);
                    data = JSON.parse(data);
                     $('#pb_loading').hide();
                    if(data.status=="success"){
                       
                        window.location.href="login.php";
                        

                    }else{
                        $('#error').html("Unexpected error! Please try again.");
                        $('#error').show();
                    }

                    
                });
            })
        })

    </script>

</body>

</html>