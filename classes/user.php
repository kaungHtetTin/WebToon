<?php 
Class User{

    public function create($data){
        
        $email=$data['email'];
        $first_name=$data['first_name'];
        $last_name=$data['last_name'];
        $password=$data['password'];

        $image_url="../uploads/placeholder.jpg";

        $error="";
        if($email=="")$error="Please enter email or phone";
        if($first_name=="") $error.="<br>Please enter password";
        if($last_name=="") $error.="<br>Please enter last name";
        if($password=="") $error.="<br>Please enter password";

        if($error!=""){
            $result['error']=$error;
            return $result;
        }

        $password=hash("md5", $password);

        $DB=new Database();

        $query="SELECT * FROM users WHERE email='$email'";
        $user=$DB->read($query);

        if($user){
            $error.="The email has already registered! Please try again.";
            $result['error']=$error;
            return $result;
        }

        $query="INSERT INTO users (email,first_name,last_name,password,image_url) 
                VALUES('$email','$first_name','$last_name','$password','$image_url')";
        
       
        $DB->save($query);

        $query="SELECT * FROM users WHERE email='$email'";
        $user=$DB->read($query);

        $result['error']=$error;
        $result['user']=$user[0];
        return $result;
    }

    public function details($id){
        $query="SELECT * FROM users WHERE id=$id";
        $DB=new Database();
        $user=$DB->read($query);
        return $user[0];
    }


    public function update($data,$FILE){
        $user_id = $data['user_id'];
        $first_name = $data['first_name'];
        $last_name = $data['last_name'];
        
        $phone = $data['phone'];

        $query = "UPDATE users SET first_name='$first_name', last_name='$last_name', phone = '$phone' WHERE id = $user_id";
        $DB = new Database();
        $DB->save($query);

        if($FILE['myfile']['name']!=""){
            $file=$FILE['myfile']['name'];
            $file_loc=$FILE['myfile']['tmp_name'];
            $folder="../uploads/images/profiles/";
            if(move_uploaded_file($file_loc,$folder.$file)){
                $image_url = $folder.$file;
                $query = "UPDATE users SET image_url='$image_url' WHERE id = $user_id";
                $DB->save($query);

            }
        }

        $response['status']="success";
        return $response;

    }

    public function changePassword($data){
        $email = $data['email'];

        $old_password=addslashes($data['old_password']);
        $old_password=hash("md5", $old_password);

        $new_password=addslashes($data['new_password']);
        $new_password=hash("md5", $new_password);

        $query="select*from users where email= '$email' limit 1";
            
        $DB=new Database();
        $result=$DB->read($query);

        if($result){
            $row=$result[0];
            if($old_password==$row['password']){
                //update new password
                $query = "UPDATE users SET password ='$new_password' WHERE email = '$email' ";
                $DB->save($query);
                $response['status']="success";
                
            }else{
                $response['status']="Fail";
                $response['msg']="Incorrect current password";
            }
        }else{
            $response['status']="Fail";
            $response['msg']="Unexpected error!";
        }

        return $response;
    }

    public function deleteAccount($data){
        $user_id = $data['user_id'];
        $email = $data['email'];

        $password=addslashes($data['password']);
        $password=hash("md5", $password);

        $query="select*from users where email= '$email' limit 1";
            
        $DB=new Database();
        $result=$DB->read($query);

        if($result){
            $row=$result[0];
            if($password==$row['password']){
                // delete account
                $query = "DELETE FROM comments WHERE user_id=$user_id";
                $DB->save($query);

                $query = "DELETE FROM likes WHERE user_id=$user_id";
                $DB->save($query);

                $query = "DELETE FROM ratings WHERE user_id=$user_id";
                $DB->save($query);

                $query = "DELETE FROM saves WHERE user_id=$user_id";
                $DB->save($query);

                $query = "DELETE FROM visits WHERE user_id=$user_id";
                $DB->save($query);

                $query = "DELETE FROM users WHERE id=$user_id";
                $DB->save($query);
                $response['status']="success";
                
            }else{
                $response['status']="Fail";
                $response['msg']="Incorrect password";
            }
        }else{
            $response['status']="Fail";
            $response['msg']="Unexpected error!";
        }

        return $response;

    }
}

?>