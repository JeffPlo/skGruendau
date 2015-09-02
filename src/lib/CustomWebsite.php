<?php
/**
 * @since 1.0.0
 * @author R4c00n <marcel.kempf93@gmail.com>
 * @licence MIT
 */
namespace Lib;


use Inc\BambeeWebsite;

/**
 * The class representing the website (user frontend).
 *
 * @since 1.0.0
 * @author R4c00n <marcel.kempf93@gmail.com>
 * @licence MIT
 */
class CustomWebsite extends BambeeWebsite {

    /**
     * @since 1.0.0
     * @return void
     */
    public function __construct() {
        # Enqueue additional scripts
        $this->scripts = array(
                array(
                        'handle' => 'vendor-js',
                        'src' => ThemeUrl . '/js/vendor.min.js',
                        'deps' => array( 'jquery' )
                ),
                array(
                        'handle' => 'main-js',
                        'src' => ThemeUrl . '/js/main.min.js',
                        'deps' => array( 'jquery' )
                )
        );

        # Enqueue additional styles
        $this->styles = array(
                array(
                        'handle' => 'vendor-css',
                        'src' => ThemeUrl . '/css/vendor.min.css'
                ),
                array(
                        'handle' => 'main-css',
                        'src' => ThemeUrl . '/css/main.min.css'
                )
        );
        parent::__construct();

        add_shortcode( 'lastposts', array($this, 'getLastPosts') );
    }

    # [posts kategorie="news" anzahl="3" zeichen="250"]
    public function getLastPosts( $atts ) {
        extract( shortcode_atts( array(
            'kategorie' => 'post',
            'anzahl' => 3,
            'zeichen' => 250
        ), $atts ) );

        global $wpdb;
        
        $sql = 'SELECT p.ID, p.post_title, p.guid, p.post_content
				FROM '. $wpdb->prefix .'terms
					INNER JOIN '. $wpdb->prefix .'term_taxonomy ON '. $wpdb->prefix .'terms.term_id = '. $wpdb->prefix .'term_taxonomy.term_id
					INNER JOIN '. $wpdb->prefix .'term_relationships AS wpr ON wpr.term_taxonomy_id = '. $wpdb->prefix .'term_taxonomy.term_taxonomy_id
					INNER JOIN '. $wpdb->prefix .'posts AS p ON p.ID = wpr.object_id
				WHERE p.post_type = "post"
					AND '. $wpdb->prefix .'term_taxonomy.taxonomy = "category"
					AND '. $wpdb->prefix .'terms.name = "'. $kategorie .'"
					AND p.post_status = "publish"
				ORDER BY p.post_date
				DESC LIMIT '. $anzahl;

        $result = $wpdb->get_results( $sql );

        $lastPosts = array();
        if( $result ) {
            foreach( $result as $key => $row ) {
                if( (int)$zeichen === 0 ) {
                    $row->read_more = 0;
                }
                else {
                    $postContent = strip_tags( $row->post_content ); //Alle HTML Tags entfernen. Nur reinen Text anzeigen lassen.
                    $row->post_content = substr( $postContent, 0, $zeichen ); //Ausgabe begrenzen
                    $row->read_more = 1;
                }

                set_query_var('singlePost', $row);
                get_template_part('partials/single');
            }
        }
    }
}