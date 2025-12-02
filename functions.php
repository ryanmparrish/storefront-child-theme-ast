<?php
/**
 * Storefront Child Theme Functions
 *
 * @package storefront-child-theme-ast
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * Enqueue parent and child theme styles
 */
function storefront_child_theme_enqueue_styles() {
    // Enqueue parent theme styles
    wp_enqueue_style('storefront-style', get_template_directory_uri() . '/style.css');
    
    // Enqueue child theme styles
    wp_enqueue_style('storefront-child-style', 
        get_stylesheet_directory_uri() . '/style.css',
        array('storefront-style'),
        null
    );
    
    // Enqueue custom JavaScript
    wp_enqueue_script('storefront-child-custom', 
        get_stylesheet_directory_uri() . '/assets/js/custom.js', 
        array('jquery'), 
        null, 
        true
    );
}
add_action('wp_enqueue_scripts', 'storefront_child_theme_enqueue_styles');

/**
 * Enqueue admin scripts for meta box
 */
function storefront_child_theme_admin_scripts($hook) {
    global $post;
    
    // Only load on post.php and post-new.php
    if ($hook == 'post-new.php' || $hook == 'post.php') {
        if ('page' === $post->post_type) {
            wp_enqueue_media();
            wp_enqueue_script('background-image-admin', 
                get_stylesheet_directory_uri() . '/assets/js/admin.js', 
                array('jquery'), 
                '1.0.0', 
                true
            );
        }
    }
}
add_action('admin_enqueue_scripts', 'storefront_child_theme_admin_scripts');

/**
 * Add meta box for background image
 */
function add_background_image_meta_box() {
    add_meta_box(
        'background_image_meta_box',
        'Full Bleed Background Image',
        'background_image_meta_box_callback',
        'page',
        'side',
        'high'
    );
}
add_action('add_meta_boxes', 'add_background_image_meta_box');

/**
 * Meta box callback function
 */
function background_image_meta_box_callback($post) {
    // Add nonce for security
    wp_nonce_field('background_image_meta_box', 'background_image_meta_box_nonce');
    
    // Get current background image ID
    $background_image_id = get_post_meta($post->ID, '_background_image_id', true);
    $background_image_url = wp_get_attachment_image_url($background_image_id, 'full');
    
    ?>
    <div class="background-image-container">
        <input type="hidden" id="background_image_id" name="background_image_id" value="<?php echo esc_attr($background_image_id); ?>" />
        
        <div id="background_image_preview" style="margin-bottom: 10px;">
            <?php if ($background_image_url): ?>
                <img src="<?php echo esc_url($background_image_url); ?>" style="max-width: 100%; height: auto; border-radius: 4px;" />
            <?php endif; ?>
        </div>
        
        <button type="button" id="upload_background_image" class="button">
            <?php echo $background_image_url ? 'Change Background Image' : 'Set Background Image'; ?>
        </button>
        
        <?php if ($background_image_url): ?>
            <button type="button" id="remove_background_image" class="button" style="margin-top: 5px; color: #dc3232;">
                Remove Background Image
            </button>
        <?php endif; ?>
        
        <p class="description">
            This image will be used as a full-screen background for this page. The content will appear over the image with a semi-transparent overlay.
        </p>
    </div>
    <?php
}

/**
 * Save meta box data
 */
function save_background_image_meta_box($post_id) {
    // Check if nonce is valid
    if (!isset($_POST['background_image_meta_box_nonce']) || 
        !wp_verify_nonce($_POST['background_image_meta_box_nonce'], 'background_image_meta_box')) {
        return;
    }
    
    // Check if user has permissions
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }
    
    // Check if not an autosave
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Save the background image ID
    if (isset($_POST['background_image_id'])) {
        update_post_meta($post_id, '_background_image_id', sanitize_text_field($_POST['background_image_id']));
    }
}
add_action('save_post', 'save_background_image_meta_box');

/**
 * Get background image URL for a page
 */
function get_page_background_image_url($post_id = null) {
    if (!$post_id) {
        $post_id = get_the_ID();
    }
    
    $background_image_id = get_post_meta($post_id, '_background_image_id', true);
    
    if ($background_image_id) {
        return wp_get_attachment_image_url($background_image_id, 'full');
    }
    
    return false;
}

/**
 * Add body class if page has background image
 */
function add_background_body_class($classes) {
    if (is_page() && get_page_background_image_url()) {
        $classes[] = 'page-with-background';
    }
    return $classes;
}
add_filter('body_class', 'add_background_body_class');

/**
 * Display background image and overlay
 */
function display_page_background() {
    if (is_page()) {
        $background_image_url = get_page_background_image_url();
        
        if ($background_image_url) {
            echo '<div class="page-background-image" style="background-image: url(\'' . esc_url($background_image_url) . '\');"></div>';
            echo '<div class="page-background-overlay"></div>';
        }
    }
}

// Hook into the page template - using multiple hooks for better compatibility
add_action('storefront_before_content', 'display_page_background');
