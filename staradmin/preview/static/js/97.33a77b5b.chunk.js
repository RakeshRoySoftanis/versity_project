(window["webpackJsonpstar-admin-pro-react"]=window["webpackJsonpstar-admin-pro-react"]||[]).push([[97],{1175:function(e,a,r){"use strict";r.r(a),r.d(a,"SimpleMap",(function(){return p}));var t=r(11),c=r(12),n=r(14),m=r(13),l=r(0),s=r.n(l),o=r(961),i="https://raw.githubusercontent.com/zcreativelabs/react-simple-maps/master/topojson-maps/world-110m.json",p=function(e){Object(n.a)(r,e);var a=Object(m.a)(r);function r(){return Object(t.a)(this,r),a.apply(this,arguments)}return Object(c.a)(r,[{key:"render",value:function(){return s.a.createElement("div",null,s.a.createElement("div",{className:"page-header"},s.a.createElement("h3",{className:"page-title"}," React Simple Maps "),s.a.createElement("nav",{"aria-label":"breadcrumb"},s.a.createElement("ol",{className:"breadcrumb"},s.a.createElement("li",{className:"breadcrumb-item"},s.a.createElement("a",{href:"!#",onClick:function(e){return e.preventDefault()}},"Maps")),s.a.createElement("li",{className:"breadcrumb-item active","aria-current":"page"},"Simple Maps")))),s.a.createElement("div",{className:"row"},s.a.createElement("div",{className:"col-sm-6 grid-margin stretch-card"},s.a.createElement("div",{className:"card"},s.a.createElement("div",{className:"card-body"},s.a.createElement("h4",{className:"card-title"},"ZoomableGroup"),s.a.createElement("div",{className:"map-dimension"},s.a.createElement(o.ComposableMap,null,s.a.createElement(o.ZoomableGroup,{zoom:1},s.a.createElement(o.Geographies,{geography:i},(function(e){return e.geographies.map((function(e){return s.a.createElement(o.Geography,{key:e.rsmKey,geography:e})}))})))))))),s.a.createElement("div",{className:"col-sm-6 grid-margin stretch-card"},s.a.createElement("div",{className:"card"},s.a.createElement("div",{className:"card-body"},s.a.createElement("h4",{className:"card-title"},"Sphere"),s.a.createElement("div",{className:"map-dimension"},s.a.createElement(o.ComposableMap,{projectionConfig:{scale:147}},s.a.createElement(o.Sphere,{stroke:"#FF5533",strokeWidth:2}),s.a.createElement(o.Geographies,{geography:i},(function(e){return e.geographies.map((function(e){return s.a.createElement(o.Geography,{key:e.rsmKey,geography:e})}))})))))))),s.a.createElement("div",{className:"row"},s.a.createElement("div",{className:"col-sm-6 grid-margin stretch-card"},s.a.createElement("div",{className:"card"},s.a.createElement("div",{className:"card-body"},s.a.createElement("h4",{className:"card-title"},"Graticule"),s.a.createElement("div",{className:"map-dimension"},s.a.createElement(o.ComposableMap,{projectionConfig:{scale:147}},s.a.createElement(o.Graticule,{stroke:"#F53"}),s.a.createElement(o.Geographies,{geography:i},(function(e){return e.geographies.map((function(e){return s.a.createElement(o.Geography,{key:e.rsmKey,geography:e})}))}))))))),s.a.createElement("div",{className:"col-sm-6 grid-margin stretch-card"},s.a.createElement("div",{className:"card"},s.a.createElement("div",{className:"card-body"},s.a.createElement("h4",{className:"card-title"},"Geographies"),s.a.createElement("div",{className:"map-dimension"},s.a.createElement(o.ComposableMap,null,s.a.createElement(o.Geographies,{geography:i},(function(e){return e.geographies.map((function(e){return s.a.createElement(o.Geography,{key:e.rsmKey,geography:e})}))}))),"`"))))),s.a.createElement("div",{className:"row"},s.a.createElement("div",{className:"col-sm-6 grid-margin stretch-card"},s.a.createElement("div",{className:"card"},s.a.createElement("div",{className:"card-body"},s.a.createElement("h4",{className:"card-title"},"Marker"),s.a.createElement("div",{className:"map-dimension"},s.a.createElement(o.ComposableMap,{projection:"geoAlbers"},s.a.createElement(o.Geographies,{geography:i},(function(e){return e.geographies.map((function(e){return s.a.createElement(o.Geography,{key:e.rsmKey,geography:e,fill:"#DDD",stroke:"#FFF"})}))})),s.a.createElement(o.Marker,{coordinates:[-74.006,40.7128]},s.a.createElement("circle",{r:8,fill:"#F53"}))))))),s.a.createElement("div",{className:"col-sm-6 grid-margin stretch-card"},s.a.createElement("div",{className:"card"},s.a.createElement("div",{className:"card-body"},s.a.createElement("h4",{className:"card-title"},"Line"),s.a.createElement("div",{className:"map-dimension"},s.a.createElement(o.ComposableMap,{projection:"geoEqualEarth",projectionConfig:{scale:420,center:[-40,30]}},s.a.createElement(o.Graticule,{stroke:"#DDD"}),s.a.createElement(o.Geographies,{geography:i,fill:"#D6D6DA",stroke:"#FFFFFF",strokeWidth:.5},(function(e){return e.geographies.map((function(e){return s.a.createElement(o.Geography,{key:e.rsmKey,geography:e})}))})),s.a.createElement(o.Line,{from:[2.3522,48.8566],to:[-74.006,40.7128],stroke:"#FF5533",strokeWidth:4,strokeLinecap:"round"}))))))))}}]),r}(l.Component);a.default=p}}]);
//# sourceMappingURL=97.33a77b5b.chunk.js.map