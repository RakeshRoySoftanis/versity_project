(window["webpackJsonpstar-admin-pro-react"]=window["webpackJsonpstar-admin-pro-react"]||[]).push([[34],{1169:function(e,a,t){"use strict";t.r(a),t.d(a,"Validation",(function(){return f}));var l=t(11),r=t(12),n=t(30),i=t(14),s=t(13),c=t(0),o=t.n(c),d=t(220),m=t(154),u=t(894),b=t(91),f=function(e){Object(i.a)(t,e);var a=Object(s.a)(t);function t(e){var r;return Object(l.a)(this,t),(r=a.call(this,e)).state={validated:!1},r.handleSubmit=r.handleSubmit.bind(Object(n.a)(r)),r}return Object(r.a)(t,[{key:"handleSubmit",value:function(e){!1===e.currentTarget.checkValidity()&&(e.preventDefault(),e.stopPropagation()),this.setState({validated:!0})}},{key:"render",value:function(){return o.a.createElement("div",null,o.a.createElement("div",{className:"page-header"},o.a.createElement("h3",{className:"page-title"},"Form Validation"),o.a.createElement("nav",{"aria-label":"breadcrumb"},o.a.createElement("ol",{className:"breadcrumb"},o.a.createElement("li",{className:"breadcrumb-item"},o.a.createElement("a",{href:"!#",onClick:function(e){return e.preventDefault()}},"Forms")),o.a.createElement("li",{className:"breadcrumb-item active","aria-current":"page"},"Validation Chart")))),o.a.createElement("div",{className:"row"},o.a.createElement("div",{className:"col-md-12 grid-margin"},o.a.createElement("div",{className:"card"},o.a.createElement("div",{className:"card-body"},o.a.createElement("h4",{className:"card-title"},"Basic Form Validation"),o.a.createElement(d.a,{noValidate:!0,validated:this.state.validated,onSubmit:this.handleSubmit},o.a.createElement(d.a.Row,null,o.a.createElement(d.a.Group,{as:m.a,md:"12",controlId:"validationCustom01"},o.a.createElement(d.a.Label,null,"First name"),o.a.createElement(d.a.Control,{required:!0,type:"text",placeholder:"First name",defaultValue:"Mark"}),o.a.createElement(d.a.Control.Feedback,null,"Looks good!")),o.a.createElement(d.a.Group,{as:m.a,md:"12",controlId:"validationCustom02"},o.a.createElement(d.a.Label,null,"Last name"),o.a.createElement(d.a.Control,{required:!0,type:"text",placeholder:"Last name",defaultValue:"Otto"}),o.a.createElement(d.a.Control.Feedback,null,"Looks good!")),o.a.createElement(d.a.Group,{as:m.a,md:"12",controlId:"validationCustomUsername"},o.a.createElement(d.a.Label,null,"Username"),o.a.createElement(u.a,null,o.a.createElement(u.a.Prepend,null,o.a.createElement(u.a.Text,{id:"inputGroupPrepend"},"@")),o.a.createElement(d.a.Control,{type:"text",placeholder:"Username","aria-describedby":"inputGroupPrepend",required:!0}),o.a.createElement(d.a.Control.Feedback,{type:"invalid"},"Please choose a username.")))),o.a.createElement(d.a.Row,null,o.a.createElement(d.a.Group,{as:m.a,md:"12",controlId:"validationCustom03"},o.a.createElement(d.a.Label,null,"City"),o.a.createElement(d.a.Control,{type:"text",placeholder:"City",required:!0}),o.a.createElement(d.a.Control.Feedback,{type:"invalid"},"Please provide a valid city.")),o.a.createElement(d.a.Group,{as:m.a,md:"3",controlId:"validationCustom04"},o.a.createElement(d.a.Label,null,"State"),o.a.createElement(d.a.Control,{type:"text",placeholder:"State",required:!0}),o.a.createElement(d.a.Control.Feedback,{type:"invalid"},"Please provide a valid state.")),o.a.createElement(d.a.Group,{as:m.a,md:"",controlId:"validationCustom05"},o.a.createElement(d.a.Label,null,"Zip"),o.a.createElement(d.a.Control,{type:"text",placeholder:"Zip",required:!0}),o.a.createElement(d.a.Control.Feedback,{type:"invalid"},"Please provide a valid zip."))),o.a.createElement(d.a.Group,null,o.a.createElement(d.a.Check,{required:!0,label:"Agree to terms and conditions",feedback:"You must agree before submitting."})),o.a.createElement(b.a,{type:"submit"},"Submit form")))))))}}]),t}(c.Component);a.default=f},135:function(e,a,t){"use strict";var l=t(0),r=t.n(l).a.createContext({controlId:void 0});a.a=r},140:function(e,a,t){"use strict";var l=t(2),r=t(4),n=t(5),i=t.n(n),s=t(0),c=t.n(s),o=t(6),d=t.n(o),m={type:d.a.string.isRequired,as:d.a.elementType},u=c.a.forwardRef((function(e,a){var t=e.as,n=void 0===t?"div":t,s=e.className,o=e.type,d=Object(r.a)(e,["as","className","type"]);return c.a.createElement(n,Object(l.a)({},d,{ref:a,className:i()(s,o&&o+"-feedback")}))}));u.displayName="Feedback",u.propTypes=m,u.defaultProps={type:"valid"},a.a=u},154:function(e,a,t){"use strict";var l=t(2),r=t(4),n=t(5),i=t.n(n),s=t(0),c=t.n(s),o=t(7),d=["xl","lg","md","sm","xs"],m=c.a.forwardRef((function(e,a){var t=e.bsPrefix,n=e.className,s=e.as,m=void 0===s?"div":s,u=Object(r.a)(e,["bsPrefix","className","as"]),b=Object(o.b)(t,"col"),f=[],p=[];return d.forEach((function(e){var a,t,l,r=u[e];if(delete u[e],null!=r&&"object"===typeof r){var n=r.span;a=void 0===n||n,t=r.offset,l=r.order}else a=r;var i="xs"!==e?"-"+e:"";null!=a&&f.push(!0===a?""+b+i:""+b+i+"-"+a),null!=l&&p.push("order"+i+"-"+l),null!=t&&p.push("offset"+i+"-"+t)})),f.length||f.push(b),c.a.createElement(m,Object(l.a)({},u,{ref:a,className:i.a.apply(void 0,[n].concat(f,p))}))}));m.displayName="Col",a.a=m},158:function(e,a,t){"use strict";var l=t(2),r=t(4),n=t(5),i=t.n(n),s=(t(95),t(0)),c=t.n(s),o=(t(56),t(140)),d=t(135),m=t(7),u=c.a.forwardRef((function(e,a){var t,n,o=e.bsPrefix,u=e.bsCustomPrefix,b=e.type,f=e.size,p=e.id,v=e.className,E=e.isValid,x=e.isInvalid,O=e.plaintext,y=e.readOnly,N=e.custom,j=e.as,h=void 0===j?"input":j,P=Object(r.a)(e,["bsPrefix","bsCustomPrefix","type","size","id","className","isValid","isInvalid","plaintext","readOnly","custom","as"]),C=Object(s.useContext)(d.a).controlId,F=N?[u,"custom"]:[o,"form-control"],I=F[0],w=F[1];if(o=Object(m.b)(I,w),O)(n={})[o+"-plaintext"]=!0,t=n;else if("file"===b){var k;(k={})[o+"-file"]=!0,t=k}else if("range"===b){var g;(g={})[o+"-range"]=!0,t=g}else if("select"===h&&N){var V;(V={})[o+"-select"]=!0,V[o+"-select-"+f]=f,t=V}else{var R;(R={})[o]=!0,R[o+"-"+f]=f,t=R}return c.a.createElement(h,Object(l.a)({},P,{type:b,ref:a,readOnly:y,id:p||C,className:i()(v,t,E&&"is-valid",x&&"is-invalid")}))}));u.displayName="FormControl",u.Feedback=o.a,a.a=u},220:function(e,a,t){"use strict";var l=t(2),r=t(4),n=t(5),i=t.n(n),s=t(0),c=t.n(s),o=(t(95),t(140)),d=t(135),m=t(7),u=c.a.forwardRef((function(e,a){var t=e.id,n=e.bsPrefix,o=e.bsCustomPrefix,u=e.className,b=e.isValid,f=e.isInvalid,p=e.isStatic,v=e.as,E=void 0===v?"input":v,x=Object(r.a)(e,["id","bsPrefix","bsCustomPrefix","className","isValid","isInvalid","isStatic","as"]),O=Object(s.useContext)(d.a),y=O.controlId,N=O.custom?[o,"custom-control-input"]:[n,"form-check-input"],j=N[0],h=N[1];return n=Object(m.b)(j,h),c.a.createElement(E,Object(l.a)({},x,{ref:a,id:t||y,className:i()(u,n,b&&"is-valid",f&&"is-invalid",p&&"position-static")}))}));u.displayName="FormCheckInput",u.defaultProps={type:"checkbox"};var b=u,f=c.a.forwardRef((function(e,a){var t=e.bsPrefix,n=e.bsCustomPrefix,o=e.className,u=e.htmlFor,b=Object(r.a)(e,["bsPrefix","bsCustomPrefix","className","htmlFor"]),f=Object(s.useContext)(d.a),p=f.controlId,v=f.custom?[n,"custom-control-label"]:[t,"form-check-label"],E=v[0],x=v[1];return t=Object(m.b)(E,x),c.a.createElement("label",Object(l.a)({},b,{ref:a,htmlFor:u||p,className:i()(o,t)}))}));f.displayName="FormCheckLabel";var p=f,v=c.a.forwardRef((function(e,a){var t=e.id,n=e.bsPrefix,u=e.bsCustomPrefix,f=e.inline,v=e.disabled,E=e.isValid,x=e.isInvalid,O=e.feedback,y=e.className,N=e.style,j=e.title,h=e.type,P=e.label,C=e.children,F=e.custom,I=e.as,w=void 0===I?"input":I,k=Object(r.a)(e,["id","bsPrefix","bsCustomPrefix","inline","disabled","isValid","isInvalid","feedback","className","style","title","type","label","children","custom","as"]),g="switch"===h||F,V=g?[u,"custom-control"]:[n,"form-check"],R=V[0],L=V[1];n=Object(m.b)(R,L);var S=Object(s.useContext)(d.a).controlId,G=Object(s.useMemo)((function(){return{controlId:t||S,custom:g}}),[S,g,t]),q=null!=P&&!1!==P&&!C,T=c.a.createElement(b,Object(l.a)({},k,{type:"switch"===h?"checkbox":h,ref:a,isValid:E,isInvalid:x,isStatic:!q,disabled:v,as:w}));return c.a.createElement(d.a.Provider,{value:G},c.a.createElement("div",{style:N,className:i()(y,n,g&&"custom-"+h,f&&n+"-inline")},C||c.a.createElement(c.a.Fragment,null,T,q&&c.a.createElement(p,{title:j},P),(E||x)&&c.a.createElement(o.a,{type:E?"valid":"invalid"},O))))}));v.displayName="FormCheck",v.defaultProps={type:"checkbox",inline:!1,disabled:!1,isValid:!1,isInvalid:!1,title:""},v.Input=b,v.Label=p;var E=v,x=c.a.forwardRef((function(e,a){var t=e.id,n=e.bsPrefix,o=e.bsCustomPrefix,u=e.className,b=e.isValid,f=e.isInvalid,p=e.lang,v=e.as,E=void 0===v?"input":v,x=Object(r.a)(e,["id","bsPrefix","bsCustomPrefix","className","isValid","isInvalid","lang","as"]),O=Object(s.useContext)(d.a),y=O.controlId,N=O.custom?[o,"custom-file-input"]:[n,"form-control-file"],j=N[0],h=N[1];return n=Object(m.b)(j,h),c.a.createElement(E,Object(l.a)({},x,{ref:a,id:t||y,type:"file",lang:p,className:i()(u,n,b&&"is-valid",f&&"is-invalid")}))}));x.displayName="FormFileInput";var O=x,y=c.a.forwardRef((function(e,a){var t=e.bsPrefix,n=e.bsCustomPrefix,o=e.className,u=e.htmlFor,b=Object(r.a)(e,["bsPrefix","bsCustomPrefix","className","htmlFor"]),f=Object(s.useContext)(d.a),p=f.controlId,v=f.custom?[n,"custom-file-label"]:[t,"form-file-label"],E=v[0],x=v[1];return t=Object(m.b)(E,x),c.a.createElement("label",Object(l.a)({},b,{ref:a,htmlFor:u||p,className:i()(o,t),"data-browse":b["data-browse"]}))}));y.displayName="FormFileLabel";var N=y,j=c.a.forwardRef((function(e,a){var t=e.id,n=e.bsPrefix,u=e.bsCustomPrefix,b=e.disabled,f=e.isValid,p=e.isInvalid,v=e.feedback,E=e.className,x=e.style,y=e.label,j=e.children,h=e.custom,P=e.lang,C=e["data-browse"],F=e.as,I=void 0===F?"div":F,w=e.inputAs,k=void 0===w?"input":w,g=Object(r.a)(e,["id","bsPrefix","bsCustomPrefix","disabled","isValid","isInvalid","feedback","className","style","label","children","custom","lang","data-browse","as","inputAs"]),V=h?[u,"custom"]:[n,"form-file"],R=V[0],L=V[1];n=Object(m.b)(R,L);var S=Object(s.useContext)(d.a).controlId,G=Object(s.useMemo)((function(){return{controlId:t||S,custom:h}}),[S,h,t]),q=null!=y&&!1!==y&&!j,T=c.a.createElement(O,Object(l.a)({},g,{ref:a,isValid:f,isInvalid:p,disabled:b,as:k,lang:P}));return c.a.createElement(d.a.Provider,{value:G},c.a.createElement(I,{style:x,className:i()(E,n,h&&"custom-file")},j||c.a.createElement(c.a.Fragment,null,h?c.a.createElement(c.a.Fragment,null,T,q&&c.a.createElement(N,{"data-browse":C},y)):c.a.createElement(c.a.Fragment,null,q&&c.a.createElement(N,null,y),T),(f||p)&&c.a.createElement(o.a,{type:f?"valid":"invalid"},v))))}));j.displayName="FormFile",j.defaultProps={disabled:!1,isValid:!1,isInvalid:!1},j.Input=O,j.Label=N;var h=j,P=t(158),C=c.a.forwardRef((function(e,a){var t=e.bsPrefix,n=e.className,o=e.children,u=e.controlId,b=e.as,f=void 0===b?"div":b,p=Object(r.a)(e,["bsPrefix","className","children","controlId","as"]);t=Object(m.b)(t,"form-group");var v=Object(s.useMemo)((function(){return{controlId:u}}),[u]);return c.a.createElement(d.a.Provider,{value:v},c.a.createElement(f,Object(l.a)({},p,{ref:a,className:i()(n,t)}),o))}));C.displayName="FormGroup";var F=C,I=(t(56),t(154)),w=c.a.forwardRef((function(e,a){var t=e.as,n=void 0===t?"label":t,o=e.bsPrefix,u=e.column,b=e.srOnly,f=e.className,p=e.htmlFor,v=Object(r.a)(e,["as","bsPrefix","column","srOnly","className","htmlFor"]),E=Object(s.useContext)(d.a).controlId;o=Object(m.b)(o,"form-label");var x="col-form-label";"string"===typeof u&&(x=x+"-"+u);var O=i()(f,o,b&&"sr-only",u&&x);return p=p||E,u?c.a.createElement(I.a,Object(l.a)({as:"label",className:O,htmlFor:p},v)):c.a.createElement(n,Object(l.a)({ref:a,className:O,htmlFor:p},v))}));w.displayName="FormLabel",w.defaultProps={column:!1,srOnly:!1};var k=w,g=c.a.forwardRef((function(e,a){var t=e.bsPrefix,n=e.className,s=e.as,o=void 0===s?"small":s,d=e.muted,u=Object(r.a)(e,["bsPrefix","className","as","muted"]);return t=Object(m.b)(t,"form-text"),c.a.createElement(o,Object(l.a)({},u,{ref:a,className:i()(n,t,d&&"text-muted")}))}));g.displayName="FormText";var V=g,R=c.a.forwardRef((function(e,a){return c.a.createElement(E,Object(l.a)({},e,{ref:a,type:"switch"}))}));R.displayName="Switch",R.Input=E.Input,R.Label=E.Label;var L=R,S=t(57),G=c.a.forwardRef((function(e,a){var t=e.bsPrefix,n=e.inline,s=e.className,o=e.validated,d=e.as,u=void 0===d?"form":d,b=Object(r.a)(e,["bsPrefix","inline","className","validated","as"]);return t=Object(m.b)(t,"form"),c.a.createElement(u,Object(l.a)({},b,{ref:a,className:i()(s,o&&"was-validated",n&&t+"-inline")}))}));G.displayName="Form",G.defaultProps={inline:!1},G.Row=Object(S.a)("form-row"),G.Group=F,G.Control=P.a,G.Check=E,G.File=h,G.Switch=L,G.Label=k,G.Text=V;a.a=G},894:function(e,a,t){"use strict";var l=t(2),r=t(4),n=t(5),i=t.n(n),s=t(0),c=t.n(s),o=t(57),d=t(7),m=c.a.forwardRef((function(e,a){var t=e.bsPrefix,n=e.size,s=e.className,o=e.as,m=void 0===o?"div":o,u=Object(r.a)(e,["bsPrefix","size","className","as"]);return t=Object(d.b)(t,"input-group"),c.a.createElement(m,Object(l.a)({ref:a},u,{className:i()(s,t,n&&t+"-"+n)}))})),u=Object(o.a)("input-group-append"),b=Object(o.a)("input-group-prepend"),f=Object(o.a)("input-group-text",{Component:"span"});m.displayName="InputGroup",m.Text=f,m.Radio=function(e){return c.a.createElement(f,null,c.a.createElement("input",Object(l.a)({type:"radio"},e)))},m.Checkbox=function(e){return c.a.createElement(f,null,c.a.createElement("input",Object(l.a)({type:"checkbox"},e)))},m.Append=u,m.Prepend=b,a.a=m}}]);
//# sourceMappingURL=34.02b4f9fd.chunk.js.map