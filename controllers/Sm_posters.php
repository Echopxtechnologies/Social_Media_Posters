<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Sm_posters extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        
        $this->load->model('sm_posters_model');
        $this->load->model('facebook_model');
        $this->load->model('instagram_model');
        $this->load->model('x_model');
        $this->load->model('linkedin_model');
        $this->load->model('tumblr_model');
        $this->load->model('pinterest_model');
        $this->load->library('form_validation');
        $this->load->model('clients_model');

        $this->load->helper('text');
        
        if (!has_permission('sm_posters', '', 'view')) {
            access_denied('sm_posters');
        }
    }
    
    /**
     * Dashboard
     */
    public function index()
    {
        $data['title'] = 'Social Media Manager';
        $data['connections'] = $this->sm_posters_model->get_all_connections();
        $data['stats'] = $this->sm_posters_model->get_dashboard_stats();
        
        $this->load->view('sm_posters/dashboard', $data);
    }
    
    /**
     * Manage Connections
     */
    public function connections()
    {
        $data['title'] = 'Social Media Connections';
        $data['connections'] = $this->sm_posters_model->get_all_connections();
        
        $this->load->view('sm_posters/connections', $data);
    }
    
    /**
     * Add Connection
     */
    public function add_connection()
    {
        if (!has_permission('sm_posters', '', 'create')) {
            access_denied('sm_posters');
        }

        if ($this->input->post()) {
            $this->form_validation->set_rules('client_id', 'Client', 'required');
            $this->form_validation->set_rules('platform', 'Platform', 'required');
            $this->form_validation->set_rules('account_id', 'Account ID', 'required');
            $this->form_validation->set_rules('access_token', 'Access Token', 'required');

            if ($this->form_validation->run() == TRUE) {
                $data = [
                    'client_id' => $this->input->post('client_id'),
                    'platform' => $this->input->post('platform'),
                    'account_name' => $this->input->post('account_name'),
                    'account_id' => $this->input->post('account_id'),
                    'access_token' => $this->input->post('access_token'),
                    'refresh_token' => $this->input->post('refresh_token'),
                    'status' => $this->input->post('status') ? 1 : 0,
                ];

                $insert_id = $this->sm_posters_model->add_connection($data);

                if ($insert_id) {
                    set_alert('success', 'Connection added successfully');
                    redirect(admin_url('sm_posters/connections'));
                } else {
                    set_alert('danger', 'Failed to add connection');
                }
            }
        }

        $data['title'] = 'Add Social Media Connection';
        $data['clients'] = $this->clients_model->get();
        $data['connection'] = null;
        
        $this->load->view('sm_posters/add_edit_connection', $data);
    }
    
    /**
     * Edit Connection
     */
    public function edit_connection($id)
    {
        if (!has_permission('sm_posters', '', 'edit')) {
            access_denied('sm_posters');
        }

        $connection = $this->sm_posters_model->get_connection($id);

        if (!$connection) {
            show_404();
        }

        if ($this->input->post()) {
            $this->form_validation->set_rules('client_id', 'Client', 'required');
            $this->form_validation->set_rules('platform', 'Platform', 'required');
            $this->form_validation->set_rules('account_id', 'Account ID', 'required');
            $this->form_validation->set_rules('access_token', 'Access Token', 'required');

            if ($this->form_validation->run() == TRUE) {
                $data = [
                    'client_id' => $this->input->post('client_id'),
                    'platform' => $this->input->post('platform'),
                    'account_name' => $this->input->post('account_name'),
                    'account_id' => $this->input->post('account_id'),
                    'access_token' => $this->input->post('access_token'),
                    'refresh_token' => $this->input->post('refresh_token'),
                    'status' => $this->input->post('status') ? 1 : 0,
                ];

                $result = $this->sm_posters_model->update_connection($id, $data);

                if ($result) {
                    set_alert('success', 'Connection updated successfully');
                    redirect(admin_url('sm_posters/connections'));
                } else {
                    set_alert('danger', 'Failed to update connection');
                }
            }
        }

        $data['title'] = 'Edit Connection';
        $data['clients'] = $this->clients_model->get();
        $data['connection'] = $connection;
        
        $this->load->view('sm_posters/add_edit_connection', $data);
    }
    
    /**
     * Delete Connection
     */
    public function delete_connection($id)
    {
        if (!has_permission('sm_posters', '', 'delete')) {
            access_denied('sm_posters');
        }

        $response = $this->sm_posters_model->delete_connection($id);

        if ($response) {
            set_alert('success', 'Connection deleted successfully');
        } else {
            set_alert('danger', 'Failed to delete connection');
        }

        redirect(admin_url('sm_posters/connections'));
    }
    
    /**
     * Toggle Connection Status
     */
    public function toggle_connection($id)
    {
        if (!has_permission('sm_posters', '', 'edit')) {
            access_denied('sm_posters');
        }

        $connection = $this->sm_posters_model->get_connection($id);

        if ($connection) {
            $new_status = $connection->status == 1 ? 0 : 1;
            $this->sm_posters_model->update_connection($id, ['status' => $new_status]);
            set_alert('success', 'Status updated successfully');
        }

        redirect(admin_url('sm_posters/connections'));
    }

    /**
     * Create Post
     */

public function create_post()
{
    if (!has_permission('sm_posters', '', 'create')) {
        access_denied('sm_posters');
    }

    if ($this->input->post()) {
        $this->form_validation->set_rules('message', 'Message', 'required');
        $this->form_validation->set_rules('platforms[]', 'Platforms', 'required');

        if ($this->form_validation->run() == TRUE) {
            $message = $this->input->post('message');
            $link = $this->input->post('link');
            $platforms = $this->input->post('platforms');
            $connections = $this->input->post('connections');
            $schedule_type = $this->input->post('schedule_type');
            
            $media_base64 = $this->input->post('media_base64');
            $media_type = $this->input->post('media_type') ? $this->input->post('media_type') : 'none';
            $media_mime = $this->input->post('media_mime');
            $media_filename = $this->input->post('media_filename');

            // Handle scheduling
            $is_scheduled = ($schedule_type == 'schedule');
            $scheduled_at = null;

            if ($is_scheduled) {
                $scheduled_date = $this->input->post('scheduled_date');
                $scheduled_time = $this->input->post('scheduled_time');
                
                // FIX: Validate date format
                if (empty($scheduled_date) || empty($scheduled_time)) {
                    set_alert('danger', 'Please provide both date and time for scheduling');
                    redirect(admin_url('sm_posters/create_post'));
                    return;
                }
                
                // FIX: Ensure proper datetime format
                $scheduled_at = $scheduled_date . ' ' . $scheduled_time . ':00';
                
                // Validate the datetime
                $timestamp = strtotime($scheduled_at);
                if ($timestamp === false) {
                    set_alert('danger', 'Invalid date/time format');
                    redirect(admin_url('sm_posters/create_post'));
                    return;
                }
                
                // FIX: Convert to MySQL datetime format
                $scheduled_at = date('Y-m-d H:i:s', $timestamp);
            }

            // Get client_id from first connection
            $client_id = 0;
            if (!empty($connections)) {
                $first_connection_id = reset($connections);
                $first_conn = $this->sm_posters_model->get_connection($first_connection_id);
                $client_id = $first_conn ? $first_conn->client_id : 0;
            }

            // Create main post record
            $post_data = [
                'client_id' => $client_id, // Fixed: Get from connection
                'message' => $message,
                'link' => $link,
                'media_type' => $media_type,
                'media_data' => !empty($media_base64) ? $media_base64 : null,
                'media_mime' => $media_mime,
                'media_filename' => $media_filename,
                'scheduled_at' => $scheduled_at,
                'is_scheduled' => $is_scheduled ? 1 : 0,
                'status' => $is_scheduled ? 'scheduled' : 'publishing'
            ];
            
            $post_id = $this->sm_posters_model->add_post($post_data);

            if (!$post_id) {
                set_alert('danger', 'Failed to create post');
                redirect(admin_url('sm_posters/create_post'));
            }

            // If scheduled, just save and exit
            if ($is_scheduled) {
                // Save platform connections
                foreach ($platforms as $platform) {
                    if (isset($connections[$platform])) {
                        $this->sm_posters_model->add_post_platform([
                            'post_id' => $post_id,
                            'connection_id' => $connections[$platform],
                            'platform' => $platform,
                            'status' => 'pending'
                        ]);
                    }
                }
                
                set_alert('success', 'Post scheduled successfully for ' . date('M d, Y h:i A', strtotime($scheduled_at)));
                redirect(admin_url('sm_posters/posts'));
            }

            // Post immediately to all selected platforms
            $results = [];
            $success_platforms = [];
            $failed_platforms = [];
            
            foreach ($platforms as $platform) {
                if (!isset($connections[$platform])) {
                    continue;
                }
                
                $connection_id = $connections[$platform];
                $connection = $this->sm_posters_model->get_connection($connection_id);
                
                if (!$connection || $connection->status != 1) {
                    $failed_platforms[] = ucfirst($platform) . ' (inactive)';
                    continue;
                }

                // Create temp file if media exists
                $media_path = null;
                if (!empty($media_base64)) {
                    $media_path = $this->_create_temp_file($media_base64, $media_mime, $media_filename);
                }

                // Post to platform
                $result = $this->_post_to_platform($platform, $connection, $message, $link, $media_path);

                // Delete temp file
                if ($media_path && file_exists($media_path)) {
                    unlink($media_path);
                }

                // Save platform post record
                $this->sm_posters_model->add_post_platform([
                    'post_id' => $post_id,
                    'connection_id' => $connection_id,
                    'platform' => $platform,
                    'platform_post_id' => $result['success'] ? $result['post_id'] : null,
                    'status' => $result['success'] ? 'published' : 'failed',
                    'error_message' => !$result['success'] ? $result['error'] : null,
                    'published_at' => $result['success'] ? date('Y-m-d H:i:s') : null
                ]);

                $results[$platform] = $result;
                
                if ($result['success']) {
                    $success_platforms[] = ucfirst($platform);
                } else {
                    $failed_platforms[] = ucfirst($platform) . ': ' . $result['error'];
                }
            }

            // Update main post status
            $all_failed = !empty($results) && count(array_filter($results, function($r) { return $r['success']; })) == 0;
            $all_success = !empty($results) && count(array_filter($results, function($r) { return !$r['success']; })) == 0;
            
            $this->sm_posters_model->update_post($post_id, [
                'status' => $all_failed ? 'failed' : ($all_success ? 'published' : 'published'),
                'published_at' => date('Y-m-d H:i:s')
            ]);

            // Show detailed results
            if (!empty($success_platforms)) {
                set_alert('success', 'Posted successfully to: ' . implode(', ', $success_platforms));
                log_activity('Posted to ' . count($success_platforms) . ' social media platform(s)');
            }
            
            if (!empty($failed_platforms)) {
                set_alert('warning', 'Failed to post to:<br>' . implode('<br>', $failed_platforms));
            }
            
            redirect(admin_url('sm_posters/posts'));
        }
    }

    $data['title'] = 'Create Social Media Post';
    $data['connections'] = $this->sm_posters_model->get_active_connections();
    
    $this->load->view('sm_posters/create_post', $data);
}

    /**
     * Route to correct platform model
     */
    private function _post_to_platform($platform, $connection, $message, $link = null, $media_path = null)
    {
        switch ($platform) {
            case 'facebook':
                return $this->facebook_model->_post_to_facebook($connection, $message, $link, $media_path);
            
            case 'instagram':
                return $this->instagram_model->post_to_instagram($connection, $message, $media_path);
            
            case 'x':
                return $this->x_model->post_to_x($connection, $message, $media_path);
            
            case 'linkedin':
                return $this->linkedin_model->post_to_linkedin($connection, $message, $link, $media_path);
            
            case 'tumblr':
                return $this->tumblr_model->post_to_tumblr($connection, $message, $media_path);
            
            case 'pinterest':
                return $this->pinterest_model->post_to_pinterest($connection, $message, $link, $media_path);
            
            default:
                return ['success' => false, 'error' => 'Unknown platform'];
        }
    }

    /**
     * Create temporary file from base64
     */
    private function _create_temp_file($base64_data, $mime_type, $filename)
    {
        $binary_data = base64_decode($base64_data);
        
        $temp_dir = FCPATH . 'uploads/temp/';
        if (!is_dir($temp_dir)) {
            mkdir($temp_dir, 0755, true);
        }
        
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $temp_filename = 'temp_' . uniqid() . '_' . time() . '.' . $extension;
        $temp_path = $temp_dir . $temp_filename;
        
        file_put_contents($temp_path, $binary_data);
        
        return $temp_path;
    }

    /**
     * View Posts
     */
    public function posts()
    {
        $data['title'] = 'Posts History';
        $data['posts'] = $this->sm_posters_model->get_all_posts();
        
        $this->load->view('sm_posters/posts', $data);
    }

    /**
     * Process scheduled posts (called by cron via web)
     */
    public function process_scheduled()
    {
        // Security: Check secret key
        $secret = $this->input->get('secret');
        $expected_secret = 'sm_posters_cron_' . md5(APP_ENCRYPTION_KEY . 'sm_posters');
        
        if ($secret !== $expected_secret) {
            show_404();
            return;
        }

        // Prevent timeout
        set_time_limit(0);
        
        echo "<pre>";
        echo "========================================\n";
        echo "Social Media Posters - Cron Job\n";
        echo "Started at: " . date('Y-m-d H:i:s') . "\n";
        echo "========================================\n\n";

        // Load required models
        $this->load->model('sm_posters_model');
        $this->load->model('facebook_model');
        $this->load->model('instagram_model');
        $this->load->model('x_model');
        $this->load->model('linkedin_model');
        $this->load->model('tumblr_model');
        $this->load->model('pinterest_model');

        // Get scheduled posts that are due
        $scheduled_posts = $this->sm_posters_model->get_due_posts();

        echo "Found " . count($scheduled_posts) . " scheduled posts to process\n\n";

        if (empty($scheduled_posts)) {
            echo "No posts to process.\n";
            
            // Update last run time
            $this->db->where('name', 'sm_posters_cron_last_run');
            $this->db->update(db_prefix() . 'options', ['value' => date('Y-m-d H:i:s')]);
            
            echo "\n========================================\n";
            echo "Cron job completed at: " . date('Y-m-d H:i:s') . "\n";
            echo "========================================\n";
            echo "</pre>";
            return;
        }

        // Process each scheduled post
        foreach ($scheduled_posts as $post) {
            echo "Processing Post ID: {$post->id}\n";
            echo "Message: " . substr($post->message, 0, 50) . "...\n";
            echo "Scheduled for: {$post->scheduled_at}\n";
            
            // Update status to publishing
            $this->sm_posters_model->update_post($post->id, ['status' => 'publishing']);
            
            // Get platforms for this post
            $platforms = $this->sm_posters_model->get_post_platforms($post->id);
            
            echo "Posting to " . count($platforms) . " platform(s)\n";
            
            $success_count = 0;
            $fail_count = 0;
            
            foreach ($platforms as $platform_record) {
                echo "  - " . ucfirst($platform_record->platform) . ": ";
                
                // Get connection
                $connection = $this->sm_posters_model->get_connection($platform_record->connection_id);
                
                if (!$connection || $connection->status != 1) {
                    echo "SKIPPED (connection inactive)\n";
                    continue;
                }
                
                // Create temp file if media exists
                $media_path = null;
                if (!empty($post->media_data)) {
                    $media_path = $this->_create_temp_file($post->media_data, $post->media_mime, $post->media_filename);
                }
                
                // Post to platform
                $result = $this->_post_to_platform($platform_record->platform, $connection, $post->message, $post->link, $media_path);
                
                // Delete temp file
                if ($media_path && file_exists($media_path)) {
                    unlink($media_path);
                }
                
                // Update platform record
                $this->sm_posters_model->update_post_platform($platform_record->id, [
                    'platform_post_id' => $result['success'] ? $result['post_id'] : null,
                    'status' => $result['success'] ? 'published' : 'failed',
                    'error_message' => !$result['success'] ? $result['error'] : null,
                    'published_at' => $result['success'] ? date('Y-m-d H:i:s') : null
                ]);
                
                if ($result['success']) {
                    echo "SUCCESS";
                    if (isset($result['post_id'])) {
                        echo " (Post ID: {$result['post_id']})";
                    }
                    echo "\n";
                    $success_count++;
                } else {
                    echo "FAILED ({$result['error']})\n";
                    $fail_count++;
                }
            }
            
            // Update main post status
            $final_status = ($fail_count > 0 && $success_count == 0) ? 'failed' : 'published';
            $this->sm_posters_model->update_post($post->id, [
                'status' => $final_status,
                'published_at' => date('Y-m-d H:i:s')
            ]);
            
            echo "Post completed: {$success_count} success, {$fail_count} failed\n";
            echo "Final status: {$final_status}\n\n";
            
            // Log activity
            log_activity('Scheduled post published [Post ID: ' . $post->id . ', Success: ' . $success_count . ', Failed: ' . $fail_count . ']');
        }

        // Update last run time
        $this->db->where('name', 'sm_posters_cron_last_run');
        $this->db->update(db_prefix() . 'options', ['value' => date('Y-m-d H:i:s')]);

        echo "\n========================================\n";
        echo "Cron job completed at: " . date('Y-m-d H:i:s') . "\n";
        echo "========================================\n";
        echo "</pre>";
    }

    /**
 * Delete Post
 */
public function delete_post($id)
{
    if (!has_permission('sm_posters', '', 'delete')) {
        access_denied('sm_posters');
    }

    $response = $this->sm_posters_model->delete_post($id);

    if ($response) {
        set_alert('success', 'Post deleted successfully');
    } else {
        set_alert('danger', 'Failed to delete post');
    }

    redirect(admin_url('sm_posters/posts'));
}

/**
 * Get post details (AJAX)
 */
public function get_post_details($id)
{
    $post = $this->sm_posters_model->get_post($id);
    
    if (!$post) {
        echo '<div class="alert alert-danger">Post not found</div>';
        return;
    }

    $platforms = $this->sm_posters_model->get_post_platforms($id);
    
    ?>
    <div class="row">
        <div class="col-md-12">
            <h4>Post Message</h4>
            <div class="well">
                <?php echo nl2br(htmlspecialchars($post->message)); ?>
            </div>
        </div>
    </div>

    <?php if ($post->link) { ?>
    <div class="row">
        <div class="col-md-12">
            <h4>Link</h4>
            <p><a href="<?php echo $post->link; ?>" target="_blank"><?php echo $post->link; ?></a></p>
        </div>
    </div>
    <?php } ?>

    <?php if ($post->media_type != 'none') { ?>
    <div class="row">
        <div class="col-md-12">
            <h4>Media</h4>
            <p>
                <i class="fa fa-<?php echo $post->media_type == 'image' ? 'image' : 'video-camera'; ?>"></i>
                <?php echo ucfirst($post->media_type); ?>: <?php echo $post->media_filename; ?>
            </p>
        </div>
    </div>
    <?php } ?>

    <div class="row">
        <div class="col-md-12">
            <h4>Platform Status</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Platform</th>
                        <th>Status</th>
                        <th>Post ID</th>
                        <th>Published</th>
                        <th>Error</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($platforms as $platform) { ?>
                    <tr>
                        <td><?php echo ucfirst($platform->platform); ?></td>
                        <td>
                            <span class="label label-<?php echo $platform->status == 'published' ? 'success' : ($platform->status == 'failed' ? 'danger' : 'warning'); ?>">
                                <?php echo ucfirst($platform->status); ?>
                            </span>
                        </td>
                        <td><?php echo $platform->platform_post_id ? $platform->platform_post_id : '-'; ?></td>
                        <td><?php echo $platform->published_at ? _dt($platform->published_at) : '-'; ?></td>
                        <td><?php echo $platform->error_message ? '<span class="text-danger">' . $platform->error_message . '</span>' : '-'; ?></td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php
}




/**
 * Test cron manually (only for admins)
 */
public function test_cron()
{
    if (!is_admin()) {
        show_404();
    }

    echo "<pre>";
    echo "Testing Cron Job...\n";
    echo "==================\n\n";

    $this->load->model('sm_posters_model');
    
    $scheduled_posts = $this->sm_posters_model->get_due_posts();
    
    echo "Found " . count($scheduled_posts) . " scheduled posts\n\n";
    
    if (empty($scheduled_posts)) {
        echo "No posts to process.\n";
        echo "\nCreate a scheduled post with a past date/time to test.\n";
        echo "</pre>";
        return;
    }

    foreach ($scheduled_posts as $post) {
        echo "Post ID: {$post->id}\n";
        echo "Message: " . substr($post->message, 0, 50) . "...\n";
        echo "Scheduled: {$post->scheduled_at}\n";
        echo "Status: {$post->status}\n\n";
    }

    echo "\nTo process these posts, run:\n";
    echo "php modules/sm_posters/cron.php\n";
    
    echo "</pre>";
}
}