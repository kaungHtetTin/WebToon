
<!-- Header Section Begin -->
<header class="header">
    <div class="container">
        <div class="row">
            <div class="col-lg-2">
                <div class="header__logo" style="padding:0px;">
                    <a href="./index.php">
                        <img src="img/logo_round2.png" alt="" style="height:50px;margin-top:7px;">
                    </a>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="header__nav">
                    <nav class="header__menu mobile-menu">
                        
                        <ul>
                            <li class="<?php if($page_name=='Home') echo'active' ?>"><a href="./index.php">Home</a></li>
                            <li class="<?php if($page_name=='My Series') echo'active' ?>"><a href="my_series.php?page=1">My Series</a></li>
                            <li class="<?php if($page_name=='Blog') echo'active' ?>"><a href="./blog.php?page=1">Our Blog</a></li>
                            <?php if(isset($categories)){ ?>
                            <li><a href="">Categories <span class="arrow_carrot-down"></span></a>
                                <ul class="dropdown">
                                    <?php foreach($categories as $category){
                                        $category_id=$category['id'];
                                        $title=$category['title'];
                                        ?>
                                        <li><a href='<?php echo "series.php?category_id=$category_id&category=$title&page=1" ?>'> <?php echo $title ?></a></li>
                                    <?php }?>
                                </ul>
                            </li>
                            <?php }?>
                        </ul>
                    </nav>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="header__right">
                    
                    <!-- <a href="#" class="search-switch"><span class="icon_search"></span></a> -->
                   
                    <?php  if(isset($_SESSION['webtoon_userid'])){?>
                             <a href="profile.php">
                                <img src="<?php echo $user['image_url'] ?>" style="width:30px; height:30px; border-radius:50px;">
                               
                            </a>
                             <a href="logout.php" style="font-size:16px;">Logout</a>
                             <a href="vip_register.php">
                                <span style="border-radius:20px;padding:7px;width:70px;text-align:center;font-size:13px;font-weight:bold;color:yellow">
                                    <?php echo $Util->formatCount( $user['point']) ?>
                                    <img style="width:25px;height:25px;margin-bottom:5px;" src="img/Coin.png" />
                                </span>
                             </a>

                    <?php } else { ?>
                            <a href="./login.php"><span class="icon_profile"></span></a>
                            <a href="login.php" style="font-size:16px;">Login</a>
                    <?php }?>
                    
                </div>
            </div>
        </div>
        <div id="mobile-menu-wrap"></div>
    </div>
</header>
<!-- Header End -->