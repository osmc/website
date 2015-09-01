Discourse.DiscoveryView = Ember.View.extend({
  _insertBanner: function() {
    if ( ad ) {
      this.$(".list-container").prepend("<div class='toplist_ad' data-adunit='discourse_toplist' data-size-mapping='toplist'></div>");

      this.$(".toplist_ad").dfp({
        dfpID:'302320750',
        sizeMapping: {
          'toplist': [
            {browser: [1100, 0], ad_sizes: [970, 90]},
            {browser: [810, 0], ad_sizes: [728, 90]},
            {browser: [530, 0], ad_sizes: [468, 60]},
            {browser: [0, 0], ad_sizes: [320, 50]}
          ]
        }
      });
      
    } else {
      
      this.$(".list-container").prepend("<a href='" + bannerlink + "'><div class='toplist_banner' style='color:#fafafa; padding:2.5em 1em; margin:0.2em 0;'><h2 style='color:white; font-size:130%; font-weight:300; text-align: center;'>" + bannertext + "</h2></div></a>");
      this.$(".toplist_banner").css({background : "url(" + bannerimg + ")"});
    }
    
  }.on('didInsertElement'),
  
  _insertDonateButton: function() {
    this.$(".list-controls .container").before('<a target="_blank" href="https://osmc.tv/contribute/donate/#donate"><button class="btn btn-default donate-button"><i class="fa fa-heart"></i>Donate</button></a>')
  }.on('didInsertElement')
});

autoLinks.forEach(function(a) {
  var re = new RegExp(a[0]);
  Discourse.Dialect.inlineRegexp({
    start: a[0],
    matcher: re,
    spaceBoundary: true,
    emitter: function() {
      setTimeout(function(){
        var textbox = $("textarea.ember-text-area");
        var newstring = textbox.val().replace(a[0], a[1]);
        textbox.val(newstring);
      }, 500);
    }
  });
});