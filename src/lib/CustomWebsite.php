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

        $this->localizedScripts = array(
            array(
                'handle' => 'admin-ajax',
                'name' => 'adminAjax',
                'data' => array(
                    'ajaxurl' => admin_url('admin-ajax.php'),
                    'nonce' => wp_create_nonce('ajax-nonce')
                ),
            )
        );

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
            ),
            array(
                'handle' => 'jquery-ui-js',
                'src' => ThemeUrl . '/js/partials/jquery.ui.js',
                'deps' => array( 'jquery' )
            ),
            array(
                'handle' => 'admin-ajax',
                'src' => admin_url('admin-ajax.php')
            )
        );

        # Enqueue additional styles
        $this->styles = array(
            array(
                    'handle' => 'vendor',
                    'src' => ThemeUrl . '/css/vendor.min.css'
            ),
            array(
                    'handle' => 'main',
                    'src' => ThemeUrl . '/css/main.min.css'
            ),
            array(
                'handle' => 'jquery-ui',
                'src' => ThemeUrl . '/css/vendor/jquery-ui.css'
            )
        );

        parent::__construct();

        add_shortcode( 'lastposts', array($this, 'getLastPosts') );
        add_shortcode( 'table', array($this, 'getTable') );
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

    public function getTable($atts) {
        extract( shortcode_atts( array(
            'type' => 'matches'
        ), $atts ) );

        $tabs = array();
        $tabsContent = array();
        $separator = '<br />';

        $tabContent = '';
        foreach (CustomBambee::$club as $team) {
            $tabLabel = $team['team'] . $separator . '<p class="subtitle">' . $team['league'] . '</p>';
            $tabData = 'data-league-id="'. $team['leagueId'] .'" data-team-id="'. $team['teamId'] .'" data-type="'. $atts['type'] .'"';
            if(empty($tabContent)) {
                $tabContent = CustomBambee::getTableContent($atts['type'], $team['leagueId']);
            }

            $tabs[] = '<li><a href="#tabs-'. $team['teamId'] .'" class="team-tab not-loaded" '. $tabData .'>' . $tabLabel .'</a></li>';
            $tabsContent[] = '<div id="tabs-'. $team['teamId'] .'">'. $tabContent .'</div>';
            $tabContent = '<div class="loader"></div>';
        }

        return '<div class="table-tabs"><ul>' . join("\n", $tabs) .'</ul>'. join("\n", $tabsContent) . '</div>';
    }
}