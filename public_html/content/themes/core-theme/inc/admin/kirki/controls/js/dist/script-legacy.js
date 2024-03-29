/* jshint -W079 */
if ( _.isUndefined( window.kirkiSetSettingValue ) ) {
	var kirkiSetSettingValue = { // jscs:ignore requireVarDeclFirst

		/**
		 * Set the value of the control.
		 *
		 * @since 3.0.0
		 * @param string setting The setting-ID.
		 * @param mixed  value   The value.
		 */
		set: function( setting, value ) {

			/**
			 * Get the control of the sub-setting.
			 * This will be used to get properties we need from that control,
			 * and determine if we need to do any further work based on those.
			 */
			var $this = this,
			    subControl = wp.customize.settings.controls[ setting ],
			    valueJSON;

			// If the control doesn't exist then return.
			if ( _.isUndefined( subControl ) ) {
				return true;
			}

			// First set the value in the wp object. The control type doesn't matter here.
			$this.setValue( setting, value );

			// Process visually changing the value based on the control type.
			switch ( subControl.type ) {

				case 'kirki-background':
					if ( ! _.isUndefined( value['background-color'] ) ) {
						$this.setColorPicker( $this.findElement( setting, '.kirki-color-control' ), value['background-color'] );
					}
					$this.findElement( setting, '.placeholder, .thumbnail' ).removeClass().addClass( 'placeholder' ).html( 'No file selected' );
					_.each( ['background-repeat', 'background-position'], function( subVal ) {
						if ( ! _.isUndefined( value[ subVal ] ) ) {
							$this.setSelectWoo( $this.findElement( setting, '.' + subVal + ' select' ), value[ subVal ] );
						}
					});
					_.each( ['background-size', 'background-attachment'], function( subVal ) {
						jQuery( $this.findElement( setting, '.' + subVal + ' input[value="' + value + '"]' ) ).prop( 'checked', true );
					});
					valueJSON = JSON.stringify( value ).replace( /'/g, '&#39' );
					jQuery( $this.findElement( setting, '.background-hidden-value' ).attr( 'value', valueJSON ) ).trigger( 'change' );
					break;

				case 'kirki-code':
					jQuery( $this.findElement( setting, '.CodeMirror' ) )[0].CodeMirror.setValue( value );
					break;

				case 'checkbox':
				case 'kirki-switch':
				case 'kirki-toggle':
					value = ( 1 === value || '1' === value || true === value ) ? true : false;
					jQuery( $this.findElement( setting, 'input' ) ).prop( 'checked', value );
					wp.customize.instance( setting ).set( value );
					break;

				case 'kirki-select':
				case 'kirki-preset':
				case 'kirki-fontawesome':
					$this.setSelectWoo( $this.findElement( setting, 'select' ), value );
					break;

				case 'kirki-slider':
					jQuery( $this.findElement( setting, 'input' ) ).prop( 'value', value );
					jQuery( $this.findElement( setting, '.kirki_range_value .value' ) ).html( value );
					break;

				case 'kirki-generic':
					if ( _.isUndefined( subControl.choices ) || _.isUndefined( subControl.choices.element ) ) {
						subControl.choices.element = 'input';
					}
					jQuery( $this.findElement( setting, subControl.choices.element ) ).prop( 'value', value );
					break;

				case 'kirki-color':
					$this.setColorPicker( $this.findElement( setting, '.kirki-color-control' ), value );
					break;

				case 'kirki-multicheck':
					$this.findElement( setting, 'input' ).each( function() {
						jQuery( this ).prop( 'checked', false );
					});
					_.each( value, function( subValue, i ) {
						jQuery( $this.findElement( setting, 'input[value="' + value[ i ] + '"]' ) ).prop( 'checked', true );
					});
					break;

				case 'kirki-multicolor':
					_.each( value, function( subVal, index ) {
						$this.setColorPicker( $this.findElement( setting, '.multicolor-index-' + index ), subVal );
					});
					break;

				case 'kirki-radio-buttonset':
				case 'kirki-radio-image':
				case 'kirki-radio':
				case 'kirki-dashicons':
				case 'kirki-color-palette':
				case 'kirki-palette':
					jQuery( $this.findElement( setting, 'input[value="' + value + '"]' ) ).prop( 'checked', true );
					break;

				case 'kirki-typography':
					_.each( ['font-family', 'variant', 'subsets'], function( subVal ) {
						if ( ! _.isUndefined( value[ subVal ] ) ) {
							$this.setSelectWoo( $this.findElement( setting, '.' + subVal + ' select' ), value[ subVal ] );
						}
					});
					_.each( ['font-size', 'line-height', 'letter-spacing', 'word-spacing'], function( subVal ) {
						if ( ! _.isUndefined( value[ subVal ] ) ) {
							jQuery( $this.findElement( setting, '.' + subVal + ' input' ) ).prop( 'value', value[ subVal ] );
						}
					});

					if ( ! _.isUndefined( value.color ) ) {
						$this.setColorPicker( $this.findElement( setting, '.kirki-color-control' ), value.color );
					}
					valueJSON = JSON.stringify( value ).replace( /'/g, '&#39' );
					jQuery( $this.findElement( setting, '.typography-hidden-value' ).attr( 'value', valueJSON ) ).trigger( 'change' );
					break;

				case 'kirki-dimensions':
					_.each( value, function( subValue, id ) {
						jQuery( $this.findElement( setting, '.' + id + ' input' ) ).prop( 'value', subValue );
					});
					break;

				case 'kirki-repeater':

					// Not yet implemented.
					break;

				case 'kirki-custom':

					// Do nothing.
					break;
				default:
					jQuery( $this.findElement( setting, 'input' ) ).prop( 'value', value );
			}
		},

		/**
		 * Set the value for colorpickers.
		 * CAUTION: This only sets the value visually, it does not change it in th wp object.
		 *
		 * @since 3.0.0
		 * @param object selector jQuery object for this element.
		 * @param string value    The value we want to set.
		 */
		setColorPicker: function( selector, value ) {
			selector.attr( 'data-default-color', value ).data( 'default-color', value ).wpColorPicker( 'color', value );
		},

		/**
		 * Sets the value in a selectWoo element.
		 * CAUTION: This only sets the value visually, it does not change it in th wp object.
		 *
		 * @since 3.0.0
		 * @param string selector The CSS identifier for this selectWoo.
		 * @param string value    The value we want to set.
		 */
		setSelectWoo: function( selector, value ) {
			jQuery( selector ).selectWoo().val( value ).trigger( 'change' );
		},

		/**
		 * Sets the value in textarea elements.
		 * CAUTION: This only sets the value visually, it does not change it in th wp object.
		 *
		 * @since 3.0.0
		 * @param string selector The CSS identifier for this textarea.
		 * @param string value    The value we want to set.
		 */
		setTextarea: function( selector, value ) {
			jQuery( selector ).prop( 'value', value );
		},

		/**
		 * Finds an element inside this control.
		 *
		 * @since 3.0.0
		 * @param string setting The setting ID.
		 * @param string element The CSS identifier.
		 */
		findElement: function( setting, element ) {
			return wp.customize.control( setting ).container.find( element );
		},

		/**
		 * Updates the value in the wp.customize object.
		 *
		 * @since 3.0.0
		 * @param string setting The setting-ID.
		 * @param mixed  value   The value.
		 */
		setValue: function( setting, value, timeout ) {
			timeout = ( _.isUndefined( timeout ) ) ? 100 : parseInt( timeout, 10 );
			wp.customize.instance( setting ).set({});
			setTimeout( function() {
				wp.customize.instance( setting ).set( value );
			}, timeout );
		}
	};
}
;/**
 * The majority of the code in this file
 * is derived from the wp-customize-posts plugin
 * and the work of @westonruter to whom I am very grateful.
 *
 * @see https://github.com/xwp/wp-customize-posts
 */

( function() {
	'use strict';

	/**
	 * A dynamic color-alpha control.
	 *
	 * @class
	 * @augments wp.customize.Control
	 * @augments wp.customize.Class
	 */
	wp.customize.kirkiDynamicControl = wp.customize.Control.extend({

		initialize: function( id, options ) {
			var control = this,
			    args    = options || {};

			args.params = args.params || {};
			if ( ! args.params.type ) {
				args.params.type = 'kirki-generic';
			}
			if ( ! args.params.content ) {
				args.params.content = jQuery( '<li></li>' );
				args.params.content.attr( 'id', 'customize-control-' + id.replace( /]/g, '' ).replace( /\[/g, '-' ) );
				args.params.content.attr( 'class', 'customize-control customize-control-' + args.params.type );
			}

			control.propertyElements = [];
			wp.customize.Control.prototype.initialize.call( control, id, args );
		},

		/**
		 * Add bidirectional data binding links between inputs and the setting(s).
		 *
		 * This is copied from wp.customize.Control.prototype.initialize(). It
		 * should be changed in Core to be applied once the control is embedded.
		 *
		 * @private
		 * @returns {void}
		 */
		_setUpSettingRootLinks: function() {
			var control = this,
			    nodes   = control.container.find( '[data-customize-setting-link]' );

			nodes.each( function() {
				var node = jQuery( this );

				wp.customize( node.data( 'customizeSettingLink' ), function( setting ) {
					var element = new wp.customize.Element( node );
					control.elements.push( element );
					element.sync( setting );
					element.set( setting() );
				});
			});
		},

		/**
		 * Add bidirectional data binding links between inputs and the setting properties.
		 *
		 * @private
		 * @returns {void}
		 */
		_setUpSettingPropertyLinks: function() {
			var control = this,
			    nodes;

			if ( ! control.setting ) {
				return;
			}

			nodes = control.container.find( '[data-customize-setting-property-link]' );

			nodes.each( function() {
				var node = jQuery( this ),
				    element,
				    propertyName = node.data( 'customizeSettingPropertyLink' );

				element = new wp.customize.Element( node );
				control.propertyElements.push( element );
				element.set( control.setting()[ propertyName ] );

				element.bind( function( newPropertyValue ) {
					var newSetting = control.setting();
					if ( newPropertyValue === newSetting[ propertyName ] ) {
						return;
					}
					newSetting = _.clone( newSetting );
					newSetting[ propertyName ] = newPropertyValue;
					control.setting.set( newSetting );
				} );
				control.setting.bind( function( newValue ) {
					if ( newValue[ propertyName ] !== element.get() ) {
						element.set( newValue[ propertyName ] );
					}
				} );
			});
		},

		/**
		 * @inheritdoc
		 */
		ready: function() {
			var control = this;

			control._setUpSettingRootLinks();
			control._setUpSettingPropertyLinks();

			wp.customize.Control.prototype.ready.call( control );

			control.deferred.embedded.done( function() {
				control.initKirkiControl();
			});
		},

		/**
		 * Embed the control in the document.
		 *
		 * Override the embed() method to do nothing,
		 * so that the control isn't embedded on load,
		 * unless the containing section is already expanded.
		 *
		 * @returns {void}
		 */
		embed: function() {
			var control   = this,
			    sectionId = control.section();

			if ( ! sectionId ) {
				return;
			}

			wp.customize.section( sectionId, function( section ) {
				if ( 'kirki-expanded' === section.params.type || section.expanded() || wp.customize.settings.autofocus.control === control.id ) {
					control.actuallyEmbed();
				} else {
					section.expanded.bind( function( expanded ) {
						if ( expanded ) {
							control.actuallyEmbed();
						}
					} );
				}
			} );
		},

		/**
		 * Deferred embedding of control when actually
		 *
		 * This function is called in Section.onChangeExpanded() so the control
		 * will only get embedded when the Section is first expanded.
		 *
		 * @returns {void}
		 */
		actuallyEmbed: function() {
			var control = this;
			if ( 'resolved' === control.deferred.embedded.state() ) {
				return;
			}
			control.renderContent();
			control.deferred.embedded.resolve(); // This triggers control.ready().
		},

		/**
		 * This is not working with autofocus.
		 *
		 * @param {object} [args] Args.
		 * @returns {void}
		 */
		focus: function( args ) {
			var control = this;
			control.actuallyEmbed();
			wp.customize.Control.prototype.focus.call( control, args );
		},

		initKirkiControl: function() {

			var control = this;

			// Save the value
			this.container.on( 'change keyup paste click', 'input', function() {
				control.setting.set( jQuery( this ).val() );
			});
		},

		kirkiValidateCSSValue: function( value ) {

			var validUnits = ['rem', 'em', 'ex', '%', 'px', 'cm', 'mm', 'in', 'pt', 'pc', 'ch', 'vh', 'vw', 'vmin', 'vmax'],
				numericValue,
				unit;

			// 0 is always a valid value, and we can't check calc() values effectively.
			if ( '0' === value || ( 0 <= value.indexOf( 'calc(' ) && 0 <= value.indexOf( ')' ) ) ) {
				return true;
			}

			// Get the numeric value.
			numericValue = parseFloat( value );

			// Get the unit
			unit = value.replace( numericValue, '' );

			// Check the validity of the numeric value and units.
			if ( isNaN( numericValue ) || -1 === jQuery.inArray( unit, validUnits ) ) {
				return false;
			}
			return true;
		}
	});
})();
;/* global kirkiControlLoader */
wp.customize.controlConstructor['kirki-background'] = wp.customize.Control.extend({

	// When we're finished loading continue processing
	ready: function() {

		'use strict';

		var control = this;

		// Init the control.
		if ( ! _.isUndefined( window.kirkiControlLoader ) && _.isFunction( kirkiControlLoader ) ) {
			kirkiControlLoader( control );
		} else {
			control.initKirkiControl();
		}
	},

	initKirkiControl: function() {

		var control = this,
		    value   = control.getValue(),
		    picker  = control.container.find( '.kirki-color-control' );

		// Hide unnecessary controls if the value doesn't have an image.
		if ( _.isUndefined( value['background-image'] ) || '' === value['background-image'] ) {
			control.container.find( '.background-wrapper > .background-repeat' ).hide();
			control.container.find( '.background-wrapper > .background-position' ).hide();
			control.container.find( '.background-wrapper > .background-size' ).hide();
			control.container.find( '.background-wrapper > .background-attachment' ).hide();
		}

		// Color.
		picker.wpColorPicker({
			change: function() {
				setTimeout( function() {
					control.saveValue( 'background-color', picker.val() );
				}, 100 );
			}
		});

		// Background-Repeat.
		control.container.on( 'change', '.background-repeat select', function() {
			control.saveValue( 'background-repeat', jQuery( this ).val() );
		});

		// Background-Size.
		control.container.on( 'change click', '.background-size input', function() {
			control.saveValue( 'background-size', jQuery( this ).val() );
		});

		// Background-Position.
		control.container.on( 'change', '.background-position select', function() {
			control.saveValue( 'background-position', jQuery( this ).val() );
		});

		// Background-Attachment.
		control.container.on( 'change click', '.background-attachment input', function() {
			control.saveValue( 'background-attachment', jQuery( this ).val() );
		});

		// Background-Image.
		control.container.on( 'click', '.background-image-upload-button', function( e ) {
			var image = wp.media({ multiple: false }).open().on( 'select', function() {

				// This will return the selected image from the Media Uploader, the result is an object.
				var uploadedImage = image.state().get( 'selection' ).first(),
				    previewImage   = uploadedImage.toJSON().sizes.full.url,
				    imageUrl,
				    imageID,
				    imageWidth,
				    imageHeight,
				    preview,
				    removeButton;

				if ( ! _.isUndefined( uploadedImage.toJSON().sizes.medium ) ) {
					previewImage = uploadedImage.toJSON().sizes.medium.url;
				} else if ( ! _.isUndefined( uploadedImage.toJSON().sizes.thumbnail ) ) {
					previewImage = uploadedImage.toJSON().sizes.thumbnail.url;
				}

				imageUrl    = uploadedImage.toJSON().sizes.full.url;
				imageID     = uploadedImage.toJSON().id;
				imageWidth  = uploadedImage.toJSON().width;
				imageHeight = uploadedImage.toJSON().height;

				// Show extra controls if the value has an image.
				if ( '' !== imageUrl ) {
					control.container.find( '.background-wrapper > .background-repeat, .background-wrapper > .background-position, .background-wrapper > .background-size, .background-wrapper > .background-attachment' ).show();
				}

				control.saveValue( 'background-image', imageUrl );
				preview      = control.container.find( '.placeholder, .thumbnail' );
				removeButton = control.container.find( '.background-image-upload-remove-button' );

				if ( preview.length ) {
					preview.removeClass().addClass( 'thumbnail thumbnail-image' ).html( '<img src="' + previewImage + '" alt="" />' );
				}
				if ( removeButton.length ) {
					removeButton.show();
				}
		    });

			e.preventDefault();
		});

		control.container.on( 'click', '.background-image-upload-remove-button', function( e ) {

			var preview,
			    removeButton;

			e.preventDefault();

			control.saveValue( 'background-image', '' );

			preview      = control.container.find( '.placeholder, .thumbnail' );
			removeButton = control.container.find( '.background-image-upload-remove-button' );

			// Hide unnecessary controls.
			control.container.find( '.background-wrapper > .background-repeat' ).hide();
			control.container.find( '.background-wrapper > .background-position' ).hide();
			control.container.find( '.background-wrapper > .background-size' ).hide();
			control.container.find( '.background-wrapper > .background-attachment' ).hide();

			if ( preview.length ) {
				preview.removeClass().addClass( 'placeholder' ).html( 'No file selected' );
			}
			if ( removeButton.length ) {
				removeButton.hide();
			}
		});
	},

	/**
	 * Gets the value.
	 */
	getValue: function() {

		var control = this,
		    value   = {};

		// Make sure everything we're going to need exists.
		_.each( control.params['default'], function( defaultParamValue, param ) {
			if ( false !== defaultParamValue ) {
				value[ param ] = defaultParamValue;
				if ( ! _.isUndefined( control.setting._value[ param ] ) ) {
					value[ param ] = control.setting._value[ param ];
				}
			}
		});
		_.each( control.setting._value, function( subValue, param ) {
			if ( _.isUndefined( value[ param ] ) ) {
				value[ param ] = subValue;
			}
		});
		return value;
	},

	/**
	 * Saves the value.
	 */
	saveValue: function( property, value ) {

		'use strict';

		var control   = this,
		    input     = jQuery( '#customize-control-' + control.id.replace( '[', '-' ).replace( ']', '' ) + ' .background-hidden-value' ),
		    valueJSON = jQuery( input ).val(),
		    valueObj  = JSON.parse( valueJSON );

		valueObj[ property ] = value;
		control.setting.set( valueObj );
		jQuery( input ).attr( 'value', JSON.stringify( valueObj ) ).trigger( 'change' );
	}
});
;wp.customize.controlConstructor['kirki-code'] = wp.customize.kirkiDynamicControl.extend({

	initKirkiControl: function() {

		var control  = this;

		// Early exit if wp.customize.CodeEditorControl is not available.
		if ( _.isUndefined( wp.customize.CodeEditorControl ) ) {
			return;
		}

		// Hide the textarea.
		jQuery( control.container.find( 'textarea.kirki-codemirror-editor' ) ).hide();

		// Add the control.
		wp.customize.control.add( new wp.customize.CodeEditorControl( control.id, {
			section: control.params.section,
			priority: control.params.priority,
			label: control.params.label,
			editor_settings: {
				codemirror: {
					mode: control.params.choices.language
				}
			},
			settings: { 'default': control.id }
		} ) );
	}
});
;wp.customize.controlConstructor['kirki-color-palette'] = wp.customize.kirkiDynamicControl.extend({});
;wp.customize.controlConstructor['kirki-color'] = wp.customize.kirkiDynamicControl.extend({

	initKirkiControl: function() {
		var control = this,
		    picker  = control.container.find( '.kirki-color-control' ),
		    clear;

		// If we have defined any extra choices, make sure they are passed-on to Iris.
		if ( ! _.isUndefined( control.params.choices ) ) {
			picker.wpColorPicker( control.params.choices );
		}

		// Tweaks to make the "clear" buttons work.
		setTimeout( function() {
			clear = control.container.find( '.wp-picker-clear' );
			clear.click( function() {
				control.setting.set( '' );
			});
		}, 200 );

		// Saves our settings to the WP API
		picker.wpColorPicker({
			change: function() {

				// Small hack: the picker needs a small delay
				setTimeout( function() {
					control.setting.set( picker.val() );
				}, 20 );
			}
		});
	}
});
;wp.customize.controlConstructor['kirki-dashicons'] = wp.customize.kirkiDynamicControl.extend({});
;wp.customize.controlConstructor['kirki-date'] = wp.customize.kirkiDynamicControl.extend({

	initKirkiControl: function() {

		var control  = this;

		// Only add in WP 4.9+.
		if ( _.isUndefined( wp.customize.DateTimeControl ) ) {
			return;
		}

		// New method for the DateTime control.
		wp.customize.control.add( new wp.customize.DateTimeControl( control.id, {
			section: control.params.section,
			priority: control.params.priority,
			label: control.params.label,
			description: control.params.description,
			settings: { 'default': control.id },
			'default': control.params['default']
		} ) );
	}
});
;/* global dimensionkirkiL10n */
wp.customize.controlConstructor['kirki-dimension'] = wp.customize.kirkiDynamicControl.extend({

	initKirkiControl: function() {

		var control = this,
		    value;

		// Notifications.
		control.kirkiNotifications();

		// Save the value
		this.container.on( 'change keyup paste', 'input', function() {

			value = jQuery( this ).val();
			control.setting.set( value );
		});
	},

	/**
	 * Handles notifications.
	 */
	kirkiNotifications: function() {

		var control = this;

		wp.customize( control.id, function( setting ) {
			setting.bind( function( value ) {
				var code = 'long_title';

				if ( false === control.kirkiValidateCSSValue( value ) ) {
					setting.notifications.add( code, new wp.customize.Notification(
						code,
						{
							type: 'warning',
							message: dimensionkirkiL10n['invalid-value']
						}
					) );
				} else {
					setting.notifications.remove( code );
				}
			} );
		} );
	}
});
;/* global dimensionskirkiL10n */
wp.customize.controlConstructor['kirki-dimensions'] = wp.customize.kirkiDynamicControl.extend({

	initKirkiControl: function() {

		var control     = this,
		    subControls = control.params.choices.controls,
		    value       = {},
		    subsArray   = [],
		    i;

		_.each( subControls, function( v, i ) {
			if ( true === v ) {
				subsArray.push( i );
			}
		} );

		for ( i = 0; i < subsArray.length; i++ ) {
			value[ subsArray[ i ] ] = control.setting._value[ subsArray[ i ] ];
			control.updateDimensionsValue( subsArray[ i ], value );
		}
	},

	/**
	 * Updates the value.
	 */
	updateDimensionsValue: function( context, value ) {

		var control = this;

		control.container.on( 'change keyup paste', '.' + context + ' input', function() {
			value[ context ] = jQuery( this ).val();

			// Notifications.
			control.kirkiNotifications();

			// Save the value
			control.saveValue( value );
		});
	},

	/**
	 * Saves the value.
	 */
	saveValue: function( value ) {

		var control  = this,
		    newValue = {};

		_.each( value, function( newSubValue, i ) {
			newValue[ i ] = newSubValue;
		});

		control.setting.set( newValue );
	},

	/**
	 * Handles notifications.
	 */
	kirkiNotifications: function() {

		var control = this;

		wp.customize( control.id, function( setting ) {
			setting.bind( function( value ) {
				var code = 'long_title',
				    subs = {},
				    message;

				setting.notifications.remove( code );

				_.each( ['top', 'bottom', 'left', 'right'], function( direction ) {
					if ( ! _.isUndefined( value[ direction ] ) ) {
						if ( false === control.kirkiValidateCSSValue( value[ direction ] ) ) {
							subs[ direction ] = dimensionskirkiL10n[ direction ];
						} else {
							delete subs[ direction ];
						}
					}
				});

				if ( ! _.isEmpty( subs ) ) {
					message = dimensionskirkiL10n['invalid-value'] + ' (' + _.values( subs ).toString() + ') ';
					setting.notifications.add( code, new wp.customize.Notification(
						code,
						{
							type: 'warning',
							message: message
						}
					) );
				} else {
					setting.notifications.remove( code );
				}
			} );
		} );
	}
});
;/* global tinyMCE */
wp.customize.controlConstructor['kirki-editor'] = wp.customize.kirkiDynamicControl.extend({

	initKirkiControl: function() {

		var control = this,
		    element = control.container.find( 'textarea' ),
		    id      = 'kirki-editor-' + control.id.replace( '[', '' ).replace( ']', '' ),
		    editor;

		wp.editor.initialize( id, {
			tinymce: {
				wpautop: true
			},
			quicktags: true,
			mediaButtons: true
		});

		editor = tinyMCE.get( id );

		if ( editor ) {
			editor.onChange.add( function( ed ) {
				var content;

				ed.save();
				content = editor.getContent();
				element.val( content ).trigger( 'change' );
				wp.customize.instance( control.id ).set( content );
			});
		}
	}
});
;/* global fontAwesomeJSON */
wp.customize.controlConstructor['kirki-fontawesome'] = wp.customize.kirkiDynamicControl.extend({

	initKirkiControl: function() {

		var control  = this,
		    element  = this.container.find( 'select' ),
		    icons    = jQuery.parseJSON( fontAwesomeJSON ),
		    selectValue,
		    selectWooOptions = {
				data: [],
				escapeMarkup: function( markup ) {
					return markup;
				},
				templateResult: function( val ) {
					return '<i class="fa fa-lg fa-' + val.id + '" aria-hidden="true"></i>' + ' ' + val.text;
				},
				templateSelection: function( val ) {
					return '<i class="fa fa-lg fa-' + val.id + '" aria-hidden="true"></i>' + ' ' + val.text;
				}
		    },
		    select;

		_.each( icons.icons, function( icon ) {
			selectWooOptions.data.push({
				id: icon.id,
				text: icon.name
			});
		});

		select = jQuery( element ).selectWoo( selectWooOptions );

		select.on( 'change', function() {
			selectValue = jQuery( this ).val();
			control.setting.set( selectValue );
		});
		select.val( control.setting._value ).trigger( 'change' );
	}
});
;wp.customize.controlConstructor['kirki-generic'] = wp.customize.kirkiDynamicControl.extend({});
;/* global kirkiControlLoader */
wp.customize.controlConstructor['kirki-image'] = wp.customize.Control.extend({

	// When we're finished loading continue processing
	ready: function() {

		'use strict';

		var control = this;

		// Init the control.
		if ( ! _.isUndefined( window.kirkiControlLoader ) && _.isFunction( kirkiControlLoader ) ) {
			kirkiControlLoader( control );
		} else {
			control.initKirkiControl();
		}
	},

	initKirkiControl: function() {

		var control       = this,
		    value         = control.getValue(),
		    saveAs        = ( ! _.isUndefined( control.params.choices ) && ! _.isUndefined( control.params.choices.save_as ) ) ? control.params.choices.save_as : 'url',
		    preview       = control.container.find( '.placeholder, .thumbnail' ),
		    previewImage  = ( 'array' === saveAs ) ? value.url : value,
		    removeButton  = control.container.find( '.image-upload-remove-button' ),
		    defaultButton = control.container.find( '.image-default-button' );

		control.container.find( '.kirki-controls-loading-spinner' ).hide();

		// Tweaks for save_as = id.
		if ( ( 'id' === saveAs || 'ID' === saveAs ) && '' !== value ) {
			wp.media.attachment( value ).fetch().then( function( mediaData ) {
				setTimeout( function() {
					var url = wp.media.attachment( value ).get( 'url' );
					preview.removeClass().addClass( 'thumbnail thumbnail-image' ).html( '<img src="' + url + '" alt="" />' );
				}, 700 );
			} );
		}

		// If value is not empty, hide the "default" button.
		if ( ( 'url' === saveAs && '' !== value ) || ( 'array' === saveAs && ! _.isUndefined( value.url ) && '' !== value.url ) ) {
			control.container.find( 'image-default-button' ).hide();
		}

		// If value is empty, hide the "remove" button.
		if ( ( 'url' === saveAs && '' === value ) || ( 'array' === saveAs && ( _.isUndefined( value.url ) || '' === value.url ) ) ) {
			removeButton.hide();
		}

		// If value is default, hide the default button.
		if ( value === control.params['default'] ) {
			control.container.find( 'image-default-button' ).hide();
		}

		if ( '' !== previewImage ) {
			preview.removeClass().addClass( 'thumbnail thumbnail-image' ).html( '<img src="' + previewImage + '" alt="" />' );
		}

		control.container.on( 'click', '.image-upload-button', function( e ) {
			var image = wp.media({ multiple: false }).open().on( 'select', function() {

					// This will return the selected image from the Media Uploader, the result is an object.
					var uploadedImage = image.state().get( 'selection' ).first(),
					    previewImage  = uploadedImage.toJSON().sizes.full.url;

					if ( ! _.isUndefined( uploadedImage.toJSON().sizes.medium ) ) {
						previewImage = uploadedImage.toJSON().sizes.medium.url;
					} else if ( ! _.isUndefined( uploadedImage.toJSON().sizes.thumbnail ) ) {
						previewImage = uploadedImage.toJSON().sizes.thumbnail.url;
					}

					if ( 'array' === saveAs ) {
						control.saveValue( 'id', uploadedImage.toJSON().id );
						control.saveValue( 'url', uploadedImage.toJSON().sizes.full.url );
						control.saveValue( 'width', uploadedImage.toJSON().width );
						control.saveValue( 'height', uploadedImage.toJSON().height );
					} else if ( 'id' === saveAs ) {
						control.saveValue( 'id', uploadedImage.toJSON().id );
					} else {
						control.saveValue( 'url', uploadedImage.toJSON().sizes.full.url );
					}

					if ( preview.length ) {
						preview.removeClass().addClass( 'thumbnail thumbnail-image' ).html( '<img src="' + previewImage + '" alt="" />' );
					}
					if ( removeButton.length ) {
						removeButton.show();
						defaultButton.hide();
					}
			    });

			e.preventDefault();
		});

		control.container.on( 'click', '.image-upload-remove-button', function( e ) {

			var preview,
			    removeButton,
			    defaultButton;

			e.preventDefault();

			control.saveValue( 'id', '' );
			control.saveValue( 'url', '' );
			control.saveValue( 'width', '' );
			control.saveValue( 'height', '' );

			preview       = control.container.find( '.placeholder, .thumbnail' );
			removeButton  = control.container.find( '.image-upload-remove-button' );
			defaultButton = control.container.find( '.image-default-button' );

			if ( preview.length ) {
				preview.removeClass().addClass( 'placeholder' ).html( 'No file selected' );
			}
			if ( removeButton.length ) {
				removeButton.hide();
				if ( jQuery( defaultButton ).hasClass( 'button' ) ) {
					defaultButton.show();
				}
			}
		});

		control.container.on( 'click', '.image-default-button', function( e ) {

			var preview,
			    removeButton,
			    defaultButton;

			e.preventDefault();

			control.saveValue( 'url', control.params['default'] );

			preview       = control.container.find( '.placeholder, .thumbnail' );
			removeButton  = control.container.find( '.image-upload-remove-button' );
			defaultButton = control.container.find( '.image-default-button' );

			if ( preview.length ) {
				preview.removeClass().addClass( 'thumbnail thumbnail-image' ).html( '<img src="' + control.params['default'] + '" alt="" />' );
			}
			if ( removeButton.length ) {
				removeButton.show();
				defaultButton.hide();
			}
		});
	},

	/**
	 * Gets the value.
	 */
	getValue: function() {
		var control = this,
		    value   = control.setting._value,
		    saveAs  = ( ! _.isUndefined( control.params.choices ) && ! _.isUndefined( control.params.choices.save_as ) ) ? control.params.choices.save_as : 'url';

		if ( 'array' === saveAs && _.isString( value ) ) {
			value = {
				url: value
			};
		}
		return value;
	},

	/**
	 * Saves the value.
	 */
	saveValue: function( property, value ) {
		var control   = this,
		    valueOld  = control.setting._value,
		    saveAs    = ( ! _.isUndefined( control.params.choices ) && ! _.isUndefined( control.params.choices.save_as ) ) ? control.params.choices.save_as : 'url';

		if ( 'array' === saveAs ) {
			if ( _.isString( valueOld ) ) {
				valueOld = {};
			}
			valueOld[ property ] = value;
			control.setting.set( valueOld );
			control.container.find( 'button' ).trigger( 'change' );
			return;
		}
		control.setting.set( value );
		control.container.find( 'button' ).trigger( 'change' );
	}
});
;wp.customize.controlConstructor['kirki-multicheck'] = wp.customize.kirkiDynamicControl.extend({

	initKirkiControl: function() {

		var control = this;

		// Save the value
		control.container.on( 'change', 'input', function() {
			var value = [],
			    i = 0;

			// Build the value as an object using the sub-values from individual checkboxes.
			jQuery.each( control.params.choices, function( key ) {
				if ( control.container.find( 'input[value="' + key + '"]' ).is( ':checked' ) ) {
					value[ i ] = key;
					i++;
				}
			});

			// Update the value in the customizer.
			control.setting.set( value );
		});
	}
});
;/* global kirkiControlLoader */
wp.customize.controlConstructor['kirki-multicolor'] = wp.customize.Control.extend({

	// When we're finished loading continue processing
	ready: function() {

		'use strict';

		var control = this;

		// Init the control.
		if ( ! _.isUndefined( window.kirkiControlLoader ) && _.isFunction( kirkiControlLoader ) ) {
			kirkiControlLoader( control );
		} else {
			control.initKirkiControl();
		}
	},

	initKirkiControl: function() {

		'use strict';

		var control = this,
		    colors  = control.params.choices,
		    keys    = Object.keys( colors ),
		    value   = this.params.value,
		    target  = control.container.find( '.iris-target' ),
		    i       = 0,
		    irisInput,
		    irisPicker;

		// Proxy function that handles changing the individual colors
		function kirkiMulticolorChangeHandler( control, value, subSetting ) {

			var picker = control.container.find( '.multicolor-index-' + subSetting ),
			    args   = {
					target: target[0],
					change: function() {

						// Color controls require a small delay.
						setTimeout( function() {

							// Set the value.
							control.saveValue( subSetting, picker.val() );

							// Trigger the change.
							control.container.find( '.multicolor-index-' + subSetting ).trigger( 'change' );
						}, 100 );
					}
			    };

			if ( _.isObject( colors.irisArgs ) ) {
				_.each( colors.irisArgs, function( irisValue, irisKey ) {
					args[ irisKey ] = irisValue;
				});
			}

			// Did we change the value?
			picker.wpColorPicker( args );
		}

		// Colors loop
		while ( i < Object.keys( colors ).length ) {

			kirkiMulticolorChangeHandler( this, value, keys[ i ] );

			// Move colorpicker to the 'iris-target' container div
			irisInput  = control.container.find( '.wp-picker-container .wp-picker-input-wrap' ),
			irisPicker = control.container.find( '.wp-picker-container .wp-picker-holder' );
			jQuery( irisInput[0] ).detach().appendTo( target[0] );
			jQuery( irisPicker[0] ).detach().appendTo( target[0] );

			i++;
		}
	},

	/**
	 * Saves the value.
	 */
	saveValue: function( property, value ) {

		'use strict';

		var control   = this,
		    input     = control.container.find( '.multicolor-hidden-value' ),
		    valueJSON = jQuery( input ).val(),
		    valueObj  = JSON.parse( valueJSON );

		valueObj[ property ] = value;
		jQuery( input ).attr( 'value', JSON.stringify( valueObj ) ).trigger( 'change' );
		control.setting.set( valueObj );
	}
});
;/* global numberKirkiL10n */
wp.customize.controlConstructor['kirki-number'] = wp.customize.kirkiDynamicControl.extend({

	initKirkiControl: function() {

		var control = this,
		    element = this.container.find( 'input' ),
		    step    = 1;

		// Set step value.
		if ( ! _.isUndefined( control.params.choices ) && ! _.isUndefined( control.params.choices.step ) ) {
			step = ( 'any' === control.params.choices.step ) ? '0.001' : control.params.choices.step;
		}

		// Init the spinner
		jQuery( element ).spinner({
			min: ( ! _.isUndefined( control.params.choices ) && ! _.isUndefined( control.params.choices.min ) ) ? control.params.choices.min : -99999,
			max: ( ! _.isUndefined( control.params.choices ) && ! _.isUndefined( control.params.choices.max ) ) ? control.params.choices.max : 99999,
			step: step
		});

		// On change
		this.container.on( 'change click keyup paste', 'input', function() {
			control.setting.set( jQuery( this ).val() );
		});

		// Notifications.
		control.kirkiNotifications();
	},

	/**
	 * Handles notifications.
	 */
	kirkiNotifications: function() {

		var control = this;

		wp.customize( control.id, function( setting ) {
			setting.bind( function( value ) {
				var code    = 'long_title',
				    min     = ( ! _.isUndefined( control.params.choices.min ) ) ? Number( control.params.choices.min ) : false,
				    max     = ( ! _.isUndefined( control.params.choices.max ) ) ? Number( control.params.choices.max ) : false,
				    step    = ( ! _.isUndefined( control.params.choices.step ) ) ? Number( control.params.choices.step ) : false,
				    invalid = false;

				// Make sure value is a number.
				value = Number( value );

				if ( false !== min && value < min ) {
					invalid = 'min-error';
				} else if ( false !== max && value > max ) {
					invalid = 'max-error';
				} else if ( false !== step && false !== min && ! Number.isInteger( ( value - min ) / step ) ) {
					invalid = 'step-error';
				}

				if ( false !== invalid ) {
					setting.notifications.add( code, new wp.customize.Notification( code, {
						type: 'warning',
						message: numberKirkiL10n[ invalid ]
					} ) );
				} else {
					setting.notifications.remove( code );
				}
			});
		});
	}
});
;wp.customize.controlConstructor['kirki-palette'] = wp.customize.kirkiDynamicControl.extend({});
;/* global kirkiSetSettingValue */
wp.customize.controlConstructor['kirki-preset'] = wp.customize.kirkiDynamicControl.extend({

	initKirkiControl: function() {

		var control = this,
		    selectValue;

		// Trigger a change
		this.container.on( 'change', 'select', function() {

			// Get the control's value
			selectValue = jQuery( this ).val();

			// Update the value using the customizer API and trigger the "save" button
			control.setting.set( selectValue );

			// We have to get the choices of this control
			// and then start parsing them to see what we have to do for each of the choices.
			jQuery.each( control.params.choices, function( key, value ) {

				// If the current value of the control is the key of the choice,
				// then we can continue processing, Otherwise there's no reason to do anything.
				if ( selectValue === key ) {

					// Each choice has an array of settings defined in it.
					// We'll have to loop through them all and apply the changes needed to them.
					jQuery.each( value.settings, function( presetSetting, presetSettingValue ) {
						kirkiSetSettingValue.set( presetSetting, presetSettingValue );
					});
				}
			});
			wp.customize.previewer.refresh();
		});
	}
});
;wp.customize.controlConstructor['kirki-radio-buttonset'] = wp.customize.kirkiDynamicControl.extend({});
;wp.customize.controlConstructor['kirki-radio-image'] = wp.customize.kirkiDynamicControl.extend({});
;wp.customize.controlConstructor['kirki-radio'] = wp.customize.kirkiDynamicControl.extend({});
;/* global kirkiControlLoader */
var RepeaterRow = function( rowIndex, container, label, control ) {

	'use strict';

	var self        = this;
	this.rowIndex   = rowIndex;
	this.container  = container;
	this.label      = label;
	this.header     = this.container.find( '.repeater-row-header' ),

	this.header.on( 'click', function() {
		self.toggleMinimize();
	});

	this.container.on( 'click', '.repeater-row-remove', function() {
		self.remove();
	});

	this.header.on( 'mousedown', function() {
		self.container.trigger( 'row:start-dragging' );
	});

	this.container.on( 'keyup change', 'input, select, textarea', function( e ) {
		self.container.trigger( 'row:update', [ self.rowIndex, jQuery( e.target ).data( 'field' ), e.target ] );
	});

	this.setRowIndex = function( rowIndex ) {
		this.rowIndex = rowIndex;
		this.container.attr( 'data-row', rowIndex );
		this.container.data( 'row', rowIndex );
		this.updateLabel();
	};

	this.toggleMinimize = function() {

		// Store the previous state.
		this.container.toggleClass( 'minimized' );
		this.header.find( '.dashicons' ).toggleClass( 'dashicons-arrow-up' ).toggleClass( 'dashicons-arrow-down' );
	};

	this.remove = function() {
		this.container.slideUp( 300, function() {
			jQuery( this ).detach();
		});
		this.container.trigger( 'row:remove', [ this.rowIndex ] );
	};

	this.updateLabel = function() {
		var rowLabelField,
		    rowLabel,
		    rowLabelSelector;

		if ( 'field' === this.label.type ) {
			rowLabelField = this.container.find( '.repeater-field [data-field="' + this.label.field + '"]' );
			if ( _.isFunction( rowLabelField.val ) ) {
				rowLabel = rowLabelField.val();
				if ( '' !== rowLabel ) {
					if ( ! _.isUndefined( control.params.fields[ this.label.field ] ) ) {
						if ( ! _.isUndefined( control.params.fields[ this.label.field ].type ) ) {
							if ( 'select' === control.params.fields[ this.label.field ].type ) {
								if ( ! _.isUndefined( control.params.fields[ this.label.field ].choices ) && ! _.isUndefined( control.params.fields[ this.label.field ].choices[ rowLabelField.val() ] ) ) {
									rowLabel = control.params.fields[ this.label.field ].choices[ rowLabelField.val() ];
								}
							} else if ( 'radio' === control.params.fields[ this.label.field ].type || 'radio-image' === control.params.fields[ this.label.field ].type ) {
								rowLabelSelector = control.selector + ' [data-row="' + this.rowIndex + '"] .repeater-field [data-field="' + this.label.field + '"]:checked';
								rowLabel = jQuery( rowLabelSelector ).val();
							}
						}
					}
					this.header.find( '.repeater-row-label' ).text( rowLabel );
					return;
				}
			}
		}
		this.header.find( '.repeater-row-label' ).text( this.label.value + ' ' + ( this.rowIndex + 1 ) );
	};
	this.updateLabel();
};

wp.customize.controlConstructor.repeater = wp.customize.Control.extend({

	// When we're finished loading continue processing
	ready: function() {

		'use strict';

		var control = this;

		// Init the control.
		if ( ! _.isUndefined( window.kirkiControlLoader ) && _.isFunction( kirkiControlLoader ) ) {
			kirkiControlLoader( control );
		} else {
			control.initKirkiControl();
		}
	},

	initKirkiControl: function() {

		'use strict';

		var control = this,
		    limit,
		    theNewRow;

		// The current value set in Control Class (set in Kirki_Customize_Repeater_Control::to_json() function)
		var settingValue = this.params.value;

		control.container.find( '.kirki-controls-loading-spinner' ).hide();

		// The hidden field that keeps the data saved (though we never update it)
		this.settingField = this.container.find( '[data-customize-setting-link]' ).first();

		// Set the field value for the first time, we'll fill it up later
		this.setValue( [], false );

		// The DIV that holds all the rows
		this.repeaterFieldsContainer = this.container.find( '.repeater-fields' ).first();

		// Set number of rows to 0
		this.currentIndex = 0;

		// Save the rows objects
		this.rows = [];

		// Default limit choice
		limit = false;
		if ( ! _.isUndefined( this.params.choices.limit ) ) {
			limit = ( 0 >= this.params.choices.limit ) ? false : parseInt( this.params.choices.limit, 10 );
		}

		this.container.on( 'click', 'button.repeater-add', function( e ) {
			e.preventDefault();
			if ( ! limit || control.currentIndex < limit ) {
				theNewRow = control.addRow();
				theNewRow.toggleMinimize();
				control.initColorPicker();
				control.initSelect( theNewRow );
			} else {
				jQuery( control.selector + ' .limit' ).addClass( 'highlight' );
			}
		});

		this.container.on( 'click', '.repeater-row-remove', function() {
			control.currentIndex--;
			if ( ! limit || control.currentIndex < limit ) {
				jQuery( control.selector + ' .limit' ).removeClass( 'highlight' );
			}
		});

		this.container.on( 'click keypress', '.repeater-field-image .upload-button,.repeater-field-cropped_image .upload-button,.repeater-field-upload .upload-button', function( e ) {
			e.preventDefault();
			control.$thisButton = jQuery( this );
			control.openFrame( e );
		});

		this.container.on( 'click keypress', '.repeater-field-image .remove-button,.repeater-field-cropped_image .remove-button', function( e ) {
			e.preventDefault();
			control.$thisButton = jQuery( this );
			control.removeImage( e );
		});

		this.container.on( 'click keypress', '.repeater-field-upload .remove-button', function( e ) {
			e.preventDefault();
			control.$thisButton = jQuery( this );
			control.removeFile( e );
		});

		/**
		 * Function that loads the Mustache template
		 */
		this.repeaterTemplate = _.memoize( function() {
			var compiled,
			    /*
			     * Underscore's default ERB-style templates are incompatible with PHP
			     * when asp_tags is enabled, so WordPress uses Mustache-inspired templating syntax.
			     *
			     * @see trac ticket #22344.
			     */
			    options = {
					evaluate: /<#([\s\S]+?)#>/g,
					interpolate: /\{\{\{([\s\S]+?)\}\}\}/g,
					escape: /\{\{([^\}]+?)\}\}(?!\})/g,
					variable: 'data'
			    };

			return function( data ) {
				compiled = _.template( control.container.find( '.customize-control-repeater-content' ).first().html(), null, options );
				return compiled( data );
			};
		});

		// When we load the control, the fields have not been filled up
		// This is the first time that we create all the rows
		if ( settingValue.length ) {
			_.each( settingValue, function( subValue ) {
				theNewRow = control.addRow( subValue );
				control.initColorPicker();
				control.initSelect( theNewRow, subValue );
			});
		}

		// Once we have displayed the rows, we cleanup the values
		this.setValue( settingValue, true, true );

		this.repeaterFieldsContainer.sortable({
			handle: '.repeater-row-header',
			update: function() {
				control.sort();
			}
		});

	},

	/**
	 * Open the media modal.
	 */
	openFrame: function( event ) {

		'use strict';

		if ( wp.customize.utils.isKeydownButNotEnterEvent( event ) ) {
			return;
		}

		if ( this.$thisButton.closest( '.repeater-field' ).hasClass( 'repeater-field-cropped_image' ) ) {
			this.initCropperFrame();
		} else {
			this.initFrame();
		}

		this.frame.open();
	},

	initFrame: function() {

		'use strict';

		var libMediaType = this.getMimeType();

		this.frame = wp.media({
			states: [
			new wp.media.controller.Library({
					library:  wp.media.query({ type: libMediaType }),
					multiple: false,
					date:     false
				})
			]
		});

		// When a file is selected, run a callback.
		this.frame.on( 'select', this.onSelect, this );
	},
	/**
	 * Create a media modal select frame, and store it so the instance can be reused when needed.
	 * This is mostly a copy/paste of Core api.CroppedImageControl in /acp/js/customize-control.js
	 */
	initCropperFrame: function() {

		'use strict';

		// We get the field id from which this was called
		var currentFieldId = this.$thisButton.siblings( 'input.hidden-field' ).attr( 'data-field' ),
		    attrs          = [ 'width', 'height', 'flex_width', 'flex_height' ], // A list of attributes to look for
		    libMediaType   = this.getMimeType();

		// Make sure we got it
		if ( _.isString( currentFieldId ) && '' !== currentFieldId ) {

			// Make fields is defined and only do the hack for cropped_image
			if ( _.isObject( this.params.fields[ currentFieldId ] ) && 'cropped_image' === this.params.fields[ currentFieldId ].type ) {

				//Iterate over the list of attributes
				attrs.forEach( function( el ) {

					// If the attribute exists in the field
					if ( ! _.isUndefined( this.params.fields[ currentFieldId ][ el ] ) ) {

						// Set the attribute in the main object
						this.params[ el ] = this.params.fields[ currentFieldId ][ el ];
					}
				}.bind( this ) );
			}
		}

		this.frame = wp.media({
			button: {
				text: 'Select and Crop',
				close: false
			},
			states: [
				new wp.media.controller.Library({
					library:         wp.media.query({ type: libMediaType }),
					multiple:        false,
					date:            false,
					suggestedWidth:  this.params.width,
					suggestedHeight: this.params.height
				}),
				new wp.media.controller.CustomizeImageCropper({
					imgSelectOptions: this.calculateImageSelectOptions,
					control: this
				})
			]
		});

		this.frame.on( 'select', this.onSelectForCrop, this );
		this.frame.on( 'cropped', this.onCropped, this );
		this.frame.on( 'skippedcrop', this.onSkippedCrop, this );

	},

	onSelect: function() {

		'use strict';

		var attachment = this.frame.state().get( 'selection' ).first().toJSON();

		if ( this.$thisButton.closest( '.repeater-field' ).hasClass( 'repeater-field-upload' ) ) {
			this.setFileInRepeaterField( attachment );
		} else {
			this.setImageInRepeaterField( attachment );
		}
	},

	/**
	 * After an image is selected in the media modal, switch to the cropper
	 * state if the image isn't the right size.
	 */

	onSelectForCrop: function() {

		'use strict';

		var attachment = this.frame.state().get( 'selection' ).first().toJSON();

		if ( this.params.width === attachment.width && this.params.height === attachment.height && ! this.params.flex_width && ! this.params.flex_height ) {
			this.setImageInRepeaterField( attachment );
		} else {
			this.frame.setState( 'cropper' );
		}
	},

	/**
	 * After the image has been cropped, apply the cropped image data to the setting.
	 *
	 * @param {object} croppedImage Cropped attachment data.
	 */
	onCropped: function( croppedImage ) {

		'use strict';

		this.setImageInRepeaterField( croppedImage );

	},

	/**
	 * Returns a set of options, computed from the attached image data and
	 * control-specific data, to be fed to the imgAreaSelect plugin in
	 * wp.media.view.Cropper.
	 *
	 * @param {wp.media.model.Attachment} attachment
	 * @param {wp.media.controller.Cropper} controller
	 * @returns {Object} Options
	 */
	calculateImageSelectOptions: function( attachment, controller ) {

		'use strict';

		var control    = controller.get( 'control' ),
		    flexWidth  = !! parseInt( control.params.flex_width, 10 ),
		    flexHeight = !! parseInt( control.params.flex_height, 10 ),
		    realWidth  = attachment.get( 'width' ),
		    realHeight = attachment.get( 'height' ),
		    xInit      = parseInt( control.params.width, 10 ),
		    yInit      = parseInt( control.params.height, 10 ),
		    ratio      = xInit / yInit,
		    xImg       = realWidth,
		    yImg       = realHeight,
		    x1,
		    y1,
		    imgSelectOptions;

		controller.set( 'canSkipCrop', ! control.mustBeCropped( flexWidth, flexHeight, xInit, yInit, realWidth, realHeight ) );

		if ( xImg / yImg > ratio ) {
			yInit = yImg;
			xInit = yInit * ratio;
		} else {
			xInit = xImg;
			yInit = xInit / ratio;
		}

		x1 = ( xImg - xInit ) / 2;
		y1 = ( yImg - yInit ) / 2;

		imgSelectOptions = {
			handles:     true,
			keys:        true,
			instance:    true,
			persistent:  true,
			imageWidth:  realWidth,
			imageHeight: realHeight,
			x1:          x1,
			y1:          y1,
			x2:          xInit + x1,
			y2:          yInit + y1
		};

		if ( false === flexHeight && false === flexWidth ) {
			imgSelectOptions.aspectRatio = xInit + ':' + yInit;
		}
		if ( false === flexHeight ) {
			imgSelectOptions.maxHeight = yInit;
		}
		if ( false === flexWidth ) {
			imgSelectOptions.maxWidth = xInit;
		}

		return imgSelectOptions;
	},

	/**
	 * Return whether the image must be cropped, based on required dimensions.
	 *
	 * @param {bool} flexW
	 * @param {bool} flexH
	 * @param {int}  dstW
	 * @param {int}  dstH
	 * @param {int}  imgW
	 * @param {int}  imgH
	 * @return {bool}
	 */
	mustBeCropped: function( flexW, flexH, dstW, dstH, imgW, imgH ) {

		'use strict';

		if ( ( true === flexW && true === flexH ) || ( true === flexW && dstH === imgH ) || ( true === flexH && dstW === imgW ) || ( dstW === imgW && dstH === imgH ) || ( imgW <= dstW ) ) {
			return false;
		}

		return true;
	},

	/**
	 * If cropping was skipped, apply the image data directly to the setting.
	 */
	onSkippedCrop: function() {

		'use strict';

		var attachment = this.frame.state().get( 'selection' ).first().toJSON();
		this.setImageInRepeaterField( attachment );

	},

	/**
	 * Updates the setting and re-renders the control UI.
	 *
	 * @param {object} attachment
	 */
	setImageInRepeaterField: function( attachment ) {

		'use strict';

		var $targetDiv = this.$thisButton.closest( '.repeater-field-image,.repeater-field-cropped_image' );

		$targetDiv.find( '.kirki-image-attachment' ).html( '<img src="' + attachment.url + '">' ).hide().slideDown( 'slow' );

		$targetDiv.find( '.hidden-field' ).val( attachment.id );
		this.$thisButton.text( this.$thisButton.data( 'alt-label' ) );
		$targetDiv.find( '.remove-button' ).show();

		//This will activate the save button
		$targetDiv.find( 'input, textarea, select' ).trigger( 'change' );
		this.frame.close();

	},

	/**
	 * Updates the setting and re-renders the control UI.
	 *
	 * @param {object} attachment
	 */
	setFileInRepeaterField: function( attachment ) {

		'use strict';

		var $targetDiv = this.$thisButton.closest( '.repeater-field-upload' );

		$targetDiv.find( '.kirki-file-attachment' ).html( '<span class="file"><span class="dashicons dashicons-media-default"></span> ' + attachment.filename + '</span>' ).hide().slideDown( 'slow' );

		$targetDiv.find( '.hidden-field' ).val( attachment.id );
		this.$thisButton.text( this.$thisButton.data( 'alt-label' ) );
		$targetDiv.find( '.upload-button' ).show();
		$targetDiv.find( '.remove-button' ).show();

		//This will activate the save button
		$targetDiv.find( 'input, textarea, select' ).trigger( 'change' );
		this.frame.close();

	},

	getMimeType: function() {

		'use strict';

		// We get the field id from which this was called
		var currentFieldId = this.$thisButton.siblings( 'input.hidden-field' ).attr( 'data-field' );

		// Make sure we got it
		if ( _.isString( currentFieldId ) && '' !== currentFieldId ) {

			// Make fields is defined and only do the hack for cropped_image
			if ( _.isObject( this.params.fields[ currentFieldId ] ) && 'upload' === this.params.fields[ currentFieldId ].type ) {

				// If the attribute exists in the field
				if ( ! _.isUndefined( this.params.fields[ currentFieldId ].mime_type ) ) {

					// Set the attribute in the main object
					return this.params.fields[ currentFieldId ].mime_type;
				}
			}
		}
		return 'image';

	},

	removeImage: function( event ) {

		'use strict';

		var $targetDiv,
		    $uploadButton;

		if ( wp.customize.utils.isKeydownButNotEnterEvent( event ) ) {
			return;
		}

		$targetDiv = this.$thisButton.closest( '.repeater-field-image,.repeater-field-cropped_image,.repeater-field-upload' );
		$uploadButton = $targetDiv.find( '.upload-button' );

		$targetDiv.find( '.kirki-image-attachment' ).slideUp( 'fast', function() {
			jQuery( this ).show().html( jQuery( this ).data( 'placeholder' ) );
		});
		$targetDiv.find( '.hidden-field' ).val( '' );
		$uploadButton.text( $uploadButton.data( 'label' ) );
		this.$thisButton.hide();

		$targetDiv.find( 'input, textarea, select' ).trigger( 'change' );

	},

	removeFile: function( event ) {

		'use strict';

		var $targetDiv,
		    $uploadButton;

		if ( wp.customize.utils.isKeydownButNotEnterEvent( event ) ) {
			return;
		}

		$targetDiv = this.$thisButton.closest( '.repeater-field-upload' );
		$uploadButton = $targetDiv.find( '.upload-button' );

		$targetDiv.find( '.kirki-file-attachment' ).slideUp( 'fast', function() {
			jQuery( this ).show().html( jQuery( this ).data( 'placeholder' ) );
		});
		$targetDiv.find( '.hidden-field' ).val( '' );
		$uploadButton.text( $uploadButton.data( 'label' ) );
		this.$thisButton.hide();

		$targetDiv.find( 'input, textarea, select' ).trigger( 'change' );

	},

	/**
	 * Get the current value of the setting
	 *
	 * @return Object
	 */
	getValue: function() {

		'use strict';

		// The setting is saved in JSON
		return JSON.parse( decodeURI( this.setting.get() ) );

	},

	/**
	 * Set a new value for the setting
	 *
	 * @param newValue Object
	 * @param refresh If we want to refresh the previewer or not
	 */
	setValue: function( newValue, refresh, filtering ) {

		'use strict';

		// We need to filter the values after the first load to remove data requrired for diplay but that we don't want to save in DB
		var filteredValue = newValue,
		    filter        = [];

		if ( filtering ) {
			jQuery.each( this.params.fields, function( index, value ) {
				if ( 'image' === value.type || 'cropped_image' === value.type || 'upload' === value.type ) {
					filter.push( index );
				}
			});
			jQuery.each( newValue, function( index, value ) {
				jQuery.each( filter, function( ind, field ) {
					if ( ! _.isUndefined( value[ field ] ) && ! _.isUndefined( value[ field ].id ) ) {
						filteredValue[index][ field ] = value[ field ].id;
					}
				});
			});
		}

		this.setting.set( encodeURI( JSON.stringify( filteredValue ) ) );

		if ( refresh ) {

			// Trigger the change event on the hidden field so
			// previewer refresh the website on Customizer
			this.settingField.trigger( 'change' );
		}
	},

	/**
	 * Add a new row to repeater settings based on the structure.
	 *
	 * @param data (Optional) Object of field => value pairs (undefined if you want to get the default values)
	 */
	addRow: function( data ) {

		'use strict';

		var control       = this,
		    template      = control.repeaterTemplate(), // The template for the new row (defined on Kirki_Customize_Repeater_Control::render_content() ).
		    settingValue  = this.getValue(), // Get the current setting value.
		    newRowSetting = {}, // Saves the new setting data.
		    templateData, // Data to pass to the template
		    newRow,
		    i;

		if ( template ) {

			// The control structure is going to define the new fields
			// We need to clone control.params.fields. Assigning it
			// ould result in a reference assignment.
			templateData = jQuery.extend( true, {}, control.params.fields );

			// But if we have passed data, we'll use the data values instead
			if ( data ) {
				for ( i in data ) {
					if ( data.hasOwnProperty( i ) && templateData.hasOwnProperty( i ) ) {
						templateData[ i ]['default'] = data[ i ];
					}
				}
			}

			templateData.index = this.currentIndex;

			// Append the template content
			template = template( templateData );

			// Create a new row object and append the element
			newRow = new RepeaterRow(
				control.currentIndex,
				jQuery( template ).appendTo( control.repeaterFieldsContainer ),
				control.params.row_label,
				control
			);

			newRow.container.on( 'row:remove', function( e, rowIndex ) {
				control.deleteRow( rowIndex );
			});

			newRow.container.on( 'row:update', function( e, rowIndex, fieldName, element ) {
				control.updateField.call( control, e, rowIndex, fieldName, element );
				newRow.updateLabel();
			});

			// Add the row to rows collection
			this.rows[ this.currentIndex ] = newRow;

			for ( i in templateData ) {
				if ( templateData.hasOwnProperty( i ) ) {
					newRowSetting[ i ] = templateData[ i ]['default'];
				}
			}

			settingValue[ this.currentIndex ] = newRowSetting;
			this.setValue( settingValue, true );

			this.currentIndex++;

			return newRow;
		}
	},

	sort: function() {

		'use strict';

		var control     = this,
		    $rows       = this.repeaterFieldsContainer.find( '.repeater-row' ),
		    newOrder    = [],
		    settings    = control.getValue(),
		    newRows     = [],
		    newSettings = [];

		$rows.each( function( i, element ) {
			newOrder.push( jQuery( element ).data( 'row' ) );
		});

		jQuery.each( newOrder, function( newPosition, oldPosition ) {
			newRows[ newPosition ] = control.rows[ oldPosition ];
			newRows[ newPosition ].setRowIndex( newPosition );

			newSettings[ newPosition ] = settings[ oldPosition ];
		});

		control.rows = newRows;
		control.setValue( newSettings );

	},

	/**
	 * Delete a row in the repeater setting
	 *
	 * @param index Position of the row in the complete Setting Array
	 */
	deleteRow: function( index ) {

		'use strict';

		var currentSettings = this.getValue(),
		    row,
		    i,
		    prop;

		if ( currentSettings[ index ] ) {

			// Find the row
			row = this.rows[ index ];
			if ( row ) {

				// Remove the row settings
				delete currentSettings[ index ];

				// Remove the row from the rows collection
				delete this.rows[ index ];

				// Update the new setting values
				this.setValue( currentSettings, true );

			}

		}

		// Remap the row numbers
		i = 1;
		for ( prop in this.rows ) {
			if ( this.rows.hasOwnProperty( prop ) && this.rows[ prop ] ) {
				this.rows[ prop ].updateLabel();
				i++;
			}
		}
	},

	/**
	 * Update a single field inside a row.
	 * Triggered when a field has changed
	 *
	 * @param e Event Object
	 */
	updateField: function( e, rowIndex, fieldId, element ) {

		'use strict';

		var type,
		    row,
		    currentSettings;

		if ( ! this.rows[ rowIndex ] ) {
			return;
		}

		if ( ! this.params.fields[ fieldId ] ) {
			return;
		}

		type            = this.params.fields[ fieldId].type;
		row             = this.rows[ rowIndex ];
		currentSettings = this.getValue();

		element = jQuery( element );

		if ( _.isUndefined( currentSettings[ row.rowIndex ][ fieldId ] ) ) {
			return;
		}

		if ( 'checkbox' === type ) {
			currentSettings[ row.rowIndex ][ fieldId ] = element.is( ':checked' );
		} else {

			// Update the settings
			currentSettings[ row.rowIndex ][ fieldId ] = element.val();
		}
		this.setValue( currentSettings, true );
	},

	/**
	 * Init the color picker on color fields
	 * Called after AddRow
	 *
	 */
	initColorPicker: function() {

		'use strict';

		var control     = this,
		    colorPicker = control.container.find( '.color-picker-hex' ),
		    options     = {},
		    fieldId     = colorPicker.data( 'field' );

		// We check if the color palette parameter is defined.
		if ( ! _.isUndefined( fieldId ) && ! _.isUndefined( control.params.fields[ fieldId ] ) && ! _.isUndefined( control.params.fields[ fieldId ].palettes ) && _.isObject( control.params.fields[ fieldId ].palettes ) ) {
			options.palettes = control.params.fields[ fieldId ].palettes;
		}

		// When the color picker value is changed we update the value of the field
		options.change = function( event, ui ) {

			var currentPicker   = jQuery( event.target ),
			    row             = currentPicker.closest( '.repeater-row' ),
			    rowIndex        = row.data( 'row' ),
			    currentSettings = control.getValue();

			currentSettings[ rowIndex ][ currentPicker.data( 'field' ) ] = ui.color.toString();
			control.setValue( currentSettings, true );

		};

		// Init the color picker
		if ( 0 !== colorPicker.length ) {
			colorPicker.wpColorPicker( options );
		}
	},

	/**
	 * Init the dropdown-pages field with selectWoo
	 * Called after AddRow
	 *
	 * @param {object} theNewRow the row that was added to the repeater
	 * @param {object} data the data for the row if we're initializing a pre-existing row
	 *
	 */
	initSelect: function( theNewRow, data ) {

		'use strict';

		var control  = this,
		    dropdown = theNewRow.container.find( '.repeater-field select' ),
		    $select,
		    dataField,
		    multiple,
		    selectWooOptions = {};

		if ( 0 === dropdown.length ) {
			return;
		}

		dataField = dropdown.data( 'field' );
		multiple  = jQuery( dropdown ).data( 'multiple' );
		if ( 'undefed' !== multiple && jQuery.isNumeric( multiple ) ) {
			multiple = parseInt( multiple, 10 );
			if ( 1 < multiple ) {
				selectWooOptions.maximumSelectionLength = multiple;
			}
		}
		$select   = jQuery( dropdown ).selectWoo( selectWooOptions ).val( data[ dataField ] );

		this.container.on( 'change', '.repeater-field select', function( event ) {

			var currentDropdown = jQuery( event.target ),
			    row             = currentDropdown.closest( '.repeater-row' ),
			    rowIndex        = row.data( 'row' ),
			    currentSettings = control.getValue();

			currentSettings[ rowIndex ][ currentDropdown.data( 'field' ) ] = jQuery( this ).val();
			control.setValue( currentSettings );
		});
	}
});
;wp.customize.controlConstructor['kirki-select'] = wp.customize.kirkiDynamicControl.extend({

	initKirkiControl: function() {

		var control  = this,
		    element  = this.container.find( 'select' ),
		    multiple = parseInt( element.data( 'multiple' ), 10 ),
		    selectValue,
		    selectWooOptions = {
				escapeMarkup: function( markup ) {
					return markup;
				}
		    };

		if ( 1 < multiple ) {
			selectWooOptions.maximumSelectionLength = multiple;
		}
		jQuery( element ).selectWoo( selectWooOptions ).on( 'change', function() {
			selectValue = jQuery( this ).val();
			control.setting.set( selectValue );
		});
	}
});
;wp.customize.controlConstructor['kirki-slider'] = wp.customize.kirkiDynamicControl.extend({

	initKirkiControl: function() {
		var control      = this,
		    changeAction = ( 'postMessage' === control.setting.transport ) ? 'mousemove change' : 'change',
			rangeInput   = control.container.find( 'input[type="range"]' ),
			textInput    = control.container.find( 'input[type="text"]' ),
		    value        = control.setting._value;

		// Set the initial value in the text input.
		textInput.attr( 'value', value );

		// If the range input value changes,
		// copy the value to the text input
		// and then save.
		rangeInput.on( changeAction, function() {
			textInput.attr( 'value', rangeInput.val() );
			control.setting.set( rangeInput.val() );
		} );

		// If the text input value changes,
		// copy the value to the range input
		// and then save.
		textInput.on( 'input paste change', function() {
			rangeInput.attr( 'value', textInput.val() );
			control.setting.set( textInput.val() );
		} );

		// If the reset button is clicked,
		// set slider and text input values to default
		// and hen save.
		control.container.find( '.slider-reset' ).on( 'click', function() {
			textInput.attr( 'value', control.params['default'] );
			rangeInput.attr( 'value', control.params['default'] );
			control.setting.set( textInput.val() );
		} );
	}
});
;/* global kirkiControlLoader */
wp.customize.controlConstructor['kirki-sortable'] = wp.customize.Control.extend({

	// When we're finished loading continue processing
	ready: function() {

		'use strict';

		var control = this;

		// Init the control.
		if ( ! _.isUndefined( window.kirkiControlLoader ) && _.isFunction( kirkiControlLoader ) ) {
			kirkiControlLoader( control );
		} else {
			control.initKirkiControl();
		}
	},

	initKirkiControl: function() {

		'use strict';

		var control = this;

		control.container.find( '.kirki-controls-loading-spinner' ).hide();

		// Set the sortable container.
		control.sortableContainer = control.container.find( 'ul.sortable' ).first();

		// Init sortable.
		control.sortableContainer.sortable({

			// Update value when we stop sorting.
			stop: function() {
				control.updateValue();
			}
		}).disableSelection().find( 'li' ).each( function() {

			// Enable/disable options when we click on the eye of Thundera.
			jQuery( this ).find( 'i.visibility' ).click( function() {
				jQuery( this ).toggleClass( 'dashicons-visibility-faint' ).parents( 'li:eq(0)' ).toggleClass( 'invisible' );
			});
		}).click( function() {

			// Update value on click.
			control.updateValue();
		});
	},

	/**
	 * Updates the sorting list
	 */
	updateValue: function() {

		'use strict';

		var control = this,
		    newValue = [];

		this.sortableContainer.find( 'li' ).each( function() {
			if ( ! jQuery( this ).is( '.invisible' ) ) {
				newValue.push( jQuery( this ).data( 'value' ) );
			}
		});
		control.setting.set( newValue );
	}
});
;wp.customize.controlConstructor['kirki-switch'] = wp.customize.kirkiDynamicControl.extend({

	initKirkiControl: function() {

		'use strict';

		var control       = this,
		    checkboxValue = control.setting._value,
		    on            = jQuery( control.container.find( '.switch-on' ) ),
		    off           = jQuery( control.container.find( '.switch-off' ) );

		// CSS modifications depending on label sizes.
		jQuery( control.container.find( '.switch label ' ) ).css( 'width', ( on.width() + off.width() + 40 ) + 'px' );
		jQuery( '#customize-control-' + control.id.replace( '[', '-' ).replace( ']', '' ) ).append(
			'<style>#customize-control-' + control.id.replace( '[', '-' ).replace( ']', '' ) + ' .switch label:after{width:' + ( on.width() + 13 ) + 'px;}#customize-control-' + control.id.replace( '[', '-' ).replace( ']', '' ) + ' .switch input:checked + label:after{left:' + ( on.width() + 22 ) + 'px;width:' + ( off.width() + 13 ) + 'px;}</style>'
		);

		// Save the value
		this.container.on( 'change', 'input', function() {
			checkboxValue = ( jQuery( this ).is( ':checked' ) ) ? true : false;
			control.setting.set( checkboxValue );
		});
	}
});
;wp.customize.controlConstructor['kirki-toggle'] = wp.customize.kirkiDynamicControl.extend({

	initKirkiControl: function() {

		var control = this,
		    checkboxValue = control.setting._value;

		// Save the value
		this.container.on( 'change', 'input', function() {
			checkboxValue = ( jQuery( this ).is( ':checked' ) ) ? true : false;
			control.setting.set( checkboxValue );
		});
	}
});
;/* global kirkiControlLoader, kirkiAllFonts */
wp.customize.controlConstructor['kirki-typography'] = wp.customize.Control.extend({

	// When we're finished loading continue processing
	ready: function() {

		'use strict';

		var control = this;

		// Init the control.
		if ( ! _.isUndefined( window.kirkiControlLoader ) && _.isFunction( kirkiControlLoader ) ) {
			kirkiControlLoader( control );
		} else {
			control.initKirkiControl();
		}
	},

	initKirkiControl: function() {

		'use strict';

		var control = this,
		    value   = control.getValue(),
		    picker;

		control.renderFontSelector();
		control.renderBackupFontSelector();
		control.renderVariantSelector();
		control.renderSubsetSelector();

		// Font-size.
		if ( control.params['default']['font-size'] ) {
			this.container.on( 'change keyup paste', '.font-size input', function() {
				control.saveValue( 'font-size', jQuery( this ).val() );
			});
		}

		// Line-height.
		if ( control.params['default']['line-height'] ) {
			this.container.on( 'change keyup paste', '.line-height input', function() {
				control.saveValue( 'line-height', jQuery( this ).val() );
			});
		}

		// Margin-top.
		if ( control.params['default']['margin-top'] ) {
			this.container.on( 'change keyup paste', '.margin-top input', function() {
				control.saveValue( 'margin-top', jQuery( this ).val() );
			});
		}

		// Margin-bottom.
		if ( control.params['default']['margin-bottom'] ) {
			this.container.on( 'change keyup paste', '.margin-bottom input', function() {
				control.saveValue( 'margin-bottom', jQuery( this ).val() );
			});
		}

		// Letter-spacing.
		if ( control.params['default']['letter-spacing'] ) {
			value['letter-spacing'] = ( jQuery.isNumeric( value['letter-spacing'] ) ) ? value['letter-spacing'] + 'px' : value['letter-spacing'];
			this.container.on( 'change keyup paste', '.letter-spacing input', function() {
				value['letter-spacing'] = ( jQuery.isNumeric( jQuery( this ).val() ) ) ? jQuery( this ).val() + 'px' : jQuery( this ).val();
				control.saveValue( 'letter-spacing', value['letter-spacing'] );
			});
		}

		// Word-spacing.
		if ( control.params['default']['word-spacing'] ) {
			this.container.on( 'change keyup paste', '.word-spacing input', function() {
				control.saveValue( 'word-spacing', jQuery( this ).val() );
			});
		}

		// Text-align.
		if ( control.params['default']['text-align'] ) {
			this.container.on( 'change', '.text-align input', function() {
				control.saveValue( 'text-align', jQuery( this ).val() );
			});
		}

		// Text-transform.
		if ( control.params['default']['text-transform'] ) {
			jQuery( control.selector + ' .text-transform select' ).selectWoo().on( 'change', function() {
				control.saveValue( 'text-transform', jQuery( this ).val() );
			});
		}

		// Color.
		if ( control.params['default'].color ) {
			picker = this.container.find( '.kirki-color-control' );
			picker.wpColorPicker({
				change: function() {
					setTimeout( function() {
						control.saveValue( 'color', picker.val() );
					}, 100 );
				}
			});
		}
	},

	/**
	 * Adds the font-families to the font-family dropdown
	 * and instantiates selectWoo.
	 */
	renderFontSelector: function() {

		var control         = this,
		    selector        = control.selector + ' .font-family select',
		    data            = [],
		    standardFonts   = [],
		    googleFonts     = [],
		    value           = control.getValue(),
		    fonts           = control.getFonts(),
		    fontSelect;

		// Format standard fonts as an array.
		if ( ! _.isUndefined( fonts.standard ) ) {
			_.each( fonts.standard, function( font ) {
				standardFonts.push({
					id: font.family.replace( /&quot;/g, '&#39' ),
					text: font.label
				});
			});
		}

		// Format google fonts as an array.
		if ( ! _.isUndefined( fonts.standard ) ) {
			_.each( fonts.google, function( font ) {
				googleFonts.push({
					id: font.family,
					text: font.label
				});
			});
		}

		// Combine forces and build the final data.
		data = [
			{ text: 'Standard Fonts', children: standardFonts },
			{ text: 'Google Fonts',   children: googleFonts }
		];

		// Instantiate selectWoo with the data.
		fontSelect = jQuery( selector ).selectWoo({
			data: data
		});

		// Set the initial value.
		if ( value['font-family'] ) {
			fontSelect.val( value['font-family'].replace( /'/g, '"' ) ).trigger( 'change' );
		}

		// When the value changes
		fontSelect.on( 'change', function() {

			// Set the value.
			control.saveValue( 'font-family', jQuery( this ).val() );

			// Re-init the font-backup selector.
			control.renderBackupFontSelector();

			// Re-init variants selector.
			control.renderVariantSelector();

			// Re-init subsets selector.
			control.renderSubsetSelector();
		});
	},

	/**
	 * Adds the font-families to the font-family dropdown
	 * and instantiates selectWoo.
	 */
	renderBackupFontSelector: function() {

		var control       = this,
		    selector      = control.selector + ' .font-backup select',
		    standardFonts = [],
		    value         = control.getValue(),
		    fontFamily    = value['font-family'],
		    variants      = control.getVariants( fontFamily ),
		    fonts         = control.getFonts(),
		    fontSelect;

		if ( _.isUndefined( value['font-backup'] ) || null === value['font-backup'] ) {
			value['font-backup'] = '';
		}

		// Hide if we're not on a google-font.
		if ( false !== variants ) {
			jQuery( control.selector + ' .font-backup' ).show();
		} else {
			jQuery( control.selector + ' .font-backup' ).hide();
		}

		// Format standard fonts as an array.
		if ( ! _.isUndefined( fonts.standard ) ) {
			_.each( fonts.standard, function( font ) {
				standardFonts.push({
					id: font.family.replace( /&quot;/g, '&#39' ),
					text: font.label
				});
			});
		}

		// Instantiate selectWoo with the data.
		fontSelect = jQuery( selector ).selectWoo({
			data: standardFonts
		});

		// Set the initial value.
		if ( 'undefined' !== typeof value['font-backup'] ) {
			fontSelect.val( value['font-backup'].replace( /'/g, '"' ) ).trigger( 'change' );
		}

		// When the value changes
		fontSelect.on( 'change', function() {

			// Set the value.
			control.saveValue( 'font-backup', jQuery( this ).val() );
		});
	},

	/**
	 * Renders the variants selector using selectWoo
	 * Displays font-variants for the currently selected font-family.
	 */
	renderVariantSelector: function() {

		var control    = this,
		    value      = control.getValue(),
		    fontFamily = value['font-family'],
		    variants   = control.getVariants( fontFamily ),
		    selector   = control.selector + ' .variant select',
		    data       = [],
		    isValid    = false,
		    fontWeight,
		    variantSelector,
		    fontStyle;

		if ( false !== variants ) {
			jQuery( control.selector + ' .variant' ).show();
			_.each( variants, function( variant ) {
				if ( value.variant === variant.id ) {
					isValid = true;
				}
				data.push({
					id: variant.id,
					text: variant.label
				});
			});
			if ( ! isValid ) {
				value.variant = 'regular';
			}

			if ( jQuery( selector ).hasClass( 'select2-hidden-accessible' ) ) {
				jQuery( selector ).selectWoo( 'destroy' );
				jQuery( selector ).empty();
			}

			// Instantiate selectWoo with the data.
			variantSelector = jQuery( selector ).selectWoo({
				data: data
			});
			variantSelector.val( value.variant ).trigger( 'change' );
			variantSelector.on( 'change', function() {
				control.saveValue( 'variant', jQuery( this ).val() );

				fontWeight = ( ! _.isString( value.variant ) ) ? '400' : value.variant.match( /\d/g );
				fontWeight = ( ! _.isObject( fontWeight ) ) ? '400' : fontWeight.join( '' );
				fontStyle  = ( -1 !== value.variant.indexOf( 'italic' ) ) ? 'italic' : 'normal';

				control.saveValue( 'font-weight', fontWeight );
				control.saveValue( 'font-style', fontStyle );
			});
		} else {
			jQuery( control.selector + ' .variant' ).hide();
		}
	},

	/**
	 * Renders the subsets selector using selectWoo
	 * Displays font-subsets for the currently selected font-family.
	 */
	renderSubsetSelector: function() {

		var control    = this,
		    value      = control.getValue(),
		    fontFamily = value['font-family'],
		    subsets    = control.getSubsets( fontFamily ),
		    selector   = control.selector + ' .subsets select',
		    data       = [],
		    validValue = value.subsets,
		    subsetSelector;

		if ( false !== subsets ) {
			jQuery( control.selector + ' .subsets' ).show();
			_.each( subsets, function( subset ) {

				if ( _.isObject( validValue ) ) {
					if ( -1 === validValue.indexOf( subset.id ) ) {
						validValue = _.reject( validValue, function( subValue ) {
							return subValue === subset.id;
						});
					}
				}

				data.push({
					id: subset.id,
					text: subset.label
				});
			});

		} else {
			jQuery( control.selector + ' .subsets' ).hide();
		}

		if ( jQuery( selector ).hasClass( 'select2-hidden-accessible' ) ) {
			jQuery( selector ).selectWoo( 'destroy' );
			jQuery( selector ).empty();
		}

		// Instantiate selectWoo with the data.
		subsetSelector = jQuery( selector ).selectWoo({
			data: data
		});
		subsetSelector.val( validValue ).trigger( 'change' );
		subsetSelector.on( 'change', function() {
			control.saveValue( 'subsets', jQuery( this ).val() );
		});
	},

	/**
	 * Get fonts.
	 */
	getFonts: function() {
		var control = this;

		if ( ! _.isUndefined( window[ 'kirkiFonts' + control.id ] ) ) {
			return window[ 'kirkiFonts' + control.id ];
		}
		if ( 'undefined' !== typeof kirkiAllFonts ) {
			return kirkiAllFonts;
		}
		return {
			google: [],
			standard: []
		};
	},

	/**
	 * Get variants for a font-family.
	 */
	getVariants: function( fontFamily ) {
		var control = this,
		    fonts   = control.getFonts();

		var variants = false;
		_.each( fonts.standard, function( font ) {
			if ( fontFamily && font.family === fontFamily.replace( /'/g, '"' ) ) {
				variants = font.variants;
				return font.variants;
			}
		});

		_.each( fonts.google, function( font ) {
			if ( font.family === fontFamily ) {
				variants = font.variants;
				return font.variants;
			}
		});
		return variants;
	},

	/**
	 * Get subsets for a font-family.
	 */
	getSubsets: function( fontFamily ) {

		var control = this,
		    subsets = false,
		    fonts   = control.getFonts();

		_.each( fonts.google, function( font ) {
			if ( font.family === fontFamily ) {
				subsets = font.subsets;
			}
		});
		return subsets;
	},

	/**
	 * Gets the value.
	 */
	getValue: function() {

		'use strict';

		var control   = this,
		    input     = control.container.find( '.typography-hidden-value' ),
		    valueJSON = jQuery( input ).val();

		return JSON.parse( valueJSON );
	},

	/**
	 * Saves the value.
	 */
	saveValue: function( property, value ) {

		'use strict';

		var control   = this,
		    input     = control.container.find( '.typography-hidden-value' ),
		    valueJSON = jQuery( input ).val(),
		    valueObj  = JSON.parse( valueJSON );

		valueObj[ property ] = value;
		wp.customize.control( control.id ).setting.set( valueObj );
		jQuery( input ).attr( 'value', JSON.stringify( valueObj ) ).trigger( 'change' );
	}
});
