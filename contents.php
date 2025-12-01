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

if($currentChapter['is_active']==1){  // not free chapter
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
        
        /* Content Display Styles */
        .content-item {
            margin-bottom: 5px;
        }
        
        .content-item.image-content img {
            width: 100%;
            border-radius: 3px;
            display: block;
            max-width: 100%;
            height: auto;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            transition: transform 0.3s ease;
        }
        
        .content-item.image-content img:hover {
            transform: scale(1.01);
        }
        
        .content-item.pdf-content {
            margin-bottom: 20px;
            padding: 30px;
            background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            border-radius: 8px;
            border: 2px dashed #dee2e6;
            text-align: center;
            transition: all 0.3s ease;
        }
        
        .content-item.pdf-content:hover {
            border-color: #dc3545;
            background: linear-gradient(135deg, #fff5f5 0%, #ffe0e0 100%);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.2);
        }
        
        .content-item.pdf-content i.fa-file-pdf-o {
            font-size: 64px;
            color: #dc3545;
            margin-bottom: 15px;
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0%, 100% {
                transform: scale(1);
            }
            50% {
                transform: scale(1.05);
            }
        }
        
        .content-item.pdf-content .btn {
            padding: 12px 30px;
            border-radius: 5px;
            text-decoration: none;
            color: white;
            background: #dc3545;
            border: none;
            font-weight: 500;
            transition: all 0.3s ease;
            display: inline-block;
        }
        
        .content-item.pdf-content .btn:hover {
            background: #c82333;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(220, 53, 69, 0.3);
        }
        
        .content-item.pdf-content h5 {
            margin-bottom: 10px;
            color: #333;
            font-weight: 600;
        }
        
        .content-item.pdf-content p {
            color: #666;
            margin-bottom: 15px;
            font-size: 14px;
        }
        
        /* Content List Styles - Matching anime__review__item theme */
        .content-list {
            width: 100%;
        }
        
        .content-list .anime__review__item {
            overflow: hidden;
            margin-bottom: 15px;
        }
        
        .content-list .anime__review__item__text {
            overflow: hidden;
            background: #1d1e39;
            border-radius: 10px;
            transition: all 0.3s ease;
        }
        
        .content-list .anime__review__item__text:hover {
            background: #252642;
            transform: translateX(5px);
        }
        
        .content-list .download-btn {
            font-size: 13px;
            color: #ffffff;
            background: #ff5b00;
            display: inline-block;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            padding: 10px 20px;
            border-radius: 4px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .content-list .download-btn:hover {
            background: #e65200;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(255, 91, 0, 0.3);
            color: #ffffff;
            text-decoration: none;
        }
        
        @media (max-width: 768px) {
            .content-list .anime__review__item__text {
                flex-direction: column;
                align-items: flex-start !important;
                gap: 15px;
            }
            
            .content-list .download-btn {
                width: 100%;
                text-align: center;
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
                            <div class="content-list">
                                <?php 
                                $episode_index = 0;
                                foreach($contents as $content){ 
                                    $content_type = isset($content['content_type']) ? $content['content_type'] : 'image';
                                    $content_url = isset($content['content_url']) ? $content['content_url'] : (isset($content['url']) ? $content['url'] : '');
                                    $content_path = getContentUrl($content_url, $content_type);
                                    $order_index = isset($content['order_index']) ? intval($content['order_index']) : $episode_index;
                                    $episode_number = $order_index + 1;
                                    $episode_index++;
                                ?>
                                    <div class="anime__review__item" style="margin-bottom: 15px;">
                                        <div class="anime__review__item__text" style="display: flex; justify-content: space-between; align-items: center; padding: 18px 30px 16px 20px;">
                                            <div style="flex: 1;">
                                                <h6 style="color: #ffffff; font-weight: 700; margin-bottom: 5px; font-size: 16px;">
                                                    <i class="fa fa-play-circle" style="margin-right: 8px; color: #ff5b00;"></i>
                                                    Episode <?= $episode_number ?>
                                                    <?php if($content_type == 'pdf'): ?>
                                                        <span style="color: #b7b7b7; font-weight: 400; font-size: 14px; margin-left: 10px;">
                                                            <i class="fa fa-file-pdf-o" style="color: #dc3545; margin-right: 5px;"></i>PDF
                                                        </span>
                                                    <?php else: ?>
                                                        <span style="color: #b7b7b7; font-weight: 400; font-size: 14px; margin-left: 10px;">
                                                            <i class="fa fa-image" style="color: #34a853; margin-right: 5px;"></i>Image
                                                        </span>
                                                    <?php endif; ?>
                                                </h6>
                                            </div>
                                            <div>
                                                <a href="<?= htmlspecialchars($content_path) ?>" 
                                                   download 
                                                   class="download-btn" 
                                                   style="font-size: 13px; color: #ffffff; background: #ff5b00; display: inline-block; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; padding: 10px 20px; border-radius: 4px; text-decoration: none; transition: all 0.3s ease;">
                                                    <i class="fa fa-download"></i> Download
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                <?php }?>
                            </div>
                        <?php } else{?>
                            <div class="anime__review__item__text" style="text-align: center; padding: 40px;">
                                <i class="fa fa-inbox" style="font-size: 48px; color: #ccc; margin-bottom: 15px;"></i>
                                <p style="color: #666; font-size: 16px;">No Content Available</p>
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