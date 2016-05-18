jQuery(document).ready(function($) {
	$(document.body).on('keyup change', 'form.register #reg_password, form.checkout #account_password, form.edit-account #password_1, form.lost_reset_password #password_1', function() {
	console.log($(this)._data);
		$(this).closest('form').find('input[type="submit"]').attr('disabled', false).removeClass('disabled');
	});
});