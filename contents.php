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
                        <?php if($contents){ ?>
                            <?php foreach($contents as $content){ ?>
                                
                                <img src="<?= $content['url'] ?>" alt="" srcset="" style="width:100%;border-radius:3px;margin-bottom:5px;">

                            <?php }?>
                        <?php } else{?>
                            <div class="anime__review__item__text">
                                <p>No Content</p>
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