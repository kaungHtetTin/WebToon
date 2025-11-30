<?php

include('config.php');

session_start();


if(isset($_POST['update_series'])){

   $pid = $_POST['pid'];

   /*$category_id = $_POST['category_id'];
   $category_id = filter_var($category_id, FILTER_SANITIZE_STRING);*/

   $title = $_POST['title'];
   $title = filter_var($title, FILTER_SANITIZE_STRING);

   $description = $_POST['description'];
   $description = filter_var($description, FILTER_SANITIZE_STRING);

   $short = $_POST['short'];
   $short = filter_var($short, FILTER_SANITIZE_STRING);

   $genre = $_POST['genre'];
   $genre = filter_var($genre, FILTER_SANITIZE_STRING);

   $orginal_work = $_POST['orginal_work'];
   $orginal_work = filter_var($orginal_work, FILTER_SANITIZE_STRING);

   $upload_status = $_POST['upload_status'];
   $upload_status = filter_var($upload_status, FILTER_SANITIZE_STRING);

   $date = $_POST['date'];
   $date = filter_var($date, FILTER_SANITIZE_STRING);

   $updated_date = $_POST['updated_date'];
   $updated_date = filter_var($updated_date, FILTER_SANITIZE_STRING);

   $rating = $_POST['rating'];
   $rating = filter_var($rating, FILTER_SANITIZE_STRING);
   
   $comment = $_POST['comment'];
   $comment = filter_var($comment, FILTER_SANITIZE_STRING);

   $view = $_POST['view'];
   $view = filter_var($view, FILTER_SANITIZE_STRING);

   $save = $_POST['save'];
   $save = filter_var($save, FILTER_SANITIZE_STRING);

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
   $image_url_folder = '../img/series/'.$image_url;

  

   $update_product = $conn->prepare("UPDATE `series` SET  title = ?, description = ?,short = ?,genre = ?,orginal_work = ?,upload_status = ?, date = ?,  updated_date = ?, rating = ?, comment = ?, view = ?, save = ?,   is_active = ?,  image_url = ?, total_chapter = ?, uploaded_chapter = ? , image_url = ?  WHERE id = ?");

   $update_product->execute([ $title, $description, $short, $genre, $orginal_work, $upload_status, $date, $updated_date, $rating, $comment, $view, $save, $is_active, $image_url, $total_chapter, $uploaded_chapter, $image_url, $pid]);

   if($update_product){
            if($image_url_size > 12000000){
               $message[] = 'image size is too large!';
            }else{
               move_uploaded_file($image_url_tmp_name, $image_url_folder);
               $message[] = 'registered successfully!';
               header('location:series.php');
            }
         }
   

}

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Admin | Series Detail</title>
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
                  <h5 class="card-title">Update Series by Admin
                    <span><a href="">View chapters</a></span>
                  </h5>
                  
                  <?php
                          $update_id = $_GET['update'];
                          $select_products = $conn->prepare("SELECT * FROM `series` WHERE id = ?");
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
                        <input type="textarea" name="description" class="form-control" value="<?= $fetch_products['description']; ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">short</label>
                      <div class="col-sm-10">
                        <input type="textarea" name="short" class="form-control" value="<?= $fetch_products['short']; ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">genre</label>
                      <div class="col-sm-10">
                        <input type="textarea" name="genre" class="form-control" value="<?= $fetch_products['genre']; ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">orginal_work</label>
                      <div class="col-sm-10">
                        <input type="textarea" name="orginal_work" class="form-control" value="<?= $fetch_products['orginal_work']; ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">upload_status</label>
                      <div class="col-sm-10">
                        <input type="textarea" name="upload_status" class="form-control" value="<?= $fetch_products['upload_status']; ?>">
                      </div>
                    </div>


                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">date</label>
                      <div class="col-sm-10">
                        <input type="date" name="date" class="form-control" value="<?= $fetch_products['date']; ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">date</label>
                      <div class="col-sm-10">
                        <input type="date" name="updated_date" class="form-control" value="<?= $fetch_products['updated_date']; ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">rating</label>
                      <div class="col-sm-10">
                        <input type="text" name="rating" class="form-control" value="<?= $fetch_products['rating']; ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">comment</label>
                      <div class="col-sm-10">
                        <input type="text" name="comment" class="form-control" value="<?= $fetch_products['comment']; ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">view</label>
                      <div class="col-sm-10">
                        <input type="text" name="view" class="form-control" value="<?= $fetch_products['view']; ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">save</label>
                      <div class="col-sm-10">
                        <input type="text" name="save" class="form-control" value="<?= $fetch_products['save']; ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">is_active </label>
                      <div class="col-sm-10">
                        <input type="text" name="is_active" class="form-control" value="<?= $fetch_products['is_active']; ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="inputNumber" class="col-sm-2 col-form-label">image_url</label>
                      <div class="col-sm-10">
                        <input class="form-control" type="file" name="image_url" value="<?= $fetch_products['image_url']; ?>">
                      </div>
                    </div> 



                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">total_chapter</label>
                      <div class="col-sm-10">
                        <input type="text" name="total_chapter" class="form-control" value="<?= $fetch_products['total_chapter']; ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">uploaded_chapter</label>
                      <div class="col-sm-10">
                        <input type="text" name="uploaded_chapter" class="form-control" value="<?= $fetch_products['uploaded_chapter']; ?>">
                      </div>
                    </div>

                    
                    

                    <div class="row mb-3">
                      <label class="col-sm-2 col-form-label">Update Series</label>
                      <div class="col-sm-10">
                        <button type="submit" name="update_series" class="btn btn-primary">Update Series</button>
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