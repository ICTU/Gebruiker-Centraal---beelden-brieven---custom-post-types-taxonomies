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
				'choices' => array(
					'bereidvoor' => '<img src="/wp-content/plugins/ictu-gc-posttypes-brieven-beelden/images/icons/stap/verdiep.png"> Bereid voor',
					'ontwerp' => '<img src="/wp-content/plugins/ictu-gc-posttypes-brieven-beelden/images/icons/stap/ontwerp.png"> Ontwerp',
					'test' => '<img src="/wp-content/plugins/ictu-gc-posttypes-brieven-beelden/images/icons/stap/lampje.png"> Test',
					'evalueer' => '<img src="/wp-content/plugins/ictu-gc-posttypes-brieven-beelden/images/icons/stap/evalueer.png"> Pas aan',
					'deel' => '<img src="/wp-content/plugins/ictu-gc-posttypes-brieven-beelden/images/icons/stap/deel.png"> Deel',
				),
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




}	
