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
