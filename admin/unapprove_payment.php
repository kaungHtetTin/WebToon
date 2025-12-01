<?php

include('config.php');

session_start();



if(isset($_POST['approve'])){

   $payment_id = $_POST['payment_id'] ?? null;
   $user_id = $_POST['user_id'] ?? null;
   $amount_paid = $_POST['amount_paid'] ?? null;
   $points_to_add = $_POST['points_to_add'] ?? 0;
   $points_to_add = (int)filter_var($points_to_add, FILTER_SANITIZE_NUMBER_INT);
   $amount_paid = $amount_paid !== null
       ? (float)filter_var($amount_paid, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION)
       : 0;

   if(!$payment_id || !$user_id){
       $message[] = 'Invalid payment or user ID!';
   } elseif($amount_paid <= 0){
       $message[] = 'Please enter a valid paid amount!';
   } elseif($points_to_add <= 0){
       $message[] = 'Please enter a valid number of points to add!';
   } else {
       // Get current user points
       $select_user = $conn->prepare("SELECT point FROM `users` WHERE id = ?");
       $select_user->execute([$user_id]);
       $user_data = $select_user->fetch(PDO::FETCH_ASSOC);
       
       if(!$user_data){
           $message[] = 'User not found!';
       } else {
           $current_points = (int)($user_data['point'] ?? 0);
           
           // Calculate new point balance (add points to current balance)
           $new_points = $current_points + $points_to_add;
           
           // Start transaction-like operations
           try {
               // Update user's point balance
               $update_user = $conn->prepare("UPDATE `users` SET point = ? WHERE id = ?");
               $update_user_success = $update_user->execute([$new_points, $user_id]);
               
               // Update payment_histories to mark as verified/approved and save amount
               $update_payment = $conn->prepare("UPDATE `payment_histories` SET amount = ?, point = ?, points_added = ?, verified = 1, confirm = 1, status = 'approved', date = CURDATE() WHERE id = ?");
               $update_payment_success = $update_payment->execute([$amount_paid, $points_to_add, $points_to_add, $payment_id]);

               if($update_user_success && $update_payment_success){
                   $message[] = 'Payment approved successfully! ' . $points_to_add . ' points added to user account.';
               } else {
                   $message[] = 'Error updating payment. Please try again.';
               }
           } catch(Exception $e) {
               $message[] = 'Error: ' . $e->getMessage();
           }
       }
   }
}

if(isset($_POST['unapprove'])){

   $payment_id = $_POST['payment_id'] ?? null;
   $user_id = $_POST['user_id'] ?? null;

   if(!$payment_id || !$user_id){
       $message[] = 'Invalid payment or user ID for revert!';
   } else {
       try {
           // Get payment record to know how many points were added
           $select_payment = $conn->prepare("SELECT point, points_added FROM `payment_histories` WHERE id = ?");
           $select_payment->execute([$payment_id]);
           $payment_row = $select_payment->fetch(PDO::FETCH_ASSOC);

           if(!$payment_row){
               $message[] = 'Payment record not found for revert!';
           } else {
               $points_added = (int)($payment_row['points_added'] ?? $payment_row['point'] ?? 0);

               // Get current user points
               $select_user = $conn->prepare("SELECT point FROM `users` WHERE id = ?");
               $select_user->execute([$user_id]);
               $user_data = $select_user->fetch(PDO::FETCH_ASSOC);

               if(!$user_data){
                   $message[] = 'User not found for revert!';
               } else {
                   $current_points = (int)($user_data['point'] ?? 0);
                   $new_points = $current_points - $points_added;
                   if ($new_points < 0) {
                       $new_points = 0;
                   }

                   // Update user points (subtract previously added points)
                   $update_user = $conn->prepare("UPDATE `users` SET point = ? WHERE id = ?");
                   $update_user_success = $update_user->execute([$new_points, $user_id]);

                   // Mark payment as unapproved / pending again
                   $update_payment = $conn->prepare("UPDATE `payment_histories` SET verified = 0, confirm = 0, status = 'pending' WHERE id = ?");
                   $update_payment_success = $update_payment->execute([$payment_id]);

                   if($update_user_success && $update_payment_success){
                       $message[] = 'Payment reverted successfully. ' . $points_added . ' points removed from user account.';
                   } else {
                       $message[] = 'Error reverting payment. Please try again.';
                   }
               }
           }
       } catch(Exception $e) {
           $message[] = 'Error: ' . $e->getMessage();
       }
   }
}

if(isset($_GET['delete'])){

   $delete_id = $_GET['delete'];
   
   $delete_products = $conn->prepare("DELETE FROM `payment_histories` WHERE id = ?");
   $delete_products->execute([$delete_id]);
   
   header('location:index.php');


}
   

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Dashboard - NiceAdmin Bootstrap Template</title>
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

  <!-- Dark / Light Mode Compatible Form Styling (uses theme CSS variables) -->
  <style>
    .payment-approve-card {
      background: var(--card-bg);
      color: var(--text-primary);
      border-radius: 16px;
      box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
      border: 1px solid var(--card-border);
      transition: background-color 0.3s ease, color 0.3s ease, border-color 0.3s ease;
    }

    .payment-approve-card .card-title {
      font-weight: 600;
      display: flex;
      align-items: center;
      gap: 8px;
      color: var(--text-primary);
    }

    .payment-approve-card .card-title i {
      color: #0d6efd;
    }

    .payment-approve-card label {
      color: var(--text-primary);
    }

    .payment-approve-card .form-control {
      background-color: var(--input-bg);
      border-color: var(--input-border);
      color: var(--text-primary);
    }

    .payment-approve-card .form-control:focus {
      border-color: #1A73E8;
      box-shadow: 0 0 0 2px rgba(26, 115, 232, 0.2);
      background-color: var(--card-bg);
      color: var(--text-primary);
    }

    .payment-approve-card .form-control[readonly] {
      background-color: var(--bg-tertiary);
      color: var(--text-secondary);
    }

    .payment-approve-card .img-thumbnail {
      background-color: var(--bg-secondary);
      border-color: var(--border-color);
    }
  </style>

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


    <section class="section dashboard">
      <div class="row">

        
        <div class="col-lg-12">
          <div class="row">

            

            <div class="col-lg-12">
              <div class="card payment-approve-card">
                <div class="card-body">
                  <h5 class="card-title">
                    <i class="bi bi-credit-card-2-front"></i>
                    Approve Payment Request
                  </h5>
                  
                  <?php if(isset($message) && !empty($message)): ?>
                    <?php foreach($message as $msg): ?>
                      <div class="alert alert-warning alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($msg); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                      </div>
                    <?php endforeach; ?>
                  <?php endif; ?>
                  
                  <?php
                      $payment_id = $_GET['update'] ?? null;
                      if(!$payment_id){
                          echo '<p class="text-danger">Invalid payment ID</p>';
                      } else {
                          $select_payment = $conn->prepare("SELECT ph.*, u.first_name, u.last_name, u.email, u.point as user_current_points FROM `payment_histories` ph LEFT JOIN `users` u ON ph.user_id = u.id WHERE ph.id = ?");
                          $select_payment->execute([$payment_id]);
                          if($select_payment->rowCount() > 0){
                             $payment = $select_payment->fetch(PDO::FETCH_ASSOC); 
                   ?>
                   
                  <form action="" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="payment_id" value="<?= $payment['id']; ?>">
                    <input type="hidden" name="user_id" value="<?= $payment['user_id']; ?>">
                    
                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">User Name</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" value="<?= htmlspecialchars($payment['first_name'] . ' ' . $payment['last_name']); ?>" readonly>
                      </div>
                    </div>
                    
                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">User Email</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" value="<?= htmlspecialchars($payment['email']); ?>" readonly>
                      </div>
                    </div>
                    
                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">Current User Points</label>
                      <div class="col-sm-10">
                        <input type="text" class="form-control" value="<?= $payment['user_current_points']; ?>" readonly>
                      </div>
                    </div>
                    
                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">Payment Screenshot</label>
                      <div class="col-sm-10">
                        <?php if($payment['screenshot_url']): 
                            $screenshot_path = getImagePath($payment['screenshot_url'], 'screenshots');
                        ?>
                            <img src="<?= htmlspecialchars($screenshot_path); ?>" style="max-width: 400px; max-height: 400px;" class="img-thumbnail" onerror="this.src='../img/placeholder.jpg'">
                        <?php else: ?>
                            <p class="text-muted">No screenshot available</p>
                        <?php endif; ?>
                      </div>
                    </div>
                    
                    <div class="row mb-3">
                      <label for="amount_paid" class="col-sm-2 col-form-label">Amount Paid (Kyats)</label>
                      <div class="col-sm-10">
                        <input
                          type="number"
                          step="0.01"
                          min="0"
                          id="amount_paid"
                          name="amount_paid"
                          class="form-control"
                          value="<?= htmlspecialchars($payment['amount'] ?? '0'); ?>"
                          required
                        >
                        <small class="form-text text-muted">Enter or adjust the paid amount in Kyats for this payment.</small>
                      </div>
                    </div>
                    
                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">Points to Add</label>
                      <div class="col-sm-10">
                        <input type="number" name="points_to_add" class="form-control" value="<?= $payment['point'] ? $payment['point'] : ($payment['points_added'] ? $payment['points_added'] : '0'); ?>" min="0" required>
                        <small class="form-text text-muted">Enter the number of points to add to user's account (Current: <?= $payment['user_current_points']; ?> → New: <span id="new_points"><?= $payment['user_current_points'] + ($payment['point'] ? $payment['point'] : ($payment['points_added'] ? $payment['points_added'] : 0)); ?></span>)</small>
                      </div>
                    </div>
                    
                    <script>
                    document.querySelector('input[name="points_to_add"]').addEventListener('input', function() {
                        var currentPoints = <?= $payment['user_current_points']; ?>;
                        var pointsToAdd = parseInt(this.value) || 0;
                        document.getElementById('new_points').textContent = currentPoints + pointsToAdd;
                    });
                    </script>
                    
                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">Status</label>
                      <div class="col-sm-10">
                        <span class="badge bg-<?= $payment['verified'] ? 'success' : 'warning'; ?>">
                          <?= $payment['verified'] ? 'Approved' : 'Pending Approval'; ?>
                        </span>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label class="col-sm-2 col-form-label">Actions</label>
                      <div class="col-sm-10 d-flex gap-2">
                        <?php if(!$payment['verified']): ?>
                          <button type="submit" name="approve" class="btn btn-success">Approve Payment</button>
                        <?php else: ?>
                          <button type="submit" name="unapprove" class="btn btn-outline-warning">Unapprove / Revert</button>
                        <?php endif; ?>
                        <a href="pending_payment.php" class="btn btn-secondary">Back to Pending</a>
                      </div>
                    </div>

                  </form>
                  <?php
                       } else {
                          echo '<p class="empty">Payment record not found!</p>';
                       }
                    }
                 ?>
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