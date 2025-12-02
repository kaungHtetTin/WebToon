<?php
session_start();

include('classes/connect.php');
include('classes/user.php');
include('classes/util.php');
$User=new User();
$Util=new Util();

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
    <style>
        .profile-container {
            background: #1d1e39;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
            margin-bottom: 30px;
        }
        
        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 30px;
            border-bottom: 2px solid rgba(255,255,255,0.1);
        }
        
        .profile-avatar-wrapper {
            position: relative;
            margin-right: 30px;
        }
        
        .profile-avatar {
            width: 180px;
            height: 180px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid #ff5b00;
            box-shadow: 0 8px 20px rgba(255,91,0,0.3);
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .profile-avatar:hover {
            transform: scale(1.05);
            box-shadow: 0 10px 25px rgba(255,91,0,0.5);
        }
        
        .camera-icon {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background: #ff5b00;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 24px;
            cursor: pointer;
            box-shadow: 0 4px 10px rgba(0,0,0,0.3);
            transition: all 0.3s ease;
            opacity: 0;
            visibility: hidden;
        }
        
        .camera-icon.show {
            opacity: 1;
            visibility: visible;
        }
        
        .camera-icon:hover {
            background: #e65200;
            transform: scale(1.1);
        }
        
        .profile-info {
            flex: 1;
        }
        
        .profile-name {
            font-size: 32px;
            font-weight: 700;
            color: #ffffff;
            margin-bottom: 15px;
            text-transform: capitalize;
        }
        
        .profile-actions {
            margin-top: 20px;
        }
        
        .btn-action {
            background: transparent;
            border: 2px solid #ff5b00;
            color: #ff5b00;
            padding: 10px 25px;
            border-radius: 5px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            display: inline-block;
            margin-right: 10px;
        }
        
        .btn-action:hover {
            background: #ff5b00;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(255,91,0,0.3);
        }
        
        .info-card {
            background: rgba(255,255,255,0.05);
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 20px;
            border: 1px solid rgba(255,255,255,0.1);
            transition: all 0.3s ease;
        }
        
        .info-card:hover {
            background: rgba(255,255,255,0.08);
            border-color: rgba(255,91,0,0.3);
            transform: translateY(-2px);
        }
        
        .info-item {
            display: flex;
            align-items: center;
            padding: 15px 0;
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        
        .info-item:last-child {
            border-bottom: none;
        }
        
        .info-label {
            font-weight: 600;
            color: #ff5b00;
            min-width: 150px;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .info-value {
            color: #ffffff;
            font-size: 16px;
            flex: 1;
        }
        
        .info-value a {
            color: #ff5b00;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .info-value a:hover {
            color: #e65200;
            text-decoration: underline;
        }
        
        .edit-form-container {
            background: rgba(255,255,255,0.05);
            border-radius: 10px;
            padding: 30px;
            border: 1px solid rgba(255,255,255,0.1);
        }
        
        .form-group {
            margin-bottom: 25px;
        }
        
        .form-group label {
            display: block;
            color: #ff5b00;
            font-weight: 600;
            margin-bottom: 8px;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        
        .form-group input {
            width: 100%;
            padding: 15px 20px;
            background: rgba(255,255,255,0.1);
            border: 2px solid rgba(255,255,255,0.1);
            border-radius: 8px;
            color: #ffffff;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #ff5b00;
            background: rgba(255,255,255,0.15);
            box-shadow: 0 0 0 3px rgba(255,91,0,0.1);
        }
        
        .form-group input::placeholder {
            color: rgba(255,255,255,0.5);
        }
        
        .btn-submit {
            background: #ff5b00;
            color: white;
            border: none;
            padding: 15px 40px;
            border-radius: 8px;
            font-weight: 700;
            font-size: 16px;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        
        .btn-submit:hover {
            background: #e65200;
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(255,91,0,0.4);
        }
        
        .point-badge {
            display: inline-flex;
            align-items: center;
            background: rgba(255,91,0,0.2);
            padding: 8px 15px;
            border-radius: 20px;
            border: 1px solid #ff5b00;
        }
        
        .point-badge .point-value {
            font-weight: 700;
            color: #ff5b00;
            margin-right: 5px;
        }
        
        @media (max-width: 768px) {
            .profile-header {
                flex-direction: column;
                text-align: center;
            }
            
            .profile-avatar-wrapper {
                margin-right: 0;
                margin-bottom: 20px;
            }
            
            .profile-name {
                font-size: 24px;
            }
            
            .info-label {
                min-width: 120px;
                font-size: 12px;
            }
        }
    </style>
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

    <!-- Profile Section Begin -->
    <section class="anime-details spad">
        <div class="container">
            <div class="profile-container">
                <div class="profile-header">
                    <div class="profile-avatar-wrapper">
                        <img id="img_profile" class="profile-avatar" src="<?php echo $Util->normalizeImageUrl($user['image_url']); ?>" alt="Profile Picture">
                        <div id="icon_camera" class="camera-icon">
                            <i class="fa fa-camera"></i>
                        </div>
                    </div>
                    
                    <div class="profile-info">
                        <div class="profile-name">
                            <?php echo htmlspecialchars($user['first_name']." ".$user['last_name']); ?>
                        </div>
                        
                        <div class="profile-actions">
                            <span id="btn_edit" class="btn-action">Edit Profile</span>
                            <span id="btn_view" class="btn-action" style="display:none">View Profile</span>
                        </div>
                    </div>
                </div>

                <div id="profile_mode">
                    <div class="info-card">
                        <div class="info-item">
                            <div class="info-label">
                                <i class="fa fa-envelope"></i> Email
                            </div>
                            <div class="info-value"><?php echo htmlspecialchars($user['email']); ?></div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">
                                <i class="fa fa-phone"></i> Phone
                            </div>
                            <div class="info-value"><?php echo htmlspecialchars($user['phone'] ? $user['phone'] : 'Not provided'); ?></div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">
                                <i class="fa fa-coins"></i> Webtoon Points
                            </div>
                            <div class="info-value">
                                <span class="point-badge">
                                    <span class="point-value"><?php echo number_format($user['point']); ?></span>
                                    <span>Points</span>
                                </span>
                                <a href="vip_register.php" style="margin-left: 20px;">Get More Points</a>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <div class="info-label">
                                <i class="fa fa-calendar"></i> Member Since
                            </div>
                            <div class="info-value"><?php echo date('d M, Y', strtotime($user['created_at'])); ?></div>
                        </div>
                    </div>
                </div>

                <div id="edit_mode" class="edit-form-container" style="display:none">
                    <form action="" method="post" enctype="multipart/form-data">
                        <input type="file" name="myfile" id="input_profile" style="display:none" accept="image/*">
                        <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">

                        <div class="form-group">
                            <label for="first_name">First Name</label>
                            <input type="text" id="first_name" name="first_name" placeholder="Enter your first name" value="<?php echo htmlspecialchars($user['first_name']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="last_name">Last Name</label>
                            <input type="text" id="last_name" name="last_name" placeholder="Enter your last name" value="<?php echo htmlspecialchars($user['last_name']); ?>" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="text" id="phone" name="phone" placeholder="Enter your phone number" value="<?php echo htmlspecialchars($user['phone']); ?>">
                        </div>
                        
                        <button class="btn-submit" type="submit">
                            <i class="fa fa-save"></i> Update Profile
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <!-- Profile Section End -->

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
                    $('#icon_camera').addClass('show');
                    edit_mode = true;
                });

                $('#btn_view').click(()=>{
                    $('#btn_view').hide();
                    $('#btn_edit').show();
                    $('#edit_mode').hide();
                    $('#profile_mode').show();
                    $('#icon_camera').removeClass('show');
                    edit_mode = false;
                });

                $('#img_profile').click(()=>{
                    if(edit_mode) $('#input_profile').click();
                });

                $('#icon_camera').click(()=>{
                    if(edit_mode) $('#input_profile').click();
                });

                $('#input_profile').change(()=>{
                    var files = $('#input_profile').prop('files');
                    if(files && files.length > 0) {
                        var file = files[0];
                        var reader = new FileReader();

                        reader.onload = function (e) {
                            var imageSrc = e.target.result;
                            $('#img_profile').attr('src', imageSrc);
                        };

                        reader.readAsDataURL(file);
                    }
                });
            });
        </script>
  

    </body>

    </html>