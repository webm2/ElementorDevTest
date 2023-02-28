<?php
/**
 * Template Name: Products Template
 * 
 * Description: A custom page template
 */

$args = array(
  'post_type' => 'products',
  'posts_per_page' => -1
);
$query = new WP_Query($args);
if ($query->have_posts()) : ?>

  <div class="products-container">

    <?php while ($query->have_posts()) : $query->the_post(); ?>

      <div style="background-color=blue;" class="product-card">
        <h2><?php the_title(); ?></h2>
        <img src="<?php the_field('main_image'); ?>" alt="<?php the_title(); ?>">
        <div class="product-price"><?php the_field('product_price'); ?></div>
        <div class="product-description"><?php the_field('product_description'); ?></div>
      </div>

    <?php endwhile; ?>

  </div>

  <?php wp_reset_postdata(); ?>

<?php endif; ?>
