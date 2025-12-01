<?php

include('config.php');

session_start();

// Optional preselected blog id from query
$preselected_blog_id = null;
$preselected_blog_title = null;
if(isset($_GET['blog_id'])){
    $preselected_blog_id = filter_var($_GET['blog_id'], FILTER_SANITIZE_NUMBER_INT);
    if($preselected_blog_id === '' || !is_numeric($preselected_blog_id)){
        $preselected_blog_id = null;
    } else {
        // Fetch blog title for display
        $stmt = $conn->prepare("SELECT title FROM `blogs` WHERE id = ?");
        $stmt->execute([$preselected_blog_id]);
        if($stmt->rowCount() > 0){
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            $preselected_blog_title = $row['title'] ?? null;
        }
    }
}

if(isset($_POST['add_blog_feeds'])){

   $blog_id = $_POST['blog_id'];
   $blog_id = filter_var($blog_id, FILTER_SANITIZE_NUMBER_INT);

   $title = $_POST['title'];
   $title = filter_var($title, FILTER_SANITIZE_STRING);

   $body = $_POST['body'];
   $body = filter_var($body, FILTER_SANITIZE_STRING);

   // Handle image upload
   $image_folder = '../uploads/images/blog_feeds/';
   $final_image_url = '';
   $upload_error = '';
   
   // Create directory if it doesn't exist
   if (!file_exists($image_folder)) {
       mkdir($image_folder, 0755, true);
   }
   
   // Validate image
   if(isset($_FILES['image']) && !empty($_FILES['image']['name']) && $_FILES['image']['error'] !== UPLOAD_ERR_NO_FILE){
       if($_FILES['image']['error'] !== UPLOAD_ERR_OK){
           switch($_FILES['image']['error']){
               case UPLOAD_ERR_INI_SIZE:
               case UPLOAD_ERR_FORM_SIZE:
                   $upload_error = 'Image file is too large!';
                   break;
               case UPLOAD_ERR_PARTIAL:
                   $upload_error = 'Image upload was incomplete!';
                   break;
               case UPLOAD_ERR_NO_TMP_DIR:
                   $upload_error = 'Missing temporary folder for image!';
                   break;
               case UPLOAD_ERR_CANT_WRITE:
                   $upload_error = 'Failed to write image to disk!';
                   break;
               default:
                   $upload_error = 'Image upload failed!';
           }
       } else {
           $image = $_FILES['image']['name'];
           $image_size = $_FILES['image']['size'];
           $image_tmp_name = $_FILES['image']['tmp_name'];
           
           // Validate file size (12MB max)
           if($image_size > 12000000){
               $upload_error = 'Image size is too large! Maximum size is 12MB.';
           } else {
               // Get file extension and validate
               $file_extension = strtolower(pathinfo($image, PATHINFO_EXTENSION));
               $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
               
               if(!in_array($file_extension, $allowed_extensions)){
                   $upload_error = 'Invalid image format! Allowed formats: JPG, JPEG, PNG, GIF, WEBP.';
               } else {
                   // Validate MIME type
                   $finfo = finfo_open(FILEINFO_MIME_TYPE);
                   $mime_type = finfo_file($finfo, $image_tmp_name);
                   finfo_close($finfo);
                   
                   $allowed_mime_types = [
                       'image/jpeg',
                       'image/jpg',
                       'image/png',
                       'image/gif',
                       'image/webp'
                   ];
                   
                   if(!in_array($mime_type, $allowed_mime_types)){
                       $upload_error = 'Invalid image file type! Please upload a valid image.';
                   } else {
                       // Sanitize filename
                       $file_name = pathinfo($image, PATHINFO_FILENAME);
                       $file_name = preg_replace('/[^a-zA-Z0-9_-]/', '_', $file_name);
                       $file_name = preg_replace('/_+/', '_', $file_name);
                       $file_name = trim($file_name, '_');
                       
                       if(empty($file_name)){
                           $file_name = 'blog_feed_image';
                       }
                       
                       if(strlen($file_name) > 100){
                           $file_name = substr($file_name, 0, 100);
                       }
                       
                       // Generate unique filename
                       $time = time();
                       $random = mt_rand(1000, 9999);
                       $unique_file = $file_name . '_' . $time . '_' . $random . '.' . $file_extension;
                       
                       // Ensure filename is unique
                       $counter = 0;
                       while(file_exists($image_folder . $unique_file) && $counter < 100){
                           $counter++;
                           $unique_file = $file_name . '_' . $time . '_' . $random . '_' . $counter . '.' . $file_extension;
                       }
                       
                       // Move uploaded file
                       if(move_uploaded_file($image_tmp_name, $image_folder . $unique_file)){
                           $final_image_url = "/uploads/images/blog_feeds/" . $unique_file;
                       } else {
                           $upload_error = 'Failed to save image file!';
                       }
                   }
               }
           }
       }
       
       if(!empty($upload_error)){
           $message[] = $upload_error;
       }
   } else {
       $upload_error = 'Please select an image file!';
       $message[] = $upload_error;
   }

   $select_products = $conn->prepare("SELECT * FROM `blog_feeds` WHERE title = ? AND blog_id = ?");
   $select_products->execute([$title, $blog_id]);

   if($select_products->rowCount() > 0){
      $message[] = 'blog feed already exist for this blog!';
   }else{

      // Only insert if upload successful
      if(empty($upload_error) && !empty($final_image_url)){
          $insert_products = $conn->prepare("INSERT INTO `blog_feeds`(blog_id, title, body, image ) VALUES(?,?,?,?)");
          $insert_products->execute([$blog_id, $title, $body, $final_image_url]);
          
          if($insert_products){
              $message[] = 'registered successfully!';
              // Redirect back to blog_feeds filtered by this blog
              header('location:blog_feeds.php?blog_id=' . $blog_id);
              exit;
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
                <?php if($preselected_blog_id): ?>
                  <input type="hidden" name="blog_id" value="<?= htmlspecialchars($preselected_blog_id); ?>">
                  <div class="row mb-3">
                    <label class="col-sm-2 col-form-label">Blog</label>
                    <div class="col-sm-10 d-flex align-items-center">
                      <strong>
                        <?= htmlspecialchars($preselected_blog_title ?? ('Blog #' . $preselected_blog_id)); ?>
                      </strong>
                    </div>
                  </div>
                <?php else: ?>
                <div class="row mb-3">
                  <label class="col-sm-2 col-form-label">Blog</label>
                  <div class="col-sm-10">
                    <select name="blog_id" class="form-select" aria-label="Select blog" required>
                      <option value="">Select a blog</option>
                      <?php
                        $show_products = $conn->prepare("SELECT * FROM `blogs` ORDER BY id DESC");
                        $show_products->execute();
                        if($show_products->rowCount() > 0){
                           while($fetch_products = $show_products->fetch(PDO::FETCH_ASSOC)){  
                     ?>
                      <option value="<?= $fetch_products['id']; ?>"><?= htmlspecialchars($fetch_products['title']); ?></option>
                     <?php
                          }
                       }else{
                          echo '<option disabled>No blogs available</option>';
                       }
                       ?>
                    </select>
                  </div>
                </div> 
                <?php endif; ?>


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