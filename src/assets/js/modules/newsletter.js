if (hash() === "newsletter") {
  newsletterShow();
};

function newsletterShow() {
  if ($(".sidebar-news").length > 0) {
    $("body").addClass("overlay-show");
    $("html, body").animate({
      scrollTop: $(".sidebar-news").offset().top - 200
    }, 500);
  }
};


$(".sidebar-news-email").on("input propertychange", function () {
	if ($(this).val().length > 0)  {
		$(this).parent().addClass("focus");
		$(this).parent().removeClass("unfocus");
	} else {
		$(this).parent().removeClass("focus");
		$(this).parent().removeClass("error");
		$(this).parent().addClass("unfocus");
	}
});

var subscribeMessage = "Subscribed!   ｡◕‿◕｡";
var errorMessage = "Failed!   :'(";
$(".sidebar-news-form").submit(function (e) {
	e.preventDefault();
	var form = $(this);
	var button = form.find("button");
	var input = form.find(".sidebar-news-email");
	var url = form.attr("action");

	button.prop("disabled", true);
	form.addClass("posting");

	$.ajax({
		url: url,
		type: "POST",
		data: form.serialize(),
		success: function (res) {
			form.removeClass("posting");
			input.val(subscribeMessage);
			input.blur();
		},
		error: function (res) {
			button.prop("disabled", false);
			form.removeClass("posting");
			form.addClass("error");
			input.val(errorMessage);
			input.blur();
		}
	});
});