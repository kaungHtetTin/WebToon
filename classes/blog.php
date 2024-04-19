<?php 
Class Blog{
    public function get($data){

        $page=$data['page'];
        $page--;
        $offset=18;
        $count=$page*$offset;


        $query="SELECT * FROM blogs ORDER BY id DESC LIMIT $count,$offset";
        $DB=new Database();
        $blogs=$DB->read($query);
        $result['blogs']=$blogs;

        $query ="SELECT count(*) as total FROM blogs";
        $total_blog=$DB->read($query);

        $result['total_blog']=$total_blog[0]['total'];
        return $result;
    }

    public function getBlogDetail($data){
        $blog_id=$data['id'];

        $query="SELECT * FROM blogs WHERE id=$blog_id limit 1";
        $DB=new Database();
        $detail=$DB->read($query);

        $result['blog']=$detail[0];

        $query="SELECT * FROM blog_feeds WHERE blog_id=$blog_id";
        $feeds=$DB->read($query);

        $result['feeds']=$feeds;

        return $result;

    }
}

?>