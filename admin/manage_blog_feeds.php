<?php

include('config.php');
require_once('includes/image_helper.php');

session_start();


if(isset($_POST['update_blog_feeds'])){

   $pid = $_POST['pid'];

   $title = $_POST['title'];
   $title = filter_var($title, FILTER_SANITIZE_STRING);

   $body = $_POST['body'];
   $body = filter_var($body, FILTER_SANITIZE_STRING);


   // Handle image upload
   $image_folder = '../uploads/images/blog_feeds/';
   $final_image_url = '';
   $upload_success = true;
   
   // Create directory if it doesn't exist
   if (!file_exists($image_folder)) {
       mkdir($image_folder, 0755, true);
   }
   
   // Get current image from database if no new file uploaded
   if(isset($_FILES['image']['name']) && !empty($_FILES['image']['name']) && isset($_FILES['image']['error']) && $_FILES['image']['error'] == UPLOAD_ERR_OK){
       $image = $_FILES['image']['name'];
       $image_size = $_FILES['image']['size'];
       $image_tmp_name = $_FILES['image']['tmp_name'];
       
       if($image_size > 12000000){
           $message[] = 'image size is too large!';
           $upload_success = false;
       }else{
           $time = time();
           $file_extension = pathinfo($image, PATHINFO_EXTENSION);
           $file_name = pathinfo($image, PATHINFO_FILENAME);
           $unique_file = $file_name . "_" . $time . "." . $file_extension;
           
           if(move_uploaded_file($image_tmp_name, $image_folder.$unique_file)){
               $final_image_url = "/uploads/images/blog_feeds/".$unique_file;
           }else{
               $message[] = 'Failed to upload image!';
               $upload_success = false;
           }
       }
   }else{
       // Get current image from database
       $select_current = $conn->prepare("SELECT image FROM `blog_feeds` WHERE id = ?");
       $select_current->execute([$pid]);
       $current_data = $select_current->fetch(PDO::FETCH_ASSOC);
       $final_image_url = $current_data['image'] ?? '';
   }
   
   // Only proceed with database update if upload was successful or no file was uploaded
   if($upload_success){
       $update_product = $conn->prepare("UPDATE `blog_feeds` SET title = ?, body = ?, image = ? WHERE id = ?");
       $update_product->execute([$title, $body, $final_image_url, $pid]);

       if($update_product){
           $message[] = 'updated successfully!';
           header('location:blog_feeds.php');
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
                  <h5 class="card-title">Update Categories by Admin</h5>
                  <?php
                      $update_id = $_GET['update'];
                      $select_products = $conn->prepare("SELECT * FROM `blog_feeds` WHERE id = ?");
                      $select_products->execute([$update_id]);
                      if($select_products->rowCount() > 0){
                         while($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)){ 
                   ?>
                  
                  <form action="" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="pid" class="form-control" value="<?= $fetch_products['id']; ?>">


                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">title</label>
                      <div class="col-sm-10">
                        <input type="text" name="title" class="form-control" value="<?= $fetch_products['title']; ?>">
                        

                      </div>
                    </div>
                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">body</label>
                      <div class="col-sm-10">
                        <input type="text" name="body" class="form-control" value="<?= $fetch_products['body']; ?>">
                      </div>
                    </div>
                   
                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">Old Image</label>
                      <div class="col-sm-10">
                        <img src="<?= htmlspecialchars(getImagePath($fetch_products['image'] ?? '', 'blog_feeds')); ?>" alt="Profile" style="height: 100px;width: 100px;" onerror="this.src='../img/placeholder.jpg'">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">image</label>
                      <div class="col-sm-10">
                        <input type="file"  name="image" class="form-control">
                      </div>
                    </div>
                    

                    <div class="row mb-3">
                      <label class="col-sm-2 col-form-label">Update Blog Feeds</label>
                      <div class="col-sm-10">
                        <button type="submit" name="update_blog_feeds" class="btn btn-primary">Update Blog Feeds</button>
                      </div>
                    </div>

                  </form>
                  <?php
                       }
                    }else{
                       echo '<p class="empty">no blog feeds found!</p>';
                    }
                 ?>
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