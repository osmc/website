$(".contact-form").submit(function (e) {
	e.preventDefault();
	var form = $(this);
	var button = form.find("button");
	var url = form.attr("action");
	button.prop("disabled", true);

	$.ajax({
		url: url,
		type: "POST",
		data: form.serialize(),
		success: function (res) {
			button.text("Message sent");
			button.addClass("button-alt1");
		},
		error: function (res) {
			button.prop("disabled", false);
			button.text("Error");
			button.addClass("button-alt2");
		}
	});
});