// See: http://ejohn.org/blog/learning-from-twitter/
(function($) {
  $(document).ready(function() {
    var resizeCallable = function() {
        switch (true)
        {
          case (window.innerWidth <= 768):
            // Do some exciting device size specific magic here.
            break;
        }
    };

    var didResize = false;
    $(window).on('load resize', function() {
      didResize = true;
    });
	
    setInterval(function() {
      if (didResize) {
        didResize = false;
        resizeCallable();
      }
    }, 250);
  });
})(jQuery);