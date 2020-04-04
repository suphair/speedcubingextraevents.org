$('.status_mail.send').addClass('fas fa-check-circle');
$('.status_mail.error').addClass('fas fa-exclamation-triangle');

$('[data-message]').click(function () {
    $(this).parents('tr').next().show();
    return false;
});