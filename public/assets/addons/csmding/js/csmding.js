define(['jquery', 'toastr', 'form', 'template'], function ($, toastr, Form, Template) {
	var Controller = {
		initqd:false,
		mounted: function () {
			var that = this;
			that._render();
			that._bind();
		},
		_render: function () {
			console.log('_render');
			var that = this;
			var isdingtalk = navigator.userAgent.indexOf('DingTalk') > -1;
			if(isdingtalk===true){
				Layer.msg('请等待，正在获取钉钉用户信息');
				that._requestParam(function(param){
					var url = param['url'];
					var urlparam = (url==null||url=='')?('?url='+Fast.api.fixurl('index/login')):'?url='+url;
					window.location = Fast.api.fixurl("csmding/ddlogin/mobilelogin"+urlparam);
				});
			}
			//csmadmin安装，则链接显示在csdamin的register画面中
			if(window.csmadmincfg==null){
				//如果csmadmin未安装，则显示钉钉登录
				$(".login-form").after('<center><a href="#" class="csmding-ddlogin">钉钉扫码登录</a></center>');	
			}
			
		},
		_bind: function () {
			var that = this;
			console.log('_bind');
			$(".csmding-ddlogin").click(function(){
				that._requestParam(function(param){
					var url = param['url'];
					var urlparam = (url==null||url=='')?'':'?url='+url;
					window.location = Fast.api.fixurl("csmding/ddlogin/dobydd"+urlparam);
				});
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
	};
	return Controller;
});