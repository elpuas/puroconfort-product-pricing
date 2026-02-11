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

$context = array(
	'quantity'    => 1,
	'price_single' => $price_single,
	'price_bulk'   => $price_bulk,
	'productName' => wp_strip_all_tags( get_the_title( $post_id ) ),
);
?>
<div
	<?php echo get_block_wrapper_attributes(); ?>
	<?php // Namespace must match store() in view.js for reactivity to work. ?>
	data-wp-interactive="puroconfort-pricing"
	data-wp-context='<?php echo esc_attr( wp_json_encode( $context ) ); ?>'
	data-wp-init="actions.init"
>
	<div class="puroconfort-product-pricing__label">
		<?php
		printf(
			/* translators: %s: single unit price */
			esc_html__( 'Precio unitario: %s c/u', 'puroconfort-product-pricing' ),
			esc_html( $format_price( $price_single ) )
		);
		?>
	</div>

	<div class="puroconfort-product-pricing__label">
		<?php
		printf(
			/* translators: %s: bulk unit price */
			esc_html__( 'Precio 12+: %s c/u', 'puroconfort-product-pricing' ),
			esc_html( $format_price( $price_bulk ) )
		);
		?>
	</div>

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

	<div class="puroconfort-product-pricing__total">
		<span><?php esc_html_e( 'Total:', 'puroconfort-product-pricing' ); ?></span>
		<?php // data-wp-text is required to bind reactive text from Interactivity state. ?>
		<span data-wp-text="state.totalFormatted">--</span>
	</div>

	<button type="button" class="wp-block-button__link wp-element-button">
		<?php esc_html_e( 'Ordenar', 'puroconfort-product-pricing' ); ?>
	</button>

	<input type="hidden" name="product_name" data-wp-bind--value="state.productName" />
	<input type="hidden" name="quantity" data-wp-bind--value="state.quantity" />
	<input type="hidden" name="unit_price_applied" data-wp-bind--value="state.unit_price_applied" />
	<input type="hidden" name="total" data-wp-bind--value="state.total" />
</div>
