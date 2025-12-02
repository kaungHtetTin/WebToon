<?php

include('config.php');
require_once('includes/image_helper.php');

session_start();



if(isset($_GET['delete'])){

   $delete_id = $_GET['delete'];
   
   $delete_products = $conn->prepare("DELETE FROM `series` WHERE id = ?");
   $delete_products->execute([$delete_id]);
   
   header('location:series.php');


}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Series Management - Admin Panel</title>
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
      <h1>Series</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item">Series</li>
          <li class="breadcrumb-item active">View Series</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">Series</h5>
                <a href="add_series.php" class="btn btn-primary">
                  <i class="bi bi-plus-circle"></i> Add Series
                </a>
              </div>

              <!-- Table with stripped rows -->
              <div class="table-responsive">
                <table class="table datatable table-hover">
                  <thead>
                    <tr>
                      <th scope="col" style="width: 60px;">#</th>
                      <th scope="col" style="width: 80px;">Image</th>
                      <th scope="col">Title</th>
                      <th scope="col" style="width: 80px;">Category</th>
                      <th scope="col" style="width: 80px;">Rating</th>
                      <th scope="col" style="width: 100px;">Views</th>
                      <th scope="col" style="width: 80px;">Saves</th>
                      <th scope="col" style="width: 80px;">Point</th>
                      <th scope="col" style="width: 80px;">Chapters</th>
                      <th scope="col" style="width: 80px;">Uploaded</th>
                      <th scope="col" style="width: 80px;">Status</th>
                      <th scope="col" style="width: 180px;">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                          $show_products = $conn->prepare("SELECT * FROM `series` ORDER BY id DESC");
                          $show_products->execute();
                          if($show_products->rowCount() > 0){
                             while($fetch_products = $show_products->fetch(PDO::FETCH_ASSOC)){  
                             
                             // Determine image path using universal function
                             $image_path = getImagePath(
                                 $fetch_products['image_url'] ?? '', 
                                 'series'
                             );
                             
                             // Get categories for this series
                             $get_categories = $conn->prepare("SELECT c.id, c.title FROM `series_categories` sc JOIN `categories` c ON sc.category_id = c.id WHERE sc.series_id = ? ORDER BY c.title ASC");
                             $get_categories->execute([$fetch_products['id']]);
                             $series_categories = $get_categories->fetchAll(PDO::FETCH_ASSOC);
                             
                             // Format numbers
                             $views = number_format($fetch_products['view'] ?? 0);
                             $saves = number_format($fetch_products['save'] ?? 0);
                             $rating = number_format($fetch_products['rating'] ?? 0, 1);
                             $point = number_format($fetch_products['point'] ?? 0);
                             $total_chapters = $fetch_products['total_chapter'] ?? 0;
                             $uploaded_chapters = $fetch_products['uploaded_chapter'] ?? 0;
                             $is_active = $fetch_products['is_active'] ?? 0;
                       ?>
                    <tr>
                      <th scope="row" class="text-muted"><?= $fetch_products['id']; ?></th>
                      <td>
                        <div class="series-image-container">
                          <img src="<?= htmlspecialchars($image_path); ?>" 
                               alt="<?= htmlspecialchars($fetch_products['title'] ?? 'Series image'); ?>" 
                               class="series-thumbnail"
                               onerror="this.src='../img/placeholder.jpg'">
                        </div>
                      </td>
                      <td>
                        <div class="series-title">
                          <strong class="d-block"><?= htmlspecialchars($fetch_products['title'] ?? 'Untitled'); ?></strong>
                        </div>
                      </td>
                      <td>
                        <?php if(!empty($series_categories)): ?>
                          <?php foreach($series_categories as $cat): ?>
                            <span class="badge bg-secondary me-1 mb-1"><?= htmlspecialchars($cat['title']); ?></span>
                          <?php endforeach; ?>
                        <?php else: ?>
                          <span class="badge bg-secondary">N/A</span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <div class="d-flex align-items-center">
                          <i class="bi bi-star-fill text-warning me-1"></i>
                          <span class="fw-bold"><?= $rating; ?></span>
                        </div>
                      </td>
                      <td>
                        <div class="d-flex align-items-center">
                          <i class="bi bi-eye text-primary me-1"></i>
                          <span><?= $views; ?></span>
                        </div>
                      </td>
                      <td>
                        <div class="d-flex align-items-center">
                          <i class="bi bi-bookmark text-success me-1"></i>
                          <span><?= $saves; ?></span>
                        </div>
                      </td>
                      <td>
                        <div class="d-flex align-items-center">
                          <i class="bi bi-coin text-warning me-1"></i>
                          <span class="fw-bold"><?= $point; ?></span>
                        </div>
                      </td>
                      <td>
                        <span class="badge bg-info"><?= $total_chapters; ?></span>
                      </td>
                      <td>
                        <span class="badge bg-primary"><?= $uploaded_chapters; ?></span>
                      </td>
                      <td>
                        <?php if($is_active): ?>
                          <span class="badge bg-success">
                            <i class="bi bi-check-circle"></i> Active
                          </span>
                        <?php else: ?>
                          <span class="badge bg-secondary">
                            <i class="bi bi-x-circle"></i> Inactive
                          </span>
                        <?php endif; ?>
                      </td>
                      <td>
                        <div class="btn-group" role="group">
                          <a href="manage_series.php?update=<?= $fetch_products['id']; ?>" 
                             class="btn btn-sm btn-outline-primary" 
                             data-bs-toggle="tooltip" 
                             title="Edit series">
                            <i class="bi bi-pencil"></i>
                          </a>
                          <a href="chapters.php?series_id=<?= $fetch_products['id']; ?>" 
                             class="btn btn-sm btn-outline-info" 
                             data-bs-toggle="tooltip" 
                             title="View chapters">
                            <i class="bi bi-file-text"></i>
                          </a>
                          <a href="series.php?delete=<?= $fetch_products['id']; ?>" 
                             class="btn btn-sm btn-outline-danger" 
                             onclick="return confirm('Are you sure you want to delete \'<?= htmlspecialchars(addslashes($fetch_products['title'] ?? 'this series')); ?>\'? This will also delete all associated chapters.');"
                             data-bs-toggle="tooltip" 
                             title="Delete series">
                            <i class="bi bi-trash"></i>
                          </a>
                        </div>
                      </td>
                    </tr>
                    <?php
                            }
                         }else{
                            echo '<tr><td colspan="12" class="text-center py-5"><div class="empty-state"><i class="bi bi-inbox empty-state-icon"></i><h5>No series found</h5><p class="text-muted">Get started by adding your first series.</p><a href="add_series.php" class="btn btn-primary mt-3"><i class="bi bi-plus-circle"></i> Add Series</a></div></td></tr>';
                         }
                         ?>
                  </tbody>
                </table>
              </div>
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
  <!-- Navigation Enhancement -->
  <script src="assets/js/navigation.js"></script>
  <!-- UX Enhancements -->
  <script src="assets/js/ux-enhancements.js"></script>

</body>

</html>