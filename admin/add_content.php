<?php

include('config.php');

session_start();

$chapter_id = isset($_GET['chapter_id']) ? filter_var($_GET['chapter_id'], FILTER_SANITIZE_NUMBER_INT) : null;
$series_id = isset($_GET['series_id']) ? filter_var($_GET['series_id'], FILTER_SANITIZE_NUMBER_INT) : null;

// Get chapter and series info
$chapter_title = '';
$series_title = '';
if($chapter_id && is_numeric($chapter_id)){
    $get_chapter = $conn->prepare("SELECT c.*, s.title as series_title FROM `chapters` c LEFT JOIN `series` s ON c.series_id = s.id WHERE c.id = ?");
    $get_chapter->execute([$chapter_id]);
    if($get_chapter->rowCount() > 0){
        $chapter_data = $get_chapter->fetch(PDO::FETCH_ASSOC);
        $chapter_title = $chapter_data['title'] ?? '';
        $series_title = $chapter_data['series_title'] ?? '';
        if(!$series_id){
            $series_id = $chapter_data['series_id'] ?? null;
        }
    }
}

if(isset($_POST['add_content'])){

   $chapter_id_post = filter_var($_POST['chapter_id'], FILTER_SANITIZE_NUMBER_INT);
   $series_id_post = filter_var($_POST['series_id'], FILTER_SANITIZE_NUMBER_INT);

   $content_url = $_FILES['content_url']['name'];
   $content_url = filter_var($content_url, FILTER_SANITIZE_STRING);
   $content_url_size = $_FILES['content_url']['size'];
   $content_url_tmp_name = $_FILES['content_url']['tmp_name'];
   
   // Get optional content name
   $content_name = isset($_POST['content_name']) ? trim(filter_var($_POST['content_name'], FILTER_SANITIZE_STRING)) : '';
   
   // Determine folder based on content type
   $content_type = filter_var($_POST['content_type'], FILTER_SANITIZE_STRING);
   if(empty($content_type)){
       // Auto-detect from file extension
       $file_extension = strtolower(pathinfo($content_url, PATHINFO_EXTENSION));
       $content_type = ($file_extension == 'pdf') ? 'pdf' : 'image';
   }
   
   // Use appropriate folder based on content type
   if($content_type == 'pdf'){
       $content_url_folder = '../uploads/pdfs/contents/';
   } else {
       $content_url_folder = '../uploads/images/contents/';
   }
   
   // Create directory if it doesn't exist
   if (!file_exists($content_url_folder)) {
       mkdir($content_url_folder, 0755, true);
   }
   
   // Get order_index (max + 1)
   $get_max_order = $conn->prepare("SELECT MAX(order_index) as max_order FROM `contents` WHERE chapter_id = ?");
   $get_max_order->execute([$chapter_id_post]);
   $max_order_data = $get_max_order->fetch(PDO::FETCH_ASSOC);
   $order_index = ($max_order_data['max_order'] ?? 0) + 1;
   
   // Generate unique filename
   $file_extension = strtolower(pathinfo($content_url, PATHINFO_EXTENSION));
   
   // Use content name if provided, otherwise use original filename
   if(!empty($content_name)){
       // Sanitize content name for filename (remove special characters, keep only alphanumeric, spaces, hyphens, underscores)
       $content_name = preg_replace('/[^a-zA-Z0-9\s\-_]/', '', $content_name);
       $content_name = preg_replace('/\s+/', '_', trim($content_name)); // Replace spaces with underscores
       $file_name = $content_name;
   } else {
       // Use original filename without extension
       $file_name = pathinfo($content_url, PATHINFO_FILENAME);
   }
   
   // Add random integer to make filename unique
   $random_int = mt_rand(1000, 9999);
   $unique_file = $file_name . "_" . $random_int . "." . $file_extension;
   
   // Set final URL based on content type
   if($content_type == 'pdf'){
       $final_content_url = "/uploads/pdfs/contents/".$unique_file;
   } else {
       $final_content_url = "/uploads/images/contents/".$unique_file;
   }

   // Upload file
   if(!empty($content_url)){
       if(move_uploaded_file($content_url_tmp_name, $content_url_folder . $unique_file)){
           $insert_content = $conn->prepare("INSERT INTO `contents`(chapter_id, content_url, content_type, order_index) VALUES(?,?,?,?)");
           $insert_content->execute([$chapter_id_post, $final_content_url, $content_type, $order_index]);
           
           if($insert_content){
               $message[] = 'Content added successfully!';
               header('location:chapters.php?series_id=' . $series_id_post);
               exit;
           }
       } else {
           $message[] = 'Failed to upload file!';
       }
   } else {
       $message[] = 'Please select a file!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Admin | Add Content</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="../img/logo_round2.png" rel="icon" type="image/png">
  <link href="../img/logo_round2.png" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&family=Google+Sans:wght@400;500;700&display=swap" rel="stylesheet">

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
</head>

<body>

  <?php include 'admin_header.php'; ?>
  <?php include 'admin_sidebar.php'; ?>

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Add Content</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item"><a href="series.php">Series</a></li>
          <li class="breadcrumb-item"><a href="chapters.php?series_id=<?= htmlspecialchars($series_id) ?>">Chapters</a></li>
          <li class="breadcrumb-item active">Add Content</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <?php if(!empty($series_title) || !empty($chapter_title)): ?>
                <div class="content-header-info mb-4">
                  <div class="d-flex align-items-center gap-3 p-3 rounded" style="background: linear-gradient(135deg, var(--active-bg) 0%, var(--bg-tertiary) 100%); border: 1px solid var(--border-color);">
                    <?php if(!empty($series_title)): ?>
                      <div class="d-flex align-items-center flex-grow-1">
                        <div class="info-icon me-3">
                          <i class="bi bi-book" style="font-size: 24px; color: #1A73E8;"></i>
                        </div>
                        <div class="info-content">
                          <div class="info-label" style="font-size: 12px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.5px; font-weight: 500; margin-bottom: 2px;">Series</div>
                          <div class="info-value" style="font-size: 18px; font-weight: 500; color: var(--text-primary);"><?= htmlspecialchars($series_title); ?></div>
                        </div>
                      </div>
                    <?php endif; ?>
                    <?php if(!empty($chapter_title)): ?>
                      <div class="d-flex align-items-center flex-grow-1">
                        <div class="info-icon me-3">
                          <i class="bi bi-file-text" style="font-size: 24px; color: #34A853;"></i>
                        </div>
                        <div class="info-content">
                          <div class="info-label" style="font-size: 12px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.5px; font-weight: 500; margin-bottom: 2px;">Chapter</div>
                          <div class="info-value" style="font-size: 18px; font-weight: 500; color: var(--text-primary);"><?= htmlspecialchars($chapter_title); ?></div>
                        </div>
                      </div>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endif; ?>
              
              <h5 class="card-title">Add New Content</h5>

              <?php
                if(isset($message)){
                   foreach($message as $message){
                      echo '
                      <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        '.$message.'
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                      </div>
                      ';
                   }
                }
              ?>

              <?php if(!$chapter_id || !is_numeric($chapter_id)): ?>
                <div class="alert alert-danger">Invalid chapter ID. <a href="series.php">Go back to Series</a></div>
              <?php else: ?>

              <form action="" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="chapter_id" value="<?= htmlspecialchars($chapter_id); ?>">
                <input type="hidden" name="series_id" value="<?= htmlspecialchars($series_id); ?>">

                <div class="row mb-3">
                  <label for="content_name" class="col-sm-2 col-form-label">Content Name</label>
                  <div class="col-sm-10">
                    <input type="text" name="content_name" class="form-control" id="content_name" placeholder="Optional: Enter a custom name for this content">
                    <small class="form-text text-muted">If provided, this will be used as the filename. Otherwise, the original filename will be used.</small>
                  </div>
                </div>

                <div class="row mb-3">
                  <label for="content_url" class="col-sm-2 col-form-label">Content File</label>
                  <div class="col-sm-10">
                    <input type="file" name="content_url" class="form-control" id="content_url" accept="image/*,.pdf" required>
                    <small class="form-text text-muted">Upload image or PDF file for this chapter content</small>
                  </div>
                </div>

                <div class="row mb-3">
                  <label for="content_type" class="col-sm-2 col-form-label">Content Type</label>
                  <div class="col-sm-10">
                    <select name="content_type" class="form-select" id="content_type">
                      <option value="image" selected>Image</option>
                      <option value="pdf">PDF</option>
                    </select>
                  </div>
                </div>

                <div class="row mb-3">
                  <div class="col-sm-10 offset-sm-2">
                    <button type="submit" name="add_content" class="btn btn-primary">Add Content</button>
                    <a href="chapters.php?series_id=<?= htmlspecialchars($series_id) ?>" class="btn btn-secondary">Cancel</a>
                  </div>
                </div>

              </form>

              <?php endif; ?>

            </div>
          </div>

        </div>
      </div>
    </section>

  </main>

  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">
    <div class="copyright">
      &copy; Copyright <strong><span>NiceAdmin</span></strong>. All Rights Reserved
    </div>
    <div class="credits">
      Designed by <a href="#">Aung Kyaw Thant</a>
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
  <!-- Navigation Enhancement -->
  <script src="assets/js/navigation.js"></script>
  <!-- UX Enhancements -->
  <script src="assets/js/ux-enhancements.js"></script>

</body>

</html>

