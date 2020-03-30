<?php

/*
// Gebruiker Centraal - beeldbank.acf-definitions.php
// ----------------------------------------------------------------------------------
// ACF definities voor beeldbank plugin
// ----------------------------------------------------------------------------------
// @package   ictu-gc-posttypes-inclusie
// @author    Paul van Buuren
// @license   GPL-2.0+
// @version   0.0.10
// @desc.     CPT procestips toegevoegd. Mogelijkheid OD-tips toe te voegen op stap-pagina.
// @link      https://github.com/ICTU/Gebruiker-Centraal---Inclusie---custom-post-types-taxonomies
 */

// Vars for icons


// Get icons from stepchart JSON
function getIcons() {
	$icon_array = [];
	$icon_list = file_get_contents( get_stylesheet_directory()  .'/images/svg/stepchart/stepchart_icons.json' );
	$icon_list = json_decode($icon_list, true);

    foreach ($icon_list as $key => $icon) {
        $icon_array[$key] =
          '<span style="display: inline-flex; margin-top: 10px; align-items: center; position: relative; min-height: 40px; padding-left: 45px">'.
          '<svg class="icon icon--medium icon--stepchart" aria-hidden="true" focusable="false" style="width: 30px; height: 40px; position: absolute; left: 7px; top: 0;"> '.
          '<use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="'. get_stylesheet_directory_uri()  .'/images/svg/stepchart/defs/svg/sprite.defs.svg#'. $key .'"></use> '.
          '</svg>' .
          '<span class="label-text">'. $icon .'</span></span>';
    }

    return $icon_array;
}


if( ! function_exists('ictu_gc_inclusie_initialize_acf_fields') ) {

	//------------------------------------------------------------------------------------------------
	// velden voor stap- en richtlijnpagina's
	acf_add_local_field_group(array(
		'key' => 'group_5c8fde441c0a9',
		'title' => '(01) - Stap en richtlijn: type pagina, icoontjes, inleiding',
		'fields' => array(
			array(
				'key' => 'field_5df7819105f34',
				'label' => 'Is dit een stap of een richtlijn?',
				'name' => 'stap_type_pagina',
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
					'stap' => 'Stap',
					'richtlijn' => 'Richtlijn',
				),
				'allow_null' => 0,
				'other_choice' => 0,
				'default_value' => 'stap',
				'layout' => 'vertical',
				'return_format' => 'value',
				'save_other_choice' => 0,
			),
			array(
				'key' => 'field_5c91fb7281870',
				'label' => 'Verkorte titel',
				'name' => 'stap_verkorte_titel',
				'type' => 'text',
				'instructions' => 'Deze tekst wordt als label getoond in het stappenschema. Gebruik bij voorkeur 1 woord.',
				'required' => 0,
				'conditional_logic' => array(
					array(
						array(
							'field' => 'field_5df7819105f34',
							'operator' => '==',
							'value' => 'stap',
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
				'key' => 'field_5c90bae01c857',
				'label' => 'Inleiding',
				'name' => 'stap_inleiding',
				'type' => 'wysiwyg',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => '',
				'tabs' => 'all',
				'toolbar' => 'basic',
				'media_upload' => 1,
				'delay' => 0,
			),
			array(
				'key' => 'field_5cdb1d4374d09',
				'label' => 'Icoontje',
				'name' => 'stap_icon',
				'type' => 'radio',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => array(
					array(
						array(
							'field' => 'field_5df7819105f34',
							'operator' => '==',
							'value' => 'stap',
						),
					),
				),
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'choices' => getIcons(),
				'allow_null' => 0,
				'other_choice' => 0,
				'default_value' => 'identificeer',
				'layout' => 'vertical',
				'return_format' => 'value',
				'save_other_choice' => 0,
			),
			array(
				'key' => 'field_5e2db1497013e',
				'label' => 'Tests en resultaten',
				'name' => 'richtlijn_resultaatblokken',
				'type' => 'repeater',
				'instructions' => 'Voeg blokken toe die een indruk geven van hoe je getest hebt en wat de resultaten waren.',
				'required' => 0,
				'conditional_logic' => array(
					array(
						array(
							'field' => 'field_5df7819105f34',
							'operator' => '==',
							'value' => 'richtlijn',
						),
					),
				),
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'collapsed' => 'field_5e2db18b7013f',
				'min' => 0,
				'max' => 0,
				'layout' => 'row',
				'button_label' => 'Blok toevoegen',
				'sub_fields' => array(
					array(
						'key' => 'field_5e2db18b7013f',
						'label' => 'Titel',
						'name' => 'richtlijn_resultaatblok_titel',
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
						'key' => 'field_5e2db1bb70140',
						'label' => 'Tekst',
						'name' => 'richtlijn_resultaatblok_tekst',
						'type' => 'wysiwyg',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'default_value' => '',
						'tabs' => 'all',
						'toolbar' => 'basic',
						'media_upload' => 0,
						'delay' => 0,
					),
					array(
						'key' => 'field_5e2db1e170141',
						'label' => 'Afbeelding',
						'name' => 'richtlijn_resultaatblok_afbeelding',
						'type' => 'image',
						'instructions' => '',
						'required' => 0,
						'conditional_logic' => 0,
						'wrapper' => array(
							'width' => '',
							'class' => '',
							'id' => '',
						),
						'return_format' => 'array',
						'preview_size' => 'medium',
						'library' => 'all',
						'min_width' => '',
						'min_height' => '',
						'min_size' => '',
						'max_width' => '',
						'max_height' => '',
						'max_size' => '',
						'mime_types' => '',
					),
					array(
						'key' => 'field_5e2db20470142',
						'label' => 'Label',
						'name' => 'richtlijn_resultaatblok_label',
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
							'geen' => 'Geen label',
							'nietzo' => 'Niet zo', // de key stemt overeen met de key uit ICTU_GC_BEELDBANK_LABELS
							'maarzo' => 'Maar zo', // de key stemt overeen met de key uit ICTU_GC_BEELDBANK_LABELS
						),
						'allow_null' => 0,
						'other_choice' => 0,
						'default_value' => 'geen',
						'layout' => 'vertical',
						'return_format' => 'value',
						'save_other_choice' => 0,
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


	//------------------------------------------------------------------------------------------------
	// velden voor thema-instellingen
	acf_add_local_field_group(array(
		'key' => 'group_5d726d93a46f2',
		'title' => 'Theme-instellingen voor beeldbankwebsite',
		'fields' => array(
			array(
				'key' => 'field_5d726daa06090',
				'label' => 'Pagina met brievenoverzicht',
				'name' => 'themesettings_inclusie_brievenpagina',
				'type' => 'post_object',
				'instructions' => '',
				'required' => 1,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'post_type' => array(
					0 => 'page',
				),
				'taxonomy' => '',
				'allow_null' => 0,
				'multiple' => 0,
				'return_format' => 'object',
				'ui' => 1,
			),
			array(
				'key' => 'field_5e32fa2b613a7',
				'label' => 'Pagina met beeldenoverzicht',
				'name' => 'themesettings_inclusie_beeldenpagina',
				'type' => 'post_object',
				'instructions' => '',
				'required' => 1,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'post_type' => array(
					0 => 'page',
				),
				'taxonomy' => '',
				'allow_null' => 0,
				'multiple' => 0,
				'return_format' => 'object',
				'ui' => 1,
			),
		),
		'location' => array(
			array(
				array(
					'param' => 'options_page',
					'operator' => '==',
					'value' => 'instellingen',
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


//------------------------------------------------------------------------------------------------
// velden voor stap

if ( 22 == 33 ) {

	acf_add_local_field_group(array(
		'key' => 'group_5c8fde441c0a9',
		'title' => 'Stap ACF DEF: inleiding en methodes',
		'fields' => array(
			array(
				'key' => 'field_5c91fb7281870',
				'label' => 'Verkorte titel',
				'name' => 'stap_verkorte_titel',
				'type' => 'text',
				'instructions' => 'Deze tekst wordt als label getoond in het stappenschema. Gebruik bij voorkeur 1 woord.',
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
				'key' => 'field_5cdb1d4374d09',
				'label' => 'Icoontje',
				'name' => 'stap_icon',
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
					'bereidvoor' => '<img src="' . esc_url( plugins_url( 'images/icons/stap/verdiep.png', dirname(__FILE__) ) )  . '"> Bereid voor',
					'ontwerp' => '<img src="' . esc_url( plugins_url( 'images/icons/stap/ontwerp.png', dirname(__FILE__) ) )  . '"> Ontwerp',
					'test' => '<img src="' . esc_url( plugins_url( 'images/icons/stap/lampje.png', dirname(__FILE__) ) )  . '"> Test',
					'evalueer' => '<img src="' . esc_url( plugins_url( 'images/icons/stap/evalueer.png', dirname(__FILE__) ) )  . '"> Pas aan',
					'deel' => '<img src="' . esc_url( plugins_url( 'images/icons/stap/deel.png', dirname(__FILE__) ) )  . '"> Deel',
				),
				'allow_null' => 0,
				'other_choice' => 0,
				'default_value' => 'identificeer',
				'layout' => 'vertical',
				'return_format' => 'value',
				'save_other_choice' => 0,
			),
			array(
				'key' => 'field_5c90bae01c857',
				'label' => 'Inleiding',
				'name' => 'stap_inleiding',
				'type' => 'wysiwyg',
				'instructions' => 'korte inleiding bij deze stap. Wordt getoond in het cirkelschema op de homepage.',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => '',
				'tabs' => 'all',
				'toolbar' => 'basic',
				'media_upload' => 1,
				'delay' => 0,
			),
/*
			array(
				'key' => 'field_5d7245be8ffc0',
				'label' => 'Titel bij de methodes',
				'name' => 'stap_methodes_titel',
				'type' => 'text',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => 'Methodes',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'maxlength' => '',
			),
			array(
				'key' => 'field_5ce3d0e2f8917',
				'label' => 'Inleiding bij de methodes',
				'name' => 'stap_methode_inleiding',
				'type' => 'wysiwyg',
				'instructions' => 'Korte inleiding bij methoden.',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => 'Dit is een selectie van methoden, technieken en instrumenten die je in kunt zetten bij het uitvoeren van deze stap. Soms gaat het om standaardmethoden die worden ingezet voor het uitvoeren van een ontwerptraject waarbij de gebruiker centraal staat. Andere methoden richten zich specifiek op inclusie.',
				'tabs' => 'all',
				'toolbar' => 'basic',
				'media_upload' => 1,
				'delay' => 0,
			),
			array(
				'key' => 'field_5c8fde5259d46',
				'label' => 'Methodes',
				'name' => 'stap_methodes',
				'type' => 'relationship',
				'instructions' => 'Kies de bij deze stap horende methodes.',
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
			array(
				'key' => 'field_5d84d94b05b52',
				'label' => 'Procestips titel',
				'name' => 'stap_procestips_sectiontitle',
				'type' => 'text',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => 'Procestips',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'maxlength' => '',
			),
			array(
				'key' => 'field_5d84dcb9d1473',
				'label' => 'Bijbehorende procestips',
				'name' => 'stap_procestips',
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
					0 => 'procestip',
				),
				'taxonomy' => '',
				'filters' => array(
					0 => 'search',
					1 => 'taxonomy',
				),
				'elements' => '',
				'min' => '',
				'max' => '',
				'return_format' => 'object',
			),
			array(
				'key' => 'field_5d84d4b652e0e',
				'label' => 'Tips Optimaal Digitaal',
				'name' => 'stap_tips_optimaal_digitaal_sectiontitle',
				'type' => 'text',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'default_value' => 'Optimaal Digitaal tips',
				'placeholder' => '',
				'prepend' => '',
				'append' => '',
				'maxlength' => '',
			),
	*/
/*
			array(
				'key' => 'field_5d84d3eed46b2',
				'label' => 'Bijbehorende tips Optimaal Digitaal',
				'name' => 'stap_tips_optimaal_digitaal',
				'type' => 'repeater',
				'instructions' => '',
				'required' => 0,
				'conditional_logic' => 0,
				'wrapper' => array(
					'width' => '',
					'class' => '',
					'id' => '',
				),
				'collapsed' => 'field_5d84d439d46b4',
				'min' => 0,
				'max' => 0,
				'layout' => 'row',
				'button_label' => 'Nieuw tip toevoegen',
				'sub_fields' => array(
					array(
						'key' => 'field_5d84d439d46b4',
						'label' => 'Titel',
						'name' => 'stap_tip_optimaal_digitaal_titel',
						'type' => 'text',
						'instructions' => 'De titel van de Optimaal Digitaal-tip',
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
						'key' => 'field_5d84d468d46b5',
						'label' => 'Tip-nummer',
						'name' => 'stap_tip_optimaal_digitaal_tipnummer',
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
						'key' => 'field_5d84d423d46b3',
						'label' => 'URL',
						'name' => 'stap_tip_optimaal_digitaal_url',
						'type' => 'url',
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
					),
					array(
						'key' => 'field_5d84d472d46b6',
						'label' => 'Tip-thema',
						'name' => 'stap_tip_optimaal_digitaal_tipthema',
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
							'commitment' => 'Commitment',
							'gebruiksgemak' => 'Gebruiksgemak',
							'informatieveiligheid' => 'Informatieveiligheid',
							'kanaalsturing' => 'Kanaalsturing',
							'procesaanpak' => 'Procesaanpak',
							'samenwerking' => 'Samenwerking',
							'inclusie' => 'Inclusie',
						),
						'allow_null' => 0,
						'other_choice' => 0,
						'default_value' => 'inclusie',
						'layout' => 'vertical',
						'return_format' => 'value',
						'save_other_choice' => 0,
					),
				),
			),
	*/

		),
		'location' => array(
			array(
				array(
					'param' => 'post_type',
					'operator' => '==',
					'value' => ICTU_GC_CPT_STAP,
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

}


/**
 * voor de homepage: mogelijkheid wel of geen nieuwsberichten te tonen
 *
 * @since 2.0.5
 *
 * @return void
 */
acf_add_local_field_group(array(
	'key' => 'group_5df800c8c11b9',
	'title' => '02 - Homepage template: nieuwsberichten tonen',
	'fields' => array(
		array(
			'key' => 'field_5df801414bf08',
			'label' => 'Wil je berichten tonen?',
			'name' => 'home_template_posts',
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
				'home_template_posts_nee' => 'Nee, geen nieuwsberichten',
				'home_template_posts_ja' => 'Ja, toon nieuwsberichten',
			),
			'allow_null' => 0,
			'other_choice' => 0,
			'default_value' => 'home_template_posts_nee',
			'layout' => 'vertical',
			'return_format' => 'value',
			'save_other_choice' => 0,
		),
		array(
			'key' => 'field_5e789e77ae1e6',
			'label' => 'Titel',
			'name' => 'home_template_posts_titel',
			'type' => 'text',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => array(
				array(
					array(
						'field' => 'field_5df801414bf08',
						'operator' => '==',
						'value' => 'home_template_posts_ja',
					),
				),
			),
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => 'Laatste nieuws en blogs',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
		array(
			'key' => 'field_5df8019c4bf09',
			'label' => 'Hoeveel berichten?',
			'name' => 'home_template_posts_number',
			'type' => 'number',
			'instructions' => 'hoeveel berichten wil je maximaal tonen',
			'required' => 0,
			'conditional_logic' => array(
				array(
					array(
						'field' => 'field_5df801414bf08',
						'operator' => '==',
						'value' => 'home_template_posts_ja',
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
			'min' => '',
			'max' => 20,
			'step' => '',
		),
		array(
			'key' => 'field_5df802084bf0a',
			'label' => 'Filteren op categorie?',
			'name' => 'home_template_posts_category_filter',
			'type' => 'radio',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => array(
				array(
					array(
						'field' => 'field_5df801414bf08',
						'operator' => '==',
						'value' => 'home_template_posts_ja',
					),
				),
			),
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'choices' => array(
				'home_template_posts_category_filter_nee' => 'Nee, niet filteren op categorie',
				'home_template_posts_category_filter_ja' => 'Ja, toon alleen berichten uit deze categorie:',
			),
			'allow_null' => 0,
			'other_choice' => 0,
			'default_value' => '',
			'layout' => 'vertical',
			'return_format' => 'value',
			'save_other_choice' => 0,
		),
		array(
			'key' => 'field_5df803760ec17',
			'label' => 'Selecteer een categorie',
			'name' => 'home_template_posts_category_filter_catid',
			'type' => 'taxonomy',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => array(
				array(
					array(
						'field' => 'field_5df802084bf0a',
						'operator' => '==',
						'value' => 'home_template_posts_category_filter_ja',
					),
				),
			),
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'taxonomy' => 'category',
			'field_type' => 'checkbox',
			'add_term' => 0,
			'save_terms' => 0,
			'load_terms' => 0,
			'return_format' => 'id',
			'multiple' => 0,
			'allow_null' => 0,
		),
		array(
			'key' => 'field_5e78db2563a35',
			'label' => 'Link en tekst voor doorklik',
			'name' => 'home_template_posts_leesmeer_link',
			'type' => 'link',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => array(
				array(
					array(
						'field' => 'field_5df801414bf08',
						'operator' => '==',
						'value' => 'home_template_posts_ja',
					),
				),
			),
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'return_format' => 'array',
		),
		array(
			'key' => 'field_5e78db5263a36',
			'label' => 'home_template_posts_leesmeer_linktekst',
			'name' => 'home_template_posts_leesmeer_linktekst',
			'type' => 'text',
			'instructions' => '',
			'required' => 0,
			'conditional_logic' => array(
				array(
					array(
						'field' => 'field_5df801414bf08',
						'operator' => '==',
						'value' => 'home_template_posts_ja',
					),
				),
			),
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'default_value' => 'Alle berichten',
			'placeholder' => '',
			'prepend' => '',
			'append' => '',
			'maxlength' => '',
		),
	),
	'location' => array(
		array(
			array(
				'param' => 'page_template',
				'operator' => '==',
				'value' => 'home-inclusie.php',
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



//------------------------------------------------------

// relatie tussen een beeld en brieven
acf_add_local_field_group(array(
	'key' => 'group_5e45572685b3c',
	'title' => '01 - Beeld: bijbehorende brieven',
	'fields' => array(
		array(
			'key' => 'field_5e455747a6962',
			'label' => 'relation_beeldbrief_beeld',
			'name' => 'relation_beeldbrief_beeld',
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
				0 => 'brief',
			),
			'taxonomy' => '',
			'filters' => array(
				0 => 'search',
				1 => 'taxonomy',
			),
			'elements' => '',
			'min' => '',
			'max' => '',
			'return_format' => 'object',
		),
		array(
			'key' => 'field_5e78d2f7a581e',
			'label' => '',
			'name' => '',
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
				'value' => 'beeld',
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

// relatie tussen een brief en beelden
acf_add_local_field_group(array(
	'key' => 'group_5e45545c9e5e0',
	'title' => '01 - Beeldbrief: bijbehorend bestand en relatie met beelden',
	'fields' => array(
		array(
			'key' => 'field_5e45547862c17',
			'label' => 'Kies een bestand',
			'name' => 'beeldbrief_file',
			'type' => 'file',
			'instructions' => 'Je kunt hier een PDF toevoegen. Als je geen PDF hebt, kun je een plaatje selecteren. Als je hier geen bestanden toevoegt, dan gebruiken we de uitgelichte afbeelding als bijbehorend bestand.',
			'required' => 0,
			'conditional_logic' => 0,
			'wrapper' => array(
				'width' => '',
				'class' => '',
				'id' => '',
			),
			'return_format' => 'array',
			'library' => 'all',
			'min_size' => '',
			'max_size' => '',
			'mime_types' => '',
		),
		array(
			'key' => 'field_5e455603cb722',
			'label' => 'Kies de bijbehorende beelden',
			'name' => 'relation_beeldbrief_beeld',
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
				0 => 'beeld',
			),
			'taxonomy' => '',
			'filters' => array(
				0 => 'search',
				1 => 'taxonomy',
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
				'value' => 'brief',
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


}
