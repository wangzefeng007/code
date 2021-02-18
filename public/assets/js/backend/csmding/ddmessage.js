define(['jquery', 'bootstrap', 'backend', 'table', 'form'],
function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
 
        },
        media:function(){
        	Form.api.bindevent($("form[role=form]"));
        	$(".backbtn").click(function(){
        		window.location = Fast.api.fixurl("csmding/ddportal/index");
        	});
 
        }
    };
    return Controller;
});