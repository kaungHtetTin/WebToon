<?php
    Class Auth{
        public function login($data){

            $error="";
            $email=addslashes($data['email']);
            $password=addslashes($data['password']);
            $password=hash("md5", $password);
            
            $query="select*from users where email= '$email' limit 1";
            
            $DB=new Database();
            $result=$DB->read($query);
            
            if($result){
                $row=$result[0];
             
                
                if($password==$row['password']){
                    
                    //create session data
                    $_SESSION['webtoon_userid']=$row['id'];
                    
                    
                }else{
                    $error="Wrong email or wrong password";
                }
            }else{
                $error="No such email was found";
            }
            return $error;

        }

        
    }
?>