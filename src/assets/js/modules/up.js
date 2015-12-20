// up button
var up = $(".up");
var upSize = parseInt(up.css("width"), 10);
var upBottom = parseInt(up.css("bottom"), 10);
var upOffset = upSize + upBottom;

// scroll up button
$(".up").click(function() {
  $("html, body").animate({
		"scrollTop": 0
	}, 300);
});