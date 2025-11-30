<?php 
$page_name="Home";
session_start();

include('classes/connect.php');
include('classes/series.php');
include('classes/util.php');
include('classes/category.php');
include('classes/view_history.php');
include('classes/visit.php');

include('classes/user.php');
$User=new User();
if(isset($_SESSION['webtoon_userid'])){
    $user=$User->details($_SESSION['webtoon_userid']);
}

$Series=new Series();
$series=$Series->index($_GET);
$Util=new Util();
$Category=new Category();
$Visit = new Visit();


$trendingSeries=$series['trending'];
$popularSeries=$series['popular'];
$newSeries=$series['newadded'];

$owl_carousels=$series['owl_carousel'];

$categories=$Category->get();

function filterCategory($id,$categories){
    foreach($categories as $category){
        if($category['id']==$id){
            return $category['title'];
        }
    }
}

$ViewHistory = new ViewHistory();

$dayViews = $ViewHistory->topViewDay();
$weekViews = $ViewHistory->topViewWeek();
$monthViews = $ViewHistory->topViewMonth();
$yearViews = $ViewHistory->topViewYear();

$newCommentSeries = $Series->newCommentSeries();


?>

<!DOCTYPE html>
<html lang="zxx">

<head>
    <?php include('layouts/head.php'); ?>
    <style>
        .category{
            color:#aaa;
            
            padding:7px;
            border-radius:5px;
            background-color:#30505050;
            margin-bottom:10px;
        }

        .category:hover{
            color:white;
            cursor: pointer;
            background-color:#444;
        }
        
        
    </style>
</head>

<body onload="script();" style="position:relative">
    <!-- Page Preloder -->
    <!-- <div id="preloder">
        <div class="loader"></div>
    </div> -->

    <!-- Header Section Begin -->
     <?php 
        include('layouts/header.php');
    ?>
    <!-- Header End -->

    <!-- Hero Section Begin -->
    <section class="hero">
        <div class="container">
            <div class="hero__slider owl-carousel">

                <?php foreach($owl_carousels as $owl){ 
                    $description = $owl['description'];
                    $description=substr($description,0,50);
                    ?>

                <div class="hero__items set-bg" data-setbg="<?php echo $Util->normalizeImageUrl($owl['cover_url']) ?>">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="hero__text">
                                <div class="label"><?php echo filterCategory($owl['category_id'],$categories) ?></div>
                                <h2 class="stroked-text"><?php echo $owl['title'] ?></h2>
                                <p class="stroked-text"><?php echo $description ?>...</p>
                                <a href="details.php?id=<?php echo $owl['series_id']?>"><span>Watch Now</span> <i class="fa fa-angle-right"></i></a>
                            </div>
                        </div>
                    </div>
                </div>

                <?php }?>
                

            </div>
        </div>
    </section>
    <!-- Hero Section End -->

    <!-- Product Section Begin -->
    <section class="product spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="trending__product">
                        <div class="row">
                            <div class="col-lg-8 col-md-8 col-sm-8">
                                <div class="section-title">
                                    <h4>Trending Now</h4>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4">
                                <div class="btn__all">
                                    <a href="series.php?category=Trending Now&page=1" class="primary-btn">View All <span class="arrow_right"></span></a>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                        <?php if($trendingSeries){ ?>
                          <?php  for($i=0;$i<count($trendingSeries);$i++) { 
                                $ser=$trendingSeries[$i];
                            ?>
  
                                <div class="col-lg-4 col-md-6 col-sm-6">
                                    <a href="details.php?id=<?php echo $ser['id']?>">
                                        <div class="product__item">
                                            <div class="product__item__pic set-bg" data-setbg="<?php echo $Util->normalizeImageUrl($ser['image_url']) ?>">
                                                <div class="ep" style="color:white;font:weight:bold">
                                                    <?php if($ser['point']==0){ ?>
                                                        Free
                                                    <?php } else{?>
                                                        <span><?= $ser['point']?> </span> <img style="width:20px;height:20px;margin-bottom:3px;" src="img/Coin.png" />
                                                    <?php }?>
                                                    
                                                </div>
                                               
                                                <div class="view"><i class="fa fa-eye"></i> <?php echo $Util->formatCount($ser['view'])?></div>
                                            </div>
                                            <div class="product__item__text">
                                                <ul>
                                                    <li>Active</li>
                                                    <li><?php echo filterCategory($ser['category_id'],$categories) ?></li>
                                                    <?php  if(isset($_SESSION['webtoon_userid'])){?>
                                                        <?php if(!$Visit->visited($_SESSION['webtoon_userid'],$ser['id'])) {?>
                                                            <li style="background:red">new</li>
                                                        <?php }?>
                                                    <?php }?>
                                                    
                                                </ul>
                                                <h5><a href="details.php?id=<?php echo $ser['id']?>"><?php echo $ser['title'] ?></a></h5>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                        
                            <?php }?>
                        
                        </div>
                        <?php } else { ?>
                            <div class="col-lg-12 col-md-12 col-sm-12">
                                <div class="section-title">
                                    <h4>No trending series found</h4>
                                </div>
                            </div>
                        <?php }?>
                    </div>
                    <div class="popular__product">
                        <div class="row">
                            <div class="col-lg-8 col-md-8 col-sm-8">
                                <div class="section-title">
                                    <h4>Popular Shows</h4>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4">
                                <div class="btn__all">
                                    <a href="series.php?category=Popular Shows&page=1" class="primary-btn">View All <span class="arrow_right"></span></a>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                        <?php if($popularSeries){ ?>
                            <?php  for($i=0;$i<count($popularSeries);$i++) { 
                                $ser=$popularSeries[$i];
                                ?>
                                <div class="col-lg-4 col-md-6 col-sm-6">
                                    <a href="details.php?id=<?php echo $ser['id']?>">
                                        <div class="product__item">
                                            <div class="product__item__pic set-bg" data-setbg="<?php echo $Util->normalizeImageUrl($ser['image_url']) ?>">
                                               <div class="ep" style="color:white;font:weight:bold">
                                                    <?php if($ser['point']==0){ ?>
                                                        Free
                                                    <?php } else{?>
                                                        <span><?= $ser['point']?> </span> <img style="width:20px;height:20px;margin-bottom:3px;" src="img/Coin.png" />
                                                    <?php }?>
                                                    
                                                </div>
                                                
                                                <div class="view"><i class="fa fa-eye"></i> <?php echo $Util->formatCount($ser['view'])?></div>
                                            </div>
                                            <div class="product__item__text">
                                                <ul>
                                                    <li>Active</li>
                                                    <li><?php echo filterCategory($ser['category_id'],$categories) ?></li>
                                                    <?php  if(isset($_SESSION['webtoon_userid'])){?>
                                                        <?php if(!$Visit->visited($_SESSION['webtoon_userid'],$ser['id'])) {?>
                                                            <li style="background:red">new</li>
                                                        <?php }?>
                                                    <?php }?>
                                                </ul>
                                                <h5><a href="details.php?id=<?php echo $ser['id']?>"><?php echo $ser['title'] ?></a></h5>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            <?php }?>
                            <?php } else { ?>
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <div class="section-title">
                                        <h4>No popular series found</h4>
                                    </div>
                                </div>
                            <?php }?>
                        </div>
                    </div>
                    <div class="recent__product">
                        <div class="row">
                            <div class="col-lg-8 col-md-8 col-sm-8">
                                <div class="section-title">
                                    <h4>Recently Added Shows</h4>
                                </div>
                            </div>
                            <div class="col-lg-4 col-md-4 col-sm-4">
                                <div class="btn__all">
                                    <a href="series.php?category=Recently Added Shows&page=1" class="primary-btn">View All <span class="arrow_right"></span></a>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                        <?php if($newSeries){ ?>
                            <?php  for($i=0;$i<count($newSeries);$i++) { 
                                $ser=$newSeries[$i];
                                ?>
                                <div class="col-lg-4 col-md-6 col-sm-6">
                                    <a href="details.php?id=<?php echo $ser['id']?>">
                                        <div class="product__item">
                                            <div class="product__item__pic set-bg" data-setbg="<?php echo $Util->normalizeImageUrl($ser['image_url']) ?>">
                                                <div class="ep" style="color:white;font:weight:bold">
                                                    <?php if($ser['point']==0){ ?>
                                                        Free
                                                    <?php } else{?>
                                                       <span><?= $ser['point']?> </span> <img style="width:20px;height:20px;margin-bottom:3px;" src="img/Coin.png" />
                                                    <?php }?>
                                                    
                                                </div>
                                               
                                                <div class="view"><i class="fa fa-eye"></i> <?php echo $Util->formatCount($ser['view'])?></div>
                                            </div>
                                            <div class="product__item__text">
                                                <ul>
                                                    <li>Active</li>
                                                    <li><?php echo filterCategory($ser['category_id'],$categories) ?></li>
                                                    <?php  if(isset($_SESSION['webtoon_userid'])){?>
                                                        <?php if(!$Visit->visited($_SESSION['webtoon_userid'],$ser['id'])) {?>
                                                            <li style="background:red">new</li>
                                                        <?php }?>
                                                    <?php }?>
                                                </ul>
                                                <h5><a href="details.php?id=<?php echo $ser['id']?>"><?php echo $ser['title'] ?></a></h5>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            <?php }?>
                            <?php } else { ?>
                                <div class="col-lg-12 col-md-12 col-sm-12">
                                    <div class="section-title">
                                        <h4>No recently added series found</h4>
                                    </div>
                                </div>
                            <?php }?>
                        </div>
                    </div>
                </div>

                
                <div class="col-lg-4 col-md-6 col-sm-8">

                    <div class="product__sidebar">
                        <div class="product__sidebar__view">
                            <div class="section-title">
                                <h5>Download App</h5>
                            </div>
                             <div>
                                <div>
                                    <img src="img/hero/hero-1.jpg" alt="" style="width:100%">
                                </div>
                                <div style="background:#fff;display:flex;padding:5px;align-items: center;">
                                    <div class="card" style="background:white;padding:5px;border-radius:7px;">
                                        <img src="img/logo_round2.png" alt="" style="width:30px;height:30px;">
                                    </div>
                                    <div style="padding-left:5px;">
                                        <div style="font-size:12px;">Word of Webtoon MM Sub</div>
                                        <div style="font-size:10px;">Download for Android</div>
                                    </div>
                                    <a href="http://www.worldofwebtoonmmsub.com/WebtoonMM.apk" style="text-decoration:none; margin-left: auto;">
                                        <div style="background:#ff5b00;color:white;padding:5px;border-radius:3px;font-size:12px;">
                                            Install Now
                                        </div>
                                    </a>
                                </div>
                             </div>
                        </div>
                    </div>

                    <div class="product__sidebar">
                        <div class="product__sidebar__view">
                            <div class="section-title">
                                <h5>Categories</h5>
                            </div>
                            <?php  foreach($categories as $category){
                                $category_id=$category['id'];
                                $title=$category['title'];
                                ?>
                                <a href='<?php echo "series.php?category_id=$category_id&category=$title&page=1" ?>'>
                                    <div class="category" >
                                        <?php echo $title?>
                                    </div>
                                </a>
                            <?php }?>
                        </div>
                    </div>

                    <div class="product__sidebar">
                        <div class="product__sidebar__view">
                            <div class="section-title">
                                <h5>Top Views</h5>
                            </div>
                            <ul class="filter__controls">
                                <li id="day" class="active" data-filter=".day">Day</li>
                                <li id="week" data-filter=".week">Week</li>
                                <li id="month" data-filter=".month">Month</li>
                                <li id="year" data-filter=".years">Years</li>
                            </ul>

                            <div class="filter__gallery">
                            
                            <div id="top_view_container" style="display:none">
                                <?php foreach($yearViews as $view){
                                        $id=$view['series_id'];
                                        $ser = $Series->topViewDetail($id);    
                                    ?>
                                        <a href="details.php?id=<?php echo $ser['id']?>">
                                        <div class="product__sidebar__view__item set-bg mix years"
                                        data-setbg="<?php echo $Util->normalizeImageUrl($ser['image_url']) ?>">
                                        <div class="ep" style="color:white;font:weight:bold">
                                            <?php if($ser['point']==0){ ?>
                                                Free
                                            <?php } else{?>
                                                <span><?= $ser['point']?> </span> <img style="width:20px;height:20px;margin-bottom:3px;" src="img/Coin.png" />
                                            <?php }?>
                                            
                                        </div>
                                        <div class="view"><i class="fa fa-eye"></i> <?php echo $Util->formatCount($ser['view'])?> </div>
                                        
                                        <h5><a href="details.php?id=<?php echo $ser['id']?>" class="stroked-text"><?php echo $ser['title'] ?></a></h5>
                                    </div>
                                    </a>
                                <?php }?>

                                <?php foreach($monthViews as $view){
                                        $id=$view['series_id'];
                                        $ser = $Series->topViewDetail($id);
                                    ?>
                                        <a href="details.php?id=<?php echo $ser['id']?>">
                                        <div class="product__sidebar__view__item set-bg mix month"
                                        data-setbg="<?php echo $Util->normalizeImageUrl($ser['image_url']) ?>">
                                        <div class="ep" style="color:white;font:weight:bold">
                                            <?php if($ser['point']==0){ ?>
                                                Free
                                            <?php } else{?>
                                                <span><?= $ser['point']?> </span> <img style="width:20px;height:20px;margin-bottom:3px;" src="img/Coin.png" />
                                            <?php }?>
                                            
                                        </div>
                                        <div class="view"><i class="fa fa-eye"></i> <?php echo $Util->formatCount($ser['view'])?> </div>
                                        <h5><a href="details.php?id=<?php echo $ser['id']?>" class="stroked-text"><?php echo $ser['title'] ?></a></h5>
                                    </div>
                                    </a>
                                <?php }?>

                                <?php foreach($weekViews as $view){
                                        $id=$view['series_id'];
                                        $ser = $Series->topViewDetail($id);
                                    ?>
                                        <a href="details.php?id=<?php echo $ser['id']?>">
                                        <div class="product__sidebar__view__item set-bg mix week"
                                        data-setbg="<?php echo $Util->normalizeImageUrl($ser['image_url']) ?>">
                                        <div class="ep" style="color:white;font:weight:bold">
                                            <?php if($ser['point']==0){ ?>
                                                Free
                                            <?php } else{?>
                                                <span><?= $ser['point']?> </span> <img style="width:20px;height:20px;margin-bottom:3px;" src="img/Coin.png" />
                                            <?php }?>
                                            
                                        </div>
                                        <div class="view"><i class="fa fa-eye"></i> <?php echo $Util->formatCount($ser['view'])?> </div>
                                        <h5><a href="details.php?id=<?php echo $ser['id']?>"  class="stroked-text"><?php echo $ser['title'] ?></a></h5>
                                    </div>
                                    </a>
                                <?php }?>

                            </div>
                            
                            <?php if($dayViews){ foreach($dayViews as $view){
                                    $id=$view['series_id'];
                                    $ser = $Series->topViewDetail($id);
                                    ?>
                                    <a href="details.php?id=<?php echo $ser['id']?>">
                                    <div class="product__sidebar__view__item set-bg mix day"
                                      data-setbg="<?php echo $Util->normalizeImageUrl($ser['image_url']) ?>">
                                    <div class="ep" style="color:white;font:weight:bold">
                                        <?php if($ser['point']==0){ ?>
                                            Free
                                        <?php } else{?>
                                           <span><?= $ser['point']?> </span> <img style="width:20px;height:20px;margin-bottom:3px;" src="img/Coin.png" />
                                        <?php }?>
                                        
                                    </div>
                                    <div class="view"><i class="fa fa-eye"></i> <?php echo $Util->formatCount($ser['view'])?> </div>
                                    
                                    <h5><a href="details.php?id=<?php echo $ser['id']?>"  class="stroked-text"><?php echo $ser['title'] ?></a></h5>
                                </div>
                                </a>
                            <?php }}?>

                        </div>
                    </div>
                
        
    </div>
</div>
</div>
</div>
</div>
</section>
<!-- Product Section End -->

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

<script>
    document.getElementById('day').addEventListener('click',()=>{
        document.getElementById('top_view_container').setAttribute('style',"");
    })
    document.getElementById('week').addEventListener('click',()=>{
        document.getElementById('top_view_container').setAttribute('style',"");
    })
    document.getElementById('month').addEventListener('click',()=>{
        document.getElementById('top_view_container').setAttribute('style',"");
    })
    document.getElementById('year').addEventListener('click',()=>{
        document.getElementById('top_view_container').setAttribute('style',"");
    })
    
     
</script>

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