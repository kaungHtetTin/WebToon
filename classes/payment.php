<?php
Class payment {
    public function add($data,$FILE){

        $user_id=$data['user_id'];

        $image_path="";
        $time=time();

        $file=$FILE['myfile']['name'];
        $file_loc=$FILE['myfile']['tmp_name'];
        
        // Get path relative to project root (go up from classes/ directory)
        $base_path = dirname(__DIR__);
        $folder = $base_path . "/uploads/images/screenshots/";
        
        // Create directory if it doesn't exist
        if (!file_exists($folder)) {
            mkdir($folder, 0755, true);
        }
        
        // Generate unique filename to prevent overwrites
        $file_extension = pathinfo($file, PATHINFO_EXTENSION);
        $file_name = pathinfo($file, PATHINFO_FILENAME);
        $unique_file = $file_name . "_" . $time . "." . $file_extension;
        
        if(move_uploaded_file($file_loc,$folder.$unique_file)){
            
            $screenshot_url = "/uploads/images/screenshots/".$unique_file;
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