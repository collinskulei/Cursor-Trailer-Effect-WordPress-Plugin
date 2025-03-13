<?php
/**
 * Plugin Name: Cursor Trailer Effect
 * Plugin URI:  https://collinskulei.digital
 * Description: Adds a customizable cursor trailer effect with adjustable color, transparency, size, and fade effect.
 * Version:     1.3
 * Author:      Collins Kulei
 * Author URI:  https://collinskulei.digital
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Add Settings Page
function cursor_trailer_add_admin_menu() {
    add_options_page('Cursor Trailer Settings', 'Cursor Trailer', 'manage_options', 'cursor-trailer', 'cursor_trailer_settings_page');
}
add_action('admin_menu', 'cursor_trailer_add_admin_menu');

// Register Plugin Settings
function cursor_trailer_register_settings() {
    register_setting('cursor_trailer_settings', 'cursor_trailer_color', array('default' => '#ffffff')); // Default: White
    register_setting('cursor_trailer_settings', 'cursor_trailer_opacity', array('default' => '40'));
    register_setting('cursor_trailer_settings', 'cursor_trailer_radius', array('default' => '10'));
    register_setting('cursor_trailer_settings', 'cursor_trailer_fade', array('default' => '0.2'));
}
add_action('admin_init', 'cursor_trailer_register_settings');

// Settings Page HTML
function cursor_trailer_settings_page() {
    ?>
    <div class="wrap">
        <h1>Cursor Trailer Settings</h1>
        <form method="post" action="options.php">
            <?php settings_fields('cursor_trailer_settings'); ?>
            <?php do_settings_sections('cursor_trailer_settings'); ?>
            <table class="form-table">
                <tr>
                    <th scope="row">Cursor Color</th>
                    <td><input type="color" name="cursor_trailer_color" value="<?php echo esc_attr(get_option('cursor_trailer_color', '#ffffff')); ?>"></td>
                </tr>
                <tr>
                    <th scope="row">Transparency (%)</th>
                    <td>
                        <input type="number" name="cursor_trailer_opacity" min="0" max="100" value="<?php echo esc_attr(get_option('cursor_trailer_opacity', '40')); ?>">
                        <p class="description">Enter a value between 0 (fully transparent) and 100 (fully opaque).</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Circle Radius (px)</th>
                    <td>
                        <input type="number" name="cursor_trailer_radius" min="5" max="50" value="<?php echo esc_attr(get_option('cursor_trailer_radius', '10')); ?>">
                        <p class="description">Set the size of the cursor trailer (default: 10px).</p>
                    </td>
                </tr>
                <tr>
                    <th scope="row">Fade Effect (seconds)</th>
                    <td>
                        <input type="number" step="0.1" name="cursor_trailer_fade" min="0" max="2" value="<?php echo esc_attr(get_option('cursor_trailer_fade', '0.2')); ?>">
                        <p class="description">Set how quickly the trailer fades out (default: 0.2s).</p>
                    </td>
                </tr>
            </table>
            <?php submit_button(); ?>
        </form>
    </div>
    <?php
}

// Enqueue CSS & JavaScript
function cursor_trailer_enqueue_scripts() {
    $color = get_option('cursor_trailer_color', '#ffffff'); // Default to White
    $opacity = get_option('cursor_trailer_opacity', '40');
    $radius = get_option('cursor_trailer_radius', '10');
    $fade = get_option('cursor_trailer_fade', '0.2');
    
    $opacity_decimal = $opacity / 100; // Convert % to decimal

    // Add CSS
    $custom_css = "
    .cursor-trailer {
        position: fixed;
        width: {$radius}px;
        height: {$radius}px;
        background-color: {$color};
        opacity: {$opacity_decimal};
        border-radius: 50%;
        pointer-events: none;
        transform: translate(-50%, -50%);
        transition: transform 0.1s linear, opacity {$fade}s ease-out;
        z-index: 9999;
    }";
    
    wp_register_style('cursor-trailer-css', false);
    wp_enqueue_style('cursor-trailer-css');
    wp_add_inline_style('cursor-trailer-css', $custom_css);

    // Add JavaScript
    $custom_js = "
    document.addEventListener('DOMContentLoaded', function () {
        const cursor = document.createElement('div');
        cursor.classList.add('cursor-trailer');
        document.body.appendChild(cursor);

        document.addEventListener('mousemove', (e) => {
            cursor.style.left = e.clientX + 'px';
            cursor.style.top = e.clientY + 'px';
            cursor.style.opacity = '{$opacity_decimal}';
            cursor.style.backgroundColor = '{$color}';
            setTimeout(() => {
                cursor.style.opacity = '0';
            }, {$fade} * 1000);
        });
    });";

    wp_register_script('cursor-trailer-js', '', [], false, true);
    wp_enqueue_script('cursor-trailer-js');
    wp_add_inline_script('cursor-trailer-js', $custom_js);
}
add_action('wp_enqueue_scripts', 'cursor_trailer_enqueue_scripts');
