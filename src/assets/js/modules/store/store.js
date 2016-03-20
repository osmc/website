$(window).on("load", function() {
  var grid = $(".store-grid");
  grid.css("height", grid.height());
});

$(".store-nav a").click(function() {
  // nav
  $(".store-nav a").removeClass("active");
  $(this).addClass("active");

  // grid
  var category = $(this).attr("data-name");
  var items = $(".store-item-wrap");
  var grid = $(".store-grid");
  var aniTime = grid.css("transition-duration").slice(0, -1) * 1000;

  grid.addClass("hide");

  setTimeout(function() {      
    if (category == "All") {
      items.removeClass("hide");
      grid.removeClass("hide");
      return;
    }

    items.addClass("hide");

    items.each(function() {
      if ($(this).attr("data-categories").indexOf(category) !== -1) {
        $(this).removeClass("hide");
      }
    });

    grid.removeClass("hide");

  }, aniTime);

});
