<?php 
Class ViewHistory{
    public function topView($init,$final){
        
        
        $query="SELECT series_id,count(*) as view FROM view_histories 
        WHERE date<='$final' and date > '$init'
        GROUP BY series_id ORDER BY view DESC limit 4";
        $DB=new Database();
        $result=$DB->read($query);
        return $result;
    }

    public function topViewDay(){
        $today = date("Y-m-d");
        $past = date("Y-m-d",strtotime("-1 days"));
        return $this->topView($past,$today);
    }

    public function topViewWeek(){
        $today = date("Y-m-d");
        $past = date("Y-m-d",strtotime("-7 days"));
        return $this->topView($past,$today);
    }

    public function topViewMonth(){
        $today = date("Y-m-d");
        $past = date("Y-m-d",strtotime("-30 days"));
        return $this->topView($past,$today);
    }

    public function topViewYear(){
        $today = date("Y-m-d");
        $past = date("Y-m-d",strtotime("-365 days"));
        return $this->topView($past,$today);
    }

}

?>