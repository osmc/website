if ($("body").hasClass("page-wiki")) {
	$(".wiki-search-input").on("input propertychange", function () {
		var val = $(this).val();
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
				$(this).text(title);
			}
		});
	});
}