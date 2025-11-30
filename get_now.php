<?php
session_start();

include('classes/connect.php');
include('classes/series.php');
include('classes/category.php');
include('classes/util.php');
include('classes/comment.php');
include('classes/chapter.php');

include('classes/user.php');
$User=new User();
$Series=new Series();

$isSaved=false;
$series_id=$_GET['id'];
$myRating=0;

if(isset($_SESSION['webtoon_userid'])){
    $user_id=$_SESSION['webtoon_userid'];
    $user=$User->details($user_id);
    $isSaved=$Series->isSaved($user_id,$series_id);

    $myRating=$Series->getMyRating($user_id,$series_id);
}else{
    header('Location:login.php');
    die;
}


$page_name="Details";


$Util=new Util();
$Comment=new Comment();

$series=$Series->details($_GET);
$series_you_like=$Series->getSeriesYouMayLike($series['category_id']);
$seriesRating=$Series->getRating($series_id);
 

if($_SERVER['REQUEST_METHOD']=="POST"){
    if(isset($_SESSION['webtoon_userid'])){
        $Series->saveSeriesByUser($_POST);
        header('Location:details.php?id='.$series_id);
        die;
    }
}






?>


<!DOCTYPE html>
<html lang="zxx">

<head>
    <?php include('layouts/head.php'); ?>
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
                       
                        <span>Get Now</span>
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
                    <div class="col-lg-3">
                        <div style="width:150px;height:300px;" class="anime__details__pic set-bg" data-setbg="<?php echo $Util->normalizeImageUrl($series['image_url']) ?>">
                            <div class="comment"><i class="fa fa-comments"></i> <?php echo $Util->formatCount($series['comment']) ?></div>
                            <div class="view"><i class="fa fa-eye"></i> <?php echo $Util->formatCount($series['view']) ?></div>
                        </div>
                    </div>
                    <div class="col-lg-9">
                        <div class="anime__details__text">
                            <div class="anime__details__title">
                                <h3><?php echo $series['title'] ?></h3>
                                
                            </div>
                        
                            <div class="anime__details__widget">
                                <div class="row">
                                    <div class="col-lg-6 col-md-6">
                                        <ul>
                                            <li><span>Type:</span> TV Series</li>
                                            <li><span>Studios:</span> Lerche</li>
                                            <li><span>Date aired:</span> Oct 02, 2019 to ?</li>
                                            <li><span>Status:</span> Airing</li>
                                            <li><span>Genre:</span> Action, Adventure, Fantasy, Magic</li>
                                        </ul>
                                    </div>
                                    <div class="col-lg-6 col-md-6">
                                        <ul>
                                            <li><span>Scores:</span> 7.31 / 1,515</li>
                                            <li><span>Rating:</span> <?php echo $seriesRating ?></li>
                                            <li><span>Views:</span> <?php echo $Util->formatCount($series['view']) ?></li>
                                             <li><span>Point:</span> <?php echo $Util->formatCount($series['point']) ?></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="anime__details__btn">
                                <?php if(!$isSaved){ ?>
                                    <?php if($user['point']<$series['point']){ ?>
                                        <div style="background:yellow;padding:5px; border-radius:5px;">
                                            You don't have enough point to get this Series.
                                        </div>
                                        <br>
                                        <h5 style="color:white;">Do you really want to get more point?</h5>
                                        <br>
                                        <a href="vip_register.php" class="follow-btn">Get More Point Now</a>
                                    <?php }else{?>
                                        <h5 style="color:white;">Do you really want to get this series?</h5>
                                        <br>
                                        <table style="width:200px;color:white">
                                            <tr>
                                                <td>Your Point </td>
                                                <td><?php echo $user['point'] ?></td>
                                            </tr>
                                            <tr>
                                                <td>Cost </td>
                                                <td> <?php echo $series['point'] ?> </td>
                                            </tr>
                                            <tr style="color:yellow">
                                                <td>Remaining </td>
                                                <td> <?php echo $user['point']-$series['point'] ?> </td>
                                            </tr>
                                        </table>
                                        <br>
                                        
                                        <form action="" method="POST">
                                            <input type="hidden" name="series_id" value="<?php echo $series_id ?>">
                                            <input type="hidden" name="user_id" value="<?php echo $user['id'] ?>">
                                            <input type="hidden" name="action_type" value="save">
                                            <a style="cursor:pointer;color:white;" onclick="this.parentNode.submit()" class="follow-btn">Get Now</a>
                                        </form>

                                    <?php }?>
                                    <br>
                                <?php }else{?>
                                        <a style="cursor:pointer;color:white;" class="follow-btn"><i class="fa fa-heart"></i></a>
                                        <a style="cursor:pointer;color:white;" class="follow-btn">Saved</a>
                                <?php }?>
                                
                                </div>
                            </div>
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

    </body>

    </html>