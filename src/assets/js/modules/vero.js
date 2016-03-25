if ($("body").hasClass("page-vero")) {
  $(".vero-menu ul").onePageNav({
      currentClass: "current",
      changeHash: false,
      scrollSpeed: 600,
      scrollThreshold: 0.5,
      easing: "swing"
  });  
  
  $(".vero-feat-list a").click(function(e) {
    
    // remove currents
    $(".vero-feat-list a").each(function() {
      $(this).removeClass("current");
    });
    
    // set current
    $(this).addClass("current");
    
    
    // change box
    var target = $(this).attr("href").substring(1);
    $(".vero-feat-" + target).prependTo($(".vero-feat-content"));
    
    e.preventDefault();
  });
}

var veroScroll = function () {
  if (isVisible("#vero-design", 0)) {
    $(".vero-design").addClass("aniStart");
  }
}