!function(a,b,c){!c&&(a.rd=c={}),["log","warn","error","debug","info"].forEach(function(b){c[b]=function(){return a.console&&b in console&&Function.apply.call(console[b],console,arguments)}})}(window,document,window.rd),function(a,b,c){function d(a,c,d){if(arguments.length>1&&"[object Object]"!==String(c)){if(!d&&(d={}),(null===c||void 0===c)&&(d.expires=-1),!isNaN(parseInt(d.expires))&&0!==d.expires){var e=d.expires,f=d.expires=new Date;e>1e3?f.setTime(e):f.setDate(f.getDate()+e)}return c=String(c),b.cookie=[encodeURIComponent(a),"=",d.raw?c:encodeURIComponent(c),d.expires?"; expires="+d.expires.toUTCString():"",d.path?"; path="+d.path:"",d.domain?"; domain="+d.domain:"",d.secure?"; secure":""].join("")}d=c||{};var g,h=d.raw?function(a){return a}:decodeURIComponent;return(g=new RegExp("(?:^|; )"+encodeURIComponent(a)+"=([^;]*)").exec(b.cookie))?h(g[1]):null}!c&&(a.rd=c={}),c.cookie=c.cookie=d}(window,document,window.rd),function(a,b,c){function d(){if(!a.localStorage||!a.sessionStorage)return!1;var b="RD.DB.TEST."+Math.random().toString().substr(2);try{sessionStorage[b]="1",sessionStorage.removeItem(b)}catch(c){return!1}return!0}function e(){return 1==arguments.length?"string"==typeof arguments[0]?g(arguments[0]):f(arguments[0]):arguments.length>1?h.apply(this,Array.prototype.slice.call(arguments)):!1}function f(a){j=c.extend({},j,a)}function g(a){var b;if(a=j.prefix+a,k){try{b=JSON.parse(localStorage[a]||sessionStorage[a])}catch(d){}return b&&"d"in b?b.t>0&&b.t<(new Date).getTime()?(localStorage.removeItem(a),sessionStorage.removeItem(a),null):b.d:null}try{b=JSON.parse(c.cookie(a))}catch(d){}return b||null}function h(a,b,d){var e=(new Date).getTime();if(a=j.prefix+a,d=parseInt(d),d=d>0?e>d?d+e:d:0,k){if(null===b||void 0===b)return localStorage.removeItem(a),sessionStorage.removeItem(a),!0;var f={d:b,t:d},g=d?localStorage:sessionStorage;return localStorage.removeItem(a),g[a]=JSON.stringify(f),setTimeout(i,10),!0}return c.cookie(a,JSON.stringify(b),{expires:d})?!0:!1}function i(){if(!j.gc||!j.gc_key||!k)return!1;var a=localStorage[j.gc_key]=parseInt(localStorage[j.gc_key])+1;if(a<j.gc)return!1;localStorage[j.gc_key]=0;var b=(new Date).getTime();for(var c in localStorage){var d=null;if(!j.prefix||0===c.indexOf(j.prefix)){try{d=JSON.parse(localStorage[c])}catch(e){}d&&d.t&&d.t<b&&localStorage.removeItem(c)}}}!c&&(a.rd=c={});var j={prefix:"",gc:2,gc_key:"_gc"},k=d();c.db=e}(window,document,window.rd),function(a,b,c,d,e,f){function g(a,b,c){var d;return function(){var e=this,f=arguments,g=function(){d=null,c||a.apply(e,f)},h=c&&!d;clearTimeout(d),d=setTimeout(g,b),h&&a.apply(e,f)}}function h(a,b,c){b||(b=250);var d,e;return function(){var f=c||this,g=+new Date,h=arguments;d&&d+b>g?(clearTimeout(e),e=setTimeout(function(){d=g,a.apply(f,h)},b)):(d=g,a.apply(f,h))}}function i(a,c){if(a in i){if(b.isFunction(i[a]))return i[a].call(this,c);if(b.isPlainObject(c))return b.extend(i[a],c)}return i[a]=c}function j(a){var c=!!rd.db("ineligible"),d=k();return d?void(c?(a&&b("#thanks h2").html("You have already entered today."),b(".frame").hide(),b("#thanks").show(),b(".carousel, .winners, .see_all_prizes").hide()):(b(".frame").hide(),b("#prize").show())):(b(".frame").hide(),b("#signup").show(),void b("#login_email").trigger("focus"))}function k(){return 1==rd.db("lis")}function l(){b.ajax({type:"POST",url:"/api/eligible",dataType:"json"}).done(function(a){return a?(w.show(),rd.db("lis",1,y),void rd.db("ineligible",!a.eligible,1e3*a.midnight)):m()})}function m(a){return b.ajax({type:"POST",url:"/api/logout",dataType:"json"}).done(function(){w.hide(),rd.db("lis",null),rd.db("name",null),rd.db("ineligible",null),rd.cookie("sid",null),u=null,v.html("")}),!1}function n(){function a(a){return a.err?c(a.msg):void x.html("Verification email sent.")}function c(){x.html("Failed to send, please try again later.")}return b.ajax({type:"POST",url:"/api/verify",dataType:"json"}).done(a).fail(c),!1}function o(c){function d(a){function c(){o.initializing=!1,o.initialized=!0}function d(a,b,c){o.initializing=!1,rd.error("GTM failed to load:",c)}return a?void(o.initializing||(o.initializing=!0,b.ajax({url:"//www.googletagmanager.com/gtm.js?id="+a,dataType:"script",cache:!0}).done(c).fail(d))):void rd.error('jds.gtm() called without an "id" param')}if(!o.initialized&&"string"===b.type(c))return d(c);var e=a.s,f=function(a){var b={};for(var c in a){var d=a[c];e[d]&&(b[c]=e[d])}return b}({"Page Name":"pageName",Channel:"channel",Category:"prop1",Subcategory:"prop2",Application:"prop5","Content ID":"prop9","Search Term":"eVar5","Registration Time":"eVar18","Internal Campaign":"eVar8","Email Campaign":"eVar15","Party ID":"eVar26","Social Campaign":"eVar35","Slideshows and Quizzes":"prop6","Sponsor Name":"eVar44","Registration Source":"eVar6","Profile ID":"eVar32","Hash ID":"eVar68","Status Code":"prop12",Story:"prop3","Newsletter Signup Source":"eVar27","Member Logged In":"eVar24","Content Type":"eVar29","Commerce Enabled?":"eVar36","External Campaign":"campaign","Search Filters":"prop67","Search Results Number":"prop68","Video Playlist ID":"eVar41","Video Player Name":"eVar40"});if(f.event="pageview",B.push(f),e.events&&e.events.match(/scRemove/)){var g={event:"Registrations","Registration Source":f["Registration Source"]};e.events.match(/event23/)&&(g["Marketing Opt Ins"]=1),B.push(g)}e.events&&e.events.match(/scAdd/)&&e.products&&e.products.split(",").forEach(function(a){B.push({event:"Newsletter Signup","Newsletter ID":a.replace(/^Newsletter;/,"")})})}function p(c){function d(){var c=a.ACPuzzle;return c?(c.create(g,"solvemedia_widget",{}),b("#solvemedia form").on("submit",function(a){a.preventDefault(),u=!0,b("#prize_form").submit(),b("#solvemedia").hide()}),b("#solvemedia .close").on("click",f),b(document).on("keyup",e),b("#solvemedia").show(),!1):(u=!0,rd.error("SolveMedia.fire executed, but ACPuzzle not ready :("),!1)}function e(a){27==a.keyCode&&f()}function f(){b(document).off("keyup",e),b("#solvemedia").hide()}if(!c||!c.key)return rd.warn("Could not initialize solvemedia roadblock. No key provided."),!1;var g=c&&c.key,h="http://api.solvemedia.com/papi/challenge.ajax?k="+g;b.ajax({url:h,dataType:"script",cache:!0,success:function(){},error:function(){rd.error("Failed to load SolveMedia JS: "+h)}}),p.fire=d}function q(){function a(){var a=u.position(),b=a&&a.left+p.scrollLeft();k=u.width(),g=b+k,i=o.width(),j=g-i+0,l=Math.round(i/k)}function c(){var a=p.scrollLeft()-.63*i;0>=a?(a=0,o.addClass(m),o.removeClass(n)):o.removeClass(m),p.animate({scrollLeft:a},400)}function d(){var a=p.scrollLeft()+.63*i;a>=j?(a=j+1,o.addClass(n),o.removeClass(m)):o.removeClass(n),p.animate({scrollLeft:a},400)}function e(){var a=p.scrollLeft();a>=j?o.addClass(n):0>=a?o.addClass(m):o.removeClass(m).removeClass(n);for(var c=Math.round(a/k),d=1>=c?0:c-1,e=d+l+2,f=d;e>=f;f++){var g=b(t[f]);if(g.length&&!g.data("loaded")){var h=g.find("img");h.attr("src",h.data("src")).data("src",!1).removeClass("ajax-loader"),g.addClass("loaded").data("loaded",!0)}}}function f(){a()}var g,i,j,k,l,m="flush_left",n="flush_right",o=b(".carousel"),p=o.find(".wrap"),q=o.find(".prev"),r=o.find(".next"),s=o.find(".today"),t=p.children(),u=b(t[t.length-1]);if(a(),o.length){var v,w=s.position();v=w?w.left+k/2-i/2:0,p.scrollLeft(v),e(),q.on("click",h(c,450)),r.on("click",h(d,450)),p.on("scroll",h(e,200)),b("window").on("resize",f)}}function r(){function a(a){a=parseInt(a),f.attr("src",i[k[a-1]]),h.html(j[k[a-1]]),e.removeClass("cur"+m).addClass("cur"+a),m=a}function c(b){b.preventDefault(),b.stopPropagation(),a(1>=m?l:m-1)}function d(b){b.preventDefault(),b.stopPropagation(),a(m>=l?1:m+1)}var e=b(".prize"),f=e.find("img"),h=b(e.find("p")[0]),i={1:f.attr("src")},j={1:h.html()},k=[1],l=1,m=1;if(e.length){var n;(n=f.data("img2"))&&(i[2]=n),(n=f.data("img3"))&&(i[3]=n),(n=h.data("desc2"))&&(j[2]=n),(n=h.data("desc3"))&&(j[3]=n);for(var o=2;3>=o;o++)(i[o]||j[o])&&(k.push(o),i[o]||(i[o]=i[o-1]),j[o]||(j[o]=j[o-1]));if(1!=(l=k.length)){e.addClass("slideshow").append(b('<b class="prev">').on("selectstart touchstart",g(c,100,!0))).append(b('<b class="next">').on("selectstart touchstart",g(d,100,!0)));for(var p=b('<div class="dots">'),o=1;l>=o;o++)p.append(b("<i>").data("cur",o));e.addClass("cur1").append(p),p.on("touchstart selectstart","i",g(function(){a(b(this).data("cur"))},100,!0))}}}function s(a){function c(){j.removeClass("on"),i.attr("disabled",null)}function d(a){return c(),!a||"err"in a?void k.html(a.msg||z).show():f(a)}function e(){c(),k.html(z).show()}a.preventDefault();var f=a.data&&a.data.success||function(){},g=a.data&&a.data.prereq||function(){return!0},h=s.$form=b(this),i=s.$submit=h.find('input[type="submit"]').attr("disabled","disabled"),j=h.find(".ajax-loader").addClass("on"),k=s.$alert=h.find(".alert").empty().hide();return g(a)?(b.ajax({type:"POST",url:h.attr("action"),data:h.serialize(),dataType:"json"}).done(d).fail(e),!1):(c(),!1)}function t(){b("#login_form").on("submit",{success:function(a){return u=null,rd.db("lis",1,y),rd.db("name",a.name,y),v.html(a.name),w.show(),a.eligible?j():(rd.db("ineligible",!0,1e3*a.midnight),j(!0)),!1}},s),b("#signup_form").on("submit",{success:function(a){return u=null,rd.db("lis",1,y),a.name&&rd.db("name",a.name,y),v.html(rd.db("name")),w.show(),s.$form.trigger("reset"),s.$form.hasClass("profile")?window.location.href=window.location.href:j(),!1}},s),b("#forgot_form").on("submit",{success:function(a){return s.$alert.show().html(a.msg),s.$form.trigger("reset"),s.$form.find("fieldset.login").hide(),s.$form.find("p").hide(),s.$submit.hide(),s.$form.find(".forgot_close").html("Dismiss"),!1}},s),b("#reset_form").on("submit",{success:function(a){return s.$alert.show().html(a.msg),s.$form.trigger("reset"),s.$form.find("fieldset, input").hide(),s.$form.find(".success").show(),!1}},s),b("#prize_form").on("submit",{prereq:function(){return k()?rd.db("ineligible")?(j(),!1):u?!0:(i.roadblock&&i.roadblock(),!1):(j(),!1)},success:function(a){u=null,rd.db("ineligible",!0,1e3*a.midnight),j()}},s),w=b(".account"),v=b(w.find("a")[0]),v.html(rd.db("name")),b(".logout").on("click",m),b("a.forgot").on("click",function(){b("#signup").addClass("forgot"),b("#login_form").find(".alert").empty(),b("#forgot_email").trigger("focus")}),b("a.forgot_close").on("click",function(){b("#signup").removeClass("forgot");var a=b("#forgot_form");a.trigger("reset"),a.find("fieldset, input").show(),a.find("p").show(),a.find(".success").hide(),a.find(".alert").empty(),b("#login_email").trigger("focus")}),x=b(".verify"),x.find("a").on("click",n),q(),r(),i.roadblock=p.fire,k()&&(w.show(),rd.db("ineligible")||l())}var u,v,w,x,y=31536e6,z="An unexpected error has occurred. Please try again.",A="dataLayer",B=a[A];a[A]||(B=a[A]=[{"gtm.start":(new Date).getTime(),event:"gtm.js"}]),i.gtm=o,i.solvemedia=p,f=0,(d=function(){return a[b]?(e&&clearInterval(e),b=a[b],a[c]&&a[c].q&&a[c].q.forEach(function(b){i.apply(a,b)}),a[c]=i,void b(t)):void(!f++&&(e=setInterval(d,50)))})()}(window,"jQuery","jds");