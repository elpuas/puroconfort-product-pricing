Examples:
- `feature/interactivity-pricing`
- `fix/forminator-hidden-fields`

- Commits must be atomic and scoped
- Do NOT squash unless explicitly requested
- Do NOT perform git operations unless the prompt explicitly asks for them

---

## Block Purpose
Implement a product pricing selector with tiered unit pricing based on quantity.

This is NOT a checkout.
This is NOT WooCommerce.
This block only calculates pricing and prepares data for submission.

---

## Data Source (ACF)

Pricing data comes from ACF post meta on the current Product post.

Expected fields:

- `price_single` (number)
  Unit price for quantities 1–11

- `price_bulk` (number)
  Unit price for quantities 12+

Values are stored as **numbers only**.
Currency formatting happens in the frontend.

---

## Business Rule (Mandatory)

There is exactly ONE rule:

- If quantity < 12 → use `price_single`
- If quantity ≥ 12 → use `price_bulk`

Users NEVER select a pricing tier manually.

---

## Block UI (Strict Contract)

The block MUST render the following UI, in this exact order:

1. `Precio unitario: $X c/u`
2. `Precio 12+: $Y c/u`
3. Quantity input:
   - `type="number"`
   - `min="1"`
   - `step="1"`
   - default value `1`
4. `Total: $Z`
5. Primary button labeled **Ordenar**

Do NOT add:
- checkboxes
- selects
- multiple quantity inputs

---

## Architecture Rules

- The block is a **dynamic block**
- Markup is rendered in `render.php`
- Interactivity and calculations happen in `view.js`
- PHP provides data only
- JavaScript handles all interaction and logic

Do NOT duplicate pricing logic between PHP and JS.

---

## Interactivity API

Using the **WordPress Interactivity API** is strongly preferred.

If Interactivity API is NOT used:
- You MUST explain why in code comments.

State must minimally include:
- quantity
- price_single
- price_bulk
- unit_price_applied
- total

---

## Forminator Integration (Phase 1)

The block must expose hidden inputs with the following names:

- `product_name`
- `quantity`
- `unit_price_applied`
- `total`

These values must stay in sync with the UI.

Do NOT:
- create Forminator forms
- configure submissions
- add Forminator-specific PHP

---

## WhatsApp Integration (Future)

Do NOT implement WhatsApp or Click to Chat yet.

Leave clear TODO comments such as:

```js
// TODO: Integrate Click to Chat / wa.me URL here
