<?php
Class Category{
    public function get(){
        $query="SELECT * FROM categories";
        $DB=new Database();
        $result=$DB->read($query);
        return $result;
    }

    function filterCategory($id,$categories){
        foreach($categories as $category){
            if($category['id']==$id){
                return $category['title'];
            }
    }
}
}
?>