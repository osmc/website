$("#nav-res-toggle").click(function () {

  if ($(this).hasClass("open"))  {
    $(".top-nav").removeClass("open");
    $("#nav-res-toggle").removeClass("open");
  } else  {
    $(".top-nav").addClass("open");
    $("#nav-res-toggle").addClass("open");
  }

});

// Wiki search

if ($("body").hasClass("page-wiki")) {
  $(".wiki-search-input").on("input propertychange", function() {
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

// FRONT PAGE

var homeClass = "page-home";

// Video

if ($("body").hasClass(homeClass)) {

  var player = new Clappr.Player({
    source: '/assets/vid/homepage-tour.mp4',
    poster: '/assets/img/video-poster.png',
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

      $(".home .thirdn img").removeClass("show");
      $(".home .thirdn .img-wrap" + currentImg + " img").addClass("show");
      $(".home .thirdn .img-wrap" + nr).css("z-index", currentZ + 1);
      $(".home .thirdn .img-wrap" + nr + " img").addClass("show");
      $(".home .thirdn li").removeClass("show");
      $(".home .thirdn li.link" + nr).addClass("show");

      oldImg = currentImg;
      currentImg = nr;

      setTimeout(function () {
        $(".home .thirdn .img-wrap" + oldImg + " img").removeClass("show");
      }, 400);

    }
    currentZ = currentZ + 1;

  });

};

// NEWSLETTER FORM

// Subscribe
$(".sidebar-news-email").on("input propertychange", function() {
   if ( $(this).val().length > 0 ) {
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
$(".sidebar-news-form").submit(function(e) {
  e.preventDefault();
  var form = $(this);
  var button = form.find("button");
	var input = form.find(".sidebar-news-email");
	var url = form.attr("action");
	  
  button.prop('disabled', true);
  form.addClass("posting");
  
  $.ajax({
    url: url,
    type: "POST",
    data: form.serialize(),
    success: function(res) {
      button.prop('disabled', false);
      form.removeClass("posting");
      input.val(subscribeMessage);
			input.blur();
    },
    error: function(res) {
      button.prop('disabled', false);
      form.removeClass("posting");
      //form.addClass("error");
      input.val(subscribeMessage);
			input.blur();
    }
  });

    

});

// DONATION

// check hash on load
var url_load = document.URL.substr(document.URL.indexOf('#') + 1);
if (url_load === "donate") {
  popupDonateShow();
};

// check hash on change
$(window).on('hashchange', function () {
  var hash = location.hash.slice(1);
  if (hash == "donate") {
    popupDonateShow();
  } else {
		popupDonateHide();
	}
});

// button loading
function buttonLoadStart() {
  var button = $(".donationwidget form").find(".clicked");
  button.prop('disabled', true);
  button.addClass("loading");
  button.find(".svg").addClass("hidden");
  button.append('<img src="https://osmc.tv/content/themes/osmc/library/images/preloader.gif">');
};
function buttonLoadStop() {
  var button = $(".donationwidget form").find(".clicked");
  button.prop('disabled', false);
  button.removeClass("loading");
  button.find(".svg").removeClass("hidden");
  button.find("img").remove();
};

// popup
function popupDonateShow() {
  var overlay = $(".overlay");
  var popup = $(".popup_donate");
  overlay.addClass("show");
  popup.addClass("show");

  setTimeout(function () {
    overlay.addClass("fade");
    popup.addClass("fade");
  }, 100);

};
function popupDonateHide() {
  $(".popup_donate").removeClass("show fade");
  $(".overlay").removeClass("show fade");
	if (location.hash.slice(1) != "q") {
		window.location.hash = "q";
	}
};

// hide popup on overlay click
$(".overlay").click(function () {
  popupDonateHide();
});

// set which donation form
$(".donationwidget button").click(function () {
  $('.donationwidget button').removeClass("clicked");
  $(this).addClass("clicked");
});

// validate form
$.each($(".donationwidget form"), function (index, oneForm)  {  
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

      if (button.hasClass("paypal"))  {
        
        var currentUrl = window.location.host + window.location.pathname;
        var paypallink = "https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=email@samnazarko.co.uk&item_name=OSMC%20Blog%20Donation&item_number=" + currentUrl + "&no_shipping=1&&no_note=1&tax=0&currency_code=" + currency + "&amount=" + amount;

        window.open(paypallink);

      }
      if (button.hasClass("stripe")) {
        
        buttonLoadStart();
        var newamount = amount + "00";
        $.getScript("https://checkout.stripe.com/checkout.js", function () {
          stripe(newamount, currency);
        });
      }
      if (button.hasClass("bitcoin")) {
        console.log("bitcoin");
        window.addEventListener('message', receiveMessage, false);
        $(".overlay").before('<iframe class="overlayCoinbase" id="coinbase_inline_iframe_db2f17ea41912e21935a4850388cd848" src="https://www.coinbase.com/checkouts/db2f17ea41912e21935a4850388cd848/inline" style="width: 460px; height: 350px; border: none; box-shadow: 0 1px 3px rgba(0,0,0,0.25);" allowtransparency="true" frameborder="0"></iframe>');
      };
    }
  });
});

// stripe
function stripe(am, cur) {
  var handler = StripeCheckout.configure({
    key: 'pk_live_HEfJk95fTFmjEBYMYVTxWFZk',
    image: '/content/themes/osmc/library/images/favicons/apple-touch-icon-180x180.png',
    token: function (token) {
      window.location.href = "https://osmc.tv/contribute/donate/thanks/";
    }
  });

  // Open Checkout with further options
  handler.open({
    name: 'OSMC Donation',
    description: "",
    amount: am,
    currency: cur,
    opened: function () {
      buttonLoadStop();
    }
  });
  
  // Close stripe checkout on page navigation
  $(window).on('popstate', function () {
    handler.close();
  });
  
};

// DOWNLOAD SCROLL TO

$(".download.devices .wrapper").click(function () {
  $('html, body').animate({
    scrollTop: $(".getstarted").offset().top - 40
  }, 800);
});

// CHARTIST.JS

function pieChart(title, items) {
  
  var nr = 0;
  var sum = function(a, b) { return a + b };
  
  var data = {
    names: [],
    series: []
  }
    
  for (i = 0; i < items.length; i++ ) {
    if (i % 2 === 0) {
      data.names.push(items[i]);
    } else {
      data.series.push(items[i]);
    }
  }
  
  var options = {
    labelInterpolationFnc: function(value) {
      var math = parseFloat((value / data.series.reduce(sum) * 100)).toFixed(2) + '%';
      if ( (nr >= data.names.length) == false ) {
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