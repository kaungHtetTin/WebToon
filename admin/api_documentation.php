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

  <title>API Documentation - WebtoonMM Admin</title>
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
      <h1>API Documentation</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item active">API Documentation</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section dashboard">
      <div class="row">
        <div class="col-lg-12">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">API Overview</h5>
              <p>The WebtoonMM API provides endpoints for authentication, series management, chapters, payments, and user operations. Most endpoints require JWT authentication via the <code>Authorization</code> header.</p>
              
              <div class="alert alert-info">
                <strong>Base URL:</strong> <code><?php echo (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . dirname(dirname($_SERVER['PHP_SELF'])); ?>/api/</code>
              </div>

              <div class="alert alert-warning">
                <strong>Authentication:</strong> Most endpoints require a JWT token in the Authorization header. Format: <code>Authorization: Bearer {token}</code> or <code>Authorization: {token}</code>
              </div>

              <!-- Authentication APIs -->
              <div class="api-section mt-4">
                <h3 class="text-primary"><i class="bi bi-shield-lock"></i> Authentication APIs</h3>
                
                <div class="api-endpoint mb-4">
                  <div class="card">
                    <div class="card-header bg-primary text-white">
                      <strong>POST</strong> /api/auth/login.php
                    </div>
                    <div class="card-body">
                      <p><strong>Description:</strong> User login endpoint. Returns JWT token and user information upon successful authentication.</p>
                      <p><strong>Authentication:</strong> Not required</p>
                      <p><strong>Request Body (POST):</strong></p>
                      <pre class="bg-light p-3 rounded"><code>{
  "email": "user@example.com",
  "password": "password123"
}</code></pre>
                      <p><strong>Success Response (200):</strong></p>
                      <pre class="bg-light p-3 rounded"><code>{
  "status": "success",
  "user": {
    "user_id": 1,
    "first_name": "John",
    "last_name": "Doe",
    "email": "user@example.com",
    "phone": "1234567890",
    "image_url": "path/to/image.jpg",
    "point": 1000
  },
  "_token": "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9...",
  "mobile_app_version_code": 1
}</code></pre>
                      <p><strong>Error Response:</strong></p>
                      <pre class="bg-light p-3 rounded"><code>{
  "status": "fail",
  "error": "Invalid credentials"
}</code></pre>
                    </div>
                  </div>
                </div>

                <div class="api-endpoint mb-4">
                  <div class="card">
                    <div class="card-header bg-primary text-white">
                      <strong>POST</strong> /api/auth/signup.php
                    </div>
                    <div class="card-body">
                      <p><strong>Description:</strong> User registration endpoint. Creates a new user account and returns JWT token.</p>
                      <p><strong>Authentication:</strong> Not required</p>
                      <p><strong>Request Body (POST):</strong></p>
                      <pre class="bg-light p-3 rounded"><code>{
  "email": "user@example.com",
  "password": "password123",
  "first_name": "John",
  "last_name": "Doe",
  "phone": "1234567890"
}</code></pre>
                      <p><strong>Success Response:</strong> Same as login endpoint</p>
                      <p><strong>Error Response:</strong></p>
                      <pre class="bg-light p-3 rounded"><code>{
  "status": "fail",
  "error": "Email already exists"
}</code></pre>
                    </div>
                  </div>
                </div>

                <div class="api-endpoint mb-4">
                  <div class="card">
                    <div class="card-header bg-success text-white">
                      <strong>GET</strong> /api/auth/check-auth.php
                    </div>
                    <div class="card-body">
                      <p><strong>Description:</strong> Validates JWT token and returns current user information.</p>
                      <p><strong>Authentication:</strong> Required (JWT token in Authorization header)</p>
                      <p><strong>Headers:</strong></p>
                      <pre class="bg-light p-3 rounded"><code>Authorization: {jwt_token}</code></pre>
                      <p><strong>Success Response:</strong></p>
                      <pre class="bg-light p-3 rounded"><code>{
  "auth": "success",
  "user": {
    "user_id": 1,
    "first_name": "John",
    "last_name": "Doe",
    "email": "user@example.com",
    "phone": "1234567890",
    "image_url": "path/to/image.jpg",
    "point": 1000
  },
  "mobile_app_version_code": 1
}</code></pre>
                      <p><strong>Error Response:</strong></p>
                      <pre class="bg-light p-3 rounded"><code>{
  "auth": "fail",
  "mobile_app_version_code": 1
}</code></pre>
                    </div>
                  </div>
                </div>

                <div class="api-endpoint mb-4">
                  <div class="card">
                    <div class="card-header bg-success text-white">
                      <strong>GET</strong> /api/auth/check-email.php
                    </div>
                    <div class="card-body">
                      <p><strong>Description:</strong> Checks if an email address is already registered.</p>
                      <p><strong>Authentication:</strong> Not required</p>
                      <p><strong>Query Parameters:</strong></p>
                      <pre class="bg-light p-3 rounded"><code>?email=user@example.com</code></pre>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Series APIs -->
              <div class="api-section mt-5">
                <h3 class="text-success"><i class="bi bi-book"></i> Series APIs</h3>
                
                <div class="api-endpoint mb-4">
                  <div class="card">
                    <div class="card-header bg-success text-white">
                      <strong>GET</strong> /api/get.php
                    </div>
                    <div class="card-body">
                      <p><strong>Description:</strong> Get all series with pagination and categories.</p>
                      <p><strong>Authentication:</strong> Not required</p>
                      <p><strong>Query Parameters:</strong></p>
                      <pre class="bg-light p-3 rounded"><code>?page=1&limit=20</code></pre>
                    </div>
                  </div>
                </div>

                <div class="api-endpoint mb-4">
                  <div class="card">
                    <div class="card-header bg-success text-white">
                      <strong>GET</strong> /api/series/get.php
                    </div>
                    <div class="card-body">
                      <p><strong>Description:</strong> Get series by category or filter (popular, trending, recent).</p>
                      <p><strong>Authentication:</strong> Not required</p>
                      <p><strong>Query Parameters:</strong></p>
                      <pre class="bg-light p-3 rounded"><code>?q=popular&page=1&limit=20
?q=trending&page=1&limit=20
?q=recent&page=1&limit=20
?category_id=1&page=1&limit=20</code></pre>
                    </div>
                  </div>
                </div>

                <div class="api-endpoint mb-4">
                  <div class="card">
                    <div class="card-header bg-success text-white">
                      <strong>GET</strong> /api/series/detail.php
                    </div>
                    <div class="card-body">
                      <p><strong>Description:</strong> Get detailed information about a specific series.</p>
                      <p><strong>Authentication:</strong> Optional (for saved status and user rating)</p>
                      <p><strong>Query Parameters:</strong></p>
                      <pre class="bg-light p-3 rounded"><code>?id=1&user_id=1</code></pre>
                      <p><strong>Response includes:</strong> Series details, chapters, categories, saved status, user rating</p>
                    </div>
                  </div>
                </div>

                <div class="api-endpoint mb-4">
                  <div class="card">
                    <div class="card-header bg-success text-white">
                      <strong>GET</strong> /api/series/search.php
                    </div>
                    <div class="card-body">
                      <p><strong>Description:</strong> Search for series by keyword.</p>
                      <p><strong>Authentication:</strong> Not required</p>
                      <p><strong>Query Parameters:</strong></p>
                      <pre class="bg-light p-3 rounded"><code>?q=search_term</code></pre>
                      <p><strong>Success Response:</strong></p>
                      <pre class="bg-light p-3 rounded"><code>{
  "status": "success",
  "series": [...]
}</code></pre>
                    </div>
                  </div>
                </div>

                <div class="api-endpoint mb-4">
                  <div class="card">
                    <div class="card-header bg-primary text-white">
                      <strong>POST</strong> /api/series/purchase.php
                    </div>
                    <div class="card-body">
                      <p><strong>Description:</strong> Purchase/save a series for the authenticated user.</p>
                      <p><strong>Authentication:</strong> Required</p>
                      <p><strong>Request Body (POST):</strong></p>
                      <pre class="bg-light p-3 rounded"><code>{
  "series_id": 1
}</code></pre>
                      <p><strong>Success Response:</strong></p>
                      <pre class="bg-light p-3 rounded"><code>{
  "status": "success",
  "msg": "Authorization Fail"
}</code></pre>
                    </div>
                  </div>
                </div>

                <div class="api-endpoint mb-4">
                  <div class="card">
                    <div class="card-header bg-primary text-white">
                      <strong>POST</strong> /api/series/rate.php
                    </div>
                    <div class="card-body">
                      <p><strong>Description:</strong> Rate a series (1-5 stars).</p>
                      <p><strong>Authentication:</strong> Required</p>
                      <p><strong>Request Body (POST):</strong></p>
                      <pre class="bg-light p-3 rounded"><code>{
  "series_id": 1,
  "star": 5
}</code></pre>
                      <p><strong>Success Response:</strong></p>
                      <pre class="bg-light p-3 rounded"><code>{
  "status": "success"
}</code></pre>
                    </div>
                  </div>
                </div>

                <div class="api-endpoint mb-4">
                  <div class="card">
                    <div class="card-header bg-primary text-white">
                      <strong>POST</strong> /api/series/delete-rating.php
                    </div>
                    <div class="card-body">
                      <p><strong>Description:</strong> Delete user's rating for a series.</p>
                      <p><strong>Authentication:</strong> Required</p>
                    </div>
                  </div>
                </div>

                <div class="api-endpoint mb-4">
                  <div class="card">
                    <div class="card-header bg-success text-white">
                      <strong>GET</strong> /api/series/my_series.php
                    </div>
                    <div class="card-body">
                      <p><strong>Description:</strong> Get all series purchased/saved by the authenticated user.</p>
                      <p><strong>Authentication:</strong> Required</p>
                      <p><strong>Headers:</strong></p>
                      <pre class="bg-light p-3 rounded"><code>Authorization: {jwt_token}</code></pre>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Chapter APIs -->
              <div class="api-section mt-5">
                <h3 class="text-info"><i class="bi bi-file-text"></i> Chapter APIs</h3>
                
                <div class="api-endpoint mb-4">
                  <div class="card">
                    <div class="card-header bg-info text-white">
                      <strong>GET</strong> /api/chapters/get.php
                    </div>
                    <div class="card-body">
                      <p><strong>Description:</strong> Get all chapters for a specific series.</p>
                      <p><strong>Authentication:</strong> Not required</p>
                      <p><strong>Query Parameters:</strong></p>
                      <pre class="bg-light p-3 rounded"><code>?series_id=1</code></pre>
                    </div>
                  </div>
                </div>

                <div class="api-endpoint mb-4">
                  <div class="card">
                    <div class="card-header bg-info text-white">
                      <strong>GET</strong> /api/chapters/get-content.php
                    </div>
                    <div class="card-body">
                      <p><strong>Description:</strong> Get chapter content (images/pages). Requires series purchase or free access.</p>
                      <p><strong>Authentication:</strong> Required (for purchased series)</p>
                      <p><strong>Query Parameters:</strong></p>
                      <pre class="bg-light p-3 rounded"><code>?chapter_id=1&series_id=1</code></pre>
                      <p><strong>Headers:</strong></p>
                      <pre class="bg-light p-3 rounded"><code>Authorization: {jwt_token}</code></pre>
                      <p><strong>Success Response:</strong></p>
                      <pre class="bg-light p-3 rounded"><code>{
  "status": "success",
  "contents": [...]
}</code></pre>
                      <p><strong>Error Response:</strong></p>
                      <pre class="bg-light p-3 rounded"><code>{
  "status": "fail",
  "msg": "Access Denied!"
}</code></pre>
                      <p><strong>Access Rules:</strong> Chapter is accessible if series is free (point==0), chapter is inactive (is_active==0), series is saved/purchased, OR chapter is free (is_free==1)</p>
                    </div>
                  </div>
                </div>

                <div class="api-endpoint mb-4">
                  <div class="card">
                    <div class="card-header bg-info text-white">
                      <strong>GET</strong> /api/chapters/download.php
                    </div>
                    <div class="card-body">
                      <p><strong>Description:</strong> Download chapter content.</p>
                      <p><strong>Authentication:</strong> Required</p>
                      <p><strong>Query Parameters:</strong></p>
                      <pre class="bg-light p-3 rounded"><code>?chapter_id=1</code></pre>
                      <p><strong>Headers:</strong></p>
                      <pre class="bg-light p-3 rounded"><code>Authorization: {jwt_token}</code></pre>
                    </div>
                  </div>
                </div>
              </div>

              <!-- User APIs -->
              <div class="api-section mt-5">
                <h3 class="text-warning"><i class="bi bi-person"></i> User APIs</h3>
                
                <div class="api-endpoint mb-4">
                  <div class="card">
                    <div class="card-header bg-primary text-white">
                      <strong>POST</strong> /api/users/update-profile.php
                    </div>
                    <div class="card-body">
                      <p><strong>Description:</strong> Update user profile information.</p>
                      <p><strong>Authentication:</strong> Required</p>
                      <p><strong>Request Body (POST):</strong></p>
                      <pre class="bg-light p-3 rounded"><code>{
  "first_name": "John",
  "last_name": "Doe",
  "phone": "1234567890"
}</code></pre>
                      <p><strong>Note:</strong> Can include image file upload</p>
                    </div>
                  </div>
                </div>

                <div class="api-endpoint mb-4">
                  <div class="card">
                    <div class="card-header bg-primary text-white">
                      <strong>POST</strong> /api/users/change-password.php
                    </div>
                    <div class="card-body">
                      <p><strong>Description:</strong> Change user password.</p>
                      <p><strong>Authentication:</strong> Required</p>
                      <p><strong>Request Body (POST):</strong></p>
                      <pre class="bg-light p-3 rounded"><code>{
  "old_password": "oldpass123",
  "new_password": "newpass123"
}</code></pre>
                    </div>
                  </div>
                </div>

                <div class="api-endpoint mb-4">
                  <div class="card">
                    <div class="card-header bg-success text-white">
                      <strong>GET</strong> /api/users/get-otp.php
                    </div>
                    <div class="card-body">
                      <p><strong>Description:</strong> Request OTP code for password reset. Sends OTP via email.</p>
                      <p><strong>Authentication:</strong> Not required</p>
                      <p><strong>Query Parameters:</strong></p>
                      <pre class="bg-light p-3 rounded"><code>?email=user@example.com</code></pre>
                    </div>
                  </div>
                </div>

                <div class="api-endpoint mb-4">
                  <div class="card">
                    <div class="card-header bg-success text-white">
                      <strong>GET</strong> /api/users/confirm-otp.php
                    </div>
                    <div class="card-body">
                      <p><strong>Description:</strong> Verify OTP code for password reset.</p>
                      <p><strong>Authentication:</strong> Not required</p>
                      <p><strong>Query Parameters:</strong></p>
                      <pre class="bg-light p-3 rounded"><code>?email=user@example.com&code=123456</code></pre>
                      <p><strong>Success Response:</strong></p>
                      <pre class="bg-light p-3 rounded"><code>{
  "status": "success"
}</code></pre>
                    </div>
                  </div>
                </div>

                <div class="api-endpoint mb-4">
                  <div class="card">
                    <div class="card-header bg-primary text-white">
                      <strong>POST</strong> /api/users/password-reset.php
                    </div>
                    <div class="card-body">
                      <p><strong>Description:</strong> Reset password using verified OTP.</p>
                      <p><strong>Authentication:</strong> Not required</p>
                      <p><strong>Request Body (POST):</strong></p>
                      <pre class="bg-light p-3 rounded"><code>{
  "email": "user@example.com",
  "password": "newpassword123",
  "code": "123456"
}</code></pre>
                    </div>
                  </div>
                </div>

                <div class="api-endpoint mb-4">
                  <div class="card">
                    <div class="card-header bg-success text-white">
                      <strong>GET</strong> /api/users/search.php
                    </div>
                    <div class="card-body">
                      <p><strong>Description:</strong> Search for users.</p>
                      <p><strong>Authentication:</strong> Not required</p>
                    </div>
                  </div>
                </div>

                <div class="api-endpoint mb-4">
                  <div class="card">
                    <div class="card-header bg-danger text-white">
                      <strong>POST</strong> /api/users/delete-account.php
                    </div>
                    <div class="card-body">
                      <p><strong>Description:</strong> Delete user account.</p>
                      <p><strong>Authentication:</strong> Required</p>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Payment APIs -->
              <div class="api-section mt-5">
                <h3 class="text-danger"><i class="bi bi-wallet2"></i> Payment APIs</h3>
                
                <div class="api-endpoint mb-4">
                  <div class="card">
                    <div class="card-header bg-success text-white">
                      <strong>GET</strong> /api/point-info.php
                    </div>
                    <div class="card-body">
                      <p><strong>Description:</strong> Get point pricing information and payment methods. Includes payment history for authenticated users. Data is fetched from database.</p>
                      <p><strong>Authentication:</strong> Optional (guest users can use "guest_user" as token)</p>
                      <p><strong>Headers:</strong></p>
                      <pre class="bg-light p-3 rounded"><code>Authorization: {jwt_token}
 or
Authorization: guest_user</code></pre>
                      <p><strong>Success Response:</strong></p>
                      <pre class="bg-light p-3 rounded"><code>{
  "prices": [
    {
      "point": 1000,
      "price": 950,
      "remark": ""
    }
  ],
  "payment_methods": [
    {
      "payment_method": "KBZ Pay",
      "phone": "09675526045",
      "icon": "http://domain.com/uploads/icons/payment-kbz-pay.jpg",
      "account_name": "John Doe"
    }
  ],
  "plan_message": "...",
  "instruction_message": "...",
  "payment_histories": [...] // Only if authenticated
}</code></pre>
                      <p><strong>Response includes:</strong> Point prices (from database), payment methods (from database), payment histories (if authenticated)</p>
                    </div>
                  </div>
                </div>

                <div class="api-endpoint mb-4">
                  <div class="card">
                    <div class="card-header bg-success text-white">
                      <strong>GET</strong> /api/payments/get-methods-and-prices.php
                    </div>
                    <div class="card-body">
                      <p><strong>Description:</strong> Get only payment methods and point prices without payment history. Simpler endpoint for public data.</p>
                      <p><strong>Authentication:</strong> Not required</p>
                      <p><strong>Success Response:</strong></p>
                      <pre class="bg-light p-3 rounded"><code>{
  "status": "success",
  "prices": [
    {
      "point": 1000,
      "price": 950
    }
  ],
  "payment_methods": [
    {
      "payment_method": "KBZ Pay",
      "phone": "09675526045",
      "icon": "http://domain.com/uploads/icons/payment-kbz-pay.jpg",
      "account_name": "John Doe"
    }
  ]
}</code></pre>
                      <p><strong>Error Response:</strong></p>
                      <pre class="bg-light p-3 rounded"><code>{
  "status": "error",
  "message": "Database connection failed"
}</code></pre>
                    </div>
                  </div>
                </div>

                <div class="api-endpoint mb-4">
                  <div class="card">
                    <div class="card-header bg-primary text-white">
                      <strong>POST</strong> /api/payments/add.php
                    </div>
                    <div class="card-body">
                      <p><strong>Description:</strong> Submit a payment request with screenshot.</p>
                      <p><strong>Authentication:</strong> Required</p>
                      <p><strong>Request Body (POST - multipart/form-data):</strong></p>
                      <pre class="bg-light p-3 rounded"><code>{
  "point": 1000,
  "payment_method": "Kbz Pay",
  "screenshot": [file]
}</code></pre>
                      <p><strong>Success Response:</strong></p>
                      <pre class="bg-light p-3 rounded"><code>{
  "status": "success",
  "message": "Payment request submitted"
}</code></pre>
                      <p><strong>Error Response:</strong></p>
                      <pre class="bg-light p-3 rounded"><code>{
  "status": "Fail",
  "message": "Cannot Authorize"
}</code></pre>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Utility APIs -->
              <div class="api-section mt-5">
                <h3 class="text-secondary"><i class="bi bi-tools"></i> Utility APIs</h3>
                
                <div class="api-endpoint mb-4">
                  <div class="card">
                    <div class="card-header bg-secondary text-white">
                      <strong>GET</strong> /api/app-update.php
                    </div>
                    <div class="card-body">
                      <p><strong>Description:</strong> Check for mobile app updates and version information.</p>
                      <p><strong>Authentication:</strong> Not required</p>
                    </div>
                  </div>
                </div>

                <div class="api-endpoint mb-4">
                  <div class="card">
                    <div class="card-header bg-secondary text-white">
                      <strong>GET</strong> /api/test.php
                    </div>
                    <div class="card-body">
                      <p><strong>Description:</strong> Test endpoint for series details.</p>
                      <p><strong>Authentication:</strong> Not required</p>
                      <p><strong>Query Parameters:</strong></p>
                      <pre class="bg-light p-3 rounded"><code>?series_id=1</code></pre>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Response Codes -->
              <div class="api-section mt-5">
                <h3 class="text-dark"><i class="bi bi-info-circle"></i> Common Response Codes</h3>
                <div class="table-responsive">
                  <table class="table table-bordered">
                    <thead>
                      <tr>
                        <th>Status Code</th>
                        <th>Description</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <td><span class="badge bg-success">200</span></td>
                        <td>Success</td>
                      </tr>
                      <tr>
                        <td><span class="badge bg-danger">401</span></td>
                        <td>Unauthorized - Invalid or missing JWT token</td>
                      </tr>
                      <tr>
                        <td><span class="badge bg-danger">403</span></td>
                        <td>Forbidden - Access denied</td>
                      </tr>
                      <tr>
                        <td><span class="badge bg-danger">405</span></td>
                        <td>Method Not Allowed - Wrong HTTP method</td>
                      </tr>
                      <tr>
                        <td><span class="badge bg-danger">500</span></td>
                        <td>Internal Server Error</td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>

              <!-- Notes -->
              <div class="api-section mt-5">
                <h3 class="text-dark"><i class="bi bi-sticky"></i> Important Notes</h3>
                <div class="alert alert-warning">
                  <ul class="mb-0">
                    <li>All API responses are in JSON format</li>
                    <li>JWT tokens expire after a certain period - implement token refresh logic</li>
                    <li>File uploads use multipart/form-data encoding</li>
                    <li>Password reset flow: Get OTP → Confirm OTP → Reset Password</li>
                    <li>Chapter access requires series purchase unless series/chapter is free</li>
                    <li>Point system: Users purchase points to unlock paid series</li>
                    <li>Payment requests require admin approval before points are credited</li>
                  </ul>
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

  <style>
    .api-section {
      border-top: 2px solid #e9ecef;
      padding-top: 2rem;
    }
    .api-section h3 {
      margin-bottom: 1.5rem;
      padding-bottom: 0.5rem;
      border-bottom: 2px solid currentColor;
    }
    .api-endpoint .card-header {
      font-size: 1.1rem;
      font-weight: 600;
    }
    pre {
      margin-bottom: 0;
      font-size: 0.9rem;
    }
    code {
      color: #d63384;
    }
    .card {
      box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }
  </style>

</body>

</html>

