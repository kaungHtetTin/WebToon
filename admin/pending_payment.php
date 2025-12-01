<?php

include('config.php');
require_once('includes/image_helper.php');

session_start();

// Ensure admin is logged in
if (!isset($_SESSION['admin_id'])) {
  header('location:login.php');
  exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Pending Payments - Admin</title>
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

</head>

<body>

<?php include 'admin_header.php'; ?>

<?php include 'admin_sidebar.php'; ?>

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Pending Payments</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item">Users &amp; Payments</li>
          <li class="breadcrumb-item active">Pending Payments</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="row">

        <div class="col-lg-12">
          <div class="row">

            <div class="col-12">
              <div class="card recent-sales overflow-auto">

                <div class="card-body">
                  <h5 class="card-title">Payment Requests Awaiting Approval</h5>

                  <div class="table-responsive">
                    <table class="table table-borderless datatable align-middle">
                      <thead>
                        <tr>
                          <th scope="col">#</th>
                          <th scope="col">User</th>
                          <th scope="col">Email</th>
                          <th scope="col">Screenshot</th>
                          <th scope="col">Amount</th>
                          <th scope="col">Points</th>
                          <th scope="col">Status</th>
                          <th scope="col">Created At</th>
                          <th scope="col">Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php
                          // Load all non-approved payment histories
                          $payments_stmt = $conn->prepare("
                            SELECT ph.*, u.first_name, u.last_name, u.email 
                            FROM `payment_histories` ph 
                            LEFT JOIN `users` u ON ph.user_id = u.id 
                            WHERE (ph.verified = 0 OR ph.confirm = 0 OR ph.status IS NULL OR ph.status != 'approved')
                            ORDER BY ph.id DESC
                          ");
                          $payments_stmt->execute();
                          if ($payments_stmt->rowCount() > 0) {
                            while ($payment = $payments_stmt->fetch(PDO::FETCH_ASSOC)) {
                              $full_name = trim(($payment['first_name'] ?? '') . ' ' . ($payment['last_name'] ?? ''));
                              $screenshot_path = !empty($payment['screenshot_url'])
                                ? getImagePath($payment['screenshot_url'], 'screenshots')
                                : '';
                              
                              $isApproved = !empty($payment['verified']) && !empty($payment['confirm']) && ($payment['status'] === 'approved');
                        ?>
                        <tr>
                          <th scope="row" class="text-muted"><?= htmlspecialchars($payment['id']); ?></th>
                          <td>
                            <div class="fw-semibold"><?= htmlspecialchars($full_name ?: 'Unknown User'); ?></div>
                          </td>
                          <td><?= htmlspecialchars($payment['email'] ?? ''); ?></td>
                          <td>
                            <?php if ($screenshot_path): ?>
                              <a href="<?= htmlspecialchars($screenshot_path); ?>" target="_blank">
                                <img src="<?= htmlspecialchars($screenshot_path); ?>"
                                     alt="Screenshot"
                                     style="width: 60px; height: 60px; object-fit: cover; border-radius: 4px;"
                                     onerror="this.src='../img/placeholder.jpg'">
                              </a>
                            <?php else: ?>
                              <span class="text-muted">No screenshot</span>
                            <?php endif; ?>
                          </td>
                          <td>
                            <span class="badge bg-success">
                              <?= htmlspecialchars($payment['amount'] ?? '0'); ?> Ks
                            </span>
                          </td>
                          <td>
                            <span class="badge bg-info">
                              <?= htmlspecialchars($payment['point'] ?? $payment['points_added'] ?? 0); ?> pts
                            </span>
                          </td>
                          <td>
                            <?php if ($isApproved): ?>
                              <span class="badge bg-success">
                                <i class="bi bi-check-circle"></i> Approved
                              </span>
                            <?php else: ?>
                              <span class="badge bg-warning text-dark">
                                <i class="bi bi-hourglass-split"></i> Pending
                              </span>
                            <?php endif; ?>
                          </td>
                          <td>
                            <?php if (!empty($payment['date'])): ?>
                              <span class="text-muted small">
                                <?= htmlspecialchars(date('d M Y', strtotime($payment['date']))); ?>
                              </span>
                            <?php else: ?>
                              <span class="text-muted small">-</span>
                            <?php endif; ?>
                          </td>
                          <td>
                            <div class="btn-group" role="group">
                              <a href="unapprove_payment.php?update=<?= $payment['id']; ?>"
                                 class="btn btn-sm btn-outline-primary"
                                 title="Review &amp; approve">
                                <i class="bi bi-check2-circle"></i>
                              </a>
                              <a href="unapprove_payment.php?delete=<?= $payment['id']; ?>"
                                 class="btn btn-sm btn-outline-danger"
                                 onclick="return confirm('Are you sure you want to delete this payment request?');"
                                 title="Delete request">
                                <i class="bi bi-trash"></i>
                              </a>
                            </div>
                          </td>
                        </tr>
                        <?php
                            }
                          } else {
                            echo '<tr><td colspan="9" class="text-center py-5">
                                    <div class="empty-state">
                                      <i class="bi bi-inbox empty-state-icon"></i>
                                      <h5>No pending payment requests</h5>
                                      <p class="text-muted mb-0">New payment screenshots from users will appear here for approval.</p>
                                    </div>
                                  </td></tr>';
                          }
                        ?>
                      </tbody>
                    </table>
                  </div>

                </div>

              </div>
            </div><!-- End Pending Payments Table -->

          </div>
        </div><!-- End Left side columns -->

      </div>
    </section>

  </main><!-- End #main -->

  <?php include 'admin_footer.php'; ?>

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


