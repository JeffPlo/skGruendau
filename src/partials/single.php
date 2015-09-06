<article id="post-<?php echo $singlePost->ID; ?>" <?php post_class($singlePost->ID); ?>>
    <h1><?php echo $singlePost->post_title; ?></h1>
    <p class="single-post-content">
        <?php
            if( $singlePost->read_more === 1 ) {
                ?>
                <a href="<?php echo $singlePost->guid; ?>">
                <?php
            }
            echo apply_filters('the_content', $singlePost->post_content);
            if( $singlePost->read_more === 1 ) {
                ?>
                </a>
                <?php
            }
        ?>
    </p>
</article>
<!-- #post-<?php $singlePost->ID; ?> -->
