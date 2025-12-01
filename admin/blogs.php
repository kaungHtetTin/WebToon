<?php

include('config.php');
require_once('includes/image_helper.php');

session_start();




if(isset($_GET['delete'])){

   $delete_id = $_GET['delete'];
   
   $delete_products = $conn->prepare("DELETE FROM `blogs` WHERE id = ?");
   $delete_products->execute([$delete_id]);
   
   header('location:blogs.php');


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
      <h1>Blogs</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item">Blogs</li>
          <li class="breadcrumb-item active">View Blogs</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">Blogs</h5>
                <a href="add_blogs.php" class="btn btn-primary">
                  <i class="bi bi-plus-circle"></i> Add Blog
                </a>
              </div>

              <!-- Table with stripped rows -->
              <div class="table-responsive">
                <table class="table datatable table-hover">
                  <thead>
                    <tr>
                      <th scope="col" style="width: 60px;">#</th>
                      <th scope="col">Title</th>
                      <th scope="col">Description</th>
                      <th scope="col" style="width: 80px;">Image</th>
                      <th scope="col" style="width: 80px;">Cover</th>
                      <th scope="col" style="width: 120px;">Date</th>
                      <th scope="col" style="width: 220px;">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                          $show_products = $conn->prepare("SELECT * FROM `blogs` ORDER BY id DESC");
                          $show_products->execute();
                          if($show_products->rowCount() > 0){
                             while($fetch_products = $show_products->fetch(PDO::FETCH_ASSOC)){  
                       ?>
                        <tr>
                          <td><?= $fetch_products['id']; ?></td>
                          <td><?= htmlspecialchars($fetch_products['title'] ?? ''); ?></td>
                          <td>
                            <span class="text-muted">
                              <?= htmlspecialchars(substr($fetch_products['description'] ?? '', 0, 80)); ?>
                              <?= strlen($fetch_products['description'] ?? '') > 80 ? '...' : ''; ?>
                            </span>
                          </td>
                          <td>
                            <img src="<?= htmlspecialchars(getImagePath($fetch_products['image_url'] ?? '', 'blogs')); ?>" 
                                 style="height: 50px;width: 50px;object-fit: cover;border-radius: 4px;" 
                                 onerror="this.src='../img/placeholder.jpg'"
                                 alt="Blog image">
                          </td>
                          <td>
                            <img src="<?= htmlspecialchars(getImagePath($fetch_products['cover_url'] ?? '', 'blogs')); ?>" 
                                 style="height: 50px;width: 50px;object-fit: cover;border-radius: 4px;" 
                                 onerror="this.src='../img/placeholder.jpg'"
                                 alt="Cover image">
                          </td>
                          <td><?= htmlspecialchars($fetch_products['date'] ?? ''); ?></td>
                          <td>
                            <div class="btn-group" role="group">
                              <a href="manage_blogs.php?update=<?= $fetch_products['id']; ?>" 
                                 class="btn btn-sm btn-outline-primary" 
                                 title="Edit blog">
                                <i class="bi bi-pencil"></i>
                              </a>
                              <a href="add_blog_feeds.php?blog_id=<?= $fetch_products['id']; ?>" 
                                 class="btn btn-sm btn-outline-success" 
                                 title="Add blog feed">
                                <i class="bi bi-plus-circle"></i>
                              </a>
                              <a href="blog_feeds.php?blog_id=<?= $fetch_products['id']; ?>" 
                                 class="btn btn-sm btn-outline-info" 
                                 title="View blog feeds">
                                <i class="bi bi-list-ul"></i>
                              </a>
                              <a href="blogs.php?delete=<?= $fetch_products['id']; ?>" 
                                 class="btn btn-sm btn-outline-danger" 
                                 onclick="return confirm('delete this blog?');"
                                 title="Delete blog">
                                <i class="bi bi-trash"></i>
                              </a>
                            </div>
                          </td>
                        </tr>
                        <?php
                            }
                         }else{
                            echo '<tr><td colspan="7" class="text-center py-4"><p class="text-muted mb-0">No blogs found</p></td></tr>';
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

</body>

</html>