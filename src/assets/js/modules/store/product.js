$(".store-product-cart").click(function(e) {
  e.preventDefault();
  var button = $(this);
  if (button.hasClass("disabled")) {
    return;
  }
  
  var quantity = $(".store-product-quantity").val();
  var iframe = $(".store-product-frame");
  
  var option = "";
  
  var variable = $(".store-product-variable");
  if (variable.length) {
    var selected = variable.find("option:selected");
    var id = selected.attr("data-id");
    var link = selected.attr("data-link");
    
    var attr = "&attribute" + link.split("attribute")[1];
    option = "&variation_id=" + id + attr;
  }
  
  var url = button.attr("href") + "&quantity=" + quantity + option;
      
  iframe.attr("src", url);
  
  button.addClass("disabled").text("Loading");
    
  iframe.on("load", function () {
    button.removeClass("disabled").addClass("success").text("Product added!");
    
    setTimeout(function() {
      button.removeClass("success").text(button.attr("data-name"));
    }, 4000);
  });
  
});