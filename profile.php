<?php
session_start();

include('classes/connect.php');
include('classes/user.php');
$User=new User();

if(isset($_SESSION['webtoon_userid'])){
    $user_id=$_SESSION['webtoon_userid'];
    $user=$User->details($user_id);
}else{

}

$page_name="Profiles";


if($_SERVER['REQUEST_METHOD']=="POST"){
    $User->update($_POST,$_FILES);

    $user_id=$_SESSION['webtoon_userid'];
    $user=$User->details($user_id);
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

    <!-- Breadcrumb Begin -->
    <div class="breadcrumb-option">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb__links">
                        <a href="./index.php"><i class="fa fa-home"></i> Home</a>
                       
                        <span>Profile</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Breadcrumb End -->

    <!-- Anime Section Begin -->
    <section class="anime-details spad">
        <div class="container">
            <div class="anime__details__content">
                <div class="row">
                    <div class="col-lg-3">
                        <img id="img_profile" src="<?php echo $user['image_url']; ?>" alt="" style="width:150px; height:150px;border-radius:50px;cursor:pointer">
                        <br>
                        <i id="icon_camera" class="fa fa-camera" style="color:white;font-size:30px;cursor:pointer;margin-left:150px;display:none"></i>
                    </div>
                    
                    <div class="col-lg-9">
                        <div class="anime__details__text">
                            <div class="anime__details__title">
                                <h3> <?php echo $user['first_name']." ".$user['last_name'] ?></h3>
                            </div>

                            <span id="btn_edit" style="color:blue;cursor:pointer">Edit Profile</span>
                            <span id="btn_view" style="color:blue;cursor:pointer;display:none">View</span>
                          
                            <div class="anime__details__widget" id="profile_mode">
                                <div class="row">
                                    <div class="col-lg-6 col-md-6">
                                        <ul>
                                            <li>
                                                <span>Email or phone:</span> <?php echo $user['email'] ?>
                                            </li>
                                            <li><span>Phone:</span> <?php echo $user['phone'] ?></li>
                                            <li><span>Webtoon Point:</span> <?php echo $user['point'] ?> <a href="vip_register.php" style="margin-left:50px;">(Get more)</a> </li>
                                            <li><span>Join At:</span> <?php echo date('d M, Y',strtotime($user['date'])); ?> </li>
                                        </ul>
                                    </div>

                                </div>
                            </div>

                            <div id="edit_mode"  class="login__form" style="display:none">
                            <br><br>
                                <form action="" method="post" enctype="multipart/form-data">

                                    <input type="file" name="myfile" id="input_profile" style="display:none">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id'] ?>">

                                    <div class="input__item">
                                        <input type="text" placeholder="please enter your first name" name="first_name"value="<?php echo $user['first_name'] ?>">
                                    </div>
                                    <div class="input__item">
                                        <input type="text" placeholder="please enter your last name" name="last_name" value="<?php echo $user['last_name'] ?>">
                                    </div>
                                     <div class="input__item">
                                        <input type="text" placeholder="please enter your phone" name="phone" value="<?php echo $user['phone'] ?>">
                                    </div>
                                    
                                    <button class="site-btn" type="submit">Update</button>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Anime Section End -->

        <!-- Footer Section Begin -->
        <?php 
            include('layouts/footer.php');
        ?>


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
                let edit_mode = false;
                $('#btn_edit').click(()=>{
                    $('#btn_view').show();
                    $('#btn_edit').hide();

                    $('#edit_mode').show();
                    $('#profile_mode').hide();
                    $('#icon_camera').show();
                    edit_mode = true;

                })

                $('#btn_view').click(()=>{
                    $('#btn_view').hide();
                    $('#btn_edit').show();

                    $('#edit_mode').hide();
                    $('#profile_mode').show();
                    $('#icon_camera').hide();
                    edit_mode = false;

                })

                $('#img_profile').click(()=>{
                    if(edit_mode) $('#input_profile').click();
                })

                $('#icon_camera').click(()=>{
                    if(edit_mode) $('#input_profile').click();
                })

                 $('#input_profile').change(()=>{

                     
                    var files=$('#input_profile').prop('files');
                    var file=files[0];
                        
                    var reader = new FileReader();

                    reader.onload = function (e) {
                        imageSrc=e.target.result;
                        $('#img_profile').attr('src', imageSrc);
                        $('#img_include').val("true");
                    };

                    reader.readAsDataURL(file);
                        
                });
            })
        </script>
  

    </body>

    </html>