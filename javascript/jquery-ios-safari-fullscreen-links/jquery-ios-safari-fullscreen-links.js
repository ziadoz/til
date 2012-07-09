// Ensure iOS Safari fullscreen links don't open in external Safari.
$(document).ready(function() {
    if (/(iPhone|iPod|iPad)/i.test(navigator.userAgent)) { 
        $('a').each(function() {
            $(this).attr('href', 'javascript:window.location = "' + $(this).attr('href') + '"');
	});
    }
});	
