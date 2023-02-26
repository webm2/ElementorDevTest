<?php

add_action('wp_enqueue_scripts', 'tt_child_enqueue_parent_styles');

function tt_child_enqueue_parent_styles()
{
    wp_enqueue_style('parent-style', get_template_directory_uri() . '/style.css');
}

/***
 * Filter function that hides the admin bar from user "wp-test"
 * All other users get to see the admin bar
 */
function hide_admin_bar_for_specific_user($show)
{

    if ('wp-test' == wp_get_current_user()->user_login) {
        return false;
    }
    return $show;
}

// show_admin_bar is a wp function being referenced here whenever a plugin is loaded, this function is called
add_filter('show_admin_bar', 'hide_admin_bar_for_specific_user');

/***
 * Register Custom Post Type
 */
function products_post_type()
{

    $labels = array(
        'name' => _x('Products', 'Post Type General Name', 'text_domain'),
        'singular_name' => _x('Product', 'Post Type Singular Name', 'text_domain'),
        'menu_name' => __('Products', 'text_domain'),
        'name_admin_bar' => __('Product', 'text_domain'),
        'archives' => __('Product Archives', 'text_domain'),
        'attributes' => __('Product Attributes', 'text_domain'),
        'parent_item_colon' => __('Parent Product:', 'text_domain'),
        'all_items' => __('All Products', 'text_domain'),
        'add_new_item' => __('Add New Product', 'text_domain'),
        'add_new' => __('Add New', 'text_domain'),
        'new_item' => __('New Product', 'text_domain'),
        'edit_item' => __('Edit Product', 'text_domain'),
        'update_item' => __('Update Product', 'text_domain'),
        'view_item' => __('View Product', 'text_domain'),
        'view_items' => __('View Products', 'text_domain'),
        'search_items' => __('Search Product', 'text_domain'),
        'not_found' => __('Not found', 'text_domain'),
        'not_found_in_trash' => __('Not found in Trash', 'text_domain'),
    );
    $args = array(
        'label' => __('Product', 'text_domain'),
        'description' => __('A custom post type for products', 'text_domain'),
        'labels' => $labels,
        'supports' => array('title', 'editor', 'thumbnail'),
        'taxonomies' => array('categories'),
        'hierarchical' => false,
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        'menu_position' => 5,
        'show_in_admin_bar' => true,
        'show_in_nav_menus' => true,
        'can_export' => true,
        'has_archive' => true,
        'rewrite' => array('slug' => 'products'),
        'menu_icon' => 'dashicons-cart',
        'show_in_rest' => true,
    );
    register_post_type('products', $args);

}
add_action('init', 'products_post_type', 0);



/**
 * Register the custom taxonomy for product categories
 
 */
function products_categories_taxonomy()
{
    $labels = array(
        'name' => _x('Categories', 'taxonomy general name'),
        'singular_name' => _x('Category', 'taxonomy singular name'),
        'search_items' => __('Search Categories'),
        'all_items' => __('All Categories'),
        'parent_item' => __('Parent Category'),
        'parent_item_colon' => __('Parent Category:'),
        'edit_item' => __('Edit Category'),
        'update_item' => __('Update Category'),
        'add_new_item' => __('Add New Category'),
        'new_item_name' => __('New Category Name'),
        'menu_name' => __('Categories'),
    );

    $args = array(
        'hierarchical' => true,
        // Make it hierarchical like categories
        'labels' => $labels,
        'show_ui' => true,
        'show_admin_column' => true,
        'query_var' => true,
        'rewrite' => array('slug' => 'product-category'),
        'show_in_rest' => true,
    );

    register_taxonomy('product_category', 'products', $args);
}
add_action('init', 'products_categories_taxonomy');

/**
 * Define the custom meta boxes for the products post type 
 * */ 
function products_custom_meta_boxes()
{
    add_meta_box('product-main-image', 'Main Image', 'product_main_image_callback', 'products', 'normal', 'high');
    add_meta_box('product-image-gallery', 'Image Gallery', 'product_image_gallery_callback', 'products', 'normal', 'high');
    add_meta_box('product-price', 'Price', 'product_price_callback', 'products', 'normal', 'high');
    add_meta_box('product-sale-price', 'Sale Price', 'product_sale_price_callback', 'products', 'normal', 'high');
    add_meta_box('product-youtube-video', 'YouTube Video', 'product_youtube_video_callback', 'products', 'normal', 'high');
    add_meta_box('product-is-on-sale', 'Is On Sale?', 'product_is_on_sale_callback', 'products', 'normal', 'high');
}

/** 
 * Define the callback function for the main image meta box 
 */
function product_main_image_callback($post)
{
    // Retrieve the current value of the main image custom field
    $main_image = get_post_meta($post->ID, 'product-main-image', true);
    // Output the HTML for the main image meta box
    ?>
    <p>
        <label for="product-main-image">Main Image:</label><br>
        <input type="text" name="product_main_image" id="product-main-image" class="regular-text"
            value="<?php echo esc_attr($main_image); ?>"><br>
        <small>Enter the URL of the main product image.</small>
    </p>
<?php
}

// Define the callback function for the image gallery meta box
function product_image_gallery_callback($post)
{
    // Retrieve the current value of the image gallery custom field
    $image_gallery = get_post_meta($post->ID, '_product_image_gallery', true);
    // Output the HTML for the image gallery meta box
    ?>
    <p>
        <label for="product-image-gallery">Image Gallery:</label><br>
        <input type="text" name="product_image_gallery" id="product-image-gallery" class="regular-text"
            value="<?php echo esc_attr($image_gallery); ?>"><br>
        <small>Enter the URLs of up to 6 images for the product gallery, separated by commas.</small>
    </p>
    <div id="product-image-gallery-preview"></div>
    <p>
        <button type="button" class="button" id="product-image-gallery-upload-button">Add Images</button>
        <button type="button" class="button" id="product-image-gallery-clear-button">Clear Images</button>
    </p>
<?php
}

// Define the callback function for the price meta box
function product_price_callback($post)
{
    // Retrieve the current value of the price custom field
    $price = get_post_meta($post->ID, '_product_price', true);
    // Output the HTML for the price meta box
    ?>
    <p>
        <label for="product-price">Price:</label><br>
        <input type="text" name="product_price" id="product-price" class="regular-text"
            value="<?php echo esc_attr($price); ?>"><br>
    </p>
<?php
}
// Define the callback function for the sale price meta box
function product_sale_price_callback($post)
{
    // Retrieve the current value of the sale price custom field
    $sale_price = get_post_meta($post->ID, '_product_sale_price', true);
    // Output the HTML for the sale price meta box
    ?>
    <p>
        <label for="product-sale-price">Sale Price:</label><br>
        <input type="text" name="product_sale_price" id="product-sale-price" class="regular-text"
            value="<?php echo esc_attr($sale_price); ?>"><br>
    </p>
<?php
}

// Define the callback function for the YouTube video meta box
function product_youtube_video_callback($post)
{
    // Retrieve the current value of the YouTube video custom field
    $youtube_video = get_post_meta($post->ID, '_product_youtube_video', true);
    // Output the HTML for the YouTube video meta box
    ?>
    <p>
        <label for="product-youtube-video">YouTube Video:</label><br>
        <input type="text" name="product_youtube_video" id="product-youtube-video" class="regular-text"
            value="<?php echo esc_attr($youtube_video); ?>"><br>
        <small>Enter the URL of the YouTube video to embed in the product page.</small>
    </p>
<?php
}

// Define the callback function for the is on sale meta box
function product_is_on_sale_callback($post)
{
    // Retrieve the current value of the is on sale custom field
    $is_on_sale = get_post_meta($post->ID, '_product_is_on_sale', true);
    // Output the HTML for the is on sale meta box
    ?>
    <p>
        <label for="product-is-on-sale">Is On Sale?</label><br>
        <input type="checkbox" name="product_is_on_sale" id="product-is-on-sale" value="yes" <?php checked($is_on_sale, 'yes'); ?>>
    </p>
<?php
}

// Save the custom meta box data
add_action('save_post_products', 'save_products_custom_meta_boxes');
function save_products_custom_meta_boxes($post_id)
{
    // Check if the post type is "products"
    if (get_post_type($post_id) == 'products') {
        // Sanitize and save the main image custom field
        if (isset($_POST['product_main_image'])) {
            $main_image = sanitize_text_field($_POST['product_main_image']);
            update_post_meta($post_id, '_product_main_image', $main_image);
        }
        // Sanitize and save the image gallery custom field
        if (isset($_POST['product_image_gallery'])) {
            $image_gallery = sanitize_text_field($_POST['product_image_gallery']);
            update_post_meta($post_id, '_product_image_gallery', $image_gallery);
        }
        // Sanitize and save the price custom field
        if (isset($_POST['product_price'])) {
            $price = floatval($_POST['product_price']);
            update_post_meta($post_id, '_product_price', $price);
        }
        // Sanitize and save the sale price custom field
        if (isset($_POST['product_sale_price'])) {
            $sale_price = floatval($_POST['product_sale_price']);
            update_post_meta($post_id, '_product_sale_price', $sale_price);
        }
        // Sanitize and save the YouTube video custom field
        if (isset($_POST['product_youtube_video'])) {
            $youtube_video = esc_url($_POST['product_youtube_video']);
            update_post_meta($post_id, '_product_youtube_video', $youtube_video);
        }
        // Sanitize and save the is on sale custom field
        if (isset($_POST['product_is_on_sale'])) {
            $is_on_sale = ($_POST['product_is_on_sale'] == 'yes') ? 'yes' : 'no';
            update_post_meta($post_id, '_product_is_on_sale', $is_on_sale);
        }
    }
}

// Enqueue scripts and styles for custom meta boxes
add_action('admin_enqueue_scripts', 'products_custom_meta_boxes_scripts');
function products_custom_meta_boxes_scripts()
{
    global $pagenow, $typenow;
    if ($typenow == 'products') {
        // Enqueue the media uploader script
        wp_enqueue_media();
        // Enqueue the script for the image gallery meta box
        wp_enqueue_script('product-image-gallery', get_template_directory_uri() . '-child/js/twenty-twenty-child.js', array('jquery'), '1.0', true);
        // Localize the script with the necessary data
        wp_localize_script('product-image-gallery', 'productImageGallery', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('product-image-gallery'),
        ));
    }
}

// Define the AJAX handler function for the image gallery meta box
add_action('wp_ajax_product_image_gallery_upload', 'product_image_gallery_upload_callback');
function product_image_gallery_upload_callback()
{
    // Verify the AJAX request
    check_ajax_referer('product-image-gallery', 'nonce');
    // Get the uploaded files
    $files = $_FILES['product-image-gallery'];
    // Upload the files
    $attachments = array();
    foreach ($files['name'] as $key => $value) {
        if ($files['name'][$key]) {
            $file = array(
                'name' => $files['name'][$key],
                'type' => $files['type'][$key],
                'tmp_name' => $files['tmp_name'][$key],
                'error' => $files['error'][$key],
                'size' => $files['size'][$key]
            );
            $upload = wp_handle_upload($file, array('test_form' => false));
            $attachments[] = $upload['url'];
        }
    }
    // Return the uploaded files as JSON
    wp_send_json_success($attachments);
}


// // Add custom meta boxes for the products custom post type
// add_action('add_meta_boxes_products', 'add_products_custom_meta_boxes');
// function add_products_custom_meta_boxes()
// {
//     // Main image meta box
//     add_meta_box(
//         'product-main-image',
//         'Main Image',
//         'product_main_image_callback',
//         'products',
//         'normal',
//         'default'
//     );
//     // Image gallery meta box
//     add_meta_box(
//         'product-image-gallery',
//         'Image Gallery',
//         'product_image_gallery_callback',
//         'products',
//         'normal',
//         'default'
//     );
//     // Price meta box
//     add_meta_box(
//         'product-price',
//         'Price',
//         'product_price_callback',
//         'products',
//         'normal',
//         'default'
//     );
//     // Sale price meta box
//     add_meta_box(
//         'product-sale-price',
//         'Sale Price',
//         'product_sale_price_callback',
//         'products',
//         'normal',
//         'default'
//     );
//     // YouTube video meta box
//     add_meta_box(
//         'product-youtube-video',
//         'YouTube Video',
//         'product_youtube_video_callback',
//         'products',
//         'normal',
//         'default'
//     );
//     // Is on sale meta box
//     add_meta_box(
//         'product-is-on-sale',
//         'Is On Sale?',
//         'product_is_on_sale_callback',
//         'products',
//         'side',
//         'default'
//     );
// }
// hide Gutenburg blocks on the product custom post type
add_filter('use_block_editor_for_post_type', 'prefix_disable_gutenberg', 10, 2);
function prefix_disable_gutenberg($current_status, $post_type)
{
    if ($post_type === 'products')
        return false;
    return $current_status;
}





