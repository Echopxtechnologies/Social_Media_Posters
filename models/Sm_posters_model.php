<?php

defined('BASEPATH') or exit('No direct script access allowed');

class Sm_posters_model extends App_Model
{
    private $table;
    private $posts_table;

    public function __construct()
    {
        parent::__construct();
        $this->table = db_prefix() . 'fb_connections';
        $this->posts_table = db_prefix() . 'fb_posts';  // Fixed: renamed from $this->posts
    }

    /**
     * Get all connections
     */
    public function get_all()
    {
        $this->db->select($this->table . '.*, ' . db_prefix() . 'clients.company');
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid = ' . $this->table . '.client_id', 'left');
        $this->db->order_by($this->table . '.created_at', 'DESC');
        
        return $this->db->get($this->table)->result();
    }

    /**
     * Get active connections only
     */
    public function get_active()
    {
        $this->db->select($this->table . '.*, ' . db_prefix() . 'clients.company');
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid = ' . $this->table . '.client_id', 'left');
        $this->db->where($this->table . '.status', 1);
        $this->db->order_by($this->table . '.created_at', 'DESC');
        
        return $this->db->get($this->table)->result();
    }

    /**
     * Get single connection
     */
    public function get($id)
    {
        $this->db->select($this->table . '.*, ' . db_prefix() . 'clients.company');
        $this->db->join(db_prefix() . 'clients', db_prefix() . 'clients.userid = ' . $this->table . '.client_id', 'left');
        $this->db->where($this->table . '.id', $id);
        
        return $this->db->get($this->table)->row();
    }

    /**
     * Add connection
     */
    public function add($data)
    {
        $data['created_by'] = get_staff_user_id();
        $data['created_at'] = date('Y-m-d H:i:s');
        
        $this->db->insert($this->table, $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            log_activity('New FB Connection Added [ID: ' . $insert_id . ']');
            return $insert_id;
        }

        return false;
    }

    /**
     * Update connection
     */
    public function update($id, $data)
    {
        $data['updated_at'] = date('Y-m-d H:i:s');
        
        $this->db->where('id', $id);
        $this->db->update($this->table, $data);

        if ($this->db->affected_rows() > 0) {
            log_activity('FB Connection Updated [ID: ' . $id . ']');
            return true;
        }

        return false;
    }

    /**
     * Delete connection
     */
    public function delete($id)
    {
        $this->db->where('id', $id);
        $this->db->delete($this->table);

        if ($this->db->affected_rows() > 0) {
            log_activity('FB Connection Deleted [ID: ' . $id . ']');
            return true;
        }

        return false;
    }

    /**
     * Get connections by client
     */
    public function get_by_client($client_id)
    {
        $this->db->where('client_id', $client_id);
        return $this->db->get($this->table)->result();
    }

    /**
     * Get all posts
     */
    public function get_all_posts()
    {
        // Fixed: Using $this->posts_table instead of $this->table
        $this->db->select($this->posts_table . '.id, ' . 
                         $this->posts_table . '.connection_id, ' . 
                         $this->posts_table . '.message, ' . 
                         $this->posts_table . '.link, ' . 
                         $this->posts_table . '.media_type, ' . 
                         $this->posts_table . '.media_mime, ' . 
                         $this->posts_table . '.media_filename, ' . 
                         $this->posts_table . '.fb_post_id, ' . 
                         $this->posts_table . '.status, ' . 
                         $this->posts_table . '.error_message, ' . 
                         $this->posts_table . '.posted_at, ' . 
                         $this->table . '.page_name');
        $this->db->join($this->table, $this->table . '.id = ' . $this->posts_table . '.connection_id', 'left');
        $this->db->order_by($this->posts_table . '.posted_at', 'DESC');
        
        return $this->db->get($this->posts_table)->result();
    }

    /**
     * Add post
     */
    public function add_post($data)
    {
        // Fixed: Using $this->posts_table instead of $this->table
        $data['posted_by'] = get_staff_user_id();
        $data['posted_at'] = date('Y-m-d H:i:s');
        
        $this->db->insert($this->posts_table, $data);
        $insert_id = $this->db->insert_id();

        if ($insert_id) {
            log_activity('New FB Post Added [ID: ' . $insert_id . ']');
            return $insert_id;
        }

        return false;
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
     * Get posts by connection
     */
    public function get_posts_by_connection($connection_id)
    {
        $this->db->where('connection_id', $connection_id);
        $this->db->order_by('posted_at', 'DESC');
        return $this->db->get($this->posts_table)->result();
    }
}