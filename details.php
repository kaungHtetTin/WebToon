<?php
session_start();

include('classes/connect.php');
include('classes/series.php');
include('classes/category.php');
include('classes/util.php');
include('classes/comment.php');
include('classes/chapter.php');
include('classes/visit.php');

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
    
    $Visit = new Visit();
    $Visit->add($user_id,$series_id);
}


$page_name="Details";




$Util=new Util();
$Comment=new Comment();

$series=$Series->details($_GET);
$series_you_like=$Series->getSeriesYouMayLike($series['category_id']);

 

if($_SERVER['REQUEST_METHOD']=="POST"){
    if(isset($_SESSION['webtoon_userid'])){
        $action_type=$_POST['action_type'];

        if($action_type=="comment"){
            $Comment->create($_POST);
            header("Location:details.php?id=$series_id");
            die;
        }else if($action_type=="save"){
            $Series->saveSeriesByUser($_POST);
        }else if($action_type=="rating"){
            $Series->rate($_POST);
            header("Location:details.php?id=$series_id");
            die;
        }    
    }else{
        header('Location:login.php');
        die;
    }
}

$seriesRating=$Series->getRating($series_id);

$total_comment=$series['comment'];
if(isset($_GET['cmt'])){
    $cmt=$_GET['cmt'];
}else{
    $cmt=1;
}
$comments=$Comment->get($series_id,$cmt);


$Chapter=new Chapter();
$chapters=$Chapter->get($series_id);




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
                       
                        <span>Details</span>
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
                        <div class="anime__details__pic set-bg" data-setbg="<?php echo $series['image_url'] ?>" style="height:350px;">
                            <div class="comment"><i class="fa fa-comments"></i> <?php echo $Util->formatCount($series['comment']) ?></div>
                            <div class="view"><i class="fa fa-eye"></i> <?php echo $Util->formatCount($series['view']) ?></div>
                        </div>
                    </div>
                    <div class="col-lg-9">
                        <div class="anime__details__text">
                            <div class="anime__details__title">
                                <h3><?php echo $series['title'] ?></h3>
                                <spa><?php echo $series['short'] ?></span>
                            </div>
                            <div class="anime__details__rating">
                               
                                <div class="rating">
                                    <form action="" method="POST">
                                        <input type="hidden" name="series_id" value="<?php echo $series_id ?>">
                                        <input type="hidden" name="user_id" value="<?php echo $user['id'] ?>">
                                        <input id="input_value" type="hidden" name="star" value="0">
                                        <input type="hidden" name="action_type" value="rating">

                                        <?php for($i=1;$i<=5;$i++){ ?>
                                            <?php if($i<=$myRating){ ?>
                                                <a style="cursor:pointer;color:white;" onclick="this.parentNode.submit()"><i id="star<?php echo $i ?>" class="fa fa-star"></i></a>
                                            <?php } else{?>
                                                <a style="cursor:pointer;color:white;" onclick="this.parentNode.submit()"><i id="star<?php echo $i ?>" class="fa fa-star-o"></i></a>
                                            <?php }?>
                                        <?php } ?>    
                                    </form>
                                </div>
                               
                                <span>
                                    <?php 
                                        if($myRating>0){
                                            echo "My rating - ". $myRating;
                                        }else{
                                            echo "Define rating";
                                        }
                                    ?>
                                </span>
                            </div>
                            <p> <?php echo $series['description'] ?></p>
                            <div class="anime__details__widget">
                                <div class="row">
                                    <div class="col-lg-6 col-md-6">
                                        <ul>
                                            <li><span>Original Work:</span> <?php echo $series['original_work'] ?></li>
                                            <li><span>Upload Status</span> <?php echo $series['upload_status'] ?></li>
                                            <li><span>Genre:</span> <?php echo $series['genre'] ?></li>
                                        </ul>
                                    </div>
                                    <div class="col-lg-6 col-md-6">
                                        <ul>
                                            <li><span>Rating:</span> <?php echo $seriesRating ?></li>
                                            <li><span>Views:</span> <?php echo $Util->formatCount($series['view']) ?> </li>
                                             <li><span>Coin:</span> <?php echo $Util->formatCount($series['point']) ?> <img style="width:20px;height:20px;margin-bottom:5px;" src="img/Coin.png" /></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="anime__details__btn">
                               
                                    <?php if($isSaved){ ?>
                                        <a style="cursor:pointer;color:white;" class="follow-btn"><i class="fa fa-heart"></i></a>
                                        <a style="cursor:pointer;color:white;" class="follow-btn">Saved</a>
                                    <?php }else { ?>
                                        <?php if($series['point']>0){ ?>
                                            <a href="get_now.php?id=<?php echo $series_id?>" class="follow-btn">Get Now</a>
                                        <?php }?>
                                        
                                    <?php }?>
                              
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <div class="col-lg-8 col-md-8">

                        <div class="anime__details__review">
                            <div class="section-title">
                                <h5>Chapters</h5>
                            </div>

                            <?php if($chapters) {
                               
                                foreach($chapters as $key=>$chapter){ 
                                
                                if($chapter['is_active']==0){
                                    $download_url=$chapter['download_url'];
                                }else{
                                    if(isset($user)){ 
                                        if($series['point']>0){
                                            if($isSaved)$download_url=$chapter['download_url'];
                                            else $download_url="get_now.php?id=".$series_id;
                                        }else{
                                            $download_url=$chapter['download_url'];
                                        }
                                        
                                    }else{
                                        $download_url="login.php";
                                    }
                                }
                                
                                ?>
                               <div class="anime__review__item" style="width:100%">
                                     
                                    <div class="anime__review__item__text" style="width:100%;">
                                        <div style="display:flex;justify-content: space-between;">
                                            <div style="width:45%">
                                                <h6><?php echo $chapter['title'] ?></h6>
                                            </div>
                                            <div style="width:45%;color:white;text-align:right">
                                               <a href="<?php echo $download_url ?>" style="text-decoration:none;color:white">
                                                 Download <i class="fa fa-download"></i>
                                               </a>
                                            </div>
                                        </div>
                                        
                                    </div>
                                </div>

                            <?php }}else{?>
                                <div class="anime__review__item">
                                     
                                    <div class="anime__review__item__text">
                                        <p>No Chapter</p>
                                    </div>
                                </div>
                            <?php }?>
                            
                            
                        </div>

                    </div>
                    <div class="col-lg-4 col-md-4">
                        <div class="anime__details__sidebar">
                            <div class="section-title">
                                <h5>you might like...</h5>
                            </div>
                            
                            <?php foreach($series_you_like as $ser){ ?>
                                <?php if($ser['id']!=$series_id){ ?>
                                    <a href="details.php?id=<?php echo $ser['id']?>">
                                        <div class="product__sidebar__view__item set-bg" data-setbg="<?php echo $ser['image_url'] ?>">
                                            <div class="ep">  <?php echo $ser['uploaded_chapter']." / ".$ser['total_chapter'] ?> </div>
                                            <div class="view"><i class="fa fa-eye"></i> <?php echo $ser['view'] ?></div>
                                            <h5> <a href="details.php?id=<?php echo $ser['id']?>" class="stroked-text"><?php echo $ser['title'] ?></a></h5>
                                        </div>
                                    </a>
                                <?php }?>
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

            var star1 = document.getElementById('star1');
            var star2 = document.getElementById('star2');
            var star3 = document.getElementById('star3');
            var star4 = document.getElementById('star4');
            var star5 = document.getElementById('star5');
            var input_value= document.getElementById('input_value');

            document.getElementById('star1').addEventListener("mouseenter",()=>{
                
                star1.setAttribute("class", "fa fa-star");
                input_value.setAttribute("value","1");
            });
            document.getElementById('star2').addEventListener("mouseenter",()=>{
                
                star1.setAttribute("class", "fa fa-star");
                star2.setAttribute("class", "fa fa-star");
                input_value.setAttribute("value","2");
            });
             document.getElementById('star3').addEventListener("mouseenter",()=>{
                
                star1.setAttribute("class", "fa fa-star");
                star2.setAttribute("class", "fa fa-star");
                star3.setAttribute("class", "fa fa-star");
                input_value.setAttribute("value","3");
            });
             document.getElementById('star4').addEventListener("mouseenter",()=>{
                
                star1.setAttribute("class", "fa fa-star");
                star2.setAttribute("class", "fa fa-star");
                star3.setAttribute("class", "fa fa-star");
                star4.setAttribute("class", "fa fa-star");
                input_value.setAttribute("value","4");
            });

            document.getElementById('star5').addEventListener("mouseenter",()=>{
                
                star1.setAttribute("class", "fa fa-star");
                star2.setAttribute("class", "fa fa-star");
                star3.setAttribute("class", "fa fa-star");
                star4.setAttribute("class", "fa fa-star");
                star5.setAttribute("class", "fa fa-star");
                input_value.setAttribute("value","5");
            });

            document.getElementById('star5').addEventListener("mouseleave",()=>{
                
                star1.setAttribute("class", "fa fa-star-o");
                star2.setAttribute("class", "fa fa-star-o");
                star3.setAttribute("class", "fa fa-star-o");
                star4.setAttribute("class", "fa fa-star-o");
                star5.setAttribute("class", "fa fa-star-o");
                input_value.setAttribute("value","0");
            });

            document.getElementById('star4').addEventListener("mouseleave",()=>{
                
                star1.setAttribute("class", "fa fa-star-o");
                star2.setAttribute("class", "fa fa-star-o");
                star3.setAttribute("class", "fa fa-star-o");
                star4.setAttribute("class", "fa fa-star-o");
                star5.setAttribute("class", "fa fa-star-o");
                input_value.setAttribute("value","0");
            });
            document.getElementById('star3').addEventListener("mouseleave",()=>{
                
                star1.setAttribute("class", "fa fa-star-o");
                star2.setAttribute("class", "fa fa-star-o");
                star3.setAttribute("class", "fa fa-star-o");
                star4.setAttribute("class", "fa fa-star-o");
                star5.setAttribute("class", "fa fa-star-o");
                input_value.setAttribute("value","0");
            });
            document.getElementById('star2').addEventListener("mouseleave",()=>{
                
                star1.setAttribute("class", "fa fa-star-o");
                star2.setAttribute("class", "fa fa-star-o");
                star3.setAttribute("class", "fa fa-star-o");
                star4.setAttribute("class", "fa fa-star-o");
                star5.setAttribute("class", "fa fa-star-o");
                input_value.setAttribute("value","0");
            });
            document.getElementById('star1').addEventListener("mouseleave",()=>{
                
                star1.setAttribute("class", "fa fa-star-o");
                star2.setAttribute("class", "fa fa-star-o");
                star3.setAttribute("class", "fa fa-star-o");
                star4.setAttribute("class", "fa fa-star-o");
                star5.setAttribute("class", "fa fa-star-o");
                input_value.setAttribute("value","0");
                
            });



        </script>


    </body>

    </html>