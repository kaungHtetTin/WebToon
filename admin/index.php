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

        <!-- Statistics Cards -->
        <div class="col-lg-12">
          <div class="row">

            <!-- Categories Card -->
            <div class="col-xxl-3 col-md-6">
              <div class="card info-card sales-card">
                <div class="card-body">
                  <h5 class="card-title">Categories <span>| Total</span></h5>
                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-tags"></i>
                    </div>
                    <a href="categories.php" class="text-decoration-none flex-grow-1">
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
            </div><!-- End Categories Card -->

            <!-- Series Card -->
            <div class="col-xxl-3 col-md-6">
              <div class="card info-card revenue-card">
                <div class="card-body">
                  <h5 class="card-title">Series <span>| Total</span></h5>
                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-book"></i>
                    </div>
                    <a href="series.php" class="text-decoration-none flex-grow-1">
                      <div class="ps-3">
                        <?php
                           $select_products = $conn->prepare("SELECT * FROM `series`");
                           $select_products->execute();
                           $number_of_products = $select_products->rowCount();
                        ?>
                        <h6><?= $number_of_products; ?></h6>
                        <span class="text-success small pt-1 fw-bold">Series</span>
                      </div>
                    </a>
                  </div>
                </div>
              </div>
            </div><!-- End Series Card -->

            <!-- Payments Card -->
            <div class="col-xxl-3 col-md-6">
              <div class="card info-card revenue-card">
                <div class="card-body">
                  <h5 class="card-title">Payments <span>| Total</span></h5>
                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-credit-card"></i>
                    </div>
                    <a href="unapprove_payment.php" class="text-decoration-none flex-grow-1">
                      <div class="ps-3">
                        <?php
                           $select_products = $conn->prepare("SELECT * FROM `payment_histories`");
                           $select_products->execute();
                           $number_of_products = $select_products->rowCount();
                        ?>
                        <h6><?= $number_of_products; ?></h6>
                        <span class="text-success small pt-1 fw-bold">Payments</span>
                      </div>
                    </a>
                  </div>
                </div>
              </div>
            </div><!-- End Payments Card -->

            <!-- Blogs Card -->
            <div class="col-xxl-3 col-md-6">
              <div class="card info-card customers-card">
                <div class="card-body">
                  <h5 class="card-title">Blogs <span>| Total</span></h5>
                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-newspaper"></i>
                    </div>
                    <a href="blogs.php" class="text-decoration-none flex-grow-1">
                      <div class="ps-3">
                        <?php
                           $select_products = $conn->prepare("SELECT * FROM `blogs`");
                           $select_products->execute();
                           $number_of_products = $select_products->rowCount();
                        ?>
                        <h6><?= $number_of_products; ?></h6>
                        <span class="text-success small pt-1 fw-bold">Blogs</span>
                      </div>
                    </a>
                  </div>
                </div>
              </div>
            </div><!-- End Blogs Card -->

            <!-- (Chapters summary card removed as requested) -->

            <!-- Users Card -->
            <div class="col-xxl-3 col-md-6">
              <div class="card info-card customers-card">
                <div class="card-body">
                  <h5 class="card-title">Users <span>| Total</span></h5>
                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-people"></i>
                    </div>
                    <a href="users.php" class="text-decoration-none flex-grow-1">
                      <div class="ps-3">
                        <?php
                           $select_products = $conn->prepare("SELECT * FROM `users`");
                           $select_products->execute();
                           $number_of_products = $select_products->rowCount();
                        ?>
                        <h6><?= $number_of_products; ?></h6>
                        <span class="text-success small pt-1 fw-bold">Users</span>
                      </div>
                    </a>
                  </div>
                </div>
              </div>
            </div><!-- End Users Card -->

            <!-- Admins Card -->
            <div class="col-xxl-3 col-md-6">
              <div class="card info-card customers-card">
                <div class="card-body">
                  <h5 class="card-title">Admins <span>| Total</span></h5>
                  <div class="d-flex align-items-center">
                    <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                      <i class="bi bi-shield-check"></i>
                    </div>
                    <a href="admin.php" class="text-decoration-none flex-grow-1">
                      <div class="ps-3">
                        <?php
                           $select_products = $conn->prepare("SELECT * FROM `admin`");
                           $select_products->execute();
                           $number_of_products = $select_products->rowCount();
                        ?>
                        <h6><?= $number_of_products; ?></h6>
                        <span class="text-success small pt-1 fw-bold">Admins</span>
                      </div>
                    </a>
                  </div>
                </div>
              </div>
            </div><!-- End Admins Card -->


            
            <!-- Payment Histories Table -->
            <div class="col-12">
              <div class="card recent-sales overflow-auto">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">Payment Histories <span>| Pending Approval</span></h5>
                    <a href="unapprove_payment.php" class="btn btn-sm btn-primary">
                      <i class="bi bi-eye"></i> View All
                    </a>
                  </div>

                  <div class="table-responsive">
                    <table class="table table-borderless datatable">
                      <thead>
                        <tr>
                          <th scope="col" style="width: 60px;">#</th>
                          <th scope="col" style="width: 100px;">User ID</th>
                          <th scope="col" style="width: 100px;">Points</th>
                          <th scope="col" style="width: 120px;">Screenshot</th>
                          <th scope="col" style="width: 120px;">Date</th>
                          <th scope="col" style="width: 100px;">Status</th>
                          <th scope="col" style="width: 150px;">Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                          $show_products = $conn->prepare("SELECT * FROM `payment_histories` ORDER BY id DESC LIMIT 10");
                          $show_products->execute();
                          if($show_products->rowCount() > 0){
                             while($fetch_products = $show_products->fetch(PDO::FETCH_ASSOC)){  
                               $is_pending = !$fetch_products['verified'] || !$fetch_products['confirm'];
                       ?>
                        <tr>
                          <th scope="row"><?= $fetch_products['id']; ?></th>
                          <td><?= $fetch_products['user_id']; ?></td>
                          <td><span class="badge bg-info"><?= number_format($fetch_products['point']); ?></span></td>
                          <td>
                            <img src="<?= htmlspecialchars(getImagePath($fetch_products['screenshot_url'] ?? '', 'screenshots')); ?>" 
                                 style="width: 80px; height: 80px; object-fit: cover; border-radius: 4px;" 
                                 onerror="this.src='../img/placeholder.jpg'"
                                 alt="Screenshot">
                          </td>
                          <td><?= htmlspecialchars($fetch_products['date'] ?? 'N/A'); ?></td>
                          <td>
                            <?php if($is_pending): ?>
                              <span class="badge bg-warning">
                                <i class="bi bi-clock"></i> Pending
                              </span>
                            <?php else: ?>
                              <span class="badge bg-success">
                                <i class="bi bi-check-circle"></i> Approved
                              </span>
                            <?php endif; ?>
                          </td>
                          <td>
                            <div class="btn-group" role="group">
                              <?php if($is_pending): ?>
                                <a href="unapprove_payment.php?update=<?= $fetch_products['id']; ?>" 
                                   class="btn btn-sm btn-outline-success" 
                                   title="Approve Payment">
                                  <i class="bi bi-check-circle"></i>
                                </a>
                              <?php endif; ?>
                              <a href="unapprove_payment.php?delete=<?= $fetch_products['id']; ?>" 
                                 class="btn btn-sm btn-outline-danger" 
                                 onclick="return confirm('Are you sure you want to delete this payment?');"
                                 title="Delete Payment">
                                <i class="bi bi-trash"></i>
                              </a>
                            </div>
                          </td>
                        </tr>
                        <?php
                            }
                         }else{
                            echo '<tr><td colspan="7" class="text-center py-4"><p class="text-muted mb-0">No payment histories found</p></td></tr>';
                         }
                         ?>
                      </tbody>
                    </table>
                  </div>

                </div>

              </div>
            </div>
            <!-- End Recent Sales -->

            <!-- Admins Table -->
            <div class="col-12">
              <div class="card recent-sales overflow-auto">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">All Admins <span>| Collection</span></h5>
                    <a href="admin.php" class="btn btn-sm btn-primary">
                      <i class="bi bi-eye"></i> View All
                    </a>
                  </div>

                  <div class="table-responsive">
                    <table class="table table-borderless datatable">
                      <thead>
                        <tr>
                          <th scope="col" style="width: 60px;">#</th>
                          <th scope="col">Name</th>
                          <th scope="col">Email</th>
                          <th scope="col" style="width: 120px;">Phone</th>
                          <th scope="col" style="width: 100px;">Status</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                          $show_products = $conn->prepare("SELECT * FROM `admin` ORDER BY id DESC LIMIT 10");
                          $show_products->execute();
                          if($show_products->rowCount() > 0){
                             while($fetch_products = $show_products->fetch(PDO::FETCH_ASSOC)){  
                       ?>
                        <tr>
                          <th scope="row"><?= $fetch_products['id']; ?></th>
                          <td><strong><?= htmlspecialchars($fetch_products['username'] ?? 'N/A'); ?></strong></td>
                          <td><?= htmlspecialchars($fetch_products['email'] ?? 'N/A'); ?></td>
                          <td><?= htmlspecialchars($fetch_products['phone'] ?? 'N/A'); ?></td>
                          <td>
                            <?php if($fetch_products['is_active'] == 1): ?>
                              <span class="badge bg-success">
                                <i class="bi bi-check-circle"></i> Active
                              </span>
                            <?php else: ?>
                              <span class="badge bg-secondary">
                                <i class="bi bi-x-circle"></i> Inactive
                              </span>
                            <?php endif; ?>
                          </td>
                        </tr>
                        <?php
                            }
                         }else{
                            echo '<tr><td colspan="5" class="text-center py-4"><p class="text-muted mb-0">No admins found</p></td></tr>';
                         }
                         ?>
                      </tbody>
                    </table>
                  </div>

                </div>

              </div>
            </div>
            <!-- End Recent Sales -->

            <!-- Users Table -->
            <div class="col-12">
              <div class="card recent-sales overflow-auto">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">All Users <span>| Collection</span></h5>
                    <a href="users.php" class="btn btn-sm btn-primary">
                      <i class="bi bi-eye"></i> View All
                    </a>
                  </div>

                  <div class="table-responsive">
                    <table class="table table-borderless datatable">
                      <thead>
                        <tr>
                          <th scope="col" style="width: 60px;">#</th>
                          <th scope="col" style="width: 60px;">Image</th>
                          <th scope="col">Name</th>
                          <th scope="col">Email</th>
                          <th scope="col" style="width: 120px;">Phone</th>
                          <th scope="col" style="width: 80px;">VIP</th>
                          <th scope="col" style="width: 100px;">Points</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                          $show_products = $conn->prepare("SELECT * FROM `users` ORDER BY id DESC LIMIT 10");
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
                          <td><strong><?= htmlspecialchars(($fetch_products['first_name'] ?? '') . ' ' . ($fetch_products['last_name'] ?? '')); ?></strong></td>
                          <td><?= htmlspecialchars($fetch_products['email'] ?? 'N/A'); ?></td>
                          <td><?= htmlspecialchars($fetch_products['phone'] ?? 'N/A'); ?></td>
                          <td>
                            <?php if($fetch_products['is_vip'] == 1): ?>
                              <span class="badge bg-warning">
                                <i class="bi bi-star-fill"></i> VIP
                              </span>
                            <?php else: ?>
                              <span class="badge bg-secondary">Regular</span>
                            <?php endif; ?>
                          </td>
                          <td><span class="badge bg-info"><?= number_format($fetch_products['point'] ?? 0); ?></span></td>
                        </tr>
                        <?php
                            }
                         }else{
                            echo '<tr><td colspan="7" class="text-center py-4"><p class="text-muted mb-0">No users found</p></td></tr>';
                         }
                         ?>
                      </tbody>
                    </table>
                  </div>

                </div>

              </div>
            </div><!-- End Recent Sales -->

            <!-- Recent Series (still shows series with their chapter content indirectly) -->
            <div class="col-12">
              <div class="card recent-sales overflow-auto">
                <div class="card-body pb-0">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">Recent Series <span>| Latest Updates</span></h5>
                    <a href="series.php" class="btn btn-sm btn-primary">
                      <i class="bi bi-eye"></i> View All
                    </a>
                  </div>

                  <div class="table-responsive">
                    <table class="table table-borderless">
                      <thead>
                        <tr>
                          <th scope="col" style="width: 80px;">Image</th>
                          <th scope="col">Title</th>
                          <th scope="col">Description</th>
                          <th scope="col" style="width: 100px;">Rating</th>
                          <th scope="col" style="width: 100px;">Views</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                          $show_products = $conn->prepare("SELECT * FROM `series` ORDER BY id DESC LIMIT 10");
                          $show_products->execute();
                          if($show_products->rowCount() > 0){
                             while($fetch_products = $show_products->fetch(PDO::FETCH_ASSOC)){  
                       ?>
                        <tr>
                          <td>
                            <div class="series-image-container-small">
                              <a href="manage_series.php?update=<?= $fetch_products['id']; ?>">
                                <img src="<?= htmlspecialchars(getImagePath($fetch_products['image_url'] ?? '', 'series')); ?>" 
                                     alt="<?= htmlspecialchars($fetch_products['title'] ?? ''); ?>" 
                                     class="series-thumbnail-small"
                                     onerror="this.src='../img/placeholder.jpg'">
                              </a>
                            </div>
                          </td>
                          <td>
                            <a href="manage_series.php?update=<?= $fetch_products['id']; ?>" class="text-primary fw-bold text-decoration-none">
                              <?= htmlspecialchars($fetch_products['title'] ?? 'Untitled'); ?>
                            </a>
                          </td>
                          <td>
                            <span class="text-muted">
                              <?= htmlspecialchars(substr($fetch_products['description'] ?? '', 0, 100)); ?>
                              <?= strlen($fetch_products['description'] ?? '') > 100 ? '...' : ''; ?>
                            </span>
                          </td>
                          <td>
                            <div class="d-flex align-items-center">
                              <i class="bi bi-star-fill text-warning me-1"></i>
                              <span class="fw-bold"><?= number_format($fetch_products['rating'] ?? 0, 1); ?></span>
                            </div>
                          </td>
                          <td>
                            <div class="d-flex align-items-center">
                              <i class="bi bi-eye text-primary me-1"></i>
                              <span><?= number_format($fetch_products['view'] ?? 0); ?></span>
                            </div>
                          </td>
                        </tr>
                        <?php
                            }
                         }else{
                            echo '<tr><td colspan="5" class="text-center py-4"><p class="text-muted mb-0">No series found</p></td></tr>';
                         }
                         ?>
                      </tbody>
                    </table>
                  </div>

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