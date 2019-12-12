<?php

/**
	* @link              https://wbvb.nl
	* @package           ictu-gc-posttypes-brieven-beelden
	*
	* @wordpress-plugin
	* Plugin Name:       ICTU / Gebruiker Centraal / Beelden en Brieven post types and taxonomies (v2)
	* Plugin URI:        https://github.com/ICTU/ICTU-Gebruiker-Centraal-Beelden-en-Brieven-CPTs-and-taxonomies
	* Description:       Eerste versie voor gebruikercentraal.nl en beeldbank.gebruikercentraal.nl voor het registeren van CPTs voor beelden en brieven
	* Version:           2.0.0
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

// nieuwe CPTs
if (!defined('ICTU_GC_CPT_STAP')) {
    define('ICTU_GC_CPT_STAP', 'stap');   // slug for custom taxonomy 'document'
}

if (!defined('ICTU_GC_BEELDBANK_IMAGES')) {
    define('ICTU_GC_BEELDBANK_IMAGES',  esc_url( plugins_url( '/images', dirname(__FILE__) ) ) );   // slug for custom taxonomy 'document'
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
		$this->includes();

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

            $this->templates = [];
            $this->template_home = 'home-inclusie.php';


    }
    
	/** ----------------------------------------------------------------------------------------------------
	* Initialise translations
	*/
	public function load_plugin_textdomain() {
	
		load_plugin_textdomain( "ictu-gc-posttypes-brieven-beelden", false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	
	}
	
	
	/** ----------------------------------------------------------------------------------------------------
	* Hook this plugins functions into WordPress
	*/
	private function includes() {
	
		require_once dirname(__FILE__) . '/includes/acffields-and-posttypes.php';
	
	}
	
	/** ----------------------------------------------------------------------------------------------------

    /** ----------------------------------------------------------------------------------------------------
     * Do actually register the post types we need
     */
    public function register_post_type() {

      // ------------------------------------------------------
      // brieven
    	$labels = array(
    		"name"             		=> "Brieven",
    		"singular_name"    		=> "Brief",
    		"menu_name"        		=> "Brieven",
    		"all_items"        		=> "Alle brieven",
    		"add_new"          		=> "Toevoegen",
    		"add_new_item"     		=> "Brief toevoegen",
    		"edit"             		=> "Brief bewerken",
    		"edit_item"        		=> "Bewerk brief",
    		"new_item"         		=> "Nieuwe brief",
    		"view"             		=> "Bekijk",
    		"view_item"        		=> "Bekijk brief",
    		"search_items"     		=> "Tips zoeken",
    		"not_found"        		=> "Geen brieven gevonden",
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
    		"labels"           		=> $labels,
    		"description"      		=> "Hier voer je de brieven in.",
    		"public"           		=> true,
    		"show_ui"          		=> true,
    		"has_archive"      		=> false,
    		"show_in_menu"     		=> true,
    		"exclude_from_search" => false,
    		"capability_type"  		=> "page",
    		"map_meta_cap"     		=> true,
    		"hierarchical"     		=> false,
    		"rewrite"          		=> array( "slug" => $theslug, "with_front" => true ),
    		"query_var"        		=> true,
    		"menu_position"    		=> 6,		
        "menu_icon"        		=> "dashicons-media-text",		
    		"supports"         		=> array( "title", "editor", "excerpt", "revisions", "thumbnail", "author" ),		
    	);
    	register_post_type( GC_KLANTCONTACT_BRIEF_CPT	, $args );
    
      // ------------------------------------------------------
      // beelden
    	$labels = array(
    		"name"             		=> "Beelden",
    		"singular_name"    		=> "Beeld",
    		"menu_name"        		=> "Beelden",
    		"all_items"        		=> "Alle beelden",
    		"add_new"          		=> "Toevoegen",
    		"add_new_item"     		=> "Beeld toevoegen",
    		"edit"             		=> "Beeld bewerken",
    		"edit_item"        		=> "Bewerk beeld",
    		"new_item"         		=> "Nieuwe beeld",
    		"view"             		=> "Bekijk",
    		"view_item"        		=> "Bekijk beeld",
    		"search_items"     		=> "Tips zoeken",
    		"not_found"        		=> "Geen beelden gevonden",
    		"not_found_in_trash"	=> "Geen beelden in de prullenbak",
    		);
    
      if ( function_exists( 'get_field' ) ) {
        $currentpageID        = get_field('beelden_page_overview', 'option');
      }    
      $theslug              = GC_BEELDENCONTEXT;
      
      if ( $currentpageID ) {
        $theslug  = str_replace( home_url() . '/', '', get_permalink( $currentpageID ) ) . GC_BEELDENCONTEXT;
      }
    
    	$args = array(
			"labels"           		=> $labels,
			"description"      		=> "Hier voer je de beelden in.",
			"public"           		=> true,
			"show_ui"          		=> true,
			"has_archive"      		=> false,
			"show_in_menu"     		=> true,
			"exclude_from_search"	=> false,
			"capability_type"  		=> "page",
			"map_meta_cap"     		=> true,
			"hierarchical"     		=> false,
			"rewrite"          		=> array( "slug" => $theslug, "with_front" => true ),
			"query_var"        		=> true,
			"menu_position"    		=> 7,		
			"menu_icon"        		=> "dashicons-format-image",		
			"supports"         		=> array( "title", "editor", "excerpt", "revisions", "author" ),		
    	);
    	register_post_type( GC_KLANTCONTACT_BEELDEN_CPT	, $args );
    
    
    
    	$labels = array(
    		"name"             		=> _x("Licentie", 'stap type', "ictu-gc-posttypes-brieven-beelden"),
    		"label"            		=> _x("Licentie", 'stap type', "ictu-gc-posttypes-brieven-beelden"),
    		"menu_name"        		=> _x("Licentie", 'stap type', "ictu-gc-posttypes-brieven-beelden"),
    		"all_items"        		=> _x("Alle licenties", 'stap type', "ictu-gc-posttypes-brieven-beelden"),
    		"edit_item"        		=> _x("Bewerk licentie", 'stap type', "ictu-gc-posttypes-brieven-beelden"),
    		"view_item"        		=> _x("Bekijk licentie", 'stap type', "ictu-gc-posttypes-brieven-beelden"),
    		"update_item"      		=> _x("Licentie bijwerken", 'stap type', "ictu-gc-posttypes-brieven-beelden"),
    		"add_new_item"     		=> _x("Licentie toevoegen", 'stap type', "ictu-gc-posttypes-brieven-beelden"),
    		"new_item_name"    		=> _x("Nieuwe licentie", 'stap type', "ictu-gc-posttypes-brieven-beelden"),
    		"search_items"     		=> _x("Zoek licentie", 'stap type', "ictu-gc-posttypes-brieven-beelden"),
    		"popular_items"    		=> _x("Meest gebruikte licenties", 'stap type', "ictu-gc-posttypes-brieven-beelden"),
    		"separate_items_with_commas" => _x("Scheid met komma's", 'stap type', "ictu-gc-posttypes-brieven-beelden"),
    		"add_or_remove_items" => _x("Licentie toevoegen of verwijderen", 'stap type', "ictu-gc-posttypes-brieven-beelden"),
    		"choose_from_most_used" => _x("Kies uit de meest gebruikte", 'stap type', "ictu-gc-posttypes-brieven-beelden"),
    		"not_found"        		=> _x("Niet gevonden", 'stap type', "ictu-gc-posttypes-brieven-beelden"),
    		);
    
    	$args = array(
    		"labels"           		=> $labels,
    		"hierarchical"     		=> true,
    		"label"            		=> _x("Licentie", 'stap type', "ictu-gc-posttypes-brieven-beelden"),
    		"show_ui"          		=> true,
    		"query_var"        		=> true,
    		"rewrite"          		=> array( 'slug' => GC_TAX_LICENTIE, 'with_front' => true ),
    		"show_admin_column"		=> false,
    	);
    	register_taxonomy( GC_TAX_LICENTIE, array( GC_KLANTCONTACT_BEELDEN_CPT ), $args );
    
    
    	$labels = array(
    		"name"             		=> _x('Organisatie', 'stap type', "ictu-gc-posttypes-brieven-beelden"),
    		"label"            		=> _x('Organisatie', 'stap type', "ictu-gc-posttypes-brieven-beelden"),
    		"menu_name"        		=> _x('Organisatie', 'stap type', "ictu-gc-posttypes-brieven-beelden"),
    		"all_items"        		=> _x('Alle organisaties', 'stap type', "ictu-gc-posttypes-brieven-beelden"),
    		"edit_item"        		=> _x('Bewerk organisatie', 'stap type', "ictu-gc-posttypes-brieven-beelden"),
    		"view_item"        		=> _x('Bekijk organisatie', 'stap type', "ictu-gc-posttypes-brieven-beelden"),
    		"update_item"      		=> _x('organisatie bijwerken', 'stap type', "ictu-gc-posttypes-brieven-beelden"),
    		"add_new_item"     		=> _x('organisatie toevoegen', 'stap type', "ictu-gc-posttypes-brieven-beelden"),
    		"new_item_name"    		=> _x('Nieuwe organisatie', 'stap type', "ictu-gc-posttypes-brieven-beelden"),
    		"search_items"     		=> _x('Zoek organisatie', 'stap type', "ictu-gc-posttypes-brieven-beelden"),
    		"popular_items"    		=> _x('Meest gebruikte organisaties', 'stap type', "ictu-gc-posttypes-brieven-beelden"),
    		"separate_items_with_commas" => _x("Scheid met komma's", 'stap type', "ictu-gc-posttypes-brieven-beelden"),
    		"add_or_remove_items" => _x('organisatie toevoegen of verwijderen', 'stap type', "ictu-gc-posttypes-brieven-beelden"),
    		"choose_from_most_used" => _x('Kies uit de meest gebruikte', 'stap type', "ictu-gc-posttypes-brieven-beelden"),
    		"not_found"        		=> _x('Niet gevonden', 'stap type', "ictu-gc-posttypes-brieven-beelden"),
    		);
    
    	$args = array(
    		"labels"           		=> $labels,
    		"hierarchical"     		=> true,
    		"label"            		=> _x('Organisatie', 'stap type', "ictu-gc-posttypes-brieven-beelden"),
    		"show_ui"          		=> true,
    		"query_var"        		=> true,
    		"rewrite"          		=> array( 'slug' => GC_TAX_ORGANISATIE, 'with_front' => true ),
    		"show_admin_column"		=> false,
    	);
    	register_taxonomy( GC_TAX_ORGANISATIE, array( GC_KLANTCONTACT_BEELDEN_CPT, GC_KLANTCONTACT_BRIEF_CPT ), $args );


        // ---------------------------------------------------------------------------------------------------
        // custom post type voor 'stappen'
        $labels = [
          "name"					=> _x('Stappen en richtlijnen', 'stap type', "ictu-gc-posttypes-brieven-beelden"),
          "singular_name"			=> _x('Stap', 'stap type', "ictu-gc-posttypes-brieven-beelden"),
          "menu_name" 				=> _x('Stappen en richtlijnen', 'stap type', "ictu-gc-posttypes-brieven-beelden"),
          "all_items" 				=> _x('Alle stappen', 'stap type', "ictu-gc-posttypes-brieven-beelden"),
          "add_new" 				=> _x('Nieuwe toevoegen', 'stap type', "ictu-gc-posttypes-brieven-beelden"),
          "add_new_item" 			=> _x('Nieuwe toevoegen', 'stap type', "ictu-gc-posttypes-brieven-beelden"),
          "edit_item" 				=> _x('Stap bewerken', 'stap type', "ictu-gc-posttypes-brieven-beelden"),
          "new_item" 				=> _x('Nieuwe stap', 'stap type', "ictu-gc-posttypes-brieven-beelden"),
          "view_item" 				=> _x('Stap bekijken', 'stap type', "ictu-gc-posttypes-brieven-beelden"),
          "search_items" 			=> _x('Zoek een stap', 'stap type', "ictu-gc-posttypes-brieven-beelden"),
          "not_found" 				=> _x('Niets gevonden', 'stap type', "ictu-gc-posttypes-brieven-beelden"),
          "not_found_in_trash"		=> _x('Niets gevonden', 'stap type', "ictu-gc-posttypes-brieven-beelden"),
          "featured_image" 			=> __('Featured image', "ictu-gc-posttypes-brieven-beelden"),
          "archives" 				=> __('Archives', "ictu-gc-posttypes-brieven-beelden"),
          "uploaded_to_this_item"		=> __('Uploaded media', "ictu-gc-posttypes-brieven-beelden"),
        ];

        $args = [
          "label" 					=> _x('Stappen en richtlijnen', 'Stappen label', "ictu-gc-posttypes-brieven-beelden"),
          "labels" 					=> $labels,
          "menu_icon" 				=> "dashicons-editor-ol",
          "description" 			=> "",
          "public" 					=> TRUE,
          "publicly_queryable"		=> TRUE,
          "show_ui" 				=> TRUE,
          "show_in_rest" 			=> FALSE,
          "rest_base" 				=> "",
          "has_archive" 			=> FALSE,
          "show_in_menu" 			=> TRUE,
          "exclude_from_search"		=> FALSE,
          "capability_type" 		=> "post",
          "map_meta_cap" 			=> TRUE,
          "hierarchical" 			=> TRUE,
          "rewrite" 				=> [ "slug" => ICTU_GC_CPT_STAP, "with_front" => TRUE ],
          "query_var" 				=> TRUE,
          "supports" 				=> [ "title", "editor", "excerpt", "page-attributes" ],
        ];
		register_post_type(ICTU_GC_CPT_STAP, $args);
		
		
		
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
		
		$span_before_start = '<span class="breadcrumb-link-wrap" itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">';
		$span_between_start = '<span itemprop="name">';
		$span_before_end = '</span>';
		
		$separator = '<span class="separator">&nbsp;</span>';
		
		if ( is_singular( ICTU_GC_CPT_STAP ) ) {
		
			$crumb_last 	= get_the_title( get_the_id() );
			$crumb_parents	= '';
			
			if ( wp_get_post_parent_id( get_the_id() ) ) {
				// this item has a parent
				$currentitemhasparents	= true;
				$counter 				= 0;
				
				$currentid 				= wp_get_post_parent_id( get_the_id() );
				
				while( $currentitemhasparents ) {
					
					$counter++;
					$new_currentid = $currentid;
//					echo 'joehoe: ' . $counter . '<br>';
					
					$crumb_parents = $span_before_start . '<a href="' . get_the_permalink( $currentid ) . '">' . get_the_title( $currentid ) . '</a>' . $span_before_end . $separator . $crumb_parents;

					
					if ( wp_get_post_parent_id( $currentid ) ) {
						$new_currentid			= wp_get_post_parent_id( $currentid );
					}					
					else {
						$currentitemhasparents	= false;
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
      
      if( function_exists('acf_add_local_field_group') ):

        // code hier
      endif;
        
    }  

}

endif;

//========================================================================================================

