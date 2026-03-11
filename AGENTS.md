# AGENTS.md

This repository contains the **Puro Confort Product Pricing** WordPress plugin.

The plugin provides a single **dynamic Gutenberg block** used to calculate tiered product pricing and prepare order data.

It is **not WooCommerce** and **not a checkout system**.

---

## Project Context

The full technical description of the block implementation is documented here:

/Users/alfredonavas/Local Sites/puro-confort/app/public/wp-content/plugins/puroconfort-product-pricing/.context/puro-confort-product-block.md

Agents must read that document before modifying the block.

---

## Development Skills

The development conventions used in this project are defined in these skills:

- /Users/alfredonavas/Local Sites/puro-confort/app/public/wp-content/plugins/puroconfort-product-pricing/.github/skills/wp-block-development
- /Users/alfredonavas/Local Sites/puro-confort/app/public/wp-content/plugins/puroconfort-product-pricing/.github/skills/wp-interactivity-api
- /Users/alfredonavas/Local Sites/puro-confort/app/public/wp-content/plugins/puroconfort-product-pricing/.github/skills/wp-plugin-development

Agents must follow the patterns defined in those files.

---

## Architecture Summary

The plugin contains a **dynamic Gutenberg block**.

General responsibilities:

PHP (`render.php`)
- resolve pricing data
- provide Interactivity API context
- render block markup

JavaScript (`view.js`)
- manage reactive state
- calculate pricing
- update UI
- handle WhatsApp interaction

The **WordPress Interactivity API** is used for all frontend reactivity.

---

## Documentation Rule

After completing any task, create a context file documenting the work.

Location:

.context/

Each file should describe:

- what was implemented
- which files were modified
- relevant notes for future work

---

## Git Rules

Branch examples:

- feature/interactivity-pricing
- fix/forminator-hidden-fields

Rules:

- commits must be atomic
- commits must be scoped
- do not squash unless requested
- do not perform git operations unless explicitly asked

---

## Development Rule

Do not introduce new architecture.

Before implementing changes:

1. read the block context document
2. review the relevant skills
3. analyze the existing implementation

Esto queda mucho más alineado con cómo trabajan los agentes:
	•	AGENTS.md = mapa
	•	.context/*.md = documentación real
	•	.github/skills/* = reglas de implementación

Sin duplicar información.

Si quieres, en el siguiente paso te puedo mostrar una mejora muy potente que usan equipos que trabajan con agentes: agregar un archivo pequeño llamado:

.context/architecture-map.md

que literalmente le dice al agente dónde está cada cosa en 10 líneas, y reduce aún más los errores cuando navega el repo.
