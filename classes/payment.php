<?php
Class payment {
    public function add($data,$FILE){

        $user_id=$data['user_id'];

        $image_path="";
        $time=time();

        $file=$FILE['myfile']['name'];
        $file_loc=$FILE['myfile']['tmp_name'];
        $folder=$_SERVER['DOCUMENT_ROOT']."/uploads/images/screenshots/";
        if(move_uploaded_file($file_loc,$folder.$file)){
            
            $screenshot_url = "/uploads/images/screenshots/".$file;
            $query = "INSERT INTO payment_histories (user_id,screenshot_url) VALUE ($user_id,'$screenshot_url')";

            $DB=new Database();
            $res=$DB->save($query);
            if($res){
                $response['status']="success";
                return $response;
            }else{
                $response['status']="fail";
                $response['error']="error 900";
                $response['message']="Unexpected error";
                return $response;
            }

        }else{
            $response['status']="fail";
            $response['error']="902";
            $response['message']="Unexpected error";
            return $response;
        }
    }
    
    public function getPaymentHistory($user_id){
        $DB=new Database();
        $query = "SELECT * FROM payment_histories WHERE user_id=$user_id ORDER BY id DESC";
        $result = $DB->read($query);
        return $result;
    }
}

?>