<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();

// Drop table
$CI->db->query('DROP TABLE IF EXISTS `' . db_prefix() . 'fb_connections`');

// Remove permissions
$CI->db->where('name', 'fb_connector');
$CI->db->delete(db_prefix() . 'permissions');

log_activity('Facebook Connector Module Uninstalled');