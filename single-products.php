<?php
/*
Template Name: Single Products
*/
get_header(); ?>
<?php while ( have_posts() ) : the_post(); ?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>



    <div class="content-container" style="padding: 20px;">

        <p><strong>Product Title:</strong>
            <?php the_title(); ?></p>

        <p><strong>Description:</strong>
            <?php the_content(); ?></p>
        <p><strong>Main Image:</strong></p>
        <?php $image_url = get_post_meta( get_the_ID(), 'product_main_image', true ); ?>
        <?php if ( ! empty( $image_url ) ) : ?>
        <div style="margin-top:10px; margin-bottom:10px; max-width:300px">
            <img src="<?php echo esc_url( $image_url ); ?>" alt="<?php the_title_attribute(); ?>" />
        </div>
        <?php endif; ?>

        <p><strong>Gallery Images:</strong></p>
        <div style="margin-top:10px; margin-bottom:10px; display:grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); class="
            products-grid">
            <?php
    // Retrieve the current values of the field
  $product_image_gallery = get_post_meta( $post->ID, 'product_image_gallery', true );
  $product_image_gallery = is_array( $product_image_gallery ) ? $product_image_gallery : array();
  
  for ( $i = 1; $i <= 6; $i++ ) : 
    
  ?>
            <div style="margin: 5px;" class="product-card">
                <img src="<?php echo esc_url( $product_image_gallery[ $i - 1 ] ); ?>"
                    id="product_image_gallery_<?php echo $i; ?>_preview"
                    style="max-width: 300px; display: <?php echo ( $product_image_gallery[ $i - 1 ] ) ? 'block' : 'none'; ?>;">
            </div>
            <?php endfor; ?>
        </div>


        <p><strong>Price:</strong>
            <?php $price = get_post_meta( get_the_ID(), 'product_price', true ); ?>
            <?php if ( ! empty( $price ) ) : ?>
            $<?php echo esc_html( $price ); ?>
            <?php endif; ?></p>

        <p><strong>Sale Price:</strong>
            <?php $sale_price = get_post_meta( get_the_ID(), 'product_sale_price', true ); ?>
            <?php if ( ! empty( $sale_price ) ) : ?>
            $<?php echo esc_html( $sale_price ); ?>
            <?php endif; ?></p>

        <p><strong>Is on Sale?:</strong>
            <?php $is_on_sale = get_post_meta( get_the_ID(), 'product_is_on_sale', true ); ?>
            <?php if ( ! empty( $is_on_sale ) ) : ?>
            Yes
            <?php else : ?>
            No
            <?php endif; ?></p>

        <p><strong>YouTube Video:</strong></p>
        <?php $youtube_url = get_post_meta( get_the_ID(), 'product_youtube_video', true ); ?>
        <?php if ( ! empty( $youtube_url ) ) : ?>
        <?php echo wp_oembed_get( $youtube_url ); ?>
        <?php endif; ?>
    </div><!-- .entry-content -->

</article><!-- #post-<?php the_ID(); ?> -->

<?php endwhile; // end of the loop. ?>
<?php get_footer(); ?>