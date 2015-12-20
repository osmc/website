if ($("#discourse-comments").length) {
  setTimeout(function() {
      var visible = isVisible(".post-comments", 300);
      comments(visible);
    }, 200);
};

var commentsLoaded = false;

// check for comment id
if (typeof topicId === "undefined") {
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
	if (visible && commentsLoaded === false && typeof draft === "undefined") {
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