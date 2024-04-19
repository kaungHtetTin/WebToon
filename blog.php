<?php 
$page_name="Blog";
session_start();
include('classes/connect.php');
include('classes/blog.php');
include('classes/util.php');


include('classes/user.php');
$User=new User();
if(isset($_SESSION['webtoon_userid'])){
    $user=$User->details($_SESSION['webtoon_userid']);
}

$page=$_GET['page'];

$Blog=new Blog();
$result=$Blog->get($_GET);

$blogs=$result['blogs'];
$total_blog=$result['total_blog'];

$Util=new Util();

?>

<!DOCTYPE html>
<html lang="zxx">

<head>
    <?php include('layouts/head.php'); ?>
</head>

<body>
    <!-- Page Preloder -->
    <!-- <div id="preloder">
        <div class="loader"></div>
    </div> -->

    <!-- Header Section Begin -->
     <?php 
        include('layouts/header.php');
    ?>
    <!-- Header End -->

    <!-- Normal Breadcrumb Begin -->
    <section class="normal-breadcrumb set-bg" data-setbg="img/normal-breadcrumb.jpg">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 text-center">
                    <div class="normal__breadcrumb__text">
                        <h2>Our Blog</h2>
                        <p>Welcome to the official Anime blog.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Normal Breadcrumb End -->

    <!-- Blog Section Begin -->
    <section class="blog spad">
        <div class="container">
            <div class="row">
                <div class="col-lg-6">
                    <div class="row">
                        <?php 
                        $index=1;
                        if($blogs){
                        for($i=0;$i<count($blogs);$i+=2){
                            $blog=$blogs[$i]; 
                            $col_check = $index%3==0;
                            $mydate=strtotime($blog['date']);
                            $mydate=date( 'Y M,d', $mydate);
                            $index++;
                            ?>

                        <div class="<?php if($col_check) echo "col-lg-12";else echo 'col-lg-6 col-md-6 col-sm-6' ?>">
                            <div class="blog__item small__item set-bg" data-setbg="<?php echo $blog['image_url']; ?>">
                                <div class="blog__item__text">
                                    <p><span class="icon_calendar"></span> <?php echo $mydate ?></p>
                                    <h4><a href="blog_details.php?id=<?php echo $blog['id'];?>"><?php echo $blog['title']; ?></a></h4>
                                </div>
                            </div>
                        </div>

                        <?php } } ?>
                    </div>
                </div>


                <div class="col-lg-6">
                   
                    <div class="row">
                        <?php 
                        $index=0;
                        if($blogs){
                        for($i=1;$i<count($blogs);$i+=2){
                            $blog=$blogs[$i]; 
                            $col_check = $index%3==0;
                            $mydate=strtotime($blog['date']);
                            $mydate=date( 'Y M,d', $mydate);
                            $index++;
                            ?>

                        <div class="<?php if($col_check) echo "col-lg-12";else echo 'col-lg-6 col-md-6 col-sm-6' ?>">
                            <div class="blog__item small__item set-bg" data-setbg="<?php echo $blog['image_url']; ?>">
                                <div class="blog__item__text">
                                    <p><span class="icon_calendar"></span> <?php echo $mydate ?></p>
                                    <h4><a href="blog_details.php?id=<?php echo $blog['id'];?>"><?php echo $blog['title']; ?></a></h4>
                                </div>
                            </div>
                        </div>

                        <?php } }?>
                    </div>
                </div>


            </div>

            <div class="product__pagination">
                <?php if($page>1) { ?>
                    <a href='<?php echo "?page=".($page-1) ?>'><i class="fa fa-angle-double-left"></i></a>
                
                <?php } ?>

                <?php for($i=0;$i<$total_blog/18;$i++){ 
                        $index=$i+1;
                    ?>
                    <a href='<?php echo "?page=$index" ?>' class="<?php if($index==$page) echo 'current-page' ?>"><?php echo $index ?></a>

                <?php } ?>
                <?php if($page<$total_blog/18) { ?>
                    <a href='<?php echo "?page=".($page+1) ?>'><i class="fa fa-angle-double-right"></i></a>
                <?php } ?>
            </div>
        </div>
    </section>
    <!-- Blog Section End -->

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