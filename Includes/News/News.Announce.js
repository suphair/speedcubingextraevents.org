$('.news_panel_link').click(function () {
    var element_text = $(this).parent().find('p');
    if (element_text.is(':visible')) {
        $(this).removeClass('show');
        element_text.hide('fast');
    } else {
        $(this).addClass('show');
        element_text.show('fast');
    }

});