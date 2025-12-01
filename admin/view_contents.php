<?php

include('config.php');

session_start();

$chapter_id = isset($_GET['chapter_id']) ? filter_var($_GET['chapter_id'], FILTER_SANITIZE_NUMBER_INT) : null;
$series_id = isset($_GET['series_id']) ? filter_var($_GET['series_id'], FILTER_SANITIZE_NUMBER_INT) : null;

// Get chapter and series info
$chapter_title = '';
$series_title = '';
if($chapter_id && is_numeric($chapter_id)){
    $get_chapter = $conn->prepare("SELECT c.*, s.title as series_title FROM `chapters` c LEFT JOIN `series` s ON c.series_id = s.id WHERE c.id = ?");
    $get_chapter->execute([$chapter_id]);
    if($get_chapter->rowCount() > 0){
        $chapter_data = $get_chapter->fetch(PDO::FETCH_ASSOC);
        $chapter_title = $chapter_data['title'] ?? '';
        $series_title = $chapter_data['series_title'] ?? '';
        if(!$series_id){
            $series_id = $chapter_data['series_id'] ?? null;
        }
    }
}

require_once('includes/image_helper.php');

?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Admin | View Contents</title>
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
</head>

<body>

  <?php include 'admin_header.php'; ?>
  <?php include 'admin_sidebar.php'; ?>

  <main id="main" class="main">

    <div class="pagetitle">
      <h1>View Contents</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.php">Home</a></li>
          <li class="breadcrumb-item"><a href="series.php">Series</a></li>
          <li class="breadcrumb-item"><a href="chapters.php?series_id=<?= htmlspecialchars($series_id) ?>">Chapters</a></li>
          <li class="breadcrumb-item active">Contents</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->

    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <?php if(!empty($series_title) || !empty($chapter_title)): ?>
                <div class="content-header-info mb-4">
                  <div class="d-flex align-items-center gap-3 p-3 rounded" style="background: linear-gradient(135deg, var(--active-bg) 0%, var(--bg-tertiary) 100%); border: 1px solid var(--border-color);">
                    <?php if(!empty($series_title)): ?>
                      <div class="d-flex align-items-center flex-grow-1">
                        <div class="info-icon me-3">
                          <i class="bi bi-book" style="font-size: 24px; color: #1A73E8;"></i>
                        </div>
                        <div class="info-content">
                          <div class="info-label" style="font-size: 12px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.5px; font-weight: 500; margin-bottom: 2px;">Series</div>
                          <div class="info-value" style="font-size: 18px; font-weight: 500; color: var(--text-primary);"><?= htmlspecialchars($series_title); ?></div>
                        </div>
                      </div>
                    <?php endif; ?>
                    <?php if(!empty($chapter_title)): ?>
                      <div class="d-flex align-items-center flex-grow-1">
                        <div class="info-icon me-3">
                          <i class="bi bi-file-text" style="font-size: 24px; color: #34A853;"></i>
                        </div>
                        <div class="info-content">
                          <div class="info-label" style="font-size: 12px; color: var(--text-secondary); text-transform: uppercase; letter-spacing: 0.5px; font-weight: 500; margin-bottom: 2px;">Chapter</div>
                          <div class="info-value" style="font-size: 18px; font-weight: 500; color: var(--text-primary);"><?= htmlspecialchars($chapter_title); ?></div>
                        </div>
                      </div>
                    <?php endif; ?>
                  </div>
                </div>
              <?php endif; ?>
              
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="card-title mb-0">Contents</h5>
                <?php if($chapter_id && is_numeric($chapter_id)): ?>
                  <a href="add_content.php?chapter_id=<?= htmlspecialchars($chapter_id); ?>&series_id=<?= htmlspecialchars($series_id) ?>" class="btn btn-primary">
                    <i class="bi bi-plus-circle"></i> Add Content
                  </a>
                <?php endif; ?>
              </div>

              <?php if(!$chapter_id || !is_numeric($chapter_id)): ?>
                <div class="alert alert-danger">Invalid chapter ID. <a href="series.php">Go back to Series</a></div>
              <?php else: ?>

              <!-- Table with stripped rows -->
              <table class="table datatable">
                <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">Order</th>
                    <th scope="col">Content</th>
                    <th scope="col">Type</th>
                    <th scope="col">Actions</th>
                  </tr>
                </thead>
                <tbody>
                  <?php
                    $show_contents = $conn->prepare("SELECT * FROM `contents` WHERE chapter_id = ? ORDER BY order_index ASC, id ASC");
                    $show_contents->execute([$chapter_id]);
                    if($show_contents->rowCount() > 0){
                       while($fetch_content = $show_contents->fetch(PDO::FETCH_ASSOC)){  
                         $image_path = getImagePath($fetch_content['content_url'] ?? '', 'contents');
                  ?>
                      <tr>
                        <td><?= $fetch_content['id']; ?></td>
                        <td><?= $fetch_content['order_index']; ?></td>
                        <td>
                          <?php if(!empty($fetch_content['content_url'])): ?>
                            <?php if(($fetch_content['content_type'] ?? 'image') == 'pdf'): ?>
                              <div class="d-flex align-items-center">
                                <i class="bi bi-file-pdf text-danger" style="font-size: 48px;"></i>
                                <span class="ms-2 text-muted">PDF File</span>
                              </div>
                            <?php else: ?>
                              <img src="<?= htmlspecialchars($image_path); ?>" 
                                   alt="Content <?= $fetch_content['id']; ?>" 
                                   style="max-width: 150px; max-height: 150px; border-radius: 4px; object-fit: cover;"
                                   onerror="this.src='../img/placeholder.jpg'">
                            <?php endif; ?>
                          <?php else: ?>
                            <span class="text-muted">No content</span>
                          <?php endif; ?>
                        </td>
                        <td>
                          <?php 
                            $content_type = htmlspecialchars($fetch_content['content_type'] ?? 'image');
                            $badge_class = ($content_type == 'pdf') ? 'bg-danger' : 'bg-info';
                          ?>
                          <span class="badge <?= $badge_class; ?>">
                            <?php if($content_type == 'pdf'): ?>
                              <i class="bi bi-file-pdf"></i> PDF
                            <?php else: ?>
                              <i class="bi bi-image"></i> Image
                            <?php endif; ?>
                          </span>
                        </td>
                        <td>
                          <div class="btn-group" role="group">
                            <a href="manage_content.php?update=<?= $fetch_content['id']; ?>&series_id=<?= htmlspecialchars($series_id) ?>" 
                               class="btn btn-sm btn-warning" 
                               title="Edit Content">
                              <i class="bi bi-pencil"></i>
                            </a>
                            <a href="chapters.php?delete_content=<?= $fetch_content['id']; ?>&chapter_id=<?= htmlspecialchars($chapter_id); ?>&series_id=<?= htmlspecialchars($series_id) ?>" 
                               onclick="return confirm('Are you sure you want to delete this content?');" 
                               class="btn btn-sm btn-danger" 
                               title="Delete Content">
                              <i class="bi bi-trash"></i>
                            </a>
                          </div>
                        </td>
                      </tr>
                      <?php
                          }
                       }else{
                          echo '<tr><td colspan="5" class="text-center py-5"><div class="empty-state"><i class="bi bi-inbox empty-state-icon"></i><h5>No contents found</h5><p class="text-muted">Get started by adding your first content.</p><a href="add_content.php?chapter_id=' . htmlspecialchars($chapter_id) . '&series_id=' . htmlspecialchars($series_id) . '" class="btn btn-primary mt-3"><i class="bi bi-plus-circle"></i> Add Content</a></div></td></tr>';
                       }
                       ?>
                </tbody>
              </table>
              <!-- End Table with stripped rows -->

              <?php endif; ?>

            </div>
          </div>

        </div>
      </div>
    </section>

  </main>

  <!-- ======= Footer ======= -->
  <footer id="footer" class="footer">
    <div class="copyright">
      &copy; Copyright <strong><span>NiceAdmin</span></strong>. All Rights Reserved
    </div>
    <div class="credits">
      Designed by <a href="#">Aung Kyaw Thant</a>
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

