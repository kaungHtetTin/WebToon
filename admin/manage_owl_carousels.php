<?php

include('config.php');
require_once('includes/image_helper.php');

session_start();

if(isset($_POST['update_owl_carousels'])){

   $pid = $_POST['pid'];
   
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
   $upload_success = true;
   $upload_error = '';
   
   // Create directory if it doesn't exist
   if (!file_exists($cover_url_folder)) {
       mkdir($cover_url_folder, 0755, true);
   }
   
   // Check if a new image is uploaded (optional for update)
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
           $upload_success = false;
       } else {
           $cover_url = $_FILES['cover_url']['name'];
           $cover_url_size = $_FILES['cover_url']['size'];
           $cover_url_tmp_name = $_FILES['cover_url']['tmp_name'];
           
           // Validate file size (12MB max)
           if($cover_url_size > 12000000){
               $upload_error = 'Image size is too large! Maximum size is 12MB.';
               $upload_success = false;
           } else {
               // Get file extension and validate
               $file_extension = strtolower(pathinfo($cover_url, PATHINFO_EXTENSION));
               $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
               
               if(!in_array($file_extension, $allowed_extensions)){
                   $upload_error = 'Invalid image format! Allowed formats: JPG, JPEG, PNG, GIF, WEBP.';
                   $upload_success = false;
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
                       $upload_success = false;
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
                           $upload_success = true;
                       } else {
                           $upload_error = 'Failed to save image file!';
                           $upload_success = false;
                       }
                   }
               }
           }
       }
       
       if(!empty($upload_error)){
           $message[] = $upload_error;
       }
   } else {
       // Get current cover_url from database
       $select_current = $conn->prepare("SELECT cover_url FROM `owl_carousels` WHERE id = ?");
       $select_current->execute([$pid]);
       $current_data = $select_current->fetch(PDO::FETCH_ASSOC);
       $final_cover_url = $current_data['cover_url'] ?? '';
   }

   // Check if series_id already exists in another carousel
   if($upload_success){
       $check_series = $conn->prepare("SELECT * FROM `owl_carousels` WHERE series_id = ? AND id != ?");
       $check_series->execute([$series_id, $pid]);
       
       if($check_series->rowCount() > 0){
           $message[] = 'This series is already in another carousel!';
       } else {
           // Only proceed with database update if upload was successful or no file was uploaded
           $update_product = $conn->prepare("UPDATE `owl_carousels` SET series_id = ?, cover_url = ?, order_index = ?, is_active = ? WHERE id = ?");
           $update_product->execute([$series_id, $final_cover_url, $order_index, $is_active, $pid]);

           if($update_product){
               $message[] = 'updated successfully!';
               header('location:owl_carousels.php');
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
                  <h5 class="card-title">Update Carousel</h5>
                  
                  <?php
                          $update_id = $_GET['update'];
                          $select_products = $conn->prepare("SELECT oc.*, s.title as series_title FROM `owl_carousels` oc LEFT JOIN `series` s ON oc.series_id = s.id WHERE oc.id = ?");
                          $select_products->execute([$update_id]);
                          if($select_products->rowCount() > 0){
                             while($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)){ 
                             
                             $image_path = getImagePath(
                                 $fetch_products['cover_url'] ?? '', 
                                 'owl_carousels'
                             );
                       ?>
                  <form action="" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="pid" class="form-control" value="<?= $fetch_products['id']; ?>">

                    <div class="row mb-3">
                      <label class="col-sm-2 col-form-label">Series <span class="text-danger">*</span></label>
                      <div class="col-sm-10">
                        <select name="series_id" class="form-select" required>
                          <option value="">Select a series</option>
                          <?php
                            $all_series = $conn->prepare("SELECT * FROM `series` ORDER BY title ASC");
                            $all_series->execute();
                            if($all_series->rowCount() > 0){
                               while($series = $all_series->fetch(PDO::FETCH_ASSOC)){  
                         ?>
                          <option value="<?= $series['id']; ?>" <?= $series['id'] == $fetch_products['series_id'] ? 'selected' : ''; ?>><?= htmlspecialchars($series['title']); ?></option>
                         <?php
                              }
                           }
                           ?>
                        </select>
                        <small class="form-text text-muted">Select the series to display in the carousel</small>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="inputNumber" class="col-sm-2 col-form-label">Order Index</label>
                      <div class="col-sm-10">
                        <input type="number" name="order_index" class="form-control" min="0" value="<?= isset($fetch_products['order_index']) ? htmlspecialchars($fetch_products['order_index']) : '0'; ?>">
                        <small class="form-text text-muted">Lower numbers appear first</small>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label class="col-sm-2 col-form-label">Status</label>
                      <div class="col-sm-10">
                        <div class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" id="is_active" name="is_active" <?= isset($fetch_products['is_active']) && $fetch_products['is_active'] == 1 ? 'checked' : ''; ?>>
                          <label class="form-check-label" for="is_active">
                            Active (Carousel will be visible)
                          </label>
                        </div>
                        <small class="form-text text-muted">Uncheck to hide this carousel</small>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="inputNumber" class="col-sm-2 col-form-label">Cover Image</label>
                      <div class="col-sm-10">
                        <input class="form-control" type="file" name="cover_url" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp">
                        <small class="form-text text-muted">Allowed formats: JPG, JPEG, PNG, GIF, WEBP. Maximum size: 12MB. Leave empty to keep current image.</small>
                        <?php if(isset($fetch_products['cover_url']) && !empty($fetch_products['cover_url'])): ?>
                          <div class="mt-2">
                            <img src="<?= htmlspecialchars($image_path); ?>" alt="Current cover" style="max-width: 200px; max-height: 150px; border-radius: 4px;" onerror="this.src='../img/placeholder.jpg'">
                            <div class="mt-1">
                              <small class="text-muted">Current: <?= htmlspecialchars($fetch_products['cover_url']); ?></small>
                            </div>
                          </div>
                        <?php endif; ?>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label class="col-sm-2 col-form-label"></label>
                      <div class="col-sm-10">
                        <button type="submit" name="update_owl_carousels" class="btn btn-primary">
                          <i class="bi bi-check-circle"></i> Update Carousel
                        </button>
                        <a href="owl_carousels.php" class="btn btn-secondary ms-2">Cancel</a>
                      </div>
                    </div>

                  </form>
                  <?php
                       }
                    }else{
                       echo '<p class="empty">no products found!</p>';
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