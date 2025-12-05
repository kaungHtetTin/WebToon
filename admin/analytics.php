<?php

  include('config.php');
  require_once('includes/image_helper.php');

  session_start();

  // Ensure admin is logged in
  if (!isset($_SESSION['admin_id'])) {
    header('location:login.php');
    exit;
  }

  // Financial Analytics - Get filter parameters
  $selected_year = isset($_GET['year']) ? (int)$_GET['year'] : date('Y');
  $selected_month = isset($_GET['month']) ? (int)$_GET['month'] : 0; // 0 means all months
  
  // Build date filter conditions
  $date_filter = "";
  $date_params = [];
  
  if ($selected_month > 0) {
    // Filter by specific year and month
    $month_start = sprintf('%04d-%02d-01', $selected_year, $selected_month);
    $month_end = sprintf('%04d-%02d-%02d', $selected_year, $selected_month, date('t', strtotime($month_start)));
    $date_filter = " AND date >= ? AND date <= ? AND date IS NOT NULL";
    $date_params = [$month_start, $month_end];
  } else {
    // Filter by year only
    $year_start = sprintf('%04d-01-01', $selected_year);
    $year_end = sprintf('%04d-12-31', $selected_year);
    $date_filter = " AND date >= ? AND date <= ? AND date IS NOT NULL";
    $date_params = [$year_start, $year_end];
  }

  // Total approved payments revenue (filtered)
  $total_revenue = 0;
  $stmt = $conn->prepare("SELECT SUM(amount) as total FROM `payment_histories` WHERE verified = 1 AND confirm = 1" . $date_filter);
  $stmt->execute($date_params);
  $revenue_data = $stmt->fetch(PDO::FETCH_ASSOC);
  $total_revenue = $revenue_data['total'] ?? 0;

  // Pending payments count (filtered)
  $stmt = $conn->prepare("SELECT COUNT(*) as count FROM `payment_histories` WHERE (verified = 0 OR confirm = 0)" . $date_filter);
  $stmt->execute($date_params);
  $pending_data = $stmt->fetch(PDO::FETCH_ASSOC);
  $pending_count = $pending_data['count'] ?? 0;

  // Approved payments count (filtered)
  $stmt = $conn->prepare("SELECT COUNT(*) as count FROM `payment_histories` WHERE verified = 1 AND confirm = 1" . $date_filter);
  $stmt->execute($date_params);
  $approved_data = $stmt->fetch(PDO::FETCH_ASSOC);
  $approved_count = $approved_data['count'] ?? 0;

  // Total payments (filtered)
  $stmt = $conn->prepare("SELECT COUNT(*) as count FROM `payment_histories` WHERE 1=1" . $date_filter);
  $stmt->execute($date_params);
  $total_payments_data = $stmt->fetch(PDO::FETCH_ASSOC);
  $total_payments = $total_payments_data['count'] ?? 0;
  
  // Get all available years from payment_histories
  $stmt = $conn->prepare("SELECT DISTINCT YEAR(date) as year FROM `payment_histories` WHERE date IS NOT NULL ORDER BY year DESC");
  $stmt->execute();
  $available_years = $stmt->fetchAll(PDO::FETCH_COLUMN);
  if (empty($available_years)) {
    $available_years = [date('Y')];
  }

  // Payments by month - if month filter is selected, show daily data; otherwise show monthly data for the year
  $payments_by_month = [];
  
  if ($selected_month > 0) {
    // Show daily data for the selected month
    $days_in_month = date('t', strtotime(sprintf('%04d-%02d-01', $selected_year, $selected_month)));
    for ($day = 1; $day <= $days_in_month; $day++) {
      $day_start = sprintf('%04d-%02d-%02d', $selected_year, $selected_month, $day);
      $day_end = $day_start;
      $day_name = date('M d', strtotime($day_start));
      
      try {
        $stmt = $conn->prepare("SELECT COUNT(*) as count, SUM(amount) as total FROM `payment_histories` WHERE date >= ? AND date <= ? AND verified = 1 AND confirm = 1 AND date IS NOT NULL");
        $stmt->execute([$day_start, $day_end]);
        $day_data = $stmt->fetch(PDO::FETCH_ASSOC);
        $payments_by_month[] = [
          'month' => $day_name,
          'count' => (int)($day_data['count'] ?? 0),
          'revenue' => (float)($day_data['total'] ?? 0)
        ];
      } catch (Exception $e) {
        $payments_by_month[] = [
          'month' => $day_name,
          'count' => 0,
          'revenue' => 0
        ];
      }
    }
  } else {
    // Show monthly data for the selected year
    for ($month = 1; $month <= 12; $month++) {
      $month_start = sprintf('%04d-%02d-01', $selected_year, $month);
      $month_end = sprintf('%04d-%02d-%02d', $selected_year, $month, date('t', strtotime($month_start)));
      $month_name = date('M Y', strtotime($month_start));
      
      try {
        $stmt = $conn->prepare("SELECT COUNT(*) as count, SUM(amount) as total FROM `payment_histories` WHERE date >= ? AND date <= ? AND verified = 1 AND confirm = 1 AND date IS NOT NULL");
        $stmt->execute([$month_start, $month_end]);
        $month_data = $stmt->fetch(PDO::FETCH_ASSOC);
        $payments_by_month[] = [
          'month' => $month_name,
          'count' => (int)($month_data['count'] ?? 0),
          'revenue' => (float)($month_data['total'] ?? 0)
        ];
      } catch (Exception $e) {
        $payments_by_month[] = [
          'month' => $month_name,
          'count' => 0,
          'revenue' => 0
        ];
      }
    }
  }

  // User Analytics
  // Total users
  $stmt = $conn->prepare("SELECT COUNT(*) as count FROM `users`");
  $stmt->execute();
  $total_users_data = $stmt->fetch(PDO::FETCH_ASSOC);
  $total_users = $total_users_data['count'] ?? 0;

  // New users this month
  $month_start = date('Y-m-01');
  $month_end = date('Y-m-t');
  $stmt = $conn->prepare("SELECT COUNT(*) as count FROM `users` WHERE created_at >= ? AND created_at <= ?");
  $stmt->execute([$month_start, $month_end]);
  $new_users_data = $stmt->fetch(PDO::FETCH_ASSOC);
  $new_users_month = $new_users_data['count'] ?? 0;

  // Total points purchased (from approved payments)
  $stmt = $conn->prepare("SELECT SUM(points_added) as total FROM `payment_histories` WHERE verified = 1 AND confirm = 1");
  $stmt->execute();
  $points_purchased_data = $stmt->fetch(PDO::FETCH_ASSOC);
  $total_points_purchased = $points_purchased_data['total'] ?? 0;
  
  // If points_added is null, try using point field
  if (!$total_points_purchased || $total_points_purchased == 0) {
    $stmt = $conn->prepare("SELECT SUM(point) as total FROM `payment_histories` WHERE verified = 1 AND confirm = 1");
    $stmt->execute();
    $points_purchased_data = $stmt->fetch(PDO::FETCH_ASSOC);
    $total_points_purchased = $points_purchased_data['total'] ?? 0;
  }

  // Users by month (last 6 months)
  $users_by_month = [];
  for ($i = 5; $i >= 0; $i--) {
    $month_start = date('Y-m-01', strtotime("-$i months"));
    $month_end = date('Y-m-t', strtotime("-$i months"));
    $month_name = date('M Y', strtotime("-$i months"));
    
    try {
      $stmt = $conn->prepare("SELECT COUNT(*) as count FROM `users` WHERE created_at >= ? AND created_at <= ? AND created_at IS NOT NULL");
      $stmt->execute([$month_start, $month_end]);
      $month_data = $stmt->fetch(PDO::FETCH_ASSOC);
      $users_by_month[] = [
        'month' => $month_name,
        'count' => (int)($month_data['count'] ?? 0)
      ];
    } catch (Exception $e) {
      $users_by_month[] = [
        'month' => $month_name,
        'count' => 0
      ];
    }
  }

  // Series Analytics
  // Total series
  $stmt = $conn->prepare("SELECT COUNT(*) as count FROM `series`");
  $stmt->execute();
  $total_series_data = $stmt->fetch(PDO::FETCH_ASSOC);
  $total_series = $total_series_data['count'] ?? 0;

  // Total views
  $stmt = $conn->prepare("SELECT SUM(view) as total FROM `series`");
  $stmt->execute();
  $total_views_data = $stmt->fetch(PDO::FETCH_ASSOC);
  $total_views = $total_views_data['total'] ?? 0;

  // Average rating
  $stmt = $conn->prepare("SELECT AVG(rating) as avg FROM `series` WHERE rating > 0");
  $stmt->execute();
  $avg_rating_data = $stmt->fetch(PDO::FETCH_ASSOC);
  $avg_rating = $avg_rating_data['avg'] ?? 0;

  // Top 10 series by views
  $stmt = $conn->prepare("SELECT id, title, view, rating, image_url FROM `series` ORDER BY view DESC LIMIT 10");
  $stmt->execute();
  $top_series = $stmt->fetchAll(PDO::FETCH_ASSOC);

  // Series by month (last 6 months)
  $series_by_month = [];
  for ($i = 5; $i >= 0; $i--) {
    $month_start = date('Y-m-01', strtotime("-$i months"));
    $month_end = date('Y-m-t', strtotime("-$i months"));
    $month_name = date('M Y', strtotime("-$i months"));
    
    try {
      $stmt = $conn->prepare("SELECT COUNT(*) as count FROM `series` WHERE date >= ? AND date <= ? AND date IS NOT NULL");
      $stmt->execute([$month_start, $month_end]);
      $month_data = $stmt->fetch(PDO::FETCH_ASSOC);
      $series_by_month[] = [
        'month' => $month_name,
        'count' => (int)($month_data['count'] ?? 0)
      ];
    } catch (Exception $e) {
      $series_by_month[] = [
        'month' => $month_name,
        'count' => 0
      ];
    }
  }

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Analytics - WebtoonMM Admin</title>
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

  <!-- Chart.js -->
  <script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>

</head>

<body>

<?php include 'admin_header.php'; ?>

<?php include 'admin_sidebar.php'; ?>

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Analytics</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item active">Analytics</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="row">

        <!-- Financial Activity Section -->
        <div class="col-12">
          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
                <h5 class="card-title mb-0"><i class="bi bi-currency-dollar"></i> Financial Activity</h5>
                
                <!-- Filter Controls - Compact Layout -->
                <form method="GET" action="" class="d-flex gap-2 align-items-center">
                  <div class="input-group input-group-sm" style="width: auto;">
                    <span class="input-group-text bg-light border-end-0"><i class="bi bi-calendar"></i></span>
                    <select name="year" id="year" class="form-select form-select-sm border-start-0" style="width: 90px;" onchange="this.form.submit()">
                      <?php foreach ($available_years as $year): ?>
                        <option value="<?= $year; ?>" <?= $selected_year == $year ? 'selected' : ''; ?>>
                          <?= $year; ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  <div class="input-group input-group-sm" style="width: auto;">
                    <select name="month" id="month" class="form-select form-select-sm" style="width: 130px;" onchange="this.form.submit()">
                      <option value="0" <?= $selected_month == 0 ? 'selected' : ''; ?>>All Months</option>
                      <?php for ($m = 1; $m <= 12; $m++): ?>
                        <option value="<?= $m; ?>" <?= $selected_month == $m ? 'selected' : ''; ?>>
                          <?= date('F', mktime(0, 0, 0, $m, 1)); ?>
                        </option>
                      <?php endfor; ?>
                    </select>
                  </div>
                  <button type="button" class="btn btn-sm btn-outline-secondary d-flex align-items-center justify-content-center" style="height: 31px; width: 31px; padding: 0;" onclick="window.location.href='analytics.php'" title="Reset Filters">
                    <i class="bi bi-arrow-counterclockwise"></i>
                  </button>
                  <small class="text-muted ms-2">
                    <i class="bi bi-info-circle"></i>
                    <?php if ($selected_month > 0): ?>
                      <?= date('M Y', mktime(0, 0, 0, $selected_month, 1, $selected_year)); ?>
                    <?php else: ?>
                      <?= $selected_year; ?>
                    <?php endif; ?>
                  </small>
                </form>
              </div>
              
              <div class="row mt-3">
                <!-- Total Revenue -->
                <div class="col-lg-3 col-md-6 mb-3">
                  <div class="card info-card revenue-card">
                    <div class="card-body">
                      <h5 class="card-title">Total Revenue 
                        <span>| 
                          <?php if ($selected_month > 0): ?>
                            <?= date('M Y', mktime(0, 0, 0, $selected_month, 1, $selected_year)); ?>
                          <?php else: ?>
                            <?= $selected_year; ?>
                          <?php endif; ?>
                        </span>
                      </h5>
                      <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                          <i class="bi bi-cash-stack"></i>
                        </div>
                        <div class="ps-3">
                          <h6><?= number_format($total_revenue, 2); ?> MMK</h6>
                          <span class="text-success small pt-1 fw-bold">Revenue</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Total Payments -->
                <div class="col-lg-3 col-md-6 mb-3">
                  <div class="card info-card sales-card">
                    <div class="card-body">
                      <h5 class="card-title">Total Payments 
                        <span>| 
                          <?php if ($selected_month > 0): ?>
                            <?= date('M Y', mktime(0, 0, 0, $selected_month, 1, $selected_year)); ?>
                          <?php else: ?>
                            <?= $selected_year; ?>
                          <?php endif; ?>
                        </span>
                      </h5>
                      <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                          <i class="bi bi-credit-card"></i>
                        </div>
                        <div class="ps-3">
                          <h6><?= number_format($total_payments); ?></h6>
                          <span class="text-success small pt-1 fw-bold">Payments</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Approved Payments -->
                <div class="col-lg-3 col-md-6 mb-3">
                  <div class="card info-card customers-card">
                    <div class="card-body">
                      <h5 class="card-title">Approved <span>| Payments</span></h5>
                      <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                          <i class="bi bi-check-circle"></i>
                        </div>
                        <div class="ps-3">
                          <h6><?= number_format($approved_count); ?></h6>
                          <span class="text-success small pt-1 fw-bold">Approved</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Pending Payments -->
                <div class="col-lg-3 col-md-6 mb-3">
                  <div class="card info-card revenue-card">
                    <div class="card-body">
                      <h5 class="card-title">Pending <span>| Payments</span></h5>
                      <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                          <i class="bi bi-clock-history"></i>
                        </div>
                        <div class="ps-3">
                          <h6><?= number_format($pending_count); ?></h6>
                          <span class="text-warning small pt-1 fw-bold">Pending</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Payment Trends Chart -->
              <div class="row mt-4">
                <div class="col-12">
                  <h6 class="mb-3">
                    Payment Trends 
                    <?php if ($selected_month > 0): ?>
                      (Daily - <?= date('F Y', mktime(0, 0, 0, $selected_month, 1, $selected_year)); ?>)
                    <?php else: ?>
                      (Monthly - <?= $selected_year; ?>)
                    <?php endif; ?>
                  </h6>
                  <canvas id="paymentChart" height="80"></canvas>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- User Activity Section -->
        <div class="col-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title"><i class="bi bi-people"></i> User Activity</h5>
              
              <div class="row mt-3">
                <!-- Total Users -->
                <div class="col-lg-4 col-md-6 mb-3">
                  <div class="card info-card sales-card">
                    <div class="card-body">
                      <h5 class="card-title">Total Users <span>| All Time</span></h5>
                      <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                          <i class="bi bi-people"></i>
                        </div>
                        <div class="ps-3">
                          <h6><?= number_format($total_users); ?></h6>
                          <span class="text-success small pt-1 fw-bold">Users</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- New Users This Month -->
                <div class="col-lg-4 col-md-6 mb-3">
                  <div class="card info-card revenue-card">
                    <div class="card-body">
                      <h5 class="card-title">New Users <span>| This Month</span></h5>
                      <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                          <i class="bi bi-person-plus"></i>
                        </div>
                        <div class="ps-3">
                          <h6><?= number_format($new_users_month); ?></h6>
                          <span class="text-success small pt-1 fw-bold">New Users</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Total Points Purchased -->
                <div class="col-lg-4 col-md-6 mb-3">
                  <div class="card info-card customers-card">
                    <div class="card-body">
                      <h5 class="card-title">Points Purchased <span>| All Time</span></h5>
                      <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                          <i class="bi bi-coin"></i>
                        </div>
                        <div class="ps-3">
                          <h6><?= number_format($total_points_purchased); ?></h6>
                          <span class="text-info small pt-1 fw-bold">Points</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- User Growth Chart -->
              <div class="row mt-4">
                <div class="col-12">
                  <h6 class="mb-3">User Growth (Last 6 Months)</h6>
                  <canvas id="userChart" height="80"></canvas>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Series Activity Section -->
        <div class="col-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title"><i class="bi bi-book"></i> Series Activity</h5>
              
              <div class="row mt-3">
                <!-- Total Series -->
                <div class="col-lg-4 col-md-6 mb-3">
                  <div class="card info-card sales-card">
                    <div class="card-body">
                      <h5 class="card-title">Total Series <span>| All Time</span></h5>
                      <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                          <i class="bi bi-book"></i>
                        </div>
                        <div class="ps-3">
                          <h6><?= number_format($total_series); ?></h6>
                          <span class="text-success small pt-1 fw-bold">Series</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Total Views -->
                <div class="col-lg-4 col-md-6 mb-3">
                  <div class="card info-card revenue-card">
                    <div class="card-body">
                      <h5 class="card-title">Total Views <span>| All Time</span></h5>
                      <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                          <i class="bi bi-eye"></i>
                        </div>
                        <div class="ps-3">
                          <h6><?= number_format($total_views); ?></h6>
                          <span class="text-success small pt-1 fw-bold">Views</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Average Rating -->
                <div class="col-lg-4 col-md-6 mb-3">
                  <div class="card info-card customers-card">
                    <div class="card-body">
                      <h5 class="card-title">Average Rating <span>| Overall</span></h5>
                      <div class="d-flex align-items-center">
                        <div class="card-icon rounded-circle d-flex align-items-center justify-content-center">
                          <i class="bi bi-star-fill"></i>
                        </div>
                        <div class="ps-3">
                          <h6><?= number_format($avg_rating, 1); ?>/5.0</h6>
                          <span class="text-warning small pt-1 fw-bold">Rating</span>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Series Growth Chart -->
              <div class="row mt-4">
                <div class="col-12">
                  <h6 class="mb-3">Series Growth (Last 6 Months)</h6>
                  <canvas id="seriesChart" height="80"></canvas>
                </div>
              </div>

              <!-- Top Series Table -->
              <div class="row mt-4">
                <div class="col-12">
                  <h6 class="mb-3">Top 10 Series by Views</h6>
                  <div class="table-responsive">
                    <table class="table table-hover">
                      <thead>
                        <tr>
                          <th scope="col" style="width: 60px;">#</th>
                          <th scope="col" style="width: 80px;">Image</th>
                          <th scope="col">Title</th>
                          <th scope="col" style="width: 120px;">Views</th>
                          <th scope="col" style="width: 100px;">Rating</th>
                        </tr>
                      </thead>
                      <tbody>
                        <?php if (!empty($top_series)): ?>
                          <?php foreach ($top_series as $index => $series): ?>
                            <tr>
                              <th scope="row"><?= $index + 1; ?></th>
                              <td>
                                <img src="<?= htmlspecialchars(getImagePath($series['image_url'] ?? '', 'series')); ?>" 
                                     alt="<?= htmlspecialchars($series['title'] ?? ''); ?>" 
                                     style="width: 60px; height: 80px; object-fit: cover; border-radius: 4px;"
                                     onerror="this.src='../img/placeholder.jpg'">
                              </td>
                              <td><strong><?= htmlspecialchars($series['title'] ?? 'Untitled'); ?></strong></td>
                              <td><span class="badge bg-info"><?= number_format($series['view'] ?? 0); ?></span></td>
                              <td>
                                <div class="d-flex align-items-center">
                                  <i class="bi bi-star-fill text-warning me-1"></i>
                                  <span><?= number_format($series['rating'] ?? 0, 1); ?></span>
                                </div>
                              </td>
                            </tr>
                          <?php endforeach; ?>
                        <?php else: ?>
                          <tr>
                            <td colspan="5" class="text-center py-4">
                              <p class="text-muted mb-0">No series found</p>
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

      </div>
    </section>

  </main><!-- End #main -->

  <?php include 'admin_footer.php'; ?>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/apexcharts/apexcharts.min.js"></script>
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/chart.js/chart.umd.js"></script>
  <script src="assets/vendor/echarts/echarts.min.js"></script>
  <script src="assets/vendor/quill/quill.min.js"></script>
  <script src="assets/vendor/simple-datatables/simple-datatables.js"></script>
  <script src="assets/vendor/tinymce/tinymce.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>

  <!-- Template Main JS File -->
  <script src="assets/js/main.js"></script>

  <script>
    // Payment Trends Chart
    const paymentCtx = document.getElementById('paymentChart');
    if (paymentCtx) {
      new Chart(paymentCtx, {
        type: 'line',
        data: {
          labels: <?= json_encode(array_column($payments_by_month, 'month')); ?>,
          datasets: [{
            label: 'Payments Count',
            data: <?= json_encode(array_column($payments_by_month, 'count')); ?>,
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1
          }, {
            label: 'Revenue (MMK)',
            data: <?= json_encode(array_column($payments_by_month, 'revenue')); ?>,
            borderColor: 'rgb(255, 99, 132)',
            backgroundColor: 'rgba(255, 99, 132, 0.2)',
            tension: 0.1,
            yAxisID: 'y1'
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: true,
          scales: {
            y: {
              beginAtZero: true,
              title: {
                display: true,
                text: 'Payments Count'
              }
            },
            y1: {
              type: 'linear',
              display: true,
              position: 'right',
              title: {
                display: true,
                text: 'Revenue (MMK)'
              },
              grid: {
                drawOnChartArea: false,
              },
            }
          }
        }
      });
    }

    // User Growth Chart
    const userCtx = document.getElementById('userChart');
    if (userCtx) {
      new Chart(userCtx, {
        type: 'bar',
        data: {
          labels: <?= json_encode(array_column($users_by_month, 'month')); ?>,
          datasets: [{
            label: 'New Users',
            data: <?= json_encode(array_column($users_by_month, 'count')); ?>,
            backgroundColor: 'rgba(54, 162, 235, 0.6)',
            borderColor: 'rgba(54, 162, 235, 1)',
            borderWidth: 1
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: true,
          scales: {
            y: {
              beginAtZero: true,
              title: {
                display: true,
                text: 'Number of Users'
              }
            }
          }
        }
      });
    }

    // Series Growth Chart
    const seriesCtx = document.getElementById('seriesChart');
    if (seriesCtx) {
      new Chart(seriesCtx, {
        type: 'line',
        data: {
          labels: <?= json_encode(array_column($series_by_month, 'month')); ?>,
          datasets: [{
            label: 'New Series',
            data: <?= json_encode(array_column($series_by_month, 'count')); ?>,
            borderColor: 'rgb(153, 102, 255)',
            backgroundColor: 'rgba(153, 102, 255, 0.2)',
            tension: 0.1,
            fill: true
          }]
        },
        options: {
          responsive: true,
          maintainAspectRatio: true,
          scales: {
            y: {
              beginAtZero: true,
              title: {
                display: true,
                text: 'Number of Series'
              }
            }
          }
        }
      });
    }
  </script>

</body>

</html>

