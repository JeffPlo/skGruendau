<article id="post-<?php echo $singlePost->ID; ?>" <?php post_class('row'); ?>>
    <div class="small-12 medium-4 column">
        <?php
        if (has_post_thumbnail( $singlePost->ID ) ) {
            $thumbnailId = get_post_thumbnail_id( $singlePost->ID );
            $imageTitle = get_the_title( $thumbnailId );
            $image = wp_get_attachment_image_src( $thumbnailId, 'large' );
            $imageMedium = wp_get_attachment_image_src( $thumbnailId, 'medium' );
            ?>
            <div class="img-shadow center-block">
                <a href="<?php echo $image[0]; ?>" target="_blank">
                    <img src="<?php echo $imageMedium[0]; ?>"
                         alt="<?php echo $imageTitle; ?>" title="<?php echo $imageTitle; ?>" />
                </a>
            </div>
            <?php
        }
        ?>
    </div>
    <div class="small-12 medium-8 column">
        <h2><?php echo $singlePost->post_title; ?></h2>
        <?php
            $postContent =  apply_filters('the_content', $singlePost->post_content);
            if( $singlePost->read_more === 1 ) {
                $postContent = '<a href="'. $singlePost->guid .'">' . $postContent . '</a>';
            }
            echo $postContent;
        ?>
    </div>
</article>
<!-- #post-<?php $singlePost->ID; ?> -->
