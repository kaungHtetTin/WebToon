<?php
Class Series {

    public function index(){
        $query="SELECT * FROM series ORDER BY view DESC Limit 9";
        $DB=new Database($query);
        $trending=$DB->read($query);
        $result['trending']=$trending;

        $query="SELECT * FROM series ORDER BY view DESC Limit 9";
        $DB=new Database($query);
        $popular=$DB->read($query);
        $result['popular']=$popular;

        $query="SELECT * FROM series ORDER BY id DESC limit 9";
        $DB=new Database($query);
        $newadded=$DB->read($query);
        $result['newadded']=$newadded;

        return $result;

    }

    public function get($data){
        $page=$data['page'];
        $page--;
        $offset=30;
        $count=$page*$offset;

        $query="SELECT * FROM series";
        $DB=new Database();
        $series=$DB->read($query);
        $result['series']=$series;

        $query="SELECT count(*) as total_series FROM series";
        $count=$DB->read($query);
        $result['total_series']=$count[0]['total_series'];

        return $result;
    }

    public function getPopularSeries($data){
        $page=$data['page'];
        $page--;
        $offset=30;
        $count=$page*$offset;

        $query="SELECT * FROM series ORDER BY view DESC LIMIT $count,$offset";
        $DB=new Database();
        $series=$DB->read($query);
        $result['series']=$series;

        $query="SELECT count(*) as total_series FROM series";
        $count=$DB->read($query);
        $result['total_series']=$count[0]['total_series'];
        return $result;
    }

    public function getTendingSeries($data){
        $page=$data['page'];
        $page--;
        $offset=30;
        $count=$page*$offset;

        $query="SELECT * FROM series ORDER BY view DESC LIMIT $count,$offset";
        $DB=new Database();
        $series=$DB->read($query);
        $result['series']=$series;

        $query="SELECT count(*) as total_series FROM series ";
        $count=$DB->read($query);
        $result['total_series']=$count[0]['total_series'];
        return $result;
    }

    public function getNewSeries($data){

        $page=$data['page'];
        $page--;
        $offset=30;
        $count=$page*$offset;

        $query="SELECT * FROM series ORDER BY id DESC LIMIT $count,$offset";
        $DB=new Database();
        $series=$DB->read($query);
        $result['series']=$series;

        $query="SELECT count(*) as total_series FROM series";
        $count=$DB->read($query);
        $result['total_series']=$count[0]['total_series'];
        return $result;
    }

    public function getMySeries($user_id){
        $query =" SELECT * FROM series JOIN saves ON series.id=saves.series_id WHERE saves.user_id=$user_id";
        $DB=new Database();
        $series=$DB->read($query);

        $query="SELECT count(*) as total_series FROM saves WHERE user_id=$user_id";
        $total=$DB->read($query);

        $result['total_series']=$total[0]['total_series'];
        $result['series']=$series;
        return $result;

    }

    public function details($data){
        $id=$data['id'];
        $query="SELECT * FROM series WHERE id=$id";
        $DB=new Database();
        $result=$DB->read($query);

        $query="UPDATE series SET view=view+1 WHERE id=$id";
        $DB->save($query);

        return $result[0];
    }

    public function isSaved($user_id,$series_id){
        $query="SELECT * FROM saves WHERE user_id=$user_id and series_id=$series_id limit 1";
        $DB=new Database();
        $result=$DB->read($query);
        return $result;
    }

    public function saveSeriesByUser($data){
        $series_id=$data['series_id'];
        $user_id=$data['user_id'];

        $DB=new Database();
        $query="SELECT * FROM users WHERE id=$user_id";
        $user=$DB->read($query);
        
        $user=$user[0];
        if($user['is_vip']==1){

            if($this->isSaved($user_id,$series_id)){
                $query="DELETE FROM saves WHERE user_id=$user_id AND series_id=$series_id";
                $save_count="UPDATE series SET save=save-1 WHERE id=$series_id";
            }else{
                $query="INSERT INTO saves (user_id,series_id) VALUE ($user_id,$series_id)";
                $save_count="UPDATE series SET save=save+1 WHERE id=$series_id";
            }

            $DB->save($query);
            $DB->save($save_count);

            header("location:details.php?id=$series_id");
            die;

        }else{
            header('Location:vip_register.php');
            die;
        }
    }

}
?>