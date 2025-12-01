<?php
// Start output buffering immediately to catch any output
ob_start();

// Suppress warnings for cleaner JSON output (but log errors)
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

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
   
   // Handle single file upload
   if(isset($_FILES['content_url']) && !empty($_FILES['content_url']['name'])){
       
       $content_url = $_FILES['content_url']['name'];
       $content_url_tmp_name = $_FILES['content_url']['tmp_name'];
       $content_url_error = $_FILES['content_url']['error'];
       
       // Get order_index
       $get_max_order = $conn->prepare("SELECT MAX(order_index) as max_order FROM `contents` WHERE chapter_id = ?");
       $get_max_order->execute([$chapter_id_post]);
       $max_order_data = $get_max_order->fetch(PDO::FETCH_ASSOC);
       $order_index = ($max_order_data['max_order'] ?? 0) + 1;
       
       // Create directories
       $pdf_folder = '../uploads/pdfs/contents/';
       $image_folder = '../uploads/images/contents/';
       if (!file_exists($pdf_folder)) mkdir($pdf_folder, 0755, true);
       if (!file_exists($image_folder)) mkdir($image_folder, 0755, true);
       
       // Detect content type
       $file_extension = strtolower(pathinfo($content_url, PATHINFO_EXTENSION));
       $content_type = ($file_extension == 'pdf') ? 'pdf' : 'image';
       $content_url_folder = ($content_type == 'pdf') ? $pdf_folder : $image_folder;
       
       // Generate unique filename
       $file_name = pathinfo($content_url, PATHINFO_FILENAME);
       $file_name = preg_replace('/[^a-zA-Z0-9\s\-_]/', '', $file_name);
       $file_name = preg_replace('/\s+/', '_', trim($file_name));
       $unique_file = $file_name . "_" . mt_rand(1000, 9999) . "." . $file_extension;
       
       // Upload file
       if($content_url_error === UPLOAD_ERR_OK && move_uploaded_file($content_url_tmp_name, $content_url_folder . $unique_file)){
           $final_content_url = ($content_type == 'pdf') ? "/uploads/pdfs/contents/".$unique_file : "/uploads/images/contents/".$unique_file;
           $insert_content = $conn->prepare("INSERT INTO `contents`(chapter_id, content_url, content_type, order_index) VALUES(?,?,?,?)");
           $insert_content->execute([$chapter_id_post, $final_content_url, $content_type, $order_index]);
           
           // Return JSON for AJAX
           if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
               ob_clean();
               header('Content-Type: application/json');
               echo json_encode(['success' => true, 'message' => 'File uploaded successfully']);
               exit;
           }
           
           header('location:chapters.php?series_id=' . $series_id_post);
           exit;
       } else {
           // Return error for AJAX
           if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
               ob_clean();
               header('Content-Type: application/json');
               echo json_encode(['success' => false, 'message' => 'Failed to upload file']);
               exit;
           }
       }
   } else {
       // No file
       if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
           ob_clean();
           header('Content-Type: application/json');
           echo json_encode(['success' => false, 'message' => 'No file selected']);
           exit;
       }
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

              <form action="" method="POST" enctype="multipart/form-data" id="uploadForm">
                <input type="hidden" name="chapter_id" value="<?= htmlspecialchars($chapter_id); ?>">
                <input type="hidden" name="series_id" value="<?= htmlspecialchars($series_id); ?>">

                <div class="row mb-3">
                  <label for="content_url" class="col-sm-2 col-form-label">Content Files</label>
                  <div class="col-sm-10">
                    <input type="file" name="content_url[]" class="form-control" id="content_url" accept="image/*,.pdf" multiple required>
                    <small class="form-text text-muted">You can select multiple image or PDF files at once. Content type will be automatically detected based on file extension.</small>
                    <div id="fileList" class="mt-2"></div>
                  </div>
                </div>

                <!-- Progress Bar Container -->
                <div id="uploadProgressContainer" class="row mb-3" style="display: none;">
                  <div class="col-sm-10 offset-sm-2">
                    <div class="card border-0 shadow-sm">
                      <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                          <h6 class="mb-0">Uploading Files...</h6>
                          <span id="uploadPercentage" class="badge bg-primary">0%</span>
                        </div>
                        <div class="progress" style="height: 25px;">
                          <div id="uploadProgressBar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100">
                            <span id="uploadStatusText" class="ms-2">0%</span>
                          </div>
                        </div>
                        <div id="uploadDetails" class="mt-2 small text-muted"></div>
                      </div>
                    </div>
                  </div>
                </div>

                <div class="row mb-3">
                  <div class="col-sm-10 offset-sm-2">
                    <button type="submit" name="add_content" class="btn btn-primary" id="submitBtn">
                      <i class="bi bi-upload me-1"></i> Upload Content
                    </button>
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

  <script>
    document.addEventListener('DOMContentLoaded', function() {
      const form = document.getElementById('uploadForm');
      const fileInput = document.getElementById('content_url');
      const progressContainer = document.getElementById('uploadProgressContainer');
      const progressBar = document.getElementById('uploadProgressBar');
      const progressPercentage = document.getElementById('uploadPercentage');
      const uploadStatusText = document.getElementById('uploadStatusText');
      const uploadDetails = document.getElementById('uploadDetails');
      const submitBtn = document.getElementById('submitBtn');

      form.addEventListener('submit', function(e) {
        e.preventDefault();

        const files = fileInput.files;
        if (files.length === 0) {
          alert('Please select at least one file!');
          return;
        }

        // Show progress
        progressContainer.style.display = 'block';
        submitBtn.disabled = true;
        
        let uploaded = 0;
        let failed = 0;
        const totalFiles = files.length;
        
        // Upload files one by one
        function uploadFile(index) {
          if (index >= totalFiles) {
            // All files uploaded
            progressBar.style.width = '100%';
            progressPercentage.textContent = '100%';
            uploadStatusText.textContent = 'Complete!';
            uploadDetails.innerHTML = `<div class="alert alert-success mb-0">Uploaded ${uploaded} of ${totalFiles} files</div>`;
            setTimeout(() => {
              window.location.href = 'chapters.php?series_id=<?= htmlspecialchars($series_id); ?>';
            }, 1000);
            return;
          }

          const file = files[index];
          const formData = new FormData();
          formData.append('add_content', '1');
          formData.append('chapter_id', form.querySelector('[name="chapter_id"]').value);
          formData.append('series_id', form.querySelector('[name="series_id"]').value);
          formData.append('content_url', file);

          const xhr = new XMLHttpRequest();
          xhr.open('POST', form.action, true);
          xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

          xhr.onload = function() {
            if (xhr.status === 200) {
              try {
                const response = JSON.parse(xhr.responseText);
                if (response.success) {
                  uploaded++;
                } else {
                  failed++;
                }
              } catch(e) {
                failed++;
              }
            } else {
              failed++;
            }

            // Update progress
            const percent = Math.round(((index + 1) / totalFiles) * 100);
            progressBar.style.width = percent + '%';
            progressPercentage.textContent = percent + '%';
            uploadStatusText.textContent = `Uploading ${index + 1} of ${totalFiles}`;
            uploadDetails.textContent = `Uploaded: ${uploaded}, Failed: ${failed}`;

            // Upload next file
            uploadFile(index + 1);
          };

          xhr.onerror = function() {
            failed++;
            uploadFile(index + 1);
          };

          xhr.send(formData);
        }

        // Start uploading
        uploadFile(0);
      });
    });
  </script>

</body>

</html>

