<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Sm_posters extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        
        $this->load->model('sm_posters_model');
        $this->load->model('facebook_model');
        $this->load->library('form_validation');
        $this->load->model('clients_model');
        
        if (!has_permission('sm_posters', '', 'view')) {
            access_denied('sm_posters');
        }
    }
    
    public function index()
    {
        $data['title'] = 'Facebook Connections';
        $data['connections'] = $this->sm_posters_model->get_all();
        
        $this->load->view('sm_posters/manage', $data);
    }
    
    public function add()
    {
        if (!has_permission('sm_posters', '', 'create')) {
            access_denied('sm_posters');
        }

        if ($this->input->post()) {
            $this->form_validation->set_rules('client_id', 'Client', 'required');
            $this->form_validation->set_rules('page_id', 'Page ID', 'required');
            $this->form_validation->set_rules('access_token', 'Access Token', 'required');

            if ($this->form_validation->run() == TRUE) {
                $data = [
                    'client_id' => $this->input->post('client_id'),
                    'page_id' => $this->input->post('page_id'),
                    'page_name' => $this->input->post('page_name'),
                    'access_token' => $this->input->post('access_token'),
                    'status' => $this->input->post('status') ? 1 : 0,
                ];

                $insert_id = $this->sm_posters_model->add($data);

                if ($insert_id) {
                    set_alert('success', 'Connection added successfully');
                    redirect(admin_url('sm_posters'));
                } else {
                    set_alert('danger', 'Failed to add connection');
                }
            }
        }

        $data['title'] = 'Add Facebook Connection';
        $data['clients'] = $this->clients_model->get();
        $data['connection'] = null;
        
        $this->load->view('sm_posters/add_edit', $data);
    }
    
    public function edit($id)
    {
        if (!has_permission('sm_posters', '', 'edit')) {
            access_denied('sm_posters');
        }

        $connection = $this->sm_posters_model->get($id);

        if (!$connection) {
            show_404();
        }

        if ($this->input->post()) {
            $this->form_validation->set_rules('client_id', 'Client', 'required');
            $this->form_validation->set_rules('page_id', 'Page ID', 'required');
            $this->form_validation->set_rules('access_token', 'Access Token', 'required');

            if ($this->form_validation->run() == TRUE) {
                $data = [
                    'client_id' => $this->input->post('client_id'),
                    'page_id' => $this->input->post('page_id'),
                    'page_name' => $this->input->post('page_name'),
                    'access_token' => $this->input->post('access_token'),
                    'status' => $this->input->post('status') ? 1 : 0,
                ];

                $result = $this->sm_posters_model->update($id, $data);

                if ($result) {
                    set_alert('success', 'Connection updated successfully');
                    redirect(admin_url('sm_posters'));
                } else {
                    set_alert('danger', 'Failed to update connection');
                }
            }
        }

        $data['title'] = 'Edit Facebook Connection';
        $data['clients'] = $this->clients_model->get();
        $data['connection'] = $connection;
        
        $this->load->view('sm_posters/add_edit', $data);
    }
    
    public function delete($id)
    {
        if (!has_permission('sm_posters', '', 'delete')) {
            access_denied('sm_posters');
        }

        $response = $this->sm_posters_model->delete($id);

        if ($response) {
            set_alert('success', 'Connection deleted successfully');
        } else {
            set_alert('danger', 'Failed to delete connection');
        }

        redirect(admin_url('sm_posters'));
    }
    
    public function toggle_status($id)
    {
        if (!has_permission('sm_posters', '', 'edit')) {
            access_denied('sm_posters');
        }

        $connection = $this->sm_posters_model->get($id);

        if ($connection) {
            $new_status = $connection->status == 1 ? 0 : 1;
            $this->sm_posters_model->update($id, ['status' => $new_status]);
            set_alert('success', 'Status updated successfully');
        }

        redirect(admin_url('sm_posters'));
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
            $this->form_validation->set_rules('connection_id', 'Connection', 'required');
            $this->form_validation->set_rules('message', 'Message', 'required');

            if ($this->form_validation->run() == TRUE) {
                $connection_id = $this->input->post('connection_id');
                $message = $this->input->post('message');
                $link = $this->input->post('link');
                
                $connection = $this->sm_posters_model->get($connection_id);
                
                if (!$connection || $connection->status != 1) {
                    set_alert('danger', 'Connection is inactive or not found');
                    redirect(admin_url('sm_posters/create_post'));
                }

                $media_path = null;
                $media_type = $this->input->post('media_type') ? $this->input->post('media_type') : 'none';
                $media_base64 = $this->input->post('media_base64');
                $media_mime = $this->input->post('media_mime');
                $media_filename = $this->input->post('media_filename');

                // Convert base64 to temporary file if media exists
                if (!empty($media_base64)) {
                    $media_path = $this->_create_temp_file($media_base64, $media_mime, $media_filename);
                }

                // Post to Facebook
                $result = $this->facebook_model->_post_to_facebook($connection, $message, $link, $media_path);

                // Delete temporary file
                if ($media_path && file_exists($media_path)) {
                    unlink($media_path);
                }

                // Save to database with base64
                $post_data = [
                    'connection_id' => $connection_id,
                    'message' => $message,
                    'link' => $link,
                    'media_type' => $media_type,
                    'media_data' => !empty($media_base64) ? $media_base64 : null,
                    'media_mime' => $media_mime,
                    'media_filename' => $media_filename,
                    'status' => $result['success'] ? 'published' : 'failed',
                    'fb_post_id' => $result['success'] ? $result['post_id'] : null,
                    'error_message' => !$result['success'] ? $result['error'] : null
                ];
                
                $this->sm_posters_model->add_post($post_data);

                if ($result['success']) {
                    set_alert('success', 'Posted to Facebook successfully! Post ID: ' . $result['post_id']);
                    log_activity('Posted to Facebook [Connection: ' . $connection->page_name . ', Post ID: ' . $result['post_id'] . ']');
                } else {
                    set_alert('danger', 'Failed to post: ' . $result['error']);
                }
                
                redirect(admin_url('sm_posters/create_post'));
            }
        }

        $data['title'] = 'Create Facebook Post';
        $data['connections'] = $this->sm_posters_model->get_all();
        $data['connections'] = array_filter($data['connections'], function($conn) {
            return $conn->status == 1;
        });
        
        $this->load->view('sm_posters/create_post', $data);
    }

    /**
     * Create temporary file from base64
     */
    private function _create_temp_file($base64_data, $mime_type, $filename)
    {
        // Decode base64
        $binary_data = base64_decode($base64_data);
        
        // Create temp directory if needed
        $temp_dir = FCPATH . 'uploads/temp/';
        if (!is_dir($temp_dir)) {
            mkdir($temp_dir, 0755, true);
        }
        
        // Generate unique filename
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $temp_filename = 'temp_' . uniqid() . '_' . time() . '.' . $extension;
        $temp_path = $temp_dir . $temp_filename;
        
        // Write file
        file_put_contents($temp_path, $binary_data);
        
        return $temp_path;
    }

    /**
     * View posts history
     */
    public function posts()
    {
        $data['title'] = 'Posts History';
        $data['posts'] = $this->sm_posters_model->get_all_posts();
        
        $this->load->view('sm_posters/posts', $data);
    }
}