<?php
    Class Comment{
        public function create($data){
            $user_id=$data['user_id'];
            $series_id=$data['series_id'];
            $body=$data['body'];

            $query="INSERT INTO comments (user_id,series_id,body) 
                    VALUES($user_id,$series_id,'$body')";
            $DB=new Database();
            $DB->save($query);

            $query="UPDATE series SET comment=comment+1 WHERE id=$series_id";
            $DB->save($query);

        }

        public function get($series_id,$page){
            $page--;
            $offset=30;
            $count=$page*$offset;

            $query="SELECT 
            users.first_name,
            users.last_name,
            users.image_url,
            comments.id,
            comments.date,
            comments.body
            FROM comments
            JOIN users
            ON users.id=comments.user_id
            WHERE series_id=$series_id
            ORDER BY comments.id DESC
            LIMIT $count,$offset
            ";

            $DB=new Database();
            $result=$DB->read($query);
            

            return $result;

        }
    }
?>