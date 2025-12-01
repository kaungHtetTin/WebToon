<?php

include('config.php');

session_start();


// Validate and sanitize series_id
$series_id = isset($_GET['series_id']) ? filter_var($_GET['series_id'], FILTER_SANITIZE_NUMBER_INT) : null;

if(isset($_GET['delete'])){

   $delete_id = filter_var($_GET['delete'], FILTER_SANITIZE_NUMBER_INT);
   $series_id_param = isset($_GET['series_id']) ? filter_var($_GET['series_id'], FILTER_SANITIZE_NUMBER_INT) : '';
   
   $delete_products = $conn->prepare("DELETE FROM `chapters` WHERE id = ?");
   $delete_products->execute([$delete_id]);
   
   if($series_id_param){
       header('location:chapters.php?series_id=' . $series_id_param);
   } else {
       header('location:series.php');
   }
   exit;

}

// Delete content
if(isset($_GET['delete_content'])){
   $delete_content_id = filter_var($_GET['delete_content'], FILTER_SANITIZE_NUMBER_INT);
   $chapter_id_param = isset($_GET['chapter_id']) ? filter_var($_GET['chapter_id'], FILTER_SANITIZE_NUMBER_INT) : '';
   $series_id_param = isset($_GET['series_id']) ? filter_var($_GET['series_id'], FILTER_SANITIZE_NUMBER_INT) : '';
   
   $delete_content = $conn->prepare("DELETE FROM `contents` WHERE id = ?");
   $delete_content->execute([$delete_content_id]);
   
   if($series_id_param && $chapter_id_param){
       header('location:chapters.php?series_id=' . $series_id_param);
   } else {
       header('location:chapters.php?series_id=' . $series_id_param);
   }
   exit;
}

// Get series title
$series_title = '';
if($series_id && is_numeric($series_id)){
    $get_series = $conn->prepare("SELECT title FROM `series` WHERE id = ?");
    $get_series->execute([$series_id]);
    if($get_series->rowCount() > 0){
        $series_data = $get_series->fetch(PDO::FETCH_ASSOC);
        $series_title = $series_data['title'] ?? '';
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Admin | Chapters Management</title>
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



  <!-- End #main -->

  
  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Chapters</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item"><a href="series.php">Series</a></li>
          <li class="breadcrumb-item active">Chapters</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <?php if($series_id && is_numeric($series_id) && !empty($series_title)): ?>
                <div class="content-header-info mb-4">
                  <div class="d-flex align-items-center gap-3 p-3 rounded" style="background: linear-gradient(135deg, var(--active-bg) 0%, var(--bg-tertiary) 100%); border: 1px solid var(--border-color);">
                    <div class="d-flex align-items-center flex-grow-1">
                      <div class="info-icon me-3">
                        <i class="bi bi-book" style="font-size: 28px; color: #1A73E8;"></i>
                      </div>
                      <div class="info-content">
                        <div class="info-label" style="font-size: 12px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.5px; font-weight: 500; margin-bottom: 2px;">Series</div>
                        <div class="info-value" style="font-size: 20px; font-weight: 500; color: var(--text-primary);"><?= htmlspecialchars($series_title); ?></div>
                        <div class="info-meta mt-1" style="font-size: 13px; color: var(--text-secondary);">ID: <?= htmlspecialchars($series_id); ?></div>
                      </div>
                    </div>
                  </div>
                </div>
              <?php endif; ?>
              
              <h5 class="card-title">Chapters
                <?php if($series_id && is_numeric($series_id)): ?>
                  <span style="margin-left:20px;"><a href="add_chapters.php?series_id=<?= htmlspecialchars($series_id) ?>" class="btn btn-sm btn-primary"><i class="bi bi-plus-circle"></i> Add Chapter</a></span>
                <?php endif; ?>
              </h5>
              <?php if(!$series_id || !is_numeric($series_id)): ?>
                <div class="alert alert-warning">Please select a valid series ID. <a href="series.php">Go back to Series</a></div>
              <?php endif; ?>


              <!-- Table with stripped rows -->
              <table class="table datatable">
                <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">Title</th>
                    <th scope="col">Description</th>
                    <th scope="col">Date</th>
                    <th scope="col">Status</th>
                    <th scope="col">Free</th>
                    <th scope="col">Actions</th>
                    
                  </tr>
                </thead>
                <tbody>
                  <?php
                        // Check if series_id is valid
                        if($series_id && is_numeric($series_id)){
                            $show_products = $conn->prepare("SELECT * FROM `chapters` WHERE series_id = ?");
                            $show_products->execute([$series_id]);
                        } else {
                            // If no valid series_id, show empty result
                            $show_products = $conn->prepare("SELECT * FROM `chapters` WHERE 1=0");
                            $show_products->execute();
                        }
                        if($show_products->rowCount() > 0){
                           while($fetch_products = $show_products->fetch(PDO::FETCH_ASSOC)){  
                     ?>
                      <tr>
                        <td><?= $fetch_products['id']; ?></td>
                        <td><strong><?= htmlspecialchars($fetch_products['title'] ?? 'Untitled'); ?></strong></td>
                        <td><?= htmlspecialchars(substr($fetch_products['description'] ?? '', 0, 100)) . (strlen($fetch_products['description'] ?? '') > 100 ? '...' : ''); ?></td>
                        <td><?= htmlspecialchars($fetch_products['date'] ?? 'N/A'); ?></td>
                        <td>
                          <?php if($fetch_products['is_active'] == 1): ?>
                            <span class="badge bg-success">Active</span>
                          <?php else: ?>
                            <span class="badge bg-secondary">Inactive</span>
                          <?php endif; ?>
                        </td>
                        <td>
                          <?php if(isset($fetch_products['is_free']) && $fetch_products['is_free'] == 1): ?>
                            <span class="badge bg-info">
                              <i class="bi bi-check-circle"></i> Free
                            </span>
                          <?php else: ?>
                            <span class="badge bg-warning">
                              <i class="bi bi-lock"></i> Paid
                            </span>
                          <?php endif; ?>
                        </td>
                        <td>
                          <div class="btn-group" role="group">
                            <a href="add_content.php?chapter_id=<?= $fetch_products['id']; ?>&series_id=<?= htmlspecialchars($series_id) ?>" 
                               class="btn btn-sm btn-primary" 
                               title="Add Content">
                              <i class="bi bi-plus-circle"></i> Add Content
                            </a>
                            <a href="view_contents.php?chapter_id=<?= $fetch_products['id']; ?>&series_id=<?= htmlspecialchars($series_id) ?>" 
                               class="btn btn-sm btn-info" 
                               title="View Contents">
                              <i class="bi bi-eye"></i> View
                            </a>
                            <a href="manage_chapters.php?update=<?= $fetch_products['id']; ?>&series_id=<?= htmlspecialchars($series_id) ?>" 
                               class="btn btn-sm btn-warning" 
                               title="Edit Chapter">
                              <i class="bi bi-pencil"></i>
                            </a>
                            <a href="chapters.php?delete=<?= $fetch_products['id']; ?>&series_id=<?= htmlspecialchars($series_id) ?>" 
                               onclick="return confirm('Are you sure you want to delete this chapter?');" 
                               class="btn btn-sm btn-danger" 
                               title="Delete Chapter">
                              <i class="bi bi-trash"></i>
                            </a>
                          </div>
                        </td>
                      </tr>
                      <?php
                          }
                       }else{
                          echo '<p class="empty">now books added yet!</p>';
                       }
                       ?>
                </tbody>
              </table>
              <!-- End Table with stripped rows -->

            </div>
          </div>

        </div>
      </div>
    </section>

  </main>


  <!-- End #main -->

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