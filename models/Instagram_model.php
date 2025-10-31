<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Instagram_model extends App_Model
{
    public function post_to_instagram($connection, $message, $media_path = null)
    {
        try {
            // Instagram requires media
            if (empty($media_path) || !file_exists($media_path)) {
                return ['success' => false, 'error' => 'Instagram requires an image or video'];
            }

            // Determine if it's image or video
            $mime_type = mime_content_type($media_path);
            $is_video = strpos($mime_type, 'video') !== false;

            // Step 1: Create media container
            $container_url = 'https://graph.facebook.com/v18.0/' . $connection->account_id . '/media';
            
            $media_data = new CURLFile($media_path, $mime_type, basename($media_path));
            
            $container_data = [
                'caption' => $message,
                'access_token' => $connection->access_token
            ];

            if ($is_video) {
                $container_data['media_type'] = 'VIDEO';
                $container_data['video_url'] = $media_path; // Note: May need public URL
            } else {
                $container_data['image_url'] = $media_path; // Note: May need public URL
            }

            // For file upload, use different approach
            // Note: Instagram Graph API requires public URLs, not file uploads
            // You may need to upload to your server first and provide public URL

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $container_url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($container_data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            $result = json_decode($response, true);

            if ($http_code != 200 || !isset($result['id'])) {
                $error = isset($result['error']['message']) ? $result['error']['message'] : 'Failed to create media container';
                return ['success' => false, 'error' => $error];
            }

            $creation_id = $result['id'];

            // Step 2: Publish the container
            $publish_url = 'https://graph.facebook.com/v18.0/' . $connection->account_id . '/media_publish';
            
            $publish_data = [
                'creation_id' => $creation_id,
                'access_token' => $connection->access_token
            ];

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $publish_url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($publish_data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            $result = json_decode($response, true);

            if ($http_code == 200 && isset($result['id'])) {
                return ['success' => true, 'post_id' => $result['id']];
            } else {
                $error = isset($result['error']['message']) ? $result['error']['message'] : 'Failed to publish';
                return ['success' => false, 'error' => $error];
            }

        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}