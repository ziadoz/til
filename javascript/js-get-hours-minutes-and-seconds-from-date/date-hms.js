console.log(
    new Intl.DateTimeFormat('en-GB', { hour: 'numeric', minute: 'numeric', second: 'numeric' })
        .formatToParts(new Date)
        .map((part) => part.value)
        .join('')
);