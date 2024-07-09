<?php
session_start();

include('classes/connect.php');
include('classes/series.php');
include('classes/category.php');
include('classes/util.php');
include('classes/view_history.php');
include('classes/visit.php');

include('classes/user.php');
$User=new User();
if(isset($_SESSION['webtoon_userid'])){

    $user=$User->details($_SESSION['webtoon_userid']);

}

$page_name=$_GET['category'];
$page=$_GET['page'];

$Visit = new Visit();

$Util=new Util();
$Series=new Series();

$Category=new Category();
$categories=$Category->get();

$category_main_id = $_GET['category_id'];

if($page_name=="Popular Shows"){
    $series=$Series->getPopularSeries($_GET);
}else if($page_name=="Trending Now"){
    $series=$Series->getTendingSeries($_GET);
}else if($page_name=="Recently Added Shows"){
    $series=$Series->getNewSeries($_GET);
}else{
     $series=$Series->getSeriesByCategory($_GET);
}

$total_series=$series['total_series'];
$filtered_series=$series['series'];

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

<body>
    <!-- Page Preloder -->
    <div id="preloder">
        <div class="loader"></div>
    </div>

   <!-- Header Section Begin -->
     <?php 
        include('layouts/header.php');
    ?>
    <!-- Header End -->

    <!-- Breadcrumb Begin -->
    <div class="breadcrumb-option">
        <div class="container">
            <div class="row">
                <div class="col-lg-12">
                    <div class="breadcrumb__links">
                        <a href="./index.php"><i class="fa fa-home"></i> Home</a>
                         
                        <span><?php echo $page_name ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Breadcrumb End -->

    <!-- Product Section Begin -->
    <section class="product-page spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-8">
                    <div class="product__page__content">
                        <div class="product__page__title">
                            <div class="row">
                                <div class="col-lg-8 col-md-8 col-sm-6">
                                    <div class="section-title">
                                        <h4> <?php echo $page_name ?></h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <?php if($filtered_series) { foreach($filtered_series as $ser){?>
                                <div class="col-lg-4 col-md-6 col-sm-6" >
                                    <a href="details.php?id=<?php echo $ser['id']?>">
                                        <div class="product__item">
                                            <div class="product__item__pic set-bg" data-setbg="<?php echo $ser['image_url'] ?>">
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
                                                    <li><?php echo $Category->filterCategory($ser['category_id'],$categories) ?></li>
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

                            <?php }}else{?>
                                <div class="anime__review__item" style="width:100%;text-align:center">     
                                    <div class="anime__review__item__text">
                                        <p>No Series</p>
                                    </div>
                                </div>
                            <?php }?>
                            
                        </div>
                    </div>

                    <div class="product__pagination">
                        <?php if($page>1) { ?>
                            <a href='<?php echo "?category_id=$category_main_id&category=$page_name&page=".($page-1) ?>'><i class="fa fa-angle-double-left"></i></a>
                        
                        <?php } ?>

                        <?php for($i=0;$i<$total_series/30;$i++){ 
                                $index=$i+1;
                            ?>
                            <a href='<?php echo "?category_id=$category_main_id&category=$page_name&page=$index" ?>' class="<?php if($index==$page) echo 'current-page' ?>"><?php echo $index ?></a>

                        <?php } ?>
                        <?php if($page<$total_series/30) { ?>
                            <a href='<?php echo "?category_id=$category_main_id&category=$page_name&page=".($page+1) ?>'><i class="fa fa-angle-double-right"></i></a>
                        <?php } ?>
                    </div>
                    
                </div>
                <div class="col-lg-4 col-md-6 col-sm-8">
                    <div class="product__sidebar">

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
                                            data-setbg="<?php echo $ser['image_url'] ?>">
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
                                            data-setbg="<?php echo $ser['image_url'] ?>">
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
                                            data-setbg="<?php echo $ser['image_url'] ?>">
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
                                </div>
                            
                            <?php foreach($dayViews as $view){
                                    $id=$view['series_id'];
                                    $ser = $Series->topViewDetail($id);
                                ?>
                                    <a href="details.php?id=<?php echo $ser['id']?>">
                                    <div class="product__sidebar__view__item set-bg mix day"
                                      data-setbg="<?php echo $ser['image_url'] ?>">
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

                        </div>
                    </div>
    <div class="product__sidebar__comment">

        <div class="section-title">
            <h5>New Comment</h5>
        </div>
        
        <?php foreach($newCommentSeries as $ser){ ?>
            <div class="product__sidebar__comment__item">
                <div class="product__sidebar__comment__item__pic">
                    <img style="width:100px;" src="<?php echo $ser['image_url'] ?>" alt="">
                </div>
                <div class="product__sidebar__comment__item__text">
                    <ul>
                        <li>Active</li>
                        <?php if($ser['point']==0){?>
                            <li>Free</li>
                        <?php }?>
                        <li><?php echo $Category->filterCategory($ser['category_id'],$categories) ?></li>
                    </ul>
                    <h5><a href="details.php?id=<?php echo $ser['series_id']?>"><?php echo $ser['title']; ?></a></h5>
                    <span><i class="fa fa-eye"></i> <?php echo $ser['view']; if($ser['view']>1) echo " Views"; else echo " View"; ?> </span>
                </div>
            </div>
        <?php }?>

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