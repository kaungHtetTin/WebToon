<?php
Class Util{
    public function formatCount($count){
        if($count<10000){
            return $count;
        }if($count>=10000 && $count<1000000){
            $number=$count/1000;
            return number_format((float)$number, 2, '.', '')."k";
        }else{
            $number=$count/1000000;
            return number_format((float)$number, 2, '.', '')."M";
        }
    }

    public function mobileAppVersionCode(){
        return 1;
    }

    public function normalizeImageUrl($image_url){
        if(empty($image_url)){
            $image_url = '/uploads/placeholder.jpg';
        }
        
        // If already a full URL (starts with http:// or https://), return as is
        if(strpos($image_url, 'http://') === 0 || strpos($image_url, 'https://') === 0){
            return $image_url;
        }
        
        // Get the base URL from server information
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443) ? 'https://' : 'http://';
        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : (isset($_SERVER['SERVER_NAME']) ? $_SERVER['SERVER_NAME'] : 'localhost');
        
        // Handle old paths like "img/series/filename.jpg" (without leading /)
        if(strpos($image_url, 'img/') === 0 && strpos($image_url, '/') !== 0){
            $image_url = '/' . $image_url;
        }
        
        // Handle old paths like "../img/series/filename.jpg"
        if(strpos($image_url, '../img/') === 0){
            $image_url = str_replace('../img/', '/img/', $image_url);
        }
        
        // If it's just a filename (no path separators), assume it's in the old location
        if(strpos($image_url, '/') !== 0 && strpos($image_url, '\\') === false && strpos($image_url, 'http') !== 0){
            $image_url = '/img/series/' . $image_url;
        }
        
        // Ensure it starts with /
        $image_url = '/' . ltrim($image_url, '/');
        
        // Construct the full URL: protocol + host + path
        $full_url = $protocol . $host."/webtoon" . $image_url;
        
        return $full_url;
    }
}
?>