<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Facebook_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Post to Facebook - handles text and image posts
     * 
     * @param object $connection - Facebook connection details
     * @param string $message - Post message/caption
     * @param string|null $link - Optional link (for text-only posts)
     * @param string|null $image - Optional path to image file
     * @return array - ['success' => bool, 'post_id' => string, 'error' => string]
     */
    public function _post_to_facebook($connection, $message, $link = null, $image = null)
    {
        try {
            // ============================================
            // CHECK IF IMAGE IS PROVIDED
            // ============================================
            $has_image = !empty($image) && file_exists($image);

            // ============================================
            // TEXT ONLY POST (no image)
            // ============================================
            if (!$has_image) {
                $url = 'https://graph.facebook.com/v18.0/' . $connection->page_id . '/feed';
                
                $post_data = [
                    'message' => $message,
                    'access_token' => $connection->access_token
                ];
                
                // Add link if provided
                if (!empty($link)) {
                    $post_data['link'] = $link;
                }
                
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post_data));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                
                $response = curl_exec($ch);
                $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
                curl_close($ch);
                
                $result = json_decode($response, true);
                
                if ($http_code == 200 && isset($result['id'])) {
                    return [
                        'success' => true,
                        'post_id' => $result['id'],
                        'type' => 'text'
                    ];
                } else {
                    $error = isset($result['error']['message']) ? $result['error']['message'] : 'Unknown error';
                    return [
                        'success' => false,
                        'error' => $error
                    ];
                }
            }

            // ============================================
            // IMAGE + TEXT POST
            // ============================================
            $url = 'https://graph.facebook.com/v18.0/' . $connection->page_id . '/photos';
            
            // Get mime type
            $mime_type = mime_content_type($image);
            
            // Create CURLFile for image upload
            $image_data = new CURLFile($image, $mime_type, basename($image));
            
            // Prepare post data (multipart form data)
            $post_data = [
                'message' => $message,
                'access_token' => $connection->access_token,
                'source' => $image_data
            ];
            
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data); // Don't use http_build_query for files!
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_TIMEOUT, 120); // 2 minutes timeout
            
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            
            // Check for curl errors
            if (curl_errno($ch)) {
                $error = curl_error($ch);
                curl_close($ch);
                return [
                    'success' => false,
                    'error' => 'Upload error: ' . $error
                ];
            }
            
            curl_close($ch);
            
            $result = json_decode($response, true);
            
            if ($http_code == 200 && isset($result['id'])) {
                return [
                    'success' => true,
                    'post_id' => $result['id'],
                    'type' => 'image'
                ];
            } else {
                $error = isset($result['error']['message']) ? $result['error']['message'] : 'Unknown error';
                return [
                    'success' => false,
                    'error' => $error
                ];
            }
            
        } catch (Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}