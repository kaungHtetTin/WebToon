<?php

include('config.php');
require_once('includes/image_helper.php');
require_once('includes/admin_auth.php');

session_start();

requirePermission('admin');

$message = [];

if (isset($_GET['delete'])) {
   $delete_id = (int)$_GET['delete'];

   if ($delete_id <= 0) {
      $message[] = 'Invalid admin id.';
   } elseif ($delete_id === (int)$_SESSION['admin_id']) {
      $message[] = 'You cannot delete your own admin account.';
   } else {
      // Prevent deleting the last admin who can manage admins.
      try {
         $count_stmt = $conn->prepare("
            SELECT COUNT(DISTINCT m.admin_id) AS c
            FROM admin_permission_map m
            INNER JOIN admin_permissions p ON p.id = m.permission_id
            INNER JOIN admin a ON a.id = m.admin_id
            WHERE p.permission_key = 'admin' AND a.is_active = 1
         ");
         $count_stmt->execute();
         $row = $count_stmt->fetch(PDO::FETCH_ASSOC);
         $admin_managers = $row ? (int)$row['c'] : 0;

         $target_has_admin_perm_stmt = $conn->prepare("
            SELECT 1
            FROM admin_permission_map m
            INNER JOIN admin_permissions p ON p.id = m.permission_id
            WHERE m.admin_id = ? AND p.permission_key = 'admin'
            LIMIT 1
         ");
         $target_has_admin_perm_stmt->execute([$delete_id]);
         $target_has_admin_perm = (bool)$target_has_admin_perm_stmt->fetchColumn();

         if ($target_has_admin_perm && $admin_managers <= 1) {
            $message[] = 'Cannot delete the last admin who can manage admin accounts.';
         } else {
            $delete_products = $conn->prepare("DELETE FROM `admin` WHERE id = ?");
            $delete_products->execute([$delete_id]);
            header('location:admin.php');
            exit;
         }
      } catch (Exception $e) {
         $message[] = 'Failed to delete admin.';
      }
   }
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
      <h1>Admin</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item">Admin</li>
          <li class="breadcrumb-item active">View Admin</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">View Admin</h5>
                <a href="manage_admin.php" class="btn btn-primary">
                  <i class="bi bi-plus-circle"></i> Add Admin
                </a>
              </div>
              <!-- <p>Add lightweight datatables to your project with using the class name to any table you wish to conver to a datatable</p>-->
              
              <?php if (!empty($message)): ?>
                <div class="alert alert-danger">
                  <?php foreach ($message as $msg): ?>
                    <div><?= htmlspecialchars($msg); ?></div>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>

              <!-- Table with stripped rows -->
              <table class="table datatable">
                <thead>
                  <tr>
                    <th scope="col">ID</th>
                    <th scope="col">Name</th>
                    <th scope="col">Email</th>
                    <th scope="col">Profile</th>
                    <th scope="col">Permissions</th>
                    <th scope="col">Status</th>
                    <th scope="col">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                        $total_perm_count = 0;
                        $cnt = $conn->prepare("SELECT COUNT(*) FROM admin_permissions WHERE is_active = 1");
                        $cnt->execute();
                        $total_perm_count = (int)$cnt->fetchColumn();

                        $show_products = $conn->prepare("
                           SELECT a.*, COALESCE(pm.perm_count, 0) AS perm_count
                           FROM `admin` a
                           LEFT JOIN (
                              SELECT admin_id, COUNT(*) AS perm_count
                              FROM admin_permission_map
                              GROUP BY admin_id
                           ) pm ON pm.admin_id = a.id
                           ORDER BY a.id ASC
                        ");
                        $show_products->execute();
                        if($show_products->rowCount() > 0){
                           while($fetch_products = $show_products->fetch(PDO::FETCH_ASSOC)){
                              $perm_count = (int)$fetch_products['perm_count'];
                              $is_full = ($total_perm_count > 0 && $perm_count >= $total_perm_count);
                     ?>
                      <tr>
                        <td><?= (int)$fetch_products['id']; ?></td>
                        <td><?= htmlspecialchars($fetch_products['username']); ?></td>
                        <td><?= htmlspecialchars($fetch_products['email']); ?></td>
                        <td>
                        <div class="post-item clearfix">
                           <img src="<?= htmlspecialchars(getImagePath($fetch_products['image_url'] ?? '', 'admin')); ?>" alt="" style="height: 50px;width: 50px; border-radius: 50%;" onerror="this.src='../img/placeholder.jpg'">
                        </div>
                        </td>
                        <td>
                          <?php if ($is_full): ?>
                            <span class="badge bg-success">All access (<?= $perm_count; ?>/<?= $total_perm_count; ?>)</span>
                          <?php elseif ($perm_count === 0): ?>
                            <span class="badge bg-secondary">No permissions</span>
                          <?php else: ?>
                            <span class="badge bg-info"><?= $perm_count; ?>/<?= $total_perm_count; ?> pages</span>
                          <?php endif; ?>
                        </td>
                        <td>
                          <?php if ((int)$fetch_products['is_active'] === 1): ?>
                            <span class="badge bg-success">Active</span>
                          <?php else: ?>
                            <span class="badge bg-secondary">Inactive</span>
                          <?php endif; ?>
                        </td>
                        <td>
                          <a href="manage_admin.php?update=<?= (int)$fetch_products['id']; ?>"><span class="badge bg-warning">Edit</span></a>
                          <?php if ((int)$fetch_products['id'] !== (int)$_SESSION['admin_id']): ?>
                            | <a href="admin.php?delete=<?= (int)$fetch_products['id']; ?>" onclick="return confirm('Delete this admin?');"><span class="badge bg-danger">Delete</span></a>
                          <?php endif; ?>
                        </td>
                      </tr>
                      <?php
                          }
                       }else{
                          echo '<tr><td colspan="7" class="text-center text-muted">No admin accounts yet.</td></tr>';
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