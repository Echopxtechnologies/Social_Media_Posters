<?php
defined('BASEPATH') or exit('No direct script access allowed');

/*
Module Name: Social Media Posters
Description: Social Media Poster to post and schedule to post the content
Version: 1.0.0
Requires at least: 2.3.*
Author: Raju
*/

define('SM_POSTERS_MODULE', 'sm_posters');
define('SM_POSTERS_PATH', module_dir_path(SM_POSTERS_MODULE));
define('SM_POSTERS_URL',  module_dir_url(SM_POSTERS_MODULE));

// Register language files (if function exists in your Perfex version)
if (function_exists('register_language_files')) {
    register_language_files(SM_POSTERS_MODULE, ['sm_posters']);
}

// Hooks
hooks()->add_action('admin_init', 'sm_posters_admin_menu');
hooks()->add_action('admin_init', 'sm_posters_permissions');

// Lifecycle
register_activation_hook(SM_POSTERS_MODULE, 'sm_posters_activate');
register_deactivation_hook(SM_POSTERS_MODULE, 'sm_posters_deactivate');
register_uninstall_hook(SM_POSTERS_MODULE, 'sm_posters_uninstall');

/**
 * Add Admin sidebar menu
 */
function sm_posters_admin_menu()
{
    if (!has_permission('sm_posters', '', 'view')) {
        return;
    }

    $CI = &get_instance();
    
    // Main menu
    $CI->app_menu->add_sidebar_menu_item('sm_posters', [
        'name'     => 'Social Media',
        'href'     => admin_url('sm_posters'),
        'icon'     => 'fa fa-share-alt',
        'position' => 35,
    ]);
    
    // Submenus
    $CI->app_menu->add_sidebar_children_item('sm_posters', [
        'slug'     => 'sm_posters_connections',
        'name'     => 'Connections',
        'href'     => admin_url('sm_posters'),
        'position' => 1,
    ]);
    
    $CI->app_menu->add_sidebar_children_item('sm_posters', [
        'slug'     => 'sm_posters_create_post',
        'name'     => 'Create Post',
        'href'     => admin_url('sm_posters/create_post'),
        'position' => 2,
    ]);
    
    $CI->app_menu->add_sidebar_children_item('sm_posters', [
        'slug'     => 'sm_posters_add',
        'name'     => 'Add Connection',
        'href'     => admin_url('sm_posters/add'),
        'position' => 3,
    ]);
}
/**
 * Register permissions
 */
function sm_posters_permissions()
{
    $capabilities = [
        'view'   => 'View',
        'create' => 'Create',
        'edit'   => 'Edit',
        'delete' => 'Delete',
    ];

    register_staff_capabilities('sm_posters', $capabilities, 'Social Media Posters');
}

/**
 * Activation: create tables/options
 */
function sm_posters_activate()
{
    require_once SM_POSTERS_PATH . 'install.php';
}

/**
 * Deactivation: keep data (no-op)
 */
function sm_posters_deactivate()
{
    // Intentionally left empty
}

/**
 * Uninstall: drop tables/options
 */
function sm_posters_uninstall()
{
    require_once SM_POSTERS_PATH . 'uninstall.php';
}