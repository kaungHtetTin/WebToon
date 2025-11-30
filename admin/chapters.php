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
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

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
          <li class="breadcrumb-item">Chapters</li>
          <li class="breadcrumb-item active">Chapters</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Chapters
                <?php if($series_id && is_numeric($series_id)): ?>
                  <span style="margin-left:20px;"><a href="add_chapters.php?series_id=<?= htmlspecialchars($series_id) ?>">Add new</a></span>
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
                    <th scope="col">series_id</th>
                    <th scope="col">title</th>
                    <th scope="col">description</th>
                    <th scope="col">download_url</th>
                    <th scope="col">date</th>
                    <th scope="col">is_active</th>
                    <th scope="col">action</th>
                    
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
                        <td><?= $fetch_products['series_id']; ?></td>
                        <td><?= $fetch_products['title']; ?></td>
                        <td><?= $fetch_products['description']; ?></td>
                        <td><?= $fetch_products['download_url']; ?></td>
                        <td><?= $fetch_products['date']; ?></td>
                        <td><?= $fetch_products['is_active']; ?></td>
                        
                        <td> <a href="manage_chapters.php?update=<?= $fetch_products['id']; ?>&series_id=<?= htmlspecialchars($series_id) ?>"><span class="badge bg-warning">Update</span></a> |
                             <a href="chapters.php?delete=<?= $fetch_products['id']; ?>&series_id=<?= htmlspecialchars($series_id) ?>" onclick="return confirm('delete this chapter?');"><span class="badge bg-danger">Delete</span></a>

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