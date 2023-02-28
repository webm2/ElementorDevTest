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

/**
 * hide Gutenburg blocks on the product custom post type
 */ 
add_filter('use_block_editor_for_post_type', 'prefix_disable_gutenberg', 10, 2);
function prefix_disable_gutenberg($current_status, $post_type)
{
    if ($post_type === 'products')
        return false;
    return $current_status;
}


/***
 * Register Custom Post Type for Products
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
 * Add custom meta boxes for the 'products' custom post type
 * 
 * ==========================================================================================================
 */
// Add custom meta boxes for the "products" post type
function products_custom_meta_boxes() {
    add_meta_box(
      'product_main_image', // Unique ID
      'Main Image', // Box title
      'product_main_image_html', // Content callback
      'products', // Post type
      'normal', // Position
      'high' // Priority
    );
    
    add_meta_box(
      'product_image_gallery', // Unique ID
      'Image Gallery (Up to 6 Images)', // Box title
      'product_image_gallery_html', // Content callback
      'products', // Post type
      'normal', // Position
      'high' // Priority
    );
    
    add_meta_box(
      'product_price', // Unique ID
      'Price', // Box title
      'product_price_html', // Content callback
      'products', // Post type
      'normal', // Position
      'high' // Priority
    );
    
    add_meta_box(
      'product_sale_price', // Unique ID
      'Sale Price', // Box title
      'product_sale_price_html', // Content callback
      'products', // Post type
      'normal', // Position
      'high' // Priority
    );
    
    add_meta_box(
      'product_is_on_sale', // Unique ID
      'Is on Sale?', // Box title
      'product_is_on_sale_html', // Content callback
      'products', // Post type
      'normal', // Position
      'high' // Priority
    );
    
    add_meta_box(
      'product_youtube_video', // Unique ID
      'YouTube Video (oEmbed)', // Box title
      'product_youtube_video_html', // Content callback
      'products', // Post type
      'normal', // Position
      'high' // Priority
    );
  }
  add_action( 'add_meta_boxes_products', 'products_custom_meta_boxes' );
  
  // Render the main image field
  function product_main_image_html( $post ) {
    // Retrieve the current value of the field
    $product_main_image = get_post_meta( $post->ID, 'product_main_image', true );
    ?>
<label for="product_main_image">Upload Main Image:</label>
<input type="text" id="product_main_image" name="product_main_image"
    value="<?php echo esc_attr( $product_main_image ); ?>">
<input type="button" class="button button-primary" value="Choose Image"
    onclick="open_media_uploader('product_main_image');">
<br>
<img src="<?php echo esc_url( $product_main_image ); ?>" id="product_main_image_preview"
    style="max-width: 200px; display: <?php echo ( $product_main_image ) ? 'block' : 'none'; ?>;">
<script>
// Function to open the WordPress media uploader
function open_media_uploader(field_id) {
    var media_uploader = wp.media({
        title: 'Choose Image',
        button: {
            text: 'Use this image'
        },
        multiple: false
    });
    media_uploader.on('select', function() {
        var attachment = media_uploader.state().get('selection').first().toJSON();
        jQuery('#' + field_id).val(attachment.url);
        jQuery('#' + field_id + '_preview').attr('src', attachment.url).show();

    });
    media_uploader.open();
}
</script>

<?php
}

// Render the image gallery field
function product_image_gallery_html( $post ) {
  // Retrieve the current values of the field
  $product_image_gallery = get_post_meta( $post->ID, 'product_image_gallery', true );
  $product_image_gallery = is_array( $product_image_gallery ) ? $product_image_gallery : array();
  ?>
<label for="product_image_gallery">Upload Image Gallery (Up to 6 Images):</label>
<br>

<?php for ( $i = 1; $i <= 6; $i++ ) : ?>
<input type="text" id="product_image_gallery_<?php echo $i; ?>" name="product_image_gallery[]"
    value="<?php echo esc_attr( $product_image_gallery[ $i - 1 ] ); ?>">
<input type="button" class="button button-primary" value="Choose Image"
    onclick="open_media_uploader('product_image_gallery_<?php echo $i; ?>');">
<img src="<?php echo esc_url( $product_image_gallery[ $i - 1 ] ); ?>"
    id="product_image_gallery_<?php echo $i; ?>_preview"
    style="max-width: 200px; display: <?php echo ( $product_image_gallery[ $i - 1 ] ) ? 'block' : 'none'; ?>;">
<br>
<?php endfor; ?>
<script>
// Function to open the WordPress media uploader
function open_media_uploader(field_id) {
    var media_uploader = wp.media({
        title: 'Choose Image',
        button: {
            text: 'Use this image'
        },
        multiple: false
    });
    media_uploader.on('select', function() {
        var attachment = media_uploader.state().get('selection').first().toJSON();
        jQuery('#' + field_id).val(attachment.url);
        jQuery('#' + field_id + '_preview').attr('src', attachment.url).show();
    });
    media_uploader.open();
}
</script>
<?php
}

// Render the price field
function product_price_html( $post ) {
  // Retrieve the current value of the field
  $product_price = get_post_meta( $post->ID, 'product_price', true );
  ?>
<label for="product_price">Price:</label>
<input type="number" id="product_price" name="product_price" value="<?php echo esc_attr( $product_price ); ?>">

<?php
}

// Render the sale price field
function product_sale_price_html( $post ) {
  // Retrieve the current value of the field
  $product_sale_price = get_post_meta( $post->ID, 'product_sale_price', true );
  ?>
<label for="product_sale_price">Sale Price:</label>
<input type="number" id="product_sale_price" name="product_sale_price"
    value="<?php echo esc_attr( $product_sale_price ); ?>">

<?php
}

// Render the is on sale field
function product_is_on_sale_html( $post ) {
  // Retrieve the current value of the field
  $product_is_on_sale = get_post_meta( $post->ID, 'product_is_on_sale', true );
  ?>
<label for="product_is_on_sale">
    <input type="checkbox" id="product_is_on_sale" name="product_is_on_sale"
        <?php checked( $product_is_on_sale, 'on' ); ?>> Product is on sale
</label>
<?php
}
// Render the YouTube video field
function product_youtube_video_html( $post ) {
    // Retrieve the current value of the field
    $product_youtube_video = get_post_meta( $post->ID, 'product_youtube_video', true );
    ?>
<label for="product_youtube_video">YouTube Video (oEmbed):</label>
<input type="text" id="product_youtube_video" name="product_youtube_video"
    value="<?php echo esc_attr( $product_youtube_video ); ?>">
<?php
    }
    
    // Save the meta box values
    function save_products_custom_meta_boxes( $post_id ) {
      // Check if the current user is authorized to save the post meta
      if ( ! current_user_can( 'edit_post', $post_id ) ) {
        return;
      }
    
      // Save the main image field
      if ( isset( $_POST['product_main_image'] ) ) {
        update_post_meta( $post_id, 'product_main_image', sanitize_text_field( $_POST['product_main_image'] ) );
      }
    
      // Save the image gallery field
      if ( isset( $_POST['product_image_gallery'] ) ) {
        $product_image_gallery = array_map( 'sanitize_text_field', $_POST['product_image_gallery'] );
        update_post_meta( $post_id, 'product_image_gallery', $product_image_gallery );
      }
    
      // Save the price field
      if ( isset( $_POST['product_price'] ) ) {
        update_post_meta( $post_id, 'product_price', sanitize_text_field( $_POST['product_price'] ) );
      }
    
      // Save the sale price field
      if ( isset( $_POST['product_sale_price'] ) ) {
        update_post_meta( $post_id, 'product_sale_price', sanitize_text_field( $_POST['product_sale_price'] ) );
      }
    
      // Save the is on sale field
      if ( isset( $_POST['product_is_on_sale'] ) ) {
        update_post_meta( $post_id, 'product_is_on_sale', 'on' );
      } else {
        delete_post_meta( $post_id, 'product_is_on_sale' );
      }
    
      // Save the YouTube video field
      if ( isset( $_POST['product_youtube_video'] ) ) {
        update_post_meta( $post_id, 'product_youtube_video', sanitize_text_field( $_POST['product_youtube_video'] ) );
      }
    }
    add_action( 'save_post_products', 'save_products_custom_meta_boxes' );

/**
 *  Display up to 6 products via shortcode ordered by Title Ascending
 * 
 */
  function products_grid_shortcode() {
    // Query all products
    $args = array(
      'post_type' => 'products',
      'posts_per_page' => 6,
      'orderby' => 'title',
          'order' => 'ASC'
    );
    $query = new WP_Query( $args );
  
    // Initialize the grid HTML
    $output = '<div style="display:grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); class="products-grid">';
  
    // Loop through the products
    if ( $query->have_posts() ) {
      while ( $query->have_posts() ) {
        $query->the_post();
  
        // Get the product data
        $product_main_image = get_post_meta( get_the_ID(), 'product_main_image', true );
        $product_price = get_post_meta( get_the_ID(), 'product_price', true );
        $product_sale_price = get_post_meta( get_the_ID(), 'product_sale_price', true );
        $product_is_on_sale = get_post_meta( get_the_ID(), 'product_is_on_sale', true );
        $product_excerpt = get_the_excerpt();
        $product_youtube_video = get_post_meta( get_the_ID(), 'product_youtube_video', true );
        $product_terms = get_the_terms(get_the_ID(), 'product_category');
        $product_url = get_permalink( get_the_ID() );
  
        // Determine the price to display based on whether the product is on sale
        if ( $product_is_on_sale && $product_sale_price ) {
          $product_display_price = $product_sale_price;
        } else {
          $product_display_price = $product_price;
        }
  
        // Generate the HTML for the product
        $output .= '<div style="border: 1px solid black; margin: 20px; padding: 20px; background-color:white; text-align:center;" class="product-card">';
        $output .= '<img src="' . esc_url( $product_main_image ) . '">';
        $output .= '<p style="margin: 10px 0px 10px 0px;"><strong>' . get_the_title() . '</strong></p>';
        //$output .= '<p class="excerpt">' . esc_html( $product_excerpt ) . '</p>';
        if ( $product_is_on_sale ) {
            $output .= '<div style="background-color:green; padding:10px; color:white; display:inline-block;" ><strong>On Sale!</strong></div>';
          }
        $output .= '<p class="price">';
       
        if ( $product_is_on_sale && $product_sale_price ) {
          $output .= '<del><strong>Orginal Price:</strong> $' . esc_html( $product_price ) . '</del></br><strong>Sale ';
        }
        $output .= 'Price:</strong> $' . esc_html( $product_display_price ) ;
        
        $output .= '</br><strong>Category:</strong> ' . esc_html( $product_terms[0]->name ) . '</p>';
        $output .= '</br><a href=' . esc_html( $product_url ) . '><div class="product-link" style="background-color:red; color:white; padding:10px; font-weight:bold; display:inline-block;">View Product</div></a>';
    
        // if ( $product_youtube_video ) {
        //   $output .= '<p class="youtube-video">' . wp_oembed_get( esc_url( $product_youtube_video ) ) . '</p>';
        // }
        $output .= '</div>';
      }
      wp_reset_postdata();
    } else {
      $output .= 'No products found.';
    }
  
    // Close the grid HTML and return the output
    $output .= '</div>';
    return $output;
  }
  add_shortcode( 'products-grid', 'products_grid_shortcode' );

  /**
   *  Shortcode to display a product in a box:
   * Shortcode attributes: product id, bg color
   * Shortcode output:
   * Product image
   * Product title
   * Product price
   * Box background color

   */
  function product_box_shortcode( $atts ) {
    $atts = shortcode_atts( array(
      'id' => '',
      'bg_color' => ''
    ), $atts );
  
    $product_id = $atts['id'];
    $bg_color = $atts['bg_color'];
  
    if ( empty( $product_id ) ) {
      return '<p>Error: no product ID provided.</p>';
    }
  
    $product = get_post( $product_id );
  
    if ( empty( $product ) || $product->post_type !== 'products' ) {
      return '<p>Error: product not found or not in "products" post type.</p>';
    }
  
    $main_image = get_post_meta( $product_id, 'main_image', true );
    $price = get_post_meta( $product_id, 'price', true );
    $is_on_sale = get_post_meta( $product_id, 'is_on_sale', true );
    $sale_price = get_post_meta( $product_id, 'sale_price', true );
  
    ob_start();
    ?>

<div class="product-box" style="background-color: <?php echo $bg_color; ?>;">
    <img src="<?php echo $main_image; ?>" alt="<?php echo $product->post_title; ?>">
    <h3><?php echo $product->post_title; ?></h3>
    <?php if ( $is_on_sale ) : ?>
    <p class="product-price sale-price"><del><?php echo $price; ?></del></p>
    <p class="product-sale-price"><?php echo $sale_price; ?></p>
    <?php else : ?>
    <p class="product-price"><?php echo $price; ?></p>
    <?php endif; ?>
</div>

<?php
    $output = ob_get_contents();
    ob_end_clean();
    return apply_filters('products_shortcode_output', $output); // custom filter that allows to override the returned value of the shortcode.
  }
  add_shortcode( 'product_box', 'product_box_shortcode' );

/**
 * custom filter that allows to override the returned value of the product box shortcode.
 */

  function custom_products_filter($output) {
    $output .= '<p>Check out our full selection of products!</p>';
    return $output;
  }
  add_filter('products_shortcode_output', 'custom_products_filter');


/**
 * json-api endpoint that receives a category name/id 
 * and returns a list of products in a json format 
 * (title, description, image, price, is on sale, sale price)  
 */

  function get_products_by_category_endpoint( $request ) {
    $category_name = $request->get_param( 'category_name' );
    $category_id = $request->get_param( 'category_id' );

    // Check if category_name or category_id is provided
    if ( empty( $category_name ) && empty( $category_id ) ) {
        return new WP_Error( 'no_category', 'No category name or ID provided.', array( 'status' => 400 ) );
    }

    // Get the category ID if category_name is provided
    if ( ! empty( $category_name ) ) {
        $category = get_term_by( 'name', $category_name, 'product_cat' );
        if ( ! $category ) {
            return new WP_Error( 'invalid_category', 'Invalid category name.', array( 'status' => 400 ) );
        }
        $category_id = $category->term_id;
    }

    // Get the products in the category
    $args = array(
        'post_type'      => 'product',
        'posts_per_page' => -1,
        'product_cat'    => $category_id,
        'orderby'        => 'title',
        'order'          => 'ASC',
    );
    $products = get_posts( $args );

    // Create a new array with the product data to return in JSON
    $products_data = array();
    foreach ( $products as $product ) {
        $product_data = array(
            'title'       => $product->post_title,
            'description' => $product->post_excerpt,
            'image'       => get_the_post_thumbnail_url( $product->ID, 'full' ),
            'price'       => get_post_meta( $product->ID, 'product_price', true ),
            'is_on_sale'  => $product->is_on_sale(),
            'sale_price'  => get_post_meta( $product->ID, 'product_sale_price', true ),
        );
        $products_data[] = $product_data;
    }

    // Return the products data in JSON format
    return new WP_REST_Response( $products_data, 200 );
}

add_action( 'rest_api_init', function () {
    register_rest_route( 'myplugin/v1', '/products_by_category', array(
        'methods'  => 'GET',
        'callback' => 'get_products_by_category_endpoint',
    ) );
} );

  

/**
 * Create shortcoce to write out single product post content
 */
function product_gallery_shortcode( $atts ) {
    $atts = shortcode_atts( array(
      'id' => get_the_ID(),
    ), $atts );
  
    // Get the gallery images
    $gallery = get_post_gallery( $atts['id'], false );
    $gallery_images = explode( ',', $gallery['ids'] );
  
    // Output the gallery
    $output = '';
    if ( ! empty( $gallery_images ) ) {
      $output .= '<div class="product-gallery">';
      foreach ( $gallery_images as $image_id ) {
        $image_src = wp_get_attachment_image_url( $image_id, 'full' );
        $image_alt = get_post_meta( $image_id, '_wp_attachment_image_alt', true );
        $output .= '<img src="' . $image_src . '" alt="' . $image_alt . '">';
      }
      $output .= '</div>';
    }
  
    return $output;
  }
  add_shortcode( 'product_gallery', 'product_gallery_shortcode' );
  