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
	* Version descr:     First setup of TOC menu added.
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

define( 'ICTU_GC_BEELDBANK_CSS',		'ictu-gc-plugin-beeldbank-css' );  
define( 'ICTU_GC_BEELDBANK_BASE_URL',   trailingslashit( plugin_dir_url( __FILE__ ) ) );
define( 'ICTU_GC_BEELDBANK_IMAGES',		esc_url( ICTU_GC_BEELDBANK_BASE_URL . 'images/' ) ); 
define( 'ICTU_GC_BEELDBANK_VERSION',	'2.0.0' );
define( 'ICTU_GC_BEELDBANK_DESC',		'First setup of TOC menu added.' );


// nieuwe CPTs
if (!defined('ICTU_GC_CPT_STAP')) {
    define('ICTU_GC_CPT_STAP', 'stap');   // slug for custom taxonomy 'document'
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

		// add styling and scripts
		add_action('wp_enqueue_scripts', array( $this, 'ictu_gc_register_frontend_style_script' ) );
		
		// add the page template to the templates list
		add_filter('theme_page_templates', array( $this, 'ictu_gc_add_page_templates' ) );
		
		// activate the page filters
		add_filter('template_redirect', array( $this, 'ictu_gc_frontend_use_page_template' ) );
		
        

    }
    
	/** ----------------------------------------------------------------------------------------------------
	* Initialise translations
	*/
	public function load_plugin_textdomain() {
	
		load_plugin_textdomain( "ictu-gc-posttypes-brieven-beelden", false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	
	}
	

	/** ----------------------------------------------------------------------------------------------------
	 * Modify page content if using a specific page template.
	 */
	public function ictu_gc_frontend_use_page_template() {

		global $post;
		
		$page_template = get_post_meta(get_the_ID(), '_wp_page_template', TRUE);
		
		if ($this->template_home == $page_template) {

			remove_filter('genesis_post_title_output', 'gc_wbvb_sharebuttons_for_page_top', 15 );
			
			//* Remove standard header
			remove_action('genesis_entry_header',	'genesis_entry_header_markup_open', 5 );
			remove_action('genesis_entry_header',	'genesis_entry_header_markup_close', 15 );
			remove_action('genesis_entry_header',	'genesis_do_post_title');
			
			//* Remove the post content (requires HTML5 theme support)
			remove_action('genesis_entry_content', 'genesis_do_post_content');
			
			// append content
			add_action( 'genesis_entry_content',	array( $this, 'ictu_gc_frontend_home_before_content' ), 8 );
			add_action( 'genesis_after_content',	array( $this, 'ictu_gc_frontend_home_after_content' ), 12 );
			
		}
		
	}
	
	/** ----------------------------------------------------------------------------------------------------
	* Hook this plugins functions into WordPress
	*/
	private function includes() {
	
		require_once dirname(__FILE__) . '/includes/acffields-and-posttypes.php';
	
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
    /**ƒ
     * Register frontend styles
     */
    public function ictu_gc_register_frontend_style_script() {

        global $post;
		$infooter = TRUE;

		wp_enqueue_style( ICTU_GC_BEELDBANK_CSS, trailingslashit(plugin_dir_url(__FILE__)) . 'css/frontend-beeldbank.css', [], ICTU_GC_BEELDBANK_VERSION, 'all');
		
		
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
	
	/**
	* Handles the front-end display.
	*
	* @return void
	*/
	public function ictu_gc_frontend_home_before_content() {

		global $post;
	
	    if (function_exists('get_field')) {
	
	        $home_inleiding = get_field('home_template_inleiding', $post->ID);
	        $home_stappen = get_field('home_template_stappen', $post->ID);
	        $home_template_poster = get_field('home_template_poster', $post->ID);
	        $home_template_poster_linktekst = get_field('home_template_poster_linktekst', $post->ID);
	
	        echo '<div class="region region--intro">' .
	          '<div id="entry__intro">' .
	          '<h1 class="entry-title">' . get_the_title() . '</h1>';
	
	
	        if ($home_inleiding) {
	            echo $home_inleiding;
	        }
	
	        if ($home_template_poster && $home_template_poster_linktekst) {
	            echo '<a href="' . $home_template_poster['url'] . '" class="btn btn--download">' . $home_template_poster_linktekst . '</a>';
	        }
	
	        echo '</div>'; // Einde Intro
	
	        if ($home_stappen):
	
	            $section_title = _x('Stappen', 'titel op home-pagina', 'ictu-gc-posttypes-inclusie');
	            $title_id = sanitize_title($section_title . '-' . $post->ID);
	            $stepcounter = 0;
	
	            echo '<div aria-labelledby="' . $title_id . '" class="stepchart">';
	            echo '<h2 id="' . $title_id . '" class="visuallyhidden">' . $section_title . '</h2>';
	
	            echo '<div class="stepchart__bg">' .
	              // Dit kan vast beter..  Paul? :)
	              '<img src="' . ICTU_GC_BEELDBANK_IMAGES . '/stappenplan-bg-fullscreen.svg" alt="Stepchart Background">' .
	              '</div>';
	
	            echo '<ol class="stepchart__items" role="tablist">';
	
	            foreach ($home_stappen as $stap):
	
	                $stepcounter++;
	
	                if (get_field('stap_verkorte_titel', $stap->ID)) {
	                    $titel = get_field('stap_verkorte_titel', $stap->ID);
	                }
	                else {
	                    $titel = get_the_title($stap->ID);
	                }
	
	                $class = 'deel';
	                if (get_field('stap_icon', $stap->ID)) {
	                    $class = get_field('stap_icon', $stap->ID);
	                }
	
	
	                if (get_field('stap_inleiding', $stap->ID)) {
	                    $inleiding = get_field('stap_inleiding', $stap->ID);
	                }
	                else {
	                    $stap_post = get_post($stap->ID);
	                    $content = $stap_post->post_content;
	                    $inleiding = apply_filters('the_content', $content);
	                }
	
	                $xtraclass = ' hidden';
	                $title_id = sanitize_title(get_the_title($stap->ID) . '-' . $stepcounter);
	                $steptitle = sprintf(_x('%s. %s', 'Label stappen', 'ictu-gc-posttypes-inclusie'), $stepcounter, $titel);
	                $readmore = sprintf(_x('%s <span class="visuallyhidden">over %s</span>', 'home lees meer', 'ictu-gc-posttypes-inclusie'), _x('Lees meer', 'home lees meer', 'ictu-gc-posttypes-inclusie'), get_the_title($stap->ID));
	
	
	                echo '<li class="stepchart__item">';
	
	                echo '<button class="stepchart__button btn btn--stepchart ' . $class . '" aria-selected="false" role="tab">' .
	                  '<span class="btn__icon"></span>' .
	                  '<span class="btn__text">' . $steptitle . '</span>' .
	                  '</button>';
	
	                echo '<section class="stepchart__description" aria-hidden="true" aria-labelledby="' . $title_id . '" role="tabpanel">' .
	                  '<button type="button" class="btn btn--close" data-trigger="action-popover-close">Sluit</button>' .
	                  '<h3 id="' . $title_id . '" class="stepchart__title">' . get_the_title($stap->ID) . '</h3>' .
	                  '<div class="description">' . $inleiding . '</div>' .
	                  '<a href="' . get_permalink($stap->ID) . '" class="cta">' . $readmore . '</a>' .
	                  '</section>';
	
	                echo '</li>';
	
	            endforeach;
	
	            echo '</ol>';
	            echo '</div>';
	
	        endif;
	
	
	        echo '</div>'; // region--intro, lekker herbruikbaar!
	
	        if (have_rows('home_template_doelgroepen')):
	
	            $section_title = _x('Doelgroepen', 'titel op Stap-pagina', 'ictu-gc-posttypes-inclusie');
	            $title_id = sanitize_title($section_title . '-' . $post->ID);
	            $posttype = '';
	
	            echo '<div class="region region--content-top">' .
	
	              '<div class="overview">' .
	              // Items
	              '<div class="overview__items grid grid--col-3">';
	
	            // loop through the rows of data
	            while (have_rows('home_template_doelgroepen')) : the_row();
	
	                $doelgroep = get_sub_field('home_template_doelgroepen_doelgroep');
	                $citaat = get_sub_field('home_template_doelgroepen_citaat');
	
	                echo $this->ictu_gc_doelgroep_card($doelgroep, $citaat);
	
	            endwhile;
	
	            echo '</div>';
	
	            $doelgroeplink = get_post_type_archive_link(ICTU_GC_CPT_DOELGROEP);
	            $label = _x('Alle doelgroepen', 'Linktekst doelgroepoverzicht', 'ictu-gc-posttypes-inclusie'); // $obj->name;
	            $doelgroeppaginaid = get_field('themesettings_inclusie_doelgroeppagina', 'option');
	
	            if ($doelgroeppaginaid) {
	                $doelgroeplink = get_permalink($doelgroeppaginaid);
	                $label = get_the_title($doelgroeppaginaid);
	            }
	
	
	            echo '<a href="' . $doelgroeplink . '" class="cta ' . $posttype . '">' . $label . '</a>';
	        endif;
	
	        echo '</div>'; // Section content-top
	
	
	    }	
	}
	/**
	* Handles the front-end display.
	*
	* @return void
	*/
	public function ictu_gc_frontend_home_after_content() {
	
		global $post;
	
	}

    

}

endif;

//========================================================================================================

