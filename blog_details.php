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

$Blog=new Blog();
$result=$Blog->getBlogDetail($_GET);

$blog=$result['blog'];
$feeds=$result['feeds'];
$Util=new Util();

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

    <!-- Blog Details Section Begin -->
    <section class="blog-details spad">
        <div class="container">
            <div class="row d-flex justify-content-center">
                <div class="col-lg-8">
                    <div class="blog__details__title">
                        <h6><span>
                            <?php 
                                $mydate=strtotime($blog['date']);
                                $mydate=date( 'Y M,d', $mydate);
                                echo $mydate;
                            ?>
                        </span></h6>
                        <h2><?php echo $blog['title'] ?></h2>
                         
                    </div>
                </div>
                <div class="col-lg-12">
                    <div class="blog__details__pic">
                        <img src="<?php echo $blog['cover_url'] ?>" alt="">
                    </div>
                </div>
                <div class="col-lg-8">
                    <div class="blog__details__content">
                        <div class="blog__details__text">
                            <p> <?php echo $blog['description']; ?> </p>
                        </div>


                        <?php if($feeds){foreach($feeds as $feed){ ?>
                            <div class="blog__details__item__text">
                                <h4><?php echo $feed['title'] ?></h4>
                                <img src="<?php echo $feed['image'] ?>" alt="">
                                <p><?php echo $feed['body'] ?></p>
                            </div>
                        <?php }}?>

                    </div>
                </div>
            </div>
        </section>
        <!-- Blog Details Section End -->

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