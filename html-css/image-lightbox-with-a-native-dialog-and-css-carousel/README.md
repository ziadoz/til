# Image Lightbox With a Native Dialog and CSS Carousel

A thumbnail gallery with a full-screen lightbox and swipeable carousel, built almost entirely from HTML and CSS. Full source in [`index.php`](index.php) and [`styles.css`](styles.css).

The moving parts:

- **Open with no JS.** A `<button command="show-modal" commandfor="lightbox">` [command invoker](https://developer.mozilla.org/en-US/docs/Web/API/Invoker_Commands_API) opens the [`<dialog>`](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/dialog) declaratively.
- **Close with no JS.** `closedby="any"` gives backdrop/<kbd>Esc</kbd> dismiss, and the close button is a `<form method="dialog">`.
- **Dark backdrop.** `dialog::backdrop { background: rgb(0 0 0 / 0.8); }`.
- **Pure-CSS carousel.** A scroll-snapping list plus [`::scroll-button()`](https://developer.mozilla.org/en-US/docs/Web/CSS/::scroll-button) for prev/next and [`::scroll-marker`](https://developer.mozilla.org/en-US/docs/Web/CSS/::scroll-marker) for dots, pinned to the edges with [anchor positioning](https://developer.mozilla.org/en-US/docs/Web/CSS/CSS_anchor_positioning). See the [MDN carousel guide](https://developer.mozilla.org/en-US/docs/Web/CSS/Guides/Overflow/Carousels).

The one gotcha: to open on the *clicked* image, you can't `scrollIntoView()` in the click handler. The invoker opens the dialog in the click's default action, *after* JS listeners, so the slide is still `display: none` when you try to scroll. Record the clicked index, then scroll on the dialog's [`toggle`](https://developer.mozilla.org/en-US/docs/Web/API/HTMLElement/toggle_event) event once it's open.
