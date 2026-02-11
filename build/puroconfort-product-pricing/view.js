import * as __WEBPACK_EXTERNAL_MODULE__wordpress_interactivity_8e89b257__ from "@wordpress/interactivity";
/******/ var __webpack_modules__ = ({

/***/ "@wordpress/interactivity"
/*!*******************************************!*\
  !*** external "@wordpress/interactivity" ***!
  \*******************************************/
(module) {

module.exports = __WEBPACK_EXTERNAL_MODULE__wordpress_interactivity_8e89b257__;

/***/ }

/******/ });
/************************************************************************/
/******/ // The module cache
/******/ var __webpack_module_cache__ = {};
/******/ 
/******/ // The require function
/******/ function __webpack_require__(moduleId) {
/******/ 	// Check if module is in cache
/******/ 	var cachedModule = __webpack_module_cache__[moduleId];
/******/ 	if (cachedModule !== undefined) {
/******/ 		return cachedModule.exports;
/******/ 	}
/******/ 	// Check if module exists (development only)
/******/ 	if (__webpack_modules__[moduleId] === undefined) {
/******/ 		var e = new Error("Cannot find module '" + moduleId + "'");
/******/ 		e.code = 'MODULE_NOT_FOUND';
/******/ 		throw e;
/******/ 	}
/******/ 	// Create a new module (and put it into the cache)
/******/ 	var module = __webpack_module_cache__[moduleId] = {
/******/ 		// no module.id needed
/******/ 		// no module.loaded needed
/******/ 		exports: {}
/******/ 	};
/******/ 
/******/ 	// Execute the module function
/******/ 	__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 
/******/ 	// Return the exports of the module
/******/ 	return module.exports;
/******/ }
/******/ 
/************************************************************************/
/******/ /* webpack/runtime/make namespace object */
/******/ (() => {
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = (exports) => {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/ })();
/******/ 
/************************************************************************/
var __webpack_exports__ = {};
// This entry needs to be wrapped in an IIFE because it needs to be isolated against other modules in the chunk.
(() => {
/*!*************************************************!*\
  !*** ./src/puroconfort-product-pricing/view.js ***!
  \*************************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _wordpress_interactivity__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @wordpress/interactivity */ "@wordpress/interactivity");
/**
 * Use this file for JavaScript code that you want to run in the front-end
 * on posts/pages that contain this block.
 *
 * When this file is defined as the value of the `viewScript` property
 * in `block.json` it will be enqueued on the front end of the site.
 *
 * Example:
 *
 * ```js
 * {
 *   "viewScript": "file:./view.js"
 * }
 * ```
 *
 * If you're not making any changes to this file because your project doesn't need any
 * JavaScript running in the front-end, then you should delete this file and remove
 * the `viewScript` property from `block.json`.
 *
 * @see https://developer.wordpress.org/block-editor/reference-guides/block-api/block-metadata/#view-script
 */


const formatPrice = value => {
  const number = Number(value) || 0;
  const hasDecimals = Math.abs(number % 1) > 0;
  const locale = document.documentElement.lang || 'es-MX';
  return `₡${number.toLocaleString(locale, {
    minimumFractionDigits: hasDecimals ? 2 : 0,
    maximumFractionDigits: hasDecimals ? 2 : 0
  })}`;
};

// Namespace must match data-wp-interactive in render.php, or directives won't bind.
const {
  state,
  actions
} = (0,_wordpress_interactivity__WEBPACK_IMPORTED_MODULE_0__.store)('puroconfort-pricing', {
  state: {
    quantity: 1,
    price_single: 0,
    price_bulk: 0,
    productName: '',
    unit_price_applied: 0,
    total: 0,
    totalFormatted: ''
  },
  actions: {
    init() {
      const context = (0,_wordpress_interactivity__WEBPACK_IMPORTED_MODULE_0__.getContext)();
      state.quantity = Number(context.quantity) || 1;
      state.price_single = Number(context.price_single) || 0;
      state.price_bulk = Number(context.price_bulk) || 0;
      state.productName = context.productName || '';
      actions.update();
    },
    update(event) {
      const next = event ? Number.parseInt(event.target.value, 10) : state.quantity;
      state.quantity = Number.isFinite(next) && next >= 1 ? next : 1;
      // Assigning into state is required so data-wp-text can react.
      state.unit_price_applied = state.quantity >= 12 ? state.price_bulk : state.price_single;
      state.total = state.quantity * state.unit_price_applied;
      state.totalFormatted = formatPrice(state.total);

      // Debugging to verify Interactivity action execution and state mutation.
      // Remove once confirmed in console.
      console.log('Quantity:', state.quantity);
      console.log('Unit price applied:', state.unit_price_applied);
      console.log('Total (raw):', state.total);
      console.log('State totalFormatted:', state.totalFormatted);
    }
    // TODO: Integrate Click to Chat / wa.me URL here.
  }
});
})();


//# sourceMappingURL=view.js.map