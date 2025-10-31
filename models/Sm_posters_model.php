<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Sm_posters_model extends App_Model
{
    private $connections_table;
    private $posts_table;
    private $post_platforms_table;

    public function __construct()
    {
        parent::__construct();
        $this->connections_table = db_prefix() . 'social_connections';
        $this->posts_table = db_prefix() . 'social_posts';
        $this->post_platforms_table = db_prefix() . 'post_platforms';
    }

    // ============================================
    // CONNECTION METHODS
    // ============================================

    /**
     * Get all connections
     */
    public function get_all_connections()
    {
        $this->db->select($this->connections_table . '.*, ' . db_prefix() . 'clients.company');
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid = ' . $this->connections_table . '.client_id', 'left');
        $this->db->order_by($this->connections_table . '.platform', 'ASC');
        $this->db->order_by($this->connections_table . '.created_at', 'DESC');
        
        return $this->db->get($this->connections_table)->result();
    }

    /**
     * Get active connections only
     */
    public function get_active_connections()
    {
        $this->db->select($this->connections_table . '.*, ' . db_prefix() . 'clients.company');
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid = ' . $this->connections_table . '.client_id', 'left');
        $this->db->where($this->connections_table . '.status', 1);
        $this->db->order_by($this->connections_table . '.platform', 'ASC');
        
        return $this->db->get($this->connections_table)->result();
    }

    /**
     * Get single connection
     */
    public function get_connection($id)
    {
        $this->db->select($this->connections_table . '.*, ' . db_prefix() . 'clients.company');
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid = ' . $this->connections_table . '.client_id', 'left');
        $this->db->where($this->connections_table . '.id', $id);
        
        return $this->db->get($this->connections_table)->row();
    }

    /**
     * Get connections by platform
     */
    public function get_connections_by_platform($platform)
    {
        $this->db->where('platform', $platform);
        $this->db->where('status', 1);
        return $this->db->get($this->connections_table)->result();
    }

    /**
     * Add connection
     */
    public function add_connection($data)
    {
        $data['created_by'] = get_staff_user_id();
        $data['created_at'] = date('Y-m-d H:i:s');
        
        $this->db->insert($this->connections_table, $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            log_activity('New Social Media Connection Added [Platform: ' . $data['platform'] . ', ID: ' . $insert_id . ']');
            return $insert_id;
        }

        return false;
    }

    /**
     * Update connection
     */
    public function update_connection($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        $this->db->where('id', $id);
        $this->db->update($this->connections_table, $data);

        if ($this->db->affected_rows() > 0) {
            log_activity('Social Media Connection Updated [ID: ' . $id . ']');
            return true;
        }

        return false;
    }

    /**
     * Delete connection
     */
    public function delete_connection($id)
    {
        $this->db->where('id', $id);
        $this->db->delete($this->connections_table);

        if ($this->db->affected_rows() > 0) {
            log_activity('Social Media Connection Deleted [ID: ' . $id . ']');
            return true;
        }

        return false;
    }

    // ============================================
    // POST METHODS
    // ============================================

    /**
     * Get all posts
     */
    public function get_all_posts($limit = null, $offset = 0)
    {
        $this->db->select($this->posts_table . '.*, ' . db_prefix() . 'clients.company');
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid = ' . $this->posts_table . '.client_id', 'left');
        $this->db->order_by($this->posts_table . '.created_at', 'DESC');
        
        if ($limit) {
            $this->db->limit($limit, $offset);
        }
        
        return $this->db->get($this->posts_table)->result();
    }

    /**
     * Get post by ID
     */
    public function get_post($id)
    {
        $this->db->where('id', $id);
        return $this->db->get($this->posts_table)->row();
    }

    /**
     * Add post
     */
    public function add_post($data)
    {
        $data['created_by'] = get_staff_user_id();
        $data['created_at'] = date('Y-m-d H:i:s');
        
        $this->db->insert($this->posts_table, $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            log_activity('New Social Media Post Created [ID: ' . $insert_id . ']');
            return $insert_id;
        }

        return false;
    }

    /**
     * Update post
     */
    public function update_post($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update($this->posts_table, $data);

        return $this->db->affected_rows() > 0;
    }

    /**
     * Delete post
     */
    public function delete_post($id)
    {
        // Delete post platforms first
        $this->db->where('post_id', $id);
        $this->db->delete($this->post_platforms_table);

        // Delete main post
        $this->db->where('id', $id);
        $this->db->delete($this->posts_table);

        if ($this->db->affected_rows() > 0) {
            log_activity('Social Media Post Deleted [ID: ' . $id . ']');
            return true;
        }

        return false;
    }

    // ============================================
    // POST PLATFORM METHODS
    // ============================================

    /**
     * Get platforms for a post
     */
    public function get_post_platforms($post_id)
    {
        $this->db->select($this->post_platforms_table . '.*, ' . $this->connections_table . '.account_name, ' . $this->connections_table . '.platform as platform_name');
        $this->db->join($this->connections_table, $this->connections_table . '.id = ' . $this->post_platforms_table . '.connection_id', 'left');
        $this->db->where($this->post_platforms_table . '.post_id', $post_id);
        
        return $this->db->get($this->post_platforms_table)->result();
    }

    /**
     * Add post platform
     */
    public function add_post_platform($data)
    {
        $this->db->insert($this->post_platforms_table, $data);
        return $this->db->insert_id();
    }

    /**
     * Update post platform
     */
    public function update_post_platform($id, $data)
    {
        $this->db->where('id', $id);
        $this->db->update($this->post_platforms_table, $data);
        
        return $this->db->affected_rows() > 0;
    }

    // ============================================
    // DASHBOARD STATS
    // ============================================

    /**
     * Get dashboard statistics
     */
    public function get_dashboard_stats()
    {
        $stats = [];

        // Total connections
        $stats['total_connections'] = $this->db->count_all_results($this->connections_table);

        // Active connections
        $this->db->where('status', 1);
        $stats['active_connections'] = $this->db->count_all_results($this->connections_table);

        // Connections by platform
        $this->db->select('platform, COUNT(*) as count');
        $this->db->where('status', 1);
        $this->db->group_by('platform');
        $platforms = $this->db->get($this->connections_table)->result();
        
        $stats['platforms'] = [];
        foreach ($platforms as $platform) {
            $stats['platforms'][$platform->platform] = $platform->count;
        }

        // Total posts
        $stats['total_posts'] = $this->db->count_all_results($this->posts_table);

        // Published posts
        $this->db->where('status', 'published');
        $stats['published_posts'] = $this->db->count_all_results($this->posts_table);

        // Scheduled posts
        $this->db->where('status', 'scheduled');
        $this->db->where('scheduled_at >', date('Y-m-d H:i:s'));
        $stats['scheduled_posts'] = $this->db->count_all_results($this->posts_table);

        // Failed posts
        $this->db->where('status', 'failed');
        $stats['failed_posts'] = $this->db->count_all_results($this->posts_table);

        // Posts this month
        $this->db->where('MONTH(created_at)', date('m'));
        $this->db->where('YEAR(created_at)', date('Y'));
        $stats['posts_this_month'] = $this->db->count_all_results($this->posts_table);

        // Recent posts
        $this->db->limit(5);
        $this->db->order_by('created_at', 'DESC');
        $stats['recent_posts'] = $this->db->get($this->posts_table)->result();

        return $stats;
    }

    /**
 * Get scheduled posts that are due
 */
public function get_due_posts()
{
    $this->db->where('is_scheduled', 1);
    $this->db->where('status', 'scheduled');
    $this->db->where('scheduled_at <=', date('Y-m-d H:i:s'));
    $this->db->order_by('scheduled_at', 'ASC');
    
    $posts = $this->db->get($this->posts_table)->result();
    
    return $posts;
}


/**
 * Run scheduled posts cron job
 * Called by Perfex's built-in cron system
 * 
 * @return array Statistics about the cron run
 */
public function run_scheduled_posts_cron()
{
    $CI = &get_instance();
    
    // Load required models
    $CI->load->model('sm_posters/facebook_model');
    $CI->load->model('sm_posters/instagram_model');
    $CI->load->model('sm_posters/x_model');
    $CI->load->model('sm_posters/linkedin_model');
    $CI->load->model('sm_posters/tumblr_model');
    $CI->load->model('sm_posters/pinterest_model');
    
    // Initialize counters
    $scanned = 0;
    $due = 0;
    $success = 0;
    $failed = 0;
    $skipped = 0;
    
    // Get all scheduled posts
    $scheduled_posts = $this->get_due_posts();
    $scanned = count($scheduled_posts);
    
    if (empty($scheduled_posts)) {
        return compact('scanned', 'due', 'success', 'failed', 'skipped');
    }
    
    // Process each scheduled post
    foreach ($scheduled_posts as $post) {
        $due++;
        
        // Update status to publishing
        $this->update_post($post->id, ['status' => 'publishing']);
        
        // Get platforms for this post
        $platforms = $this->get_post_platforms($post->id);
        
        if (empty($platforms)) {
            $skipped++;
            $this->update_post($post->id, ['status' => 'failed']);
            log_message('error', "[SM_POSTERS] Post {$post->id} has no platforms");
            continue;
        }
        
        $post_success_count = 0;
        $post_fail_count = 0;
        
        // Post to each platform
        foreach ($platforms as $platform_record) {
            // Get connection
            $connection = $this->get_connection($platform_record->connection_id);
            
            if (!$connection || $connection->status != 1) {
                $post_fail_count++;
                $this->update_post_platform($platform_record->id, [
                    'status' => 'failed',
                    'error_message' => 'Connection inactive or not found'
                ]);
                continue;
            }
            
            // Create temp file if media exists
            $media_path = null;
            if (!empty($post->media_data)) {
                $media_path = $this->_create_temp_file_from_base64(
                    $post->media_data,
                    $post->media_mime,
                    $post->media_filename
                );
            }
            
            // Post to platform
            $result = $this->_post_to_platform(
                $platform_record->platform,
                $connection,
                $post->message,
                $post->link,
                $media_path,
                $CI
            );
            
            // Delete temp file
            if ($media_path && file_exists($media_path)) {
                unlink($media_path);
            }
            
            // Update platform record
            $this->update_post_platform($platform_record->id, [
                'platform_post_id' => $result['success'] ? $result['post_id'] : null,
                'status' => $result['success'] ? 'published' : 'failed',
                'error_message' => !$result['success'] ? $result['error'] : null,
                'published_at' => $result['success'] ? date('Y-m-d H:i:s') : null
            ]);
            
            if ($result['success']) {
                $post_success_count++;
                log_message('info', sprintf(
                    '[SM_POSTERS] Post=%d Platform=%s Success PostID=%s',
                    $post->id,
                    $platform_record->platform,
                    $result['post_id']
                ));
            } else {
                $post_fail_count++;
                log_message('error', sprintf(
                    '[SM_POSTERS] Post=%d Platform=%s Failed Error=%s',
                    $post->id,
                    $platform_record->platform,
                    $result['error']
                ));
            }
        }
        
        // Update main post status
        $final_status = ($post_fail_count > 0 && $post_success_count == 0) ? 'failed' : 'published';
        $this->update_post($post->id, [
            'status' => $final_status,
            'published_at' => date('Y-m-d H:i:s')
        ]);
        
        if ($post_success_count > 0) {
            $success++;
        } else {
            $failed++;
        }
        
        // Log detailed activity
        log_activity(sprintf(
            'Scheduled post published [Post ID: %d, Success: %d, Failed: %d]',
            $post->id,
            $post_success_count,
            $post_fail_count
        ));
    }
    
    // Update last run time
    $CI->db->where('name', 'sm_posters_cron_last_run');
    $CI->db->update(db_prefix() . 'options', ['value' => date('Y-m-d H:i:s')]);
    
    return compact('scanned', 'due', 'success', 'failed', 'skipped');
}

/**
 * Create temporary file from base64 data
 */
private function _create_temp_file_from_base64($base64_data, $mime_type, $filename)
{
    $binary_data = base64_decode($base64_data);
    
    $temp_dir = FCPATH . 'uploads/temp/';
    if (!is_dir($temp_dir)) {
        mkdir($temp_dir, 0755, true);
    }
    
    $extension = pathinfo($filename, PATHINFO_EXTENSION);
    $temp_filename = 'cron_temp_' . uniqid() . '_' . time() . '.' . $extension;
    $temp_path = $temp_dir . $temp_filename;
    
    file_put_contents($temp_path, $binary_data);
    
    return $temp_path;
}

/**
 * Post to specific platform
 */
private function _post_to_platform($platform, $connection, $message, $link = null, $media_path = null, $CI)
{
    try {
        switch ($platform) {
            case 'facebook':
                return $CI->facebook_model->_post_to_facebook($connection, $message, $link, $media_path);
            
            case 'instagram':
                return $CI->instagram_model->post_to_instagram($connection, $message, $media_path);
            
            case 'x':
                return $CI->x_model->post_to_x($connection, $message, $media_path);
            
            case 'linkedin':
                return $CI->linkedin_model->post_to_linkedin($connection, $message, $link, $media_path);
            
            case 'tumblr':
                return $CI->tumblr_model->post_to_tumblr($connection, $message, $media_path);
            
            case 'pinterest':
                return $CI->pinterest_model->post_to_pinterest($connection, $message, $link, $media_path);
            
            default:
                return [
                    'success' => false,
                    'error' => 'Unknown platform: ' . $platform
                ];
        }
    } catch (Exception $e) {
        return [
            'success' => false,
            'error' => 'Exception: ' . $e->getMessage()
        ];
    }
}
}