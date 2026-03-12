# Puro Confort Product Pricing

A custom WordPress plugin that provides one dynamic Gutenberg block for product quantity selection, tiered pricing calculation, and order handoff through WhatsApp or a dedicated order form page.

This plugin is not WooCommerce and does not implement cart, checkout, payment processing, or order persistence.

## What It Does

The block `puroconfort/puroconfort-product-pricing` renders:

- Unit price for low quantity (1-11)
- Unit price for bulk quantity (12+)
- Quantity stepper (`-` / `+`)
- Reactive total price
- `Ordenar` button (redirects to configurable form page with query params)
- `WhatsApp` button (opens pre-filled WhatsApp chat)
- Hidden inputs synchronized with computed state (`product_name`, `quantity`, `unit_price_applied`, `total`)

## Current Architecture

The plugin is split between server rendering and frontend interactivity:

- PHP (`render.php`): resolves initial data, sanitizes button metadata, outputs dynamic HTML + Interactivity directives.
- JavaScript (`view.js`): manages reactive state with the WordPress Interactivity API, computes prices, and handles button actions.
- Editor (`edit.js`): exposes block settings in `InspectorControls` and previews output using `ServerSideRender`.

Interactivity root and store namespace:

- Markup root: `data-wp-interactive="puroconfort-pricing"`
- Store: `store( 'puroconfort-pricing', ... )`

## Block Attributes

Defined in `src/puroconfort-product-pricing/block.json`:

- `priceSingle` (`number`, default `0`): unit price for quantities `< 12`
- `priceBulk` (`number`, default `0`): unit price for quantities `>= 12`
- `whatsappPhone` (`string`, default `""`): destination WhatsApp number (digits)
- `formPageUrl` (`string`, default `""`): URL/path of order form page

## Price Rules

Pricing logic is fixed and automatic:

- If `quantity < 12`, apply `priceSingle`
- If `quantity >= 12`, apply `priceBulk`
- `total = quantity * unit_price_applied`

No manual tier selection exists in the UI.

## Data Resolution Strategy (Server)

`render.php` resolves pricing values in this order:

1. Block attributes (`priceSingle`, `priceBulk`)
2. Fallback post meta (`price_single`, `price_bulk`) for legacy content
3. Fallback to `0.0`

This keeps old posts compatible while making block attributes the primary source of truth.

## Frontend Actions

Implemented in `src/puroconfort-product-pricing/view.js`:

- `actions.init`: seeds state from `data-wp-context`
- `actions.update`: clamps quantity and recomputes totals
- `actions.increment` / `actions.decrement`: stepper controls
- `actions.openWhatsApp`: opens `https://wa.me/{phone}?text=...`
- `actions.openEmailForm`: navigates to form URL with query params

### Email Form Redirect Parameters

`openEmailForm` appends:

- `product={state.productName}`
- `quantity={state.quantity}`
- `total={state.total}`

Example target URL:

`/formulario-pedido/?product=Almohada+Premium&quantity=2&total=15980`

## File Map

- `puroconfort-product-pricing.php`: plugin bootstrap and block registration
- `src/puroconfort-product-pricing/block.json`: block metadata, attributes, assets, dynamic render mapping
- `src/puroconfort-product-pricing/edit.js`: inspector controls + SSR preview
- `src/puroconfort-product-pricing/render.php`: server-rendered block HTML and context
- `src/puroconfort-product-pricing/view.js`: Interactivity API store, calculations, button actions
- `src/puroconfort-product-pricing/style.scss`: shared frontend/editor styles
- `src/puroconfort-product-pricing/editor.scss`: editor-only styles
- `build/`: generated build artifacts used at runtime

## Requirements

- WordPress: `>= 6.8` (per plugin header)
- PHP: `>= 7.4` (per plugin header)
- Node.js + npm (for build workflows)

## Local Development

Install dependencies:

```bash
npm install
```

Start development build with watch mode:

```bash
npm run start
```

Create production build:

```bash
npm run build
```

Available scripts:

- `npm run start`
- `npm run build`
- `npm run lint:js`
- `npm run lint:css`
- `npm run format`
- `npm run plugin-zip`

## Installation

1. Place the plugin folder in `wp-content/plugins/puroconfort-product-pricing`.
2. Activate **Puro Confort Product Pricing** in WordPress Admin.
3. Edit a product page/post and insert **Puro Confort Product Pricing** block.
4. Configure prices and action settings in the block sidebar.

## Editor Configuration

Block sidebar panels:

- `Precios`
  - `Precio unitario (1-11 unidades)`
  - `Precio mayoreo (12+ unidades)`
- `WhatsApp`
  - `WhatsApp Number` (digits only)
- `Formulario de pedido`
  - `URL de la página del formulario`

If `whatsappPhone` or `formPageUrl` is empty, the respective frontend button is rendered disabled.

## Integration Notes

### WhatsApp

The button sends a pre-filled message including:

- product title
- page URL
- selected quantity
- estimated total
- customer detail prompts

### Forminator / Email Flow

The `Ordenar` button redirects to a form page with URL parameters. The receiving form can prefill hidden fields from query params (`product`, `quantity`, `total`).

## Known Limitations

- No order submission backend in this plugin
- No cart/checkout/payment logic
- No inventory handling
- Hidden fields are rendered but not submitted by this plugin directly

## Quality / Review Notes

During this codebase review, one implementation detail stands out:

- `src/puroconfort-product-pricing/view.js` still contains debug `console.log(...)` calls in `actions.update`. These are harmless in functionality but should usually be removed in production to reduce console noise.

## Project Documentation

Internal project docs live in:

- `.context/puro-confort-product-block.md`
- `.context/email-order-button.md`
- `.github/skills/wp-block-development/SKILL.md`
- `.github/skills/wp-interactivity-api/SKILL.md`
- `.github/skills/wp-plugin-development/SKILL.md`

## License

GPL-2.0-or-later
