// Auto-wire elements as they enter and leave the DOM, using a single
// MutationObserver, so you never have to re-run an init after touching the page
// (ajax, innerHTML, cloned templates, infinite scroll, etc.). Load this after
// Bootstrap's JS bundle.
//
// - Tooltips: any [data-bs-toggle="tooltip"] with a non-empty title is created
//   on insert and disposed on removal. Toggling those attributes on an element
//   that's already in the page works too.
// - Timers: any [data-timer] element (attribute = a start time in epoch
//   milliseconds) shows the seconds elapsed since then, ticking every second.
//   The interval is cleared when the element leaves the DOM.

(() => {
    const { Tooltip } = bootstrap;

    // Skip empty titles: Bootstrap throws normalising an empty data-bs-title,
    // and a titleless tooltip can never show anyway.
    const isTooltipElement = (el) => el.matches('[data-bs-toggle="tooltip"]:not([data-bs-title=""])');
    const bindTooltip = (el) => Tooltip.getOrCreateInstance(el); // idempotent
    const disposeTooltip = (el) => Tooltip.getInstance(el)?.dispose(); // null-safe

    const isTimerElement = (el) => el.matches('[data-timer]');
    const startTimer = (el) => {
        const tick = () => {
            const seconds = Math.floor((Date.now() - Number(el.dataset.timer)) / 1000);
            el.textContent = `${seconds}s`;
        };

        tick(); // paint immediately, don't wait a second for the first frame

        return setInterval(tick, 1000);
    };

    const ready = (fn) =>
        document.readyState === 'loading' ? document.addEventListener('DOMContentLoaded', fn) : fn();

    ready(() => {
        // Keyed by element so a removed timer's interval can be cleared.
        const timers = new Map();

        const mount = (el) => {
            if (isTooltipElement(el)) {
                bindTooltip(el);
            }

            if (isTimerElement(el)) {
                timers.set(el, startTimer(el));
            }
        };

        const unmount = (el) => {
            if (isTooltipElement(el)) {
                disposeTooltip(el);
            }

            if (timers.has(el)) {
                clearInterval(timers.get(el));
                timers.delete(el);
            }
        };

        // Wire up whatever is already on the page.
        document.querySelectorAll('[data-bs-toggle="tooltip"], [data-timer]').forEach(mount);

        new MutationObserver((mutations) => {
            for (const mutation of mutations) {
                // An element already in the DOM had its tooltip attributes changed:
                // bind if it now qualifies, tear down if it no longer does.
                if (mutation.type === 'attributes') {
                    if (isTooltipElement(mutation.target)) {
                        bindTooltip(mutation.target);
                    } else {
                        disposeTooltip(mutation.target);
                    }

                    continue;
                }

                // addedNodes/removedNodes give the top of each changed subtree, so
                // walk into each one to catch matching descendants.
                for (const node of mutation.addedNodes) {
                    if (node.nodeType === Node.ELEMENT_NODE) {
                        [node, ...node.querySelectorAll('*')].forEach(mount);
                    }
                }

                for (const node of mutation.removedNodes) {
                    if (node.nodeType === Node.ELEMENT_NODE) {
                        [node, ...node.querySelectorAll('*')].forEach(unmount);
                    }
                }
            }
        }).observe(document.body, {
            childList: true,
            subtree: true,
            attributes: true,
            attributeFilter: ['data-bs-toggle', 'data-bs-title'],
        });
    });
})();
