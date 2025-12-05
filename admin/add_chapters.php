<?php

include('config.php');

session_start();

// Initialize message array
$message = [];

// Validate and sanitize series_id from URL
$series_id = isset($_GET['series_id']) ? filter_var($_GET['series_id'], FILTER_SANITIZE_NUMBER_INT) : null;

// Verify series exists
$series_title = '';
$series_exists = false;
if($series_id && is_numeric($series_id)){
    $get_series = $conn->prepare("SELECT title FROM `series` WHERE id = ?");
    $get_series->execute([$series_id]);
    if($get_series->rowCount() > 0){
        $series_data = $get_series->fetch(PDO::FETCH_ASSOC);
        $series_title = $series_data['title'] ?? '';
        $series_exists = true;
    }
}

if(isset($_POST['add_chapter'])){

   // Validate series_id
   $series_id_post = isset($_POST['series_id']) ? filter_var($_POST['series_id'], FILTER_SANITIZE_NUMBER_INT) : null;
   if(!$series_id_post || !is_numeric($series_id_post)){
       $message[] = 'Invalid series ID!';
   } else {
       // Verify series exists
       $check_series = $conn->prepare("SELECT id FROM `series` WHERE id = ?");
       $check_series->execute([$series_id_post]);
       if($check_series->rowCount() == 0){
           $message[] = 'Series does not exist!';
       }
   }

   // Validate title (required)
   $title = isset($_POST['title']) ? trim($_POST['title']) : '';
   if(empty($title)){
       $message[] = 'Chapter title is required!';
   } else {
       $title = filter_var($title, FILTER_SANITIZE_STRING);
       if(strlen($title) < 2){
           $message[] = 'Chapter title must be at least 2 characters long!';
       }
   }

   // Validate description (optional but sanitize if provided)
   $description = isset($_POST['description']) ? trim($_POST['description']) : '';
   if(!empty($description)){
       $description = filter_var($description, FILTER_SANITIZE_STRING);
   }

   // Validate date (required)
   $date = isset($_POST['date']) ? trim($_POST['date']) : '';
   if(empty($date)){
       $message[] = 'Chapter date is required!';
   } else {
       $date = filter_var($date, FILTER_SANITIZE_STRING);
       // Validate date format (YYYY-MM-DD)
       if(!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date)){
           $message[] = 'Invalid date format! Please use YYYY-MM-DD format.';
       }
   }

   // Handle checkbox - if checked, value is 1, otherwise 0
   $is_active = isset($_POST['is_active']) && $_POST['is_active'] == 'on' ? 1 : 0;
   $is_free = isset($_POST['is_free']) && $_POST['is_free'] == 'on' ? 1 : 0;

   // Only proceed if no validation errors
   if(empty($message)){
       // Check if chapter title already exists in this series (not globally)
       $select_products = $conn->prepare("SELECT * FROM `chapters` WHERE title = ? AND series_id = ?");
       $select_products->execute([$title, $series_id_post]);

       if($select_products->rowCount() > 0){
          $message[] = 'Chapter title already exists in this series!';
       } else {
          try {
              $insert_products = $conn->prepare("INSERT INTO `chapters`(series_id, title, description, date, is_active, is_free) VALUES(?,?,?,?,?,?)");
              $insert_products->execute([$series_id_post, $title, $description, $date, $is_active, $is_free]);

              if($insert_products){
                  // Update uploaded_chapter count in series table
                  $update_series = $conn->prepare("UPDATE `series` SET uploaded_chapter = uploaded_chapter + 1 WHERE id = ?");
                  $update_series->execute([$series_id_post]);
                  
                  $message[] = 'Chapter added successfully!';
                  // Redirect after 1 second to show success message
                  header("refresh:1;url=chapters.php?series_id=$series_id_post");
                  exit;
              } else {
                  $message[] = 'Failed to add chapter. Please try again.';
              }
          } catch(PDOException $e){
              $message[] = 'Database error: ' . $e->getMessage();
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

  <title>Admin | Add Chapter</title>
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
      <h1>Add Chapter</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item"><a href="series.php">Series</a></li>
          <li class="breadcrumb-item"><a href="chapters.php?series_id=<?= htmlspecialchars($series_id); ?>">Chapters</a></li>
          <li class="breadcrumb-item active">Add Chapter</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">

        
        <div class="col-lg-12">
          <div class="row">

            <div class="col-lg-12">

              <div class="card">
                <div class="card-body">
                  <h5 class="card-title">Add New Chapter</h5>

                  <?php
                  // Display error/success messages
                  if(isset($message) && !empty($message)){
                     foreach($message as $msg){
                        $alert_type = (strpos(strtolower($msg), 'success') !== false) ? 'success' : 'warning';
                        echo '
                        <div class="alert alert-'.$alert_type.' alert-dismissible fade show" role="alert">
                          '.htmlspecialchars($msg).'
                          <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        ';
                     }
                  }
                  ?>

                  <?php if(!$series_exists): ?>
                    <div class="alert alert-danger">
                      <i class="bi bi-exclamation-triangle me-2"></i>
                      Invalid or missing series ID. <a href="series.php">Go back to Series</a>
                    </div>
                  <?php else: ?>

                  <?php if(!empty($series_title)): ?>
                    <div class="alert alert-info mb-3">
                      <i class="bi bi-book me-2"></i>
                      <strong>Series:</strong> <?= htmlspecialchars($series_title); ?>
                    </div>
                  <?php endif; ?>

                  <form action="" method="POST" id="addChapterForm">
                    
                    <input type="hidden" name="series_id" value="<?= htmlspecialchars($series_id); ?>">

                    <div class="row mb-3">
                      <label for="title" class="col-sm-2 col-form-label">Title <span class="text-danger">*</span></label>
                      <div class="col-sm-10">
                        <input type="text" name="title" id="title" class="form-control" required minlength="2" value="<?= isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>">
                        <small class="form-text text-muted">Chapter title (required, minimum 2 characters)</small>
                        <div class="invalid-feedback">Please provide a valid chapter title (at least 2 characters).</div>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="description" class="col-sm-2 col-form-label">Description</label>
                      <div class="col-sm-10">
                        <textarea name="description" id="description" class="form-control" rows="3"><?= isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                        <small class="form-text text-muted">Optional chapter description</small>
                      </div>
                    </div>
                   
                    <div class="row mb-3">
                      <label for="date" class="col-sm-2 col-form-label">Date <span class="text-danger">*</span></label>
                      <div class="col-sm-10">
                        <input type="date" name="date" id="date" class="form-control" required value="<?= isset($_POST['date']) ? htmlspecialchars($_POST['date']) : date('Y-m-d'); ?>">
                        <small class="form-text text-muted">Chapter publication date (required)</small>
                        <div class="invalid-feedback">Please select a valid date.</div>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label class="col-sm-2 col-form-label">Status</label>
                      <div class="col-sm-10">
                        <div class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                          <label class="form-check-label" for="is_active">
                            Active (Chapter will be visible to users)
                          </label>
                        </div>
                        <small class="form-text text-muted">Uncheck to make this chapter inactive/hidden</small>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label class="col-sm-2 col-form-label">Free Access</label>
                      <div class="col-sm-10">
                        <div class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" id="is_free" name="is_free">
                          <label class="form-check-label" for="is_free">
                            Free (Chapter is free to access)
                          </label>
                        </div>
                        <small class="form-text text-muted">Check to make this chapter free, uncheck if it requires payment</small>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <div class="col-sm-10 offset-sm-2">
                        <button type="submit" name="add_chapter" class="btn btn-primary">
                          <i class="bi bi-plus-circle me-1"></i> Add Chapter
                        </button>
                        <a href="chapters.php?series_id=<?= htmlspecialchars($series_id); ?>" class="btn btn-secondary">
                          <i class="bi bi-arrow-left me-1"></i> Cancel
                        </a>
                      </div>
                    </div>

                  </form>

                  <?php endif; ?>

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
  <!-- Navigation Enhancement -->
  <script src="assets/js/navigation.js"></script>
  <!-- UX Enhancements -->
  <script src="assets/js/ux-enhancements.js"></script>

  <script>
    // Form validation
    (function() {
      'use strict';
      const form = document.getElementById('addChapterForm');
      if(form) {
        form.addEventListener('submit', function(event) {
          if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
          }
          form.classList.add('was-validated');
        }, false);
      }
    })();
  </script>

</body>

</html>