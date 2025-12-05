<?php

include('config.php');

session_start();

// Auth guard
if (!isset($_SESSION['admin_id'])) {
  header('location:login.php');
  exit;
}

$message = [];

if (isset($_POST['add_payment_method'])) {

  $payment_type = isset($_POST['payment_type']) ? trim($_POST['payment_type']) : '';
  $payment_type = filter_var($payment_type, FILTER_SANITIZE_STRING);

  $payment_number = isset($_POST['payment_number']) ? trim($_POST['payment_number']) : '';
  $payment_number = filter_var($payment_number, FILTER_SANITIZE_STRING);

  $account_name = isset($_POST['account_name']) ? trim($_POST['account_name']) : '';
  $account_name = filter_var($account_name, FILTER_SANITIZE_STRING);

  if ($payment_type === '' || $payment_number === '' || $account_name === '') {
    $message[] = 'All fields are required.';
  } else {
    $stmt = $conn->prepare("INSERT INTO `payment_methods` (payment_type, payment_number, account_name) VALUES (?, ?, ?)");
    $result = $stmt->execute([$payment_type, $payment_number, $account_name]);

    if ($result) {
      header('location:payment_methods.php?success=' . urlencode('Payment method added successfully!'));
      exit;
    } else {
      $message[] = 'Failed to add payment method. Please try again.';
    }
  }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Add Payment Method - Admin</title>
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
      <h1>Add Payment Method</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item">Users &amp; Payments</li>
          <li class="breadcrumb-item"><a href="payment_methods.php">Payment Methods</a></li>
          <li class="breadcrumb-item active">Add</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-8">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Payment Method Details</h5>

              <?php if (!empty($message)) : ?>
                <div class="alert alert-danger">
                  <?php foreach ($message as $msg) : ?>
                    <div><?= htmlspecialchars($msg); ?></div>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>

              <!-- Payment Method Form -->
              <form action="" method="POST">

                <div class="row mb-3">
                  <label for="payment_type" class="col-sm-3 col-form-label">Payment Type</label>
                  <div class="col-sm-9">
                    <input type="text" class="form-control" id="payment_type" name="payment_type" placeholder="e.g. KBZ Pay, WavePay, Bank Transfer" required>
                    <small class="form-text text-muted">This is the label users will see (e.g. KBZ Pay, WavePay).</small>
                  </div>
                </div>

                <div class="row mb-3">
                  <label for="account_name" class="col-sm-3 col-form-label">Account Name</label>
                  <div class="col-sm-9">
                    <input type="text" class="form-control" id="account_name" name="account_name" placeholder="Account owner name" required>
                  </div>
                </div>

                <div class="row mb-3">
                  <label for="payment_number" class="col-sm-3 col-form-label">Payment Number</label>
                  <div class="col-sm-9">
                    <input type="text" class="form-control" id="payment_number" name="payment_number" placeholder="Phone / Account number" required>
                    <small class="form-text text-muted">This can be phone number or bank account number.</small>
                  </div>
                </div>

                <div class="row mb-3">
                  <div class="col-sm-12 text-end">
                    <a href="payment_methods.php" class="btn btn-outline-secondary me-2">
                      <i class="bi bi-arrow-left"></i> Back
                    </a>
                    <button type="submit" name="add_payment_method" class="btn btn-primary">
                      <i class="bi bi-save"></i> Save Payment Method
                    </button>
                  </div>
                </div>

              </form><!-- End Payment Method Form -->

            </div>
          </div>

        </div>
      </div>
    </section>

  </main><!-- End #main -->

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
  <!-- Navigation Enhancement -->
  <script src="assets/js/navigation.js"></script>
  <!-- UX Enhancements -->
  <script src="assets/js/ux-enhancements.js"></script>

</body>

</html>


