<?php
/**
 * @since 1.0.0
 * @author R4c00n <marcel.kempf93@gmail.com>
 * @licence MIT
 */
namespace Lib;


use Inc\Bambee;

/**
 * The class representing both website (user frontend) and WordPress admin.
 *
 * @since 1.0.0
 * @author R4c00n <marcel.kempf93@gmail.com>
 * @licence MIT
 */
class CustomBambee extends Bambee {
    public static $season = 2016;
    public static $club = array(
        array(
            'team' => '1. Mannschaft',
            'league' => 'Verbandsliga Nord',
            'leagueId' => 625,
            'teamId' => 4615
        ),
        array(
            'team' => '2. Mannschaft',
            'league' => 'Bezirksklasse',
            'leagueId' => 599,
            'teamId' => 4391
        ),
        array(
            'team' => '3. Mannschaft',
            'league' => 'Kreisklasse A',
            'leagueId' => 603,
            'teamId' => 4425
        ),
        array(
            'team' => '4. Mannschaft',
            'league' => 'Kreisklasse B',
            'leagueId' => 605,
            'teamId' => 4561
        ),
        array(
            'team' => '1. und 2. Jugendteam',
            'league' => 'MVSJ-Liga A (Bezirk 4)',
            'leagueId' => 657,
            'teamId' => 4841
        ),
        array(
            'team' => '3. Jugendteam',
            'league' => 'MVSJ-Liga B (Bezirk 4)',
            'leagueId' => 659,
            'teamId' => 4853
        ),
        array(
            'team' => '4. Jugendteam',
            'league' => 'MVSJ-Liga C (Bezirk 4)',
            'leagueId' => 661,
            'teamId' => 4867
        ),
    );

    /**
     * @since 1.0.0
     * @return void
     */
    public function __construct() {
        parent::__construct();

        add_action( 'init', array($this, 'disableEmojis') );
        add_action( 'wp_ajax_nopriv_get_data', array($this, 'getData') );
        add_action( 'wp_ajax_get_data', array($this, 'getData') );
    }

    public function disableEmojis() {
        remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
        remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );
        remove_action( 'wp_print_styles', 'print_emoji_styles' );
        remove_action( 'admin_print_styles', 'print_emoji_styles' );
        remove_filter( 'the_content_feed', 'wp_staticize_emoji' );
        remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );
        remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );
        add_filter( 'tiny_mce_plugins', array($this, 'disableEmojisTinymce') );
    }

    public function disableEmojisTinymce( $plugins ) {
        if ( is_array( $plugins ) ) {
            return array_diff( $plugins, array( 'wpemoji' ) );
        } else {
            return array();
        }
    }

    public function getData() {
        $nonce = filter_input(INPUT_POST, 'nonce');
        if ( !wp_verify_nonce( $nonce, "ajax-nonce")) {
            echo json_encode(array('data' => "Nonce is bad!", 'nonce' => $nonce));
            die();
        }

        $result = '';
        $callFunction = filter_input(INPUT_POST, 'function');
        switch($callFunction) {
            case 'getTableContent':
                $leagueId = filter_input(INPUT_POST, 'leagueId');
                $type = filter_input(INPUT_POST, 'type');
                $result = $this->getTableContent($type, $leagueId);
        }


        echo $result;
        wp_die();
    }

    public static function getTableContent($type, $leagueId) {
        $content = '';
        $in_charset = 'ISO-8859-1';
        $out_charset = 'utf-8';

        if('results' === $type) {
            $pattern = '=<p>.*</p>=siU';
            $replace = '';
            $type = 'tabelle';
        }
        else {
            $pattern = 'href="/';
            $replace = 'target="_blank" href="http://hessen.portal64.de/';
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

        $target = 'http://hessen.portal64.de/ergebnisse/show/'. CustomBambee::$season .'/'. $leagueId .'/'. $type . '/plain/';
        // open file
        if (is_resource(@fopen($target, 'r'))) {
            // get table content
            $file_content = file_get_contents($target, false, $context);
            if ($in_charset != $out_charset) {
                $content = iconv($in_charset, $out_charset, $file_content);
            }

            $content = str_ireplace($pattern, $replace, $content);
        } else {
            // could not load table
            $content = '<p>die Tabelle konnte nicht eingelesen werden!</p>';
        }

        return $content;
    }
}