<?php

include('config.php');
/*include('connect.php');*/

session_start();

/*$DB=new Database();
$query ="SELECT * FROM categories ";
$categories = $DB->read($query);

$query = "UPDATE categories SET id = 10 ";
$DB->save($query);
*/


if(isset($_POST['add_categories'])){

   $title = $_POST['title'];
   $title = filter_var($title, FILTER_SANITIZE_STRING);
   
   $description = $_POST['description'];
   $description = filter_var($description, FILTER_SANITIZE_STRING);
  
   $date = $_POST['date'];
   $date = filter_var($date, FILTER_SANITIZE_STRING);
   // Handle checkbox - if checked, value is 1, otherwise 0
   $is_active = isset($_POST['is_active']) && $_POST['is_active'] == 'on' ? 1 : 0;

  
   $select_products = $conn->prepare("SELECT * FROM `categories` WHERE title = ?");
   $select_products->execute([$title]);

   if($select_products->rowCount() > 0){
      header('location:add_categories.php?error=' . urlencode('Category title already exists! Please choose a different title.'));
      exit();
   }else{

      $insert_products = $conn->prepare("INSERT INTO `categories`(title, description, date, is_active ) VALUES(?,?,?,?)");
      $result = $insert_products->execute([$title, $description, $date, $is_active]);
      
      if($result){
         header('location:categories.php?success=' . urlencode('Category added successfully!'));
      } else {
         header('location:add_categories.php?error=' . urlencode('Failed to add category. Please try again.'));
      }
      exit();

   }

};



?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Forms / Elements - NiceAdmin Bootstrap Template</title>
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
      <h1>Add New Category by Admin</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item">Add </li>
          <li class="breadcrumb-item active">Category</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Add New Category</h5>

              <!-- General Form Elements -->
              <form action="" method="POST" enctype="multipart/form-data">
                <div class="row mb-3">
                  <label for="inputText" class="col-sm-2 col-form-label">Title <span class="text-danger">*</span></label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="title" id="inputText" required minlength="3" maxlength="100" placeholder="Enter category title">
                    <small class="form-text text-muted">Category title (3-100 characters)</small>
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="inputDescription" class="col-sm-2 col-form-label">Description</label>
                  <div class="col-sm-10">
                    <textarea class="form-control" name="description" id="inputDescription" rows="3" maxlength="500" placeholder="Enter category description (optional)"></textarea>
                    <small class="form-text text-muted">Brief description of the category (max 500 characters)</small>
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="inputDate" class="col-sm-2 col-form-label">Date <span class="text-danger">*</span></label>
                  <div class="col-sm-10">
                    <input type="date" class="form-control" name="date" id="inputDate" required value="<?= date('Y-m-d'); ?>">
                    <small class="form-text text-muted">Category creation date</small>
                  </div>
                </div>

                <div class="row mb-3">
                  <label class="col-sm-2 col-form-label">Status</label>
                  <div class="col-sm-10">
                    <div class="form-check form-switch">
                      <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                      <label class="form-check-label" for="is_active">
                        Active (Category will be visible to users)
                      </label>
                    </div>
                    <small class="form-text text-muted">Uncheck to make this category inactive/hidden</small>
                  </div>
                </div>
                
                

                <!-- <div class="row mb-3">
                  <label for="inputNumber" class="col-sm-2 col-form-label">File Upload</label>
                  <div class="col-sm-10">
                    <input class="form-control" type="file" id="formFile" name="">
                  </div>
                </div> -->

                
                

                <!-- <div class="row mb-3">
                  <label for="inputTime" class="col-sm-2 col-form-label">Time</label>
                  <div class="col-sm-10">
                    <input type="time" class="form-control">
                  </div>
                </div> -->

                
              
                <!-- <div class="row mb-3">
                  <label class="col-sm-2 col-form-label">Select</label>
                  <div class="col-sm-10">
                    <select class="form-select" aria-label="Default select example">
                      <option selected>Open this select menu</option>
                      <option value="1">One</option>
                      <option value="2">Two</option>
                      <option value="3">Three</option>
                    </select>
                  </div>
                </div> -->

                <!-- <div class="row mb-3">
                  <label class="col-sm-2 col-form-label">Multi Select</label>
                  <div class="col-sm-10">
                    <select class="form-select" multiple aria-label="multiple select example">
                      <option selected>Open this select menu</option>
                      <option value="1">One</option>
                      <option value="2">Two</option>
                      <option value="3">Three</option>
                    </select>
                  </div>
                </div> -->

                <div class="row mb-3">
                  <div class="col-sm-10 offset-sm-2">
                    <button type="submit" name="add_categories" class="btn btn-primary">
                      <i class="bi bi-check-circle"></i> Add Category
                    </button>
                    <a href="categories.php" class="btn btn-secondary ms-2">Cancel</a>
                  </div>
                </div>

              </form><!-- End General Form Elements -->

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