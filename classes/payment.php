<?php
Class payment {
    public function add($data,$FILE){


        $user_id=$data['user_id'];

        $image_path="";
        $time=time();

        $file=$FILE['myfile']['name'];
        $file_loc=$FILE['myfile']['tmp_name'];
        $folder="uploads/images/screenshots/";
        if(move_uploaded_file($file_loc,$folder.$file)){

            $query = "INSERT INTO payment_histories (user_id,screenshot_url) VALUE ($user_id,'$file')";

            $DB=new Database();
            $res=$DB->save($query);
            if($res){
                $response['status']="success";
                return $response;
            }else{
                $response['status']="fail";
                $response['error']="error 900";
                return $response;
            }

        }else{
            $response['status']="fail";
            $response['error']="902";
            return $response;
        }



    }
}

?>