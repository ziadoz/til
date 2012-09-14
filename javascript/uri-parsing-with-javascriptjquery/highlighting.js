(function($) {
    $(document).ready(function() {
        // Highlight Navigation
        var url = $.parseUrl(document.location);
        $('a').each(function() {
            var link = $.parseUrl(this.href);
            if (link.pathname !== '' && link.pathname === url.pathname) {
                $(this).siblings().removeClass('active');
                $(this).addClass('active');
            }
        });
    });

    // URL Parsing
    $.extend({
        parseUrl: function(url) {
            var parser = document.createElement('a');
            parser.href = url;
            return parser;      
        }
    });
})(jQuery);