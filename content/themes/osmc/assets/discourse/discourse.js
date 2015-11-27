var ad = true;

var bannertext = "Get 10% off at the OSMC Store today using coupon 'autumnsavings'.";
var bannerlink = "https://osmc.tv/shop";
var bannerimg = "https://osmc.tv/assets/discourse/banner.png";

var autoLinks = [
["!logs", "To get a better understanding of the problem you are experiencing we need more information from you. Please see http://osmc.tv/wiki/general/how-to-submit-a-useful-support-request/ for advice on how to help us."],
["!osmcwiki", "Your question is explained in the OSMC Wiki. Have a look at https://osmc.tv/wiki. This should be your first port of call for OSMC help. If you feel something is missing, feel free to edit it or tell us how we can improve it."],
["!kodiwiki", "Your question is explained in the Kodi Wiki. Have a look at http://kodi.wiki. This should be your first port of call for Kodi related questions"],
["!piracy", "We have removed your thread from public viewing because it mentions a pirate addon. We cannot provide support for these. To get a better idea of our piracy policy, please see this link here https://discourse.osmc.tv/faq#piracy" + "\n\nThanks for your understanding"],
["!mediainfo", "It would be good if we could find out more about the file you are having issues playing. Please see this Wiki resource for further help https://osmc.tv/wiki/general/how-to-get-mediainfo-output"],
["!againlogs", "As requested previously, we need logs to get a better understanding of the problem. Please see http://osmc.tv/wiki/general/how-to-submit-a-useful-support-request/"],
["!threadjack", "Please start your own thread outlining your symptoms fully and providing full logs. Please see http://osmc.tv/wiki/general/how-to-submit-a-useful-support-request/. Your issue may not necessarily be related to the original poster's, and to ensure that both they and you are able to get their issue resolved, we would like you to start a new thread for clarity"],
["!access", "Details how to access OSMC via command line locally or via ssh can be found here: https://osmc.tv/wiki/general/accessing-the-command-line/"],
["!userpass", "Details about standard username/password can be found here: https://discourse.osmc.tv/t/usernames-and-passwords/"],
["!cheatsheet", "https://discourse.osmc.tv/t/cheatsheets-and-tutorials-for-users-new-to-linux-based-operating-systems/5980"],
["!addon", "For Addon questions you are much better helped by the Kodi forum http://forum.kodi.tv/forumdisplay.php?fid=27"]
];

!function(e,t){"use strict";!function(t){"function"==typeof define&&define.amd?define(["jquery"],t):t("object"==typeof exports?require("jquery"):e.jQuery||e.Zepto)}(function(n){var o=this||{},a="",i=0,s=0,r=0,l=".adunit",d=!1,c=!1,u="googleAdUnit",g=function(e,t,s){var l;i=0,r=0,a=e,l=n(t),o.shouldCheckForAdBlockers=function(){return s?"function"==typeof s.afterAdBlocked:!1},A(s,l).then(function(){s=f(s),o.dfpOptions=s,n(function(){p(s,l),h(s,l)})})},f=function(o){var a={setTargeting:{},setCategoryExclusion:"",setLocation:"",enableSingleRequest:!0,collapseEmptyDivs:"original",refreshExisting:!0,disablePublisherConsole:!1,disableInitialLoad:!1,noFetch:!1,namespace:t,sizeMapping:{}};if("undefined"==typeof o.setUrlTargeting||o.setUrlTargeting){var i=v(o.url);n.extend(!0,a.setTargeting,{UrlHost:i.Host,UrlPath:i.Path,UrlQuery:i.Query})}return n.extend(!0,a,o),a.googletag&&e.googletag.cmd.push(function(){n.extend(!0,e.googletag,a.googletag)}),a},p=function(t,s){var l=e.googletag;s.each(function(){var e=n(this);i++;var o=y(e,t),s=m(e,o),r=b(e);e.data("existingContent",e.html()),e.html("").addClass("display-none"),l.cmd.push(function(){var i,d=e.data(u);if(d)i=d;else{var c;c=""===a?o:"/"+a+"/"+o,e.data("outofpage")?i=l.defineOutOfPageSlot(c,s):(i=l.defineSlot(c,r,s),e.data("companion")&&(i=i.addService(l.companionAds()))),i=i.addService(l.pubads())}var g=e.data("targeting");g&&n.each(g,function(e,t){i.setTargeting(e,t)});var f=e.data("exclusions");if(f){var p,h=f.split(",");n.each(h,function(e,t){p=n.trim(t),p.length>0&&i.setCategoryExclusion(p)})}var v=e.data("size-mapping");if(v&&t.sizeMapping[v]){var m=l.sizeMapping();n.each(t.sizeMapping[v],function(e,t){m.addSize(t.browser,t.ad_sizes)}),i.defineSizeMapping(m.build())}e.data(u,i),"function"==typeof t.beforeEachAdLoaded&&t.beforeEachAdLoaded.call(this,e)})}),l.cmd.push(function(){var e=l.pubads();t.enableSingleRequest&&e.enableSingleRequest(),n.each(t.setTargeting,function(t,n){e.setTargeting(t,n)});var a=t.setLocation;if("object"==typeof a&&("number"==typeof a.latitude&&"number"==typeof a.longitude&&"number"==typeof a.precision?e.setLocation(a.latitude,a.longitude,a.precision):"number"==typeof a.latitude&&"number"==typeof a.longitude&&e.setLocation(a.latitude,a.longitude)),t.setCategoryExclusion.length>0){var d,c=t.setCategoryExclusion.split(",");n.each(c,function(t,o){d=n.trim(o),d.length>0&&e.setCategoryExclusion(d)})}t.collapseEmptyDivs&&e.collapseEmptyDivs(),t.disablePublisherConsole&&e.disablePublisherConsole(),t.companionAds&&(l.companionAds().setRefreshUnfilledSlots(!0),t.disableInitialLoad||e.enableVideoAds()),t.disableInitialLoad&&e.disableInitialLoad(),t.noFetch&&e.noFetch(),e.addEventListener("slotRenderEnded",function(e){r++;var o=n("#"+e.slot.getSlotId().getDomId()),a=e.isEmpty?"none":"block",l=o.data("existingContent");"none"===a&&n.trim(l).length>0&&"original"===t.collapseEmptyDivs&&(o.show().html(l),a="block display-original"),o.removeClass("display-none").addClass("display-"+a),"function"==typeof t.afterEachAdLoaded&&t.afterEachAdLoaded.call(this,o,e),"function"==typeof t.afterAllAdsLoaded&&r===i&&t.afterAllAdsLoaded.call(this,s)}),o.shouldCheckForAdBlockers()&&!l._adBlocked_&&setTimeout(function(){var a=e.getSlots?e.getSlots():[];a.length>0&&n.get(a[0].getContentUrl()).always(function(e){200!==e.status&&n.each(a,function(){var e=n("#"+this.getSlotId().getDomId());t.afterAdBlocked.call(o,e,this)})})},0),l.enableServices()})},h=function(t,a){var i=e.googletag;if(o.shouldCheckForAdBlockers()&&!i._adBlocked_&&i.getVersion){var s="//partner.googleadservices.com/gpt/pubads_impl_"+i.getVersion()+".js";n.getScript(s).always(function(e){e&&"error"===e.statusText&&n.each(a,function(){t.afterAdBlocked.call(o,n(this))})})}a.each(function(){var e=n(this),a=e.data(u);i._adBlocked_&&o.shouldCheckForAdBlockers()&&t.afterAdBlocked.call(o,e),i.cmd.push(t.refreshExisting&&a&&e.hasClass("display-block")?function(){i.pubads().refresh([a])}:function(){i.display(e.attr("id"))})})},v=function(t){var n=(t||e.location.toString()).match(/^(([^:/?#]+):)?(\/\/([^/?#]*))?([^?#]*)(\?([^#]*))?(#(.*))?/),o=n[4]||"",a=(n[5]||"").replace(/(.)\/$/,"$1"),i=n[7]||"",s=i.replace(/\=/gi,":").split("&");return{Host:o,Path:a,Query:s}},m=function(e,t){return s++,e.attr("id")||e.attr("id",t.replace(/[^A-z0-9]/g,"_")+"-auto-gen-id-"+s).attr("id")},y=function(e,t){var n=e.data("adunit")||t.namespace||e.attr("id")||"";return"function"==typeof t.alterAdUnitName&&(n=t.alterAdUnitName.call(this,n,e)),n},b=function(e){var t=[],o=e.data("dimensions");if(o){var a=o.split(",");n.each(a,function(e,n){var o=n.split("x");t.push([parseInt(o[0],10),parseInt(o[1],10)])})}else t.push([e.width(),e.height()]);return t},A=function(t,a){function i(){o.shouldCheckForAdBlockers()&&n.each(a,function(){t.afterAdBlocked.call(o,n(this))})}if(c=c||n('script[src*="googletagservices.com/tag/js/gpt.js"]').length)return d&&i(),n.Deferred().resolve();var s=n.Deferred();e.googletag=e.googletag||{},e.googletag.cmd=e.googletag.cmd||[];var r=document.createElement("script");r.async=!0,r.type="text/javascript",r.onerror=function(){E(),s.resolve(),d=!0,i()},r.onload=function(){googletag._loadStarted_||(googletag._adBlocked_=!0,i()),s.resolve()};var l="https:"===document.location.protocol;r.src=(l?"https:":"http:")+"//www.googletagservices.com/tag/js/gpt.js";var u=document.getElementsByTagName("script")[0];return u.parentNode.insertBefore(r,u),"none"===r.style.display&&E(),s},E=function(){var t=e.googletag,a=t.cmd,i=function(e,n,o,a){return t.ads.push(o),t.ads[o]={renderEnded:function(){},addService:function(){return this}},t.ads[o]};t={cmd:{push:function(e){e.call(o)}},ads:[],pubads:function(){return this},noFetch:function(){return this},disableInitialLoad:function(){return this},disablePublisherConsole:function(){return this},enableSingleRequest:function(){return this},setTargeting:function(){return this},collapseEmptyDivs:function(){return this},enableServices:function(){return this},defineSlot:function(e,t,n){return i(e,t,n,!1)},defineOutOfPageSlot:function(e,t){return i(e,[],t,!0)},display:function(e){return t.ads[e].renderEnded.call(o),this}},n.each(a,function(e,n){t.cmd.push(n)})};n.dfp=n.fn.dfp=function(e,n){n=n||{},e===t&&(e=a),"object"==typeof e&&(n=e,e=n.dfpID||a);var o=this;return"function"==typeof this&&(o=l),g(e,o,n),this}})}(window);
Discourse.DiscoveryView=Ember.View.extend({_insertBanner:function(){ad?(this.$(".list-container").prepend("<div class='toplist_ad' data-adunit='discourse_toplist' data-size-mapping='toplist'></div>"),this.$(".toplist_ad").dfp({dfpID:"302320750",sizeMapping:{toplist:[{browser:[1100,0],ad_sizes:[970,90]},{browser:[810,0],ad_sizes:[728,90]},{browser:[530,0],ad_sizes:[468,60]},{browser:[0,0],ad_sizes:[320,50]}]}})):(this.$(".list-container").prepend("<a href='"+bannerlink+"'><div class='toplist_banner' style='color:#fafafa; padding:2.5em 1em; margin:0.2em 0;'><h2 style='color:white; font-size:130%; font-weight:300; text-align: center;'>"+bannertext+"</h2></div></a>"),this.$(".toplist_banner").css({background:"url("+bannerimg+")"}))}.on("didInsertElement"),_insertDonateButton:function(){this.$(".list-controls .container").before('<a target="_blank" href="https://osmc.tv/blog/#donate"><button class="btn btn-default donate-button"><i class="fa fa-heart"></i>Donate</button></a>')}.on("didInsertElement")}),autoLinks.forEach(function(t){var e=new RegExp(t[0]);Discourse.Dialect.inlineRegexp({start:t[0],matcher:e,spaceBoundary:!0,emitter:function(){setTimeout(function(){var e=$("textarea.ember-text-area"),n=e.val().replace(t[0],t[1]);e.val(n)},500)}})});