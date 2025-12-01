<?php

include('config.php');

session_start();

if (!isset($_SESSION['admin_id'])) {
  header('location:login.php');
  exit;
}

$message = [];

// Load existing record
$point_price = null;
if (isset($_GET['update'])) {
  $update_id = $_GET['update'];
  $stmt = $conn->prepare("SELECT * FROM `point_prices` WHERE id = ?");
  $stmt->execute([$update_id]);
  if ($stmt->rowCount() > 0) {
    $point_price = $stmt->fetch(PDO::FETCH_ASSOC);
  } else {
    header('location:point_prices.php?error=' . urlencode('Point price not found.'));
    exit;
  }
} else {
  header('location:point_prices.php');
  exit;
}

if (isset($_POST['update_point_price'])) {

  $point = isset($_POST['point']) ? trim($_POST['point']) : '';
  $amount = isset($_POST['amount']) ? trim($_POST['amount']) : '';

  if ($point === '' || $amount === '') {
    $message[] = 'Both point and amount are required.';
  } elseif (!is_numeric($point) || !is_numeric($amount) || $point <= 0 || $amount <= 0) {
    $message[] = 'Point and amount must be positive numbers.';
  } else {
    // Optional: prevent duplicate point value on another row
    $check = $conn->prepare("SELECT id FROM `point_prices` WHERE point = ? AND id != ?");
    $check->execute([$point, $point_price['id']]);
    if ($check->rowCount() > 0) {
      $message[] = 'Another price for this point amount already exists.';
    } else {
      $stmt = $conn->prepare("UPDATE `point_prices` SET point = ?, amount = ? WHERE id = ?");
      $result = $stmt->execute([$point, $amount, $point_price['id']]);

      if ($result) {
        header('location:point_prices.php?success=' . urlencode('Point price updated successfully!'));
        exit;
      } else {
        $message[] = 'Failed to update point price. Please try again.';
      }
    }
  }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Edit Point Price - Admin</title>
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
      <h1>Edit Point Price</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item">Users &amp; Payments</li>
          <li class="breadcrumb-item"><a href="point_prices.php">Point Prices</a></li>
          <li class="breadcrumb-item active">Edit</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-6">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Point Price Details</h5>

              <?php if (!empty($message)) : ?>
                <div class="alert alert-danger">
                  <?php foreach ($message as $msg) : ?>
                    <div><?= htmlspecialchars($msg); ?></div>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>

              <!-- Point Price Form -->
              <form action="" method="POST">

                <div class="row mb-3">
                  <label for="point" class="col-sm-4 col-form-label">Points</label>
                  <div class="col-sm-8">
                    <input type="number"
                           class="form-control"
                           id="point"
                           name="point"
                           min="1"
                           step="1"
                           value="<?= htmlspecialchars($point_price['point']); ?>"
                           required>
                  </div>
                </div>

                <div class="row mb-3">
                  <label for="amount" class="col-sm-4 col-form-label">Amount (Kyats)</label>
                  <div class="col-sm-8">
                    <input type="number"
                           class="form-control"
                           id="amount"
                           name="amount"
                           min="1"
                           step="1"
                           value="<?= htmlspecialchars($point_price['amount']); ?>"
                           required>
                  </div>
                </div>

                <div class="row mb-3">
                  <div class="col-sm-12 text-end">
                    <a href="point_prices.php" class="btn btn-outline-secondary me-2">
                      <i class="bi bi-arrow-left"></i> Back
                    </a>
                    <button type="submit" name="update_point_price" class="btn btn-primary">
                      <i class="bi bi-save"></i> Update Point Price
                    </button>
                  </div>
                </div>

              </form><!-- End Point Price Form -->

            </div>
          </div>

        </div>
      </div>
    </section>

  </main><!-- End #main -->

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


