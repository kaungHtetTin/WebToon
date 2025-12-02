<?php
session_start();
$page_name="Enjoy";
include('classes/connect.php');
include('classes/series.php');
include('classes/category.php');
include('classes/util.php');
include('classes/chapter.php');

include('classes/user.php');
$User=new User();
$Series=new Series();

$isSaved=false;
$series_id=$_GET['series_id'];
$current_chapter_id = $_GET['c_chapter_id'];


if(isset($_SESSION['webtoon_userid'])){
    $user_id=$_SESSION['webtoon_userid'];
    $user=$User->details($user_id);
    $isSaved=$Series->isSaved($user_id,$series_id);

}

$Chapter=new Chapter();
$chapters=$Chapter->get($series_id);


$series=$Series->details(array('id'=>$series_id));
$currentChapter = getCurrentChapter($chapters,$current_chapter_id);

function getCurrentChapter($chapters,$current_chapter_id){
    foreach($chapters as $chapter){
        if($chapter['id']==$current_chapter_id){
            return $chapter;
        }
    }
}

$contents = $Chapter->getContent($currentChapter['id']);

// Check if chapter is free
$is_free_chapter = isset($currentChapter['is_free']) && $currentChapter['is_free'] == 1;

// Only check access restrictions if chapter is not free
if(!$is_free_chapter && $currentChapter['is_active']==1){  // not free chapter
    if(isset($user)){ 
        if($series['point']>0){
            if(!$isSaved) header("location:get_now.php?id=$series_id");
        }
    }else{
        header("location:login.php");
    }
}

// Helper function to get content URL
function getContentUrl($content_url, $content_type = 'image') {
    // If it's already a full URL, return as is
    if(strpos($content_url, 'http') === 0) {
        return $content_url;
    }
    
    // If empty, return placeholder
    if(empty($content_url)) {
        return 'img/placeholder.jpg';
    }
    
    // Use the image resource path helper for proper path resolution
    $Util = new Util();
    
    if($content_type == 'pdf') {
        // For PDFs, check both possible locations
        if(strpos($content_url, '/uploads/pdfs/contents/') === 0) {
            return ltrim($content_url, '/');
        } elseif(strpos($content_url, 'uploads/pdfs/contents/') === 0) {
            return $content_url;
        } elseif(strpos($content_url, '/uploads/') === 0) {
            return ltrim($content_url, '/');
        } else {
            // Try to construct path - check if file exists in PDF folder first
            $pdf_path = 'uploads/pdfs/contents/' . basename($content_url);
            if(file_exists($pdf_path)) {
                return $pdf_path;
            }
            // Fallback to image folder
            return 'uploads/images/contents/' . basename($content_url);
        }
    } else {
        // For images, use the util helper with empty base_path for client-side
        return $Util->getImageResourcePath($content_url, 'contents', '');
    }
}

?>


<!DOCTYPE html>
<html lang="zxx">

<head>
    <?php include('layouts/head.php'); ?>

    <style>
        .chapter{
            cursor: pointer;
        }
        .chapter:hover{
            background:#333;
        }
        
        /* Newsfeed Style Content Display */
        .content-feed {
            width: 100%;
        }
        
        .content-feed-item {
            margin-bottom: 20px;
            background: #1d1e39;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        
        .content-feed-item.image-content {
            background: transparent;
            box-shadow: none;
        }
        
        .content-feed-item.image-content img {
            width: 100%;
            display: block;
            max-width: 100%;
            height: auto;
            border-radius: 0;
            cursor: pointer;
            transition: opacity 0.3s ease;
        }
        
        .content-feed-item.image-content img:hover {
            opacity: 0.95;
        }
        
        .content-feed-item.pdf-content {
            padding: 40px 30px;
            text-align: center;
            background: linear-gradient(135deg, #1d1e39 0%, #252642 100%);
        }
        
        .content-feed-item.pdf-content i.fa-file-pdf-o {
            font-size: 64px;
            color: #dc3545;
            margin-bottom: 20px;
            display: block;
        }
        
        .content-feed-item.pdf-content h5 {
            color: #ffffff;
            font-weight: 600;
            margin-bottom: 15px;
        }
        
        .content-feed-item.pdf-content .download-btn {
            font-size: 14px;
            color: #ffffff;
            background: #dc3545;
            display: inline-block;
            font-weight: 600;
            padding: 12px 30px;
            border-radius: 5px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .content-feed-item.pdf-content .download-btn:hover {
            background: #c82333;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
            color: #ffffff;
            text-decoration: none;
        }
        
        @media (max-width: 768px) {
            .content-feed-item {
                margin-bottom: 15px;
            }
            
            .content-feed-item.pdf-content {
                padding: 30px 20px;
            }
        }
    </style>

</head>

<body>
    <!-- Page Preloder -->
    <div id="preloder">
        <div class="loader"></div>
    </div>

    <!-- Breadcrumb Begin -->
    <div class="breadcrumb-option">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb__links">
                        <a href="./index.php"><i class="fa fa-home"></i> Home</a>
                       
                        <span>
                            <a href="details.php?id=<?php echo $series_id?>">
                                <?= $series['title'] ?>
                            </a>
                        </span>
                          <span>Chapters</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Breadcrumb End -->

    <!-- Anime Section Begin -->
    <section class="anime-details spad">
        <div class="container">
            <div class="anime__details__content">

                <div class="row">
                    <div class="col-lg-8 col-md-8">
                        <?php if($contents && count($contents) > 0){ ?>
                            <div class="content-feed">
                                <?php 
                                $episode_index = 0;
                                foreach($contents as $content){ 
                                    $content_type = isset($content['content_type']) ? $content['content_type'] : 'image';
                                    $content_url = isset($content['content_url']) ? $content['content_url'] : (isset($content['url']) ? $content['url'] : '');
                                    $content_path = getContentUrl($content_url, $content_type);
                                    $order_index = isset($content['order_index']) ? intval($content['order_index']) : $episode_index;
                                    $episode_index++;
                                ?>
                                    <?php if($content_type == 'image'): ?>
                                        <div class="content-feed-item image-content">
                                            <img src="<?= htmlspecialchars($content_path) ?>" 
                                                 alt="Content Image" 
                                                 loading="lazy"
                                                 onclick="window.open('<?= htmlspecialchars($content_path) ?>', '_blank')">
                                        </div>
                                    <?php else: ?>
                                        <div class="content-feed-item pdf-content">
                                            <i class="fa fa-file-pdf-o"></i>
                                            <h5>PDF Document</h5>
                                            <a href="<?= htmlspecialchars($content_path) ?>" 
                                               download 
                                               class="download-btn">
                                                <i class="fa fa-download"></i> Download PDF
                                            </a>
                                        </div>
                                    <?php endif; ?>
                                <?php }?>
                            </div>
                        <?php } else{?>
                            <div class="anime__review__item__text" style="text-align: center; padding: 40px; background: #1d1e39; border-radius: 10px;">
                                <i class="fa fa-inbox" style="font-size: 48px; color: #ccc; margin-bottom: 15px;"></i>
                                <p style="color: #b7b7b7; font-size: 16px;">No Content Available</p>
                            </div>
                        <?php }?>
                    </div>
                    
                    <div class="col-lg-4 col-md-4">
                        <div class="anime__details__review">
                            <div class="section-title">
                                <h5>Chapters</h5>
                            </div>

                            <?php if($chapters) {
                               
                                foreach($chapters as $key=>$chapter){ 
                                    $url="contents.php?series_id=$series_id&c_chapter_id=".$chapter['id'];
                                ?>
                                <a href="<?=$url ?>">
                                    <div class="anime__review__item" style="width:100%">
    
                                        <div class="chapter anime__review__item__text" style="width:100%;<?php if($chapter['id']==$current_chapter_id) echo "background:#777;"; ?>">
                                            <div style="display:flex;justify-content: space-between;">
                                                <div style="width:45%">
                                                    <h6><?php echo $chapter['title'] ?></h6>
                                                </div>
                                            </div>
                                            
                                        </div>
                                    </div>
                                </a>

                            <?php }}else{?>
                                <div class="anime__review__item">
                                     
                                    <div class="anime__review__item__text">
                                        <p>No Chapter</p>
                                    </div>
                                </div>
                            <?php }?>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Anime Section End -->

<!-- Footer Section Begin -->
    <?php 
        include('layouts/footer.php');
    ?>
  <!-- Footer Section End -->

          <!-- Search model Begin -->
          <div class="search-model">
            <div class="h-100 d-flex align-items-center justify-content-center">
                <div class="search-close-switch"><i class="icon_close"></i></div>
                <form class="search-model-form">
                    <input type="text" id="search-input" placeholder="Search here.....">
                </form>
            </div>
        </div>
        <!-- Search model end -->

        <!-- Js Plugins -->
        <script src="js/jquery-3.3.1.min.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script src="js/player.js"></script>
        <script src="js/jquery.nice-select.min.js"></script>
        <script src="js/mixitup.min.js"></script>
        <script src="js/jquery.slicknav.js"></script>
        <script src="js/owl.carousel.min.js"></script>
        <script src="js/main.js"></script>

        <script>

        </script>


    </body>

    </html>