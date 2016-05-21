if ($("body").hasClass("page-wiki")) {
    
  var wikiCheck = function() {
    $(".wiki-cat").each(function() {
      var noResults =  $(this).find(".wiki-cat-noresults");
      var items = $(this).find("ul li").not(".hide, .wiki-cat-noresults");
      
      if (items.length === 0) {
        noResults.addClass("show");
      } else {
        noResults.removeClass("show");
      }
    });
  };
  
  var wikiSearch = function(input) {
    var val = input.val();
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
      }
    });
  };
  
  $(".wiki-search-input").on("input propertychange", function () {
    wikiSearch($(this));
    wikiCheck();
  });
}