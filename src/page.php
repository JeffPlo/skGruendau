<?php
/*
 Template Name: Volle Breite
 * @template  skGruendau
 * @revised   20.10.2015
 * @author    J.Plotnikow
 * @license   GPL, http://www.opensource.org/licenses/gpl-license
 */
?>

<?php
global $bambee, $bambeeWebsite;
get_header();

set_query_var('singlePost', $post);
get_template_part('partials/width', 'full');

get_footer();