<?php 
Class User{

    public function create($data){
        $email=$data['email'];
        $first_name=$data['first_name'];
        $last_name=$data['last_name'];
        $password=$data['password'];
        $image_url="img/placeholder.jpg";

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
}

?>