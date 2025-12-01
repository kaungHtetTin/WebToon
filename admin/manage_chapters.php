<?php

include('config.php');

session_start();


if(isset($_POST['update_chapters'])){

   $pid = $_POST['pid'];

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
   $download_url_folder = '../uploads/pdfs/';
   
   // Create directory if it doesn't exist
   if (!file_exists($download_url_folder)) {
       mkdir($download_url_folder, 0755, true);
   }
   
   // Generate unique filename to prevent overwrites (only if file is uploaded)
   $unique_file = $download_url;
   if(!empty($download_url)){
       $time = time();
       $file_extension = pathinfo($download_url, PATHINFO_EXTENSION);
       $file_name = pathinfo($download_url, PATHINFO_FILENAME);
       $unique_file = $file_name . "_" . $time . "." . $file_extension;
   }
   

   // Only update download_url if a new file is uploaded
   if(!empty($download_url)){
       $update_product = $conn->prepare("UPDATE `chapters` SET title = ?, description = ?, date = ?, is_active = ?, download_url = ? WHERE id = ?");
       $update_product->execute([$title, $description, $date, $is_active, $unique_file, $pid]);
   }else{
       $update_product = $conn->prepare("UPDATE `chapters` SET title = ?, description = ?, date = ?, is_active = ? WHERE id = ?");
       $update_product->execute([$title, $description, $date, $is_active, $pid]);
   }

   if($update_product){
      if(!empty($download_url)){
          if($download_url_size > 12000000){
             $message[] = 'download_url size is too large!';
          }else{
             move_uploaded_file($download_url_tmp_name, $download_url_folder.$unique_file);
             $message[] = 'updated successfully!';
             header('location:chapters.php');
          }
      }else{
          $message[] = 'updated successfully!';
          header('location:chapters.php');
      }
   }
   

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
                  <h5 class="card-title">Update Categories by Admin</h5>
                  <?php
                      $update_id = $_GET['update'];
                      $select_products = $conn->prepare("SELECT * FROM `chapters` WHERE id = ?");
                      $select_products->execute([$update_id]);
                      if($select_products->rowCount() > 0){
                         while($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)){ 
                   ?>
                  
                  <form action="" method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="pid" class="form-control" value="<?= $fetch_products['id']; ?>">


                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">title</label>
                      <div class="col-sm-10">
                        <input type="text" name="title" class="form-control" value="<?= $fetch_products['title']; ?>">
                        

                      </div>
                    </div>
                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">description</label>
                      <div class="col-sm-10">
                        <input type="text" name="description" class="form-control" value="<?= $fetch_products['description']; ?>">
                      </div>
                    </div>
                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">date</label>
                      <div class="col-sm-10">
                        <input type="date" name="date" class="form-control" value="<?= $fetch_products['date']; ?>">
                      </div>
                    </div>
                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">is_active </label>
                      <div class="col-sm-10">
                        <input type="text" name="is_active" class="form-control" value="<?= $fetch_products['is_active']; ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">download_url</label>
                      <div class="col-sm-10">
                        <?= $fetch_products['download_url']; ?>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">download_url</label>
                      <div class="col-sm-10">
                        <input type="file" name="download_url" class="form-control">
                      </div>
                    </div>
                    

                    <div class="row mb-3">
                      <label class="col-sm-2 col-form-label">Update Chapters</label>
                      <div class="col-sm-10">
                        <button type="submit" name="update_chapters" class="btn btn-primary">Update Chapters</button>
                      </div>
                    </div>

                  </form>
                  <?php
                       }
                    }else{
                       echo '<p class="empty">no products found!</p>';
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