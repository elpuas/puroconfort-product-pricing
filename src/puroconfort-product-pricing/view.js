/**
 * Use this file for JavaScript code that you want to run in the front-end
 * on posts/pages that contain this block.
 *
 * When this file is defined as the value of the `viewScript` property
 * in `block.json` it will be enqueued on the front end of the site.
 *
 * Example:
 *
 * ```js
 * {
 *   "viewScript": "file:./view.js"
 * }
 * ```
 *
 * If you're not making any changes to this file because your project doesn't need any
 * JavaScript running in the front-end, then you should delete this file and remove
 * the `viewScript` property from `block.json`.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-metadata/#view-script
 */

import { store, getContext } from '@wordpress/interactivity';

const formatPrice = ( value ) => {
	const number = Number( value ) || 0;
	const hasDecimals = Math.abs( number % 1 ) > 0;
	const locale = document.documentElement.lang || 'es-MX';

	return `$${ number.toLocaleString( locale, {
		minimumFractionDigits: hasDecimals ? 2 : 0,
		maximumFractionDigits: hasDecimals ? 2 : 0,
	} ) }`;
};

// Namespace must match data-wp-interactive in render.php, or directives won't bind.
store( 'puroconfort-pricing', {
	state: {
		quantity: 1,
		price_single: 0,
		price_bulk: 0,
		productName: '',
		unit_price_applied: 0,
		total: 0,
		totalFormatted: '',
	},
	actions: {
		init() {
			const context = getContext();
			const { state } = this;

			state.quantity = Number( context.quantity ) || 1;
			state.price_single = Number( context.price_single ) || 0;
			state.price_bulk = Number( context.price_bulk ) || 0;
			state.productName = context.productName || '';

			this.actions.update();
		},
		update( event ) {
			const { state } = this;
			const next = event
				? Number.parseInt( event.target.value, 10 )
				: state.quantity;

			state.quantity = Number.isFinite( next ) && next >= 1 ? next : 1;
			// Assigning into state is required so data-wp-text can react.
			state.unit_price_applied =
				state.quantity >= 12 ? state.price_bulk : state.price_single;
			state.total = state.quantity * state.unit_price_applied;
			state.totalFormatted = formatPrice( state.total );

			// Debugging to verify Interactivity action execution and state mutation.
			// Remove once confirmed in console.
			console.log( 'Quantity:', state.quantity );
			console.log( 'Unit price applied:', state.unit_price_applied );
			console.log( 'Total (raw):', state.total );
			console.log( 'State totalFormatted:', state.totalFormatted );
		},
		// TODO: Integrate Click to Chat / wa.me URL here.
	},
} );
