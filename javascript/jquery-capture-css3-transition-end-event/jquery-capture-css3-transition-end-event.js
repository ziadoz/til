$(document).ready(function() {
    $('div').on('transitionend MSTransitionEnd webkitTransitionEnd oTransitionEnd', function(event) {
        // Do stuff after transition
    });
});