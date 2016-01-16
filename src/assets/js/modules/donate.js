// check hash on load
if (hash() === "donate") {
	donateShow();
};

$(".donate-exit").click(function () {
	donateExit();
});

function donateShow() {
  $(".donate").addClass("show");
  $("html, body").animate({
    scrollTop: 0
  }, 500);
};

function donateExit() {
  removeHash();
	$(".donate").removeClass("show");
};

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
				var paypallink = "https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=donate@osmc.tv&item_name=OSMC%20Blog%20Donation&item_number=" + currentUrl + "&no_shipping=1&&no_note=1&tax=0&currency_code=" + currency + "&amount=" + amount;
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
			window.location.href = "https://osmc.tv/donate-thanks";
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
