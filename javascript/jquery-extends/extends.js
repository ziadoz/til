$.fn.extend({
    // ---- HTML Comments ---- //
    comments: function() {
        return $(this).contents()
                      .filter(function() { 
                          return this.nodeType == 8;
                       });
    },
  
    // ---- Outer HTML ---- //
    outerHtml: function() {
        return $(this).clone().wrap('<div>').parent().html();
    }
});

$.extend({
    // ---- Quick URL Parsing ---- //
    parseUrl: function(url) {
        var parser = document.createElement('a');
        parser.href = url;
        return parser;
    }
});