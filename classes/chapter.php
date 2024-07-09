<?php
Class Chapter {
    public function get($series_id){
        $query="SELECT * FROM chapters WHERE series_id=$series_id";
        $DB=new Database();
        $result=$DB->read($query);
        return $result;
    }
}
?>