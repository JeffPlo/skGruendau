<article id="post-<?php echo $singlePost->ID; ?>" <?php post_class('row'); ?>>
    <div class="small-12 column">
        <h1><?php echo $singlePost->post_title; ?></h1>
        <p class="single-post-content">
            <?php echo apply_filters('the_content', $singlePost->post_content); ?>
        </p>
    </div>
</article>
<!-- #post-<?php $singlePost->ID; ?> -->