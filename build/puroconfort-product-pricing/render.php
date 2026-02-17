<?php
/**
 * PHP file to use when rendering the block type on the server to show on the front end.
 *
 * The following variables are exposed to the file:
 *     $attributes (array): The block attributes.
 *     $content (string): The block default content.
 *     $block (WP_Block): The block instance.
 *
 * @see https://github.com/WordPress/gutenberg/blob/trunk/docs/reference-guides/block-api/block-metadata.md#render
 */
if ( ! isset( $block ) || ! $block instanceof WP_Block ) {
	return;
}

$post_id = get_the_ID();
if ( ! $post_id ) {
	return;
}

$price_single_raw = get_post_meta( $post_id, 'price_single', true );
$price_bulk_raw   = get_post_meta( $post_id, 'price_bulk', true );

$price_single = is_numeric( $price_single_raw ) ? (float) $price_single_raw : 0.0;
$price_bulk   = is_numeric( $price_bulk_raw ) ? (float) $price_bulk_raw : 0.0;

$format_price = static function ( $value ) {
	$decimals = ( $value - floor( $value ) ) > 0 ? 2 : 0;
	return '₡' . number_format_i18n( $value, $decimals );
};

$input_id = wp_unique_id( 'puroconfort-qty-' );

$whatsapp_phone = isset( $attributes['whatsappPhone'] ) ? preg_replace( '/[^0-9]/', '', $attributes['whatsappPhone'] ) : '';

$context = array(
	'quantity'    => 1,
	'price_single' => $price_single,
	'price_bulk'   => $price_bulk,
	'productName' => wp_strip_all_tags( get_the_title( $post_id ) ),
);

$wrapper_attributes = get_block_wrapper_attributes(
	array(
		'class' => 'wp-block-group has-border-color has-border-light-border-color has-global-padding is-layout-constrained',
		'style' => 'border-width:1px;border-radius:5px;padding-top:var(--wp--preset--spacing--large);padding-right:var(--wp--preset--spacing--large);padding-bottom:var(--wp--preset--spacing--large);padding-left:var(--wp--preset--spacing--large)',
	)
);
?>
<div
	<?php echo $wrapper_attributes; ?>
	<?php // Namespace must match store() in view.js for reactivity to work. ?>
	data-wp-interactive="puroconfort-pricing"
	data-wp-context='<?php echo esc_attr( wp_json_encode( $context ) ); ?>'
	data-wp-init="actions.init"
>
	<div class="wp-block-group has-small-font-size has-global-padding is-layout-constrained">
		<div class="wp-block-group is-content-justification-space-between is-nowrap is-layout-flex" style="justify-content: space-between;">
			<div class="wp-block-group is-nowrap is-layout-flex">
				<p><strong>✓</strong></p>
				<p><?php esc_html_e( 'Precio Unitario c/u', 'puroconfort-product-pricing' ); ?></p>
			</div>
			<p><?php echo esc_html( $format_price( $price_single ) ); ?></p>
		</div>

		<hr class="wp-block-separator has-text-color has-border-light-color has-alpha-channel-opacity has-border-light-background-color has-background is-style-separator-thin" style="margin-block-start: 16px;">

		<div class="wp-block-group is-content-justification-space-between is-nowrap is-layout-flex" style="justify-content: space-between; margin-block-start: 16px;">
			<div class="wp-block-group is-nowrap is-layout-flex">
				<p><strong>✓</strong></p>
				<p><?php esc_html_e( 'Precio 12+ c/u', 'puroconfort-product-pricing' ); ?></p>
			</div>
			<p><?php echo esc_html( $format_price( $price_bulk ) ); ?></p>
		</div>
	</div>

	<div class="wp-block-group is-content-justification-space-between is-nowrap is-layout-flex">
		<div class="puroconfort-product-pricing__quantity">
			<label class="screen-reader-text" for="<?php echo esc_attr( $input_id ); ?>">
				<?php esc_html_e( 'Cantidad', 'puroconfort-product-pricing' ); ?>
			</label>
			<div class="puroconfort-product-pricing__stepper" aria-label="<?php esc_attr_e( 'Cantidad', 'puroconfort-product-pricing' ); ?>">
				<button
					type="button"
					class="puroconfort-product-pricing__stepper-btn"
					data-wp-on--click="actions.decrement"
					aria-label="<?php esc_attr_e( 'Disminuir cantidad', 'puroconfort-product-pricing' ); ?>"
				>
					<span aria-hidden="true">−</span>
				</button>
				<span
					id="<?php echo esc_attr( $input_id ); ?>"
					class="puroconfort-product-pricing__stepper-value"
					data-wp-text="state.quantity"
				>1</span>
				<button
					type="button"
					class="puroconfort-product-pricing__stepper-btn"
					data-wp-on--click="actions.increment"
					aria-label="<?php esc_attr_e( 'Aumentar cantidad', 'puroconfort-product-pricing' ); ?>"
				>
					<span aria-hidden="true">+</span>
				</button>
			</div>
		</div>

		<div class="wp-block-group has-base-font-size is-horizontal is-nowrap is-layout-flex">
			<p class="has-primary-font-family has-x-large-font-size" style="font-style:normal;font-weight:500;line-height:1">
				<?php // data-wp-text is required to bind reactive text from Interactivity state. ?>
				<span data-wp-text="state.totalFormatted">--</span>
			</p>
			<p class="has-secondary-color has-text-color has-primary-font-family has-small-font-size" style="margin-top:1.4rem">
				<?php esc_html_e( 'Total', 'puroconfort-product-pricing' ); ?>
			</p>
		</div>
	</div>

	<div class="wp-block-buttons is-layout-flex">
		<div class="wp-block-button has-custom-width wp-block-button__width-100">
			<button
				type="button"
				class="wp-block-button__link has-main-color has-primary-alt-background-color has-text-color has-background has-link-color wp-element-button"
				data-whatsapp-phone="<?php echo esc_attr( $whatsapp_phone ); ?>"
				data-wp-on--click="actions.openWhatsApp"
				<?php echo empty( $whatsapp_phone ) ? 'disabled' : ''; ?>
			>
				<?php esc_html_e( 'Comprar', 'puroconfort-product-pricing' ); ?>
			</button>
		</div>
	</div>

	<input type="hidden" name="product_name" data-wp-bind--value="state.productName" />
	<input type="hidden" name="quantity" data-wp-bind--value="state.quantity" />
	<input type="hidden" name="unit_price_applied" data-wp-bind--value="state.unit_price_applied" />
	<input type="hidden" name="total" data-wp-bind--value="state.total" />
</div>
