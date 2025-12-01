<?php

include('config.php');
require_once('includes/image_helper.php');

session_start();

// Ensure admin is logged in
if(!isset($_SESSION['admin_id'])){
   header('location:login.php');
   exit;
}

$admin_id = $_SESSION['admin_id'];

if(isset($_POST['update_profile'])){

   $username = $_POST['username'];
   $username = filter_var($username, FILTER_SANITIZE_STRING);
  
   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   
   $phone = $_POST['phone'];
   $phone = filter_var($phone, FILTER_SANITIZE_STRING);

   $update_profile = $conn->prepare("UPDATE `admin` SET username = ?,  email = ?, phone = ? WHERE id = ?");
   $update_profile->execute([$username, $email, $phone, $admin_id]);

   $image_folder = '../uploads/images/admin/';
   $old_image = $_POST['old_image'];
   
   // Create directory if it doesn't exist
   if (!file_exists($image_folder)) {
       mkdir($image_folder, 0755, true);
   }

   if(isset($_FILES['image']['name']) && !empty($_FILES['image']['name']) && isset($_FILES['image']['error']) && $_FILES['image']['error'] == UPLOAD_ERR_OK){
       $image = $_FILES['image']['name'];
       $image_size = $_FILES['image']['size'];
       $image_tmp_name = $_FILES['image']['tmp_name'];
       
       if($image_size > 12000000){
           $message[] = 'image size is too large!';
       }else{
           // Generate unique filename to prevent overwrites
           $time = time();
           $file_extension = pathinfo($image, PATHINFO_EXTENSION);
           $file_name = pathinfo($image, PATHINFO_FILENAME);
           $unique_file = $file_name . "_" . $time . "." . $file_extension;
           $final_image_url = "/uploads/images/admin/".$unique_file;
           
           $update_image = $conn->prepare("UPDATE `admin` SET image_url = ? WHERE id = ?");
           $update_image->execute([$final_image_url, $admin_id]);
           if($update_image){
               if(move_uploaded_file($image_tmp_name, $image_folder.$unique_file)){
                   // Delete old image if it exists and is in the uploads folder
                   if(!empty($old_image) && file_exists('../uploads/images/admin/'.$old_image)){
                       unlink('../uploads/images/admin/'.$old_image);
                   }elseif(!empty($old_image) && file_exists('img/'.$old_image)){
                       unlink('img/'.$old_image);
                   }
                   $message[] = 'image updated successfully!';
               }else{
                   $message[] = 'Failed to upload image!';
               }
           };
       };
   };

}


if(isset($_POST['update_password'])){

   
   $old_pass = $_POST['old_pass'];
   $update_pass = md5($_POST['update_pass']);
   $update_pass = filter_var($update_pass, FILTER_SANITIZE_STRING);
   $new_pass = md5($_POST['new_pass']);
   $new_pass = filter_var($new_pass, FILTER_SANITIZE_STRING);
   $confirm_pass = md5($_POST['confirm_pass']);
   $confirm_pass = filter_var($confirm_pass, FILTER_SANITIZE_STRING);

   if(!empty($update_pass) AND !empty($new_pass) AND !empty($confirm_pass)){
      if($update_pass != $old_pass){
         $message[] = 'old password not matched!';
      }elseif($new_pass != $confirm_pass){
         $message[] = 'confirm password not matched!';
      }else{
         $update_pass_query = $conn->prepare("UPDATE `admin` SET password = ? WHERE id = ?");
         $update_pass_query->execute([$confirm_pass, $admin_id]);
         $message[] = 'password updated successfully!';
      }
   }

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Users / Profile - NiceAdmin Bootstrap Template</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="../img/logo_round2.png" rel="icon" type="image/png">
  <link href="../img/logo_round2.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/boxicons/css/boxicons.min.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.snow.css" rel="stylesheet">
  <link href="assets/vendor/quill/quill.bubble.css" rel="stylesheet">
  <link href="assets/vendor/remixicon/remixicon.css" rel="stylesheet">
  <link href="assets/vendor/simple-datatables/style.css" rel="stylesheet">

  <!-- Template Main CSS File -->
  <link href="assets/css/style.css" rel="stylesheet">

  <!-- =======================================================
  * Template Name: NiceAdmin - v2.2.2
  * Template URL: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>
<style type="text/css">

</style>

<body>

<?php include 'admin_header.php'; ?>

<?php include 'admin_sidebar.php'; ?>



  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Profile</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item">Admin</li>
          <li class="breadcrumb-item active">Profile</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section profile">
      <div class="row">
        <!-- Left: Compact profile card -->
        <div class="col-xl-4">
          <div class="card">
            <div class="card-body profile-card pt-4 d-flex flex-column align-items-center text-center">
              <?php
                  $select_profile = $conn->prepare("SELECT * FROM `admin` WHERE id = ?");
                  $select_profile->execute([$admin_id]);
                  $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
               ?>   

              <div class="user-image-container mb-3">
                <img src="<?= htmlspecialchars(getImagePath($fetch_profile['image_url'] ?? '', 'admin')); ?>" 
                     alt="Profile" 
                     class="user-avatar"
                     onerror="this.src='../img/placeholder.jpg'">
              </div>
              <h2 class="mb-1"><?= htmlspecialchars($fetch_profile['username'] ?? 'Admin'); ?></h2>
              <p class="mb-1 text-muted"><?= htmlspecialchars($fetch_profile['email'] ?? ''); ?></p>
              <p class="text-muted small mb-0"><?= htmlspecialchars($fetch_profile['phone'] ?? ''); ?></p>
            </div>
          </div>
        </div>

        <!-- Right: Tabs -->
        <div class="col-xl-8">

          <div class="card">
            <div class="card-body pt-3">
              <!-- Bordered Tabs -->
              <ul class="nav nav-tabs nav-tabs-bordered">

                <li class="nav-item">
                  <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-overview">Overview</button>
                </li>

                <li class="nav-item">
                  <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-edit">Edit Profile</button>
                </li>

                <li class="nav-item">
                  <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-change-password">Change Password</button>
                </li>

              </ul>
              <div class="tab-content pt-2">
                
                <div class="tab-pane fade show active profile-overview" id="profile-overview">
                   <?php
                      $select_profile = $conn->prepare("SELECT * FROM `admin` WHERE id = ?");
                      $select_profile->execute([$admin_id]);
                      $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
                   ?>  

                  <h5 class="card-title">Profile Details</h5>

                  <div class="row mb-2">
                    <div class="col-lg-3 col-md-4 label">Full Name</div>
                    <div class="col-lg-9 col-md-8"><?= htmlspecialchars($fetch_profile['username'] ?? ''); ?></div>
                  </div>

                  <div class="row mb-2">
                    <div class="col-lg-3 col-md-4 label">Email</div>
                    <div class="col-lg-9 col-md-8"><?= htmlspecialchars($fetch_profile['email'] ?? ''); ?></div>
                  </div>

                  <div class="row mb-2">
                    <div class="col-lg-3 col-md-4 label">Phone</div>
                    <div class="col-lg-9 col-md-8"><?= htmlspecialchars($fetch_profile['phone'] ?? ''); ?></div>
                  </div>
                  
                </div>

                <div class="tab-pane fade profile-edit pt-3" id="profile-edit">

                  <!-- Profile Edit Form -->
                  <form action="" method="POST" enctype="multipart/form-data">
                    <div class="row mb-3">
                      <label for="profileImage" class="col-md-4 col-lg-3 col-form-label">Profile Image</label>
                      <div class="col-md-8 col-lg-9">
                        <img src="<?= htmlspecialchars(getImagePath($fetch_profile['image_url'] ?? '', 'admin')); ?>" alt="Profile" onerror="this.src='../img/placeholder.jpg'">
                        
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="fullName" class="col-md-4 col-lg-3 col-form-label">Choose Picture</label>
                      <div class="col-md-8 col-lg-9">
                        <input type="file" name="image" accept="image/jpg, image/jpeg, image/png" class="box">
                        <input type="hidden" name="old_image" value="<?= $fetch_profile['image_url']; ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="fullName" class="col-md-4 col-lg-3 col-form-label">User Name</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="username" type="text" class="form-control" id="Name" value="<?= $fetch_profile['username']; ?>">
                      </div>
                    </div>

                    

                    <div class="row mb-3">
                      <label for="Phone" class="col-md-4 col-lg-3 col-form-label">Phone</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="phone" type="number" class="form-control" id="Phone" value="<?= $fetch_profile['phone']; ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="Email" class="col-md-4 col-lg-3 col-form-label">Email</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="email" type="email" class="form-control" id="Email" value="<?= $fetch_profile['email']; ?>">
                      </div>
                    </div>
                    
                    <div class="text-center">
                      <button type="submit" value="update profile" name="update_profile" class="btn btn-primary">Save Changes</button>
                    </div>
                  </form><!-- End Profile Edit Form -->

                </div>

                <div class="tab-pane fade pt-3" id="profile-change-password">
                  <!-- Change Password Form -->
                  <form  action="" method="POST" enctype="multipart/form-data">

                    <div class="row mb-3">
                      <label for="Old Password" class="col-md-4 col-lg-3 col-form-label">old password </label>
                      <div class="col-md-8 col-lg-9">
                        <input type="hidden" name="old_pass" value="<?= $fetch_profile['password']; ?>">                        
                        <input type="password" class="form-control" name="update_pass" placeholder="enter previous password" class="box">
                        
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="New Password" class="col-md-4 col-lg-3 col-form-label">new password </label>
                      <div class="col-md-8 col-lg-9">
                        
                        <input type="password" class="form-control" name="new_pass" placeholder="enter new password" class="box">
                        
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="Comfirm Password" class="col-md-4 col-lg-3 col-form-label">confirm password </label>
                      <div class="col-md-8 col-lg-9">
                        <input type="password" class="form-control" name="confirm_pass" placeholder="confirm new password" class="box">
                      </div>
                    </div>

                    <div class="text-center">
                      <button type="submit" class="btn btn-primary" name="update_password">Change Password</button>
                    </div>
                  </form><!-- End Change Password Form -->

                </div>

              </div><!-- End Bordered Tabs -->

            </div>
          </div>

        </div>
      </div>
    </section>

  </main><!-- End #main -->

  <?php include 'admin_footer.php'; ?>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/chart.js/chart.min.js"></script>
  <script src="assets/vendor/echarts/echarts.min.js"></script>
  <script src="assets/vendor/quill/quill.min.js"></script>
  <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

</body>

</html>