<?php
    Class Util{
        public function formatCount($count){
            if($count<10000){
                return $count;
            }if($count>=10000 && $count<1000000){
                $number=$count/1000;
                return number_format((float)$number, 2, '.', '')."k";
            }else{
                $number=$count/1000000;
                return number_format((float)$number, 2, '.', '')."M";
            }
        }
    }
?>