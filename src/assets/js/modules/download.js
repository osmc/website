$(".download-devices-wrap").click(function () {
	$("html, body").animate({
		"scrollTop": $("#installation").offset().top - 20
	}, 600);
});

$(".download-disk-button").click(function () {
	var tables = $(".download-tables");
	if (tables.hasClass("show")) {
		tables.removeClass("show");
	} else  {
		tables.addClass("show");
	}
});