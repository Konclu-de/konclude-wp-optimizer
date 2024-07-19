<?php
/**
 * @package         Konclude WordPress Optimizer
 * @author          Archie Makuwa
 * @copyright       2024 Konclude (Pty) Ltd
 * @license         ??
 * 
 * Plugin Name:     Konclude WordPress Optimizer
 * Description:     A plugin to enhance the security of your WordPress site by setting various security and optimizations settings.
 * Version:         1.0.0
 * Author:          Konclude (Archie Makuwa)
 * Author URI:      https://www.konclu.de
 * 
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

// Add security headers based on options
function kwo_add_security_headers() {
    $options = get_option('kwo_options');

    if (!empty($options['x_frame_options'])) {
        header('X-Frame-Options: SAMEORIGIN');
    }
    if (!empty($options['csp'])) {
        header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://trusted.cdn.com; style-src 'self' 'unsafe-inline'; img-src 'self' data: https:; connect-src 'self' https://api.trusted.com");
    }
    if (!empty($options['hsts'])) {
        header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
    }
    if (!empty($options['x_content_type_options'])) {
        header('X-Content-Type-Options: nosniff');
    }
    if (!empty($options['x_xss_protection'])) {
        header('X-XSS-Protection: 1; mode=block');
    }
    if (!empty($options['referrer_policy'])) {
        header('Referrer-Policy: no-referrer-when-downgrade');
    }
    if (!empty($options['permissions_policy'])) {
        header('Permissions-Policy: geolocation=(self), microphone=()');
    }
}
add_action('send_headers', 'kwo_add_security_headers');

// Disable XML-RPC to prevent brute force attacks
if (!empty(get_option('kwo_options')['disable_xmlrpc'])) {
    add_filter('xmlrpc_enabled', '__return_false');
}

// Disable file editing from the admin panel
if (!empty(get_option('kwo_options')['disable_file_editing'])) {
    define('DISALLOW_FILE_EDIT', true);
}

// Add options page
function kwo_add_admin_menu() {
    add_options_page(
        'Konclude WordPress Optimizer',
        'Security Headers',
        'manage_options',
        'kwo',
        'kwo_options_page'
    );
}
add_action('admin_menu', 'kwo_add_admin_menu');

// Register settings
function kwo_settings_init() {
    register_setting('kwo', 'kwo_options');

    add_settings_section(
        'kwo_section_headers',
        __('Security Headers', 'kwo'),
        'kwo_section_headers_cb',
        'kwo'
    );

    add_settings_field(
        'x_frame_options',
        __('X-Frame-Options', 'kwo'),
        'kwo_x_frame_options_render',
        'kwo',
        'kwo_section_headers'
    );

    add_settings_field(
        'csp',
        __('Content-Security-Policy', 'kwo'),
        'kwo_csp_render',
        'kwo',
        'kwo_section_headers'
    );

    add_settings_field(
        'hsts',
        __('HTTP Strict Transport Security (HSTS)', 'kwo'),
        'kwo_hsts_render',
        'kwo',
        'kwo_section_headers'
    );

    add_settings_field(
        'x_content_type_options',
        __('X-Content-Type-Options', 'kwo'),
        'kwo_x_content_type_options_render',
        'kwo',
        'kwo_section_headers'
    );

    add_settings_field(
        'x_xss_protection',
        __('X-XSS-Protection', 'kwo'),
        'kwo_x_xss_protection_render',
        'kwo',
        'kwo_section_headers'
    );

    add_settings_field(
        'referrer_policy',
        __('Referrer Policy', 'kwo'),
        'kwo_referrer_policy_render',
        'kwo',
        'kwo_section_headers'
    );

    add_settings_field(
        'permissions_policy',
        __('Permissions Policy', 'kwo'),
        'kwo_permissions_policy_render',
        'kwo',
        'kwo_section_headers'
    );

    add_settings_section(
        'kwo_section_other',
        __('Other Security Options', 'kwo'),
        'kwo_section_other_cb',
        'kwo'
    );

    add_settings_field(
        'disable_xmlrpc',
        __('Disable XML-RPC', 'kwo'),
        'kwo_disable_xmlrpc_render',
        'kwo',
        'kwo_section_other'
    );

    add_settings_field(
        'disable_file_editing',
        __('Disable File Editing', 'kwo'),
        'kwo_disable_file_editing_render',
        'kwo',
        'kwo_section_other'
    );
}
add_action('admin_init', 'kwo_settings_init');

function kwo_section_headers_cb() {
    echo __('Configure the HTTP security headers you want to enable.', 'kwo');
}

function kwo_section_other_cb() {
    echo __('Additional security options.', 'kwo');
}

function kwo_x_frame_options_render() {
    $options = get_option('kwo_options');
    ?>
    <input type='checkbox' name='kwo_options[x_frame_options]' <?php checked($options['x_frame_options'], 1); ?> value='1'>
    <?php
}

function kwo_csp_render() {
    $options = get_option('kwo_options');
    ?>
    <input type='checkbox' name='kwo_options[csp]' <?php checked($options['csp'], 1); ?> value='1'>
    <?php
}

function kwo_hsts_render() {
    $options = get_option('kwo_options');
    ?>
    <input type='checkbox' name='kwo_options[hsts]' <?php checked($options['hsts'], 1); ?> value='1'>
    <?php
}

function kwo_x_content_type_options_render() {
    $options = get_option('kwo_options');
    ?>
    <input type='checkbox' name='kwo_options[x_content_type_options]' <?php checked($options['x_content_type_options'], 1); ?> value='1'>
    <?php
}

function kwo_x_xss_protection_render() {
    $options = get_option('kwo_options');
    ?>
    <input type='checkbox' name='kwo_options[x_xss_protection]' <?php checked($options['x_xss_protection'], 1); ?> value='1'>
    <?php
}

function kwo_referrer_policy_render() {
    $options = get_option('kwo_options');
    ?>
    <input type='checkbox' name='kwo_options[referrer_policy]' <?php checked($options['referrer_policy'], 1); ?> value='1'>
    <?php
}

function kwo_permissions_policy_render() {
    $options = get_option('kwo_options');
    ?>
    <input type='checkbox' name='kwo_options[permissions_policy]' <?php checked($options['permissions_policy'], 1); ?> value='1'>
    <?php
}

function kwo_disable_xmlrpc_render() {
    $options = get_option('kwo_options');
    ?>
    <input type='checkbox' name='kwo_options[disable_xmlrpc]' <?php checked($options['disable_xmlrpc'], 1); ?> value='1'>
    <?php
}

function kwo_disable_file_editing_render() {
    $options = get_option('kwo_options');
    ?>
    <input type='checkbox' name='kwo_options[disable_file_editing]' <?php checked($options['disable_file_editing'], 1); ?> value='1'>
    <?php
}

function kwo_options_page() {
    ?>
    <form action='options.php' method='post'>
        <h2>Konclude WordPress Optimizer</h2>
        <?php
        settings_fields('kwo');
        do_settings_sections('kwo');
        submit_button();
        ?>
    </form>
    <?php
}
