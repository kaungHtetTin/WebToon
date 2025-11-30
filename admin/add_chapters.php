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


   $is_active = $_POST['is_active'];
   $is_active = filter_var($is_active, FILTER_SANITIZE_STRING);

   $download_url = $_FILES['download_url']['name'];
   $download_url = filter_var($download_url, FILTER_SANITIZE_STRING);
   $download_url_size = $_FILES['download_url']['size'];
   $download_url_tmp_name = $_FILES['download_url']['tmp_name'];
   $download_url_folder = '../uploads/pdfs/'.$download_url;

  
   $select_products = $conn->prepare("SELECT * FROM `chapters` WHERE title = ?");
   $select_products->execute([$title]);

   if($select_products->rowCount() > 0){
      $message[] = 'chapters name already exist!';
   }else{

      $insert_products = $conn->prepare("INSERT INTO `chapters`(series_id, title, description, date, is_active, download_url) VALUES(?,?,?,?,?,?)");
      $insert_products->execute([$series_id, $title, $description, $date, $is_active, $download_url]);


      if($insert_products){
            if($download_url_size > 12000000){
               $message[] = 'download_url size is too large!';
            }else{
               move_uploaded_file($download_url_tmp_name, $download_url_folder);
               $message[] = 'registered successfully!';
               header("location:chapters.php?series_id=$series_id");
            }
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
  <link href="assets/img/favicon.png" rel="icon">
  <link href="assets/img/apple-touch-icon.png" rel="apple-touch-icon">

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

                  
                  <form action="" method="POST" enctype="multipart/form-data">
                    
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
                      <label for="inputText" class="col-sm-2 col-form-label">download_url</label>
                      <div class="col-sm-10">
                        <input type="file" name="download_url" class="form-control">
                      </div>
                    </div>
                   
                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">date</label>
                      <div class="col-sm-10">
                        <input type="date" name="date" class="form-control">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">is_active</label>
                      <div class="col-sm-10">
                        <input type="number" name="is_active" class="form-control">
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