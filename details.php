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
if(isset($_SESSION['webtoon_userid'])){
    $user_id=$_SESSION['webtoon_userid'];
    $user=$User->details($user_id);
    $isSaved=$Series->isSaved($user_id,$series_id);
}


$page_name="Details";




$Util=new Util();
$Comment=new Comment();

$series=$Series->details($_GET);


if($_SERVER['REQUEST_METHOD']=="POST"){
    if(isset($_SESSION['webtoon_userid'])){
        $action_type=$_POST['action_type'];

        if($action_type=="comment"){
            $Comment->create($_POST);
            header("Location:details.php?id=$series_id");
            die;
        }else if($action_type=="save"){
            $Series->saveSeriesByUser($_POST);
        }        
    }else{
        header('Location:login.php');
        die;
    }
}


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
                        <div class="anime__details__pic set-bg" data-setbg="<?php echo $series['image_url'] ?>">
                            <div class="comment"><i class="fa fa-comments"></i> <?php echo $Util->formatCount($series['comment']) ?></div>
                            <div class="view"><i class="fa fa-eye"></i> <?php echo $Util->formatCount($series['view']) ?></div>
                        </div>
                    </div>
                    <div class="col-lg-9">
                        <div class="anime__details__text">
                            <div class="anime__details__title">
                                <h3><?php echo $series['title'] ?></h3>
                                <span>フェイト／ステイナイト, Feito／sutei naito</span>
                            </div>
                            <div class="anime__details__rating">
                                <div class="rating">
                                    <a href="#"><i class="fa fa-star"></i></a>
                                    <a href="#"><i class="fa fa-star-o"></i></a>
                                    <a href="#"><i class="fa fa-star"></i></a>
                                    <a href="#"><i class="fa fa-star"></i></a>
                                    <a href="#"><i class="fa fa-star-half-o"></i></a>
                                </div>
                                <span>1.029 Votes</span>
                            </div>
                            <p> <?php echo $series['description'] ?></p>
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
                                            <li><span>Rating:</span> 8.5 / 161 times</li>
                                            <li><span>Views:</span> <?php echo $Util->formatCount($series['view']) ?></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <div class="anime__details__btn">
                                <form action="" method="POST">
                                    <input type="hidden" name="series_id" value="<?php echo $series_id ?>">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id'] ?>">
                                    <input type="hidden" name="action_type" value="save">
                                        <?php if($isSaved){ ?>
                                            <a style="cursor:pointer;color:white;" onclick="this.parentNode.submit()" class="follow-btn"><i class="fa fa-heart"></i></a>
                                            <a href="cursor:pointer;color:white;" onclick="this.parentNode.submit()" class="follow-btn">Remove</a>
                                        <?php }else { ?>
                                            <a style="cursor:pointer;color:white;" onclick="this.parentNode.submit()" class="follow-btn"><i class="fa fa-heart-o"></i> Save</a>
                                        <?php }?>
                                </form>
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

                            <?php if($chapters) {foreach($chapters as $chapter){ 
                                if(isset($user)){ 
                                    if($user['is_vip']==1)$download_url=$chapter['download_url'];
                                    else $download_url="vip_register.php";
                                }else{
                                    $download_url="login.php";
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

                        <div class="anime__details__review">
                            <div class="section-title">
                                <h5>Reviews</h5>
                            </div>

                            <?php if($comments) {foreach($comments as $comment){ 
                                $mydate=strtotime($comment['date']);
                                $mydate=date( 'Y M,d', $mydate);
                                ?>
                                <div class="anime__review__item">
                                    <div class="anime__review__item__pic">
                                        <img src="<?php echo $comment['image_url'] ?>" alt="">
                                    </div>
                                    <div class="anime__review__item__text">
                                        <h6><?php echo $comment['first_name']." ".$comment['last_name'] ?> - <span> <?php echo $mydate ?></span></h6>
                                        <p><?php echo $comment['body'] ?></p>
                                    </div>
                                </div>
                            <?php }}else{?>
                                <div class="anime__review__item">
                                     
                                    <div class="anime__review__item__text">
                                        <p>No Review</p>
                                    </div>
                                </div>
                            <?php }?>
                            <?php if($total_comment>30) {?>
                             <div class="product__pagination">
                                <?php if($cmt>1) { ?>
                                    <a href='<?php echo "?id=$series_id&cmt=".($cmt-1) ?>'><i class="fa fa-angle-double-left"></i></a>
                                
                                <?php } ?>

                                <?php for($i=0;$i<$total_comment/30;$i++){ 
                                        $index=$i+1;
                                    ?>
                                    <a href='<?php echo "?id=$series_id&cmt=$index" ?>' class="<?php if($index==$cmt) echo 'current-page' ?>"><?php echo $index ?></a>

                                <?php } ?>
                                <?php if($cmt<$total_comment/30) { ?>
                                    <a href='<?php echo "?id=$series_id&cmt=".($cmt+1) ?>'><i class="fa fa-angle-double-right"></i></a>
                                <?php } ?>
                            </div>
                            <?php }?>
                            
                        </div>

                        <div class="anime__details__form">
                            <div class="section-title">
                                <h5>Your Comment</h5>
                            </div>
                            <form action="" method="POST">
                                <?php if(isset($_SESSION['webtoon_userid'])){ ?>
                                    <input type="hidden" name="series_id" value="<?php echo $series_id ?>">
                                    <input type="hidden" name="user_id" value="<?php echo $user['id'] ?>">
                                    <input type="hidden" name="action_type" value="comment">
                                <?php }?>
                                <textarea placeholder="Your Comment" name="body"></textarea>
                                <button type="submit"><i class="fa fa-location-arrow"></i> Review</button>
                            </form>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-4">
                        <div class="anime__details__sidebar">
                            <div class="section-title">
                                <h5>you might like...</h5>
                            </div>
                            <div class="product__sidebar__view__item set-bg" data-setbg="img/sidebar/tv-1.jpg">
                                <div class="ep">18 / ?</div>
                                <div class="view"><i class="fa fa-eye"></i> 9141</div>
                                <h5><a href="#">Boruto: Naruto next generations</a></h5>
                            </div>
                            <div class="product__sidebar__view__item set-bg" data-setbg="img/sidebar/tv-2.jpg">
                                <div class="ep">18 / ?</div>
                                <div class="view"><i class="fa fa-eye"></i> 9141</div>
                                <h5><a href="#">The Seven Deadly Sins: Wrath of the Gods</a></h5>
                            </div>
                            <div class="product__sidebar__view__item set-bg" data-setbg="img/sidebar/tv-3.jpg">
                                <div class="ep">18 / ?</div>
                                <div class="view"><i class="fa fa-eye"></i> 9141</div>
                                <h5><a href="#">Sword art online alicization war of underworld</a></h5>
                            </div>
                            <div class="product__sidebar__view__item set-bg" data-setbg="img/sidebar/tv-4.jpg">
                                <div class="ep">18 / ?</div>
                                <div class="view"><i class="fa fa-eye"></i> 9141</div>
                                <h5><a href="#">Fate/stay night: Heaven's Feel I. presage flower</a></h5>
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