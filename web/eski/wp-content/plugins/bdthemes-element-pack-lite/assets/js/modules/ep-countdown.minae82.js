!function(e,n){"use strict";var t=function(e,n){var t=e.find(".bdt-countdown-wrapper");if(t.length){var o=t.data("settings"),d=o.endTime,r=o.loopHours,i=o.isLogged,a=function(e,n,t){var o="";if(t){var d=new Date;d.setTime(d.getTime()+60*t*60*1e3),o="; expires="+d.toUTCString()}document.cookie=e+"="+(n||"")+o+"; path=/"},c=function(e){for(var n=e+"=",t=document.cookie.split(";"),o=0;o<t.length;o++){for(var d=t[o];" "==d.charAt(0);)d=d.substring(1,d.length);if(0==d.indexOf(n))return d.substring(n.length,d.length)}return null},u=function(e,n){return Math.floor(Math.random()*(n-e+1)+e)},s=function(e){var n=e-Date.now();return{total:n,seconds:n/1e3%60,minutes:n/1e3/60%60,hours:n/1e3/60/60%24,days:n/1e3/60/60/24}},l=function(e){jQuery.ajax({url:o.adminAjaxUrl,type:"post",data:{action:"element_pack_countdown_end",endTime:e,couponTrickyId:o.couponTrickyId},success:function(e){"ended"==e&&("message"==o.endActionType&&(jQuery(o.msgId).css({display:"block"}),jQuery(o.id+"-timer").css({display:"none"})),"url"==o.endActionType&&setInterval((function(){jQuery(location).attr("href",o.redirectUrl)}),o.redirectDelay))},error:function(){console.log("Error")}})},f=function(){jQuery.ajax({url:o.adminAjaxUrl,type:"post",data:{action:"element_pack_countdown_end",endTime:d,couponTrickyId:o.couponTrickyId},success:function(e){},error:function(){}})},m=function(){jQuery.ajax({url:o.adminAjaxUrl,type:"post",data:{action:"element_pack_countdown_end",endTime:d,couponTrickyId:o.couponTrickyId},success:function(e){"ended"==e&&setTimeout((function(){document.getElementById(o.triggerId).click()}),1500)},error:function(){}})},p=function(e){clearInterval(e)};if(0==r){var y=bdtUIkit.countdown(n(o.id+"-timer"),{date:o.finalTime}),v=setInterval((function(){var e=s(y.date).seconds.toFixed(0);parseInt(e)<0&&(jQuery("body").hasClass("elementor-editor-active")||(jQuery(o.id+"-msg").css({display:"none"}),"none"!=o.endActionType&&l(d)),p(v))}),1e3);if("coupon-code"==o.endActionType)var T=setInterval((function(){var e=s(y.date).seconds.toFixed(0);parseInt(e)<0&&(jQuery("body").hasClass("elementor-editor-active")||"coupon-code"==o.endActionType&&f(d),p(T))}),1e3);if(!1!==o.triggerId)T=setInterval((function(){var e=s(y.date).seconds.toFixed(0);parseInt(e)<0&&(jQuery("body").hasClass("elementor-editor-active")||m(),p(T))}),1e3)}if(!1!==r){var w,I=new Date,g=u(6,14),b=60*r*60*1e3-60*g*1e3,j=new Date(I.getTime()+b).toISOString(),k=c("bdtCountdownLoopTime");null!=k&&"undefined"!=k||!1!==i||a("bdtCountdownLoopTime",j,r),w=!1===i?c("bdtCountdownLoopTime"):j,n(o.id+"-timer").attr("data-bdt-countdown","date: "+w);var h=(y=bdtUIkit.countdown(n(o.id+"-timer"),{date:w})).date;setInterval((function(){var e=s(h).seconds.toFixed(0);parseInt(e)>0&&(null!=k&&"undefined"!=k||!1!==i||(a("bdtCountdownLoopTime",j,r),bdtUIkit.countdown(n(o.id+"-timer"),{date:w})))}),1e3)}}};jQuery(window).on("elementor/frontend/init",(function(){elementorFrontend.hooks.addAction("frontend/element_ready/bdt-countdown.default",t),elementorFrontend.hooks.addAction("frontend/element_ready/bdt-countdown.bdt-tiny-countdown",t)}))}(jQuery,window.elementorFrontend);