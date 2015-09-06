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

    public $season = 2015;
    public $club = array(
        array(
            'team' => '1. Mannschaft',
            'league' => 'Landesklasse Ost',
            'leagueId' => 483,
            'teamId' => 3529
        ),
        array(
            'team' => '2. Mannschaft',
            'league' => 'Bezirksklasse',
            'leagueId' => 471,
            'teamId' => 3393
        ),
        array(
            'team' => '3. Mannschaft',
            'league' => 'Kreisklasse A',
            'leagueId' => 467,
            'teamId' => 3439
        ),
        array(
            'team' => '4. Mannschaft',
            'league' => 'Kreisklasse B II',
            'leagueId' => 529,
            'teamId' => 3903
        ),
        array(
            'team' => '1. und 2. Jugendteam',
            'league' => 'MVSJ-league, Staffel A',
            'leagueId' => 501,
            'teamId' => 4009
        ),
        array(
            'team' => '3. Jugendteam',
            'league' => 'MVSJ-league, Staffel B',
            'leagueId' => 503,
            'teamId' => 4033
        ),
        array(
            'team' => '4. Jugendteam',
            'league' => 'MVSJ-league, Staffel C',
            'leagueId' => 505,
            'teamId' => 4047
        ),
    );

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
                    'src' => ThemeUrl . '/js/main.js',
                    'deps' => array( 'jquery' )
            ),
            array(
                'handle' => 'jquery-ui-js',
                'src' => ThemeUrl . '/js/partials/jquery.ui.js',
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
            ),
            array(
                'handle' => 'jquery-ui-css',
                'src' => ThemeUrl . '/css/jquery.ui.css'
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
        $separator = '<br />';

        foreach ($this->club as $team) {
            $tabLabel = $team['team'] . $separator . '<p class="subtitle">' . $team['league'] . '</p>';
            $tabData = 'data-league-id="'. $team['leagueId'] .'" data-team-id="'. $team['teamId'] .'" data-type="'. $atts['type'] .'"';

            $tabs[] = '<li><a href="#tabs-'. $team['teamId'] .'" class="team-tab not-loaded" '. $tabData .'>' . $tabLabel .'</a></li>';
            $tabs_content[] = '<div id="tabs-'. $team['teamId'] .'"></div>';
        }

        return '<div class="table-tabs"><ul>' . join("\n", $tabs) .'</ul>'. join("\n", $tabs_content) . '</div>';
    }
    
    public function getTableContent() {

        $in_charset = 'ISO-8859-1';
        $out_charset = 'utf-8';

        return;
        if('results' === $atts['type']) {
            $suchmuster = '=<p>.*</p>=siU';
            $ersetzung = '';
            $type = 'tabelle';
        }
        else {
            $suchmuster = 'href\="\/';
            $ersetzung = 'href\="http:\/\/hessen.portal64.de\/';
            $type = 'termin';
        }


        $opts = array(
            'http' => array(
                'method' => "GET",
                'header' => implode("\r\n", array(
                    'Content-type: text/plain; charset=' . $in_charset
                ))
            )
        );

        $context = stream_context_create($opts);
        $tab_nummer = 0;

        $tab_nummer++;
        $target = 'http://hessen.portal64.de/ergebnisse/show/'. $year .'/'. $nummer['leagueId'] .'/'. $type . '/plain/';
        // Test, ob der die Tabelle ausgelesen werden kann
        $tabs[] = '<li><a href="#tabs-' . $tab_nummer . '">' . $nummer['team'] . $separator . '<p class="subtitle">' . $nummer['league'] . '</p></a></li>';
        if (is_resource(@fopen($target, 'r'))) {

            // Tabelle holen
            $file_content = file_get_contents($target, false, $context);
            if ($in_charset != $out_charset) {
                $contents = iconv($in_charset, $out_charset, $file_content);
            }

            $contents = str_ireplace($suchmuster, $ersetzung, $contents);
        } else {
            // Warnung oder ähnliches ausgeben
            $contents = '<p>die Tabelle konnte nicht eingelesen werden!</p>';
        }


        $js[] = '$(\'a[href*="einzelergebnisse/' . $nummer['teamId'] . '"]\').addClass(\'bluelight\');';
    }

}