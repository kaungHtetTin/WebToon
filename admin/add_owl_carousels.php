<?php

include('config.php');

session_start();

if(isset($_POST['add_owl_carousels'])){

   $series_id = $_POST['series_id'];
   $series_id = filter_var($series_id, FILTER_SANITIZE_NUMBER_INT);
   
   $order_index = $_POST['order_index'];
   $order_index = filter_var($order_index, FILTER_SANITIZE_NUMBER_INT);
   if(empty($order_index) || !is_numeric($order_index)) {
       $order_index = 0;
   }
   
   // Handle checkbox - if checked, value is 1, otherwise 0
   $is_active = isset($_POST['is_active']) && $_POST['is_active'] == 'on' ? 1 : 0;

   // Handle image upload with validation
   $cover_url_folder = '../uploads/images/owl_carousels/';
   $final_cover_url = '';
   $upload_error = '';
   
   // Create directory if it doesn't exist
   if (!file_exists($cover_url_folder)) {
       mkdir($cover_url_folder, 0755, true);
   }
   
   // Validate and process image upload
   if(isset($_FILES['cover_url']) && !empty($_FILES['cover_url']['name']) && $_FILES['cover_url']['error'] !== UPLOAD_ERR_NO_FILE){
       if($_FILES['cover_url']['error'] !== UPLOAD_ERR_OK){
           switch($_FILES['cover_url']['error']){
               case UPLOAD_ERR_INI_SIZE:
               case UPLOAD_ERR_FORM_SIZE:
                   $upload_error = 'Image file is too large!';
                   break;
               case UPLOAD_ERR_PARTIAL:
                   $upload_error = 'Image upload was incomplete!';
                   break;
               case UPLOAD_ERR_NO_TMP_DIR:
                   $upload_error = 'Missing temporary folder!';
                   break;
               case UPLOAD_ERR_CANT_WRITE:
                   $upload_error = 'Failed to write file to disk!';
                   break;
               default:
                   $upload_error = 'Image upload failed!';
           }
       } else {
           $cover_url = $_FILES['cover_url']['name'];
           $cover_url_size = $_FILES['cover_url']['size'];
           $cover_url_tmp_name = $_FILES['cover_url']['tmp_name'];
           
           // Validate file size (12MB max)
           if($cover_url_size > 12000000){
               $upload_error = 'Image size is too large! Maximum size is 12MB.';
           } else {
               // Get file extension and validate
               $file_extension = strtolower(pathinfo($cover_url, PATHINFO_EXTENSION));
               $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
               
               if(!in_array($file_extension, $allowed_extensions)){
                   $upload_error = 'Invalid image format! Allowed formats: JPG, JPEG, PNG, GIF, WEBP.';
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
                       $upload_error = 'Invalid image file type! Please upload a valid image.';
                   } else {
                       // Sanitize filename
                       $file_name = pathinfo($cover_url, PATHINFO_FILENAME);
                       $file_name = preg_replace('/[^a-zA-Z0-9_-]/', '_', $file_name);
                       $file_name = preg_replace('/_+/', '_', $file_name);
                       $file_name = trim($file_name, '_');
                       
                       if(empty($file_name)){
                           $file_name = 'carousel_image';
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
                       while(file_exists($cover_url_folder . $unique_file) && $counter < 100){
                           $counter++;
                           $unique_file = $file_name . '_' . $time . '_' . $random . '_' . $counter . '.' . $file_extension;
                       }
                       
                       // Move uploaded file
                       if(move_uploaded_file($cover_url_tmp_name, $cover_url_folder . $unique_file)){
                           $final_cover_url = "/uploads/images/owl_carousels/" . $unique_file;
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

   // Check if series_id already exists in carousels
   if(empty($upload_error)){
       $check_series = $conn->prepare("SELECT * FROM `owl_carousels` WHERE series_id = ?");
       $check_series->execute([$series_id]);
       
       if($check_series->rowCount() > 0){
           $message[] = 'This series is already in the carousel!';
       } else {
           $insert_products = $conn->prepare("INSERT INTO `owl_carousels`(series_id, cover_url, order_index, is_active) VALUES(?,?,?,?)");
           $insert_products->execute([$series_id, $final_cover_url, $order_index, $is_active]);
           
           if($insert_products && empty($upload_error)){
               $message[] = 'registered successfully!';
               header('location:owl_carousels.php');
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
      <h1>Add New Carousel</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item"><a href="owl_carousels.php">Carousels</a></li>
          <li class="breadcrumb-item active">Add Carousel</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Add New Carousel</h5>

              <!-- General Form Elements -->
              <form action="" method="POST" enctype="multipart/form-data">
                <div class="row mb-3">
                  <label class="col-sm-2 col-form-label">Series <span class="text-danger">*</span></label>
                  <div class="col-sm-10">
                    <select name="series_id" class="form-select" required>
                      <option value="">Select a series</option>
                      <?php
                        $show_products = $conn->prepare("SELECT * FROM `series` ORDER BY title ASC");
                        $show_products->execute();
                        if($show_products->rowCount() > 0){
                           while($fetch_products = $show_products->fetch(PDO::FETCH_ASSOC)){  
                     ?>
                      <option value="<?= $fetch_products['id']; ?>"><?= htmlspecialchars($fetch_products['title']); ?></option>
                     <?php
                          }
                       }else{
                          echo '<option disabled>No series available</option>';
                       }
                       ?>
                    </select>
                    <small class="form-text text-muted">Select the series to display in the carousel</small>
                  </div>
                </div>

                <div class="row mb-3">
                  <label for="inputNumber" class="col-sm-2 col-form-label">Order Index</label>
                  <div class="col-sm-10">
                    <input type="number" name="order_index" class="form-control" min="0" value="0">
                    <small class="form-text text-muted">Lower numbers appear first. Default: 0</small>
                  </div>
                </div>

                <div class="row mb-3">
                  <label class="col-sm-2 col-form-label">Status</label>
                  <div class="col-sm-10">
                    <div class="form-check form-switch">
                      <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                      <label class="form-check-label" for="is_active">
                        Active (Carousel will be visible)
                      </label>
                    </div>
                    <small class="form-text text-muted">Uncheck to hide this carousel</small>
                  </div>
                </div>

                <div class="row mb-3">
                  <label for="inputNumber" class="col-sm-2 col-form-label">Cover Image <span class="text-danger">*</span></label>
                  <div class="col-sm-10">
                    <input class="form-control" type="file" name="cover_url" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" required>
                    <small class="form-text text-muted">Allowed formats: JPG, JPEG, PNG, GIF, WEBP. Maximum size: 12MB</small>
                  </div>
                </div>

                <div class="row mb-3">
                  <label class="col-sm-2 col-form-label"></label>
                  <div class="col-sm-10">
                    <button type="submit" name="add_owl_carousels" class="btn btn-primary">
                      <i class="bi bi-check-circle"></i> Add Carousel
                    </button>
                    <a href="owl_carousels.php" class="btn btn-secondary ms-2">Cancel</a>
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
      &copy; Copyright <strong><span>worldofwebtoonmmsub</span></strong>. All Rights Reserved
    </div>
    <div class="credits">
      Designed by maxmadmm
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