<?php

defined('BASEPATH') or exit('No direct script access allowed');

$CI = &get_instance();

// Create connections table
if (!$CI->db->table_exists(db_prefix() . 'fb_connections')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'fb_connections` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `client_id` int(11) NOT NULL,
        `page_id` varchar(255) NOT NULL,
        `page_name` varchar(255) DEFAULT NULL,
        `access_token` text NOT NULL,
        `status` tinyint(1) NOT NULL DEFAULT 1,
        `created_by` int(11) NOT NULL,
        `created_at` datetime NOT NULL,
        `updated_at` datetime DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `client_id` (`client_id`),
        KEY `status` (`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

// Create posts table with LONGBLOB for media
if (!$CI->db->table_exists(db_prefix() . 'fb_posts')) {
    $CI->db->query('CREATE TABLE `' . db_prefix() . 'fb_posts` (
        `id` int(11) NOT NULL AUTO_INCREMENT,
        `connection_id` int(11) NOT NULL,
        `message` text,
        `link` varchar(500) DEFAULT NULL,
        `media_type` enum("none","image","video") DEFAULT "none",
        `media_data` LONGBLOB DEFAULT NULL,
        `media_mime` varchar(100) DEFAULT NULL,
        `media_filename` varchar(255) DEFAULT NULL,
        `fb_post_id` varchar(255) DEFAULT NULL,
        `status` enum("pending","published","failed") DEFAULT "pending",
        `error_message` text,
        `posted_by` int(11) NOT NULL,
        `posted_at` datetime NOT NULL,
        PRIMARY KEY (`id`),
        KEY `connection_id` (`connection_id`),
        KEY `status` (`status`)
    ) ENGINE=InnoDB DEFAULT CHARSET=' . $CI->db->char_set . ';');
}

log_activity('SM Posters Module Installed');