var homeClass = "page-home";
if ($("body").hasClass(homeClass)) {

	if (hash() === "newsletter" || hash() === "donate") {
		window.location.replace("/blog/#" + hash());
	}

  $(".firstn-down").click(function() {
    $("html, body").animate({
      scrollTop: $(".secondn").offset().top - 80
    }, 500);
  });

	var player = new Clappr.Player({
		source: cdn + '/assets/vid/homepage-tour.mp4',
		poster: cdn + '/assets/img/home/video-poster.png',
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
	$(".home .firstn-back").css("height", h130);
  $(".home .firstn-wrap2").addClass("show");

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
