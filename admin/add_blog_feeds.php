<?php

include('config.php');

session_start();




if(isset($_POST['add_blog_feeds'])){

   $blog_id = $_POST['blog_id'];
   $blog_id = filter_var($blog_id, FILTER_SANITIZE_STRING);

   $title = $_POST['title'];
   $title = filter_var($title, FILTER_SANITIZE_STRING);

   $body = $_POST['body'];
   $body = filter_var($body, FILTER_SANITIZE_STRING);

   $image = $_FILES['image']['name'];
   $image = filter_var($image, FILTER_SANITIZE_STRING);
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = '../uploads/images/blog_feeds/';
   
   // Create directory if it doesn't exist
   if (!file_exists($image_folder)) {
       mkdir($image_folder, 0755, true);
   }
   
   // Generate unique filename to prevent overwrites
   $time = time();
   $file_extension = pathinfo($image, PATHINFO_EXTENSION);
   $file_name = pathinfo($image, PATHINFO_FILENAME);
   $unique_file = $file_name . "_" . $time . "." . $file_extension;
   $final_image_url = "/uploads/images/blog_feeds/".$unique_file;

  
   $select_products = $conn->prepare("SELECT * FROM `blog_feeds` WHERE title = ?");
   $select_products->execute([$title]);

   if($select_products->rowCount() > 0){
      $message[] = 'blog feed already exist!';
   }else{

      $insert_products = $conn->prepare("INSERT INTO `blog_feeds`(blog_id, title, body, image ) VALUES(?,?,?,?)");
      $insert_products->execute([$blog_id, $title, $body, $final_image_url]);
      

      if($insert_products){
            if($image_size > 12000000){
               $message[] = 'image size is too large!';
            }else{
               move_uploaded_file($image_tmp_name, $image_folder.$unique_file);
               $message[] = 'registered successfully!';
               header('location:blog_feeds.php');
            }
         }

   }

};

?>



<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Forms / Elements - NiceAdmin Bootstrap Template</title>
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

    <div class="pagetitle">
      <h1>Add New Blog Feeds by Admin</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item">Add </li>
          <li class="breadcrumb-item active">Blog Feeds</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">General Form Elements</h5>

              <!-- General Form Elements -->
              <form action="" method="POST" enctype="multipart/form-data">
                <div class="row mb-3">
                  <label class="col-sm-2 col-form-label">blog_id</label>
                  <div class="col-sm-10">
                    <select name="blog_id" class="form-select" aria-label="Default select example">
                      <option selected>Open this select menu</option>
                      <?php
                        $show_products = $conn->prepare("SELECT * FROM `blogs`");
                        $show_products->execute();
                        if($show_products->rowCount() > 0){
                           while($fetch_products = $show_products->fetch(PDO::FETCH_ASSOC)){  
                     ?>
                      
                      <option value="<?= $fetch_products['id']; ?>" ><?= $fetch_products['title']; ?></option>
                     <?php
                          }
                       }else{
                          echo '<p class="empty">now books added yet!</p>';
                       }
                       ?>
                    </select>
                  </div>
                </div> 


                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">title</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="title">
                      </div>
                    </div>
                
                    <div class="row mb-3">
                      <label for="inputNumber" class="col-sm-2 col-form-label">image</label>
                      <div class="col-sm-10">
                        <input class="form-control" type="file" name="image">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="inputEmail" class="col-sm-2 col-form-label">body</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" name="body">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label class="col-sm-2 col-form-label">Submit Button</label>
                      <div class="col-sm-10">
                        <button type="submit" name="add_blog_feeds" class="btn btn-primary">Submit Form</button>
                      </div>
                    </div>

              </form><!-- End General Form Elements -->

            </div>
          </div>

        </div>

        
      </div>
    </section>

  </main><!-- End #main -->

  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">
    <div class="copyright">
      &copy; Copyright <strong><span>NiceAdmin</span></strong>. All Rights Reserved
    </div>
    <div class="credits">
      <!-- All the links in the footer should remain intact. -->
      <!-- You can delete the links only if you purchased the pro version. -->
      <!-- Licensing information: https://bootstrapmade.com/license/ -->
      <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/ -->
      Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
    </div>
  </footer><!-- End Footer -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

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