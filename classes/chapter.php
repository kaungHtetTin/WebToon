<?php
Class Chapter {
    public function get($series_id){
        $query="SELECT id,series_id,title,description,is_active FROM chapters WHERE series_id=$series_id";
        $DB=new Database();
        $result=$DB->read($query);
        return $result;
    }

    public function details($data){
        $id = $data['id'];
        $DB =new Database();
        $query = "SELECT * FROM chapters WHERE id=$id LIMIT 1";
        $result = $DB->read($query);
        if($result) return $result[0];
        else $result;
    }

    public function download($chapter_id, $user_id){
        $DB = new Database();

        // get chapter
        $query = "SELECT * FROM chapters WHERE id=$chapter_id LIMIT 1";
        $chapter = $DB->read($query);
        if(!$chapter){
            return ['status'=>'Fail','message'=>'Invalid','code'=>1];
        }

        $chapter = $chapter[0];
        $series_id = $chapter['series_id'];

        if($chapter['is_active']==0){
            return  ['status'=>'success','download_url'=>$chapter['download_url']];
        }

        // check if purchased
        $query = "SELECT * FROM saves WHERE user_id=$user_id AND series_id=$series_id LIMIT 1";
        $purchased = $DB->read($query);

        if(!$purchased){
            return ['status'=>'Fail','message'=>'You need to purchase the series','code'=>2];
        }

        return  ['status'=>'success','download_url'=>$chapter['download_url']];

    }

    public function getContent($chapter_id){
        $DB = new Database();
        $query = "SELECT * FROM contents WHERE chapter_id=$chapter_id";
        $contents = $DB->read($query);
        return $contents;
    }
}
?>