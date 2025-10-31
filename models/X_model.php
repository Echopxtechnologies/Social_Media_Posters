<?php
defined('BASEPATH') or exit('No direct script access allowed');

class X_model extends App_Model
{
    public function post_to_x($connection, $message, $media_path = null)
    {
        try {
            // Twitter/X API v2
            $url = 'https://api.twitter.com/2/tweets';

            // Truncate message to 280 characters
            if (strlen($message) > 280) {
                $message = substr($message, 0, 277) . '...';
            }

            $post_data = ['text' => $message];

            // Upload media first if exists
            if (!empty($media_path) && file_exists($media_path)) {
                $media_id = $this->_upload_media_to_x($connection, $media_path);
                if ($media_id) {
                    $post_data['media'] = ['media_ids' => [$media_id]];
                }
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($post_data));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                'Authorization: Bearer ' . $connection->access_token,
                'Content-Type: application/json'
            ]);
            
            $response = curl_exec($ch);
            $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            $result = json_decode($response, true);

            if ($http_code == 201 && isset($result['data']['id'])) {
                return ['success' => true, 'post_id' => $result['data']['id']];
            } else {
                $error = isset($result['errors'][0]['message']) ? $result['errors'][0]['message'] : 'Unknown error';
                return ['success' => false, 'error' => $error];
            }

        } catch (Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    private function _upload_media_to_x($connection, $media_path)
    {
        // Twitter media upload endpoint
        // Implementation needed for media upload
        return null;
    }
}