<?php

include('config.php');

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

  <title>Tables / Data - NiceAdmin Bootstrap Template</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  <!-- Favicons -->
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

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
      <h1>Series</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
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
              <h5 class="card-title">View Series</h5>
              <p>Add lightweight datatables to your project with using the class name to any table you wish to conver to a datatable</p>

              <!-- Table with stripped rows -->
              <table class="table datatable">
                <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">catid</th>
                    <th scope="col">title</th>
                    <th scope="col">description</th>
                    <th scope="col">short</th>
                    <th scope="col">genre</th>
                    <th scope="col">orginal_work</th>
                    <th scope="col">upload_status</th>
                    <th scope="col">rating</th>
                    <th scope="col">comment</th>
                    <th scope="col">view</th>
                    <th scope="col">save</th>
                    <th scope="col">active</th>
                    <th scope="col">image</th>
                    <th scope="col">total</th>
                    <th scope="col">upload</th>
                    <th scope="col">action</th>
                    
                  </tr>
                </thead>
                <tbody>
                  <?php
                        $show_products = $conn->prepare("SELECT * FROM `series`");
                        $show_products->execute();
                        if($show_products->rowCount() > 0){
                           while($fetch_products = $show_products->fetch(PDO::FETCH_ASSOC)){  
                     ?>
                  <tr>
                   
                    <th scope="row"><?= $fetch_products['id']; ?></th>
                        <td><?= $fetch_products['category_id']; ?></td>
                        <td><?= $fetch_products['title']; ?></td>
                        <td><?= substr($fetch_products['description'],0,20)."..." ?></td>
                        <td><?= $fetch_products['short']; ?></td> 
                        <td><?= $fetch_products['genre']; ?></td> 
                        <td><?= $fetch_products['orginal_work']; ?></td> 
                        <td><?= $fetch_products['upload_status']; ?></td> 
                        <td><?= $fetch_products['rating']; ?></td>
                        <td><?= $fetch_products['comment']; ?></td>
                        <td><?= $fetch_products['view']; ?></td>
                        <td><?= $fetch_products['save']; ?></td>
                        <td><?= $fetch_products['is_active']; ?></td>
                        <td><?= $fetch_products['image_url']; ?></td>
                        <td><?= $fetch_products['total_chapter']; ?></td>
                        <td><?= $fetch_products['uploaded_chapter']; ?></td>
                        <td> <a href="manage_series.php?update=<?= $fetch_products['id']; ?>"><span class="badge bg-warning">Update</span></a> <br><br>
                              <a href="series.php?delete=<?= $fetch_products['id']; ?>" onclick="return confirm('delete this series?');"><span class="badge bg-danger">Delete</span></a> <br><br>
                              <a href="chapters.php?series_id=<?= $fetch_products['id']; ?>"><span class="badge bg-primary">Chapters</span></a> 
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