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

// Use block attributes as the source of truth; fall back to post meta for existing posts.
$attr_price_single = isset( $attributes['priceSingle'] ) && is_numeric( $attributes['priceSingle'] ) ? (float) $attributes['priceSingle'] : null;
$attr_price_bulk   = isset( $attributes['priceBulk'] ) && is_numeric( $attributes['priceBulk'] ) ? (float) $attributes['priceBulk'] : null;

$price_single_raw = get_post_meta( $post_id, 'price_single', true );
$price_bulk_raw   = get_post_meta( $post_id, 'price_bulk', true );

$price_single = $attr_price_single !== null ? $attr_price_single : ( is_numeric( $price_single_raw ) ? (float) $price_single_raw : 0.0 );
$price_bulk   = $attr_price_bulk   !== null ? $attr_price_bulk   : ( is_numeric( $price_bulk_raw )   ? (float) $price_bulk_raw   : 0.0 );

$format_price = static function ( $value ) {
	$decimals = ( $value - floor( $value ) ) > 0 ? 2 : 0;
	return '₡' . number_format_i18n( $value, $decimals );
};

$input_id = wp_unique_id( 'puroconfort-qty-' );

$whatsapp_phone = isset( $attributes['whatsappPhone'] ) ? preg_replace( '/[^0-9]/', '', $attributes['whatsappPhone'] ) : '';

$form_page_url = isset( $attributes['formPageUrl'] ) ? $attributes['formPageUrl'] : '';

$page_title = wp_strip_all_tags( get_the_title( $post_id ) );
$page_url   = get_permalink( $post_id );

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

	<div class="wp-block-buttons puroconfort-product-pricing__actions is-layout-flex">
		<div class="wp-block-button has-custom-width wp-block-button__width-50">
			<button
				type="button"
				class="wp-block-button__link puroconfort-product-pricing__btn-email wp-element-button"
				data-form-page-url="<?php echo esc_url( $form_page_url ); ?>"
				data-wp-on--click="actions.openEmailForm"
				<?php echo empty( $form_page_url ) ? 'disabled' : ''; ?>
			>
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M2 4a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v.01L12 13 2 4.01V4Zm0 3.2 10 8.5 10-8.5V20a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V7.2Z"/></svg>
				<?php esc_html_e( 'Ordenar', 'puroconfort-product-pricing' ); ?>
			</button>
		</div>
		<div class="wp-block-button has-custom-width wp-block-button__width-50">
			<button
				type="button"
				class="wp-block-button__link puroconfort-product-pricing__btn-whatsapp has-text-color has-background wp-element-button"
				data-whatsapp-phone="<?php echo esc_attr( $whatsapp_phone ); ?>"
				data-page-title="<?php echo esc_attr( $page_title ); ?>"
				data-page-url="<?php echo esc_attr( $page_url ); ?>"
				data-wp-on--click="actions.openWhatsApp"
				<?php echo empty( $whatsapp_phone ) ? 'disabled' : ''; ?>
			>
				<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.511-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413Z"/></svg>
				<?php esc_html_e( 'WhatsApp', 'puroconfort-product-pricing' ); ?>
			</button>
		</div>
	</div>

	<input type="hidden" name="product_name" data-wp-bind--value="state.productName" />
	<input type="hidden" name="quantity" data-wp-bind--value="state.quantity" />
	<input type="hidden" name="unit_price_applied" data-wp-bind--value="state.unit_price_applied" />
	<input type="hidden" name="total" data-wp-bind--value="state.total" />
</div>
