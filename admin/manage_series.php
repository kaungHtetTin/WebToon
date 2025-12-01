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

   $original_work = $_POST['original_work'];
   $original_work = filter_var($original_work, FILTER_SANITIZE_STRING);

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

   // Handle checkbox - if checked, value is 1, otherwise 0
   $is_active = isset($_POST['is_active']) && $_POST['is_active'] == 'on' ? 1 : 0;

   $point = $_POST['point'];
   $point = filter_var($point, FILTER_SANITIZE_NUMBER_INT);
   if(empty($point) || !is_numeric($point)) {
       $point = 0;
   }

   $total_chapter = $_POST['total_chapter'];
   $total_chapter = filter_var($total_chapter, FILTER_SANITIZE_STRING);


   $uploaded_chapter = $_POST['uploaded_chapter'];
   $uploaded_chapter = filter_var($uploaded_chapter, FILTER_SANITIZE_STRING);

   // Handle image upload
   $image_url_folder = '../uploads/images/series/';
   $final_image_url = '';
   $upload_success = true;
   
   // Create directory if it doesn't exist
   if (!file_exists($image_url_folder)) {
       mkdir($image_url_folder, 0755, true);
   }
   
   // Check if a new image is uploaded
   if(isset($_FILES['image_url']['name']) && !empty($_FILES['image_url']['name']) && isset($_FILES['image_url']['error']) && $_FILES['image_url']['error'] == UPLOAD_ERR_OK){
       $image_url = $_FILES['image_url']['name'];
       $image_url_size = $_FILES['image_url']['size'];
       $image_url_tmp_name = $_FILES['image_url']['tmp_name'];
       
       if($image_url_size > 12000000){
           $message[] = 'image size is too large!';
           $upload_success = false;
       }else{
           // Generate unique filename to prevent overwrites
           $time = time();
           $file_extension = pathinfo($image_url, PATHINFO_EXTENSION);
           $file_name = pathinfo($image_url, PATHINFO_FILENAME);
           $unique_file = $file_name . "_" . $time . "." . $file_extension;
           
           // Upload the file first
           if(move_uploaded_file($image_url_tmp_name, $image_url_folder.$unique_file)){
               $final_image_url = "/uploads/images/series/".$unique_file;
               $upload_success = true;
           }else{
               $message[] = 'Failed to upload image!';
               $upload_success = false;
           }
       }
   }

   // Only proceed with database update if upload was successful or no file was uploaded
   if($upload_success){
       // Get current image_url from database if no new image was uploaded
       if(empty($final_image_url)){
           $select_current = $conn->prepare("SELECT image_url FROM `series` WHERE id = ?");
           $select_current->execute([$pid]);
           $current_data = $select_current->fetch(PDO::FETCH_ASSOC);
           $final_image_url = $current_data['image_url'] ?? '';
       }

       // Update database
       $update_product = $conn->prepare("UPDATE `series` SET title = ?, description = ?, short = ?, genre = ?, original_work = ?, upload_status = ?, date = ?, updated_date = ?, rating = ?, comment = ?, view = ?, save = ?, is_active = ?, point = ?, image_url = ?, total_chapter = ?, uploaded_chapter = ? WHERE id = ?");
       $update_product->execute([$title, $description, $short, $genre, $original_work, $upload_status, $date, $updated_date, $rating, $comment, $view, $save, $is_active, $point, $final_image_url, $total_chapter, $uploaded_chapter, $pid]);

       if($update_product){
           $message[] = 'updated successfully!';
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
                        <input type="text" name="title" class="form-control" value="<?= isset($fetch_products['title']) ? htmlspecialchars($fetch_products['title']) : ''; ?>">
                        

                      </div>
                    </div>

                    

                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">description</label>
                      <div class="col-sm-10">
                        <input type="textarea" name="description" class="form-control" value="<?= isset($fetch_products['description']) ? htmlspecialchars($fetch_products['description']) : ''; ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">short</label>
                      <div class="col-sm-10">
                        <input type="textarea" name="short" class="form-control" value="<?= isset($fetch_products['short']) ? htmlspecialchars($fetch_products['short']) : ''; ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">genre</label>
                      <div class="col-sm-10">
                        <input type="textarea" name="genre" class="form-control" value="<?= isset($fetch_products['genre']) ? htmlspecialchars($fetch_products['genre']) : ''; ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">original_work</label>
                      <div class="col-sm-10">
                        <input type="textarea" name="original_work" class="form-control" value="<?= isset($fetch_products['original_work']) ? htmlspecialchars($fetch_products['original_work']) : ''; ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">upload_status</label>
                      <div class="col-sm-10">
                        <input type="textarea" name="upload_status" class="form-control" value="<?= isset($fetch_products['upload_status']) ? htmlspecialchars($fetch_products['upload_status']) : ''; ?>">
                      </div>
                    </div>


                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">date</label>
                      <div class="col-sm-10">
                        <input type="date" name="date" class="form-control" value="<?= isset($fetch_products['date']) ? htmlspecialchars($fetch_products['date']) : ''; ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">date</label>
                      <div class="col-sm-10">
                        <input type="date" name="updated_date" class="form-control" value="<?= isset($fetch_products['updated_date']) ? htmlspecialchars($fetch_products['updated_date']) : ''; ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">rating</label>
                      <div class="col-sm-10">
                        <input type="text" name="rating" class="form-control" value="<?= isset($fetch_products['rating']) ? htmlspecialchars($fetch_products['rating']) : ''; ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">comment</label>
                      <div class="col-sm-10">
                        <input type="text" name="comment" class="form-control" value="<?= isset($fetch_products['comment']) ? htmlspecialchars($fetch_products['comment']) : ''; ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">view</label>
                      <div class="col-sm-10">
                        <input type="text" name="view" class="form-control" value="<?= isset($fetch_products['view']) ? htmlspecialchars($fetch_products['view']) : ''; ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">save</label>
                      <div class="col-sm-10">
                        <input type="text" name="save" class="form-control" value="<?= isset($fetch_products['save']) ? htmlspecialchars($fetch_products['save']) : ''; ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label class="col-sm-2 col-form-label">Status</label>
                      <div class="col-sm-10">
                        <div class="form-check form-switch">
                          <input class="form-check-input" type="checkbox" id="is_active" name="is_active" <?= isset($fetch_products['is_active']) && $fetch_products['is_active'] == 1 ? 'checked' : ''; ?>>
                          <label class="form-check-label" for="is_active">
                            Active (Series will be visible to users)
                          </label>
                        </div>
                        <small class="form-text text-muted">Uncheck to make this series inactive/hidden</small>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="inputNumber" class="col-sm-2 col-form-label">Point</label>
                      <div class="col-sm-10">
                        <input type="number" name="point" class="form-control" min="0" value="<?= isset($fetch_products['point']) ? htmlspecialchars($fetch_products['point']) : '0'; ?>">
                        <small class="form-text text-muted">Points required to purchase/access this series</small>
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="inputNumber" class="col-sm-2 col-form-label">image_url</label>
                      <div class="col-sm-10">
                        <input class="form-control" type="file" name="image_url">
                        <?php if(isset($fetch_products['image_url']) && !empty($fetch_products['image_url'])): ?>
                          <small>Current: <?= htmlspecialchars($fetch_products['image_url']); ?></small>
                        <?php endif; ?>
                      </div>
                    </div> 



                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">total_chapter</label>
                      <div class="col-sm-10">
                        <input type="text" name="total_chapter" class="form-control" value="<?= isset($fetch_products['total_chapter']) ? htmlspecialchars($fetch_products['total_chapter']) : ''; ?>">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="inputText" class="col-sm-2 col-form-label">uploaded_chapter</label>
                      <div class="col-sm-10">
                        <input type="text" name="uploaded_chapter" class="form-control" value="<?= isset($fetch_products['uploaded_chapter']) ? htmlspecialchars($fetch_products['uploaded_chapter']) : ''; ?>">
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