$(window).on("scroll", function () {
  var top = $(document).scrollTop();
  var bottom = $(document).scrollTop() + $(window).height();
  
  // load discourse comments if visible
  if ($("#discourse-comments").length && !commentsLoaded) {
    var visible = isVisible(".post-comments", 300);
    comments(visible);
  }
  
  // up button scroll
  var footerPos = $(".footer").offset().top;
  if (bottom >= footerPos) {
    up.addClass("bottom");
    up.css("top", footerPos - upOffset);
  } else {
    up.removeClass("bottom");
    up.css("top", "");
  }
      
  if (top > 1100) {
    up.addClass("show");
  } else {
    up.removeClass("show"); 
  }
});