define(
		[ 'jquery', 'bootstrap', 'backend', 'table', 'form' ],
		function($, undefined, Backend, Table, Form) {

			var Controller = {
				jstest : function() {

					require(
							[ 'https://g.alicdn.com/dingding/dingtalk-jsapi/2.10.3/dingtalk.open.js' ],
							function(dd) {
								dd.config({
									agentId : Config.agentId, // 必填，微应用ID
									corpId : Config.corpId,// 必填，企业ID
									timeStamp : Config.timeStamp, // 必填，生成签名的时间戳
									nonceStr : Config.nonceStr, // 必填，生成签名的随机串
									signature : Config.signature, // 必填，签名
									type : 0, // 选填。0表示微应用的jsapi,1表示服务窗的jsapi；不填默认为0。该参数从dingtalk.js的0.8.3版本开始支持
									jsApiList : [ 'runtime.info',
											'device.base.getUUID',
											'biz.contact.complexPicker',
											'device.geolocation.get',
											'biz.contact.choose',
											'device.notification.confirm',
											'device.notification.alert',
											'device.notification.prompt',
											'biz.ding.post',
											'biz.util.openLink', ]
								// 必填，需要使用的jsapi列表，注意：不要带dd。
								});

								dd.ready(function() {
									alert('钉钉JSAPI初始化完成');
									$(".btn-getUUID").disabled = false;
									$(".btn-getUUID").click(function() {
										dd.device.base.getUUID({
											onSuccess : function(data) {
												alert(JSON.stringify(data));
											},
											onFail : function(error) {
												alert(JSON.stringify(error));
											}
										});
									});
									$(".btn-complexPicker").disabled = false;
									$(".btn-complexPicker").click(function() {
										dd.biz.contact.complexPicker({
											title : "测试标题", // 标题
											corpId : Config.corpId, // 企业的corpId
											multiple : true, // 是否多选
											limitTips : "超出了", // 超过限定人数返回提示
											maxUsers : 1000, // 最大可选人数
											pickedUsers : [], // 已选用户
											pickedDepartments : [], // 已选部门
											disabledUsers : [], // 不可选用户
											disabledDepartments : [], // 不可选部门
											requiredUsers : [], // 必选用户（不可取消选中状态）
											requiredDepartments : [], // 必选部门（不可取消选中状态）
											appId : 158, // 微应用Id，企业内部应用查看AgentId
											permissionType : "xxx", // 可添加权限校验，选人权限，目前只有GLOBAL这个参数
											responseUserOnly : false, // 返回人，或者返回人和部门
											startWithDepartmentId : 0, // 仅支持0和-1
											onSuccess : function(result) {
												alert(JSON.stringify(result));
											},
											onFail : function(err) {
												alert(JSON.stringify(err));
											}
										});
									});

									$(".btn-vibrate").disabled = false;
									$(".btn-vibrate").click(function() {
										dd.device.notification.vibrate({
											duration : 300, // 震动时间，android可配置
															// iOS忽略
											onSuccess : function(result) {
											},
											onFail : function(err) {
											}
										});
									});

									$(".btn-scan").disabled = false;
									$(".btn-scan").click(function() {
										dd.biz.util.scan({
											type : String, // type 为
															// all、qrCode、barCode，默认是all。
											onSuccess : function(data) {
												alert(JSON.stringify(result));
											},
											onFail : function(err) {
											}
										});
									});

									$(".btn-geolocation").disabled = false;
									$(".btn-geolocation").click(function() {
										dd.device.geolocation.get({
											targetAccuracy : Number,
											coordinate : Number,
											withReGeocode : Boolean,
											useCache : true, // 默认是true，如果需要频繁获取地理位置，请设置false
											onSuccess : function(result) {
												alert(JSON.stringify(result));
											},
											onFail : function(err) {
											}
										});
									});

									dd.error(function(error) {
										alert('xxx');
										alert('dd error: '
												+ JSON.stringify(error));
									});
								});

							});
				},
				webservicetest:function(){
					$(".btn-accessToken").click(function(){
						Fast.api.ajax({
						    url: Fast.api.fixurl("csmding/dingtest/testaccesstoken"),
						    type: "get",
						}, function (data, ret) {
							Layer.msg('AccessToken=<BR>'+data.accesstoken);
						    return false;
						}, function (data, ret) {
						    return false;
						});	
					});
					
					$(".btn-getAdmin").click(function(){
						Fast.api.ajax({
						    url: Fast.api.fixurl("csmding/dingtest/testgetadmin"),
						    type: "get",
						}, function (data, ret) {
							Layer.msg(data.resp);
						    return false;
						}, function (data, ret) {
						    return false;
						});	
					});
					
					$(".btn-department").click(function(){
						Fast.api.ajax({
						    url: Fast.api.fixurl("csmding/dingtest/testdepartment"),
						    type: "get",
						}, function (data, ret) {
							Layer.msg(data.resp);
						    return false;
						}, function (data, ret) {
						    return false;
						});	
					});
				},
			};
			return Controller;
		});