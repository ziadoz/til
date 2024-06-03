// Export bookmarks, open HTML, run this:

const links = [];

document.querySelectorAll('body > dt:nth-of-type(4) a').forEach((a) => {
    links.push(a.getAttribute('href'));
});

links.join("\n");