<?php
    include_once('../classes/connect.php');
    include_once('../classes/payment.php');
    include_once('../classes/jwt.php');

    // Database connection using PDO (same as admin)
    $db_name = "mysql:host=localhost;dbname=webtoon2";
    $username = "root";
    $password = "";
    $conn = new PDO($db_name, $username, $password);

    // Get base URL for icon generation
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
    $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : (isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'localhost');
    $base_url = $protocol . $host . dirname(dirname($_SERVER['PHP_SELF']));

    // Function to generate icon URL based on payment type
    function getPaymentIconUrl($payment_type, $base_url) {
        // Normalize payment type name for icon filename
        $icon_name = strtolower(str_replace(' ', '-', trim($payment_type)));
        // Common extensions to try (in order of preference)
        $extensions = ['jpg', 'png', 'jpeg', 'svg'];
        // Return the most likely path (jpg is most common)
        $icon_path = "/uploads/icons/payment-{$icon_name}.jpg";
        return $base_url . $icon_path;
    }

    // Fetch point prices from database
    $point_infor['prices'] = [];
    try {
        $stmt = $conn->prepare("SELECT point, amount FROM `point_prices` ORDER BY point ASC");
        $stmt->execute();
        $prices = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($prices as $price) {
            $point_infor['prices'][] = [
                'point' => (int)$price['point'],
                'price' => (float)$price['amount'],
                'remark' => ""
            ];
        }
    } catch (Exception $e) {
        // Fallback to empty array if query fails
        $point_infor['prices'] = [];
    }

    // Fetch payment methods from database
    $point_infor['payment_methods'] = [];
    try {
        $stmt = $conn->prepare("SELECT payment_type, payment_number, account_name FROM `payment_methods` ORDER BY id ASC");
        $stmt->execute();
        $methods = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        foreach ($methods as $method) {
            $point_infor['payment_methods'][] = [
                'payment_method' => $method['payment_type'],
                'phone' => $method['payment_number'],
                'icon' => getPaymentIconUrl($method['payment_type'], $base_url),
                'account_name' => $method['account_name']
            ];
        }
    } catch (Exception $e) {
        // Fallback to empty array if query fails
        $point_infor['payment_methods'] = [];
    }

    $point_infor['plan_message']="Series များဝင်ရောက် ကြည့်ရှုရန် Webtoon Point များကို အသုံးပြုရမည်ဖြစ်ပြီး အောက်ပါ Plan များအတိုင်း ဝယ်ယူ နိုင်ပါသည်။";
    $point_infor['instruction_message']= "ကျသင့်ငွေပေးချေပြီးပါက ငွေလွှဲပြီးစီးကြောင်း screenshot အား ‌အောက်တွင် ပေးပို့ရမည်ဖြစ်ပြီး၊ Admin များမှ Screenshot အား စစ်ဆေးပြီးစီးပါက သက်ဆိုင်ရာ Account သို့ Point အရေအတွက်ကို ထည့်သွင်းပေးသွားမည်ဖြစ်ပါသည်။ ";

    $JWT = new JWT();

    $requestHeaders = apache_request_headers();
    $jwt_auth_token =$requestHeaders['Authorization'];

    if($jwt_auth_token=="guest_user"){
        echo json_encode($point_infor);
        return;
    }

    $user = $JWT->validateJWT($jwt_auth_token);

    if($user){
        $user_id = $user['userId'];

        $Payment = new payment();
        $paymentHistories = $Payment->getPaymentHistory($user_id);
        $point_infor['payment_histories']=$paymentHistories;

    }else{  
        $point_infor['payment_histories']=false;
    }


    echo json_encode($point_infor);
?>