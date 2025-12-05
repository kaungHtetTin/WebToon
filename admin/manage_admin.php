<?php

include('config.php');
require_once('includes/image_helper.php');

session_start();



$message = [];
$is_edit_mode = isset($_GET['update']) && !empty($_GET['update']);

// Handle form submission for both add and update
if(isset($_POST['update_users']) || isset($_POST['add_admin'])){

   $pid = isset($_POST['pid']) ? $_POST['pid'] : null;

   $username = $_POST['username'];
   $username = filter_var($username, FILTER_SANITIZE_STRING);

   $email = $_POST['email'];
   $email = filter_var($email, FILTER_SANITIZE_STRING);
   
   $phone = $_POST['phone'];
   $phone = filter_var($phone, FILTER_SANITIZE_STRING);

   // Handle password - if updating and password is empty, keep old password
   $password = '';
   if($is_edit_mode && $pid && empty($_POST['password'])){
       // Get current password from database
       $select_current = $conn->prepare("SELECT password FROM `admin` WHERE id = ?");
       $select_current->execute([$pid]);
       $current_data = $select_current->fetch(PDO::FETCH_ASSOC);
       $password = $current_data['password'] ?? '';
   } else {
       $password = md5($_POST['password']);
       $password = filter_var($password, FILTER_SANITIZE_STRING);
   }
   
   // Handle checkbox - if checked, value is 1, otherwise 0
   $is_active = isset($_POST['is_active']) && $_POST['is_active'] == 'on' ? 1 : 0;

   // Handle image_url upload
   $image_url_folder = '../uploads/images/admin/';
   $final_image_url = '';
   $upload_success = true;
   
   // Create directory if it doesn't exist
   if (!file_exists($image_url_folder)) {
       mkdir($image_url_folder, 0755, true);
   }
   
   // Get current image_url from database if no new file uploaded (only for update)
   if(isset($_FILES['image_url']['name']) && !empty($_FILES['image_url']['name']) && isset($_FILES['image_url']['error']) && $_FILES['image_url']['error'] == UPLOAD_ERR_OK){
       $image_url = $_FILES['image_url']['name'];
       $image_url_size = $_FILES['image_url']['size'];
       $image_url_tmp_name = $_FILES['image_url']['tmp_name'];
       
       if($image_url_size > 12000000){
           $message[] = 'image_url size is too large!';
           $upload_success = false;
       }else{
           $time = time();
           $file_extension = pathinfo($image_url, PATHINFO_EXTENSION);
           $file_name = pathinfo($image_url, PATHINFO_FILENAME);
           $unique_file = $file_name . "_" . $time . "." . $file_extension;
           
           if(move_uploaded_file($image_url_tmp_name, $image_url_folder.$unique_file)){
               $final_image_url = "/uploads/images/admin/".$unique_file;
           }else{
               $message[] = 'Failed to upload image!';
               $upload_success = false;
           }
       }
   }else{
       // Get current image_url from database (only for update mode)
       if($is_edit_mode && $pid){
           $select_current = $conn->prepare("SELECT image_url FROM `admin` WHERE id = ?");
           $select_current->execute([$pid]);
           $current_data = $select_current->fetch(PDO::FETCH_ASSOC);
           $final_image_url = $current_data['image_url'] ?? '';
       }
   }

   // Only proceed with database operation if upload was successful or no file was uploaded
   if($upload_success){
       if($is_edit_mode && $pid){
           // Update existing admin
           $update_product = $conn->prepare("UPDATE `admin` SET username = ?,  email = ?, phone = ?, password = ?,  is_active = ?,  image_url = ? WHERE id = ?");
           $update_product->execute([$username, $email, $phone, $password, $is_active, $final_image_url, $pid]);

           if($update_product){
               $message[] = 'updated successfully!';
               header('location:admin.php');
               exit;
           }
       } else {
           // Add new admin
           $add_admin = $conn->prepare("INSERT INTO `admin` (username, email, phone, password, is_active, image_url) VALUES (?, ?, ?, ?, ?, ?)");
           $add_admin->execute([$username, $email, $phone, $password, $is_active, $final_image_url]);

           if($add_admin){
               $message[] = 'Admin added successfully!';
               header('location:admin.php');
               exit;
           }
       }
   }
   

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Dashboard - NiceAdmin Bootstrap Template</title>
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

<body>

<?php include 'admin_header.php'; ?>



<?php include 'admin_sidebar.php'; ?>





  <main id="main" class="main">


    <section class="section dashboard">
      <div class="row">

        
        <div class="col-lg-12">
          <div class="row">

            

            <div class="col-lg-12">

              <div class="card">
                <div class="card-body">
                  <h5 class="card-title"><?= $is_edit_mode ? 'Update Admin Details' : 'Add New Admin'; ?></h5>
                  
                  <?php if (!empty($message)): ?>
                    <div class="alert alert-<?= strpos($message[0], 'success') !== false ? 'success' : 'danger'; ?>">
                      <?php foreach ($message as $msg): ?>
                        <div><?= htmlspecialchars($msg); ?></div>
                      <?php endforeach; ?>
                    </div>
                  <?php endif; ?>
                  
                  <?php
                      $fetch_products = null;
                      if($is_edit_mode){
                          $update_id = $_GET['update'];
                          $select_products = $conn->prepare("SELECT * FROM `admin` WHERE id = ?");
                          $select_products->execute([$update_id]);
                          if($select_products->rowCount() > 0){
                             $fetch_products = $select_products->fetch(PDO::FETCH_ASSOC);
                          } else {
                              echo '<div class="alert alert-danger">Admin not found!</div>';
                              $is_edit_mode = false;
                          }
                      }
                   ?>
                   
                  <form action="" method="POST" enctype="multipart/form-data">
                    <?php if($is_edit_mode && $fetch_products): ?>
                      <input type="hidden" name="pid" class="form-control" value="<?= $fetch_products['id']; ?>">
                    <?php endif; ?>
                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">Username <span class="text-danger">*</span></label>
                      <div class="col-sm-10">
                        <input type="text" name="username" class="form-control" value="<?= $fetch_products['username'] ?? ''; ?>" required>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">Phone</label>
                      <div class="col-sm-10">
                        <input type="number" name="phone" class="form-control" value="<?= $fetch_products['phone'] ?? ''; ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">Email <span class="text-danger">*</span></label>
                      <div class="col-sm-10">
                        <input type="email" name="email" class="form-control" value="<?= $fetch_products['email'] ?? ''; ?>" required>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">Password <span class="text-danger">*</span></label>
                      <div class="col-sm-10">
                        <input type="password" name="password" class="form-control" value="" <?= $is_edit_mode ? '' : 'required'; ?>>
                        <?php if($is_edit_mode): ?>
                          <small class="form-text text-muted">Leave blank to keep current password</small>
                        <?php endif; ?>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label class="col-sm-2 col-form-label">Status</label>
                      <div class="col-sm-10">
                        <div class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" id="is_active" name="is_active" <?= isset($fetch_products['is_active']) && $fetch_products['is_active'] == 1 ? 'checked' : ''; ?>>
                          <label class="form-check-label" for="is_active">
                            Active (Admin account will be active)
                          </label>
                        </div>
                        <small class="form-text text-muted">Uncheck to deactivate this admin account</small>
                      </div>
                    </div>

                    <?php if($is_edit_mode && $fetch_products && !empty($fetch_products['image_url'])): ?>
                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">Current Profile</label>
                      <div class="col-sm-10">
                        <img src="<?= htmlspecialchars(getImagePath($fetch_products['image_url'] ?? '', 'admin')); ?>" alt="Profile" style="height: 100px;width: 100px; border-radius: 8px; object-fit: cover;" onerror="this.src='../img/placeholder.jpg'">
                      </div>
                    </div>
                    <?php endif; ?>
                    
                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">Profile Image</label>
                      <div class="col-sm-10">
                        <input type="file" name="image_url" class="form-control" accept="image/*">
                        <small class="form-text text-muted">Upload a profile image (optional)</small>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label class="col-sm-2 col-form-label"></label>
                      <div class="col-sm-10">
                        <?php if($is_edit_mode): ?>
                          <button type="submit" name="update_users" class="btn btn-primary">
                            <i class="bi bi-save"></i> Update Admin
                          </button>
                        <?php else: ?>
                          <button type="submit" name="add_admin" class="btn btn-primary">
                            <i class="bi bi-plus-circle"></i> Add Admin
                          </button>
                        <?php endif; ?>
                        <a href="admin.php" class="btn btn-outline-secondary ms-2">
                          <i class="bi bi-arrow-left"></i> Back
                        </a>
                      </div>
                    </div>

                  </form>
                </div>
              </div>

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