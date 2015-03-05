(function(e,t,n){var r=undefined,i=31536e6,s="An unexpected error has occurred. Please try again.",o="focus",u="blur",a="keyup",f="scroll",l="resize",c="submit",h="reset",p="click",d="selectstart touchstart",v=p+" "+d,m=1,g=2,y=3,b=4,w=5,E=6,S=7,x=8,T=9,N=!1,C,k,L,A,O,M,_,D,P,H,B,j,F,I,q;C=function(e,t,n,r){var i;return function(){var s=this,o=arguments,u=function(){i=null,n||e.apply(s,o)},a=n&&!i;clearTimeout(i),i=setTimeout(u,t),a?e.apply(s,o):r&&r.call(i,t)}},k=function(e){var t=[];return function(n,r){if(arguments.length===0)return t.forEach(function(t){return t[0].call(t[1],e)});t.push([n,r||this])}},L={enterAnonymous:k("enterAnonymous"),signup:k("signup"),signupFail:k("signupFail"),login:k("login"),loginFail:k("loginFail"),enter:k("enter"),enterDuplicate:k("enterDuplicate"),enterFail:k("enterFail"),forgot:k("forgot"),reset:k("reset"),verify:k("verify"),verifyRequest:k("verifyRequest"),profileUpdate:k("profileUpdate"),logout:k("logout"),slideshow:k("slideshow")},A=function(e,t,n){if(arguments.length>1&&String(t)!=="[object Object]"){!n&&(n={});if(t===null||t===undefined)n.expires=-1;if(!isNaN(parseInt(n.expires))&&n.expires!==0){var r=n.expires,i=n.expires=new Date;r>1e3?i.setTime(r):i.setDate(i.getDate()+r)}return t=String(t),document.cookie=[encodeURIComponent(e),"=",n.raw?t:encodeURIComponent(t),n.expires?"; expires="+n.expires.toUTCString():"",n.path?"; path="+n.path:"",n.domain?"; domain="+n.domain:"",n.secure?"; secure":""].join("")}n=t||{};var s,o=n.raw?function(e){return e}:decodeURIComponent;return(s=(new RegExp("(?:^|; )"+encodeURIComponent(e)+"=([^;]*)")).exec(document.cookie))?o(s[1]):null},O=function(){function a(e){var n;e=t+e;if(u){try{n=JSON.parse(i[e]||s[e])}catch(r){}return!!n&&"d"in n?n.t>0&&n.t<(new Date).getTime()?(i[o](e),s[o](e),null):n.d:null}try{n=JSON.parse(A(e))}catch(r){}return n||null}function f(e,r,a){var f=(new Date).getTime();e=t+e,a=parseInt(a),a=a>0?a<f?a+f:a:0;if(u){if(r===null||r===undefined)return i[o](e),s[o](e),!0;var l={d:r,t:a},c=a?i:s;return i[o](e),c[e]=JSON.stringify(l),setTimeout(n,10),!0}return A(e,JSON.stringify(r),{expires:a})?!0:!1}function n(){if(!n||!r||!u)return!1;var e=i[r]=parseInt(i[r])+1;if(e<n)return!1;i[r]=0;var s=(new Date).getTime();for(var a in i){var f=null;if(!t||a.indexOf(t)===0){try{f=JSON.parse(i[a])}catch(l){}f&&f.t&&f.t<s&&i[o](a)}}}var t="",n=2,r="_gc",i=e.localStorage,s=e.sessionStorage,o="removeItem",u=function(){if(!i||!s)return!1;var e="TEST"+Math.random().toString().substr(2);try{s[e]="1",s[o](e)}catch(t){return!1}return!0};return function(t,n){var r=arguments;return r.length==1?typeof r[0]=="string"?a(r[0]):!1:r.length>1?f.apply(this,Array.prototype.slice.call(r)):!1}}(),M=function(e,t,n,r){t||(t=250);var i,s;return function(){var o=n||this,u=+(new Date),a=arguments;i&&u<i+t?(r&&r.call(o,s,t),clearTimeout(s),s=setTimeout(function(){i=u,e.apply(o,a)},t)):(i=u,e.apply(o,a))}},_=function(){function t(t,n){e.push([t,n||this])}var e=[];return t.fire=function(){return e.forEach(function(e){return e[0].call(e[1])})},t}(),D=function(){function f(t){t=parseInt(t),n.attr("src",i[o[t-1]]),r.html(s[o[t-1]]),e.removeClass("cur"+a).addClass("cur"+t),a=t,L.slideshow()}function l(e){e.preventDefault(),e.stopPropagation(),f(a<=1?u:a-1)}function c(e){e.preventDefault(),e.stopPropagation(),f(a>=u?1:a+1)}var e,n,r,i={},s={},o=[1],u=1,a=1;_(function(){e=t(".prize");if(!e.length)return;n=e.find("img"),r=t(e.find("p")[0]),i[1]=n.attr("src"),s[1]=r.html();var a;if(a=n.data("img2"))i[2]=a;if(a=n.data("img3"))i[3]=a;if(a=r.data("desc2"))s[2]=a;if(a=r.data("desc3"))s[3]=a;for(var h=2;h<=3;h++)if(i[h]||s[h])o.push(h),i[h]||(i[h]=i[h-1]),s[h]||(s[h]=s[h-1]);if((u=o.length)==1)return;e.addClass("slideshow").append(t('<b class="prev">').on(d,C(l,100,!0))).append(t('<b class="next">').on(d,C(c,100,!0)));var p=t('<div class="dots">');for(var h=1;h<=u;h++)p.append(t("<i>").data("cur",h));e.addClass("cur1").append(p),p.on(d,"i",C(function(){f(t(this).data("cur"))},100,!0))})}(),P=function(){function b(){var e=h.position(),t=e&&e.left+s.scrollLeft();g=h.width(),p=t+g,v=i.width(),m=p-v+0,y=Math.round(v/g)}function w(e){var t=s.scrollLeft()-v*.63;t<=0?(t=0,i.addClass(n),i.removeClass(r)):i.removeClass(n),s.animate({scrollLeft:t},400),L.slideshow()}function E(e){var t=s.scrollLeft()+v*.63;t>=m?(t=m+1,i.addClass(r),i.removeClass(n)):i.removeClass(r),s.animate({scrollLeft:t},400),L.slideshow()}function S(e){var o=s.scrollLeft();o>=m?i.addClass(r):o<=0?i.addClass(n):i.removeClass(n).removeClass(r);var u=Math.round(o/g),a=u<=1?0:u-1,f=a+y;for(var l=a;l<=f;l++){var h=t(c[l]);if(h.length&&!h.data("loaded")){var p=h.find("img");p.attr("src",p.data("src")).data("src",!1).removeClass("loader"),h.addClass("loaded").data("loaded",!0)}}}function x(e){b()}var n="flush_left",r="flush_right",i,s,o,u,a,c,h,p,v,m,g,y;_(function(){i=t(".carousel");if(!i.length)return;s=i.find(".wrap"),o=i.find(".prev"),u=i.find(".next"),a=i.find(".today"),c=s.children(),h=t(c[c.length-1]),b();var n=a.position(),r=n?n.left+g/2-v/2:0;s.scrollLeft(r),S(),o.on(d,M(w,450)),u.on(d,M(E,450)),s.on(f,M(S,32)),t(e).on(l,x)})}(),H=function(){function e(n){function d(){h.removeClass("on"),f.removeClass("loading"),l.attr("disabled",null)}function v(e,n,a){d(),t(".logo").trigger(o).trigger(u);if(!e||!1 in e||e.status!==m){switch(e.status){case y:logout();break;case T:I.roadblock(function(){f.trigger(c)})}return p.html(e.message||s).show(),i(e)}return r(e)}function g(e,n,r){return d(),t(".logo").trigger(o),p.html(s).show(),i()}n.preventDefault();var r=n.data&&n.data.success||function(){},i=n.data&&n.data.fail||function(){},a=n.data&&n.data.prereq||function(){return!0},f=e.$form=t(this).addClass("loading"),l=e.$submit=f.find('input[type="submit"]').attr("disabled","disabled"),h=f.find("loader").addClass("on"),p=e.$alert=f.find(".alert").empty().hide();return a(n)?(t.ajax({type:"POST",url:f.attr("action"),data:f.serialize(),dataType:"json"}).done(v).fail(g),!1):(d(),!1)}return e}(),B=function(){function o(e){return d.callback=e,i?(t(document).on(a,u),s.show(),f(),!1):(r=!0,console.error("SolveMedia.fire executed, but ACPuzzle not ready :("),!1)}function u(e){return e.which==27?(l(),!1):!0}function f(){i.focus_response_field()}function l(){t(document).off("keyup",u),t("#solvemedia").hide()}function h(e){var n=t(e.target);switch(n.attr("id")||n[0].nodeName){case"adcopy-link-refresh":i.reload(),f();break;case"adcopy-link-audio":i.change2audio(),f();break;case"adcopy-link-image":i.change2image(),f();break;case"adcopy-link-info":case"I":i.moreinfo();break;case"solvemedia":case"B":l();break;default:return!0}return!1}function d(o){if(!o)return console.warn("Could not initialize SolveMedia roadblock. No key provided."),!1;s=t("#solvemedia");if(!s.length)return!1;e.ACPuzzleInfo={protocol:e.location.protocol.match(/^https?:$/)?"":"http:",apiserver:"//api.solvemedia.com",mediaserver:"//api.solvemedia.com",magic:"u7WpnzA6KVaLL0XYMDopVg",chalapi:"ajax",chalstamp:1424984044,lang:"en",size:"standard",theme:"custom",type:"img",onload:null},_(function(){t.ajax({url:n,dataType:"script",cache:!0}).fail(function(){console.error("Failed to load SolveMedia JS: "+n)}).done(function(){i=e.ACPuzzle,i.create(o)}),s.on(p,h),s.find("form").on(c,{success:function(e){r=!0,l(),t.type(d.callback)=="function"&&d.callback()},fail:function(e){return i.reload(),!1}},H)})}var n="//api.solvemedia.com/papi/_puzzle.js",i,s;return d.fire=o,d}(),j=function(){function o(e,t){var n={event:e};return t!==!1&&O("user_id")&&(n.userId=O("user_id")),console.debug("ADS_BEING_THOTTLED",!N),n.ad=!N,r.push(n)}function u(e){if(s)return;if(!e){console.error('jds("gtm", ?) called without a "GTM-XXXXXX"');return}s=!0,t.ajax({url:"//www.googletagmanager.com/gtm.js?id="+e,dataType:"script",cache:!0}).done(function(){s=!1,i=!0}).fail(function(e,t,n){s=!1,console.error("GTM failed to load:",n)})}var n="dataLayer",r,i,s;return function(t,s){if(i)return o(t,s);if(typeof t=="string"&&t.indexOf("GTM-")===0)return e[n]||(r=e[n]=[{"gtm.start":+(new Date),event:"gtm.js"}]),u(t)}}(),F=function(){function f(n){t(e).scrollTop(n||0)}function l(e){var n=!!O("ineligible"),r=d();if(!r){t(".frame").hide(),t("#signup").show(),t("#login_email").trigger(o),L.enterAnonymous();return}n?(e&&t("#thanks h2").html("You have already entered today."),t(".frame").hide(),t("#thanks").show(),t(".carousel, #winners, .see_all_prizes").hide()):(t(".frame").hide(),t("#prize").show())}function d(){return O("lis")==1}function v(){t.ajax({type:"POST",url:"/api/eligible",dataType:"json"}).done(function(e){if(!e||!e.status||e.status!=m)return g();u.show(),O("lis",1,i),O("user_id",e.user_id,i),O("ineligible",!e.eligible,e.midnight*1e3)})}function g(){return t.ajax({type:"POST",url:"/api/logout",dataType:"json"}).done(function(e){u.hide(),O("lis",null),O("user_id",null),O("name",null),O("ineligible",null),A("sid",null),r=null,n.html(""),L.logout()}),!1}function y(){function e(e){if(e.err)return n(e.message);a.html("Verification email sent."),L.verifyRequest()}function n(e){a.html(s)}return t.ajax({type:"POST",url:"/api/verify",dataType:"json"}).done(e).fail(n),!1}function b(e){q.refreshAds(),console.debug("ADS_BEING_THOTTLED: ",N);var t=!j(e);console.debug("GTM "+e+": "+(t?"successfully matched":"FAILED to match against")+" a GTM event")}var n,u,a;for(var w in L)L[w](b);_(function(){t("#login_form").on(c,{success:function(e){return f(0),r=null,O("lis",1,i),O("user_id",e.user_id,i),O("name",e.name,i),n.html(e.name),u.show(),L.login(),e.eligible?l():(O("ineligible",!0,e.midnight*1e3),l(!0),L.enterDuplicate()),!1},fail:function(){L.loginFail()}},H),t("#signup_form").on(c,{success:function(t){return f(0),r=null,O("lis",1,i),O("user_id",t.user_id,i),t.name&&O("name",t.name,i),n.html(O("name")),u.show(),H.$form.trigger(h),H.$form.hasClass("profile")?(L.profileUpdate(),e.location.href=e.location.href):(L.signup(),l()),!1},fail:function(){L.signupFail()}},H),t("#forgot_form").on(c,{success:function(e){return H.$alert.show().html(e.message),H.$form.trigger(h),H.$form.find("fieldset.login").hide(),H.$form.find("p").hide(),H.$submit.hide(),H.$form.find(".forgot_close").html("Dismiss"),L.forgot(),!1}},H),t("#reset_form").on(c,{success:function(e){return f(0),H.$alert.show().html(e.message),H.$form.trigger(h),H.$form.find("fieldset, input").hide(),H.$form.find(".success").show(),L.reset(),!1}},H),t("#prize_form").on(c,{prereq:function(){return d()?O("ineligible")?(l(!0),L.enterDuplicate(),!1):!0:(l(),!1)},success:function(e){f(0),r=null,O("ineligible",!0,e.midnight*1e3),l(),L.enter()}},H),u=t(".account"),n=t(u.find("a")[0]),n.html(O("name")),t(".logout").on(p,g),t("a.forgot").on(p,function(){t("#signup").addClass("forgot"),t("#login_form").find(".alert").empty(),t("#forgot_email").trigger(o)}),t("a.forgot_close").on(p,function(){t("#signup").removeClass("forgot");var e=t("#forgot_form");e.trigger(h),e.find("fieldset, input").show(),e.find("p").show(),e.find(".success").hide(),e.find(".alert").empty(),t("#login_email").trigger(o)}),a=t(".verify"),a.find("a").on(p,y),I.roadblock=B.fire,I.gtm=j,d()&&(u.show(),O("ineligible")||v())})}(),I=function(){function e(){var n=[],r;return arguments.length?(Array.prototype.push.apply(n,arguments),r=n.shift(),r&&r in e&&t.isFunction(e[r])?e[r].apply(this,n):!1):!1}return e.solvemedia=B,e}(),q=function(){function i(){}function s(){o(),u("//cdn.yldbt.com/js/yieldbot.intent.js",a,f)}function o(){t(".ad").each(function(e,n){var r=t(n);if(!r.data("id"))return;r.empty().append(t("<div>").attr("id",r.data("id")))})}function u(e,n,r){t.ajax({url:e,dataType:"script",cache:!0}).done(n).fail(r)}function a(){var n=e.yieldbot;n.pub("d45f"),n.defineSlot("LB"),n.defineSlot("MR"),n.enableAsync(),n.go(),console.log("Yieldbot loaded"),e.OX_ads=[{slot_id:"537278266_728x90_ATF",auid:"537278266",vars:n.getSlotCriteria("LB")},{slot_id:"537278268_300x250_ATF",auid:"537278268",vars:n.getSlotCriteria("MR")},{slot_id:"537278269_300x250_BTF",auid:"537278269"},{slot_id:"537278267_728x90_BTF",auid:"537278267"}],t.extend(!0,r,e.OX_ads),u("//ox-d.junemedia.com/w/1.0/jstag",l,c)}function f(){console.error("Yieldbot failed to load")}function l(){console.log("OpenX JavaScript Loaded")}function c(){console.error("OpenX JavaScript failed to load")}function h(){N=!1,console.debug("Ad refresh at "+new Date),o(),r.forEach(function(e){OX.load(e)})}var n=4e3,r=[],p=i.refreshAds=C(function(){setTimeout(h,1e3)},n,!0,function(){N=!0});return _(function(){t("body>header .menu").on("click selectstart touchstart",C(function(e){e.preventDefault(),t(this).closest("header").toggleClass("open")},100,!0)),s()}),e.BR=i,i}();var R,U=0;(function z(){if(!e[t]){U++||(R=setInterval(z,16));return}R&&clearInterval(R),t=e[t],e[n]&&e[n].q&&e[n].q.forEach(function(t){I.apply(e,t)}),e[n]=I,t(_.fire)})()})(window,"jQuery","jds");