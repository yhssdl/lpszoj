/*! UEditorPlus v2.0.0*/
!function(){function initTabs(){for(var a=$G("tabhead").children,b=0;b<a.length;b++)domUtils.on(a[b],"click",function(a){var b=a.target||a.srcElement;setTabFocus(b.getAttribute("data-content-id"))});setTabFocus("upload")}function setTabFocus(a){if(a){var b,c,d=$G("tabhead").children;for(b=0;b<d.length;b++)c=d[b].getAttribute("data-content-id"),c==a?(domUtils.addClass(d[b],"focus"),domUtils.addClass($G(c),"focus")):(domUtils.removeClasses(d[b],"focus"),domUtils.removeClasses($G(c),"focus"));switch(a){case"upload":uploadFile=uploadFile||new UploadFile("queueList");break;case"online":onlineFile=onlineFile||new OnlineFile("fileList")}}}function initButtons(){dialog.onok=function(){for(var a,b=[],c=$G("tabhead").children,d=0;d<c.length;d++)if(domUtils.hasClass(c[d],"focus")){a=c[d].getAttribute("data-content-id");break}switch(a){case"upload":b=uploadFile.getInsertList();var e=uploadFile.getQueueCount();if(e)return $(".info","#queueList").html('<span style="color:red;">'+"还有2个未上传文件".replace(/[\d]/,e)+"</span>"),!1;break;case"online":b=onlineFile.getInsertList()}editor.execCommand("insertfile",b)}}function UploadFile(a){this.$wrap=a.constructor==String?$("#"+a):$(a),this.init()}function OnlineFile(a){this.container=utils.isString(a)?document.getElementById(a):a,this.init()}var uploadFile,onlineFile;window.onload=function(){initTabs(),initButtons()},UploadFile.prototype={init:function(){this.fileList=[],this.initContainer(),this.initUploader()},initContainer:function(){this.$queue=this.$wrap.find(".filelist")},initUploader:function(){function a(a){var b=h('<li id="'+a.id+'"><p class="title">'+a.name+'</p><p class="imgWrap"></p><p class="progress"><span></span></p></li>'),c=h('<div class="file-panel"><span class="cancel">'+lang.uploadDelete+'</span><span class="rotateRight">'+lang.uploadTurnRight+'</span><span class="rotateLeft">'+lang.uploadTurnLeft+"</span></div>").appendTo(b),d=b.find("p.progress span"),e=b.find("p.imgWrap"),g=h('<p class="error"></p>').hide().appendTo(b),i=function(a){switch(a){case"exceed_size":text=lang.errorExceedSize;break;case"interrupt":text=lang.errorInterrupt;break;case"http":text=lang.errorHttp;break;case"not_allow_type":text=lang.errorFileType;break;default:text=lang.errorUploadRetry}g.text(text).show()};"invalid"===a.getStatus()?i(a.statusText):(e.text(lang.uploadPreview),"|png|jpg|jpeg|bmp|gif|".indexOf("|"+a.ext.toLowerCase()+"|")==-1?e.empty().addClass("notimage").append('<i class="file-preview file-type-'+a.ext.toLowerCase()+'"></i><span class="file-title" title="'+a.name+'">'+a.name+"</span>"):browser.ie&&browser.version<=7?e.text(lang.uploadNoPreview):f.makeThumb(a,function(a,b){if(a||!b)e.text(lang.uploadNoPreview);else{var c=h('<img src="'+b+'">');e.empty().append(c),c.on("error",function(){e.text(lang.uploadNoPreview)})}},t,u),w[a.id]=[a.size,0],a.rotation=0,a.ext&&A.indexOf(a.ext.toLowerCase())!=-1||(i("not_allow_type"),f.removeFile(a))),a.on("statuschange",function(e,f){"progress"===f?d.hide().width(0):"queued"===f&&(b.off("mouseenter mouseleave"),c.remove()),"error"===e||"invalid"===e?(i(a.statusText),w[a.id][1]=1):"interrupt"===e?i("interrupt"):"queued"===e?w[a.id][1]=0:"progress"===e&&(g.hide(),d.css("display","block")),b.removeClass("state-"+f).addClass("state-"+e)}),b.on("mouseenter",function(){c.stop().animate({height:30})}),b.on("mouseleave",function(){c.stop().animate({height:0})}),c.on("click","span",function(){var b,c=h(this).index();switch(c){case 0:return void f.removeFile(a);case 1:a.rotation+=90;break;case 2:a.rotation-=90}x?(b="rotate("+a.rotation+"deg)",e.css({"-webkit-transform":b,"-mos-transform":b,"-o-transform":b,transform:b})):e.css("filter","progid:DXImageTransform.Microsoft.BasicImage(rotation="+~~(a.rotation/90%4+4)%4+")")}),b.insertBefore(n)}function b(a){var b=h("#"+a.id);delete w[a.id],c(),b.off().find(".file-panel").off().end().remove()}function c(){var a,b=0,c=0,d=p.children();h.each(w,function(a,d){c+=d[0],b+=d[0]*d[1]}),a=c?b/c:0,d.eq(0).text(Math.round(100*a)+"%"),d.eq(1).css("width",Math.round(100*a)+"%"),e()}function d(a,b){if(a!=v){var c=f.getStats();switch(m.removeClass("state-"+v),m.addClass("state-"+a),a){case"pedding":j.addClass("element-invisible"),k.addClass("element-invisible"),o.removeClass("element-invisible"),p.hide(),l.hide(),f.refresh();break;case"ready":o.addClass("element-invisible"),j.removeClass("element-invisible"),k.removeClass("element-invisible"),p.hide(),l.show(),m.text(lang.uploadStart),f.refresh();break;case"uploading":p.show(),l.hide(),m.text(lang.uploadPause);break;case"paused":p.show(),l.hide(),m.text(lang.uploadContinue);break;case"confirm":if(p.show(),l.hide(),m.text(lang.uploadStart),c=f.getStats(),c.successNum&&!c.uploadFailNum)return void d("finish");break;case"finish":p.hide(),l.show(),c.uploadFailNum?m.text(lang.uploadRetry):m.text(lang.uploadStart)}v=a,e()}g.getQueueCount()?m.removeClass("disabled"):m.addClass("disabled")}function e(){var a,b="";"ready"===v?b=lang.updateStatusReady.replace("_",q).replace("_KB",WebUploader.formatSize(r)):"confirm"===v?(a=f.getStats(),a.uploadFailNum&&(b=lang.updateStatusConfirm.replace("_",a.successNum).replace("_",a.successNum))):(a=f.getStats(),b=lang.updateStatusFinish.replace("_",q).replace("_KB",WebUploader.formatSize(r)).replace("_",a.successNum),a.uploadFailNum&&(b+=lang.updateStatusError.replace("_",a.uploadFailNum))),l.html(b)}var f,g=this,h=jQuery,i=g.$wrap,j=i.find(".filelist"),k=i.find(".statusBar"),l=k.find(".info"),m=i.find(".uploadBtn"),n=(i.find(".filePickerBtn"),i.find(".filePickerBlock")),o=i.find(".placeholder"),p=k.find(".progress").hide(),q=0,r=0,s=window.devicePixelRatio||1,t=113*s,u=113*s,v="",w={},x=function(){var a=document.createElement("p").style,b="transition"in a||"WebkitTransition"in a||"MozTransition"in a||"msTransition"in a||"OTransition"in a;return a=null,b}(),y=editor.getActionUrl(editor.getOpt("fileActionName")),z=editor.getOpt("fileMaxSize"),A=(editor.getOpt("fileAllowFiles")||[]).join("").replace(/\./g,",").replace(/^[,]/,"");return WebUploader.Uploader.support()?editor.getOpt("fileActionName")?(f=g.uploader=WebUploader.create({pick:{id:"#filePickerReady",label:lang.uploadSelectFile},swf:"../../third-party/webuploader/Uploader.swf",server:y,fileVal:editor.getOpt("fileFieldName"),duplicate:!0,fileSingleSizeLimit:z,compress:!1}),f.addButton({id:"#filePickerBlock"}),f.addButton({id:"#filePickerBtn",label:lang.uploadAddFile}),d("pedding"),f.on("fileQueued",function(b){b.ext&&A.indexOf(b.ext.toLowerCase())!=-1&&b.size<=z&&(q++,r+=b.size),1===q&&(o.addClass("element-invisible"),k.show()),a(b)}),f.on("fileDequeued",function(a){a.ext&&A.indexOf(a.ext.toLowerCase())!=-1&&a.size<=z&&(q--,r-=a.size),b(a),c()}),f.on("filesQueued",function(a){f.isInProgress()||"pedding"!=v&&"finish"!=v&&"confirm"!=v&&"ready"!=v||d("ready"),c()}),f.on("all",function(a,b){switch(a){case"uploadFinished":d("confirm",b);break;case"startUpload":var c=utils.serializeParam(editor.queryCommandValue("serverparam"))||"",e=utils.formatUrl(y+(y.indexOf("?")==-1?"?":"&")+"encode=utf-8&"+c);f.option("server",e),d("uploading",b);break;case"stopUpload":d("paused",b)}}),f.on("uploadBeforeSend",function(a,b,c){y.toLowerCase().indexOf("jsp")!=-1&&(c.X_Requested_With="XMLHttpRequest")}),f.on("uploadProgress",function(a,b){var d=h("#"+a.id),e=d.find(".progress span");e.css("width",100*b+"%"),w[a.id][1]=b,c()}),f.on("uploadSuccess",function(a,b){var c=h("#"+a.id);try{var d=b._raw||b,e=utils.str2json(d);"SUCCESS"==e.state?(g.fileList.push(e),c.append('<span class="success"></span>')):c.find(".error").text(e.state).show()}catch(f){c.find(".error").text(lang.errorServerUpload).show()}}),f.on("uploadError",function(a,b){}),f.on("error",function(b,c){"Q_TYPE_DENIED"!=b&&"F_EXCEED_SIZE"!=b||a(c)}),f.on("uploadComplete",function(a,b){}),m.on("click",function(){return!h(this).hasClass("disabled")&&void("ready"===v?f.upload():"paused"===v?f.upload():"uploading"===v&&f.stop())}),m.addClass("state-"+v),void c()):void h("#filePickerReady").after(h("<div>").html(lang.errorLoadConfig)).hide():void h("#filePickerReady").after(h("<div>").html(lang.errorNotSupport)).hide()},getQueueCount:function(){var a,b,c,d=0,e=this.uploader.getFiles();for(b=0;a=e[b++];)c=a.getStatus(),"queued"!=c&&"uploading"!=c&&"progress"!=c||d++;return d},getInsertList:function(){var a,b,c,d=[],e=editor.getOpt("fileUrlPrefix");for(a=0;a<this.fileList.length;a++)c=this.fileList[a],b=c.url,d.push({title:c.original||b.substr(b.lastIndexOf("/")+1),url:e+b});return d}},OnlineFile.prototype={init:function(){this.initContainer(),this.initEvents(),this.initData()},initContainer:function(){this.container.innerHTML="",this.list=document.createElement("ul"),this.clearFloat=document.createElement("li"),domUtils.addClass(this.list,"list"),domUtils.addClass(this.clearFloat,"clearFloat"),this.list.appendChild(this.clearFloat),this.container.appendChild(this.list)},initEvents:function(){var a=this;domUtils.on($G("fileList"),"scroll",function(b){var c=this;c.scrollHeight-(c.offsetHeight+c.scrollTop)<10&&a.getFileData()}),domUtils.on(this.list,"click",function(a){var b=a.target||a.srcElement,c=b.parentNode;"li"==c.tagName.toLowerCase()&&(domUtils.hasClass(c,"selected")?domUtils.removeClasses(c,"selected"):domUtils.addClass(c,"selected"))})},initData:function(){this.state=0,this.listSize=editor.getOpt("fileManagerListSize"),this.listIndex=0,this.listEnd=!1,this.getFileData()},getFileData:function(){var _this=this;_this.listEnd||this.isLoadingData||(this.isLoadingData=!0,ajax.request(editor.getActionUrl(editor.getOpt("fileManagerActionName")),{timeout:1e5,data:utils.extend({start:this.listIndex,size:this.listSize},editor.queryCommandValue("serverparam")),method:"get",onsuccess:function(r){try{var json=eval("("+r.responseText+")");"SUCCESS"==json.state&&(_this.pushData(json.list),_this.listIndex=parseInt(json.start)+parseInt(json.list.length),_this.listIndex>=json.total&&(_this.listEnd=!0),_this.isLoadingData=!1)}catch(e){if(r.responseText.indexOf("ue_separate_ue")!=-1){var list=r.responseText.split(r.responseText);_this.pushData(list),_this.listIndex=parseInt(list.length),_this.listEnd=!0,_this.isLoadingData=!1}}},onerror:function(){_this.isLoadingData=!1}}))},pushData:function(a){var b,c,d,e,f,g=this,h=editor.getOpt("fileManagerUrlPrefix");for(b=0;b<a.length;b++)if(a[b]&&a[b].url){if(c=document.createElement("li"),f=document.createElement("span"),d=a[b].url.substr(a[b].url.lastIndexOf(".")+1),"png|jpg|jpeg|gif|bmp".indexOf(d)!=-1)e=document.createElement("img"),domUtils.on(e,"load",function(a){return function(){g.scale(a,a.parentNode.offsetWidth,a.parentNode.offsetHeight)}}(e)),e.width=113,e.setAttribute("src",h+a[b].url+(a[b].url.indexOf("?")==-1?"?noCache=":"&noCache=")+(+new Date).toString(36));else{var i=document.createElement("i"),j=document.createElement("span");j.innerHTML=a[b].url.substr(a[b].url.lastIndexOf("/")+1),e=document.createElement("div"),e.appendChild(i),e.appendChild(j),domUtils.addClass(e,"file-wrapper"),domUtils.addClass(j,"file-title"),domUtils.addClass(i,"file-type-"+d),domUtils.addClass(i,"file-preview")}domUtils.addClass(f,"icon"),c.setAttribute("data-url",h+a[b].url),a[b].original&&c.setAttribute("data-title",a[b].original),c.appendChild(e),c.appendChild(f),this.list.insertBefore(c,this.clearFloat)}},scale:function(a,b,c,d){var e=a.width,f=a.height;"justify"==d?e>=f?(a.width=b,a.height=c*f/e,a.style.marginLeft="-"+parseInt((a.width-b)/2)+"px"):(a.width=b*e/f,a.height=c,a.style.marginTop="-"+parseInt((a.height-c)/2)+"px"):e>=f?(a.width=b*e/f,a.height=c,a.style.marginLeft="-"+parseInt((a.width-b)/2)+"px"):(a.width=b,a.height=c*f/e,a.style.marginTop="-"+parseInt((a.height-c)/2)+"px")},getInsertList:function(){var a,b=this.list.children,c=[];for(a=0;a<b.length;a++)if(domUtils.hasClass(b[a],"selected")){var d=b[a].getAttribute("data-url"),e=b[a].getAttribute("data-title")||d.substr(d.lastIndexOf("/")+1);c.push({title:e,url:d})}return c}}}();