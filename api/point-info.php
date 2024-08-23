<?php
    include_once('../classes/connect.php');
    include_once('../classes/payment.php');
    include_once('../classes/jwt.php');

    $point_infor['prices']=[
        [
            'point'=>1,
            'price'=>1,
            'remark'=>""
        ],
        [
            'point'=>1000,
            'price'=>950,
            'remark'=>""
        ],
        [
            'point'=>10000,
            'price'=>9000,
            'remark'=>""
        ],
        [
            'point'=>50000,
            'price'=>40000,
            'remark'=>""
        ],
        [
            'point'=>100000,
            'price'=>70000,
            'remark'=>""
        ],
         
    ];

    $point_infor['payment_methods']=[
        [
            'payment_method'=>"Kbz Pay",
            'phone'=>'09675526045',
            'icon'=>'https://www.worldofwebtoonmmsub.com/uploads/icons/payment-kbz-pay.jpg',
            'account_name'=>"Zon Phoo Paing"
        ],
        [
            'payment_method'=>"Wave Pay",
            'phone'=>'09675526045',
            'icon'=>'https://www.worldofwebtoonmmsub.com/uploads/icons/payment-wave-pay.jpg',
            'account_name'=>"Zon Phoo Paing"
        ],
        [
            'payment_method'=>"AYA Pay",
            'phone'=>'09675526045',
            'icon'=>"https://www.worldofwebtoonmmsub.com/uploads/icons/payment-aya-pay.png",
            'account_name'=>"Zon Phoo Paing"
        ]
    ];

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