<?php
Class Series {

    public function index(){
        $DB=new Database();
        $query="SELECT * FROM series ORDER BY view DESC Limit 6";
        
        $trending=$DB->read($query);
        $result['trending']=$trending;

        $query="SELECT * FROM series ORDER BY view DESC Limit 6";
   
        $popular=$DB->read($query);
        $result['popular']=$popular;

        $query="SELECT * FROM series ORDER BY id DESC limit 6";
     
        $newadded=$DB->read($query);
        $result['newadded']=$newadded;

        $query="SELECT * FROM owl_carousels JOIN series on series.id=owl_carousels.series_id";
        $owl_carousels=$DB->read($query);
        $result['owl_carousel']=$owl_carousels;

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

    public function getSeriesByCategory($data){
        $page=$data['page'];
        $category_id=$data['category_id'];
        $page--;
        $offset=30;
        $count=$page*$offset;

        $query="SELECT * FROM series WHERE category_id=$category_id ORDER BY id DESC LIMIT $count,$offset";
        $DB=new Database();
        $series=$DB->read($query);
        $result['series']=$series;

        $query="SELECT count(*) as total_series FROM series WHERE category_id=$category_id";
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

    public function getSeriesYouMayLike($category_id){
        $query="SELECT * FROM series WHERE category_id=$category_id ORDER BY view DESC limit 5";
        $DB=new Database();
        $result=$DB->read($query);
        return $result;
    }

    public function newCommentSeries(){
        $query ="SELECT * FROM series
        JOIN comments ON series.id=comments.series_id
        ORDER BY comments.date DESC LIMIT 4
        ";
        $DB=new Database();
        $result=$DB->read($query);
        return $result;
    }

    public function details($data){
        $id=$data['id'];
        $query="SELECT * FROM series WHERE id=$id limit 1";
        $DB=new Database();
        $result=$DB->read($query);

        $query="UPDATE series SET view=view+1 WHERE id=$id";
        $DB->save($query);
        
        $query="INSERT INTO view_histories (series_id) VALUES ($id)";
        $DB->save($query);

        return $result[0];
    }



    public function topViewDetail($id){
        $query="SELECT * FROM series WHERE id=$id limit 1";
        $DB=new Database();
        $result=$DB->read($query);
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

        $query="SELECT * FROM series WHERE id=$series_id";
        $series=$DB->read($query);

        $series=$series[0];

        $remaining_point=$user['point']-$series['point'];

        $query="INSERT INTO saves (user_id,series_id) VALUE ($user_id,$series_id)";
        $save_count="UPDATE series SET save=save+1 WHERE id=$series_id";
        $user_query="UPDATE users SET point=$remaining_point WHERE id=$user_id";

        $DB->save($query);
        $DB->save($save_count);
        $DB->save($user_query);
    }

    public function rate($data){
        $user_id=$data['user_id'];
        $series_id=$data['series_id'];
        $star=$data['star'];

        $DB=new Database();

        $query="SELECT * FROM ratings WHERE user_id=$user_id and series_id=$series_id";
        $result=$DB->read($query);
        if($result){
            $query ="UPDATE ratings SET star=$star WHERE user_id=$user_id and series_id=$series_id";
        }else{
            $query ="INSERT INTO ratings (user_id,series_id,star) VALUES($user_id,$series_id,$star)";
        }
    
        $DB->save($query);

    }

    public function getMyRating($user_id,$series_id){
        $query="SELECT * FROM ratings WHERE user_id=$user_id and series_id=$series_id";
        $DB=new Database();
        $result=$DB->read($query);
        if($result) return $result[0]['star'];
        else return 0;

    }

    public function getRating($series_id){
        $query="SELECT count(*) as count, SUM(star) as stars FROM ratings WHERE series_id=$series_id";
        $DB=new Database();
        $ratings=$DB->read($query);

        $ratings=$ratings[0];
        $count=$ratings['count'];
        $stars=$ratings['stars'];
        if($count>0){
            $final_rating_value=$stars/$count;
            return number_format((float)$final_rating_value, 1, '.', '');
        }else{
            return 0.0;
        }
        

    }

}
?>