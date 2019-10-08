<?php

/**
 * @link              https://wbvb.nl
* @package           ictu-gc-posttypes-brieven-beelden
 *
 * @wordpress-plugin
 * Plugin Name:       ICTU / Gebruiker Centraal / Beelden en Brieven post types and taxonomies
 * Plugin URI:        https://github.com/ICTU/ICTU-Gebruiker-Centraal-Beelden-en-Brieven-CPTs-and-taxonomies
 * Description:       Plugin voor gebruikercentraal.nl voor het registeren van custom post types en taxonomieën
 * Version:           0.0.2
 * Author:            Paul van Buuren
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

if ( ! defined( 'GC_KLANTCONTACT_BEELDEN_CPT' ) ) {
  define( 'GC_KLANTCONTACT_BEELDEN_CPT', 'beeld' );
}

if ( ! defined( 'GC_BRIEVENCONTEXT' ) ) {
  define( 'GC_BRIEVENCONTEXT', 'briefcpt' );
}

if ( ! defined( 'GC_BEELDENCONTEXT' ) ) {
  define( 'GC_BEELDENCONTEXT', 'beeldcpt' );
}

if ( ! defined( 'GC_KLANTCONTACT_BRIEF_CPT' ) ) {
  define( 'GC_KLANTCONTACT_BRIEF_CPT', 'brief' );
}

if ( ! defined( 'GC_TAX_LICENTIE' ) ) {
  define( 'GC_TAX_LICENTIE', 'licentie' );
}

if ( ! defined( 'GC_TAX_ORGANISATIE' ) ) {
  define( 'GC_TAX_ORGANISATIE', 'organisatie' );
}


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


if ( ! class_exists( 'ICTU_GC_Register_posttypes_brieven_beelden' ) ) :

  class ICTU_GC_Register_posttypes_brieven_beelden {
  
    /**
     * @var Rijksvideo
     */
    public $gcposttypes = null;
  
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

    }
  
    /** ----------------------------------------------------------------------------------------------------
     * Hook this plugins functions into WordPress
     */
    private function setup_actions() {

      add_action( 'init', array( $this, 'register_post_type' ) );
      add_action( 'init', array( $this, 'acf_fields' ) );
      
      
      add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );
      add_action( 'init', array( $this, 'rhswp_dossiercontext_add_rewrite_rules' ) );


      add_filter( 'genesis_single_crumb',   array( $this, 'filter_breadcrumb' ), 10, 2 );
      add_filter( 'genesis_page_crumb',     array( $this, 'filter_breadcrumb' ), 10, 2 );
      add_filter( 'genesis_archive_crumb',  array( $this, 'filter_breadcrumb' ), 10, 2 ); 				
      
      
      add_action( 'genesis_entry_content',  array( $this, 'append_content' ), 15 ); 				

    }
    
    /** ----------------------------------------------------------------------------------------------------
     * Initialise translations
     */
    public function load_plugin_textdomain() {

      load_plugin_textdomain( "ictu-gc-posttypes-brieven-beelden", false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );

    }

    /** ----------------------------------------------------------------------------------------------------
     * Do actually register the post types we need
     */
    public function register_post_type() {

      // ------------------------------------------------------
      // brieven
    	$labels = array(
    		"name"                => "Brieven",
    		"singular_name"       => "Brief",
    		"menu_name"           => "Brieven",
    		"all_items"           => "Alle brieven",
    		"add_new"             => "Toevoegen",
    		"add_new_item"        => "Brief toevoegen",
    		"edit"                => "Brief bewerken",
    		"edit_item"           => "Bewerk brief",
    		"new_item"            => "Nieuwe brief",
    		"view"                => "Bekijk",
    		"view_item"           => "Bekijk brief",
    		"search_items"        => "Tips zoeken",
    		"not_found"           => "Geen brieven gevonden",
    		"not_found_in_trash"  => "Geen brieven in de prullenbak",
    		);
    
      $currentpageID          = '';
      if ( function_exists( 'get_field' ) ) {
        $currentpageID          = get_field('brief_page_overview', 'option');
      }
      $theslug                = 'brieven';
      $theslug                = GC_BRIEVENCONTEXT;
      
      if ( $currentpageID ) {
        $theslug  = str_replace( home_url() . '/', '', get_permalink( $currentpageID ) ) . GC_BRIEVENCONTEXT;
      }
        
    	$args = array(
    		"labels"              => $labels,
    		"description"         => "Hier voer je de brieven in.",
    		"public"              => true,
    		"show_ui"             => true,
    		"has_archive"         => false,
    		"show_in_menu"        => true,
    		"exclude_from_search" => false,
    		"capability_type"     => "page",
    		"map_meta_cap"        => true,
    		"hierarchical"        => false,
    		"rewrite"             => array( "slug" => $theslug, "with_front" => true ),
    		"query_var"           => true,
    		"menu_position"       => 6,		
        "menu_icon"           => "dashicons-media-text",		
    		"supports"            => array( "title", "editor", "excerpt", "revisions", "thumbnail", "author" ),		
    	);
    	register_post_type( GC_KLANTCONTACT_BRIEF_CPT	, $args );
    
      // ------------------------------------------------------
      // beelden
    	$labels = array(
    		"name"                => "Beelden",
    		"singular_name"       => "Beeld",
    		"menu_name"           => "Beelden",
    		"all_items"           => "Alle beelden",
    		"add_new"             => "Toevoegen",
    		"add_new_item"        => "Beeld toevoegen",
    		"edit"                => "Beeld bewerken",
    		"edit_item"           => "Bewerk beeld",
    		"new_item"            => "Nieuwe beeld",
    		"view"                => "Bekijk",
    		"view_item"           => "Bekijk beeld",
    		"search_items"        => "Tips zoeken",
    		"not_found"           => "Geen beelden gevonden",
    		"not_found_in_trash"  => "Geen beelden in de prullenbak",
    		);
    
      if ( function_exists( 'get_field' ) ) {
        $currentpageID        = get_field('beelden_page_overview', 'option');
      }    
      $theslug              = GC_BEELDENCONTEXT;
      
      if ( $currentpageID ) {
        $theslug  = str_replace( home_url() . '/', '', get_permalink( $currentpageID ) ) . GC_BEELDENCONTEXT;
      }
    
    	$args = array(
    		"labels"              => $labels,
    		"description"         => "Hier voer je de beelden in.",
    		"public"              => true,
    		"show_ui"             => true,
    		"has_archive"         => false,
    		"show_in_menu"        => true,
    		"exclude_from_search" => false,
    		"capability_type"     => "page",
    		"map_meta_cap"        => true,
    		"hierarchical"        => false,
    		"rewrite"             => array( "slug" => $theslug, "with_front" => true ),
    		"query_var"           => true,
    		"menu_position"       => 7,		
        "menu_icon"           => "dashicons-format-image",		
    		"supports"            => array( "title", "editor", "excerpt", "revisions", "author" ),		
    	);
    	register_post_type( GC_KLANTCONTACT_BEELDEN_CPT	, $args );
    
    
    
    	$labels = array(
    		"name"                => "Licentie",
    		"label"               => "Licentie",
    		"menu_name"           => "Licentie",
    		"all_items"           => "Alle licenties",
    		"edit_item"           => "Bewerk licentie",
    		"view_item"           => "Bekijk licentie",
    		"update_item"         => "Licentie bijwerken",
    		"add_new_item"        => "Licentie toevoegen",
    		"new_item_name"       => "Nieuwe licentie",
    		"search_items"        => "Zoek licentie",
    		"popular_items"       => "Meest gebruikte licenties",
    		"separate_items_with_commas" => "Scheid met komma's",
    		"add_or_remove_items" => "Licentie toevoegen of verwijderen",
    		"choose_from_most_used" => "Kies uit de meest gebruikte",
    		"not_found"           => "Niet gevonden",
    		);
    
    	$args = array(
    		"labels"              => $labels,
    		"hierarchical"        => true,
    		"label"               => "Licentie",
    		"show_ui"             => true,
    		"query_var"           => true,
    		"rewrite"             => array( 'slug' => GC_TAX_LICENTIE, 'with_front' => true ),
    		"show_admin_column"   => false,
    	);
    	register_taxonomy( GC_TAX_LICENTIE, array( GC_KLANTCONTACT_BEELDEN_CPT ), $args );
    
    
    	$labels = array(
    		"name"                => "Organisatie",
    		"label"               => "Organisatie",
    		"menu_name"           => "Organisatie",
    		"all_items"           => "Alle organisaties",
    		"edit_item"           => "Bewerk organisatie",
    		"view_item"           => "Bekijk organisatie",
    		"update_item"         => "organisatie bijwerken",
    		"add_new_item"        => "organisatie toevoegen",
    		"new_item_name"       => "Nieuwe organisatie",
    		"search_items"        => "Zoek organisatie",
    		"popular_items"       => "Meest gebruikte organisaties",
    		"separate_items_with_commas" => "Scheid met komma's",
    		"add_or_remove_items" => "organisatie toevoegen of verwijderen",
    		"choose_from_most_used" => "Kies uit de meest gebruikte",
    		"not_found"           => "Niet gevonden",
    		);
    
    	$args = array(
    		"labels"              => $labels,
    		"hierarchical"        => true,
    		"label"               => "Organisatie",
    		"show_ui"             => true,
    		"query_var"           => true,
    		"rewrite"             => array( 'slug' => GC_TAX_ORGANISATIE, 'with_front' => true ),
    		"show_admin_column"   => false,
    	);
    	register_taxonomy( GC_TAX_ORGANISATIE, array( GC_KLANTCONTACT_BEELDEN_CPT, GC_KLANTCONTACT_BRIEF_CPT ), $args );
    
    
      // ---------------------------------------------------------------------------------------------------

      // clean up after ourselves
    	flush_rewrite_rules();
  
    }


    /** ----------------------------------------------------------------------------------------------------
     * Add rewrite rules
     */
    public function rhswp_dossiercontext_add_rewrite_rules() {
    
  
    }


    /** ----------------------------------------------------------------------------------------------------
     * filter the breadcrumb
     */
    public function append_content( $thecontent) {

      global $post;
      
      // code hier

      return $thecontent;

    }
    


    /** ----------------------------------------------------------------------------------------------------
     * filter the breadcrumb
     */
    public function filter_breadcrumb( $crumb = '', $args = '' ) {

      global $post;
      
      // code hier

      return $crumb;

    }
    
    //** ---------------------------------------------------------------------------------------------------


    public function acf_fields() {
      
      if( function_exists('acf_add_local_field_group') ):

        // code hier
      endif;
        
    }  

}

endif;

//========================================================================================================

