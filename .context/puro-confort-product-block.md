# Puro Confort Product Pricing Block — Current Implementation

## Overview

The **Puro Confort Product Pricing** plugin ships a single dynamic Gutenberg block (`puroconfort/puroconfort-product-pricing`) that displays tiered unit pricing, a quantity stepper, a computed total, and a **WhatsApp order button**.

It is **not** a WooCommerce block. It does not handle checkout, cart, or payments. Its sole purpose is to present pricing, let the user pick a quantity, and open a pre-filled WhatsApp conversation so the customer can complete the order via chat.

The block uses the **WordPress Interactivity API** for all client-side reactivity.

---

## Block Architecture

### Registration

The block is registered in the main plugin file `puroconfort-product-pricing.php` via:

```php
function puroconfort_puroconfort_product_pricing_block_init() {
    wp_register_block_types_from_metadata_collection(
        __DIR__ . '/build',
        __DIR__ . '/build/blocks-manifest.php'
    );
}
add_action( 'init', 'puroconfort_puroconfort_product_pricing_block_init' );
```

This loads block metadata from the generated `build/blocks-manifest.php`, which mirrors `src/puroconfort-product-pricing/block.json`.

### Dynamic Rendering

The block is **dynamic** — it has no `save` function. The `render` property in `block.json` points to `render.php`, which is executed server-side on every page load.

### Editor Experience

In the editor, `edit.js` uses `<ServerSideRender>` to preview the dynamic output. It exposes three block attributes via `InspectorControls`:

| Attribute       | Type     | Purpose                               |
| --------------- | -------- | ------------------------------------- |
| `priceSingle`   | `number` | Unit price for quantities 1–11        |
| `priceBulk`     | `number` | Unit price for quantities 12+         |
| `whatsappPhone` | `string` | WhatsApp destination (digits only)    |

### Interactivity Support

`block.json` declares `"interactivity": true` in `supports`, and the view script is registered as a **module** (`viewScriptModule`), which is required by the Interactivity API.

---

## Price Calculation Logic

### Data Sources (PHP — `render.php`)

Pricing values are resolved with a dual-source fallback:

1. **Block attributes** (`$attributes['priceSingle']`, `$attributes['priceBulk']`) — primary source.
2. **ACF post meta** (`price_single`, `price_bulk`) — fallback for existing posts where meta was set before block attributes existed.

```php
$price_single = $attr_price_single !== null
    ? $attr_price_single
    : ( is_numeric( $price_single_raw ) ? (float) $price_single_raw : 0.0 );
```

### Tier Rule (JS — `view.js`)

There is exactly **one** pricing rule:

| Condition        | Unit Price Applied |
| ---------------- | ------------------ |
| `quantity < 12`  | `price_single`     |
| `quantity >= 12` | `price_bulk`       |

The formula, executed inside `actions.update()`:

```
unit_price_applied = quantity >= 12 ? price_bulk : price_single
total              = quantity * unit_price_applied
```

The user **never** selects a tier manually; it is applied automatically based on the quantity value.

### Currency Formatting

- **PHP**: A closure `$format_price` formats values with the `₡` (Costa Rican colón) symbol and `number_format_i18n`.
- **JS**: A `formatPrice()` helper uses `Number.toLocaleString()` based on `document.documentElement.lang` (defaults to `es-MX`).

---

## Frontend Behavior

All frontend interactivity is powered by the **WordPress Interactivity API** store `puroconfort-pricing` defined in `view.js`.

### Initialization (`actions.init`)

On block mount (`data-wp-init="actions.init"`), the action reads the server-provided `data-wp-context` JSON and seeds the reactive state:

```js
state.quantity     = Number( context.quantity ) || 1;
state.price_single = Number( context.price_single ) || 0;
state.price_bulk   = Number( context.price_bulk ) || 0;
state.productName  = context.productName || '';
```

Then it calls `actions.update()` to compute the initial total.

### Quantity Stepper

The UI does **not** use a native `<input type="number">`. Instead, it renders a custom stepper with two `<button>` elements bound to Interactivity actions:

| Button | Directive                          | Action              |
| ------ | ---------------------------------- | -------------------- |
| **−**  | `data-wp-on--click="actions.decrement"` | `quantity - 1` (min 1) |
| **+**  | `data-wp-on--click="actions.increment"` | `quantity + 1`         |

The displayed quantity is a `<span>` bound via `data-wp-text="state.quantity"`.

### Reactive Updates

Every call to `actions.update()`:

1. Clamps quantity to ≥ 1.
2. Selects `unit_price_applied` based on the tier rule.
3. Computes `total = quantity * unit_price_applied`.
4. Formats `totalFormatted` for display.

The total is rendered in a `<span data-wp-text="state.totalFormatted">`.

### Hidden Inputs

Four hidden `<input>` fields are bound to state via `data-wp-bind--value`:

| Name                 | Bound State            |
| -------------------- | ---------------------- |
| `product_name`       | `state.productName`    |
| `quantity`           | `state.quantity`       |
| `unit_price_applied` | `state.unit_price_applied` |
| `total`              | `state.total`          |

These are currently **not consumed by any form**. They are prepared for a future Forminator integration.

---

## WhatsApp Integration

### Button

The "Comprar" button is rendered at the bottom of the block. It carries server-side data as `data-*` attributes:

| Data Attribute          | Source                                   |
| ----------------------- | ---------------------------------------- |
| `data-whatsapp-phone`   | `$attributes['whatsappPhone']` (sanitized to digits) |
| `data-page-title`       | `get_the_title( $post_id )` (stripped of HTML) |
| `data-page-url`         | `get_permalink( $post_id )`              |

If `whatsappPhone` is empty, the button is rendered with the `disabled` attribute.

### Link Generation (`actions.openWhatsApp`)

When clicked, the action:

1. Reads `phone`, `pageTitle`, and `pageUrl` from the button's `data-*` attributes.
2. Constructs a pre-filled WhatsApp message containing:
   - Product name (page title)
   - Product URL
   - Selected quantity
   - Estimated total (formatted)
   - Prompt fields for the customer to fill: name, phone, email, delivery address, confirmed quantity
3. Opens `https://wa.me/{phone}?text={encodedMessage}` in a new tab with `noopener,noreferrer`.

### Message Template

```
{pageTitle}
{pageUrl}

Cantidad seleccionada: {quantity}
Total estimado: ₡{total}

Para que tu pedido salga más rápido, por favor indícanos:
- Nombre completo:
- Teléfono:
- Correo electrónico:
- Dirección de entrega:

Cantidad confirmada:
```

---

## Data Flow (PHP → JS → UI)

```
┌─────────────────────────────────────────────────────────┐
│  PHP (render.php)                                       │
│                                                         │
│  1. Reads block attributes + ACF post meta fallback     │
│  2. Resolves price_single, price_bulk, productName      │
│  3. Builds $context array                               │
│  4. Outputs JSON in  data-wp-context  attribute         │
│  5. Outputs whatsappPhone, pageTitle, pageUrl as        │
│     data-* attributes on the "Comprar" button           │
└──────────────────────────┬──────────────────────────────┘
                           │ HTML with embedded data
                           ▼
┌─────────────────────────────────────────────────────────┐
│  JS (view.js) — Interactivity API Store                 │
│                                                         │
│  1. actions.init() reads data-wp-context via getContext()│
│  2. Seeds reactive state (quantity, prices, productName)│
│  3. actions.update() computes unit_price_applied, total │
│  4. State mutations trigger data-wp-text / data-wp-bind │
│     directives → DOM updates automatically              │
└──────────────────────────┬──────────────────────────────┘
                           │ Reactive DOM bindings
                           ▼
┌─────────────────────────────────────────────────────────┐
│  UI                                                     │
│                                                         │
│  • Quantity stepper (−/+) → actions.decrement/increment │
│  • Total display ← state.totalFormatted                 │
│  • Hidden inputs ← state.quantity, total, etc.          │
│  • "Comprar" button → actions.openWhatsApp              │
│    (reads data-* attrs + state for WhatsApp message)    │
└─────────────────────────────────────────────────────────┘
```

---

## Relevant Files

| File | Role |
| ---- | ---- |
| `puroconfort-product-pricing.php` | Plugin entry point. Registers block types from the build manifest on `init`. |
| `src/puroconfort-product-pricing/block.json` | Block metadata: name, attributes (`priceSingle`, `priceBulk`, `whatsappPhone`), supports (interactivity), and asset references. |
| `src/puroconfort-product-pricing/index.js` | Client-side block registration (`registerBlockType`). Imports `edit.js` and `style.scss`. |
| `src/puroconfort-product-pricing/edit.js` | Editor component. Renders `InspectorControls` for price/WhatsApp settings and `<ServerSideRender>` for live preview. |
| `src/puroconfort-product-pricing/render.php` | Server-side render template. Resolves pricing from attributes/meta, outputs the full block HTML with Interactivity API directives and `data-wp-context`. |
| `src/puroconfort-product-pricing/view.js` | Frontend Interactivity API store (`puroconfort-pricing`). Handles quantity changes, tier pricing logic, total calculation, currency formatting, and WhatsApp link generation. |
| `src/puroconfort-product-pricing/style.scss` | Shared styles (front + editor) for the quantity stepper component. |
| `src/puroconfort-product-pricing/editor.scss` | Editor-only styles (debug dotted border). |
| `build/blocks-manifest.php` | Auto-generated manifest consumed by `wp_register_block_types_from_metadata_collection`. |
| `build/puroconfort-product-pricing/view.asset.php` | Auto-generated asset descriptor declaring `@wordpress/interactivity` as a dependency (module type). |

---

## Notes for Future Email Integration

1. **Hidden inputs are already in place.** The block renders four hidden `<input>` fields (`product_name`, `quantity`, `unit_price_applied`, `total`) whose values stay in sync with the Interactivity API state. A Forminator form can wrap or consume these fields directly.

2. **No form element exists yet.** The hidden inputs are not inside a `<form>`. A Forminator shortcode or custom form wrapper will need to enclose the block output (or be placed adjacent) and map these fields.

3. **WhatsApp is the only submission channel.** Currently, clicking "Comprar" opens WhatsApp. Email integration would add an alternative or replacement flow — likely triggered by a new "Ordenar" button that submits the form to Forminator instead of (or in addition to) opening WhatsApp.

4. **State is the single source of truth.** All computed values live in the Interactivity API store. Any new submission method should read from the same `state.*` properties (or their bound hidden inputs) to avoid duplicating logic.

5. **No server-side submission handling exists.** The plugin has no PHP code for processing form submissions, sending emails, or saving orders. Forminator would handle that entirely.

6. **The `data-*` attributes on the "Comprar" button** (`data-whatsapp-phone`, `data-page-title`, `data-page-url`) carry metadata that an email form might also need (product name, URL). Consider whether these should move into the Interactivity API context/state for unified access.
