<?php
/*
 Template Name: Schulschach
 * @template  skGruendau
 * @revised   20.10.2015
 * @author    J.Plotnikow
 * @license   GPL, http://www.opensource.org/licenses/gpl-license
 */
?>

<?php
global $bambee, $bambeeWebsite;
get_header();

$args = array( 'category' => 15 ); # category 'schulschach'
$custom_posts = get_posts( $args );

$custom_posts[] = $post;
foreach($custom_posts as $custom_post) {
    set_query_var('singlePost', $custom_post);
    get_template_part('partials/single');
}

get_footer();