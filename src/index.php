<?php global $bambee, $bambeeWebsite; ?>
<?php get_header(); ?>

<?php while ( have_posts() ) : the_post() ?>
    <div class="row">
        <div class="small-12 columns">
            <?php the_content(); ?>
        </div>
    </div>
<?php endwhile; ?>

<?php get_footer(); ?>