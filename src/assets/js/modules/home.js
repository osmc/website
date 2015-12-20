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