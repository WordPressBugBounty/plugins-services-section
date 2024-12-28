(()=>{"use strict";var t={744:(t,e,n)=>{var o=n(795);e.H=o.createRoot,o.hydrateRoot},795:t=>{t.exports=window.ReactDOM}},e={};const n=window.React;var o=function n(o){var r=e[o];if(void 0!==r)return r.exports;var i=e[o]={exports:{}};return t[o](i,i.exports,n),i.exports}(744);const r=(t,e)=>{return null==(n=e)||""===n||Array.isArray(n)&&0===n.length||"object"==typeof n&&0===Object.keys(n).length||"string"==typeof n&&""===n.trim()||"number"==typeof n&&0===n?"":`${t}: ${e};`;var n},i=(t,e=!0,n=!0,o=!0)=>{const{type:i="solid",color:a="",gradient:s="",image:$={},position:l="",attachment:d="",repeat:c="",size:p="",overlayColor:g=""}=t||{};return"gradient"===i&&n?r("background",s):"image"===i&&o?`background: url(${$?.url});\n\t\t\t\t${r("background-color",g)}\n\t\t\t\t${r("background-position",l)}\n\t\t\t\t${r("background-size",p)}\n\t\t\t\t${r("background-repeat",c)}\n\t\t\t\t${r("background-attachment",d)}\n\t\t\t\t${r("background-repeat",c)}\n\t\t\t\tbackground-blend-mode: overlay;`:e&&r("background",a)},a=t=>{const{width:e="0px",style:n="solid",color:o="",side:r="all",radius:i="0px"}=t||{},a=t=>{const e=r?.toLowerCase();return e?.includes("all")||e?.includes(t)},s=`${e} ${n} ${o}`,$=`\n\t\t${"0px"!==e&&e?["top","right","bottom","left"].map((t=>a(t)?`border-${t}: ${s};`:"")).join(""):""}\n\t\t${i?`border-radius: ${i};`:""}\n\t`;return $},s=(t,e="box")=>{const{hOffset:n="0px",vOffset:o="0px",blur:r="0px",spreed:i="0px",color:a="#7090b0",isInset:s=!1}=t||{},$=`${n} ${o} ${r}`;return("text"===e?`${$} ${a}`:`${$} ${i} ${a} ${s?"inset":""}`)||"none"},$=t=>{const{side:e=2,vertical:n="0px",horizontal:o="0px",top:r="0px",right:i="0px",bottom:a="0px",left:s="0px"}=t||{};return 2===e?`${n} ${o}`:`${r} ${i} ${a} ${s}`},l=(t,e,n=!0)=>{const{fontFamily:o="Default",fontCategory:i="sans-serif",fontVariant:a=400,fontWeight:s=400,isUploadFont:$=!0,fontSize:l={desktop:15,tablet:15,mobile:15},fontStyle:d="normal",textTransform:c="none",textDecoration:p="auto",lineHeight:g="135%",letterSpace:u="0px"}=e||{},b=!n||!o||"Default"===o,m=l?.desktop||l,x=l?.tablet||m,f=l?.mobile||x,y=`\n\t\t${b?"":`font-family: '${o}', ${i};`}\n\t\t${r("font-weight",s)}\n\t\t${r("font-size",m?`${m}px`:"")}\n\t\t${r("font-style",d)}\n\t\t${r("text-transform",c)}\n\t\t${r("text-decoration",p)}\n\t\t${r("line-height",g)}\n\t\t${r("letter-spacing",u)}\n\t`,h=a&&400!==a?"400i"===a?":ital@1":a?.includes("00i")?`: ital, wght@1, ${a?.replace("00i","00")} `:`: wght@${a} `:"",k=b?"":`https://fonts.googleapis.com/css2?family=${o?.split(" ").join("+")}${h.replace(/ /g,"")}&display=swap`;return{googleFontLink:!$||b?"":`@import url(${k});`,styles:`${t}{\n\t\t\t${y}\n\t\t}\n\t\t@media only screen and (min-width: 641px) and (max-width: 1024px) {\n\t\t\t${t}{\n\t\t\t\t${r("font-size",x?`${x}px`:"")}\n\t\t\t}\n\t\t}\n\t\t@media only screen and (max-width: 640px) {\n\t\t\t${t}{\n\t\t\t\t${r("font-size",f?`${f}px`:"")}\n\t\t\t}\n\t\t}`.replace(/\s+/g," ").trim()}},d="ssbServices",c=({attributes:t,id:e,isBackend:o=!1})=>{const{columnGap:r,rowGap:c,background:p,textAlign:g,itemHeight:u,itemPadding:b,itemBorder:m,itemShadow:x,iconPadding:f,iconMargin:y,titleTypo:h,titleMargin:k,descTypo:v}=t,w=`#${e} .${d}`,S=`${w} .ssbService`,L=o?`${w} .block-editor-inner-blocks .block-editor-block-list__layout{\n\t\tgrid-gap: ${c} ${r};\n\t}`:`${w}{\n\t\tgrid-gap: ${c} ${r};\n\t}`;return(0,n.createElement)("style",{dangerouslySetInnerHTML:{__html:`\n\t\t${l("",h)?.googleFontLink}\n\t\t${l("",v)?.googleFontLink}\n\t\t${l(`${S} .title`,h)?.styles}\n\t\t${l(`${S} .description`,v)?.styles}\n\n\t\t${w}{\n\t\t\t${i(p)}\n\t\t}\n\t\t${L}\n\n\t\t${S}{\n\t\t\ttext-align: ${g};\n\t\t\tmin-height: ${u};\n\t\t\tpadding: ${$(b)};\n\t\t\t${a(m)||"border-radius: 15px;"}\n\t\t\tbox-shadow: ${s(x)};\n\t\t}\n\t\t${S} .bgLayer{\n\t\t\tborder-radius: ${m?.radius||"15px"}\n\t\t}\n\t\t${S} .icon{\n\t\t\tpadding: ${$(f)};\n\t\t\tmargin: ${$(y)};\n\t\t}\n\t\t${S} .title{\n\t\t\tmargin: ${$(k)};\n\t\t}\n\t\t`.replace(/\s+/g," ")}})};document.addEventListener("DOMContentLoaded",(()=>{document.querySelectorAll(".wp-block-services-section-services").forEach((t=>{const e=JSON.parse(t.dataset.attributes),r=document.querySelector(`#${t.id} .${d}Style`);(0,o.H)(r).render((0,n.createElement)(c,{attributes:e,id:t.id})),t?.removeAttribute("data-attributes")}))}))})();