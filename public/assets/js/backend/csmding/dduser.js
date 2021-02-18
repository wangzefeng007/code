define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        _loadUser:function(departids){
          	Fast.api.ajax({
           	    url: Fast.api.fixurl("csmding/dduser/getUsersByDepartsAjax"),
           	    type: "get",
           	    data:{departids:departids},
           	}, function (data, ret) {
               	$("input[name=id]").val(data.userids);
               	$("input[name=id-operate]").val('in');
               	var table = $("#table");
                table.bootstrapTable('refresh', {});
           	    return false;
           	}, function (data, ret) {
          	    return false;
           	});  	
        },
        index: function () {
        	var that = this;
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'csmding/dduser/index' + location.search,
//                    add_url: 'csmding/dduser/add',
//                    edit_url: 'csmding/dduser/edit',
//                    del_url: 'csmding/dduser/del',
//                    multi_url: 'csmding/dduser/multi',
                    table: 'csmding_dduser',
                }
            });
            
            require(['jstree'], function () {
                $('#channeltree').on("changed.jstree", function (e, data) {
                	console.log(e);
                	console.log(data);
                	if(data.selected!=null && data.selected.length>0){
                    	var departids = data.selected.join(",");//+","+data.node.parents.join(",")
                    	departids = departids.replace(',#','');
                    	console.log(departids);
                    	that._loadUser(departids);
              		
                	}else{
                		console.log('-=---');
                	}
                    return false;
                });           	
                $('#channeltree').jstree({
                    "themes": {
                        "stripes": true
                    },
                    "checkbox": {
                        "keep_selected_style": true,
                    },
                    'plugins': [ ],
                    "core": {
                        "multiple": false,
                        'check_callback': true,
                        "data":{
                        	'url' : Fast.api.fixurl('csmding/dduser/getdeparttreeAjax'),
                        }
                    }
                });
            });
            
            $(".btn-sync").click(function(){

            	Fast.api.open(Fast.api.cdnurl("/addons/csmding/cli/index"), "从钉钉端同步到本地", );
            });
            

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'order',
                order:"desc",
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                       {field: 'name', title: __('Name')},
//                        {field: 'tel', title: __('Tel')},
//                        {field: 'workplace', title: __('Workplace')},
                        {field: 'mobile', title: __('Mobile')},
                        {field: 'email', title: __('Email')},
                        {field: 'orgemail', title: __('Orgemail')},
//                        {field: 'orderindepts', title: __('Orderindepts')},
//                        {field: 'active', title: __('Active'), searchList: {"true":__('Active true'),"false":__('Active false')}, formatter: Table.api.formatter.normal},
//                        {field: 'isadmin', title: __('Isadmin'), searchList: {"true":__('Isadmin true'),"false":__('Isadmin false')}, formatter: Table.api.formatter.normal},
//                        {field: 'isboss', title: __('Isboss'), searchList: {"true":__('Isboss true'),"false":__('Isboss false')}, formatter: Table.api.formatter.normal},
//                        {field: 'isleaderindepts', title: __('Isleaderindepts')},
//                        {field: 'ishide', title: __('Ishide'), searchList: {"true":__('Ishide true'),"false":__('Ishide false')}, formatter: Table.api.formatter.normal},
                        {field: 'position', title: __('Position')},
                        {field: 'avatar', title: __('Avatar'), events: Table.api.events.image, formatter: Table.api.formatter.image},
//                        {field: 'hireddate', title: __('Hireddate')},
//                        {field: 'jobnumber', title: __('Jobnumber')},
//                        {field: 'issenior', title: __('Issenior'), searchList: {"true":__('Issenior true'),"false":__('Issenior false')}, formatter: Table.api.formatter.normal},
//                        {field: 'statecode', title: __('Statecode')},
//                        {field: 'order', title: __('Order')},
//                        {field: 'isleader', title: __('Isleader'), searchList: {"true":__('True'),"false":__('False')}, formatter: Table.api.formatter.normal},
//                        {field: 'status', title: __('Status'), searchList: {"normal":__('Normal'),"hidden":__('Hidden')}, formatter: Table.api.formatter.status},
                        {field: 'userid', title: 'Userid'},
//                        {field: 'unionid', title: 'unionid'},
 //                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
//                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'faadmin_id', title: __('Faadmin_id')},
//                        {field: 'fauser_id', title: __('Fauser_id')},
                        {field: 'faisactivie', title: '是否激活', searchList: {"true":__('Faisactivie true'),"false":__('Faisactivie false')}, formatter: Table.api.formatter.normal},
//                        {field: 'faisfilladmin', title: __('Faisfilladmin'), searchList: {"true":__('Faisfilladmin true'),"false":__('Faisfilladmin false')}, formatter: Table.api.formatter.normal},
//                        {field: 'b1', title: __('B1')},
//                        {field: 'b2', title: __('B2')},
//                        {field: 'b3', title: __('B3')},
//                        {field: 'b4', title: __('B4')},
//                        {field: 'b5', title: __('B5')},
//                        {field: 'b6', title: __('B6')},
//                        {field: 'b7', title: __('B7')},
//                        {field: 'b8', title: __('B8')},
//                        {field: 'b9', title: __('B9')},
//                        {field: 'remoteversion', title: __('Remoteversion')},
//                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                        {
                            field: 'operate',
                            title: __('Operate'),
                            table: table, events: Table.api.events.operate,
                            formatter: Table.api.formatter.operate,
                            //formatter: Table.api.formatter.buttons,
                            buttons: [
                                {
                                    name: 'bind',
                                    text: __('绑定账号'),
                                    classname: 'btn btn-xs btn-success btn-dialog',
                                    icon: 'fa fa-list',
                                    url: 'csmding/dduser/selectuser',
                                    callback: function (data) {
                                    	Fast.api.close(data);
                                    },
                                    visible: function (row) {
                                        //返回true时按钮显示,返回false隐藏
                                        return true;
                                    }
                                },                         
                            ]
                        }  
                    ]
                ], 
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        },
        selectuser:function(){
        	var that = this;
        	Form.api.bindevent($("form[role=form]"), function (data,ret) {
        		Fast.api.close();	
        	});
        },
    };
    return Controller;
});