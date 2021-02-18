define(['jquery', 'toastr', 'form', 'template'], function ($, toastr, Form, Template) {
	var csmadmin = {
			mobilelogin:function(){
			    require(['https://g.alicdn.com/dingding/dingtalk-jsapi/2.10.3/dingtalk.open.js'], function (dd) {
			    	dd.ready(function() {
			    	    dd.runtime.permission.requestAuthCode({
			    	        corpId: Config.ddcorpId,
			    	        onSuccess: function(result) {
			    	        	Fast.api.ajax({
			    	        	    url: Fast.api.fixurl("csmding/ddlogin/mobilelogin"),
			    	        	    type: "post",
			    	        	    data:{code:result.code},
			    	        	}, function (data, ret) {
			    	        		window.location = Config.url;
			    	        	    return false;
			    	        	}, function (data, ret) {
			    	        	    return false;
			    	        	});
			    	        },
			    	        onFail : function(err) {
			    	        	alert(JSON.stringify(err));
			    	        }
			    	    });
			    	});
			    });
			},
			dobydd:function(){
				var that = this;
				var ddappid = $("#ddappid").val();
				var hosturl = $("#hosturl").val();
				that._requestParam(function(param){
					var url = param['url'];
					var getparam = (url==null||url=='')?'':'?url='+url;
					var url = hosturl+Fast.api.fixurl("csmding/ddlogin/dobyddtologin"+getparam);
					that.renderDDQrcode(ddappid,url);
				}); 
				
	        	$("#csmadmin-gotologin").click(function(){
	        		window.location = Fast.api.fixurl("index/login");
	        	});
			},
			_requestParam:function(func){
				var getParams = {};
			    var url = location.search; // 获取url中"?"符后的字串
			    if (url.indexOf("?") != -1) {  
			        var str = url.substr(1);  
			        strs = str.split("&");  
			        for(var i = 0; i < strs.length; i ++) {
			            getParams[strs[i].split("=")[0]]=decodeURI(strs[i].split("=")[1]);  
			        }  
			    }
			    func(getParams);
			},
			dobyddtologin:function(){
				var that = this;
				var captchaimg = Fast.api.cdnurl("/captcha.html");
				$("#modifymobilecaptchaimg").removeAttr("src").attr('src',captchaimg);
		        $("#modifymobilecaptchaimg").click(function(){
		            $("#modifymobilecaptchaimg").removeAttr("src").attr('src',captchaimg);
		        });
		        Form.api.bindevent($("form[role=form]"), function (data,ret) {
					that._requestParam(function(param){
						var url = param['url'];
						var url = (url==null||url=='')?Fast.api.fixurl("index/index"):unescape(url);
						window.location = url;
					});  	        	
		        },function(){
		        	$("#modifymobilecaptchaimg").removeAttr("src").attr('src',captchaimg);
		        });
				
			},
			/**
			 * 渲染钉钉登录二维码
			 * 
			 * @usage: var ddappid = $("#ddappid").val(); var hosturl =
			 *         $("#hosturl").val(); var url =
			 *         hosturl+Fast.api.fixurl("csmding/ddlogin/sendddlogintmpcode");
			 *         csmadmin.renderDDQrcode(ddappid,url);
			 */
			renderDDQrcode:function(ddappid,redirecturl){
				requirejs(['https://g.alicdn.com/dingding/dinglogin/0.0.5/ddLogin.js'], function(ddLogin){
					var url = encodeURIComponent(redirecturl);
					var goto = encodeURIComponent('https://oapi.dingtalk.com/connect/oauth2/sns_authorize?appid='+ddappid+'&response_type=code&scope=snsapi_login&state=STATE&redirect_uri='+url);
					var obj = DDLogin({
					     id:"login_container",
					     goto: goto,
					     style: "border:none;background-color:#FFFFFF;",
					     width : "365",
					     height: "400"
					 });
					var handleMessage = function (event) {
				        var origin = event.origin;
				        console.log("origin", event.origin);
				        if( origin == "https://login.dingtalk.com" ) { // 判断是否来自ddLogin扫码事件。
				            var loginTmpCode = event.data; // 拿到loginTmpCode后就可以在这里构造跳转链接进行跳转了
				            console.log("loginTmpCode", loginTmpCode);
				            window.location = 'https://oapi.dingtalk.com/connect/oauth2/sns_authorize?appid='+ddappid+'&response_type=code&scope=snsapi_login&state=STATE&redirect_uri=' + url + '&loginTmpCode=' + loginTmpCode;
				        }
					};
					if (typeof window.addEventListener != 'undefined') {
					    window.addEventListener('message', handleMessage, false);
					} else if (typeof window.attachEvent != 'undefined') {
					    window.attachEvent('onmessage', handleMessage);
					}
				});
			},
	};
	return csmadmin;
});