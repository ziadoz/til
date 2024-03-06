// Create a proxy to fetch which returns a pre-determined fake JSON response
window.fetch = new Proxy(window.fetch, {
    apply(target, that, args) {
        return Promise.resolve(
            new Response(
                '{ "foo": "bar" }',
                { status: 200, headers: { 'Content-Type': 'application/json' }},
            )
        );
    },
});

// Call fetch() and log the fake response
fetch('https://www.example.com')
  .then((r) => r.json())
  .then((j) => console.log(j.foo))
  .catch((e) => console.log('error', e.message));

// This is useful for Laravel Dusk or Codeception browser tests, when you don't want to hit a real API route.