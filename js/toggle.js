
/*
// Gebruiker Centraal - toggle.js
// ----------------------------------------------------------------------------------
// JS voor accordions op homepage
// ----------------------------------------------------------------------------------
// @package   	ictu-gc-posttypes-inclusie
// @author    	Paul van Buuren
// @license		GPL-2.0+
// @version		0.0.11a
// @desc.		JS homepage voor openen en sluiten modal windows verbeterd.
// @credits		Scott Vinkle - see: https://codepen.io/svinkle/pen/mKfru
//				via https://a11yproject.com/patterns.html
// @link      	https://github.com/ICTU/Gebruiker-Centraal---Inclusie---custom-post-types-taxonomies
 */

var console;
var isSmallerScreenSize = true;
var showOneAnswerAtATime = false;


(function (document, window, undefined) {
	
//	window.alert('laatste versie');
	
	'use strict';

	// Vars
	var toggleSection			= jQuery('#home-chart'),
		buttonsWithOpenFunction	= jQuery('.js-openclosebutton'),
		toggleTextinfo			= jQuery('.js-descriptionbox'),
		modalOpen 				= false,
		allFocusable  			= jQuery( ":focusable" ),
		theModal,
		sectionHeight			= 210;

toggleSection.addClass("jsloaded");
toggleSection.attr('tabindex', '0');
toggleSection.focus();

	/**
	* Save section focus
	*/
	var closeAllSections = function () {

		theModal = null;
		modalOpen = false;

		toggleTextinfo.each(function () {
			var thisSection = jQuery(this);
			if (thisSection.hasClass('active')) {
				// Hide answer
				thisSection.removeClass('active');      
				thisSection.find('.btn--close').remove();
				thisSection.attr('aria-hidden', 'true');
			}
		});
		
	};
		
	
	/**
	* Save section focus
	*/
	var saveFocus = function (elem, theButtonThatOpensItsSection) {
		
		// Reset other tab attributes
		theButtonThatOpensItsSection.each(function () {
//			jQuery(this).attr('tabindex', '-1');
			jQuery(this).attr('aria-selected', 'false');
		});
		
		// Set this tab attributes
		elem.attr({
//			'tabindex': '0',
			'aria-selected': 'true'
		});
		
	};


	// Restrict focus to the modal window when it's open.
	// Tabbing will just loop through the whole modal.
	// Shift + Tab will allow backup to the top of the modal,
	// and then stop.
	function focusRestrict ( event ) {

// console.log("focusRestrict: modalOpen=" + modalOpen + ", showOneAnswerAtATime=" + showOneAnswerAtATime + ", isSmallerScreenSize=" + isSmallerScreenSize);

		if ( modalOpen && showOneAnswerAtATime && ! isSmallerScreenSize ) {
// console.log("dus wel checken. ID=" + theModal.attr('id') );
			// is the focus inside the opened popup?
			if ( jQuery(event.target).parents("#" + theModal.attr('id') ).length == 1 ) { 
				// focus is inside the right container
				// no need for action
// console.log('alles is fleks. niks doen');
			}
			else {
				// outside. put it back in
				event.stopPropagation();
				theModal.focus();
// console.log('HO ho, terug in je hok!');
			}
		}
		else {
			// modal not open
// console.log("dus niet checken");
		}

	}
	
	jQuery( ":focusable" ).bind('focus', focusRestrict );
	
	
	/**
	* Show answer on click
	*/
	var doShowSection = function (elem, theButtonThatOpensItsSection) {
		
		var thisSection = elem.next();
		
		// Save focus
		saveFocus(elem, theButtonThatOpensItsSection);

		
		// Set this tab attributes
		if (thisSection.hasClass('active')) {
			// Hide answer
			thisSection.removeClass('active');      
			elem.attr('aria-expanded', 'false');      
			thisSection.attr('aria-hidden', 'true');

			thisSection.attr("style","");

		}
		else {
			if (showOneAnswerAtATime) {
				// Hide all answers
				toggleTextinfo.removeClass('active').attr('aria-hidden', 'true');
				buttonsWithOpenFunction.attr('aria-expanded', 'false');
			}

			// Show answer
			thisSection.addClass('active');      
			elem.attr('aria-expanded', 'true');      
			thisSection.attr('aria-hidden', 'false');

			if ( ! isSmallerScreenSize ) {

				// position the text above the button
				var domRect = thisSection.height();			
				var verplaats = ( ( domRect + sectionHeight ) * -1 );
				thisSection.attr("style", "transform: translateY(" + verplaats + "px)");


				if ( thisSection.find('btn--close').length ) {
					// knop is al aanwezig
// console.log('knop al aanwezig');
				}
				else {
					// knop nog toevoegen
// console.log('knop nog toevoegen');

//					thisSection.find('h3').append( 'button.btn--close' );
					thisSection.append( '<button class="btn--close">×</button>' );
					
					// close modal by btn click/hit
					var mClose        	= thisSection.find('button.btn--close');
					
// console.log('knop toegevoegd');
					
					mClose.attr( "aria-label", "Sluit pop-up" );
					
					// Handle button click event
					mClose.on( "click", closeAllSections );    
// console.log('event listener toegevoegd');
				}

			}

			modalOpen = true;
			theModal = thisSection;
			theModal.attr('tabindex', '0');
			theModal.focus();

			
		}

		
	};

	
	/**
	* Keyboard interaction
	*/
//	var keyboardInteraction	= function (elem, e, theButtonThatOpensItsSection) {
/*		
		var keyCode			= e.which,
		nextSection 		= elem.next().next().is('section.step') ? elem.next().next() : false,
		previousSection 	= elem.prev().prev().is('section.step') ? elem.prev().prev() : false,
		firstSection 		= elem.parent().find('section.step:first'),
		lastSection 		= elem.parent().find('section.step:last');

		// console.log('Keycode: ' + keyCode );
		
		switch(keyCode) {
			// Escape
			case 27:
// console.log('Esc!');
				e.preventDefault();
				e.stopPropagation();
				closeAllSections();
				break;
			
			// Left/Up
			case 37:
			case 38:
				e.preventDefault();
				e.stopPropagation();
				
				// Check for previous section
				if (!previousSection) {
					// No previous, set focus on last section
					lastSection.focus();
				} else {
					// Move focus to previous section
					previousSection.focus();
				}
				
				break;
			
			// Right/Down
			case 39:
			case 40:
				e.preventDefault();
				e.stopPropagation();
				
				// Check for next section
				if (!nextSection) {
					// No next, set focus on first section
					firstSection.focus();
				} else {
					// Move focus to next section
					nextSection.focus();
				}
				
				break;
			
			// Home
			case 36:
				e.preventDefault();
				e.stopPropagation();
				
				// Set focus on first section
				firstSection.focus();
				break;
			
			// End
			case 35:
				e.preventDefault();
				e.stopPropagation();
				
				// Set focus on last section
				lastSection.focus();
				break;
			
			// Enter/Space
			case 13:
			case 32:
				e.preventDefault();
				e.stopPropagation();
				
				// Show answer content
				doShowSection(elem, theButtonThatOpensItsSection);
				break;
			}
*/			
//	};
	
	/**
	* On load, setup roles and initial properties
	*/
	
	// Each FAQ section
	buttonsWithOpenFunction.each(function (i) {
		jQuery(this).attr({
			'id': 'faq-question-' + i,
			'role': 'tab',
			'aria-controls': 'faq-answer-' + i,
			'aria-expanded': 'false',
			'aria-selected': 'false'
//			'tabindex': '-1'
		});
	});
	
	// Each FAQ Answer
	toggleTextinfo.each(function (i) {
		jQuery(this).attr({
			'id': 'faq-answer-' + i,
			'role': 'tabpanel',
			'aria-labelledby': 'faq-question-' + i,
			'aria-hidden': 'true'
		});
	});
	
	// Each FAQ Section
	toggleSection.each(function () {
		var $this = jQuery(this),
		theButtonThatOpensItsSection = $this.find('.js-openclosebutton');
		
		// Set section attributes
		$this.attr({
			'role': 'tablist',
			'aria-multiselectable': 'true'
		});
		
		theButtonThatOpensItsSection.each(function (i) {
			var $this = jQuery(this);
			
			// Make first tab clickable
			if (i === 0) {
				$this.attr('tabindex', '0');
			}
			
			// Click event
			$this.on('click', function () {
				doShowSection(jQuery(this), theButtonThatOpensItsSection);
			});
			
//			// Keydown event
//			$this.on('keydown', function (e) {
//				keyboardInteraction(jQuery(this), e, theButtonThatOpensItsSection);
//			});
			
			// Focus event
			$this.on('focus', function () {
				saveFocus(jQuery(this), theButtonThatOpensItsSection);
			});
		});
	});
	
})(document, window  );


// =========================================================================================================

// media query change
function checkWidthChange(mq) {

	if (mq.matches) {
		// window width is at least 800px
		isSmallerScreenSize = false;
		showOneAnswerAtATime = true;
	}
	else {
		// window width is less than 800px
		isSmallerScreenSize = true;
		showOneAnswerAtATime = false;
	}
	
}

// =========================================================================================================

// media query event handler
if (matchMedia ) {
	var mq = window.matchMedia('(min-width: 800px)');
	mq.addListener(checkWidthChange);
	checkWidthChange(mq);
}

// =========================================================================================================
