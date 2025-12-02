<?php
session_start();

include('classes/connect.php');
include('classes/series.php');
include('classes/category.php');
include('classes/util.php');
include('classes/view_history.php');

include('classes/user.php');
$User=new User();
if(isset($_SESSION['webtoon_userid'])){

    $user=$User->details($_SESSION['webtoon_userid']);

}else{
    header("location:login.php");
    die;
}

$page_name="My Series";
$page=$_GET['page'];

$Util=new Util();
$Series=new Series();
$Category=new Category();


$series=$Series->getMySeries($user['id']);

$categories=$Category->get();

$total_series=$series['total_series'];
$filtered_series=$series['series'];

$ViewHistory = new ViewHistory();

$dayViews = $ViewHistory->topViewDay();
$weekViews = $ViewHistory->topViewWeek();
$monthViews = $ViewHistory->topViewMonth();
$yearViews = $ViewHistory->topViewYear();

// Helper function to get all category titles from series categories array
function getAllCategoryTitles($series){
    if(isset($series['categories']) && !empty($series['categories'])){
        $titles = [];
        foreach($series['categories'] as $category){
            if(isset($category['title'])){
                $titles[] = $category['title'];
            }
        }
        return !empty($titles) ? implode(', ', $titles) : 'Uncategorized';
    }
    return 'Uncategorized';
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
                           <?php  if($filtered_series){ foreach($filtered_series as $ser){?>
                                <div class="col-lg-4 col-md-6 col-sm-6" >
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
                                                    <li><?php echo getAllCategoryTitles($ser) ?></li>
                                                </ul>
                                                <h5><a href="details.php?id=<?php echo $ser['id']?>"><?php echo $ser['title'] ?></a></h5>
                                            </div>
                                        </div>
                                    </a>
                                </div>

                            <?php }}else {?>
                                <div class="anime__review__item" style="width:100%">
                                     
                                    <div class="anime__review__item__text" style="width:100%;text-align:center">
                                        <p>No series</p>
                                    </div>
                                </div>

                            <?php } ?>
                        </div>
                    </div>
                    <div class="product__pagination">
                        <?php if($page>1) { ?>
                            <a href='<?php echo "?category=$page_name&page=".($page-1) ?>'><i class="fa fa-angle-double-left"></i></a>
                        
                        <?php } ?>

                        <?php for($i=0;$i<$total_series/30;$i++){ 
                                $index=$i+1;
                            ?>
                            <a href='<?php echo "?category=$page_name&page=$index" ?>' class="<?php if($index==$page) echo 'current-page' ?>"><?php echo $index ?></a>

                        <?php } ?>
                        <?php if($page<$total_series/30) { ?>
                            <a href='<?php echo "?category=$page_name&page=".($page+1) ?>'><i class="fa fa-angle-double-right"></i></a>
                        <?php } ?>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-sm-8">
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
                                            data-setbg="<?php echo $Util->normalizeImageUrl($ser['image_url']) ?>">
                                            <div class="ep" style="color:white;font:weight:bold">
                                                <?php if($ser['point']==0){ ?>
                                                    Free
                                                <?php } else{?>
                                                    <span><?= $ser['point']?> </span> <img style="width:20px;height:20px;margin-bottom:3px;" src="img/Coin.png" />
                                                <?php }?>
                                                
                                            </div>
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
                                            data-setbg="<?php echo $Util->normalizeImageUrl($ser['image_url']) ?>">
                                           <div class="ep" style="color:white;font:weight:bold">
                                                <?php if($ser['point']==0){ ?>
                                                    Free
                                                <?php } else{?>
                                                    <span><?= $ser['point']?> </span> <img style="width:20px;height:20px;margin-bottom:3px;" src="img/Coin.png" />
                                                <?php }?>
                                                
                                            </div>
                                            <div class="view"><i class="fa fa-eye"></i> <?php echo $Util->formatCount($ser['view'])?> </div>
                                            <h5><a href="details.php?id=<?php echo $ser['id']?>"><?php echo $ser['title'] ?></a></h5>
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
                                      data-setbg="<?php echo $Util->normalizeImageUrl($ser['image_url']) ?>">
                                    <div class="ep" style="color:white;font:weight:bold">
                                        <?php if($ser['point']==0){ ?>
                                            Free
                                        <?php } else{?>
                                           <span><?= $ser['point']?> </span> <img style="width:20px;height:20px;margin-bottom:3px;" src="img/Coin.png" />
                                        <?php }?>
                                        
                                    </div>
                                    <div class="view"><i class="fa fa-eye"></i> <?php echo $Util->formatCount($ser['view'])?> </div>
                                    <h5><a href="details.php?id=<?php echo $ser['id']?>"><?php echo $ser['title'] ?></a></h5>
                                </div>
                                </a>
                            <?php }?>
          
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