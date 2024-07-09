<?php
    class Visit {
        public function add($user_id, $series_id){
            $DB=new Database();
            $query = "SELECT * FROM visits WHERE user_id=$user_id and series_id=$series_id";
            $result = $DB->read($query);
    
            if(!$result){
                $query = "INSERT INTO visits (user_id,series_id) VALUES ($user_id,$series_id)";
                $DB->save($query);
            }
            
        }
        
        public function visited($user_id, $series_id){
            $DB=new Database();
            $query = "SELECT * FROM visits WHERE user_id=$user_id and series_id=$series_id";
            $result = $DB->read($query);
            
            return $result;
        }
    }
?>