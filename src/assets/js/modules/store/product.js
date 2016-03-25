var cartFrame = $(".store-cart-frame iframe");
var buyFrame = $(".store-product-frame");

$(".store-product-cart").click(function(e) {
  e.preventDefault();
  var button = $(this);
  if (button.hasClass("disabled")) {
    return;
  }
  
  var quantity = $(".store-product-quantity-input").val();
  
  var option = "";
  
  var variable = $(".store-product-var-input");
  if (variable.length) {
    var selected = variable.find("option:selected");
    var id = selected.attr("data-id");
    var link = selected.attr("data-link");
    
    var attr = "&attribute" + link.split("attribute")[1];
    option = "&variation_id=" + id + attr;
  }
  
  var url = button.attr("href") + "&quantity=" + quantity + option;
  buyFrame.attr("src", url);
  
  button.addClass("disabled").text("Loading");
    
  buyFrame.on("load", function () {
    button.removeClass("disabled").addClass("success").text("Product added!");
    
    cartFrame.attr("src", cartFrame.attr("src"));
    
    setTimeout(function() {
      button.removeClass("success").text(button.attr("data-name"));
    }, 4000);
  });
  
});

cartFrame.iFrameResize();