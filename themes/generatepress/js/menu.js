( function() {
	'use strict';

	if ( 'querySelector' in document && 'addEventListener' in window ) {
		/**
		 * matches() pollyfil
		 * @see https://developer.mozilla.org/en-US/docs/Web/API/Element/closest#Browser_compatibility
		 */
		if ( ! Element.prototype.matches ) {
			Element.prototype.matches = Element.prototype.msMatchesSelector || Element.prototype.webkitMatchesSelector;
		}

		/**
		 * closest() pollyfil
		 * @see https://developer.mozilla.org/en-US/docs/Web/API/Element/closest#Browser_compatibility
		 */
		if ( ! Element.prototype.closest ) {
			Element.prototype.closest = function( s ) {
				var el = this;
				var ancestor = this;
				if ( ! document.documentElement.contains( el ) ) {
					return null;
				}
				do {
					if ( ancestor.matches( s ) ) {
						return ancestor;
					}
					ancestor = ancestor.parentElement;
				} while ( ancestor !== null );
				return null;
			};
		}

		var allNavToggles = document.querySelectorAll( '.menu-toggle' ),
			dropdownToggle = document.querySelectorAll( 'nav .dropdown-menu-toggle' ),
			body = document.body,
			htmlEl = document.documentElement;

		/**
		 * Start mobile menu toggle.
		 *
		 * @param e The event.
		 * @param _this The clicked item.
		 */
		var toggleNav = function( e, _this ) {
			if ( ! _this ) {
				var _this = this;
			}

			if ( _this.getAttribute( 'data-nav' ) ) {
				var parentContainer = document.getElementById( _this.getAttribute( 'data-nav' ) );
			} else {
				var parentContainer = document.getElementById( _this.closest( 'nav' ).getAttribute( 'id' ) );
			}

			var nav = parentContainer.getElementsByTagName( 'ul' )[0];

			if ( parentContainer.classList.contains( 'toggled' ) ) {
				parentContainer.classList.remove( 'toggled' );
				htmlEl.classList.remove( 'mobile-menu-open' );
				nav.setAttribute( 'aria-hidden', 'true' );
				_this.setAttribute( 'aria-expanded', 'false' );

				if ( body.classList.contains( 'dropdown-hover' ) ) {
					var dropdownItems = nav.querySelectorAll( 'li.menu-item-has-children' );
					for ( var i = 0; i < dropdownItems.length; i++ ) {
						dropdownItems[i].querySelector( '.dropdown-menu-toggle' ).setAttribute( 'tabindex', '' );
					}
				}
			} else {
				parentContainer.classList.add( 'toggled' );
				htmlEl.classList.add( 'mobile-menu-open' );
				nav.setAttribute( 'aria-hidden', 'false' );
				_this.setAttribute( 'aria-expanded', 'true' );

				if ( body.classList.contains( 'dropdown-hover' ) ) {
					var dropdownItems = nav.querySelectorAll( 'li.menu-item-has-children' );
					for ( var i = 0; i < dropdownItems.length; i++ ) {
						dropdownItems[i].querySelector( '.dropdown-menu-toggle' ).setAttribute( 'tabindex', '0' );
					}
				}
			}
		}

		for ( var i = 0; i < allNavToggles.length; i++ ) {
			allNavToggles[i].addEventListener( 'click', toggleNav, false );
		}

		/**
		 * Open sub-menus
		 *
		 * @param e The event.
		 * @param _this The clicked item.
		 */
		var toggleSubNav = function( e, _this ) {

			if ( ! _this ) {
				var _this = this;
			}

			if ( ( _this.closest( 'nav' ).classList.contains( 'toggled' ) || htmlEl.classList.contains( 'slide-opened' ) ) && ! body.classList.contains( 'dropdown-click' ) ) {
				e.preventDefault();
				var closestLi = _this.closest( 'li' );

				var dropdownToggle = closestLi.querySelector( '.dropdown-menu-toggle' );
				if ( 'false' == dropdownToggle.getAttribute( 'aria-expanded' ) ) {
					dropdownToggle.setAttribute( 'aria-expanded', 'true' );
				} else {
					dropdownToggle.setAttribute( 'aria-expanded', 'false' );
				}

				var subMenu = closestLi.querySelector( '.sub-menu' );

				closestLi.classList.toggle( 'sfHover' );
				subMenu.classList.toggle( 'toggled-on' );
			}

			e.stopPropagation();
		}

		for ( var i = 0; i < dropdownToggle.length; i++ ) {
			dropdownToggle[i].addEventListener( 'click', toggleSubNav, false );
			dropdownToggle[i].addEventListener( 'keypress', function( e ) {
				var key = e.which || e.keyCode;
				if (key === 13) { // 13 is enter
					toggleSubNav( e, this );
				}
			}, false );
		}

		/**
		 * Disable the mobile menu if our toggle isn't visible.
		 * Makes it possible to style mobile item with .toggled class.
		 */
		var checkMobile = function() {
			for ( var i = 0; i < allNavToggles.length; i++ ) {
				if ( allNavToggles[i].offsetParent === null ) {
					var closestParent = allNavToggles[i].closest( 'nav' );
					var closestNav = closestParent.getElementsByTagName( 'ul' )[0];
					var closestNavItems = closestNav.getElementsByTagName( 'li' );
					var closestSubMenus = closestNav.getElementsByTagName( 'ul' );
					if ( closestParent ) {
						closestParent.classList.remove( 'toggled' );
						htmlEl.classList.remove( 'mobile-menu-open' );
						allNavToggles[i].setAttribute( 'aria-expanded', 'false' );

						for ( var li = 0; li < closestNavItems.length; li++ ) {
							closestNavItems[li].classList.remove( 'sfHover' );
						}

						for ( var sm = 0; sm < closestSubMenus.length; sm++ ) {
							closestSubMenus[sm].classList.remove( 'toggled-on' );
						}

						if ( closestNav ) {
							closestNav.setAttribute( 'aria-hidden', 'true' );
						}
					}
				}
			}
		}
		window.addEventListener( 'resize', checkMobile, false );
		window.addEventListener( 'orientationchange', checkMobile, false );

	}

})();
