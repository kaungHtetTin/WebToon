<?php

include('config.php');
require_once('includes/image_helper.php');

session_start();



if(isset($_GET['delete'])){

   $delete_id = $_GET['delete'];
   
   $delete_products = $conn->prepare("DELETE FROM `owl_carousels` WHERE id = ?");
   $delete_products->execute([$delete_id]);
   
   header('location:owl_carousels.php');


}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Tables / Data - NiceAdmin Bootstrap Template</title>
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



  <!-- End #main -->

  
  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Owl Carousels</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item">Owl Carousels</li>
          <li class="breadcrumb-item active">View Carousels</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">Owl Carousels</h5>
                <a href="add_owl_carousels.php" class="btn btn-primary">
                  <i class="bi bi-plus-circle"></i> Add Carousel
                </a>
              </div>

              <!-- Table with stripped rows -->
              <div class="table-responsive">
                <table class="table datatable table-hover">
                  <thead>
                    <tr>
                      <th scope="col" style="width: 60px;">#</th>
                      <th scope="col" style="width: 80px;">Image</th>
                      <th scope="col">Series</th>
                      <th scope="col" style="width: 100px;">Order</th>
                      <th scope="col" style="width: 80px;">Status</th>
                      <th scope="col" style="width: 180px;">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                          $show_products = $conn->prepare("SELECT oc.*, s.title as series_title FROM `owl_carousels` oc LEFT JOIN `series` s ON oc.series_id = s.id ORDER BY oc.order_index ASC, oc.id DESC");
                          $show_products->execute();
                          if($show_products->rowCount() > 0){
                             while($fetch_products = $show_products->fetch(PDO::FETCH_ASSOC)){  
                             
                             // Determine image path using universal function
                             $image_path = getImagePath(
                                 $fetch_products['cover_url'] ?? '', 
                                 'owl_carousels'
                             );
                             
                             $order_index = $fetch_products['order_index'] ?? 0;
                             $is_active = $fetch_products['is_active'] ?? 0;
                       ?>
                    <tr>
                      <th scope="row" class="text-muted"><?= $fetch_products['id']; ?></th>
                      <td>
                        <div class="series-image-container-small">
                          <img src="<?= htmlspecialchars($image_path); ?>" 
                               alt="<?= htmlspecialchars($fetch_products['series_title'] ?? 'Carousel image'); ?>" 
                               class="series-thumbnail-small"
                               onerror="this.src='../img/placeholder.jpg'">
                        </div>
                      </td>
                      <td>
                        <div class="series-title">
                          <strong class="d-block"><?= htmlspecialchars($fetch_products['series_title'] ?? 'Series #' . $fetch_products['series_id']); ?></strong>
                          <small class="text-muted">ID: <?= htmlspecialchars($fetch_products['series_id']); ?></small>
                        </div>
                      </td>
                      <td>
                        <span class="badge bg-info"><?= $order_index; ?></span>
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
                          <a href="manage_owl_carousels.php?update=<?= $fetch_products['id']; ?>" 
                             class="btn btn-sm btn-outline-primary" 
                             data-bs-toggle="tooltip" 
                             title="Edit carousel">
                            <i class="bi bi-pencil"></i>
                          </a>
                          <a href="owl_carousels.php?delete=<?= $fetch_products['id']; ?>" 
                             class="btn btn-sm btn-outline-danger" 
                             onclick="return confirm('Are you sure you want to delete this carousel?');"
                             data-bs-toggle="tooltip" 
                             title="Delete carousel">
                            <i class="bi bi-trash"></i>
                          </a>
                        </div>
                      </td>
                    </tr>
                    <?php
                            }
                         }else{
                            echo '<tr><td colspan="6" class="text-center py-5"><div class="empty-state"><i class="bi bi-inbox empty-state-icon"></i><h5>No carousels found</h5><p class="text-muted">Get started by adding your first carousel.</p><a href="add_owl_carousels.php" class="btn btn-primary mt-3"><i class="bi bi-plus-circle"></i> Add Carousel</a></div></td></tr>';
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