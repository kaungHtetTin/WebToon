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

    /**
     * Determine the correct image resource path for admin panel use
     * 
     * @param string $image_url The image URL from database (can be full path, relative path, or filename)
     * @param string $type The type of image resource (series, blogs, admin, blog_feeds, screenshots, etc.)
     * @param string $base_path Base path for relative resolution (default: '../' for admin panel)
     * @return string The resolved image path relative to the calling file
     */
    public function getImageResourcePath($image_url, $type = 'series', $base_path = '../') {
        // Default placeholder path
        $placeholder = $base_path . 'img/placeholder.jpg';
        
        // If image_url is empty, return placeholder
        if(empty($image_url)) {
            return $placeholder;
        }
        
        // Define image type directories mapping
        $type_directories = [
            'series' => ['uploads/images/series/', 'img/trending/'],
            'blogs' => ['uploads/images/blogs/', 'img/blog/'],
            'admin' => ['uploads/images/admin/', 'img/profile/'],
            'blog_feeds' => ['uploads/images/blog_feeds/'],
            'screenshots' => ['uploads/images/screenshots/', 'img/screenshot_url/'],
            'owl_carousels' => ['img/owl_carousels/'],
            'trending' => ['img/trending/'],
            'profile' => ['img/profile/', 'uploads/images/admin/'],
        ];
        
        // Get possible directories for this type
        $possible_dirs = isset($type_directories[$type]) ? $type_directories[$type] : ['uploads/images/' . $type . '/'];
        
        // Case 1: Full path starting with /uploads/
        if(strpos($image_url, '/uploads/') === 0) {
            $image_path = $base_path . ltrim($image_url, '/');
            if(file_exists($image_path)) {
                return $image_path;
            }
        }
        
        // Case 2: Path starting with uploads/ (without leading slash)
        if(strpos($image_url, 'uploads/') === 0) {
            $image_path = $base_path . $image_url;
            if(file_exists($image_path)) {
                return $image_path;
            }
        }
        
        // Case 3: Absolute path starting with /
        if(strpos($image_url, '/') === 0 && strpos($image_url, 'http') !== 0) {
            $image_path = $base_path . ltrim($image_url, '/');
            if(file_exists($image_path)) {
                return $image_path;
            }
        }
        
        // Case 4: Relative path starting with ../
        if(strpos($image_url, '../') === 0) {
            // Already relative, check if file exists
            if(file_exists($image_url)) {
                return $image_url;
            }
        }
        
        // Case 5: Just filename - try multiple possible locations
        if(strpos($image_url, '/') === false && strpos($image_url, '\\') === false && strpos($image_url, 'http') !== 0) {
            // Try each possible directory for this type
            foreach($possible_dirs as $dir) {
                $image_path = $base_path . $dir . $image_url;
                if(file_exists($image_path)) {
                    return $image_path;
                }
            }
        }
        
        // Case 6: Path with img/ prefix
        if(strpos($image_url, 'img/') === 0) {
            $image_path = $base_path . $image_url;
            if(file_exists($image_path)) {
                return $image_path;
            }
        }
        
        // If nothing found, return placeholder
        return $placeholder;
    }
}
?>