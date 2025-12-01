<?php
/**
 * Image Resource Helper
 * 
 * This file provides a convenient wrapper function for getting image resource paths
 * throughout the admin panel. It uses the Util class from classes/util.php
 * 
 * Usage:
 *   require_once('includes/image_helper.php');
 *   $image_path = getImagePath($image_url, 'series');
 * 
 * @param string $image_url The image URL from database
 * @param string $type The type of image (series, blogs, admin, blog_feeds, screenshots, etc.)
 * @param string $base_path Base path for relative resolution (default: '../' for admin panel)
 * @return string The resolved image path
 */
if (!function_exists('getImagePath')) {
    function getImagePath($image_url, $type = 'series', $base_path = '../') {
        // Ensure Util class is loaded
        if (!class_exists('Util')) {
            require_once(__DIR__ . '/../../classes/util.php');
        }
        
        $util = new Util();
        return $util->getImageResourcePath($image_url, $type, $base_path);
    }
}

