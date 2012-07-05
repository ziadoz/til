// Throttle / Debounce Plugin: http://benalman.com/projects/jquery-throttle-debounce-plugin/

$(document).ready(function() {
    var callback = function(event) {			
        event.preventDefault();
        // Do exciting things here.	
    };

    $('form.search').on({
        submit: callback,
        keyup:  $.debounce(350, callback)
    });
});