<?php

include('config.php');

session_start();

// Simple auth guard in case this page is accessed directly
if (!isset($_SESSION['admin_id'])) {
  header('location:login.php');
  exit;
}

// Delete logic
if (isset($_GET['delete'])) {

  $delete_id = $_GET['delete'];
  $delete_stmt = $conn->prepare("DELETE FROM `payment_methods` WHERE id = ?");
  $result = $delete_stmt->execute([$delete_id]);

  if ($result) {
    header('location:payment_methods.php?success=' . urlencode('Payment method deleted successfully!'));
  } else {
    header('location:payment_methods.php?error=' . urlencode('Failed to delete payment method. Please try again.'));
  }
  exit();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Payment Methods - Admin</title>
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

</head>

<body>

  <?php include 'admin_header.php'; ?>

  <?php include 'admin_sidebar.php'; ?>

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Payment Methods</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item">Users &amp; Payments</li>
          <li class="breadcrumb-item active">Payment Methods</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">Payment Methods</h5>
                <a href="add_payment_methods.php" class="btn btn-primary">
                  <i class="bi bi-plus-circle"></i> Add Payment Method
                </a>
              </div>

              <!-- Table with stripped rows -->
              <div class="table-responsive">
                <table class="table datatable table-hover">
                  <thead>
                    <tr>
                      <th scope="col" style="width: 60px;">#</th>
                      <th scope="col">Payment Type</th>
                      <th scope="col">Account Name</th>
                      <th scope="col">Payment Number</th>
                      <th scope="col" style="width: 180px;">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                    $stmt = $conn->prepare("SELECT * FROM `payment_methods` ORDER BY id DESC");
                    $stmt->execute();
                    if ($stmt->rowCount() > 0) {
                      while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    ?>
                        <tr>
                          <th scope="row" class="text-muted"><?= htmlspecialchars($row['id']); ?></th>
                          <td>
                            <span class="badge bg-primary">
                              <i class="bi bi-wallet2 me-1"></i><?= htmlspecialchars($row['payment_type']); ?>
                            </span>
                          </td>
                          <td><?= htmlspecialchars($row['account_name']); ?></td>
                          <td class="font-monospace"><?= htmlspecialchars($row['payment_number']); ?></td>
                          <td>
                            <div class="btn-group" role="group">
                              <a href="manage_payment_methods.php?update=<?= $row['id']; ?>"
                                 class="btn btn-sm btn-outline-primary"
                                 data-bs-toggle="tooltip"
                                 title="Edit payment method">
                                <i class="bi bi-pencil"></i>
                              </a>
                              <a href="payment_methods.php?delete=<?= $row['id']; ?>"
                                 class="btn btn-sm btn-outline-danger"
                                 onclick="return confirm('Are you sure you want to delete this payment method?');"
                                 data-bs-toggle="tooltip"
                                 title="Delete payment method">
                                <i class="bi bi-trash"></i>
                              </a>
                            </div>
                          </td>
                        </tr>
                    <?php
                      }
                    } else {
                      echo '<tr><td colspan="5" class="text-center py-5">
                              <div class="empty-state">
                                <i class="bi bi-wallet2 empty-state-icon"></i>
                                <h5>No payment methods found</h5>
                                <p class="text-muted">Get started by adding your first payment method.</p>
                                <a href="add_payment_methods.php" class="btn btn-primary mt-3">
                                  <i class="bi bi-plus-circle"></i> Add Payment Method
                                </a>
                              </div>
                            </td></tr>';
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

  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">
    <div class="copyright">
      &copy; Copyright <strong><span>NiceAdmin</span></strong>. All Rights Reserved
    </div>
    <div class="credits">
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


