<?php 
$page_name="Home";
session_start();

include('classes/connect.php');
include('classes/series.php');
include('classes/util.php');
include('classes/category.php');
include('classes/view_history.php');

include('classes/user.php');
$User=new User();
if(isset($_SESSION['webtoon_userid'])){
    $user=$User->details($_SESSION['webtoon_userid']);
}

$Series=new Series();
$series=$Series->index();
$Util=new Util();
$Category=new Category();



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

                <div class="hero__items set-bg" data-setbg="<?php echo $owl['cover_url'] ?>">
                    <div class="row">
                        <div class="col-lg-6">
                            <div class="hero__text">
                                <div class="label"><?php echo filterCategory($owl['category_id'],$categories) ?></div>
                                <h2><?php echo $owl['title'] ?></h2>
                                <p><?php echo $description ?>...</p>
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

                          <?php  for($i=0;$i<count($trendingSeries);$i++) { 
                                $ser=$trendingSeries[$i];
                            ?>
  
                                <div class="col-lg-4 col-md-6 col-sm-6">
                                    <a href="details.php?id=<?php echo $ser['id']?>">
                                        <div class="product__item">
                                            <div class="product__item__pic set-bg" data-setbg="<?php echo $ser['image_url'] ?>">
                                                <div class="ep"> <?php if($ser['point']==0) echo"Free  "; else echo $ser['point']." Pt."?></div>
                                                <div class="comment"><i class="fa fa-comments"></i> <?php echo $Util->formatCount($ser['comment'])?> </div>
                                                <div class="view"><i class="fa fa-eye"></i> <?php echo $Util->formatCount($ser['view'])?></div>
                                            </div>
                                            <div class="product__item__text">
                                                <ul>
                                                    <li>Active</li>
                                                    <li><?php echo filterCategory($ser['category_id'],$categories) ?></li>
                                                </ul>
                                                <h5><a href="details.php?id=<?php echo $ser['id']?>"><?php echo $ser['title'] ?></a></h5>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                        
                            <?php }?>
                        
                        </div>
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

                            <?php  for($i=0;$i<count($popularSeries);$i++) { 
                                $ser=$popularSeries[$i];
                                ?>
                                <div class="col-lg-4 col-md-6 col-sm-6">
                                    <a href="details.php?id=<?php echo $ser['id']?>">
                                        <div class="product__item">
                                            <div class="product__item__pic set-bg" data-setbg="<?php echo $ser['image_url'] ?>">
                                                <div class="ep"> <?php if($ser['point']==0) echo"Free  "; else echo $ser['point']." Pt."?></div>
                                                <div class="comment"><i class="fa fa-comments"></i> <?php echo $Util->formatCount($ser['comment'])?> </div>
                                                <div class="view"><i class="fa fa-eye"></i> <?php echo $Util->formatCount($ser['view'])?></div>
                                            </div>
                                            <div class="product__item__text">
                                                <ul>
                                                    <li>Active</li>
                                                    <li><?php echo filterCategory($ser['category_id'],$categories) ?></li>
                                                </ul>
                                                <h5><a href="details.php?id=<?php echo $ser['id']?>"><?php echo $ser['title'] ?></a></h5>
                                            </div>
                                        </div>
                                    </a>
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
                            <?php  for($i=0;$i<count($newSeries);$i++) { 
                                $ser=$newSeries[$i];
                                ?>
                                <div class="col-lg-4 col-md-6 col-sm-6">
                                    <a href="details.php?id=<?php echo $ser['id']?>">
                                        <div class="product__item">
                                            <div class="product__item__pic set-bg" data-setbg="<?php echo $ser['image_url'] ?>">
                                                <div class="ep"> <?php if($ser['point']==0) echo"Free  "; else echo $ser['point']." Pt."?></div>
                                                <div class="comment"><i class="fa fa-comments"></i> <?php echo $Util->formatCount($ser['comment'])?> </div>
                                                <div class="view"><i class="fa fa-eye"></i> <?php echo $Util->formatCount($ser['view'])?></div>
                                            </div>
                                            <div class="product__item__text">
                                                <ul>
                                                    <li>Active</li>
                                                    <li><?php echo filterCategory($ser['category_id'],$categories) ?></li>
                                                </ul>
                                                <h5><a href="details.php?id=<?php echo $ser['id']?>"><?php echo $ser['title'] ?></a></h5>
                                            </div>
                                        </div>
                                    </a>
                                </div>
                            <?php }?>
                        </div>
                    </div>
                </div>

                
                <div class="col-lg-4 col-md-6 col-sm-8">

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
                                        data-setbg="<?php echo $ser['image_url'] ?>">
                                        <div class="ep"> <?php if($ser['point']==0) echo"Free  "; else echo $ser['point']." Pt."?></div>
                                        <div class="view"><i class="fa fa-eye"></i> <?php echo $Util->formatCount($ser['view'])?> </div>
                                        <h5><a href="details.php?id=<?php echo $ser['id']?>"><?php echo $ser['title'] ?></a></h5>
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
                                        <div class="ep"> <?php if($ser['point']==0) echo"Free  "; else echo $ser['point']." Pt."?></div>
                                        <div class="view"><i class="fa fa-eye"></i> <?php echo $Util->formatCount($ser['view'])?> </div>
                                        <h5><a href="details.php?id=<?php echo $ser['id']?>"><?php echo $ser['title'] ?></a></h5>
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
                                        <div class="ep"> <?php if($ser['point']==0) echo"Free  "; else echo $ser['point']." Pt."?></div>
                                        <div class="view"><i class="fa fa-eye"></i> <?php echo $Util->formatCount($ser['view'])?> </div>
                                        <h5><a href="details.php?id=<?php echo $ser['id']?>"><?php echo $ser['title'] ?></a></h5>
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
                                      data-setbg="<?php echo $ser['image_url'] ?>">
                                    <div class="ep"> <?php if($ser['point']==0) echo"Free  "; else echo $ser['point']." Pt."?></div>
                                    <div class="view"><i class="fa fa-eye"></i> <?php echo $Util->formatCount($ser['view'])?> </div>
                                    <h5><a href="details.php?id=<?php echo $ser['id']?>"><?php echo $ser['title'] ?></a></h5>
                                </div>
                                </a>
                            <?php }}?>

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
                        <li><?php echo filterCategory($ser['category_id'],$categories) ?></li>
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