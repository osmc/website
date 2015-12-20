function isVisible(elem, offset) {
	var $elem = $(elem);
	var $window = $(window);
	var docViewTop = $window.scrollTop();
	var docViewBottom = docViewTop + $window.height();
	var elemTop = $elem.offset().top;
	var elemBottom = elemTop + $elem.height() - offset;
	return ((elemBottom <= docViewBottom) && (elemTop >= docViewTop));
}

var hash = function () {
	return location.hash.slice(1);
};

function removeHash() {
  history.pushState(undefined, undefined, " ");
};

// check hash on change
window.addEventListener("hashchange", function (event) {
  var h = hash();
  
	if (h === "donate") {
		donateShow();
	}
  
  if (h === "newsletter") {
    newsletterShow();
  }
});