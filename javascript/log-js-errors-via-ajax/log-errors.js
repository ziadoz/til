// On error send to backend route which will log error into central logging service.
window.onerror = function(message, file, line) {
  fetch(
    '/log/js-error', 
    {
      method: 'POST',
      headers: { 
        'Content-Type': 'application/json' 
      },
      body: JSON.stringify({
        file: file,
        line: line,
        message: message,
        browser: navigator.userAgent,
        url: window.location.href
    })
  );
};