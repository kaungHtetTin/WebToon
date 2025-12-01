<?php

include('config.php');

session_start();



if(isset($_POST['add_blogs'])){

   

   $title = $_POST['title'];
   $title = filter_var($title, FILTER_SANITIZE_STRING);

   $description = $_POST['description'];
   $description = filter_var($description, FILTER_SANITIZE_STRING);

   $date = $_POST['date'];
   $date = filter_var($date, FILTER_SANITIZE_STRING);

   // Handle image uploads with validation
   $image_url_folder = '../uploads/images/blogs/';
   $final_image_url = '';
   $final_cover_url = '';
   $upload_error = '';

   // Create directories if they don't exist
   if (!file_exists($image_url_folder)) {
       mkdir($image_url_folder, 0755, true);
   }

   // Validate main image
   if(isset($_FILES['image_url']) && !empty($_FILES['image_url']['name']) && $_FILES['image_url']['error'] !== UPLOAD_ERR_NO_FILE){
       if($_FILES['image_url']['error'] !== UPLOAD_ERR_OK){
           switch($_FILES['image_url']['error']){
               case UPLOAD_ERR_INI_SIZE:
               case UPLOAD_ERR_FORM_SIZE:
                   $upload_error = 'Main image file is too large!';
                   break;
               case UPLOAD_ERR_PARTIAL:
                   $upload_error = 'Main image upload was incomplete!';
                   break;
               case UPLOAD_ERR_NO_TMP_DIR:
                   $upload_error = 'Missing temporary folder for main image!';
                   break;
               case UPLOAD_ERR_CANT_WRITE:
                   $upload_error = 'Failed to write main image to disk!';
                   break;
               default:
                   $upload_error = 'Main image upload failed!';
           }
       } else {
           $image_url = $_FILES['image_url']['name'];
           $image_url_size = $_FILES['image_url']['size'];
           $image_url_tmp_name = $_FILES['image_url']['tmp_name'];

           // Validate file size (12MB max)
           if($image_url_size > 12000000){
               $upload_error = 'Main image size is too large! Maximum size is 12MB.';
           } else {
               // Get file extension and validate
               $image_extension = strtolower(pathinfo($image_url, PATHINFO_EXTENSION));
               $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

               if(!in_array($image_extension, $allowed_extensions)){
                   $upload_error = 'Invalid main image format! Allowed formats: JPG, JPEG, PNG, GIF, WEBP.';
               } else {
                   // Validate MIME type
                   $finfo = finfo_open(FILEINFO_MIME_TYPE);
                   $mime_type = finfo_file($finfo, $image_url_tmp_name);
                   finfo_close($finfo);

                   $allowed_mime_types = [
                       'image/jpeg',
                       'image/jpg',
                       'image/png',
                       'image/gif',
                       'image/webp'
                   ];

                   if(!in_array($mime_type, $allowed_mime_types)){
                       $upload_error = 'Invalid main image file type! Please upload a valid image.';
                   } else {
                       // Sanitize filename
                       $image_name = pathinfo($image_url, PATHINFO_FILENAME);
                       $image_name = preg_replace('/[^a-zA-Z0-9_-]/', '_', $image_name);
                       $image_name = preg_replace('/_+/', '_', $image_name);
                       $image_name = trim($image_name, '_');

                       if(empty($image_name)){
                           $image_name = 'blog_image';
                       }

                       if(strlen($image_name) > 100){
                           $image_name = substr($image_name, 0, 100);
                       }

                       // Generate unique filename
                       $time = time();
                       $random = mt_rand(1000, 9999);
                       $unique_image = $image_name . '_' . $time . '_' . $random . '.' . $image_extension;

                       // Ensure filename is unique
                       $counter = 0;
                       while(file_exists($image_url_folder . $unique_image) && $counter < 100){
                           $counter++;
                           $unique_image = $image_name . '_' . $time . '_' . $random . '_' . $counter . '.' . $image_extension;
                       }

                       // Move uploaded file
                       if(move_uploaded_file($image_url_tmp_name, $image_url_folder . $unique_image)){
                           $final_image_url = "/uploads/images/blogs/" . $unique_image;
                       } else {
                           $upload_error = 'Failed to save main image file!';
                       }
                   }
               }
           }
       }
       
       if(!empty($upload_error)){
           $message[] = $upload_error;
       }
   } else {
       $upload_error = 'Please select a main image file!';
       $message[] = $upload_error;
   }

   // Validate cover image
   if(isset($_FILES['cover_url']) && !empty($_FILES['cover_url']['name']) && $_FILES['cover_url']['error'] !== UPLOAD_ERR_NO_FILE){
       if($_FILES['cover_url']['error'] !== UPLOAD_ERR_OK){
           switch($_FILES['cover_url']['error']){
               case UPLOAD_ERR_INI_SIZE:
               case UPLOAD_ERR_FORM_SIZE:
                   $upload_error = 'Cover image file is too large!';
                   break;
               case UPLOAD_ERR_PARTIAL:
                   $upload_error = 'Cover image upload was incomplete!';
                   break;
               case UPLOAD_ERR_NO_TMP_DIR:
                   $upload_error = 'Missing temporary folder for cover image!';
                   break;
               case UPLOAD_ERR_CANT_WRITE:
                   $upload_error = 'Failed to write cover image to disk!';
                   break;
               default:
                   $upload_error = 'Cover image upload failed!';
           }
       } else {
           $cover_url = $_FILES['cover_url']['name'];
           $cover_url_size = $_FILES['cover_url']['size'];
           $cover_url_tmp_name = $_FILES['cover_url']['tmp_name'];

           // Validate file size (12MB max)
           if($cover_url_size > 12000000){
               $upload_error = 'Cover image size is too large! Maximum size is 12MB.';
           } else {
               // Get file extension and validate
               $cover_extension = strtolower(pathinfo($cover_url, PATHINFO_EXTENSION));
               $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];

               if(!in_array($cover_extension, $allowed_extensions)){
                   $upload_error = 'Invalid cover image format! Allowed formats: JPG, JPEG, PNG, GIF, WEBP.';
               } else {
                   // Validate MIME type
                   $finfo = finfo_open(FILEINFO_MIME_TYPE);
                   $mime_type = finfo_file($finfo, $cover_url_tmp_name);
                   finfo_close($finfo);

                   $allowed_mime_types = [
                       'image/jpeg',
                       'image/jpg',
                       'image/png',
                       'image/gif',
                       'image/webp'
                   ];

                   if(!in_array($mime_type, $allowed_mime_types)){
                       $upload_error = 'Invalid cover image file type! Please upload a valid image.';
                   } else {
                       // Sanitize filename
                       $cover_name = pathinfo($cover_url, PATHINFO_FILENAME);
                       $cover_name = preg_replace('/[^a-zA-Z0-9_-]/', '_', $cover_name);
                       $cover_name = preg_replace('/_+/', '_', $cover_name);
                       $cover_name = trim($cover_name, '_');

                       if(empty($cover_name)){
                           $cover_name = 'blog_cover';
                       }

                       if(strlen($cover_name) > 100){
                           $cover_name = substr($cover_name, 0, 100);
                       }

                       // Generate unique filename
                       $time2 = time();
                       $random2 = mt_rand(1000, 9999);
                       $unique_cover = $cover_name . '_' . $time2 . '_' . $random2 . '.' . $cover_extension;

                       // Ensure filename is unique
                       $counter2 = 0;
                       while(file_exists($image_url_folder . $unique_cover) && $counter2 < 100){
                           $counter2++;
                           $unique_cover = $cover_name . '_' . $time2 . '_' . $random2 . '_' . $counter2 . '.' . $cover_extension;
                       }

                       // Move uploaded file
                       if(move_uploaded_file($cover_url_tmp_name, $image_url_folder . $unique_cover)){
                           $final_cover_url = "/uploads/images/blogs/" . $unique_cover;
                       } else {
                           $upload_error = 'Failed to save cover image file!';
                       }
                   }
               }
           }
       }
       
       if(!empty($upload_error)){
           $message[] = $upload_error;
       }
   } else {
       $upload_error = 'Please select a cover image file!';
       $message[] = $upload_error;
   }

  
   $select_products = $conn->prepare("SELECT * FROM `blogs` WHERE title = ?");
   $select_products->execute([$title]);

   if($select_products->rowCount() > 0){
      $message[] = 'blogs name already exist!';
   }else{

      // Only insert if uploads were successful
      if(empty($upload_error) && !empty($final_image_url) && !empty($final_cover_url)){
         $insert_products = $conn->prepare("INSERT INTO `blogs`( title, description, date, image_url, cover_url) VALUES(?,?,?,?,?)");
         $insert_products->execute([$title, $description, $date, $final_image_url, $final_cover_url]);

         if($insert_products){
             $message[] = 'registered successfully!';
             header('location:blogs.php');
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
                  <h5 class="card-title">Add New Blog by Admin</h5>

                  
                  <form action="" method="POST" enctype="multipart/form-data">
                  

                    <div class="row mb-3">
                      <label for="inputTitle" class="col-sm-2 col-form-label">Title</label>
                      <div class="col-sm-10">
                        <input type="text" name="title" id="inputTitle" class="form-control" required>
                      </div>
                    </div>
                    <div class="row mb-3">
                      <label for="inputDescription" class="col-sm-2 col-form-label">Description</label>
                      <div class="col-sm-10">
                        <textarea name="description" id="inputDescription" class="form-control" rows="3"></textarea>
                      </div>
                    </div>
                    <div class="row mb-3">
                      <label for="inputImage" class="col-sm-2 col-form-label">Main Image</label>
                      <div class="col-sm-10">
                        <input type="file" name="image_url" id="inputImage" class="form-control" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" required>
                        <small class="form-text text-muted">Main image for the blog content. Allowed formats: JPG, JPEG, PNG, GIF, WEBP. Max size: 12MB.</small>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="inputCover" class="col-sm-2 col-form-label">Cover Image</label>
                      <div class="col-sm-10">
                        <input type="file" name="cover_url" id="inputCover" class="form-control" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" required>
                        <small class="form-text text-muted">Cover image displayed on listings. Allowed formats: JPG, JPEG, PNG, GIF, WEBP. Max size: 12MB.</small>
                      </div>
                    </div>
                   
                    <div class="row mb-3">
                      <label for="inputDate" class="col-sm-2 col-form-label">Date</label>
                      <div class="col-sm-10">
                        <input type="date" name="date" id="inputDate" class="form-control" value="<?= date('Y-m-d'); ?>">
                      </div>
                    </div>

                  

                    <div class="row mb-3">
                      <label class="col-sm-2 col-form-label">Add Blog</label>
                      <div class="col-sm-10">
                        <button type="submit" name="add_blogs" class="btn btn-primary">Add Blog</button>
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