<?php
/*
 * Plugin Name: Suche nach Shortcodes
 * Plugin URI: 
https://darkwolfcave.de/eigenes-wordpress-plugin-einfach-erstellen/
 * Description: Suche nach Shortcodes
 * Version: 0.3
 * Author: DarkWolfCave
 * Author URI: https://darkwolfcave.de
*/

if (!defined('ABSPATH')) die('No direct access allowed');
function shortcode_search() {
    if (!current_user_can('manage_options')) {
        // Der Benutzer hat nicht die erforderliche Berechtigung
        echo 'Du hast keine Berechtigung für diese Seite.';
        return;
    }

    $shortcodes_by_page = array();

  //  $pages_query = new WP_Query(array('post_type' => array('page',
//'post'), 'posts_per_page' => -1));
    $pages_query = new WP_Query(array('post_type' => array('page', 'post'), 'posts_per_page' => -1, 'post_status' => 'publish'));

    if ($pages_query->have_posts()) {
        while ($pages_query->have_posts()) {
            $pages_query->the_post();
            $content = get_the_content();
            $shortcodes = get_all_shortcodes($content);

            if (!empty($shortcodes)) {
                $page_id = get_the_ID();
                $page_title = get_the_title();
                $page_url = get_edit_post_link($page_id);

                foreach ($shortcodes as $shortcode) {

                    // Verhindere Duplikate
                    $found = false;
                    foreach ($shortcodes_by_page[$shortcode] as 
$existing) {
                        if ($existing['title'] === $page_title) {
                            $found = true;
                            break;
                        }
                    }

                    if (!$found) {
                        $shortcodes_by_page[$shortcode][] = array(
                            'title' => $page_title,
                            'url' => $page_url
                        );
                    }
                }
            }
        }
        wp_reset_postdata();
    }

    // Ausgabe der Dropdown-Listen und der Tabelle
    echo '<form>';
    echo '<label for="shortcode-dropdown">Shortcode 
auswählen:</label>';
    echo '<select id="shortcode-dropdown">';
    echo '<option value="">-- Shortcode auswählen --</option>';

    foreach ($shortcodes_by_page as $shortcode => $pages) {
        echo '<option value="' . esc_attr($shortcode) . '">' . 
esc_html($shortcode) . '</option>';
    }

    echo '</select>';
    echo '</form>';

    echo '<table id="shortcode-table">';
    echo '<tr><th>Seiten</th></tr>';

    foreach ($shortcodes_by_page as $shortcode => $pages) {
        echo '<tr class="shortcode-table-row" id="shortcode-row-' . 
esc_attr($shortcode) . '" style="display: none;">';
        echo '<td>';
        foreach ($pages as $page) {
            echo '<a href="' . esc_url($page['url']) . '" 
target="_blank">' . esc_html($page['title']) . '</a><br>';
        }
        echo '</td>';
        echo '</tr>';
    }

    echo '</table>';
}

function get_all_shortcodes($content) {
    $pattern = get_shortcode_regex();
    preg_match_all("/$pattern/", $content, $matches);
    return $matches[2];
}

add_action('admin_menu', 'shortcode_search_menu');
add_action('admin_enqueue_scripts', 'load_shortcode_search_script');

function shortcode_search_menu() {
    add_menu_page('Shortcode Search', 'Shortcode Search', 
'manage_options', 'shortcode_search', 'shortcode_search');
}

function load_shortcode_search_script() {
    wp_enqueue_script('shortcode-search-script', 
plugins_url('shortcode-search.js', __FILE__));
}
?>
