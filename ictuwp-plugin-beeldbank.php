<?php

/**
 * @link              https://wbvb.nl
 * @package           ictu-gc-posttypes-brieven-beelden
 *
 * @wordpress-plugin
 * Plugin Name:       ICTU / Gebruiker Centraal / Beelden en Brieven post types and taxonomies (v2)
 * Plugin URI:        https://github.com/ICTU/ICTU-Gebruiker-Centraal-Beelden-en-Brieven-CPTs-and-taxonomies
 * Description:       Eerste versie voor gebruikercentraal.nl en beeldbank.gebruikercentraal.nl voor het registeren van CPTs voor beelden en brieven
 * Version:           2.0.5
 * Version descr:     Start samenwerking met Tamara op beeldbank.
 * Author:            Paul van Buuren & Tamara de Haas
 * Author URI:        https://wbvb.nl/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ictu-gc-posttypes-brieven-beelden
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

//========================================================================================================

add_action( 'plugins_loaded', array( 'ICTU_GC_Register_posttypes_brieven_beelden', 'init' ), 10 );

//========================================================================================================

if ( ! defined( 'GC_BEELDBANK_BEELD_CPT' ) ) {
	define( 'GC_BEELDBANK_BEELD_CPT', 'beeld' );
}

if ( ! defined( 'GC_BRIEVENCONTEXT' ) ) {
	define( 'GC_BRIEVENCONTEXT', 'briefcpt' );
}

if ( ! defined( 'GC_BEELDENCONTEXT' ) ) {
	define( 'GC_BEELDENCONTEXT', 'beeldcpt' );
}

if ( ! defined( 'GC_BEELDBANK_BRIEF_CPT' ) ) {
	define( 'GC_BEELDBANK_BRIEF_CPT', 'brief' );
}

if ( ! defined( 'GC_TAX_LICENTIE' ) ) {
	define( 'GC_TAX_LICENTIE', 'licentie' );
}

if ( ! defined( 'GC_TAX_ORGANISATIE' ) ) {
	define( 'GC_TAX_ORGANISATIE', 'organisatie' );
}

define( 'ICTU_GC_BEELDBANK_CSS', 'ictu-gc-plugin-beeldbank-css' );
define( 'ICTU_GC_BEELDBANK_BASE_URL', trailingslashit( plugin_dir_url( __FILE__ ) ) );
define( 'ICTU_GC_BEELDBANK_IMAGES', esc_url( ICTU_GC_BEELDBANK_BASE_URL . 'images/' ) );
define( 'ICTU_GC_BEELDBANK_VERSION', '2.0.5' );
define( 'ICTU_GC_BEELDBANK_DESC', 'Start samenwerking met Tamara op beeldbank.' );


// nieuwe CPTs
if ( ! defined( 'ICTU_GC_CPT_STAP' ) ) {
	define( 'ICTU_GC_CPT_STAP', 'stap' );   // slug for custom taxonomy 'stap'
}

// Vertaalbaar maken van de 'niet-zo, maar-zo' labels
define( 'ICTU_GC_BEELDBANK_LABELS', array(
	"nietzo" => _x( "Niet zo", 'labels', "ictu-gc-posttypes-brieven-beelden" ),
	"maarzo" => _x( "Maar zo", 'labels', "ictu-gc-posttypes-brieven-beelden" )
) );


//========================================================================================================

// constants for rewrite rules


//========================================================================================================


/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    0.0.2
 */


if (!class_exists('ICTU_GC_Register_posttypes_brieven_beelden')) :

    class ICTU_GC_Register_posttypes_brieven_beelden {

        /**
         * @var Rijksvideo
         */
        public $gcposttypes = NULL;

        /** ----------------------------------------------------------------------------------------------------
         * Init
         */
        public static function init() {

            $gcposttypes = new self();

        }

        /** ----------------------------------------------------------------------------------------------------
         * Constructor
         */
        public function __construct() {

            $this->setup_actions();
            $this->includes();

        }

        /** ----------------------------------------------------------------------------------------------------
         * Hook this plugins functions into WordPress
         */
        private function setup_actions() {

            add_action('init', [$this, 'register_post_type']);
            add_action('init', [$this, 'acf_fields']);


            add_action('plugins_loaded', [$this, 'load_plugin_textdomain']);
            //		add_action( 'init', array( $this, 'add_rewrite_rules' ) );


            add_filter('genesis_single_crumb', [
              $this,
              'filter_breadcrumb',
            ], 10, 2);
            add_filter('genesis_page_crumb', [
              $this,
              'filter_breadcrumb',
            ], 10, 2);
            add_filter('genesis_archive_crumb', [
              $this,
              'filter_breadcrumb',
            ], 10, 2);

            //  bidirectional relations beeld & brief
            add_filter('acf/update_value/name=relation_beeldbrief_beeld', 'bidirectional_acf_update_value', 10, 3);


            $this->templates = [];
            // @since 2.0.4
            // de waarde voor 'template_home' is hetzelfde als template_home in de inclusie-plugin;
            // door deze gedeelde naam kunnen ze functies en styling delen
            $this->template_home = 'home-inclusie.php';

            // add styling and scripts
            add_action('wp_enqueue_scripts', [
              $this,
              'ictu_gc_register_frontend_style_script',
            ]);

            // add the page template to the templates list
            add_filter('theme_page_templates', [
              $this,
              'ictu_gc_add_page_templates',
            ]);

            // activate the page filters
            add_filter('template_redirect', [
              $this,
              'ictu_gc_frontend_use_page_template',
            ]);

            // disable the author pages
            add_action('template_redirect', 'ictu_gctheme_disable_author_pages');

            // add header css
            //        add_action('wp_enqueue_scripts', array( $this, 'ictu_gc_append_header_css_local' ) );
            add_action('wp_enqueue_scripts', 'ictu_gctheme_card_append_header_css');


        }

        /** ----------------------------------------------------------------------------------------------------
         * Initialise translations
         */
        public function load_plugin_textdomain() {

            load_plugin_textdomain("ictu-gc-posttypes-brieven-beelden", FALSE, dirname(plugin_basename(__FILE__)) . '/languages/');

        }


        /** ----------------------------------------------------------------------------------------------------
         * Modify page content if using a specific page template.
         */
        public function ictu_gc_frontend_use_page_template() {

            global $post;

            $page_template = get_post_meta(get_the_ID(), '_wp_page_template', TRUE);

            if ($this->template_home == $page_template) {

	            remove_filter( 'genesis_post_title_output', 'gc_wbvb_sharebuttons_for_page_top', 15 );

	            //* Remove standard header
	            remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_open', 5 );
	            remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_close', 15 );
	            remove_action( 'genesis_entry_header', 'genesis_do_post_title' );

	            //* Remove the post content (requires HTML5 theme support)
	            remove_action( 'genesis_entry_content', 'genesis_do_post_content' );

	            // dit zijn de stappen in de cirkel
	            add_action( 'genesis_entry_content', array( $this, 'ictu_gc_frontend_home_before_content' ), 8 );

	            // teasers toevoegen (paarse blokken)
	            add_action( 'genesis_loop', 'ictu_gctheme_home_template_teasers', 12 );

				// action voor het toevoegen van berichten
	            add_action( 'genesis_loop', array( $this, 'ictu_beeldbank_home_add_posts' ), 12 );

				// in page.php wordt de gerelateerde content toegevoegd via
				// add_action( 'genesis_loop', 'ictu_gctheme_frontend_general_get_related_content', 12 );


            }
            elseif (GC_BEELDBANK_BEELD_CPT == get_post_type()) {

                // alles voor een beeld

                remove_action('genesis_entry_content', 'genesis_do_post_content');

                add_action('genesis_entry_content', [
                  $this,
                  'ictu_gc_frontend_brief_append_container_start',
                ], 6);
                add_action('genesis_entry_content', [
                  $this,
                  'ictu_gc_frontend_append_afbeelding',
                ], 8);

                add_action('genesis_entry_content', [
                  $this,
                  'ictu_gc_frontend_beeld_append_downloadinfo',
                ], 12);
                add_action('genesis_entry_content', [
                  $this,
                  'ictu_gc_frontend_brief_append_container_end',
                ], 14);

                add_action('genesis_entry_content', [
                  $this,
                  'ictu_gc_frontend_brief_append_related_content',
                ], 16);

            }
            elseif (GC_BEELDBANK_BRIEF_CPT == get_post_type()) {

                // alles voor een single brief

                // remove the content
                remove_action('genesis_entry_content', 'genesis_do_post_content');

                add_action('genesis_entry_content', [
                  $this,
                  'ictu_gc_frontend_brief_append_container_start',
                ], 6);
                add_action('genesis_entry_content', [
                  $this,
                  'ictu_gc_frontend_brief_append_afbeelding',
                ], 8);

                // the content is here
                add_action('genesis_entry_content', [
                  $this,
                  'ictu_gc_frontend_brief_append_downloadinfo',
                ], 12);
                add_action('genesis_entry_content', [
                  $this,
                  'ictu_gc_frontend_brief_append_container_end',
                ], 14);

                add_action('genesis_entry_content', [
                  $this,
                  'ictu_gc_frontend_brief_append_related_content',
                ], 16);

            }
            elseif (ICTU_GC_CPT_STAP == get_post_type()) {

                //* Remove standard header
                remove_action('genesis_entry_header', 'genesis_entry_header_markup_open', 5);
                remove_action('genesis_entry_header', 'genesis_entry_header_markup_close', 15);
                remove_action('genesis_entry_header', 'genesis_do_post_title');

                // voeg pijlenschema toe
                add_action('genesis_entry_header', [
                  $this,
                  'ictu_gc_frontend_stap_append_title',
                ], 10);

                // gerelateerde myflex
                add_action('genesis_before_loop', [
                  $this,
                  'ictu_gc_frontend_stap_before_content',
                ], 8);

                // bij een richtlijn: toon de resultaatblokken
                add_action('genesis_after_entry_content', [
                  $this,
                  'ictu_gc_frontend_richtlijn_get_resultaatblokken',
                ], 12);

                // bij een stap of richtlijn: toon de juiste andere richtlijnen
                add_action('genesis_after_entry_content', [
                  $this,
                  'ictu_gc_frontend_stap_get_richtlijnen',
                ], 14);

                // gerelateerde content
                add_action('genesis_after_loop', [
                  $this,
                  'ictu_gc_frontend_stap_get_related_content',
                ], 16);

            }

        }

        /** ----------------------------------------------------------------------------------------------------
         * Hook this plugins functions into WordPress
         */
        private function includes() {

            require_once dirname(__FILE__) . '/includes/beeldbank.acf-definitions.php';

        }

        /** ----------------------------------------------------------------------------------------------------
         * Hides the custom post template for pages on WordPress 4.6 and older
         *
         * @param array $post_templates Array of page templates. Keys are filenames, values are translated names.
         *
         * @return array Expanded array of page templates.
         */
        function ictu_gc_add_page_templates($post_templates) {

            $post_templates[$this->template_home] = _x('Beeldbank Home page', "naam template", "ictu-gc-posttypes-brieven-beelden");
            return $post_templates;

        }


        /** ----------------------------------------------------------------------------------------------------
         * /**ƒ
         * Register frontend styles
         */
        public function ictu_gc_register_frontend_style_script() {

            global $post;
            $infooter = TRUE;

            wp_enqueue_style(ICTU_GC_BEELDBANK_CSS, trailingslashit(plugin_dir_url(__FILE__)) . 'css/frontend-beeldbank.css', [], ICTU_GC_BEELDBANK_VERSION, 'all');


            //		if (WP_DEBUG) {
            //		wp_enqueue_script('inclusie-stepchart', trailingslashit(plugin_dir_url(__FILE__)) . 'js/stepchart.js', '', ICTU_GC_INCL_VERSION, $infooter);
            //		wp_enqueue_script('inclusie-btn', trailingslashit(plugin_dir_url(__FILE__)) . 'js/btn.js', '', ICTU_GC_INCL_VERSION, $infooter);
            //		}
            //		else {
            wp_enqueue_script('inclusie-stepchart', trailingslashit(plugin_dir_url(__FILE__)) . 'js/stepchart.js', '', ICTU_GC_BEELDBANK_VERSION, $infooter);
            wp_enqueue_script('inclusie-btn', trailingslashit(plugin_dir_url(__FILE__)) . 'js/btn.js', '', ICTU_GC_BEELDBANK_VERSION, $infooter);
            //		}


        }


        /** ----------------------------------------------------------------------------------------------------
         * Do actually register the post types we need
         */
        public function register_post_type() {

            // ------------------------------------------------------
            // brieven
            $labels = [
              "name" => "Brieven",
              "singular_name" => "Brief",
              "menu_name" => "Brieven",
              "all_items" => "Alle brieven",
              "add_new" => "Toevoegen",
              "add_new_item" => "Brief toevoegen",
              "edit" => "Brief bewerken",
              "edit_item" => "Bewerk brief",
              "new_item" => "Nieuwe brief",
              "view" => "Bekijk",
              "view_item" => "Bekijk brief",
              "search_items" => "Tips zoeken",
              "not_found" => "Geen brieven gevonden",
              "not_found_in_trash" => "Geen brieven in de prullenbak",
            ];

            $currentpageID = '';
            if (function_exists('get_field')) {
                $currentpageID = get_field('brief_page_overview', 'option');
            }
            $theslug = 'brieven';
            $theslug = GC_BRIEVENCONTEXT;

            if ($currentpageID) {
                $theslug = str_replace(home_url() . '/', '', get_permalink($currentpageID)) . GC_BRIEVENCONTEXT;
            }

            $args = [
              "labels" => $labels,
              "description" => "Hier voer je de brieven in.",
              "public" => TRUE,
              "show_ui" => TRUE,
              "has_archive" => FALSE,
              "show_in_menu" => TRUE,
              "exclude_from_search" => FALSE,
              "capability_type" => "page",
              "map_meta_cap" => TRUE,
              "hierarchical" => FALSE,
              "rewrite" => ["slug" => $theslug, "with_front" => TRUE],
              "query_var" => TRUE,
              "menu_position" => 6,
              "menu_icon" => "dashicons-media-text",
              "supports" => [
                "title",
                "editor",
                "excerpt",
                "revisions",
                "thumbnail",
                "author",
              ],
            ];
            register_post_type(GC_BEELDBANK_BRIEF_CPT, $args);

            // ------------------------------------------------------
            // beelden
            $labels = [
              "name" => "Beelden",
              "singular_name" => "Beeld",
              "menu_name" => "Beelden",
              "all_items" => "Alle beelden",
              "add_new" => "Toevoegen",
              "add_new_item" => "Beeld toevoegen",
              "edit" => "Beeld bewerken",
              "edit_item" => "Bewerk beeld",
              "new_item" => "Nieuwe beeld",
              "view" => "Bekijk",
              "view_item" => "Bekijk beeld",
              "search_items" => "Tips zoeken",
              "not_found" => "Geen beelden gevonden",
              "not_found_in_trash" => "Geen beelden in de prullenbak",
            ];

            if (function_exists('get_field')) {
                $currentpageID = get_field('beelden_page_overview', 'option');
            }
            $theslug = GC_BEELDENCONTEXT;

            if ($currentpageID) {
                $theslug = str_replace(home_url() . '/', '', get_permalink($currentpageID)) . GC_BEELDENCONTEXT;
            }

            $args = [
              "labels" => $labels,
              "description" => "Hier voer je de beelden in.",
              "public" => TRUE,
              "show_ui" => TRUE,
              "has_archive" => FALSE,
              "show_in_menu" => TRUE,
              "exclude_from_search" => FALSE,
              "capability_type" => "page",
              "map_meta_cap" => TRUE,
              "hierarchical" => FALSE,
              "rewrite" => ["slug" => $theslug, "with_front" => TRUE],
              "query_var" => TRUE,
              "menu_position" => 7,
              "menu_icon" => "dashicons-format-image",
              "supports" => [
                "title",
                "editor",
                "excerpt",
                "revisions",
                "thumbnail",
                "author",
              ],
            ];
            register_post_type(GC_BEELDBANK_BEELD_CPT, $args);


            $labels = [
              "name" => _x("Licentie", 'stap type', "ictu-gc-posttypes-brieven-beelden"),
              "label" => _x("Licentie", 'stap type', "ictu-gc-posttypes-brieven-beelden"),
              "menu_name" => _x("Licentie", 'stap type', "ictu-gc-posttypes-brieven-beelden"),
              "all_items" => _x("Alle licenties", 'stap type', "ictu-gc-posttypes-brieven-beelden"),
              "edit_item" => _x("Bewerk licentie", 'stap type', "ictu-gc-posttypes-brieven-beelden"),
              "view_item" => _x("Bekijk licentie", 'stap type', "ictu-gc-posttypes-brieven-beelden"),
              "update_item" => _x("Licentie bijwerken", 'stap type', "ictu-gc-posttypes-brieven-beelden"),
              "add_new_item" => _x("Licentie toevoegen", 'stap type', "ictu-gc-posttypes-brieven-beelden"),
              "new_item_name" => _x("Nieuwe licentie", 'stap type', "ictu-gc-posttypes-brieven-beelden"),
              "search_items" => _x("Zoek licentie", 'stap type', "ictu-gc-posttypes-brieven-beelden"),
              "popular_items" => _x("Meest gebruikte licenties", 'stap type', "ictu-gc-posttypes-brieven-beelden"),
              "separate_items_with_commas" => _x("Scheid met komma's", 'stap type', "ictu-gc-posttypes-brieven-beelden"),
              "add_or_remove_items" => _x("Licentie toevoegen of verwijderen", 'stap type', "ictu-gc-posttypes-brieven-beelden"),
              "choose_from_most_used" => _x("Kies uit de meest gebruikte", 'stap type', "ictu-gc-posttypes-brieven-beelden"),
              "not_found" => _x("Niet gevonden", 'stap type', "ictu-gc-posttypes-brieven-beelden"),
            ];

            $args = [
              "labels" => $labels,
              "hierarchical" => TRUE,
              "label" => _x("Licentie", 'stap type', "ictu-gc-posttypes-brieven-beelden"),
              "show_ui" => TRUE,
              "query_var" => TRUE,
              "rewrite" => ['slug' => GC_TAX_LICENTIE, 'with_front' => TRUE],
              "show_admin_column" => FALSE,
            ];
            register_taxonomy(GC_TAX_LICENTIE, [GC_BEELDBANK_BEELD_CPT], $args);


            $labels = [
              "name" => _x('Organisatie', 'stap type', "ictu-gc-posttypes-brieven-beelden"),
              "label" => _x('Organisatie', 'stap type', "ictu-gc-posttypes-brieven-beelden"),
              "menu_name" => _x('Organisatie', 'stap type', "ictu-gc-posttypes-brieven-beelden"),
              "all_items" => _x('Alle organisaties', 'stap type', "ictu-gc-posttypes-brieven-beelden"),
              "edit_item" => _x('Bewerk organisatie', 'stap type', "ictu-gc-posttypes-brieven-beelden"),
              "view_item" => _x('Bekijk organisatie', 'stap type', "ictu-gc-posttypes-brieven-beelden"),
              "update_item" => _x('organisatie bijwerken', 'stap type', "ictu-gc-posttypes-brieven-beelden"),
              "add_new_item" => _x('organisatie toevoegen', 'stap type', "ictu-gc-posttypes-brieven-beelden"),
              "new_item_name" => _x('Nieuwe organisatie', 'stap type', "ictu-gc-posttypes-brieven-beelden"),
              "search_items" => _x('Zoek organisatie', 'stap type', "ictu-gc-posttypes-brieven-beelden"),
              "popular_items" => _x('Meest gebruikte organisaties', 'stap type', "ictu-gc-posttypes-brieven-beelden"),
              "separate_items_with_commas" => _x("Scheid met komma's", 'stap type', "ictu-gc-posttypes-brieven-beelden"),
              "add_or_remove_items" => _x('organisatie toevoegen of verwijderen', 'stap type', "ictu-gc-posttypes-brieven-beelden"),
              "choose_from_most_used" => _x('Kies uit de meest gebruikte', 'stap type', "ictu-gc-posttypes-brieven-beelden"),
              "not_found" => _x('Niet gevonden', 'stap type', "ictu-gc-posttypes-brieven-beelden"),
            ];

            $args = [
              "labels" => $labels,
              "hierarchical" => TRUE,
              "label" => _x('Organisatie', 'stap type', "ictu-gc-posttypes-brieven-beelden"),
              "show_ui" => TRUE,
              "query_var" => TRUE,
              "rewrite" => ['slug' => GC_TAX_ORGANISATIE, 'with_front' => TRUE],
              "show_admin_column" => FALSE,
            ];
            register_taxonomy(GC_TAX_ORGANISATIE, [
              GC_BEELDBANK_BEELD_CPT,
              GC_BEELDBANK_BRIEF_CPT,
            ], $args);


            // ---------------------------------------------------------------------------------------------------
            // custom post type voor 'stappen'
            $labels = [
              "name" => _x('Stappen en richtlijnen', 'stap type', "ictu-gc-posttypes-brieven-beelden"),
              "singular_name" => _x('Stap / richtlijn', 'stap type', "ictu-gc-posttypes-brieven-beelden"),
              "menu_name" => _x('Stappen en richtlijnen', 'stap type', "ictu-gc-posttypes-brieven-beelden"),
              "all_items" => _x('Alle stappen / richtlijnen', 'stap type', "ictu-gc-posttypes-brieven-beelden"),
              "add_new" => _x('Nieuwe toevoegen', 'stap type', "ictu-gc-posttypes-brieven-beelden"),
              "add_new_item" => _x('Nieuwe toevoegen', 'stap type', "ictu-gc-posttypes-brieven-beelden"),
              "edit_item" => _x('Stap / richtlijn bewerken', 'stap type', "ictu-gc-posttypes-brieven-beelden"),
              "new_item" => _x('Nieuwe stap / richtlijn', 'stap type', "ictu-gc-posttypes-brieven-beelden"),
              "view_item" => _x('Stap / richtlijn bekijken', 'stap type', "ictu-gc-posttypes-brieven-beelden"),
              "search_items" => _x('Zoek een stap / richtlijn', 'stap type', "ictu-gc-posttypes-brieven-beelden"),
              "not_found" => _x('Niets gevonden', 'stap type', "ictu-gc-posttypes-brieven-beelden"),
              "not_found_in_trash" => _x('Niets gevonden', 'stap type', "ictu-gc-posttypes-brieven-beelden"),
              "featured_image" => __('Featured image', "ictu-gc-posttypes-brieven-beelden"),
              "archives" => __('Archives', "ictu-gc-posttypes-brieven-beelden"),
              "uploaded_to_this_item" => __('Uploaded media', "ictu-gc-posttypes-brieven-beelden"),
            ];

            $args = [
              "label" => _x('Stappen en richtlijnen', 'Stappen label', "ictu-gc-posttypes-brieven-beelden"),
              "labels" => $labels,
              "menu_icon" => "dashicons-editor-ol",
              "description" => "",
              "public" => TRUE,
              "publicly_queryable" => TRUE,
              "show_ui" => TRUE,
              "show_in_rest" => FALSE,
              "rest_base" => "",
              "has_archive" => FALSE,
              "show_in_menu" => TRUE,
              "exclude_from_search" => FALSE,
              "capability_type" => "post",
              "map_meta_cap" => TRUE,
              "hierarchical" => TRUE,
              "rewrite" => ["slug" => ICTU_GC_CPT_STAP, "with_front" => TRUE],
              "query_var" => TRUE,
              "supports" => ["title", "editor", "excerpt", "page-attributes"],
            ];
            register_post_type(ICTU_GC_CPT_STAP, $args);


            // clean up after ourselves
            flush_rewrite_rules();

        }


        /** ----------------------------------------------------------------------------------------------------
         * Add rewrite rules
         */
        public function add_rewrite_rules() {


        }


        /** ----------------------------------------------------------------------------------------------------
         * filter the breadcrumb
         */
        public function filter_breadcrumb($crumb = '', $args = '') {

            global $post;

            $span_before_start = '<span class="breadcrumb-link-wrap" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
            $span_between_start = '<span itemprop="name">';
            $span_before_end = '</span>';

            $separator = '<span class="separator">&nbsp;</span>';

            if (is_singular(ICTU_GC_CPT_STAP)) {

                $crumb_last = get_the_title(get_the_id());
                $crumb_parents = '';

                if (wp_get_post_parent_id(get_the_id())) {
                    // this item has a parent
                    $currentitemhasparents = TRUE;
                    $counter = 0;

                    $currentid = wp_get_post_parent_id(get_the_id());

                    while ($currentitemhasparents) {

                        $counter++;
                        $new_currentid = $currentid;
                        //					echo 'joehoe: ' . $counter . '<br>';

                        $crumb_parents = $span_before_start . '<a href="' . get_the_permalink($currentid) . '">' . get_the_title($currentid) . '</a>' . $span_before_end . $separator . $crumb_parents;


                        if (wp_get_post_parent_id($currentid)) {
                            $new_currentid = wp_get_post_parent_id($currentid);
                        }
                        else {
                            $currentitemhasparents = FALSE;
                        }
                        $currentid = $new_currentid;
                    }
                }

                $crumb = $crumb_parents . $crumb_last;

            }

            return $crumb;

        }

        //** ---------------------------------------------------------------------------------------------------


        public function acf_fields() {

            if (function_exists('acf_add_local_field_group')):

                // code hier
            endif;

        }


        /**
         * Handles the front-end display.
         *
         * @return void
         */
        public function ictu_gc_frontend_home_before_content() {

            global $post;

            ictu_gctheme_home_template_stappen($post, ICTU_GC_BEELDBANK_IMAGES . 'stappenplan-bg-fullscreen.svg');

        }


        /**
         * Voeg berichten toe aan de homepage. Alle berichten of gefilterd op een bepaalde categorie
         *
         * @since 2.0.5
         *
         * @return void
         */
        public function ictu_beeldbank_home_add_posts() {
            global $post;

            if (function_exists('get_field')) {

                $home_template_posts = get_field('home_template_posts', $post->ID);

                if ('home_template_posts_ja' == $home_template_posts) {

                    $posts_number = get_field('home_template_posts_number', $post->ID);
                    $posts_category_filter = get_field('home_template_posts_category_filter', $post->ID);
                    $posts_title = get_field('home_template_posts_titel', $post->ID);
                    
                    if ( ! $posts_title ) {
	                    $posts_title = _x( 'Laatste nieuws en blogs', "titel boven berichten op home", 'ictu-gc-posttypes-brieven-beelden' );
                    }
                    
                    $posts_categories = [];

                    if (!($posts_number > 0 && $posts_number < 99)) {
                        $posts_number = 3;
                    }

                    $args = array(
						'post_type' => 'post',
						'posts_per_page' => $posts_number,
						'post_status' => 'publish',
						'orderby' => 'publish_date',
						'order'   => 'DESC',
                    );

                    if ('home_template_posts_category_filter_ja' == $posts_category_filter) {
                        $args['cat'] = get_field('home_template_posts_category_filter_catid', $post->ID);
                    }

                    $posts_home = new WP_query($args);

                    if ($posts_home->have_posts()) {

                        $columncounter = 'grid--col-2';
                        $countcount = $posts_home->found_posts;

                        if ($countcount < 2) {
                            $columncounter = 'grid--col-1';
                        }
                        elseif ($countcount === 4) {
                            $columncounter = 'grid--col-2';
                        }
                        elseif ($posts_home->found_posts > 2) {
                            $columncounter = 'grid--col-3';
                        }

                        echo '<h2>' . $posts_title. '</h2>';
                        echo '<div class="grid ' . $columncounter . '">';

                        $postcounter = 0;

                        while ($posts_home->have_posts()) : $posts_home->the_post();

                            $postcounter++;
                            $title_id = sanitize_title(get_the_title($post) . '-' . $post->ID);
                            $section_id = sanitize_title('post-' . $post->ID);

                            echo '<section aria-labelledby="' . $title_id . '" class="card" id="' . $section_id . '">';
                            echo '<div class="card__image"></div>';
                            echo '<div class="card__content">';
                            echo
                              '<h3 class="card__title" id="' . $title_id . '">' .
                              '<a href="' . get_permalink($post->ID) . '" class="arrow-link">' .
                              '<span class="arrow-link__text">' . get_the_title($post) . '</span>' .
                              '<span class="arrow-link__icon"></span>' .
                              '</a></h3>';
                            echo '<p>' . get_the_excerpt($post->ID) . '</p>';
                            echo '</div>';
                            echo '</section>';


                        endwhile;

                        echo '</div>';

						// algemene link naar de berichtenpagina
						echo '<p><a href="' . get_post_type_archive_link( 'post' ) . '">' . _x( 'Alle berichten', "home link naar berichten", 'ictu-gc-posttypes-brieven-beelden' ) . '</a></p>';


                        wp_reset_query();

                    }

                }

            } // if (function_exists('get_field')) {

        }




        //========================================================================================================

        /**
         * Handles the front-end display.
         *
         * @return void
         */
        public function ictu_gc_frontend_stap_before_content() {

            global $post;

            $homepageID = get_option('page_on_front');

            if (!$homepageID) {
                return;
            }

            $parentsofcurrentpage = [];

            if (wp_get_post_parent_id(get_the_id())) {
                // this step has a parent
                $currentitemhasparents = TRUE;
                $currentid = wp_get_post_parent_id(get_the_id());

                while ($currentitemhasparents) {

                    $new_currentid = $currentid;

                    $parentsofcurrentpage[] = $currentid;

                    if (wp_get_post_parent_id($currentid)) {
                        // current item heeft parent
                        $new_currentid = wp_get_post_parent_id($currentid);
                    }
                    else {
                        // current item heeft *GEEN* parent
                        $currentitemhasparents = FALSE;
                    }
                    $currentid = $new_currentid;
                }
            }

            if (function_exists('get_field')) {

                $home_stappen = get_field('home_template_stappen', $homepageID);

                if ($home_stappen):
                    $section_title = _x('Stappen', 'titel op home-pagina', 'ictu-gc-posttypes-inclusie');
                    $title_id = sanitize_title($section_title . '-' . $post->ID);
                    $stepcounter = 0;

                    echo '<div aria-labelledby="' . $title_id . '" class="stepnav">';
                    echo '<h2 id="' . $title_id . '" class="visuallyhidden">' . $section_title . '</h2>';
                    echo '<ol class="stepnav__items l-item-count-' . count($home_stappen) . '">';

                    foreach ($home_stappen as $stap):
                        unset($icon_classes);
                        $active = '';
                        $icon_classes[] = 'stepnav__icon';
                        $stepcounter++;
                        $titel = get_the_title($stap->ID);

                        if (get_field('stap_verkorte_titel', $stap->ID)) {
                            $titel = get_field('stap_verkorte_titel', $stap->ID);
                        }

                        if (get_field('stap_icon', $stap->ID)) {
                            $icon_classes[] = 'icon--' . get_field('stap_icon', $stap->ID);
                        }

                        if ($stap->ID === $post->ID) {
                            $active = 'active';
                        }

                        if (in_array($stap->ID, $parentsofcurrentpage)) {
                            $active = 'active';
                        }

                        $stepnav_item = '<li id="step_' . $stepcounter . '" class="stepnav__step">' .
                          '<a href="' . get_permalink($stap->ID) . '" class="stepnav__link ' . (($active) ? 'is-active' : '') . '" title="' . $titel . '" >' .
                          '<svg class="icon icon--stepnav" focusable="false">' .
                          '<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="' . get_stylesheet_directory_uri() . '/images/svg/stepchart/defs/svg/sprite.defs.svg#' . get_field('stap_icon', $stap->ID) . '"></use> ' .
                          '</svg> ' .
                          '<span class="stepnav__linktext">' . $titel . '</span>' .
                          '</a></li>';

                        echo $stepnav_item;

                    endforeach;

                    echo '</ol>';
                    echo '</div>';

                endif;
            }

            $stap_inleiding = get_field('stap_inleiding', $post->ID);


            // Make reusable Intro region as a data container
            echo '<div class="region region--content-top">' .
              '<div class="page-intro inleiding">' .
              '<header class="entry-header"><h1 class="entry-title">' . get_the_title() . '</h1>' . '</header>';

            if ($stap_inleiding) {
                echo $stap_inleiding;
            }

            echo '</div>'; // .region--content-top
            echo '</div>'; // #step-inleiding


        }


        //========================================================================================================

        /**
         * Adds the featured image
         *
         * @since 2.0.4
         *
         * @return void
         */
        public function ictu_gc_frontend_brief_append_afbeelding() {

            global $post;

            $imageID = get_post_thumbnail_id(get_the_id());

            if ($imageID) {

                $args = [
                  'ID' => $imageID,
                  'thumb-size' => BLOG_SINGLE_DESKTOP,
                  'cssclass' => 'flex-item',
                  'echo' => FALSE,
                  'data-group' => sanitize_title(get_the_title(get_the_id())),
                  'echo' => FALSE,
                ];
                echo ictu_gctheme_write_lightboximage($args);

            }

        }

        //========================================================================================================


        /**
         * Ends container voor brief
         *
         * @since 2.0.4
         *
         * @return void
         */
        public function ictu_gc_frontend_brief_append_downloadinfo() {

            echo '<div class="entry-content-inner"> <p>Brief downloaden</p>';
            echo genesis_do_post_content();
            echo '</div>'; // .myflex

        }


        //========================================================================================================

        /**
         * Ends container voor brief
         *
         * @since 2.0.4
         *
         * @return void
         */
        public function ictu_gc_frontend_beeld_append_downloadinfo() {

            global $post;

            $size = 'full';

            echo '<div class="entry-content-inner">';

            $imageid = get_post_thumbnail_id(get_the_id());
            $image = wp_get_attachment_image_src($imageid, 'full');

            if ($image) {

                $imagemeta = wp_get_attachment_metadata($imageid);
                $file = get_attached_file($imageid);
                $filesize = gc_wbvb_get_human_filesize(filesize($file));
                $mimetype = mime_content_type($file);
                $mimes = explode('/', $mimetype);
                $titel = get_the_title($image_id);
                $arialabel = sprintf(_x('Download %s', 'download image met titel', 'ictu-gc-posttypes-inclusie'), $titel);

                echo '<section class="download-box">';
                echo '<header>'; // .download-box
                echo '<h4>' . $titel . '</h4>';
                echo '<a href="' . $image[0] . '" class="btn btn--download" download aria-label="' . $arialabel . '">' . _x('Download', 'download image', "ictu-gc-posttypes-brieven-beelden") . '</a>';
                echo '</header>'; // .download-box

                echo '<dl>';
                echo '<dt class="visuallyhidden">' . _x('File size', 'download image', "ictu-gc-posttypes-brieven-beelden") . '</dt><dd>' . $filesize . '</<dd>';
                echo '<dt class="visuallyhidden">' . _x('File type', 'download image', "ictu-gc-posttypes-brieven-beelden") . '</dt><dd>' . strtoupper($mimes[1]) . '</<dd>';
                echo '</dl>';

                echo '</section>'; // .download-box
            }


            echo genesis_do_post_content();
            echo '</div>'; // .myflex

        }

        //========================================================================================================

        /**
         * Ends container voor brief
         *
         * @since 2.0.4
         *
         * @return void
         */
        public function ictu_gc_frontend_brief_append_related_content() {

            $args = [
            ];
            echo $this->ictu_gc_frontend_briefbeeld_append_related_content($args);

        }



        //========================================================================================================

        /**
         * Display a set of links to related content or a set of links to external sites
         *
         * This function either returns an array with links, or returns an HTML string, or echoes HTML string.
         * Can return 2 type of blocks:
         * 1. block with items for 'gerelateerde_content_toevoegen'. This is a block with content from the local site.
         * 2. block with items for 'handige_links_toevoegen'. This is a block with links to externas sites.
         *
         * @since 4.1.1
         *
         * @param array $args Argument for what to do: echo or return links or return HTML string.
         *
         * @return array $menuarray Array with links and link text (if $args['getmenu'] => TRUE).
         * @return string $return HTML string with related links (if $args['echo'] => FALSE).
         */

        function ictu_gc_frontend_briefbeeld_append_related_content($args = []) {

            global $post;

            // defaults
            $menuarray = [];
            $return = '';
            //		$return     = '<h1>ictu_gc_frontend_briefbeeld_append_related_content</h1>';
            $defaults = [
              'ID' => 0,
              'titletag' => 'h2',
              'getmenu' => FALSE,
              'echo' => TRUE,
            ];

            // Parse incoming $args into an array and merge it with $defaults
            $args = wp_parse_args($args, $defaults);


            if (function_exists('get_field')) {

                // interne links
                $related_items = get_field('relation_beeldbrief_beeld', $post->ID);

                if ($related_items) {

                    $columncounter = 'col-2';
                    $countcount = count($related_items);

                    if ($countcount < 2) {
                        $columncounter = 'col-1';
                    }
                    elseif ($countcount === 4) {
                        $columncounter = 'col-2';
                    }
                    elseif ($countcount > 2) {
                        $columncounter = 'col-3';
                    }

                    if (GC_BEELDBANK_BEELD_CPT === get_post_type()) {
                        // titel voor een beeld en bijbehorende brieven
                        $section_title = sprintf(_n('Dit beeld wordt gebruikt in de volgende brief', 'Dit beeld wordt gebruikt in de volgende brieven', $countcount, 'gebruikercentraal'), number_format_i18n($countcount));
                    }
                    else {
                        // titel voor een brief en bijbehorende beelden
                        $section_title = sprintf(_n('Deze brief gebruikt het volgende beeld', 'Deze brief gebruikt de volgende beelden', $countcount, 'gebruikercentraal'), number_format_i18n($countcount));
                    }

                    $title_id = sanitize_title($section_title . '-title');

                    $return .= '<section aria-labelledby="' . $title_id . '" class="section section--related section--related-content ' . $columncounter . '">';

                    $return .= '<div class="wrap">';
                    $return .= '<h2 id="' . $title_id . '" class="section__title">' . $section_title . '</h2>';
                    $return .= '</div>'; // class="wrap";


                    $return .= '<div class="wrap-outer">';
                    $return .= '</div>'; // class="wrap-outer";

                    $return .= '<div class="grid grid--' . $columncounter . '">';


                    // loop through the rows of data
                    foreach ($related_items as $post):

                        setup_postdata($post);

                        //					$theid          = $post->ID;
                        $section_title = get_the_title($post->ID);
                        //					$section_text   = get_the_excerpt( $theid );
                        //					$section_link   = get_sub_field( 'home_template_teaser_link' );
                        $title_id = sanitize_title($section_title);
                        //					$block_id       = sanitize_title( 'related_' . $theid );

                        if ($args['getmenu']) {
                            $menuarray[$title_id] = $section_title;
                        }
                        else {
                            $return .= ictu_gctheme_card_featuredimage($post);
                        }

                    endforeach;

                    wp_reset_postdata();

                    if (!$args['getmenu']) {

                        $return .= '</div>'; // class="grid ' . $columncounter . '">';
                        $return .= '</section>';

                    }

                }
                else {
                    // nothing
                }

                // externe links
                $handigelinks = get_field('handige_links_toevoegen', $post->ID);

                if ('ja' === $handigelinks) {

                    $section_title = get_field('links_block_title', $post->ID);
                    $title_id = sanitize_title($section_title . '-title');

                    if ($args['getmenu']) {
                        $menuarray[$title_id] = $section_title;
                    }
                    else {
                        $return .= '<section  aria-labelledby="' . $title_id . '" class="section section--related section--related-links">';
                        $return .= '<div class="wrap">';
                        $return .= '<h2 id="' . $title_id . '" class="section__title">' . $section_title . '</h2>';

                        $links_block_items = get_field('links_block_items');

                        if ($links_block_items):

                            while (have_rows('links_block_items')): the_row();

                                $item_url = get_sub_field('links_block_item_url');
                                $item_linktext = get_sub_field('links_block_item_linktext');
                                $item_description = get_sub_field('links_block_item_description');

                                $return .= '<div>';
                                $return .= '<h3><a href="' . esc_url($item_url) . '">';
                                $return .= sanitize_text_field($item_linktext) . '</a></h3>';

                                if ($item_description) {
                                    $return .= '<p>' . sanitize_text_field($item_description) . '</p>';
                                }

                                $return .= '</div>';

                            endwhile;

                        endif;

                        $return .= '</div>'; //  class="wrap";
                        $return .= '</section>'; // .section--related-links

                    }

                }
                else {
                    // nothing
                }
            } // if ( function_exists( 'get_field' ) )
            else {
                $return = 'Activeer ACF plugin';
            }

            if ($args['getmenu']) {
                return $menuarray;
            }
            elseif ($args['echo']) {
                echo $return;
            }
            else {
                return $return;
            }

        }

        //========================================================================================================


        /**
         * Adds the featured image
         *
         * @since 2.0.4
         *
         * @return void
         */
        public function ictu_gc_frontend_append_afbeelding() {

            global $post;

            $imageID = get_post_thumbnail_id(get_the_id());

            if ($imageID) {

                $args = [
                  'ID' => $imageID,
                  'thumb-size' => BLOG_SINGLE_DESKTOP,
                    //				'cssclass' 		=> 'flex-item',
                  'echo' => FALSE,
                  'data-group' => sanitize_title(get_the_title(get_the_id())),
                  'echo' => FALSE,
                ];
                echo '<div class="flex-item">';
                echo ictu_gctheme_write_lightboximage($args);
                echo '</div>'; // .flex-item

            }

        }



        //========================================================================================================

        /**
         * Handles the front-end display.
         *
         * @return void
         */
        public function ictu_gc_frontend_stap_append_title() {

            global $post;

            $section_title = _x('Tips', 'titel op Stap-pagina', 'ictu-gc-posttypes-inclusie');
            $title_id = sanitize_title($section_title . '-' . $post->ID);

            // force a title, but do not make it seeable
            echo '<h2 id="' . $title_id . '" class="visuallyhidden">' . $section_title . '</h2>';

        }


        //========================================================================================================


        /**
         * Handles the front-end display.
         *
         * @return void
         */
        public function ictu_gc_frontend_stap_get_richtlijnen($args = []) {

            global $post;
            $return = '';

            $postparentid = wp_get_post_parent_id($post);

            $args = [
              'post_type' => ICTU_GC_CPT_STAP,
              'hierarchical' => TRUE,
            ];

            if ($postparentid) {
                // this is a child ('richtlijn')
                // get the siblings
                $args['type'] = 'get siblings';
                $args['parent'] = $postparentid;
                $title = 'Bekijk de andere richtlijnen';
            }
            else {
                // this is a parent ('stap')
                // get the children
                $args['type'] = 'get children';
                $args['parent'] = $post->ID;
                $title = 'Richtlijnen';
            }

            $children = get_pages($args);

            if ($children) :

                $columncounter = 'grid--col-2';
                $countcount = count($children);

                if ($args['type'] === 'get siblings') {
                    $countcount = ($countcount - 1);
                }

                if ($countcount === 3) {
                    $columncounter = 'grid--col-3';
                }
                else {
                    $columncounter = 'grid--col-2';
                }

                echo '<div class="section section--page-children">'.
                        '<span class="bg"></span>'.
                        '<div class="l-section-top">'.
                        '<h2 class="section__title"> ' . $title . '</h2>'.
                    '</div>';

                echo '<div class="l-section-content">' .
                    '<div class="grid ' . $columncounter . '">';

                $postcounter = 0;

                foreach ($children as $page) {

                    $postcounter++;

                    if ($page->ID === $post->ID) {
                        continue;
                    }

                    $title_id = sanitize_title(get_the_title($page) . '-' . $page->ID);
                    $section_id = sanitize_title('post-' . $page->ID);

                    $card = ':D';

                    $card = '<div aria-labelledby="' . $title_id . '" class="card card--featured-image" id="' . $section_id . '">';
                    $card .= '<div class="card__content">';
                    $card .='<h2 class="card__title" id="' . $title_id . '">' .
                    '<a class="arrow-link" href="' . get_permalink($page->ID) . '">' .
                    '<span class="arrow-link__text">' . get_the_title($page) . '</span>' .
                    '<span class="arrow-link__icon"></span>' .
                    '</a></h2>';
                    $card .='<p>' . get_the_excerpt($page->ID) . '</p>';
                    $card .= '</div></div>';

                    echo $card;
                }

                echo '</div></div></div>';

            endif;

        }

        //========================================================================================================


        /**
         * Handles the front-end display.
         *
         * @return void
         */
        public function ictu_gc_frontend_richtlijn_get_resultaatblokken($args = []) {

            global $post;

            $homepageID = $post->ID;

            $defaults = [
              'ID' => 0,
              'titletag' => 'h2',
              'getmenu' => FALSE,
              'echo' => TRUE,
            ];

            // Parse incoming $args into an array and merge it with $defaults
            $args = wp_parse_args($args, $defaults);

            $resultaatblokken = get_field('richtlijn_resultaatblokken', $homepageID);

            if ($resultaatblokken):

                $stepcounter = 0;

                // 3 blokken of meer mogen gestapeld worden, met een grote boven en twee kleine onder
                $itemcount = ' item-count-veel';

                if (!(count($resultaatblokken) % 2)) {
                    // even aantal blokken mogen naast elkaar, met elk 50% breedte
                    $itemcount = ' item-count-even';
                }
                elseif (1 === count($resultaatblokken)) {
                    // 1 enkel blok is 100% breed
                    $itemcount = ' item-count-1';
                }


                echo '<div class="section--resultaten' . $itemcount . '">';

                foreach ($resultaatblokken as $resultaatblok):
                    the_row();

                    $stepcounter++;

                    $titel = $resultaatblok['richtlijn_resultaatblok_titel'];
                    $text = $resultaatblok['richtlijn_resultaatblok_tekst'];
                    $image = $resultaatblok['richtlijn_resultaatblok_afbeelding'];
                    $label = $resultaatblok['richtlijn_resultaatblok_label'];

                    $size = 'medium';

                    $title_id = sanitize_title($titel);

                    echo '<div class="section--resultaten_block">';

                    if ($image) {

                        // thumbnail
                        $thumb = $image['sizes'][$size];
                        $width = $image['sizes'][$size . '-width'];
                        $height = $image['sizes'][$size . '-height'];
                        $labeltext = '';
                        $cssclass = '';

                        if ('geen' === $label) {
                        }
                        else {
                            $labeltext = '<p class="label ' . $label . '">' . ICTU_GC_BEELDBANK_LABELS[$label] . '</p>';
                        }

                        echo '<div class="resultaten_block_image">';

                        $alt = get_post_meta($image['ID'], '_wp_attachment_image_alt', '_wp_attachment_image_alt');

                        if (!$alt) {
                            $cssclass = 'missing-alt-desc-for-image';
                            $alt = sprintf(_x('Image %s for \'%s\'', 'Alternative alt-description.', 'ictu-gc-posttypes-inclusie'), $stepcounter, get_the_title($post->ID));
                        }

                        $args2 = [
                          'ID' => $image['ID'],
                          'echo' => FALSE,
                          'data-group' => sanitize_title(get_the_title()),
                          'echo' => FALSE,
                          'thumb-size' => $size,
                          'alt' => $alt,
                          'cssclass' => $cssclass,
                        ];

                        echo ictu_gctheme_write_lightboximage($args2);

                        echo $labeltext;

                        echo '</div>'; // .resultaten_block_image
                    }

                    echo '<div class="resultaten_block_text">';
                    echo '<' . $args['titletag'] . ' id="' . $title_id . '">' . $titel . '</' . $args['titletag'] . '>';
                    echo '<p>';
                    echo strip_tags($text, '<br>');
                    echo '</p>';
                    echo '</div>';
                    echo '</div>'; // .resultaten_block_text

                endforeach;

                echo '</div>';

            endif;

        }



        //========================================================================================================


        /**
         * Handles the front-end display.
         *
         * @return void
         */
        public function ictu_gc_frontend_stap_get_related_content($args = []) {

            global $post;

            $defaults = [
              'ID' => 0,
              'titletag' => 'h2',
              'getmenu' => FALSE,
              'echo' => TRUE,
            ];

            // Parse incoming $args into an array and merge it with $defaults
            $args = wp_parse_args($args, $defaults);

            // for ictu_gctheme_frontend_general_get_related_content(), see related-content-links.php in themes/gebruiker-centraal
            // @since	1.1.3
            $return = ictu_gctheme_frontend_general_get_related_content($args);

            if ($args['getmenu']) {
                return $return;
            }
            elseif ($args['echo']) {
                echo $return;
            }
            else {
                return $return;
            }


        }


        //========================================================================================================


    }

endif;

//========================================================================================================

