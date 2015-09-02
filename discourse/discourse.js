var ad = true;

var bannertext = "OSMC's July update is here with Kodi 15, click here to learn more";
var bannerlink = "http://bit.ly/1IHiX1F";
var bannerimg = "https://osmc.tv/content/themes/osmc/library/images/forum_banner_back.png";

var autoLinks = [
["!log", "https://discourse.osmc.tv/t/how-to-obtain-provide-necessary-info-for-a-useful-support-request-includes-current-versions/5507"],
["!wiki", "https://osmc.tv/wiki"],
["!piracy", "https://discourse.osmc.tv/faq#piracy"],
["!mediainfo", "https://osmc.tv/wiki/general/how-to-get-mediainfo-output"]
];

!function(e,t){"use strict";!function(t){"function"==typeof define&&define.amd?define(["jquery"],t):t("object"==typeof exports?require("jquery"):e.jQuery||e.Zepto)}(function(n){var i,a=this,o="",s=0,r=0,l=0,d=".adunit",c={},u=!1,g="googleAdUnit",f=function(e,t,a){s=0,l=0,o=e,i=n(t),S(),p(a),n(function(){h(),m()})},p=function(i){if(c={setTargeting:{},setCategoryExclusion:"",setLocation:"",enableSingleRequest:!0,collapseEmptyDivs:"original",refreshExisting:!0,disablePublisherConsole:!1,disableInitialLoad:!1,noFetch:!1,namespace:t,sizeMapping:{}},"undefined"==typeof i.setUrlTargeting||i.setUrlTargeting){var a=y(i.url);n.extend(!0,c.setTargeting,{UrlHost:a.Host,UrlPath:a.Path,UrlQuery:a.Query})}n.extend(!0,c,i),c.googletag&&e.googletag.cmd.push(function(){n.extend(!0,e.googletag,c.googletag)})},h=function(){var t=e.googletag;i.each(function(){var e=n(this);s++;var i=b(e),a=v(e,i),r=E(e);e.data("existingContent",e.html()),e.html("").addClass("display-none"),t.cmd.push(function(){var s,l=e.data(g);l?s=l:(e.data("outofpage")?s=t.defineOutOfPageSlot("/"+o+"/"+i,a):(s=t.defineSlot("/"+o+"/"+i,r,a),e.data("companion")&&(s=s.addService(t.companionAds()))),s=s.addService(t.pubads()));var d=e.data("targeting");d&&n.each(d,function(e,t){s.setTargeting(e,t)});var u=e.data("exclusions");if(u){var f,p=u.split(",");n.each(p,function(e,t){f=n.trim(t),f.length>0&&s.setCategoryExclusion(f)})}var h=e.data("size-mapping");if(h&&c.sizeMapping[h]){var m=t.sizeMapping();n.each(c.sizeMapping[h],function(e,t){m.addSize(t.browser,t.ad_sizes)}),s.defineSizeMapping(m.build())}e.data(g,s),"function"==typeof c.beforeEachAdLoaded&&c.beforeEachAdLoaded.call(this,e)})}),t.cmd.push(function(){var e=t.pubads();c.enableSingleRequest&&e.enableSingleRequest(),n.each(c.setTargeting,function(t,n){e.setTargeting(t,n)});var a=c.setLocation;if("object"==typeof a&&("number"==typeof a.latitude&&"number"==typeof a.longitude&&"number"==typeof a.precision?e.setLocation(a.latitude,a.longitude,a.precision):"number"==typeof a.latitude&&"number"==typeof a.longitude&&e.setLocation(a.latitude,a.longitude)),c.setCategoryExclusion.length>0){var o,r=c.setCategoryExclusion.split(",");n.each(r,function(t,i){o=n.trim(i),o.length>0&&e.setCategoryExclusion(o)})}c.collapseEmptyDivs&&e.collapseEmptyDivs(),c.disablePublisherConsole&&e.disablePublisherConsole(),c.companionAds&&(t.companionAds().setRefreshUnfilledSlots(!0),c.disableInitialLoad||e.enableVideoAds()),c.disableInitialLoad&&e.disableInitialLoad(),c.noFetch&&e.noFetch(),e.addEventListener("slotRenderEnded",function(e){l++;var t=n("#"+e.slot.getSlotId().getDomId()),a=e.isEmpty?"none":"block",o=t.data("existingContent");"none"===a&&n.trim(o).length>0&&"original"===c.collapseEmptyDivs&&(t.show().html(o),a="block display-original"),t.removeClass("display-none").addClass("display-"+a),"function"==typeof c.afterEachAdLoaded&&c.afterEachAdLoaded.call(this,t,e),"function"==typeof c.afterAllAdsLoaded&&l===s&&c.afterAllAdsLoaded.call(this,i)}),t.enableServices()})},m=function(){i.each(function(){var t=n(this),i=t.data(g),a=e.googletag;a.cmd.push(c.refreshExisting&&i&&t.hasClass("display-block")?function(){a.pubads().refresh([i])}:function(){a.display(t.attr("id"))})})},y=function(t){var n=(t||e.location.toString()).match(/^(([^:/?#]+):)?(\/\/([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?/),i=n[4]||"",a=(n[5]||"").replace(/(.)\/$/,"$1"),o=n[7]||"",s=o.replace(/\=/gi,":").split("&");return{Host:i,Path:a,Query:s}},v=function(e,t){return r++,e.attr("id")||e.attr("id",t.replace(/[^A-z0-9]/g,"_")+"-auto-gen-id-"+r).attr("id")},b=function(e){var t=e.data("adunit")||c.namespace||e.attr("id")||"";return"function"==typeof c.alterAdUnitName&&(t=c.alterAdUnitName.call(this,t,e)),t},E=function(e){var t=[],i=e.data("dimensions");if(i){var a=i.split(",");n.each(a,function(e,n){var i=n.split("x");t.push([parseInt(i[0],10),parseInt(i[1],10)])})}else t.push([e.width(),e.height()]);return t},S=function(){if(u=u||n('script[src*="googletagservices.com/tag/js/gpt.js"]').length,!u){e.googletag=e.googletag||{},e.googletag.cmd=e.googletag.cmd||[];var t=document.createElement("script");t.async=!0,t.type="text/javascript",t.onerror=function(){x()};var i="https:"===document.location.protocol;t.src=(i?"https:":"http:")+"//www.googletagservices.com/tag/js/gpt.js";var a=document.getElementsByTagName("script")[0];a.parentNode.insertBefore(t,a),"none"===t.style.display&&x()}},x=function(){var t=e.googletag,i=t.cmd;setTimeout(function(){var e=function(e,n,i,a){return t.ads.push(i),t.ads[i]={renderEnded:function(){},addService:function(){return this}},t.ads[i]};t={cmd:{push:function(e){e.call(a)}},ads:[],pubads:function(){return this},noFetch:function(){return this},disableInitialLoad:function(){return this},disablePublisherConsole:function(){return this},enableSingleRequest:function(){return this},setTargeting:function(){return this},collapseEmptyDivs:function(){return this},enableServices:function(){return this},defineSlot:function(t,n,i){return e(t,n,i,!1)},defineOutOfPageSlot:function(t,n){return e(t,[],n,!0)},display:function(e){return t.ads[e].renderEnded.call(a),this}},n.each(i,function(e,n){t.cmd.push(n)})},50)};n.dfp=n.fn.dfp=function(e,n){n=n||{},e===t&&(e=o),"object"==typeof e&&(n=e,e=n.dfpID||o);var i=this;return"function"==typeof this&&(i=d),f(e,i,n),this}})}(window);
Discourse.DiscoveryView=Ember.View.extend({_insertBanner:function(){ad?(this.$(".list-container").prepend("<div class='toplist_ad' data-adunit='discourse_toplist' data-size-mapping='toplist'></div>"),this.$(".toplist_ad").dfp({dfpID:"302320750",sizeMapping:{toplist:[{browser:[1100,0],ad_sizes:[970,90]},{browser:[810,0],ad_sizes:[728,90]},{browser:[530,0],ad_sizes:[468,60]},{browser:[0,0],ad_sizes:[320,50]}]}})):(this.$(".list-container").prepend("<a href='"+bannerlink+"'><div class='toplist_banner' style='color:#fafafa; padding:2.5em 1em; margin:0.2em 0;'><h2 style='color:white; font-size:130%; font-weight:300; text-align: center;'>"+bannertext+"</h2></div></a>"),this.$(".toplist_banner").css({background:"url("+bannerimg+")"}))}.on("didInsertElement"),_insertDonateButton:function(){this.$(".list-controls .container").before('<a target="_blank" href="https://osmc.tv/contribute/donate/#donate"><button class="btn btn-default donate-button"><i class="fa fa-heart"></i>Donate</button></a>')}.on("didInsertElement")}),autoLinks.forEach(function(t){var e=new RegExp(t[0]);Discourse.Dialect.inlineRegexp({start:t[0],matcher:e,spaceBoundary:!0,emitter:function(){setTimeout(function(){var e=$("textarea.ember-text-area"),n=e.val().replace(t[0],t[1]);e.val(n)},500)}})});