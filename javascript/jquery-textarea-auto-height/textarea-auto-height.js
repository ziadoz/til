$('textarea.notes').on('focus blur keyup', function() {
    var paddingTop = $(this).css('padding-top').replace('px', ''),
        paddingBottom = $(this).css('padding-bottom').replace('px', '');
		
    $(this).css('height', '1px');
    $(this).css('height', (this.scrollHeight - paddingTop - paddingBottom) + 'px');
}).blur();