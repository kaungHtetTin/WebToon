<?php
// Rendered by requirePermission() when the current admin lacks the required permission.
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Access Denied</title>
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
  <main>
    <div class="container">
      <section class="section error-403 d-flex flex-column align-items-center justify-content-center" style="min-height: 90vh;">
        <h1 class="display-1 fw-bold text-danger">403</h1>
        <h2 class="mb-2">Access Denied</h2>
        <p class="text-muted mb-4 text-center">
          You do not have permission to access this page.<br>
          Please contact a Super Admin if you believe this is an error.
        </p>
        <div class="d-flex gap-2">
          <a class="btn btn-primary" href="index.php"><i class="bi bi-house-door"></i> Go to Dashboard</a>
          <a class="btn btn-outline-secondary" href="logout.php"><i class="bi bi-box-arrow-right"></i> Sign Out</a>
        </div>
      </section>
    </div>
  </main>
</body>
</html>
