<?php

/**
 * @link              https://wbvb.nl
* @package           ictu-gc-posttypes-brieven-beelden
 *
 * @wordpress-plugin
 * Plugin Name:       ICTU / Gebruiker Centraal Beelden en Brieven post types and taxonomies
 * Plugin URI:        https://github.com/ICTU/Gebruiker-Centraal---beelden-brieven---custom-post-types-taxonomies
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
      
      if ( is_singular( ICTU_GC_CPT_CITAAT ) ) {

        if ( function_exists( 'get_field' ) ) {

          $auteur = get_field( 'citaat_auteur', $post->ID );
          
          if ( $auteur ) {
            echo '<p><cite>' . $auteur . '</cite></p>';
          }
          
        }
        
      }
      elseif ( is_singular( ICTU_GC_CPT_STAP ) ) {
      }
      elseif ( is_singular( ICTU_GC_CPT_DOELGROEP ) ) {
      }
      elseif ( is_singular( ICTU_GC_CPT_VAARDIGHEID ) ) {
      }
      elseif ( is_singular( ICTU_GC_CPT_METHODE ) ) {
      }
      
      if ( is_singular( ICTU_GC_CPT_STAP ) || is_singular( 'page' ) ) {
        
        $gerelateerdecontent = get_field( 'gerelateerde_content_toevoegen', $post->ID );
        
        if ( $gerelateerdecontent == 'ja' ) {

          $section_title  = get_field( 'content_block_title', $post->ID );
          $title_id       = sanitize_title( $section_title . '-title' );
          
          echo '<section aria-labelledby="' . $title_id . '">';
          echo '<h2 id="' . $title_id . '">' . $section_title . '</h2>';
          
          $posts = get_field('content_block_items');
  
          if( $posts ): 

            echo '<ul>';

            foreach( $posts as $p ): 
            
              echo '<li> <a href="' . get_permalink( $p->ID ) . '">' . get_the_title( $p->ID ) . '</a></li>';
              
            endforeach;

            echo '</ul>';

          endif; 

          echo '</section>';

        }
        
        $handigelinks = get_field( 'handige_links_toevoegen', $post->ID );
        
        if ( $handigelinks == 'ja' ) {

          $section_title  = get_field( 'links_block_title', $post->ID );
          $title_id       = sanitize_title( $section_title . '-title' );
          
          echo '<section aria-labelledby="' . $title_id . '">';
          echo '<h2 id="' . $title_id . '">' . $section_title . '</h2>';
          
          $links_block_items = get_field('links_block_items');
  
          if( $links_block_items ): 

            echo '<ul>';

            while( have_rows('links_block_items') ): the_row();
            
            	$links_block_item_url         = get_sub_field('links_block_item_url');
            	$links_block_item_linktext    = get_sub_field('links_block_item_linktext');
            	$links_block_item_description = get_sub_field('links_block_item_description');

              echo '<li> <a href="' . esc_url( $links_block_item_url ) . '">' . sanitize_text_field( $links_block_item_linktext ) . '</a>';
              
              if ( $links_block_item_description ) {
                echo '<br>' . sanitize_text_field( $links_block_item_description );
              }
              
              echo '</li>';
            	
            
            endwhile;

            echo '</ul>';
            
          endif; 

          echo '</section>';

        }        
      }
      
/*

if ( ! defined( 'ICTU_GC_CPT_VAARDIGHEID' ) ) {
  define( 'ICTU_GC_CPT_VAARDIGHEID', 'vaardigheid' );  // slug for custom post type 'doelgroep'
}

if ( ! defined( 'ICTU_GC_CPT_METHODE' ) ) {
  define( 'ICTU_GC_CPT_METHODE', 'methode' );  // slug for custom post type 'doelgroep'
}

*/        

      return $thecontent;

    }
    


    /** ----------------------------------------------------------------------------------------------------
     * filter the breadcrumb
     */
    public function filter_breadcrumb( $crumb = '', $args = '' ) {

      global $post;
      
      if ( is_singular( ICTU_GC_CPT_CITAAT ) || is_singular( ICTU_GC_CPT_STAP ) ) {
        
        $crumb = get_the_title( get_the_id() ) ;
        
      }

      return $crumb;

    }
    
    //** ---------------------------------------------------------------------------------------------------


    public function acf_fields() {
      
      if( function_exists('acf_add_local_field_group') ):
        
        acf_add_local_field_group(array(
        	'key' => 'group_5c8f882222034',
        	'title' => 'Citaten',
        	'fields' => array(
        		array(
        			'key' => 'field_5c8f882d1c131',
        			'label' => 'citaat auteur',
        			'name' => 'citaat_auteur',
        			'type' => 'text',
        			'instructions' => '',
        			'required' => 0,
        			'conditional_logic' => 0,
        			'wrapper' => array(
        				'width' => '',
        				'class' => '',
        				'id' => '',
        			),
        			'default_value' => '',
        			'placeholder' => '',
        			'prepend' => '',
        			'append' => '',
        			'maxlength' => '',
        		),
        	),
        	'location' => array(
        		array(
        			array(
        				'param' => 'post_type',
        				'operator' => '==',
        				'value' => 'citaat',
        			),
        		),
        	),
        	'menu_order' => 0,
        	'position' => 'acf_after_title',
        	'style' => 'default',
        	'label_placement' => 'top',
        	'instruction_placement' => 'label',
        	'hide_on_screen' => '',
        	'active' => true,
        	'description' => '',
        ));
        
        acf_add_local_field_group(array(
        	'key' => 'group_5c8fd6bacf265',
        	'title' => 'Do\'s & don\'ts',
        	'fields' => array(
        		array(
        			'key' => 'field_5c8fd6e89973a',
        			'label' => 'combinatie van do en don\'t',
        			'name' => 'do_dont_combinatie',
        			'type' => 'repeater',
        			'instructions' => '',
        			'required' => 0,
        			'conditional_logic' => 0,
        			'wrapper' => array(
        				'width' => '',
        				'class' => '',
        				'id' => '',
        			),
        			'collapsed' => '',
        			'min' => 0,
        			'max' => 0,
        			'layout' => 'table',
        			'button_label' => '',
        			'sub_fields' => array(
        				array(
        					'key' => 'field_5c8fdb7dbec2a',
        					'label' => 'dodontcombo_do',
        					'name' => 'dodontcombo_do',
        					'type' => 'text',
        					'instructions' => '',
        					'required' => 0,
        					'conditional_logic' => 0,
        					'wrapper' => array(
        						'width' => '49',
        						'class' => '',
        						'id' => '',
        					),
        					'default_value' => '',
        					'placeholder' => '',
        					'prepend' => '',
        					'append' => '',
        					'maxlength' => '',
        				),
        				array(
        					'key' => 'field_5c8fdba0bec2b',
        					'label' => 'dodontcombo_dont',
        					'name' => 'dodontcombo_dont',
        					'type' => 'text',
        					'instructions' => '',
        					'required' => 0,
        					'conditional_logic' => 0,
        					'wrapper' => array(
        						'width' => '49',
        						'class' => '',
        						'id' => '',
        					),
        					'default_value' => '',
        					'placeholder' => '',
        					'prepend' => '',
        					'append' => '',
        					'maxlength' => '',
        				),
        			),
        		),
        	),
        	'location' => array(
        		array(
        			array(
        				'param' => 'post_type',
        				'operator' => '==',
        				'value' => 'vaardigheid',
        			),
        		),
        	),
        	'menu_order' => 0,
        	'position' => 'normal',
        	'style' => 'default',
        	'label_placement' => 'top',
        	'instruction_placement' => 'label',
        	'hide_on_screen' => '',
        	'active' => true,
        	'description' => '',
        ));
        
        acf_add_local_field_group(array(
        	'key' => 'group_5c8fd53fbda24',
        	'title' => 'Facts & figures',
        	'fields' => array(
        		array(
        			'key' => 'field_5c8fd54f1a328',
        			'label' => 'facts_title',
        			'name' => 'facts_title',
        			'type' => 'text',
        			'instructions' => '',
        			'required' => 0,
        			'conditional_logic' => 0,
        			'wrapper' => array(
        				'width' => '',
        				'class' => '',
        				'id' => '',
        			),
        			'default_value' => '',
        			'placeholder' => '',
        			'prepend' => '',
        			'append' => '',
        			'maxlength' => '',
        		),
        		array(
        			'key' => 'field_5c8fd568b08ec',
        			'label' => 'facts_description',
        			'name' => 'facts_description',
        			'type' => 'wysiwyg',
        			'instructions' => '',
        			'required' => 0,
        			'conditional_logic' => 0,
        			'wrapper' => array(
        				'width' => '',
        				'class' => '',
        				'id' => '',
        			),
        			'tabs' => 'all',
        			'toolbar' => 'full',
        			'media_upload' => 1,
        			'default_value' => '',
        			'delay' => 0,
        		),
        		array(
        			'key' => 'field_5c8fd57f464c4',
        			'label' => 'facts_source_url',
        			'name' => 'facts_source_url',
        			'type' => 'text',
        			'instructions' => '',
        			'required' => 0,
        			'conditional_logic' => 0,
        			'wrapper' => array(
        				'width' => '',
        				'class' => '',
        				'id' => '',
        			),
        			'default_value' => '',
        			'placeholder' => '',
        			'prepend' => '',
        			'append' => '',
        			'maxlength' => '',
        		),
        		array(
        			'key' => 'field_5c8fd5975c283',
        			'label' => 'facts_source_label',
        			'name' => 'facts_source_label',
        			'type' => 'text',
        			'instructions' => '',
        			'required' => 0,
        			'conditional_logic' => 0,
        			'wrapper' => array(
        				'width' => '',
        				'class' => '',
        				'id' => '',
        			),
        			'default_value' => '',
        			'placeholder' => '',
        			'prepend' => '',
        			'append' => '',
        			'maxlength' => '',
        		),
        	),
        	'location' => array(
        		array(
        			array(
        				'param' => 'post_type',
        				'operator' => '==',
        				'value' => 'doelgroep',
        			),
        		),
        	),
        	'menu_order' => 0,
        	'position' => 'normal',
        	'style' => 'default',
        	'label_placement' => 'top',
        	'instruction_placement' => 'label',
        	'hide_on_screen' => '',
        	'active' => true,
        	'description' => '',
        ));
        
        acf_add_local_field_group(array(
        	'key' => 'group_5c8f9ba967736',
        	'title' => 'Gerelateerde content',
        	'fields' => array(
        		array(
        			'key' => 'field_5c8fe203a8435',
        			'label' => 'Gerelateerde content toevoegen?',
        			'name' => 'gerelateerde_content_toevoegen',
        			'type' => 'radio',
        			'instructions' => '',
        			'required' => 0,
        			'conditional_logic' => 0,
        			'wrapper' => array(
        				'width' => '',
        				'class' => '',
        				'id' => '',
        			),
        			'choices' => array(
        				'ja' => 'Ja',
        				'nee' => 'Nee',
        			),
        			'allow_null' => 0,
        			'other_choice' => 0,
        			'default_value' => 'nee',
        			'layout' => 'vertical',
        			'return_format' => 'value',
        			'save_other_choice' => 0,
        		),
        		array(
        			'key' => 'field_5c8fd404bd765',
        			'label' => 'content_block_title',
        			'name' => 'content_block_title',
        			'type' => 'text',
        			'instructions' => '',
        			'required' => 1,
        			'conditional_logic' => array(
        				array(
        					array(
        						'field' => 'field_5c8fe203a8435',
        						'operator' => '==',
        						'value' => 'ja',
        					),
        				),
        			),
        			'wrapper' => array(
        				'width' => '',
        				'class' => '',
        				'id' => '',
        			),
        			'default_value' => '',
        			'placeholder' => '',
        			'prepend' => '',
        			'append' => '',
        			'maxlength' => '',
        		),
        		array(
        			'key' => 'field_5c8fd42e15a23',
        			'label' => 'content_block_items',
        			'name' => 'content_block_items',
        			'type' => 'relationship',
        			'instructions' => '',
        			'required' => 0,
        			'conditional_logic' => array(
        				array(
        					array(
        						'field' => 'field_5c8fe203a8435',
        						'operator' => '==',
        						'value' => 'ja',
        					),
        				),
        			),
        			'wrapper' => array(
        				'width' => '',
        				'class' => '',
        				'id' => '',
        			),
        			'post_type' => array(
        				0 => 'post',
        				1 => 'page',
        				2 => 'doelgroep',
        				3 => 'stap',
        			),
        			'taxonomy' => '',
        			'filters' => array(
        				0 => 'search',
        				1 => 'post_type',
        				2 => 'taxonomy',
        			),
        			'elements' => '',
        			'min' => '',
        			'max' => '',
        			'return_format' => 'object',
        		),
        	),
        	'location' => array(
        		array(
        			array(
        				'param' => 'post_type',
        				'operator' => '==',
        				'value' => 'stap',
        			),
        		),
        		array(
        			array(
        				'param' => 'post_type',
        				'operator' => '==',
        				'value' => 'page',
        			),
        		),
        	),
        	'menu_order' => 0,
        	'position' => 'normal',
        	'style' => 'default',
        	'label_placement' => 'top',
        	'instruction_placement' => 'label',
        	'hide_on_screen' => '',
        	'active' => true,
        	'description' => '',
        ));
        
        acf_add_local_field_group(array(
        	'key' => 'group_5c8fdeebf0c34',
        	'title' => 'Handige links',
        	'fields' => array(
        		array(
        			'key' => 'field_5c8fe142c5418',
        			'label' => 'Handige links toevoegen?',
        			'name' => 'handige_links_toevoegen',
        			'type' => 'radio',
        			'instructions' => '',
        			'required' => 0,
        			'conditional_logic' => 0,
        			'wrapper' => array(
        				'width' => '',
        				'class' => '',
        				'id' => '',
        			),
        			'choices' => array(
        				'ja' => 'Ja',
        				'nee' => 'Nee',
        			),
        			'allow_null' => 0,
        			'other_choice' => 0,
        			'default_value' => 'nee',
        			'layout' => 'vertical',
        			'return_format' => 'value',
        			'save_other_choice' => 0,
        		),
        		array(
        			'key' => 'field_5c8fdeec07d4b',
        			'label' => 'links_block_title',
        			'name' => 'links_block_title',
        			'type' => 'text',
        			'instructions' => '',
        			'required' => 1,
        			'conditional_logic' => array(
        				array(
        					array(
        						'field' => 'field_5c8fe142c5418',
        						'operator' => '==',
        						'value' => 'ja',
        					),
        				),
        			),
        			'wrapper' => array(
        				'width' => '',
        				'class' => '',
        				'id' => '',
        			),
        			'default_value' => '',
        			'placeholder' => '',
        			'prepend' => '',
        			'append' => '',
        			'maxlength' => '',
        		),
        		array(
        			'key' => 'field_5c8fdeec07d58',
        			'label' => 'links_block_items',
        			'name' => 'links_block_items',
        			'type' => 'repeater',
        			'instructions' => '',
        			'required' => 0,
        			'conditional_logic' => array(
        				array(
        					array(
        						'field' => 'field_5c8fe142c5418',
        						'operator' => '==',
        						'value' => 'ja',
        					),
        				),
        			),
        			'wrapper' => array(
        				'width' => '',
        				'class' => '',
        				'id' => '',
        			),
        			'collapsed' => '',
        			'min' => 1,
        			'max' => 0,
        			'layout' => 'table',
        			'button_label' => '',
        			'sub_fields' => array(
        				array(
        					'key' => 'field_5c8fdf2a0c1a1',
        					'label' => 'links_block_item_url',
        					'name' => 'links_block_item_url',
        					'type' => 'url',
        					'instructions' => '',
        					'required' => 0,
        					'conditional_logic' => 0,
        					'wrapper' => array(
        						'width' => '30',
        						'class' => '',
        						'id' => '',
        					),
        					'default_value' => '',
        					'placeholder' => '',
        				),
        				array(
        					'key' => 'field_5c8fe1000c1a2',
        					'label' => 'links_block_item_linktext',
        					'name' => 'links_block_item_linktext',
        					'type' => 'text',
        					'instructions' => '',
        					'required' => 0,
        					'conditional_logic' => 0,
        					'wrapper' => array(
        						'width' => '30',
        						'class' => '',
        						'id' => '',
        					),
        					'default_value' => '',
        					'placeholder' => '',
        					'prepend' => '',
        					'append' => '',
        					'maxlength' => '',
        				),
        				array(
        					'key' => 'field_5c8fe10d0c1a3',
        					'label' => 'links_block_item_description',
        					'name' => 'links_block_item_description',
        					'type' => 'textarea',
        					'instructions' => '',
        					'required' => 0,
        					'conditional_logic' => 0,
        					'wrapper' => array(
        						'width' => '40',
        						'class' => '',
        						'id' => '',
        					),
        					'default_value' => '',
        					'placeholder' => '',
        					'maxlength' => '',
        					'rows' => '',
        					'new_lines' => '',
        				),
        			),
        		),
        	),
        	'location' => array(
        		array(
        			array(
        				'param' => 'post_type',
        				'operator' => '==',
        				'value' => 'stap',
        			),
        		),
        		array(
        			array(
        				'param' => 'post_type',
        				'operator' => '==',
        				'value' => 'page',
        			),
        		),
        	),
        	'menu_order' => 0,
        	'position' => 'normal',
        	'style' => 'default',
        	'label_placement' => 'top',
        	'instruction_placement' => 'label',
        	'hide_on_screen' => '',
        	'active' => true,
        	'description' => '',
        ));
        
        acf_add_local_field_group(array(
        	'key' => 'group_5c8fde441c0a9',
        	'title' => 'Methodes',
        	'fields' => array(
        		array(
        			'key' => 'field_5c8fde5259d46',
        			'label' => 'stap_methodes',
        			'name' => 'stap_methodes',
        			'type' => 'relationship',
        			'instructions' => '',
        			'required' => 0,
        			'conditional_logic' => 0,
        			'wrapper' => array(
        				'width' => '',
        				'class' => '',
        				'id' => '',
        			),
        			'post_type' => array(
        				0 => 'methode',
        			),
        			'taxonomy' => '',
        			'filters' => array(
        				0 => 'search',
        			),
        			'elements' => '',
        			'min' => '',
        			'max' => '',
        			'return_format' => 'object',
        		),
        	),
        	'location' => array(
        		array(
        			array(
        				'param' => 'post_type',
        				'operator' => '==',
        				'value' => 'stap',
        			),
        		),
        	),
        	'menu_order' => 0,
        	'position' => 'normal',
        	'style' => 'default',
        	'label_placement' => 'top',
        	'instruction_placement' => 'label',
        	'hide_on_screen' => '',
        	'active' => true,
        	'description' => '',
        ));
        
      endif;
        
      }  

}

endif;

//========================================================================================================

