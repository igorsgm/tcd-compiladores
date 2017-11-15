(function ($) {

	baseUrl = new RegExp(/^.*\//).exec(window.location.href)[0];

	submitFormCode = function () {

		var form = $('[data-form="code"]');

		$.ajax({
			url:      baseUrl + form.attr('action'),
			type:     'POST',
			data:     {
				code: $('#code').val()
			},
			dataType: 'json',
			success:  function (data) {
				onCompileSuccess(data);
			},
			error:    function (data) {
			}
		});
	};

	onCompileSuccess = function (data) {
		console.log(data);
	};

	$(document).on('click', '#compile', function () {
		submitFormCode();
	});

}(window.jQuery.noConflict(), window, document));