// Resources
// @see: https://www.technipages.com/how-to-auto-refresh-chrome-tabs-without-an-extension
// @see: https://stackoverflow.com/questions/1217929/how-to-automatically-reload-a-web-page-at-a-certain-time

// Paste this into the Web Inspector on the browser tab you want to refresh and hit Enter.

// Change this to the exact time you want to refresh the page at:
let then = new Date('2022-12-09 01:01:00'); 

window.setTimeout(() => window.location.reload(true), then.getTime() - new Date().getTime());