<?php

include('config.php');

session_start();




if(isset($_POST['add_series'])){

   // Handle multiple categories
   $category_ids = [];
   if(isset($_POST['category_ids']) && is_array($_POST['category_ids'])){
       foreach($_POST['category_ids'] as $cat_id){
           $cat_id = filter_var($cat_id, FILTER_SANITIZE_NUMBER_INT);
           if($cat_id && is_numeric($cat_id)){
               $category_ids[] = $cat_id;
           }
       }
   }
   
   // Validate at least one category is selected
   if(empty($category_ids)){
       $message[] = 'Please select at least one category!';
   }

   $title = $_POST['title'];
   $title = filter_var($title, FILTER_SANITIZE_STRING);

   $description = $_POST['description'];
   $description = filter_var($description, FILTER_SANITIZE_STRING);
  
   $date = $_POST['date'];
   $date = filter_var($date, FILTER_SANITIZE_STRING);

   
   // Handle checkbox - if checked, value is 1, otherwise 0
   $is_active = isset($_POST['is_active']) && $_POST['is_active'] == 'on' ? 1 : 0;


   $total_chapter = $_POST['total_chapter'];
   $total_chapter = filter_var($total_chapter, FILTER_SANITIZE_STRING);


   $uploaded_chapter = $_POST['uploaded_chapter'];
   $uploaded_chapter = filter_var($uploaded_chapter, FILTER_SANITIZE_STRING);

   // Handle image upload with validation
   $image_url_folder = '../uploads/images/series/';
   $final_image_url = '';
   $upload_error = '';
   
   // Create directory if it doesn't exist
   if (!file_exists($image_url_folder)) {
       mkdir($image_url_folder, 0755, true);
   }
   
   // Validate and process image upload
   if(isset($_FILES['image_url']) && !empty($_FILES['image_url']['name']) && $_FILES['image_url']['error'] !== UPLOAD_ERR_NO_FILE){
       if($_FILES['image_url']['error'] !== UPLOAD_ERR_OK){
           switch($_FILES['image_url']['error']){
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
           $image_url = $_FILES['image_url']['name'];
           $image_url_size = $_FILES['image_url']['size'];
           $image_url_tmp_name = $_FILES['image_url']['tmp_name'];
           
           // Validate file size (12MB max)
           if($image_url_size > 12000000){
               $upload_error = 'Image size is too large! Maximum size is 12MB.';
           } else {
               // Get file extension and validate
               $file_extension = strtolower(pathinfo($image_url, PATHINFO_EXTENSION));
               $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
               
               if(!in_array($file_extension, $allowed_extensions)){
                   $upload_error = 'Invalid image format! Allowed formats: JPG, JPEG, PNG, GIF, WEBP.';
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
                       $upload_error = 'Invalid image file type! Please upload a valid image.';
                   } else {
                       // Sanitize filename: remove special characters, spaces, and non-ASCII
                       $file_name = pathinfo($image_url, PATHINFO_FILENAME);
                       $file_name = preg_replace('/[^a-zA-Z0-9_-]/', '_', $file_name); // Replace special chars with underscore
                       $file_name = preg_replace('/_+/', '_', $file_name); // Replace multiple underscores with single
                       $file_name = trim($file_name, '_'); // Remove leading/trailing underscores
                       
                       // If filename is empty after sanitization, use default
                       if(empty($file_name)){
                           $file_name = 'series_image';
                       }
                       
                       // Limit filename length
                       if(strlen($file_name) > 100){
                           $file_name = substr($file_name, 0, 100);
                       }
                       
                       // Generate unique filename
                       $time = time();
                       $random = mt_rand(1000, 9999);
                       $unique_file = $file_name . '_' . $time . '_' . $random . '.' . $file_extension;
                       
                       // Ensure filename is unique
                       $counter = 0;
                       while(file_exists($image_url_folder . $unique_file) && $counter < 100){
                           $counter++;
                           $unique_file = $file_name . '_' . $time . '_' . $random . '_' . $counter . '.' . $file_extension;
                       }
                       
                       // Move uploaded file
                       if(move_uploaded_file($image_url_tmp_name, $image_url_folder . $unique_file)){
                           $final_image_url = "/uploads/images/series/" . $unique_file;
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
       // Image is required for new series
       $upload_error = 'Please select an image file!';
       $message[] = $upload_error;
   }

  
   $select_products = $conn->prepare("SELECT * FROM `series` WHERE title = ?");
   $select_products->execute([$title]);

   if($select_products->rowCount() > 0){
      $message[] = 'book title already exist!';
   }else if(empty($category_ids)){
      $message[] = 'Please select at least one category!';
   }else{

      // Insert series (without category_id)
      $insert_products = $conn->prepare("INSERT INTO `series`(title, description, date,  is_active, total_chapter, uploaded_chapter, image_url ) VALUES(?,?,?,?,?,?,?)");
      $insert_products->execute([$title, $description, $date, $is_active, $total_chapter, $uploaded_chapter, $final_image_url]);
      
      if($insert_products && empty($upload_error)){
          $series_id = $conn->lastInsertId();
          
          // Insert all selected categories into junction table
          $insert_category = $conn->prepare("INSERT INTO `series_categories`(series_id, category_id) VALUES(?, ?)");
          foreach($category_ids as $cat_id){
              $insert_category->execute([$series_id, $cat_id]);
          }
          
          $message[] = 'registered successfully!';
          header('location:series.php');
          exit;
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
      <h1>Add New Category by Admin</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item">Add </li>
          <li class="breadcrumb-item active">Category</li>
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
                  <label class="col-sm-2 col-form-label">Categories</label>
                  <div class="col-sm-10">
                    <div class="form-check-group" style="max-height: 200px; overflow-y: auto; border: 1px solid #dee2e6; padding: 15px; border-radius: 5px;">
                      <?php
                        $show_categories = $conn->prepare("SELECT * FROM `categories` ORDER BY `title` ASC");
                        $show_categories->execute();
                        if($show_categories->rowCount() > 0){
                           while($fetch_category = $show_categories->fetch(PDO::FETCH_ASSOC)){  
                     ?>
                      <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="category_ids[]" value="<?= $fetch_category['id']; ?>" id="category_<?= $fetch_category['id']; ?>">
                        <label class="form-check-label" for="category_<?= $fetch_category['id']; ?>">
                          <?= htmlspecialchars($fetch_category['title']); ?>
                        </label>
                      </div>
                     <?php
                          }
                       }else{
                          echo '<p class="empty">No categories available yet!</p>';
                       }
                       ?>
                    </div>
                    <small class="form-text text-muted">Select one or more categories for this series</small>
                  </div>
                </div> 


                <div class="row mb-3">
                  <label for="inputText" class="col-sm-2 col-form-label">title</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="title">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="inputEmail" class="col-sm-2 col-form-label">description</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="description">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="inputDate" class="col-sm-2 col-form-label">date</label>
                  <div class="col-sm-10">
                    <input type="date" class="form-control" name="date">
                  </div>
                </div>

                <!-- <div class="row mb-3">
                  <label for="inputDate" class="col-sm-2 col-form-label">rating</label>
                  <div class="col-sm-10">
                    <input type="number" class="form-control" name="rating">
                  </div>
                </div>

                <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">comment</label>
                      <div class="col-sm-10">
                        <input type="text" name="comment" class="form-control">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">view</label>
                      <div class="col-sm-10">
                        <input type="text" name="view" class="form-control">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">save</label>
                      <div class="col-sm-10">
                        <input type="text" name="save" class="form-control">
                      </div>
                    </div> -->

                    <div class="row mb-3">
                      <label class="col-sm-2 col-form-label">Status</label>
                      <div class="col-sm-10">
                        <div class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                          <label class="form-check-label" for="is_active">
                            Active (Series will be visible to users)
                          </label>
                        </div>
                        <small class="form-text text-muted">Uncheck to make this series inactive/hidden</small>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="inputNumber" class="col-sm-2 col-form-label">Series Image</label>
                      <div class="col-sm-10">
                        <input class="form-control" type="file" name="image_url" accept="image/jpeg,image/jpg,image/png,image/gif,image/webp" required>
                        <small class="form-text text-muted">Allowed formats: JPG, JPEG, PNG, GIF, WEBP. Maximum size: 12MB</small>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">total_chapter</label>
                      <div class="col-sm-10">
                        <input type="text" name="total_chapter" class="form-control">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">uploaded_chapter</label>
                      <div class="col-sm-10">
                        <input type="text" name="uploaded_chapter" class="form-control">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label class="col-sm-2 col-form-label">Submit Button</label>
                      <div class="col-sm-10">
                        <button type="submit" name="add_series" class="btn btn-primary">Submit Form</button>
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