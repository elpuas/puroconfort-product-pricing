/**
 * Retrieves the translation of text.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-i18n/
 */
/**
 * React hook that is used to mark the block wrapper element.
 * It provides all the necessary props like the class name.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-block-editor/#useblockprops
 */
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, __experimentalNumberControl as NumberControl } from '@wordpress/components';

/**
 * Server-side rendering for dynamic blocks in the editor.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/packages/packages-server-side-render/
 */
import ServerSideRender from '@wordpress/server-side-render';

/**
 * Lets webpack process CSS, SASS or SCSS files referenced in JavaScript files.
 * Those files can contain any CSS code that gets applied to the editor.
 *
 * @see https://www.npmjs.com/package/@wordpress/scripts#using-css
 */
import './editor.scss';

/**
 * The edit function describes the structure of your block in the context of the
 * editor. This represents what the editor will render when the block is used.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-edit-save/#edit
 *
 * @param {Object} props               Block props.
 * @param {Object} props.attributes    Block attributes.
 * @param {Function} props.setAttributes Setter for block attributes.
 * @return {Element} Element to render.
 */
export default function Edit( { attributes, setAttributes } ) {
	const { whatsappPhone, priceSingle, priceBulk } = attributes;

	return (
		<>
			<InspectorControls>
				<PanelBody title="Precios">
					<NumberControl
						label="Precio unitario (1–11 unidades)"
						help="Precio por unidad para pedidos menores a 12."
						value={ priceSingle }
						min={ 0 }
						spinControls="native"
						onChange={ ( value ) =>
							setAttributes( { priceSingle: Number( value ) || 0 } )
						}
					/>
					<NumberControl
						label="Precio mayoreo (12+ unidades)"
						help="Precio por unidad para pedidos de 12 o más."
						value={ priceBulk }
						min={ 0 }
						spinControls="native"
						onChange={ ( value ) =>
							setAttributes( { priceBulk: Number( value ) || 0 } )
						}
					/>
				</PanelBody>
				<PanelBody title="WhatsApp">
					<TextControl
						label="WhatsApp Number"
						help="Digits only, example: 50689816449"
						value={ whatsappPhone }
						onChange={ ( value ) =>
							setAttributes( {
								whatsappPhone: value.replace( /[^0-9]/g, '' ),
							} )
						}
					/>
				</PanelBody>
			</InspectorControls>
			<div { ...useBlockProps() }>
				<ServerSideRender
					block="puroconfort/puroconfort-product-pricing"
					attributes={ attributes }
				/>
			</div>
		</>
	);
}
