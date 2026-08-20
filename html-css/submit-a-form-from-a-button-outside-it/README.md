# Submit a Form From a Button Outside It

A `<button>` (or any submit control) doesn't have to live inside the `<form>` it submits. Give the form an `id` and point the button at it with the `form` attribute, and the button will submit that form even when it sits elsewhere in the page.

```html
<form id="myForm" action="/save" method="post">
    <input name="title">
</form>

<button form="myForm">Submit</button>
```

The `form` attribute is part of the HTML standard for [form-associated elements](https://developer.mozilla.org/en-US/docs/Web/HTML/Element/button#form) (`button`, `input`, `select`, `textarea`, `output`, etc.), so the same trick works for inputs that need to belong to a form they aren't nested inside.

This is handy when layout forces the controls apart, for example a sticky footer bar with a save button that submits a form higher up the page, or a modal whose action buttons sit in a separate footer element from the fields.

You can also override the form's own settings from the button using the `formaction`, `formmethod`, `formenctype`, `formtarget`, and `formnovalidate` attributes, so a second button can post the same form to a different endpoint.

```html
<form id="myForm" action="/save" method="post">
    <input name="title">
</form>

<button form="myForm">Save</button>
<button form="myForm" formaction="/save-draft" formnovalidate>Save Draft</button>
```

Here the first button submits to `/save` as configured on the form, while the second reuses the same fields but posts to `/save-draft` and skips validation, all without a second `<form>`.

The association works in JavaScript too. A form's [`elements`](https://developer.mozilla.org/en-US/docs/Web/API/HTMLFormElement/elements) collection includes every associated control, including ones sitting outside the form via the `form` attribute. So if you disable submit buttons until some content has loaded, iterating `form.elements` catches the external buttons as well, not just the ones nested inside.

```js
[...supportForm.elements]
    .filter((element) => element.type === 'submit')
    .forEach((button) => (button.disabled = false));
```

Querying `supportForm.querySelectorAll('[type=submit]')` would miss those outside buttons, since they aren't descendants of the form in the DOM.
