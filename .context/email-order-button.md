# Email Order Button — Implementation

## What Was Added

A second action button ("Ordenar") was added to the product pricing block, allowing customers to place orders via an email form powered by Forminator. The button sits next to the existing WhatsApp button.

The email button redirects the user to a configurable form page, passing product data as query parameters.

---

## Files Modified

### `src/puroconfort-product-pricing/block.json`

Added the `formPageUrl` attribute:

```json
"formPageUrl": {
    "type": "string",
    "default": ""
}
```

This stores the URL of the page containing the Forminator form.

### `src/puroconfort-product-pricing/edit.js`

Added a new `InspectorControls` panel ("Formulario de pedido") with a `TextControl` for configuring the form page URL. The attribute `formPageUrl` is destructured alongside existing attributes.

### `src/puroconfort-product-pricing/render.php`

- Reads `$form_page_url` from `$attributes['formPageUrl']`.
- Replaced the single full-width WhatsApp button with two half-width buttons side-by-side:
  - **"Ordenar"** — email form button (dark/neutral style). Carries `data-form-page-url` and triggers `actions.openEmailForm`. Disabled when `formPageUrl` is empty.
  - **"WhatsApp"** — existing WhatsApp button (green style). All original `data-*` attributes and `actions.openWhatsApp` directive preserved.
- The URL is output with `esc_url()` for proper sanitization.

### `src/puroconfort-product-pricing/view.js`

Added `actions.openEmailForm` to the Interactivity API store:

```js
openEmailForm( event ) {
    const btn = event.target.closest( '[data-form-page-url]' );
    const formPageUrl = btn ? btn.dataset.formPageUrl : '';
    // ...
    const url = new URL( formPageUrl, window.location.origin );
    url.searchParams.set( 'product', state.productName );
    url.searchParams.set( 'quantity', state.quantity );
    url.searchParams.set( 'total', state.total );
    window.location.href = url.href;
}
```

No existing actions, state variables, or logic were modified.

### `src/puroconfort-product-pricing/style.scss`

Added styles for the two-button layout:

- `.puroconfort-product-pricing__actions` — flex gap between buttons.
- `.puroconfort-product-pricing__btn-email` — dark background (`#333`), white text.
- `.puroconfort-product-pricing__btn-whatsapp` — green background (`#25d366`), white text.
- Both include hover and disabled states.

---

## Query Parameter Structure

When the user clicks "Ordenar", the block constructs a URL using the `URL` API:

```
{formPageUrl}?product={productName}&quantity={quantity}&total={total}
```

| Parameter  | Source                | Example     |
| ---------- | --------------------- | ----------- |
| `product`  | `state.productName`   | `Toalla+Premium` |
| `quantity`  | `state.quantity`      | `6`         |
| `total`    | `state.total`         | `9900`      |

The `URL` constructor handles both relative paths (`/formulario/`) and absolute URLs. Parameters are properly encoded via `URLSearchParams`.

---

## How the Forminator Page Receives the Data

The Forminator form page receives product data via URL query parameters (`$_GET`).

Forminator supports pre-populating form fields from query parameters using the `{query_param_name}` merge tag or by configuring hidden fields with default values from URL parameters.

Recommended Forminator field configuration:

| Forminator Field | Type   | Default Value / Pre-fill      |
| ---------------- | ------ | ----------------------------- |
| Product Name     | Hidden | `{query:product}` or URL param `product` |
| Quantity         | Hidden | `{query:quantity}` or URL param `quantity` |
| Total            | Hidden | `{query:total}` or URL param `total`    |

The form should also include visible fields for customer information (name, email, phone, address) and send a notification email upon submission.

---

## What Was NOT Changed

- Interactivity API store structure
- Pricing tier logic (`update`, `increment`, `decrement`)
- WhatsApp message generation (`openWhatsApp`)
- Hidden input fields
- State variables
- Quantity stepper behavior
- Currency formatting
