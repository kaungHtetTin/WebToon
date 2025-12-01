<?php

include('config.php');

session_start();

$series_id=$_GET['series_id'];

if(isset($_POST['add_chapter'])){

   $series_id = $_POST['series_id'];
   $series_id = filter_var($series_id, FILTER_SANITIZE_STRING);

   $title = $_POST['title'];
   $title = filter_var($title, FILTER_SANITIZE_STRING);

   $description = $_POST['description'];
   $description = filter_var($description, FILTER_SANITIZE_STRING);

   $date = $_POST['date'];
   $date = filter_var($date, FILTER_SANITIZE_STRING);


   // Handle checkbox - if checked, value is 1, otherwise 0
   $is_active = isset($_POST['is_active']) && $_POST['is_active'] == 'on' ? 1 : 0;
   $is_free = isset($_POST['is_free']) && $_POST['is_free'] == 'on' ? 1 : 0;

  
   $select_products = $conn->prepare("SELECT * FROM `chapters` WHERE title = ?");
   $select_products->execute([$title]);

   if($select_products->rowCount() > 0){
      $message[] = 'chapters name already exist!';
   }else{

      $insert_products = $conn->prepare("INSERT INTO `chapters`(series_id, title, description, date, is_active, is_free) VALUES(?,?,?,?,?,?)");
      $insert_products->execute([$series_id, $title, $description, $date, $is_active, $is_free]);


      if($insert_products){
            $message[] = 'registered successfully!';
            header("location:chapters.php?series_id=$series_id");
         }

   }

};



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

              <div class="card">
                <div class="card-body">
                  <h5 class="card-title">Add New Chapter by Admin</h5>

                  
                  <form action="" method="POST">
                    
                    <input type="hidden" name="series_id" value="<?= $series_id ?>">

                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">title</label>
                      <div class="col-sm-10">
                        <input type="text" name="title" class="form-control">
                      </div>
                    </div>
                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">description</label>
                      <div class="col-sm-10">
                        <input type="text" name="description" class="form-control">
                      </div>
                    </div>
                   
                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">date</label>
                      <div class="col-sm-10">
                        <input type="date" name="date" class="form-control">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label class="col-sm-2 col-form-label">Status</label>
                      <div class="col-sm-10">
                        <div class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                          <label class="form-check-label" for="is_active">
                            Active (Chapter will be visible to users)
                          </label>
                        </div>
                        <small class="form-text text-muted">Uncheck to make this chapter inactive/hidden</small>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label class="col-sm-2 col-form-label">Free Access</label>
                      <div class="col-sm-10">
                        <div class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" id="is_free" name="is_free">
                          <label class="form-check-label" for="is_free">
                            Free (Chapter is free to access)
                          </label>
                        </div>
                        <small class="form-text text-muted">Check to make this chapter free, uncheck if it requires payment</small>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label class="col-sm-2 col-form-label">Add Category</label>
                      <div class="col-sm-10">
                        <button type="submit" name="add_chapter" class="btn btn-primary">Add chapter</button>
                      </div>
                    </div>

                  </form>

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