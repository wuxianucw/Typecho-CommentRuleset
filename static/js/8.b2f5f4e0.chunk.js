(this.webpackJsonpfrontend=this.webpackJsonpfrontend||[]).push([[8],{327:function(e,a,t){"use strict";t.r(a),t.d(a,"default",(function(){return ne}));var n=t(52),l=t(41),c=t(0),r=t.n(c),o=t(222),i=t.n(o),u=t(4),s=t(208),d=t(23),m=t(13),E=t(332),p=t(333),f=t(330),g=t(331),b=t(328),h=t(353),k=t(329),C=t(198),w=t(70),v=t(164),y=t(349),O=t(199),x=t(215),P=t(276),j=t.n(P),S=t(275),R=t.n(S),D=t(281),I=t.n(D),N=t(279),_=t.n(N),T=t(282),A=t.n(T),B=t(277),F=t.n(B),V=t(278),z=t.n(V),J=t(280),W=t.n(J),L=t(334),M=t(338),U=t(342),q=t(340),G=t(341),H=t(339),K=t(202),Q=t(211),X=[{id:"name",numeric:!1,disablePadding:!0,label:"\u89c4\u5219\u540d\u79f0"},{id:"ruid",numeric:!1,disablePadding:!1,label:"RUID",tooltip:"\u89c4\u5219\u552f\u4e00\u6807\u8bc6\u7b26"},{id:"remark",numeric:!1,disablePadding:!1,label:"\u89c4\u5219\u5907\u6ce8"},{id:"status",numeric:!1,disablePadding:!1,label:"\u89c4\u5219\u72b6\u6001"},{id:"priority",numeric:!0,disablePadding:!1,label:"\u89c4\u5219\u4f18\u5148\u7ea7",tooltip:"\u6570\u503c\u8d8a\u5927\u7684\u89c4\u5219\u4f18\u5148\u7ea7\u8d8a\u9ad8"}];function Y(e){var a=e.onSelectAllClick,t=e.numSelected,n=e.rowCount;return r.a.createElement(b.a,null,r.a.createElement(k.a,null,r.a.createElement(f.a,{padding:"checkbox"},r.a.createElement(y.a,{indeterminate:t>0&&t<n,checked:n>0&&t===n,onChange:a})),X.map((function(e){return e.tooltip?r.a.createElement(x.a,{key:e.id,title:e.tooltip},r.a.createElement(f.a,{align:e.numeric?"right":"left",padding:e.disablePadding?"none":"default"},e.label)):r.a.createElement(f.a,{key:e.id,align:e.numeric?"right":"left",padding:e.disablePadding?"none":"default"},e.label)})),r.a.createElement(f.a,{align:"right"},"\u64cd\u4f5c")))}var Z=Object(s.a)((function(e){return{root:{paddingLeft:e.spacing(2),paddingRight:e.spacing(1)},highlight:{color:e.palette.secondary.main,backgroundColor:Object(d.e)(e.palette.secondary.light,.85)},title:{flex:"1 1 100%"}}}));function $(e){var a=Z(),t=e.numSelected,n=e.rowCount,c=e.disableDelete,o=e.onAddClick,i=e.onDeleteClick;return r.a.createElement(C.a,{className:Object(u.a)(a.root,Object(l.a)({},a.highlight,t>0))},t>0?r.a.createElement(w.a,{className:a.title,color:"inherit",variant:"subtitle1",component:"div"},"\u9009\u4e2d ",t," \u6761\u89c4\u5219"):r.a.createElement(w.a,{className:a.title,variant:"h6",id:"tableTitle",component:"div"},"\u76ee\u524d\u5171 ",n," \u6761\u89c4\u5219"),t>0?r.a.createElement(x.a,{title:c?"\u9009\u4e2d\u7684\u89c4\u5219\u4e2d\u5305\u542b\u9501\u5b9a\u7684\u89c4\u5219":"\u5220\u9664\u89c4\u5219"},r.a.createElement("span",null,r.a.createElement(O.a,{disabled:c,onClick:i},r.a.createElement(R.a,null)))):r.a.createElement(x.a,{title:"\u65b0\u589e\u89c4\u5219"},r.a.createElement(O.a,{onClick:o},r.a.createElement(j.a,null))))}var ee=Object(s.a)((function(e){return{root:{width:"100%"},paper:{width:"100%"},table:{minWidth:750},disableVerticalPadding:{padding:"0 16px"},statusIcon:{marginRight:e.spacing(1),"& svg":{verticalAlign:"bottom"}},backdrop:{zIndex:e.zIndex.drawer+1,color:"#fff"}}}));function ae(e,a,t,n,l){return{name:e,ruid:a,remark:t,status:n,priority:l}}function te(e){if("object"!==typeof e||!e)return[];var a=[];for(var t in e){var n=e[t],l=n.name,c=n.remark,r=n.status,o=n.priority;a.push(ae(l,t,c,r,o))}return a}function ne(e){var a=Object(m.a)(),t=ee(),l=e.history,c=r.a.useState(window.__pageData.rules),o=Object(n.a)(c,2),u=o[0],s=o[1],d=r.a.useState([]),b=Object(n.a)(d,2),C=b[0],w=b[1],P=r.a.useState(0),j=Object(n.a)(P,2),S=j[0],R=j[1],D=r.a.useState(0),N=Object(n.a)(D,2),T=N[0],B=N[1],V=r.a.useState(10),J=Object(n.a)(V,2),Z=J[0],ae=J[1],ne=r.a.useState(!1),le=Object(n.a)(ne,2),ce=le[0],re=le[1],oe=r.a.useState(""),ie=Object(n.a)(oe,2),ue=ie[0],se=ie[1],de=r.a.useState(!1),me=Object(n.a)(de,2),Ee=me[0],pe=me[1],fe=r.a.useState(["",!0]),ge=Object(n.a)(fe,2),be=ge[0],he=ge[1],ke=r.a.useState(!1),Ce=Object(n.a)(ke,2),we=Ce[0],ve=Ce[1],ye=r.a.useState(!1),Oe=Object(n.a)(ye,2),xe=Oe[0],Pe=Oe[1],je=r.a.useState(!1),Se=Object(n.a)(je,2),Re=Se[0],De=Se[1];r.a.useEffect((function(){return function(){window.__pageData.rules=u}}),[u]);var Ie=function(e,a){var t=C.indexOf(a),n=function(e){var a=u.filter((function(a){return a.ruid===e}));return!(a.length<1)&&-1!==a[0].status.indexOf("locked")}(a),l=[];-1===t?l=l.concat(C,a):0===t?l=l.concat(C.slice(1)):t===C.length-1?l=l.concat(C.slice(0,-1)):t>0&&(l=l.concat(C.slice(0,t),C.slice(t+1))),n&&R(S+(-1===t?1:-1)),w(l)},Ne=function(){re(!1)},_e=Z-Math.min(Z,u.length-T*Z),Te={on:{text:"\u751f\u6548",icon:r.a.createElement(F.a,{htmlColor:a.palette.success.dark})},off:{text:"\u672a\u751f\u6548",icon:r.a.createElement(z.a,{color:"action"})},locked:{text:"\u9501\u5b9a",icon:r.a.createElement(_.a,{color:"action"})},uncompiled:{text:"\u672a\u7f16\u8bd1",icon:r.a.createElement(W.a,{htmlColor:a.palette.warning.dark})}},Ae=function(){pe(!1)},Be=r.a.useRef();r.a.useEffect((function(){return function(){xe&&Be.current.cancel()}}),[]);var Fe=function(){ve(!1)},Ve=function(){De(!1)};return window.loadRules||(console.warn("\u8bf7\u79fb\u9664 window.loadRules \u8c03\u8bd5\u4ee3\u7801"),window.loadRules=function(){console.warn("\u5f53\u4f60\u8fd8\u5728\u8c03\u7528 window.loadRules \u65f6\uff0c\u4f60\u5e94\u8be5\u53cd\u601d\u4e00\u4e0b\u4f60\u662f\u4e0d\u662f\u6709\u4ec0\u4e48\u95ee\u9898"),Be.current=i.a.CancelToken.source(),Pe(!0),i.a.get(window.__pageData.apiBase,{params:{a:"removeRules",ruid:[]},cancelToken:Be.current.token}).then((function(e){var a=e.data;s(te(a)),w([]),Pe(!1)})).catch((function(e){i.a.isCancel(e)||(console.error(e),De(!0)),Pe(!1)}))}),r.a.createElement("div",{className:t.root},r.a.createElement(v.a,{className:t.paper},r.a.createElement($,{numSelected:C.length,rowCount:u.length,disableDelete:S>0,onAddClick:function(){l.push("/edit")},onDeleteClick:function(){pe(!0)}}),r.a.createElement(g.a,null,r.a.createElement(E.a,{className:t.table},r.a.createElement(Y,{classes:t,numSelected:C.length,onSelectAllClick:function(e){if(e.target.checked){var a=0,t=u.map((function(e){return-1!==e.status.indexOf("locked")&&a++,e.ruid}));return R(a),void w(t)}R(0),w([])},rowCount:u.length}),r.a.createElement(p.a,null,u.slice(T*Z,T*Z+Z).map((function(e){var a,n=(a=e.ruid,-1!==C.indexOf(a)),c=-1!==e.status.indexOf("locked");return r.a.createElement(k.a,{hover:!0,role:"checkbox",tabIndex:-1,key:e.ruid,selected:n},r.a.createElement(f.a,{padding:"checkbox"},r.a.createElement(y.a,{onClick:function(a){return Ie(0,e.ruid)},checked:n})),r.a.createElement(f.a,{component:"th",scope:"row",padding:"none"},e.name),r.a.createElement(f.a,null,r.a.createElement("code",null,e.ruid)),r.a.createElement(f.a,{className:t.disableVerticalPadding},r.a.createElement(L.a,{onClick:function(){return a=e.remark,se(""===a?"\uff08\u65e0\u5907\u6ce8\uff09":a),void re(!0);var a}},"\u70b9\u51fb\u67e5\u770b")),r.a.createElement(f.a,{className:t.disableVerticalPadding},e.status.map((function(e){return r.a.createElement(x.a,{key:e,title:Te[e].text},r.a.createElement("span",{className:t.statusIcon},Te[e].icon))}))),r.a.createElement(f.a,{align:"right"},e.priority),r.a.createElement(f.a,{align:"right",className:t.disableVerticalPadding},r.a.createElement(x.a,{title:c?"\u9501\u5b9a\u72b6\u6001\u4e0b\u7981\u6b62\u7f16\u8f91":"\u7f16\u8f91"},r.a.createElement("span",{style:{display:"inline-block"}},r.a.createElement(O.a,{disabled:c,onClick:function(){return function(e){l.push("/edit/"+e)}(e.ruid)}},r.a.createElement(I.a,null)))),r.a.createElement(x.a,{title:c?"\u89e3\u9501":"\u9501\u5b9a"},r.a.createElement("span",{style:{display:"inline-block"}},r.a.createElement(O.a,{onClick:function(){return function(e,a){he([e,!a]),ve(!0)}(e.ruid,c)}},c?r.a.createElement(A.a,null):r.a.createElement(_.a,null))))))})),_e>0&&r.a.createElement(k.a,{style:{height:53*_e}},r.a.createElement(f.a,{colSpan:X.length+2}))))),r.a.createElement(h.a,{rowsPerPageOptions:[5,10,25],component:"div",count:u.length,rowsPerPage:Z,page:T,backIconButtonText:"\u4e0a\u4e00\u9875",nextIconButtonText:"\u4e0b\u4e00\u9875",labelRowsPerPage:"\u6bcf\u9875\u89c4\u5219\u6570\uff1a",labelDisplayedRows:function(e){var a=e.from,t=e.to,n=e.count;return"".concat(n," \u6761\u4e2d\u7684\u7b2c ").concat(a," \u81f3 ").concat(t," \u6761")},onChangePage:function(e,a){B(a)},onChangeRowsPerPage:function(e){ae(parseInt(e.target.value,10)),B(0)}})),r.a.createElement(M.a,{open:ce,fullWidth:!0,onClose:Ne},r.a.createElement(H.a,null,"\u67e5\u770b\u89c4\u5219\u5907\u6ce8"),r.a.createElement(q.a,null,r.a.createElement(G.a,null,ue)),r.a.createElement(U.a,null,r.a.createElement(L.a,{onClick:Ne,color:"primary",autoFocus:!0},"\u5173\u95ed"))),r.a.createElement(M.a,{open:Ee,onClose:Ae},r.a.createElement(H.a,null,"\u786e\u5b9a\u5220\u9664\u8fd9\u4e9b\u89c4\u5219\u5417\uff1f"),r.a.createElement(q.a,null,r.a.createElement(G.a,null,"\u6b64\u64cd\u4f5c\u4e0d\u53ef\u9006\uff0c\u88ab\u5220\u9664\u7684\u89c4\u5219\u5c06\u65e0\u6cd5\u6062\u590d\u3002")),r.a.createElement(U.a,null,r.a.createElement(L.a,{onClick:function(){pe(!1),Be.current=i.a.CancelToken.source(),Pe(!0),i.a.get(window.__pageData.apiBase,{params:{a:"removeRules",ruid:C},cancelToken:Be.current.token}).then((function(e){var a=e.data;s(te(a)),w([]),Pe(!1)})).catch((function(e){i.a.isCancel(e)||(console.error(e),De(!0)),Pe(!1)}))},color:"secondary"},"\u786e\u5b9a"),r.a.createElement(L.a,{onClick:Ae,color:"primary",autoFocus:!0},"\u53d6\u6d88"))),r.a.createElement(M.a,{open:we,onClose:Fe},r.a.createElement(H.a,null,"\u786e\u5b9a",be[1]?"\u9501\u5b9a":"\u89e3\u9501","\u8fd9\u6761\u89c4\u5219\u5417\uff1f"),r.a.createElement(q.a,null,r.a.createElement(G.a,null,be[1]?"\u89c4\u5219\u88ab\u9501\u5b9a\u540e\uff0c\u9664\u975e\u624b\u52a8\u89e3\u9501\uff0c\u5426\u5219\u5c06\u65e0\u6cd5\u88ab\u7f16\u8f91\u3002\u8fd9\u80fd\u591f\u6709\u6548\u964d\u4f4e\u8bef\u64cd\u4f5c\u7684\u53ef\u80fd\u6027\u3002":"\u89c4\u5219\u88ab\u89e3\u9501\u540e\uff0c\u5c06\u53ef\u4ee5\u76f4\u63a5\u88ab\u7f16\u8f91\u3002\u8fd9\u53ef\u80fd\u4f1a\u589e\u52a0\u8bef\u64cd\u4f5c\u7684\u98ce\u9669\u3002")),r.a.createElement(U.a,null,r.a.createElement(L.a,{onClick:function(){ve(!1),Be.current=i.a.CancelToken.source(),Pe(!0),i.a.get(window.__pageData.apiBase,{params:{a:"lockRule",ruid:be[0],opt:be[1]?1:0},cancelToken:Be.current.token}).then((function(){s((function(e){var a,t=e;return a=t.find((function(e){return e.ruid===be[0]})).status,be[1]?a.push("locked"):a.splice(a.findIndex((function(e){return"locked"===e})),1),t})),Pe(!1)})).catch((function(e){i.a.isCancel(e)||(console.error(e),De(!0)),Pe(!1)}))},color:"secondary"},"\u786e\u5b9a"),r.a.createElement(L.a,{onClick:Fe,color:"primary",autoFocus:!0},"\u53d6\u6d88"))),r.a.createElement(K.a,{className:t.backdrop,open:xe},r.a.createElement(Q.a,{color:"inherit"})),r.a.createElement(M.a,{open:Re,onClose:Ve},r.a.createElement(H.a,null,"\u8bf7\u6c42\u540e\u7aef API \u5931\u8d25\uff01"),r.a.createElement(q.a,null,r.a.createElement(G.a,null,"\u53ef\u80fd\u662f\u7f51\u7edc\u9519\u8bef\u6216\u540e\u7aef\u5904\u7406\u5931\u8d25\uff0c\u8bf7\u6253\u5f00\u5f00\u53d1\u4eba\u5458\u5de5\u5177\u67e5\u770b\u8be6\u7ec6\u4fe1\u606f\u3002")),r.a.createElement(U.a,null,r.a.createElement(L.a,{onClick:Ve,color:"primary",autoFocus:!0},"\u786e\u5b9a"))))}}}]);
//# sourceMappingURL=8.b2f5f4e0.chunk.js.map