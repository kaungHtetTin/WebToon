<?php

  include_once('config.php');
  require_once('includes/image_helper.php');

  session_start();

  

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Admin | Dashboard</title>
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



  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Dashboard</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item active">Dashboard</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="row">

        <!-- Left side columns -->
        <div class="col-lg-12">
          <div class="row">

            <!-- Sales Card -->
            <div class="col-xxl-4 col-md-6">
              <div class="card info-card sales-card">



                <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li class="dropdown-header text-start">
                      <h6>Filter</h6>
                    </li>

                    <li><a class="dropdown-item" href="#">Today</a></li>
                    <li><a class="dropdown-item" href="#">This Month</a></li>
                    <li><a class="dropdown-item" href="#">This Year</a></li>
                  </ul>
                </div>

                <div class="card-body">
                  <h5 class="card-title">Categories <span>| Total</span></h5>

                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-cart"></i>
                    </div>
                    <a href="categories.php">
                      <div class="ps-3">
                      <?php
                         $select_products = $conn->prepare("SELECT * FROM `categories`");
                         $select_products->execute();
                         $number_of_products = $select_products->rowCount();
                      ?>
                      <h6><?= $number_of_products; ?></h6>
                      <span class="text-success small pt-1 fw-bold">Categories</span>

                    </div>
                    </a>
                    
                  </div>
                </div>

              </div>
            </div><!-- End Sales Card -->

            <!-- Revenue Card -->
            <div class="col-xxl-4 col-md-6">
              <div class="card info-card revenue-card">

                <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li class="dropdown-header text-start">
                      <h6>Filter</h6>
                    </li>

                    <li><a class="dropdown-item" href="#">Today</a></li>
                    <li><a class="dropdown-item" href="#">This Month</a></li>
                    <li><a class="dropdown-item" href="#">This Year</a></li>
                  </ul>
                </div>

                <div class="card-body">
                  <h5 class="card-title">Series <span>| Total</span></h5>

                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-currency-dollar"></i>
                    </div>
                    <a href="series.php">
                      <div class="ps-3">
                      <?php
                         $select_products = $conn->prepare("SELECT * FROM `series`");
                         $select_products->execute();
                         $number_of_products = $select_products->rowCount();
                      ?>
                      <h6><?= $number_of_products; ?></h6>
                      <span class="text-success small pt-1 fw-bold">series</span>

                    </div>
                    </a>
                    
                  </div>
                </div>

              </div>
            </div><!-- End Revenue Card -->

            <!-- Revenue Card -->
            <div class="col-xxl-4 col-md-6">
              <div class="card info-card revenue-card">

                <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li class="dropdown-header text-start">
                      <h6>Filter</h6>
                    </li>

                    <li><a class="dropdown-item" href="#">Today</a></li>
                    <li><a class="dropdown-item" href="#">This Month</a></li>
                    <li><a class="dropdown-item" href="#">This Year</a></li>
                  </ul>
                </div>

                <div class="card-body">
                  <h5 class="card-title">Payments <span>| Total</span></h5>

                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-currency-dollar"></i>
                    </div>
                    <a href="payment_histories.php">
                      <div class="ps-3">
                        <?php
                           $select_products = $conn->prepare("SELECT * FROM `payment_histories`");
                           $select_products->execute();
                           $number_of_products = $select_products->rowCount();
                        ?>
                        <h6><?= $number_of_products; ?></h6>
                        <span class="text-success small pt-1 fw-bold">payment_histories</span>

                      </div>
                    </a>
                    
                  </div>
                </div>

              </div>
            </div><!-- End Revenue Card -->

            <!-- Customers Card -->
            <div class="col-xxl-4 col-xl-12">

              <div class="card info-card customers-card">

                <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li class="dropdown-header text-start">
                      <h6>Filter</h6>
                    </li>

                    <li><a class="dropdown-item" href="#">Today</a></li>
                    <li><a class="dropdown-item" href="#">This Month</a></li>
                    <li><a class="dropdown-item" href="#">This Year</a></li>
                  </ul>
                </div>

                <div class="card-body">
                  <h5 class="card-title">Chapters <span>| Total</span></h5>

                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-people"></i>
                    </div>
                    <a href="chapters.php">
                        <div class="ps-3">
                        <?php
                           $select_products = $conn->prepare("SELECT * FROM `chapters`");
                           $select_products->execute();
                           $number_of_products = $select_products->rowCount();
                        ?>
                        <h6><?= $number_of_products; ?></h6>
                        <span class="text-danger small pt-1 fw-bold">Chapters</span>

                      </div>
                    </a>
                    
                  </div>

                </div>
              </div>

            </div>
            <!-- End Customers Card -->

            <!-- Customers Card -->
            <div class="col-xxl-4 col-xl-12">

              <div class="card info-card customers-card">

                <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li class="dropdown-header text-start">
                      <h6>Filter</h6>
                    </li>

                    <li><a class="dropdown-item" href="#">Today</a></li>
                    <li><a class="dropdown-item" href="#">This Month</a></li>
                    <li><a class="dropdown-item" href="#">This Year</a></li>
                  </ul>
                </div>

                <div class="card-body">
                  <h5 class="card-title">Blogs <span>| Total</span></h5>

                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-people"></i>
                    </div>
                    <a href="blogs.php">
                      <div class="ps-3">
                      <?php
                         $select_products = $conn->prepare("SELECT * FROM `blogs`");
                         $select_products->execute();
                         $number_of_products = $select_products->rowCount();
                      ?>
                      <h6><?= $number_of_products; ?></h6>
                      <span class="text-danger small pt-1 fw-bold">Blogs</span>

                    </div>
                    </a>
                    
                  </div>

                </div>
              </div>

            </div>
            <!-- End Customers Card -->

            <!-- Customers Card -->
            <div class="col-xxl-4 col-xl-12">

              <div class="card info-card customers-card">

                <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li class="dropdown-header text-start">
                      <h6>Filter</h6>
                    </li>

                    <li><a class="dropdown-item" href="#">Today</a></li>
                    <li><a class="dropdown-item" href="#">This Month</a></li>
                    <li><a class="dropdown-item" href="#">This Year</a></li>
                  </ul>
                </div>

                <div class="card-body">
                  <h5 class="card-title">Blog_Feeds <span>| Total</span></h5>

                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-people"></i>
                    </div>
                    <a href="blog_feeds.php">
                      <div class="ps-3">
                      <?php
                         $select_products = $conn->prepare("SELECT * FROM `blog_feeds`");
                         $select_products->execute();
                         $number_of_products = $select_products->rowCount();
                      ?>
                      <h6><?= $number_of_products; ?></h6>
                      <span class="text-danger small pt-1 fw-bold">Blog_Feeds</span>

                    </div>
                    </a>
                    
                  </div>

                </div>
              </div>

            </div>
            <!-- End Customers Card -->

            <!-- Customers Card -->
            <div class="col-xxl-4 col-xl-12">

              <div class="card info-card customers-card">

                <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li class="dropdown-header text-start">
                      <h6>Filter</h6>
                    </li>

                    <li><a class="dropdown-item" href="#">Today</a></li>
                    <li><a class="dropdown-item" href="#">This Month</a></li>
                    <li><a class="dropdown-item" href="#">This Year</a></li>
                  </ul>
                </div>

                <div class="card-body">
                  <h5 class="card-title">Admins <span>| Total</span></h5>

                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-people"></i>
                    </div>
                    <a href="admin.php">
                      <div class="ps-3">
                      <?php
                         $select_products = $conn->prepare("SELECT * FROM `admin`");
                         $select_products->execute();
                         $number_of_products = $select_products->rowCount();
                      ?>
                      <h6><?= $number_of_products; ?></h6>
                      <span class="text-danger small pt-1 fw-bold">Admins</span>

                    </div>
                    </a>
                    
                  </div>

                </div>
              </div>

            </div><!-- End Customers Card -->


            <!-- Customers Card -->
            <div class="col-xxl-4 col-xl-12">

              <div class="card info-card customers-card">

                <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li class="dropdown-header text-start">
                      <h6>Filter</h6>
                    </li>

                    <li><a class="dropdown-item" href="#">Today</a></li>
                    <li><a class="dropdown-item" href="#">This Month</a></li>
                    <li><a class="dropdown-item" href="#">This Year</a></li>
                  </ul>
                </div>

                <div class="card-body">
                  <h5 class="card-title">Users <span>| Total</span></h5>

                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-people"></i>
                    </div>
                    <a href="users.php">
                      <div class="ps-3">
                      <?php
                         $select_products = $conn->prepare("SELECT * FROM `users`");
                         $select_products->execute();
                         $number_of_products = $select_products->rowCount();
                      ?>
                      <h6><?= $number_of_products; ?></h6>
                      <span class="text-danger small pt-1 fw-bold">Users</span>

                    </div>
                    </a>
                    
                  </div>

                </div>
              </div>

            </div><!-- End Customers Card -->


            
            <!-- Recent Sales -->
            <div class="col-12">
              <div class="card recent-sales overflow-auto">

                <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li class="dropdown-header text-start">
                      <h6>Filter</h6>
                    </li>

                    <li><a class="dropdown-item" href="#">Today</a></li>
                    <li><a class="dropdown-item" href="#">This Month</a></li>
                    <li><a class="dropdown-item" href="#">This Year</a></li>
                  </ul>
                </div>

                <div class="card-body">
                  <h5 class="card-title">Unapprove Payment Histories List<span>| Collection</span></h5>

                  <table class="table table-borderless datatable">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">user id</th>
                        
                        <th scope="col">point</th>
                        <th scope="col">screenshot url</th>
                        <th scope="col">date</th>
                        <th scope="col">verified</th>
                        <th scope="col">confirmed</th>
                        <th scope="col">Action</th>


                      </tr>
                    </thead>
                    <tbody>
                      <?php
                        $show_products = $conn->prepare("SELECT * FROM `payment_histories`");
                        $show_products->execute();
                        if($show_products->rowCount() > 0){
                           while($fetch_products = $show_products->fetch(PDO::FETCH_ASSOC)){  
                     ?>
                      <tr>
                        <th scope="row"><?= $fetch_products['id']; ?></th>
                        <td><?= $fetch_products['user_id']; ?></td>
                        <td><?= $fetch_products['point']; ?></td>
                        <td><img src="<?= htmlspecialchars(getImagePath($fetch_products['screenshot_url'] ?? '', 'screenshots')); ?>" style="width: 100px;height: 100px;" onerror="this.src='../img/placeholder.jpg'"></td>

                        <td><?= $fetch_products['date']; ?></td>
                        <td><?= $fetch_products['verified']; ?></td>
                        <td><?= $fetch_products['confirm']; ?></td>
                        <td>
                          <?php if(!$fetch_products['verified'] || !$fetch_products['confirm']): ?>
                            <a href="unapprove_payment.php?update=<?= $fetch_products['id']; ?>"><span class="badge bg-success">Approve</span></a> |
                          <?php else: ?>
                            <span class="badge bg-secondary">Approved</span> |
                          <?php endif; ?>
                          <a href="unapprove_payment.php?delete=<?= $fetch_products['id']; ?>" onclick="return confirm('delete this payment?');"><span class="badge bg-danger">Delete</span></a>
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

                </div>

              </div>
            </div>
            <!-- End Recent Sales -->

            <!-- Recent Sales -->
            <div class="col-12">
              <div class="card recent-sales overflow-auto">

                <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li class="dropdown-header text-start">
                      <h6>Filter</h6>
                    </li>

                    <li><a class="dropdown-item" href="#">Today</a></li>
                    <li><a class="dropdown-item" href="#">This Month</a></li>
                    <li><a class="dropdown-item" href="#">This Year</a></li>
                  </ul>
                </div>

                <div class="card-body">
                  <h5 class="card-title">All Admin <span>| Collection</span></h5>

                  <table class="table table-borderless datatable">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">name</th>
                        <th scope="col">email</th>
                        <th scope="col">phone</th>
                        <th scope="col">active</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                        $show_products = $conn->prepare("SELECT * FROM `admin`");
                        $show_products->execute();
                        if($show_products->rowCount() > 0){
                           while($fetch_products = $show_products->fetch(PDO::FETCH_ASSOC)){  
                     ?>
                      <tr>
                        <th scope="row"><?= $fetch_products['id']; ?></th>
                        <td><?= $fetch_products['username']; ?></td>
                        <td><?= $fetch_products['email']; ?></td>
                        <td><?= $fetch_products['phone']; ?></td>
                        <td><span class="badge bg-success"><?= $fetch_products['is_active']; ?></span></td>
                      </tr>
                      <?php
                          }
                       }else{
                          echo '<p class="empty">now books added yet!</p>';
                       }
                       ?>
                    </tbody>
                  </table>

                </div>

              </div>
            </div>
            <!-- End Recent Sales -->

            <!-- Recent Sales -->
            <div class="col-12">
              <div class="card recent-sales overflow-auto">

                <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li class="dropdown-header text-start">
                      <h6>Filter</h6>
                    </li>

                    <li><a class="dropdown-item" href="#">Today</a></li>
                    <li><a class="dropdown-item" href="#">This Month</a></li>
                    <li><a class="dropdown-item" href="#">This Year</a></li>
                  </ul>
                </div>

                <div class="card-body">
                  <h5 class="card-title">All Users <span>| Collection</span></h5>

                  <table class="table table-borderless datatable">
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col" style="width: 60px;">Image</th>
                        <th scope="col">name</th>
                        <th scope="col">email</th>
                        <th scope="col">phone</th>
                        <th scope="col">vip</th>
                        <th scope="col">point</th>

                      </tr>
                    </thead>
                    <tbody>
                      <?php
                        $show_products = $conn->prepare("SELECT * FROM `users`");
                        $show_products->execute();
                        if($show_products->rowCount() > 0){
                           while($fetch_products = $show_products->fetch(PDO::FETCH_ASSOC)){  
                     ?>
                      <tr>
                        <th scope="row"><?= $fetch_products['id']; ?></th>
                        <td>
                          <div class="user-image-container">
                            <img src="<?= htmlspecialchars(getImagePath($fetch_products['image_url'] ?? '', 'profile')); ?>" 
                                 alt="<?= htmlspecialchars(($fetch_products['first_name'] ?? '') . ' ' . ($fetch_products['last_name'] ?? '')); ?>" 
                                 class="user-avatar"
                                 onerror="this.src='../img/placeholder.jpg'">
                          </div>
                        </td>
                        <td><?= $fetch_products['first_name']; ?> <?= $fetch_products['last_name']; ?></td>
                        <td><?= $fetch_products['email']; ?></td>
                        <td><?= $fetch_products['phone']; ?></td>
                        <td><span class="badge bg-success"><?= $fetch_products['is_vip']; ?></span></td>
                        <td><span class="badge bg-success"><?= $fetch_products['point']; ?></span></td>

                      </tr>
                      <?php
                          }
                       }else{
                          echo '<p class="empty">now books added yet!</p>';
                       }
                       ?>
                    </tbody>
                  </table>

                </div>

              </div>
            </div><!-- End Recent Sales -->

            <!-- Top Selling -->
            <div class="col-12">
              <div class="card recent-sales overflow-auto">

                <div class="filter">
                  <a class="icon" href="#" data-bs-toggle="dropdown"><i class="bi bi-three-dots"></i></a>
                  <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                    <li class="dropdown-header text-start">
                      <h6>Filter</h6>
                    </li>

                    <li><a class="dropdown-item" href="series.php">See All</a></li>
                    <!-- <li><a class="dropdown-item" href="#">This Month</a></li>
                    <li><a class="dropdown-item" href="#">This Year</a></li> -->
                  </ul>
                </div>

                <div class="card-body pb-0">
                  <h5 class="card-title">Last Update Series <span>| Collection</span></h5>

                  <table class="table table-borderless">
                    <thead>
                      <tr>
                        <th scope="col">image</th>
                        <th scope="col">title</th>
                        <th scope="col">description</th>
                        <th scope="col">rating</th>
                        <th scope="col">view</th>
                      </tr>
                    </thead>
                    <tbody>
                      <?php
                        $show_products = $conn->prepare("SELECT * FROM `series` order by id desc limit 10");
                        $show_products->execute();
                        if($show_products->rowCount() > 0){
                           while($fetch_products = $show_products->fetch(PDO::FETCH_ASSOC)){  
                     ?>
                      <tr>
                        <th scope="row">
                          <div class="series-image-container-small">
                            <a href="#">
                              <img src="<?= htmlspecialchars(getImagePath($fetch_products['image_url'] ?? '', 'series')); ?>" 
                                   alt="<?= htmlspecialchars($fetch_products['title'] ?? ''); ?>" 
                                   class="series-thumbnail-small"
                                   onerror="this.src='../img/placeholder.jpg'">
                            </a>
                          </div>
                        </th>
                        <td><a href="#" class="text-primary fw-bold"><?= $fetch_products['title']; ?></a></td>
                        <td><?= $fetch_products['description']; ?></td>
                        <td class="fw-bold"><?= $fetch_products['rating']; ?></td>
                        <td><?= $fetch_products['view']; ?></td>
                      </tr>
                      <?php
                          }
                       }else{
                          echo '<p class="empty">now books added yet!</p>';
                       }
                       ?>
                    </tbody>
                  </table>

                </div>

              </div>
            </div><!-- End Top Selling -->

          </div>
        </div><!-- End Left side columns -->



      </div>
    </section>

  </main><!-- End #main -->

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