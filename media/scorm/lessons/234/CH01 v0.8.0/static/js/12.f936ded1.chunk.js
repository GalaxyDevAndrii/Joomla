(this["webpackJsonpall-in-one"]=this["webpackJsonpall-in-one"]||[]).push([[12],{817:function(e,t,n){"use strict";n.r(t);var r=n(3),o=n.n(r),i=n(6),a=n(7),c=n(14),u=n(95),l=n(62),s={initialized:!1,apiWrapper:function(){var e={code:"0",string:"No Error",diagnostic:"No Error"},t={code:"101",string:"General Exception",diagnostic:"General Exception"},n=!1,r=null;return{doLMSInitialize:o,doLMSFinish:function(){if(!n)return"true";var e,t=a();if(null==t)return S("Unable to locate the LMS's API Implementation.\nLMSFinish was not successful."),"false";if("true"!==(e=t.LMSFinish("")).toString()){var r=i();S("LMSFinish failed with error code: ".concat(r.code))}return n=!1,e.toString()},doLMSGetValue:function(t){var r=a(),c="";if(null==r)S("Unable to locate the LMS's API Implementation.\nLMSGetValue was not successful.");else if(n||o()){c=r.LMSGetValue(t);var u=i();u.code!==e.code&&(S("LMSGetValue(".concat(t,") failed. \n        ").concat(u.code,": ").concat(u.string)),c="")}else{var l=i();S("LMSGetValue failed - Could not initialize communication with the LMS - error code: ".concat(l.code))}return c.toString()},doLMSSetValue:function(e,t){var r,c=a(),u="false";null==c?S("Unable to locate the LMS's API Implementation.\nLMSSetValue was not successful."):n||o()?"true"!==(u=c.LMSSetValue(e,t)).toString()&&(r=i(),S("LMSSetValue(".concat(e,", ").concat(t,") failed. \n          ").concat(r.code,": ").concat(r.string))):S("LMSSetValue failed - Could not initialize communication with the LMS - error code: "+(r=i()).code);return u.toString()},doLMSCommit:function(){var e,t=a(),r="false";null==t?S("Unable to locate the LMS's API Implementation.\nLMSCommit was not successful."):n||o()?"true"!==(r=t.LMSCommit(""))&&(e=i(),S("LMSCommit failed - error code: ".concat(e.code))):(e=i(),S("LMSCommit failed - Could not initialize communication with the LMS - error code: ".concat(e.code)));return r.toString()}};function o(){if(n)return"true";var e=a();if(null==e)return S("Unable to locate the LMS's API Implementation.\nLMSInitialize was not successful."),"false";var t=e.LMSInitialize("");if("true"!==t.toString()){var r=i();S("LMSInitialize failed with error code: ".concat(r.code))}else n=!0;return t.toString()}function i(){var n={code:e.code,string:e.string,diagnostic:e.diagnostic},r=a();return null==r?(S("Unable to locate the LMS's API Implementation.\nCannot determine LMS error code."),n.code=t.code,n.string=t.string,n.diagnostic="Unable to locate the LMS's API Implementation. Cannot determine LMS error code.",n):(n.code=r.LMSGetLastError().toString(),n.code!==e.code&&(n.string=r.LMSGetErrorString(n.code),n.diagnostic=r.LMSGetDiagnostic("")),n)}function a(){return null==r&&(r=function(){var e=c(window);null==e&&null!=window.opener&&"undefined"!=typeof window.opener&&(e=c(window.opener));return e}()),r}function c(e){for(var t=0;null==e.API&&null!=e.parent&&e.parent!==e;){if(++t>7)return S("Error finding API -- too deeply nested."),null;e=e.parent}return e.API}}(),startTime:null},d=[console.log.bind(console)],p={initialize:function(){var e;s.initialized="true"===s.apiWrapper.doLMSInitialize(),e="incomplete",s.apiWrapper.doLMSSetValue("cmi.core.lesson_status",e),s.initialized&&(s.startTime=new Date,window.addEventListener&&(window.addEventListener("unload",f),window.addEventListener("beforeunload",m)))},progressProvider:{getProgress:function(){var e={};try{e=JSON.parse(Object(l.b)(s.apiWrapper.doLMSGetValue("cmi.suspend_data")))}catch(t){S("Unable to restore progress")}return e},saveProgress:function(e){var t="true"===s.apiWrapper.doLMSSetValue("cmi.suspend_data",JSON.stringify(e)),n="true"===s.apiWrapper.doLMSSetValue("cmi.core.lesson_status","incomplete"),r=t&&n;r&&(s.apiWrapper.doLMSSetValue("cmi.core.exit","suspend"),s.apiWrapper.doLMSCommit());return r},removeProgress:function(){s.apiWrapper.doLMSSetValue("cmi.core.lesson_status","passed");var e="true"===s.apiWrapper.doLMSSetValue("cmi.suspend_data","");e&&(s.apiWrapper.doLMSSetValue("cmi.core.exit",""),s.apiWrapper.doLMSCommit());return e}},userInfoProvider:{getUsername:function(){return s.apiWrapper.doLMSGetValue("cmi.core.student_name")},getAccountId:function(){return s.apiWrapper.doLMSGetValue("cmi.core.student_id")},getAccountHomePage:function(){return window.location.origin||"".concat(window.location.protocol,"//").concat(window.location.host)}},courseFinished:function(e){s.apiWrapper.doLMSSetValue("cmi.core.lesson_status",Object(u.c)(e.status)?"passed":"failed"),L(e.score),s.apiWrapper.doLMSCommit()},courseFinalized:function(e){window.removeEventListener&&(window.removeEventListener("unload",f),window.removeEventListener("beforeunload",m));e.isCourseFinished||L(e.score);f()},addErrorHandler:function(e){d.push(e)}};function S(e){for(var t=0;t<d.length;t++)d[t](e)}function f(){var e=(new Date).getTime()-s.startTime.getTime();s.apiWrapper.doLMSSetValue("cmi.core.session_time",new Date(0,0,0,0,0,0,e).toString().replace(/.*(\d{2}:\d{2}:\d{2}).*/,"$1")),s.apiWrapper.doLMSCommit(),s.apiWrapper.doLMSFinish()}function m(e){return e.preventDefault(),e.returnValue="",""}function L(e){s.apiWrapper.doLMSSetValue("cmi.core.score.min","0"),s.apiWrapper.doLMSSetValue("cmi.core.score.max","100"),s.apiWrapper.doLMSSetValue("cmi.core.score.raw",e)}var M={initialized:!1,apiWrapper:function(){var e=window.console,t={code:"0",string:"No Error",diagnostic:"No Error"},n={code:"101",string:"General Exception",diagnostic:"General Exception"},r=!1,o=null;return{doLMSInitialize:i,doLMSFinish:function(){if(!r)return"true";var e=l(),t=e.Terminate("");if(null==e)return d("Unable to locate the LMS's API Implementation.\nTerminate was not successful."),"false";if("true"!==t.toString()){var n=u();d("Terminate failed with error code: ".concat(n.code))}return r=!1,t.toString()},doLMSGetValue:a,doLMSSetValue:c,doLMSCommit:function(){var e=l(),t="false";if(null==e)d("Unable to locate the LMS's API Implementation.\nCommit was not successful.");else if(r||i()){if("true"!==(t=e.Commit(""))){var n=u();d("Commit failed - error code: ".concat(n.code))}}else{var o=u();d("Commit failed - Could not initialize communication with the LMS - error code: ".concat(o.code))}return t.toString()}};function i(){if(r)return"true";var e=l();if(null==e)return d("Unable to locate the LMS's API Implementation.\nInitialize was not successful."),"false";var t=e.Initialize("");if("true"!==t.toString()){var n=u();d("Initialize failed with error code: ".concat(n.code))}else r=!0;return t.toString()}function a(e){var n=l(),o="";if(null==n)d("Unable to locate the LMS's API Implementation.\nGetValue was not successful.");else if(r||i()){o=n.GetValue(e);var a=u();a.code!==t.code&&(d("GetValue(".concat(e,") failed.\n         ").concat(a.code,": ").concat(a.string)),o="")}else{var c=u();d("GetValue failed - Could not initialize communication with the LMS - error code: ".concat(c.code))}return o.toString()}function c(e,t){var n=l(),o="false";if(null==n)d("Unable to locate the LMS's API Implementation.\nSetValue was not successful.");else if(r||i()){if("true"!==(o=n.SetValue(e,t)).toString()){var a=u();d("SetValue(".concat(e,", ").concat(t,") failed. \n        ").concat(a.code,": ").concat(a.string))}}else{var c=u();d("SetValue failed - Could not initialize communication with the LMS - error code: ".concat(c.code))}return o.toString()}function u(){var e={code:t.code,string:t.string,diagnostic:t.diagnostic},r=l();return null==r?(d("Unable to locate the LMS's API Implementation.\nCannot determine LMS error code."),e.code=n.code,e.string=n.string,e.diagnostic="Unable to locate the LMS's API Implementation. Cannot determine LMS error code.",e):(e.code=r.GetLastError().toString(),e.code!==t.code&&(e.string=r.GetErrorString(e.code),e.diagnostic=r.GetDiagnostic("")),e)}function l(){return null==o&&(o=function(){var e=s(window);null==e&&null!=window.opener&&"undefined"!=typeof window.opener&&(e=s(window.opener));null==e&&d("Unable to find an API adapter");return e}()),o}function s(e){for(var t=0;null==e.API_1484_11&&null!=e.parent&&e.parent!==e;){if(++t>500)return d("Error finding API -- too deeply nested."),null;e=e.parent}return e.API_1484_11}function d(t){e.log(t)}}(),startTime:null},g=[console.log.bind(console)],w={initialize:function(){M.initialized="true"===M.apiWrapper.doLMSInitialize(),M.apiWrapper.doLMSSetValue("cmi.completion_status","incomplete"),M.apiWrapper.doLMSSetValue("cmi.success_status","unknown"),M.initialized&&(M.startTime=new Date,window.addEventListener&&(window.addEventListener("unload",v),window.addEventListener("beforeunload",h)))},progressProvider:{getProgress:function(){var e={};try{e=JSON.parse(Object(l.b)(M.apiWrapper.doLMSGetValue("cmi.suspend_data")))}catch(t){!function(e){for(var t=0;t<g.length;t++)g[t](e)}("Unable to restore progress")}return e},saveProgress:function(e){var t="true"===M.apiWrapper.doLMSSetValue("cmi.suspend_data",JSON.stringify(e)),n="true"===M.apiWrapper.doLMSSetValue("cmi.completion_status","incomplete"),r=t&&n;r&&(M.apiWrapper.doLMSSetValue("cmi.exit","suspend"),M.apiWrapper.doLMSCommit());return r},removeProgress:function(){var e="true"===M.apiWrapper.doLMSSetValue("cmi.suspend_data","");e&&(M.apiWrapper.doLMSSetValue("cmi.exit",""),M.apiWrapper.doLMSCommit());return e}},userInfoProvider:{getUsername:function(){return M.apiWrapper.doLMSGetValue("cmi.learner_name")},getAccountId:function(){return M.apiWrapper.doLMSGetValue("cmi.learner_id")},getAccountHomePage:function(){return window.location.origin||"".concat(window.location.protocol,"//").concat(window.location.host)}},courseFinished:function(e){M.apiWrapper.doLMSSetValue("cmi.completion_status","completed"),M.apiWrapper.doLMSSetValue("cmi.success_status",Object(u.c)(e.status)?"passed":"failed"),I(e.score),M.apiWrapper.doLMSCommit()},courseFinalized:function(e){window.removeEventListener&&(window.removeEventListener("unload",v),window.removeEventListener("beforeunload",h));e.isCourseFinished||I(e.score);v()},addErrorHandler:function(e){g.push(e)}};function v(){var e=(new Date).getTime()-M.startTime.getTime();M.apiWrapper.doLMSSetValue("cmi.session_time",function(e){var t=Math.round(e%1e3/10),n=Math.floor(e/1e3%60),r=Math.floor(e/6e4%60),o=Math.floor(e/36e5%24),i=t>0?".".concat(t):"";return"PT".concat(o,"H").concat(r,"M").concat(n).concat(i,"S")}(e)),M.apiWrapper.doLMSCommit(),M.apiWrapper.doLMSFinish()}function h(e){return e.preventDefault(),e.returnValue="",""}function I(e){M.apiWrapper.doLMSSetValue("cmi.score.min","0"),M.apiWrapper.doLMSSetValue("cmi.score.max","100"),M.apiWrapper.doLMSSetValue("cmi.score.raw",e),M.apiWrapper.doLMSSetValue("cmi.score.scaled",e/100)}var W=new(function(){function e(){Object(i.a)(this,e),this.apiWrapper=void 0}return Object(a.a)(e,[{key:"on",value:function(e){this.apiWrapper=e?w:p,c.a.on(c.b.COURSE_FINISHED,this.apiWrapper.courseFinished),c.a.on(c.b.COURSE_FINALIZED,this.apiWrapper.courseFinalized)}},{key:"off",value:function(){c.a.off(c.b.COURSE_FINISHED,this.apiWrapper.courseFinished),c.a.off(c.b.COURSE_FINALIZED,this.apiWrapper.courseFinalized)}}]),e}()),V=function(){function e(){Object(i.a)(this,e),this.apiWrapper=void 0}return Object(a.a)(e,[{key:"initialize",value:function(e){return o.a.async((function(t){for(;;)switch(t.prev=t.next){case 0:this.apiWrapper=e?w:p,this.apiWrapper.initialize(),W.on(e);case 3:case"end":return t.stop()}}),null,this)}},{key:"addErrorHandler",get:function(){return this.apiWrapper.addErrorHandler}},{key:"userInfoProvider",get:function(){return this.apiWrapper.userInfoProvider}},{key:"progressProvider",get:function(){return this.apiWrapper.progressProvider}}]),e}();t.default=new V}}]);
//# sourceMappingURL=12.f936ded1.chunk.js.map