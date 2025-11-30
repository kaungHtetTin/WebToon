<?php

include('config.php');

session_start();




if(isset($_POST['add_series'])){

   $category_id = $_POST['category_id'];
   $category_id = filter_var($category_id, FILTER_SANITIZE_STRING);

   $title = $_POST['title'];
   $title = filter_var($title, FILTER_SANITIZE_STRING);

   $description = $_POST['description'];
   $description = filter_var($description, FILTER_SANITIZE_STRING);
  
   $date = $_POST['date'];
   $date = filter_var($date, FILTER_SANITIZE_STRING);

   
   $is_active = $_POST['is_active'];
   $is_active = filter_var($is_active, FILTER_SANITIZE_STRING);


   $total_chapter = $_POST['total_chapter'];
   $total_chapter = filter_var($total_chapter, FILTER_SANITIZE_STRING);


   $uploaded_chapter = $_POST['uploaded_chapter'];
   $uploaded_chapter = filter_var($uploaded_chapter, FILTER_SANITIZE_STRING);

   $image_url = $_FILES['image_url']['name'];
   $image_url = filter_var($image_url, FILTER_SANITIZE_STRING);
   $image_url_size = $_FILES['image_url']['size'];
   $image_url_tmp_name = $_FILES['image_url']['tmp_name'];
   $image_url_folder = '../uploads/images/series/';
   
   // Create directory if it doesn't exist
   if (!file_exists($image_url_folder)) {
       mkdir($image_url_folder, 0755, true);
   }
   
   // Generate unique filename to prevent overwrites
   $time = time();
   $file_extension = pathinfo($image_url, PATHINFO_EXTENSION);
   $file_name = pathinfo($image_url, PATHINFO_FILENAME);
   $unique_file = $file_name . "_" . $time . "." . $file_extension;
   $final_image_url = "/uploads/images/series/".$unique_file;

  
   $select_products = $conn->prepare("SELECT * FROM `series` WHERE title = ?");
   $select_products->execute([$title]);

   if($select_products->rowCount() > 0){
      $message[] = 'book title already exist!';
   }else{

      $insert_products = $conn->prepare("INSERT INTO `series`(category_id, title, description, date,  is_active, total_chapter, uploaded_chapter, image_url ) VALUES(?,?,?,?,?,?,?,?)");
      $insert_products->execute([$category_id, $title, $description, $date, $is_active, $total_chapter, $uploaded_chapter, $final_image_url]);
      

      if($insert_products){
            if($image_url_size > 12000000){
               $message[] = 'image size is too large!';
            }else{
               move_uploaded_file($image_url_tmp_name, $image_url_folder.$unique_file);
               $message[] = 'registered successfully!';
               header('location:series.php');
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

  <title>Forms / Elements - NiceAdmin Bootstrap Template</title>
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

    <div class="pagetitle">
      <h1>Add New Category by Admin</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
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
              <h5 class="card-title">General Form Elements</h5>

              <!-- General Form Elements -->
              <form action="" method="POST" enctype="multipart/form-data">
                <div class="row mb-3">
                  <label class="col-sm-2 col-form-label">category_id</label>
                  <div class="col-sm-10">
                    <select name="category_id" class="form-select" aria-label="Default select example">
                      <option selected>Open this select menu</option>
                      <?php
                        $show_products = $conn->prepare("SELECT * FROM `categories`");
                        $show_products->execute();
                        if($show_products->rowCount() > 0){
                           while($fetch_products = $show_products->fetch(PDO::FETCH_ASSOC)){  
                     ?>
                      
                      <option value="<?= $fetch_products['id']; ?>" ><?= $fetch_products['title']; ?></option>
                     <?php
                          }
                       }else{
                          echo '<p class="empty">now books added yet!</p>';
                       }
                       ?>
                    </select>
                  </div>
                </div> 


                <div class="row mb-3">
                  <label for="inputText" class="col-sm-2 col-form-label">title</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="title">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="inputEmail" class="col-sm-2 col-form-label">description</label>
                  <div class="col-sm-10">
                    <input type="text" class="form-control" name="description">
                  </div>
                </div>
                <div class="row mb-3">
                  <label for="inputDate" class="col-sm-2 col-form-label">date</label>
                  <div class="col-sm-10">
                    <input type="date" class="form-control" name="date">
                  </div>
                </div>

                <!-- <div class="row mb-3">
                  <label for="inputDate" class="col-sm-2 col-form-label">rating</label>
                  <div class="col-sm-10">
                    <input type="number" class="form-control" name="rating">
                  </div>
                </div>

                <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">comment</label>
                      <div class="col-sm-10">
                        <input type="text" name="comment" class="form-control">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">view</label>
                      <div class="col-sm-10">
                        <input type="text" name="view" class="form-control">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">save</label>
                      <div class="col-sm-10">
                        <input type="text" name="save" class="form-control">
                      </div>
                    </div> -->

                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">is_active</label>
                      <div class="col-sm-10">
                        <input type="text" name="is_active" class="form-control">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="inputNumber" class="col-sm-2 col-form-label">image_url</label>
                      <div class="col-sm-10">
                        <input class="form-control" type="file" name="image_url">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">total_chapter</label>
                      <div class="col-sm-10">
                        <input type="text" name="total_chapter" class="form-control">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">uploaded_chapter</label>
                      <div class="col-sm-10">
                        <input type="text" name="uploaded_chapter" class="form-control">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label class="col-sm-2 col-form-label">Submit Button</label>
                      <div class="col-sm-10">
                        <button type="submit" name="add_series" class="btn btn-primary">Submit Form</button>
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
      &copy; Copyright <strong><span>NiceAdmin</span></strong>. All Rights Reserved
    </div>
    <div class="credits">
      <!-- All the links in the footer should remain intact. -->
      <!-- You can delete the links only if you purchased the pro version. -->
      <!-- Licensing information: https://bootstrapmade.com/license/ -->
      <!-- Purchase the pro version with working PHP/AJAX contact form: https://bootstrapmade.com/nice-admin-bootstrap-admin-html-template/ -->
      Designed by <a href="https://bootstrapmade.com/">BootstrapMade</a>
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

</body>

</html>