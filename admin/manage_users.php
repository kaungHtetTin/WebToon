<?php

include('config.php');
require_once('includes/image_helper.php');

session_start();

// Handle optional password update
if (isset($_POST['update_users'])) {

  $pid = $_POST['pid'] ?? null;
  $password_raw = $_POST['password'] ?? '';

  if ($pid && $password_raw !== '') {
    $password = md5($password_raw);
    $password = filter_var($password, FILTER_SANITIZE_STRING);

    $update_product = $conn->prepare("UPDATE `users` SET password = ? WHERE id = ?");
    $update_product->execute([$password, $pid]);
  }

  header('location:manage_users.php?update=' . urlencode($pid));
  exit;
}

// Load user and related data
$user_id = isset($_GET['update']) ? (int)$_GET['update'] : 0;
$user = null;
$payment_histories = [];
$saved_series = [];

if ($user_id > 0) {
  // User details
  $stmt = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
  $stmt->execute([$user_id]);
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($user) {
    // Payment histories
    $ph_stmt = $conn->prepare("
      SELECT * FROM `payment_histories`
      WHERE user_id = ?
      ORDER BY id DESC
      LIMIT 50
    ");
    $ph_stmt->execute([$user_id]);
    $payment_histories = $ph_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Saved series (purchased / saved by this user)
    $ss_stmt = $conn->prepare("
      SELECT 
        s.*,
        c.title AS category_title,
        sv.date AS saved_date
      FROM saves sv
      JOIN series s ON s.id = sv.series_id
      LEFT JOIN categories c ON c.id = s.category_id
      WHERE sv.user_id = ?
      ORDER BY sv.date DESC, sv.id DESC
    ");
    $ss_stmt->execute([$user_id]);
    $saved_series = $ss_stmt->fetchAll(PDO::FETCH_ASSOC);
  }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>User Details - Admin</title>
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

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>User Details</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item"><a href="users.php">Users</a></li>
          <li class="breadcrumb-item active">User Details</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="row">

        <?php if (!$user): ?>
          <div class="col-12">
            <div class="card">
              <div class="card-body">
                <h5 class="card-title">User not found</h5>
                <p class="mb-0">The requested user does not exist. Please go back to the users list.</p>
                <a href="users.php" class="btn btn-secondary mt-3">
                  <i class="bi bi-arrow-left"></i> Back to Users
                </a>
              </div>
            </div>
          </div>
        <?php else: ?>

        <!-- User profile + summary -->
        <div class="col-lg-4">
          <div class="card">
            <div class="card-body text-center pt-4">
              <?php
                $avatar_path = getImagePath($user['image_url'] ?? '', 'admin');
              ?>
              <div class="user-image-container mb-3 mx-auto">
                <img src="<?= htmlspecialchars($avatar_path); ?>"
                     alt="Avatar"
                     class="user-avatar"
                     onerror="this.src='../img/placeholder.jpg'">
              </div>
              <h5 class="mb-1">
                <?= htmlspecialchars(trim(($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '')) ?: 'User #'.$user['id']); ?>
              </h5>
              <p class="text-muted mb-1">
                <?= htmlspecialchars($user['email'] ?? ''); ?>
              </p>
              <p class="text-muted small mb-3">
                <?= htmlspecialchars($user['phone'] ?? ''); ?>
              </p>

              <div class="d-flex justify-content-center gap-2 mb-3">
                <span class="badge <?= !empty($user['is_vip']) ? 'bg-success' : 'bg-secondary'; ?>">
                  <i class="bi bi-star-fill me-1"></i>
                  <?= !empty($user['is_vip']) ? 'VIP' : 'Normal'; ?>
                </span>
                <span class="badge bg-info">
                  <i class="bi bi-coin me-1"></i>
                  <?= htmlspecialchars($user['point'] ?? 0); ?> points
                </span>
              </div>

              <!-- Optional: Admin password reset -->
              <button class="btn btn-outline-secondary btn-sm" type="button" data-bs-toggle="collapse" data-bs-target="#passwordResetForm" aria-expanded="false">
                <i class="bi bi-key"></i> Reset Password
              </button>

              <div class="collapse mt-3" id="passwordResetForm">
                <form action="" method="POST">
                  <input type="hidden" name="pid" value="<?= $user['id']; ?>">
                  <div class="mb-2 text-start">
                    <label class="form-label">New Password</label>
                    <input type="password" name="password" class="form-control" placeholder="Enter new password">
                  </div>
                  <button type="submit" name="update_users" class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-save"></i> Save New Password
                  </button>
                </form>
              </div>
            </div>
          </div>
        </div>

        <!-- Right side: Payment histories + Saved series -->
        <div class="col-lg-8">
          <div class="row">

            <!-- Payment Histories -->
            <div class="col-12">
              <div class="card recent-sales overflow-auto">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">Payment Histories</h5>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-borderless align-middle">
                      <thead>
                        <tr>
                          <th scope="col">#</th>
                          <th scope="col">Amount</th>
                          <th scope="col">Points</th>
                          <th scope="col">Status</th>
                          <th scope="col">Date</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php if (!empty($payment_histories)): ?>
                          <?php foreach ($payment_histories as $ph): ?>
                            <tr>
                              <td class="text-muted"><?= htmlspecialchars($ph['id']); ?></td>
                              <td>
                                <span class="badge bg-success">
                                  <?= htmlspecialchars($ph['amount'] ?? 0); ?> Ks
                                </span>
                              </td>
                              <td>
                                <span class="badge bg-info">
                                  <?= htmlspecialchars($ph['point'] ?? $ph['points_added'] ?? 0); ?> pts
                                </span>
                              </td>
                              <td>
                                <?php
                                  $isApproved = !empty($ph['verified']) && !empty($ph['confirm']) && ($ph['status'] === 'approved');
                                ?>
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
                                <?= !empty($ph['date']) ? htmlspecialchars(date('d M Y', strtotime($ph['date']))) : '-'; ?>
                              </td>
                            </tr>
                          <?php endforeach; ?>
                        <?php else: ?>
                          <tr>
                            <td colspan="5" class="text-center py-4">
                              <div class="empty-state">
                                <i class="bi bi-inbox empty-state-icon"></i>
                                <h5>No payment history</h5>
                                <p class="text-muted mb-0">This user has not submitted any payments yet.</p>
                              </div>
                            </td>
                          </tr>
                        <?php endif; ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>

            <!-- Saved Series -->
            <div class="col-12">
              <div class="card recent-sales overflow-auto">
                <div class="card-body">
                  <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="card-title mb-0">Saved Series</h5>
                  </div>
                  <div class="table-responsive">
                    <table class="table table-borderless align-middle">
                      <thead>
                        <tr>
                          <th scope="col">Series</th>
                          <th scope="col">Category</th>
                          <th scope="col">Point</th>
                          <th scope="col">Saved Date</th>
                          <th scope="col">Actions</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php if (!empty($saved_series)): ?>
                          <?php foreach ($saved_series as $series): ?>
                            <tr>
                              <td>
                                <strong><?= htmlspecialchars($series['title'] ?? 'Series #'.$series['id']); ?></strong>
                              </td>
                              <td><?= htmlspecialchars($series['category_title'] ?? '-'); ?></td>
                              <td>
                                <span class="badge bg-info">
                                  <i class="bi bi-coin me-1"></i>
                                  <?= htmlspecialchars($series['point'] ?? 0); ?>
                                </span>
                              </td>
                              <td>
                                <?= !empty($series['saved_date']) ? htmlspecialchars(date('d M Y', strtotime($series['saved_date']))) : '-'; ?>
                              </td>
                              <td>
                                <a href="../details.php?id=<?= $series['id']; ?>" target="_blank" class="btn btn-sm btn-outline-primary">
                                  <i class="bi bi-box-arrow-up-right"></i> View
                                </a>
                              </td>
                            </tr>
                          <?php endforeach; ?>
                        <?php else: ?>
                          <tr>
                            <td colspan="5" class="text-center py-4">
                              <div class="empty-state">
                                <i class="bi bi-collection-play empty-state-icon"></i>
                                <h5>No saved series</h5>
                                <p class="text-muted mb-0">This user has not purchased/saved any series yet.</p>
                              </div>
                            </td>
                          </tr>
                        <?php endif; ?>
                      </tbody>
                    </table>
                  </div>
                </div>
              </div>
            </div>

          </div>
        </div>

        <?php endif; ?>

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


