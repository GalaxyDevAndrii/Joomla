(this["webpackJsonpall-in-one"]=this["webpackJsonpall-in-one"]||[]).push([[11],{817:function(e,t,n){"use strict";n.r(t);var i=n(6),a=n(7),r=n(12),o=n(10),s=n(43),u=n(13),c=n(14),l=n(157),h=n(3),m=n.n(h),v=n(59),d=n(17),b=n(145),f=new(function(){function e(){Object(i.a)(this,e),this.reviewApiUrl=void 0,this.courseId=void 0,this.name=void 0,this.email=void 0,this.authoringToolDomain=void 0}return Object(a.a)(e,[{key:"initialize",value:function(e){var t=e.reviewApiUrl,n=e.courseId,i=e.authoringToolDomain;this.reviewApiUrl=t,this.courseId=n,this.authoringToolDomain=i,this.restoreUser()}},{key:"sendCommentRequest",value:function(e){var t;return m.a.async((function(n){for(;;)switch(n.prev=n.next){case 0:if(t=e.data,window.navigator.onLine){n.next=4;break}return n.abrupt("return",b.a);case 4:return t.createdByName=this.name,t.createdBy=this.email,t.courseId=this.courseId,n.next=9,m.a.awrap(v.b.post("".concat(this.reviewApiUrl,"comments"),{headers:{"Content-Type":d.b.JSON,"X-Authoring-Tool-Domain":this.authoringToolDomain},data:t}));case 9:if(200===n.sent.status){n.next=12;break}return n.abrupt("return",b.a);case 12:return n.abrupt("return",b.h);case 13:case"end":return n.stop()}}),null,this)}},{key:"restoreUser",value:function(){return this.email=localStorage.getItem(b.f),this.name=localStorage.getItem(b.g),{name:this.name,email:this.email}}},{key:"storeUser",value:function(e){localStorage.setItem(b.f,e.email),localStorage.setItem(b.g,e.name),this.email=e.email,this.name=e.name}}]),e}()),I=function(e){function t(){var e;return Object(i.a)(this,t),(e=Object(r.a)(this,Object(o.a)(t).call(this))).handlers=void 0,e.handlers=[{event:c.b.REVIEW_INITIALIZED,instance:e.reviewInitialize.bind(Object(s.a)(e))},{event:c.b.COMMENT_SENT,instance:e.sendComment.bind(Object(s.a)(e))},{event:c.b.REVIEWER_AUTHORIZED,instance:e.userAuthorized.bind(Object(s.a)(e))},{event:c.b.REVIEW_RESTORE_USER,instance:e.restoreUser.bind(Object(s.a)(e))}],e}return Object(u.a)(t,e),Object(a.a)(t,[{key:"reviewInitialize",value:function(e){var t=e.reviewApiUrl,n=e.courseId,i=e.authoringToolDomain;f.initialize({reviewApiUrl:t,courseId:n,authoringToolDomain:i})}},{key:"sendComment",value:function(e){var t=e.data;return f.sendCommentRequest({data:t})}},{key:"userAuthorized",value:function(e){var t=e.name,n=e.email;f.storeUser({name:t,email:n})}},{key:"restoreUser",value:function(){return f.restoreUser()}}]),t}(l.a),p=function(){function e(){Object(i.a)(this,e),this.eventHandler=void 0,this.eventHandler=new I}return Object(a.a)(e,[{key:"subscribe",value:function(){this.eventHandler.on()}},{key:"unsubscribe",value:function(){this.eventHandler.off()}}]),e}();t.default=new p}}]);
//# sourceMappingURL=11.252c7215.chunk.js.map