$.extend({
    isMobile: function() {
        return navigator.userAgent.match(/Android|webOS|iPhone|iPod|iPad|BlackBerry/i) !== null;
    }
});