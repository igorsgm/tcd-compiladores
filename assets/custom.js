(function ($) {

	baseUrl = new RegExp(/^.*\//).exec(window.location.href)[0];

	submitFormCode = function () {

		var form = $('[data-form="code"]');

		// form.submit();
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
		$('[data-panel-body="console"]').html(data);
	};

	$(document).on('click', '#compile', function () {
		submitFormCode();
	});

}(window.jQuery.noConflict(), window, document));