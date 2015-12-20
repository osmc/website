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
	if (url.substring(0,4) === "http" && url !== "https://store.osmc.tv") {
		$(item).attr("target","_blank");
	}
});