var post = $(".post-content");
var images = post.find("img");

images.each(function() {
  var img = $(this);
  var imgSrc = img.attr("src");
  
  if (!img.parent().is("a") && !img.parent().hasClass("store-product-feat")) {
    img.wrap($('<a>',{
      href: imgSrc,
      class: "lightbox"
    }));
  }
});

$(".lightbox-wrap").Chocolat({
  imageSelector: ".lightbox",
  duration: 0,
  imageSize: "default",
  loop: true
});