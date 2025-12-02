<?php
Class Series {

    // Helper method to get categories for a series
    private function getSeriesCategories($series_id){
        $DB = new Database();
        $series_id = intval($series_id);
        
        // Get categories from junction table
        $query = "SELECT c.id, c.title 
                  FROM series_categories sc 
                  JOIN categories c ON sc.category_id = c.id 
                  WHERE sc.series_id = $series_id 
                  ORDER BY c.title ASC";
        $result = $DB->read($query);
        
        // Fallback to category_id if no junction table entries
        if(empty($result)){
            $query = "SELECT c.id, c.title 
                      FROM categories c 
                      WHERE c.id = (SELECT category_id FROM series WHERE id = $series_id LIMIT 1)";
            $fallback = $DB->read($query);
            if($fallback && !empty($fallback)){
                return $fallback;
            }
        }
        
        return $result ? $result : [];
    }

    // Helper method to add categories to series array/object
    private function addCategoriesToSeries($series_data){
        if(is_array($series_data)){
            // If it's an array of series
            if(isset($series_data[0]) && is_array($series_data[0])){
                foreach($series_data as &$series){
                    $series['categories'] = $this->getSeriesCategories($series['id']);
                }
            } else {
                // Single series object
                if(isset($series_data['id'])){
                    $series_data['categories'] = $this->getSeriesCategories($series_data['id']);
                }
            }
        }
        return $series_data;
    }

    public function index($data){
 
        if(isset($data['user_id'])){
            $user_id = $data['user_id'];
        }else{
            $user_id=0;
        } 

        $DB=new Database();
        $query="SELECT 
            s.*,
            CASE 
                WHEN (SELECT 1 FROM visits v WHERE v.series_id = s.id AND v.user_id = $user_id) IS NOT NULL THEN '1'
                ELSE '0'
            END AS visited
            FROM series s
            ORDER BY s.view DESC Limit 12";
        
        $trending=$DB->read($query);
 
        $result['trending']=$trending;

        $query="SELECT
            s.*,
            CASE 
                WHEN (SELECT 1 FROM visits v WHERE v.series_id = s.id AND v.user_id = $user_id) IS NOT NULL THEN '1'
                ELSE '0'
            END AS visited
            FROM series s
            ORDER BY view DESC Limit 12";
   
        $popular=$DB->read($query);
        $result['popular']=$popular;

        $query="SELECT
            s.*,
            CASE 
                WHEN (SELECT 1 FROM visits v WHERE v.series_id = s.id AND v.user_id = $user_id) IS NOT NULL THEN '1'
                ELSE '0'
            END AS visited
            FROM series s
            ORDER BY id DESC limit 12";
     
        $newadded=$DB->read($query);
        $result['newadded']=$newadded;

        $query="SELECT * FROM owl_carousels JOIN series on series.id=owl_carousels.series_id";
        $owl_carousels=$DB->read($query);
        $result['owl_carousel']=$owl_carousels;

        // Add categories to all series arrays
        if(isset($result['trending'])){
            $result['trending'] = $this->addCategoriesToSeries($result['trending']);
        }
        if(isset($result['popular'])){
            $result['popular'] = $this->addCategoriesToSeries($result['popular']);
        }
        if(isset($result['newadded'])){
            $result['newadded'] = $this->addCategoriesToSeries($result['newadded']);
        }
        if(isset($result['owl_carousel'])){
            $result['owl_carousel'] = $this->addCategoriesToSeries($result['owl_carousel']);
        }

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

        // Add categories to series
        if(isset($result['series'])){
            $result['series'] = $this->addCategoriesToSeries($result['series']);
        }

        return $result;
    }

    public function getPopularSeries($data){
        if(isset($data['user_id'])){
            $user_id = $data['user_id'];
        }else{
            $user_id=0;
        } 

        $page=$data['page'];
        $page--;
        $offset=30;
        $count=$page*$offset;

        $query="SELECT
         s.*,
            CASE 
                WHEN (SELECT 1 FROM visits v WHERE v.series_id = s.id AND v.user_id = $user_id) IS NOT NULL THEN '1'
                ELSE '0'
            END AS visited
        FROM series s ORDER BY view DESC LIMIT $count,$offset";

        $DB=new Database();
        $series=$DB->read($query);
        $result['series']=$series;

        $query="SELECT count(*) as total_series FROM series";
        $count=$DB->read($query);
        $result['total_series']=$count[0]['total_series'];
        
        // Add categories to series
        if(isset($result['series'])){
            $result['series'] = $this->addCategoriesToSeries($result['series']);
        }
        
        return $result;
    }

    public function getTendingSeries($data){
        if(isset($data['user_id'])){
            $user_id = $data['user_id'];
        }else{
            $user_id=0;
        } 

        $page=$data['page'];
        $page--;
        $offset=30;
        $count=$page*$offset;

        $query="SELECT
        s.*,
        CASE 
            WHEN (SELECT 1 FROM visits v WHERE v.series_id = s.id AND v.user_id = $user_id) IS NOT NULL THEN '1'
            ELSE '0'
        END AS visited
        FROM series s ORDER BY view DESC LIMIT $count,$offset";
        $DB=new Database();
        $series=$DB->read($query);
        $result['series']=$series;

        $query="SELECT count(*) as total_series FROM series ";
        $count=$DB->read($query);
        $result['total_series']=$count[0]['total_series'];
        return $result;
    }

    public function getNewSeries($data){

        if(isset($data['user_id'])){
            $user_id = $data['user_id'];
        }else{
            $user_id=0;
        } 

        $page=$data['page'];
        $page--;
        $offset=30;
        $count=$page*$offset;

        $query="SELECT
        s.*,
        CASE 
            WHEN (SELECT 1 FROM visits v WHERE v.series_id = s.id AND v.user_id = $user_id) IS NOT NULL THEN '1'
            ELSE '0'
        END AS visited
        FROM series s ORDER BY id DESC LIMIT $count,$offset";
        $DB=new Database();
        $series=$DB->read($query);
        $result['series']=$series;

        $query="SELECT count(*) as total_series FROM series";
        $count=$DB->read($query);
        $result['total_series']=$count[0]['total_series'];
        
        // Add categories to series
        if(isset($result['series'])){
            $result['series'] = $this->addCategoriesToSeries($result['series']);
        }
        
        return $result;
    }

    public function getSeriesByCategory($data){

        if(isset($data['user_id'])){
            $user_id = $data['user_id'];
        }else{
            $user_id=0;
        } 

        $page=$data['page'];
        if(isset($data['q'])){
             $category_id=$data['q'];
        }else{
            $category_id=$data['category_id'];
        }
       
        $page--;
        $offset=30;
        $count=$page*$offset;

        // Use junction table to find series with this category
        $query="SELECT DISTINCT
        s.*,
        CASE 
            WHEN (SELECT 1 FROM visits v WHERE v.series_id = s.id AND v.user_id = $user_id) IS NOT NULL THEN '1'
            ELSE '0'
        END AS visited
        FROM series s 
        INNER JOIN series_categories sc ON s.id = sc.series_id
        WHERE sc.category_id = $category_id 
        ORDER BY s.id DESC LIMIT $count,$offset";
        $DB=new Database();
        $series=$DB->read($query);
        $result['series']=$series;

        $query="SELECT COUNT(DISTINCT s.id) as total_series 
                FROM series s 
                INNER JOIN series_categories sc ON s.id = sc.series_id 
                WHERE sc.category_id = $category_id";
        $count=$DB->read($query);
        $result['total_series']=$count[0]['total_series'];
        
        // Add categories to series
        if(isset($result['series'])){
            $result['series'] = $this->addCategoriesToSeries($result['series']);
        }
        
        return $result;
    }

    public function getMySeries($user_id){
        $query =" SELECT 
            series.*
        FROM series
        JOIN saves ON series.id=saves.series_id WHERE saves.user_id=$user_id
        ORDER BY saves.date DESC";

        $DB=new Database();
        $series=$DB->read($query);

        $query="SELECT count(*) as total_series FROM saves WHERE user_id=$user_id";
        $total=$DB->read($query);

        $result['total_series']=$total[0]['total_series'];
        $result['series']=$series;
        
        // Add categories to series
        if(isset($result['series'])){
            $result['series'] = $this->addCategoriesToSeries($result['series']);
        }
        
        return $result;
    }

    public function getSeriesYouMayLike($category_id){
        // Use junction table to find series with this category
        $query="SELECT DISTINCT s.* 
                FROM series s 
                INNER JOIN series_categories sc ON s.id = sc.series_id 
                WHERE sc.category_id = $category_id 
                ORDER BY s.view DESC 
                LIMIT 5";
        $DB=new Database();
        $result=$DB->read($query);
        
        // Add categories to series
        if($result){
            $result = $this->addCategoriesToSeries($result);
        }
        
        return $result;
    }

    public function newCommentSeries(){
        $query ="SELECT DISTINCT series.* FROM series
        JOIN comments ON series.id=comments.series_id
        ORDER BY comments.date DESC LIMIT 4
        ";
        $DB=new Database();
        $result=$DB->read($query);
        
        // Add categories to series
        if($result){
            $result = $this->addCategoriesToSeries($result);
        }
        
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

        if($result){
            $series = $result[0];
            // Add categories to series
            $series = $this->addCategoriesToSeries($series);
            return $series;
        }else{
            return false;
        }
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

        if($user['point']<$series['point']) return false;

        $remaining_point=$user['point']-$series['point'];

        $query="INSERT INTO saves (user_id,series_id) VALUE ($user_id,$series_id)";
        $save_count="UPDATE series SET save=save+1 WHERE id=$series_id";
        $user_query="UPDATE users SET point=$remaining_point WHERE id=$user_id";

        $DB->save($query);
        $DB->save($save_count);
        $DB->save($user_query);

        return true;
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

    public function deleteRating($data){
        $user_id=$data['user_id'];
        $series_id=$data['series_id'];
        $DB=new Database();
        $query = "DELETE FROM ratings WHERE user_id=$user_id and series_id=$series_id";
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

    public function search($data){
         if(isset($data['user_id'])){
            $user_id = $data['user_id'];
        }else{
            $user_id=0;
        }

        $searching = $data['search'];

        $DB = new Database();
        $query="SELECT DISTINCT
            s.*,
            CASE 
                WHEN (SELECT 1 FROM visits v WHERE v.series_id = s.id AND v.user_id = $user_id) IS NOT NULL THEN '1'
                ELSE '0'
            END AS visited
            FROM series s
            WHERE s.title LIKE '%$searching%' OR s.description LIKE '%$searching%'  OR s.genre LIKE '%$searching%' 
            ORDER BY s.rating";

        $result = $DB->read($query);
        
        // Add categories to series
        if($result){
            $result = $this->addCategoriesToSeries($result);
        }

        return $result;
    }

}
?>