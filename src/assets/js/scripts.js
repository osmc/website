var hash = function () {
	return location.hash.slice(1);
};

// highlight current nav item
var relativeUrl = $(location).attr("pathname").split("/");
$(".nav-" + relativeUrl[1]).addClass("nav-current");

// Highlight blog on tag page and blog post
if (relativeUrl[1] === "tag" || $.isNumeric(relativeUrl[1])) {
	$(".nav-blog").addClass("nav-current");
}

// Open external links in new window
$(".nav-ul li a").each(function(i, item) {
	var url = $(item).attr("href");
	if (url.substring(0,4) === "http") {
		$(item).attr("target","_blank");
	}
});

// FRONT PAGE
var homeClass = "page-home";

if ($("body").hasClass(homeClass)) {

	var player = new Clappr.Player({
		source: '/assets/vid/homepage-tour.mp4',
		poster: '/assets/img/home/video-poster.png',
		preload: 'none',
		loop: 'true',
		width: '100%',
		height: '100%',
		parentId: '#player',
		chromeless: 'true'
	});

	$(".secondn-vignette").click(function ()  {

		var el = ".secondn-";

		var vid = $(el + "video-wrap video").get(0);
		var play = $(el + "video-wrap button.media-control-button[data-playpause]");
		var overlay = $(el + "overlay");
		var icon = $(el + "playicon");

		if (vid.paused) {
			play.click();
			overlay.addClass("hidden");
			icon.addClass("hidden");
		} else {
			play.click();
			overlay.removeClass("hidden");
			icon.removeClass("hidden");
		}

	});


	// set height 100%

	var hwindow = $(window).height();
	var h130 = $(window).height() * 1.55;
	$(".home .firstn").css("height", hwindow);
	setTimeout(function () {
		$(".home .firstn-wrap2").addClass("show");
	}, 100);
	$(".home .firstn-back").css("height", h130);

	// images

	currentZ = 1;
	currentImg = 1;

	$(".home .thirdn li").click(function ()  {
		var lclass = $(this).attr("class");
		var nr = lclass.substr(lclass.length - 1);

		if ($.isNumeric(nr)) {

			$(".thirdn-pics-wrap img").removeClass("show");
			$(".thirdn-pics-wrap .img-wrap" + currentImg + " img").addClass("show");
			$(".thirdn-pics-wrap .img-wrap" + nr).css("z-index", currentZ + 1);
			$(".thirdn-pics-wrap .img-wrap" + nr + " img").addClass("show");
			$(".thirdn-text li").removeClass("show");
			$(".thirdn-text li.link" + nr).addClass("show");

			oldImg = currentImg;
			currentImg = nr;

			setTimeout(function () {
				$(".thirdn-pics-wrap .img-wrap" + oldImg + " img").removeClass("show");
			}, 400);

		}
		currentZ = currentZ + 1;

	});

};

// Download
$(".download-disk-button").click(function () {
	var tables = $(".download-tables");
	if (tables.hasClass("show")) {
		tables.removeClass("show");
	} else  {
		tables.addClass("show");
	}
});

// Wiki search
if ($("body").hasClass("page-wiki")) {
	$(".wiki-search-input").on("input propertychange", function () {
		var val = $(this).val();
		var search = val.toLowerCase();

		$(".wiki-cat-list li a").each(function () {
			var title = $(this).attr("data-name");
			$(this).text(title);
			var match = title.toLowerCase().search(search);

			if (match > -1) {
				$(this).parent().removeClass("hide");

				if (val.length > 0) {
					var start = match;
					var end = match + search.length;
					var index = title.substring(start, end);
					var newText = title.replace(index, "<span class='highlight'>" + index + "</span>");
					$(this).html(newText);
				}
			} else {
				$(this).parent().addClass("hide");
				$(this).text(title);
			}
		});
	});
}

// hash scroll
if (hash() !== "donate" && hash().length > 1) {
	$("html, body").stop().animate({
		"scrollTop": $("#" + hash()).offset().top
	}, 200);
};

// Newsletter
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
			button.prop("disabled", false);
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

// DONATION

// check hash on load
if (hash() === "donate") {
	$(".donate").addClass("show");
};

$(".donate-exit").click(function () {
	history.pushState(undefined, undefined, " ");
	$(".donate").removeClass("show");
});

// check hash on change
window.addEventListener("hashchange", function (event) {
	if (hash() === "donate") {
		$(".donate").addClass("show");
		$("html, body").animate({
			scrollTop: 0
		}, 500);
	} else  {
		$(".donate").removeClass("show");
	}
});

// button loading
function buttonLoadStart() {
	var button = $(".donate-form").find(".clicked");
	button.prop("disabled", true);
	button.addClass("loading");
	button.find(".donate-stripe-svg").addClass("hidden");
	button.append("<img src='/assets/img/icon/load2.gif'>");
};

function buttonLoadStop() {
	var button = $(".donate-form").find(".clicked");
	button.prop("disabled", false);
	button.removeClass("loading");
	button.find(".donate-stripe-svg").removeClass("hidden");
	button.find("img").remove();
};

// set which donation form
$(".donate-button").click(function () {
	$(".donate-button").removeClass("clicked");
	$(this).addClass("clicked");
});

// validate form
$.each($(".donate-form"), function (index, oneForm)  {
	$(oneForm).validate({
		rules: {
			amount: {
				required: true,
				digits: true
			}
		},
		submitHandler: function () {
			var form = $(oneForm);

			var button = form.find(".clicked");
			var amount = form.find(".amount").val();
			var currency = form.find(".radio:checked").val();

			if (button.hasClass("donate-paypal"))  {
				var currentUrl = window.location.host + window.location.pathname;
				var paypallink = "https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=email@samnazarko.co.uk&item_name=OSMC%20Blog%20Donation&item_number=" + currentUrl + "&no_shipping=1&&no_note=1&tax=0&currency_code=" + currency + "&amount=" + amount;
				window.open(paypallink);
			}

			if (button.hasClass("donate-stripe")) {
				buttonLoadStart();
				var newamount = amount + "00";
				$.getScript("https://checkout.stripe.com/checkout.js", function () {
					stripe(newamount, currency);
				});
			}
		}
	});
});

// stripe
function stripe(am, cur) {
	var handler = StripeCheckout.configure({
		key: "pk_live_HEfJk95fTFmjEBYMYVTxWFZk",
		image: "/assets/img/logo/logo2-b.png",
		token: function (token) {
			window.location.href = "https://osmc.tv/contribute/donate/thanks/";
		}
	});

	// Open Checkout with further options
	handler.open({
		name: "OSMC Donation",
		description: "",
		amount: am,
		currency: cur,
		opened: function () {
			buttonLoadStop();
		}
	});

	// Close stripe checkout on page navigation
	$(window).on("popstate", function () {
		handler.close();
	});

};

// Contact form
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
			button.prop("disabled", false);
			button.text("Message sent");
		},
		error: function (res) {
			button.prop("disabled", false);
			button.text("Error");
		}
	});
});

// CHARTIST.JS
if (typeof CTtitle !== "undefined") {
	pieChart(CTtitle, CTdata);
}

function pieChart(title, items) {
	var nr = 0;
	var sum = function (a, b) {
		return a + b
	};

	var data = {
		names: [],
		series: []
	}

	for (i = 0; i < items.length; i++) {
		if (i % 2 === 0) {
			data.names.push(items[i]);
		} else {
			data.series.push(items[i]);
		}
	}

	var options = {
		labelInterpolationFnc: function (value) {
			var math = parseFloat((value / data.series.reduce(sum) * 100)).toFixed(2) + '%';
			if ((nr >= data.names.length) == false) {
				pieLegend(nr, math);
				nr += 1;
			}
			return math;
		},
		chartPadding: 0,
		labelOffset: 15,
	};

	var div = ".ct-chart";
	Chartist.Pie(div, data, options);
	var ctChart = $(div);

	ctChart.before('<p style="text-align: center; margin-top: 2em;"><em>' + title + '</em></p>');
	ctChart.after('<div class="ct-list"></div>');
	var legend = $(".ct-list");

	function pieLegend(i, calc) {
		var listItem = "<span class='button ct-series-" + i + "'>" + data.names[i] + " - " + calc + "</span>";
		legend.append(listItem);
	};

};

// Discourse comments
if ($("#discourse-comments").length) {
	$(window).on("scroll", function () {
		if (!commentsLoaded) {
			var visible = isVisible(".post-comments", 300);
			comments(visible);
		}
	});

	var visible = isVisible(".post-comments", 300);
	comments(visible);
}

var commentsLoaded = false;
function isVisible(elem, offset) {
	var $elem = $(elem);
	var $window = $(window);
	var docViewTop = $window.scrollTop();
	var docViewBottom = docViewTop + $window.height();
	var elemTop = $elem.offset().top;
	var elemBottom = elemTop + $elem.height() - offset;
	return ((elemBottom <= docViewBottom) && (elemTop >= docViewTop));
}


// check for comment id
if ( !topicId ) {
	var DiscourseSource = { 
		discourseUrl: 'https://discourse.osmc.tv/',
    discourseEmbedUrl: "https://osmc.tv" + window.location.pathname
	};
} else {
	var DiscourseSource = { 
		discourseUrl: 'https://discourse.osmc.tv/',
    topicId: topicId
	};
}

function comments(visible) {
	if (visible && commentsLoaded === false) {
		commentsLoaded = true;
		DiscourseEmbed = DiscourseSource;
		(function () {
			var d = document.createElement("script");
			d.type = "text/javascript";
			d.async = true;
			d.src = DiscourseEmbed.discourseUrl + "javascripts/embed.js";
			(document.getElementsByTagName("head")[0] || document.getElementsByTagName("body")[0]).appendChild(d);
		})();
	}
};