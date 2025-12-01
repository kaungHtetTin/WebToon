<?php

include('config.php');

session_start();

$update_id = isset($_GET['update']) ? filter_var($_GET['update'], FILTER_SANITIZE_NUMBER_INT) : null;
$series_id = isset($_GET['series_id']) ? filter_var($_GET['series_id'], FILTER_SANITIZE_NUMBER_INT) : null;

// Get content info
$content_data = null;
$chapter_title = '';
$series_title = '';
if($update_id && is_numeric($update_id)){
    $get_content = $conn->prepare("SELECT c.*, ch.title as chapter_title, s.title as series_title, ch.series_id FROM `contents` c LEFT JOIN `chapters` ch ON c.chapter_id = ch.id LEFT JOIN `series` s ON ch.series_id = s.id WHERE c.id = ?");
    $get_content->execute([$update_id]);
    if($get_content->rowCount() > 0){
        $content_data = $get_content->fetch(PDO::FETCH_ASSOC);
        $chapter_title = $content_data['chapter_title'] ?? '';
        $series_title = $content_data['series_title'] ?? '';
        if(!$series_id){
            $series_id = $content_data['series_id'] ?? null;
        }
    }
}

if(isset($_POST['update_content'])){

   $pid = filter_var($_POST['pid'], FILTER_SANITIZE_NUMBER_INT);
   $series_id_post = filter_var($_POST['series_id'], FILTER_SANITIZE_NUMBER_INT);

   $content_type = filter_var($_POST['content_type'], FILTER_SANITIZE_STRING);
   if(empty($content_type)){
       $content_type = 'image';
   }

   $order_index = filter_var($_POST['order_index'], FILTER_SANITIZE_NUMBER_INT);

   $content_url = $_FILES['content_url']['name'];
   $content_url = filter_var($content_url, FILTER_SANITIZE_STRING);
   $content_url_size = $_FILES['content_url']['size'];
   $content_url_tmp_name = $_FILES['content_url']['tmp_name'];
   
   // Determine folder based on content type
   if($content_type == 'pdf'){
       $content_url_folder = '../uploads/pdfs/contents/';
   } else {
       $content_url_folder = '../uploads/images/contents/';
   }
   
   // Create directory if it doesn't exist
   if (!file_exists($content_url_folder)) {
       mkdir($content_url_folder, 0755, true);
   }

   // Only update content_url if a new file is uploaded
   if(!empty($content_url)){
       // Generate unique filename
       $time = time();
       $file_extension = pathinfo($content_url, PATHINFO_EXTENSION);
       $file_name = pathinfo($content_url, PATHINFO_FILENAME);
       $unique_file = $file_name . "_" . $time . "." . $file_extension;
       
       // Set final URL based on content type
       if($content_type == 'pdf'){
           $final_content_url = "/uploads/pdfs/contents/".$unique_file;
       } else {
           $final_content_url = "/uploads/images/contents/".$unique_file;
       }
       
       if(move_uploaded_file($content_url_tmp_name, $content_url_folder . $unique_file)){
           $update_product = $conn->prepare("UPDATE `contents` SET content_url = ?, content_type = ?, order_index = ? WHERE id = ?");
           $update_product->execute([$final_content_url, $content_type, $order_index, $pid]);
       } else {
           $message[] = 'Failed to upload file!';
       }
   } else {
       $update_product = $conn->prepare("UPDATE `contents` SET content_type = ?, order_index = ? WHERE id = ?");
       $update_product->execute([$content_type, $order_index, $pid]);
   }

   if(isset($update_product) && $update_product){
       $message[] = 'Content updated successfully!';
       header('location:chapters.php?series_id=' . $series_id_post);
       exit;
   }

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Admin | Update Content</title>
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
      <h1>Update Content</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item"><a href="series.php">Series</a></li>
          <li class="breadcrumb-item"><a href="chapters.php?series_id=<?= htmlspecialchars($series_id) ?>">Chapters</a></li>
          <li class="breadcrumb-item active">Update Content</li>
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
              
              <h5 class="card-title">Update Content</h5>

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

              <?php if(!$content_data): ?>
                <div class="alert alert-danger">Content not found. <a href="series.php">Go back to Series</a></div>
              <?php else: ?>

              <form action="" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="pid" value="<?= htmlspecialchars($content_data['id']); ?>">
                <input type="hidden" name="series_id" value="<?= htmlspecialchars($series_id); ?>">

                <div class="row mb-3">
                  <label for="content_url" class="col-sm-2 col-form-label">Current Content</label>
                  <div class="col-sm-10">
                    <?php if(!empty($content_data['content_url'])): ?>
                      <?php 
                        require_once('includes/image_helper.php');
                        $current_content_type = $content_data['content_type'] ?? 'image';
                        if($current_content_type == 'pdf'):
                      ?>
                        <div class="d-flex align-items-center mb-3">
                          <i class="bi bi-file-pdf text-danger" style="font-size: 64px;"></i>
                          <div class="ms-3">
                            <p class="mb-0"><strong>PDF File</strong></p>
                            <small class="text-muted"><?= htmlspecialchars($content_data['content_url']); ?></small>
                          </div>
                        </div>
                      <?php else: ?>
                        <?php 
                          $image_path = getImagePath($content_data['content_url'], 'contents');
                        ?>
                        <img src="<?= htmlspecialchars($image_path); ?>" alt="Content" style="max-width: 300px; max-height: 300px; border-radius: 8px; margin-bottom: 10px;" onerror="this.src='../img/placeholder.jpg'">
                        <br>
                        <small class="text-muted">Current: <?= htmlspecialchars($content_data['content_url']); ?></small>
                      <?php endif; ?>
                    <?php else: ?>
                      <p class="text-muted">No content file</p>
                    <?php endif; ?>
                  </div>
                </div>

                <div class="row mb-3">
                  <label for="content_url" class="col-sm-2 col-form-label">New Content File</label>
                  <div class="col-sm-10">
                    <input type="file" name="content_url" class="form-control" id="content_url" accept="image/*,.pdf">
                    <small class="form-text text-muted">Leave empty to keep current file. Upload image or PDF file</small>
                  </div>
                </div>

                <div class="row mb-3">
                  <label for="content_type" class="col-sm-2 col-form-label">Content Type</label>
                  <div class="col-sm-10">
                    <select name="content_type" class="form-select" id="content_type">
                      <option value="image" <?= ($content_data['content_type'] ?? '') == 'image' ? 'selected' : ''; ?>>Image</option>
                      <option value="pdf" <?= ($content_data['content_type'] ?? '') == 'pdf' ? 'selected' : ''; ?>>PDF</option>
                    </select>
                  </div>
                </div>

                <div class="row mb-3">
                  <label for="order_index" class="col-sm-2 col-form-label">Order Index</label>
                  <div class="col-sm-10">
                    <input type="number" name="order_index" class="form-control" id="order_index" value="<?= htmlspecialchars($content_data['order_index'] ?? 0); ?>" min="0">
                    <small class="form-text text-muted">Lower numbers appear first</small>
                  </div>
                </div>

                <div class="row mb-3">
                  <div class="col-sm-10 offset-sm-2">
                    <button type="submit" name="update_content" class="btn btn-primary">Update Content</button>
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

