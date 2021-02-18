define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
        	Form.api.bindevent($("form[role=form]"), function (data,ret) {
        		console.log(data);
        		console.log(ret);	
        		$("#pre-result").html(data.result);
        		$("#pre-generatecode").html(data.generatecode);
        	});	
        	$(".a-help").click(function(){
        		Fast.api.open(Fast.api.fixurl(Config.helpurl), "使用说明");
        	});
        	
        	var jsapidemourl = Fast.api.fixurl('csmding/dingtest/jstest');
        	$("#a-jsapidemo").text(jsapidemourl);
        	$("#a-jsapidemo").attr('href',jsapidemourl);
        },
        help:function(){
        	
        },
    };
    return Controller;
});