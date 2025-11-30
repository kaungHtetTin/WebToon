<?php

include('config.php');

session_start();


if(isset($_POST['update_blogs'])){

   $pid = $_POST['pid'];

   $title = $_POST['title'];
   $title = filter_var($title, FILTER_SANITIZE_STRING);

   $description = $_POST['description'];
   $description = filter_var($description, FILTER_SANITIZE_STRING);

   $date = $_POST['date'];
   $date = filter_var($date, FILTER_SANITIZE_STRING);
   
   $image_url = $_FILES['image_url']['name'];
   $image_url = filter_var($image_url, FILTER_SANITIZE_STRING);
   $image_url_size = $_FILES['image_url']['size'];
   $image_url_tmp_name = $_FILES['image_url']['tmp_name'];
   $image_url_folder = 'img/trending/'.$image_url;

   $cover_url = $_FILES['cover_url']['name'];
   $cover_url = filter_var($cover_url, FILTER_SANITIZE_STRING);
   $cover_url_size = $_FILES['cover_url']['size'];
   $cover_url_tmp_name = $_FILES['cover_url']['tmp_name'];
   $cover_url_folder = 'img/blog/'.$cover_url;
   

   $update_product = $conn->prepare("UPDATE `blogs` SET title = ?, description = ?,  date = ?, image_url = ?, cover_url = ? WHERE id = ?");
   $update_product->execute([$title, $description, $date,  $image_url, $cover_url, $pid]);

   if($update_product){
      if($image_url_size && $cover_url_size > 12000000){
         $message[] = 'image_url size and cover_url size is too large!';
      }else{
         move_uploaded_file($image_url_tmp_name, $image_url_folder);
         move_uploaded_file($cover_url_tmp_name, $cover_url_folder);

         $message[] = 'updated successfully!';
         header('location:blogs.php');
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
                  <h5 class="card-title">Update Categories by Admin</h5>
                  <?php
                      $update_id = $_GET['update'];
                      $select_products = $conn->prepare("SELECT * FROM `blogs` WHERE id = ?");
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
                      <label for="inputText" class="col-sm-2 col-form-label">Old Image_Url</label>
                      <div class="col-sm-10">
                        <img src="img/trending/<?= $fetch_products['image_url']; ?>" alt="Profile" style="height: 100px;width: 100px;">
                      </div>
                    </div>
                  

                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">New image_url</label>
                      <div class="col-sm-10">
                        <input type="file" name="image_url" class="form-control">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">Old Cover_Url</label>
                      <div class="col-sm-10">
                        <img src="img/blog/<?= $fetch_products['cover_url']; ?>" alt="Profile" style="height: 100px;width: 100px;">
                      </div>
                    </div>
                  

                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">New cover_url</label>
                      <div class="col-sm-10">
                        <input type="file" name="cover_url" class="form-control">
                      </div>
                    </div>
                    

                    <div class="row mb-3">
                      <label class="col-sm-2 col-form-label">Update Blogs</label>
                      <div class="col-sm-10">
                        <button type="submit" name="update_blogs" class="btn btn-primary">Update Blogs</button>
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