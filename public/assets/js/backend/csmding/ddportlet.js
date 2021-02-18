define(['jquery', 'bootstrap', 'backend', 'table', 'form'],
function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            $('.carousel').carousel({
                interval: 5000
            });
            $(".articlehref").click(function(){
            	var id = $(this).attr("data-id");
            	var url = $(this).attr("data-url");
            	var type = $(this).attr("data-type");
            	switch(type){
            	case "content":
            		window.open(Fast.api.fixurl('csmding/ddportlet/article?id='+id));
            		break;
            	case "url":
            		window.open(url);
            		break;
            	}
            });
 
            $(".carousel-img").click(function(){
            	var url = Fast.api.fixurl($(this).attr("data-url"));
            	window.open(url);
            });
            
            $(".div-func").click(function(){
            	var url = $(this).attr("data-url");
            	window.open(Fast.api.fixurl(url));
            });

        },
        mngapps:function(){
        	$(".backbtn").click(function(){
        		window.location = Fast.api.fixurl("csmding/ddportal/index");
        	});
 
        },
        article:function(){
        	
        }
    };
    return Controller;
});