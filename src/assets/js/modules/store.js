$(".store-product-cart").submit(function (e) {
  e.preventDefault();
  var form = $(this);
  var url = form.attr("action");
  
  console.log(url);
  
  $.ajax({
		url: url,
		type: "post",
    data: form.serialize(),
		success: function (res) {
      console.log("done");
		},
		error: function (res) {
			console.log(res);
		}
	});
  
});