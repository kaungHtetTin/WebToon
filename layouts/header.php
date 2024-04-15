
<!-- Header Section Begin -->
<header class="header">
    <div class="container">
        <div class="row">
            <div class="col-lg-2">
                <div class="header__logo">
                    <a href="./index.php">
                        <img src="img/logo.png" alt="">
                    </a>
                </div>
            </div>
            <div class="col-lg-7">
                <div class="header__nav">
                    <nav class="header__menu mobile-menu">
                        <ul>
                            <li class="<?php if($page_name=='Home') echo'active' ?>"><a href="./index.php">Home</a></li>
                            <li><a href="./categories.html">Categories <span class="arrow_carrot-down"></span></a>
                                <ul class="dropdown">
                                    <li><a href="./categories.html">Categories</a></li>
                                    <li><a href="./anime-details.html">Anime Details</a></li>
                                    <li><a href="./anime-watching.html">Anime Watching</a></li>
                                    <li><a href="./blog-details.html">Blog Details</a></li>
                                    <li><a href="./signup.html">Sign Up</a></li>
                                    <li><a href="./login.html">Login</a></li>
                                </ul>
                            </li>

                            <li><a href="./blog.php">Our Blog</a></li>
                            <li class="<?php if($page_name=='My Series') echo'active' ?>"><a href="my_series.php?page=1">My Series</a></li>
                        </ul>
                    </nav>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="header__right">
                    <a href="#" class="search-switch"><span class="icon_search"></span></a>
                    <?php  if(isset($_SESSION['webtoon_userid'])){?>
                             <a href="profile.php">
                                <img src="<?php echo $user['image_url'] ?>" style="width:30px; height:30px; border-radius:50px;">
                               
                            </a>
                             <a href="logout.php">Log out</a>

                    <?php } else { ?>
                            <a href="./login.php"><span class="icon_profile"></span></a>
                            <a href="login.php">Log In</a>
                    <?php }?>
                    
                </div>
            </div>
        </div>
        <div id="mobile-menu-wrap"></div>
    </div>
</header>
<!-- Header End -->