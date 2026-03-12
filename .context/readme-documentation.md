# README Documentation Task

## What Was Implemented

A new root `README.md` was created to document the plugin comprehensively based on the current source code.

The README includes:

- plugin purpose and scope
- block capabilities
- architecture split (PHP render + JS Interactivity)
- block attributes and tiered pricing rules
- server data resolution behavior (attributes + meta fallback)
- frontend actions (`init`, `update`, `increment`, `decrement`, `openWhatsApp`, `openEmailForm`)
- order-form query parameter behavior
- file map and requirements
- installation and development commands
- editor configuration details
- integration notes (WhatsApp and Forminator)
- known limitations
- review note about debug console logs in `view.js`
- links to internal project documentation

## Files Modified

- `README.md` (new)

## Relevant Notes For Future Work

- The README captures both the WhatsApp and email-form flows now present in the block.
- If pricing rules, attributes, or action handlers change, update README sections:
  - **Block Attributes**
  - **Price Rules**
  - **Frontend Actions**
- The review note flags production cleanup potential for debug logs in:
  - `src/puroconfort-product-pricing/view.js`
