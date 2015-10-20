<article id="post-<?php echo $singlePost->ID; ?>" <?php post_class('row'); ?>>
    <div class="small-12 medium-4 column">
        <?php
        if (has_post_thumbnail( $singlePost->ID ) ) {
            $image = wp_get_attachment_image_src( get_post_thumbnail_id( $singlePost->ID ), 'large' );
            ?>
            <div class="img-shadow center-block">
                <a href="<?php echo $image[0]; ?>" target="_blank">
                    <?php echo get_the_post_thumbnail( $singlePost->ID, 'medium' ); ?>
                </a>
            </div>
            <?php
        }
        ?>
    </div>
    <div class="small-12 medium-8 column">
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
    </div>
</article>
<!-- #post-<?php $singlePost->ID; ?> -->
