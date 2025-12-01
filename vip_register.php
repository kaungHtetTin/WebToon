<?php
$page_name="Get Points";
session_start();
$error="";

include('classes/connect.php');
include('classes/user.php');
include('classes/payment.php');

$User=new User();
$Payment = new Payment();

$payments=false;
if(isset($_SESSION['webtoon_userid'])){
    $user=$User->details($_SESSION['webtoon_userid']);
    $payments = $Payment->getPaymentHistory($user['id']);
   
}


if($_SERVER['REQUEST_METHOD']=="POST"){
    
    if(!isset($_FILES['myfile'])){
        $error = "Please select a screenshot";
    }else{
        
        $result = $Payment->add($_POST,$_FILES);
       
    }
}


?>


<!DOCTYPE html>
<html lang="zxx">

<head>
    <?php include('layouts/head.php'); ?>
    <style>
        .fcolor{
            color:white;
        }
    </style>
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
                       
                        <span>Get Points</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Breadcrumb End -->

    <!-- Anime Section Begin -->
    <section class="anime-details spad">
        <div class="container">
            <div class="anime__details__content" style="color:white">
                <h3 class="fcolor">Point ဝယ်ယူမယ်</h3>
                <p class="fcolor"> Series များဝင်ရောက် ကြည့်ရှုရန် Webtoon Point များကို အသုံးပြုရမည်ဖြစ်ပြီး အောက်ပါ Plan များအတိုင်း ဝယ်ယူ နိုင်ပါသည်။</p>

                <?php
                    // Fetch dynamic point prices from admin-defined table
                    $DB = new Database();
                    $pointPrices = $DB->read("SELECT * FROM point_prices ORDER BY point ASC");
                ?>

                <?php if (!empty($pointPrices)) : ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th class="fcolor">Point</th>
                                <th class="fcolor">ကျသင့်ငွေ</th>
                                <th class="fcolor">မှတ်ချက်</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pointPrices as $pp) : ?>
                                <tr>
                                    <td class="fcolor"><?= htmlspecialchars($pp['point']); ?></td>
                                    <td class="fcolor"><?= htmlspecialchars($pp['amount']); ?> kyats</td>
                                    <td class="fcolor"></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else : ?>
                    <p class="fcolor">လောလောဆယ် point price မရှိသေးပါ။ Admin မှ ပြင်ဆင်ပါမည်။</p>
                <?php endif; ?>
                <br><br>

                <h4 class="fcolor">ငွေပေးချေနိုင်သော နည်းလမ်းများ</h4>
                <p class="fcolor">အောက်ပါနည်းလမ်းများအတိုင်း‌ငွေပေးချေနိုင်ပါသည်။</p>

                <?php
                    // Fetch dynamic payment methods from admin-defined table
                    $paymentMethods = $DB->read("SELECT * FROM payment_methods ORDER BY id ASC");
                ?>

                <?php if (!empty($paymentMethods)) : ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th class="fcolor">Payment Type</th>
                                    <th class="fcolor">Account Name</th>
                                    <th class="fcolor">Payment Number</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($paymentMethods as $pm) : ?>
                                    <tr>
                                        <td class="fcolor"><?= htmlspecialchars($pm['payment_type']); ?></td>
                                        <td class="fcolor"><?= htmlspecialchars($pm['account_name']); ?></td>
                                        <td class="fcolor">
                                            <span class="payment-number"><?= htmlspecialchars($pm['payment_number']); ?></span>
                                            <button type="button"
                                                    class="btn btn-sm btn-outline-light ms-2"
                                                    onclick='copyPaymentNumber(<?= json_encode($pm["payment_number"]); ?>)'
                                                    title="Copy number">
                                                <i class="fa fa-clone"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else : ?>
                    <p class="fcolor">လောလောဆယ် payment method မရှိသေးပါ။ Adminမှ ပြင်ဆင်ပါမည်။</p>
                <?php endif; ?>

                <br><br>
                <?php if(isset($_SESSION['webtoon_userid'])){ ?>
                <h4 class="fcolor">ကျသင့်ငွေပေးချေပြီးပါက</h4>
                <p class="fcolor">ကျသင့်ငွေပေးချေပြီးပါက ငွေလွှဲပြီးစီးကြောင်း screenshot အား ‌အောက်တွင် ပေးပို့ရမည်ဖြစ်ပြီး၊ Admin များမှ Screenshot အား စစ်ဆေးပြီးစီးပါက သက်ဆိုင်ရာ Account သို့ Point အရေအတွက်ကို ထည့်သွင်းပေးသွားမည်ဖြစ်ပါသည်။  </p>

                <br><br>
                <div class="login__form">
                    <h4 class="fcolor">Sending Screenshot</h4>
                    <p class="fcolor"> Please select a payment screenshot.</p>

                    <form action="" method="POST" enctype="multipart/form-data">
                        <input type="file" name="myfile" accept="image/*">
                        <input type="hidden" name="user_id" value="<?php echo $user['id'] ?>" >                   
                        <?php if($error!="") {?>
                            <div style="padding:5px; border-radius:3px;width:370px;margin-bottom:20px;color:red">
                                <?php echo $error ?>
                            </div>
                        <?php }?>
                        <button type="submit" class="site-btn">Send Now</button>
                    </form>
                </div>
                
                <br><br>
                
                 <h4 class="fcolor">Payment Histories</h4>
                 <br>
                    <?php if($payments){ ?>
                        <table class="table">
                            <thead>
                                <th class="fcolor">Date</th>
                                <th class="fcolor">Amount</th>
                                <th class="fcolor">Point Received</th>
                                <th class="fcolor">Status</th>
                            </thead>
                            <?php foreach($payments as $payment){ ?>
                                <tr>
                                    <td class="fcolor"><?= date('d M , Y',  strtotime( $payment['date'])) ; ?></td>
                                    <td class="fcolor"><?= $payment['amount']==0? '':$payment['amount']  ?> </td>
                                    <td class="fcolor"><?= $payment['point']==0? '':$payment['point']  ?> </td>
                                    <td class="fcolor"><?= $payment['verified']==1? '':'Requesting'  ?> </td>
                                </tr>
                            <?php }?>
                             
                        </table>
                    <?php }else {?>
                        <div>No payment history</div>
                    <?php }?>
                
                <?php }else {?>

                    <div> Webtoon point များဝယ်ယူရန် သင်၏ Account ကို Login ဝင်ထားရန် လိုအပ်ပါသည်။ </div>
                    <a href="login.php"> Login Now</a>
                <?php }?>

            </div>

        </section>
        <!-- Anime Section End -->

<!-- Footer Section Begin -->
    <?php 
        include('layouts/footer.php');
    ?>
  <!-- Footer Section End -->


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
            function copyPaymentNumber(number) {
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(number).then(function () {
                        alert('Copied: ' + number);
                    }).catch(function () {
                        alert('Copy failed. Please copy manually.');
                    });
                } else {
                    var tempInput = document.createElement('input');
                    tempInput.value = number;
                    document.body.appendChild(tempInput);
                    tempInput.select();
                    try {
                        document.execCommand('copy');
                        alert('Copied: ' + number);
                    } catch (e) {
                        alert('Copy failed. Please copy manually.');
                    }
                    document.body.removeChild(tempInput);
                }
            }
        </script>

        </body>

        </html>