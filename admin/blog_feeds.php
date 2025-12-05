<?php

include('config.php');
require_once('includes/image_helper.php');

session_start();

// Optional filter by blog
$filter_blog_id = null;
if(isset($_GET['blog_id'])){
    $filter_blog_id = filter_var($_GET['blog_id'], FILTER_SANITIZE_NUMBER_INT);
    if($filter_blog_id === '' || !is_numeric($filter_blog_id)){
        $filter_blog_id = null;
    }
}

// Handle delete action
if(isset($_GET['delete'])){

   $delete_id = $_GET['delete'];
   
   $delete_products = $conn->prepare("DELETE FROM `blog_feeds` WHERE id = ?");
   $delete_products->execute([$delete_id]);
   
   // Preserve blog filter if present
   if($filter_blog_id){
       header('location:blog_feeds.php?blog_id=' . $filter_blog_id);
   }else{
       header('location:blog_feeds.php');
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
      <h1>Blog Feeds</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item"><a href="blogs.php">Blogs</a></li>
          <li class="breadcrumb-item active">Blog Feeds</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">
                  Blog Feeds
                  <?php if($filter_blog_id): ?>
                    <span class="text-muted" style="font-size: 0.85rem;">
                      (Filtered for Blog ID: <?= htmlspecialchars($filter_blog_id); ?>)
                    </span>
                  <?php endif; ?>
                </h5>
                <div class="d-flex gap-2">
                  <?php if($filter_blog_id): ?>
                    <a href="add_blog_feeds.php?blog_id=<?= htmlspecialchars($filter_blog_id); ?>" class="btn btn-primary btn-sm">
                      <i class="bi bi-plus-circle"></i> Add Blog Feed
                    </a>
                    <a href="blog_feeds.php" class="btn btn-outline-secondary btn-sm">
                      <i class="bi bi-x-circle"></i> Clear Filter
                    </a>
                  <?php else: ?>
                    <a href="add_blog_feeds.php" class="btn btn-primary btn-sm">
                      <i class="bi bi-plus-circle"></i> Add Blog Feed
                    </a>
                  <?php endif; ?>
                </div>
              </div>

              <!-- Table with stripped rows -->
              <div class="table-responsive">
                <table class="table datatable table-hover">
                  <thead>
                    <tr>
                      <th scope="col" style="width: 60px;">#</th>
                      <th scope="col">Blog</th>
                      <th scope="col">Feed Title</th>
                      <th scope="col" style="width: 80px;">Image</th>
                      <th scope="col">Body</th>
                      <th scope="col" style="width: 160px;">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                          // Join with blogs to show blog titles and enable navigation from blog feeds to blogs
                          if($filter_blog_id){
                              $show_products = $conn->prepare("
                                SELECT bf.*, b.title AS blog_title 
                                FROM `blog_feeds` bf 
                                LEFT JOIN `blogs` b ON bf.blog_id = b.id
                                WHERE bf.blog_id = ?
                                ORDER BY bf.id DESC
                              ");
                              $show_products->execute([$filter_blog_id]);
                          }else{
                              $show_products = $conn->prepare("
                                SELECT bf.*, b.title AS blog_title 
                                FROM `blog_feeds` bf 
                                LEFT JOIN `blogs` b ON bf.blog_id = b.id
                                ORDER BY bf.id DESC
                              ");
                              $show_products->execute();
                          }
                          if($show_products->rowCount() > 0){
                             while($fetch_products = $show_products->fetch(PDO::FETCH_ASSOC)){  
                       ?>
                        <tr>
                          <td><?= $fetch_products['id']; ?></td>
                          <td>
                            <div class="d-flex flex-column">
                              <a href="manage_blogs.php?update=<?= $fetch_products['blog_id']; ?>" class="text-primary fw-bold text-decoration-none">
                                <?= htmlspecialchars($fetch_products['blog_title'] ?? ('Blog #' . $fetch_products['blog_id'])); ?>
                              </a>
                              <small class="text-muted">ID: <?= htmlspecialchars($fetch_products['blog_id']); ?></small>
                            </div>
                          </td>
                          <td><?= htmlspecialchars($fetch_products['title'] ?? ''); ?></td>
                          <td>
                            <img src="<?= htmlspecialchars(getImagePath($fetch_products['image'] ?? '', 'blog_feeds')); ?>" 
                                 style="height: 50px;width: 50px;object-fit: cover;border-radius: 4px;" 
                                 onerror="this.src='../img/placeholder.jpg'" 
                                 alt="Feed image">
                          </td>
                          <td>
                            <span class="text-muted">
                              <?= htmlspecialchars(substr($fetch_products['body'] ?? '', 0, 80)); ?>
                              <?= strlen($fetch_products['body'] ?? '') > 80 ? '...' : ''; ?>
                            </span>
                          </td>
                          <td>
                            <div class="btn-group" role="group">
                              <a href="manage_blog_feeds.php?update=<?= $fetch_products['id']; ?>" 
                                 class="btn btn-sm btn-outline-primary" 
                                 title="Edit feed">
                                <i class="bi bi-pencil"></i>
                              </a>
                              <a href="blog_feeds.php?delete=<?= $fetch_products['id']; ?>" 
                                 class="btn btn-sm btn-outline-danger" 
                                 onclick="return confirm('delete this blog feed?');"
                                 title="Delete feed">
                                <i class="bi bi-trash"></i>
                              </a>
                            </div>
                          </td>
                        </tr>
                        <?php
                            }
                         }else{
                            echo '<tr><td colspan="6" class="text-center py-4"><p class="text-muted mb-0">No blog feeds found</p></td></tr>';
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