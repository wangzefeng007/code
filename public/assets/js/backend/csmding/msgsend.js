define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'csmding/msgsend/index' + location.search,
                    add_url: 'csmding/msgsend/add',
                    edit_url: 'csmding/msgsend/edit',
                    del_url: 'csmding/msgsend/del',
                    multi_url: 'csmding/msgsend/multi',
                    table: 'csmding_msgsend',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'name', title: __('Name')},
//                        {field: 'msgtype', title: __('Msgtype'), searchList: {"oa":__('Oa')}, formatter: Table.api.formatter.normal},
//                        {field: 'oaheadtext', title: __('Oaheadtext')},
//                        {field: 'oabodytitle', title: __('Oabodytitle')},
//                        {field: 'oaheadbgcolor', title: __('Oaheadbgcolor')},
//                        {field: 'oamessageurl', title: __('Oamessageurl'), formatter: Table.api.formatter.url},
//                        {field: 'oabodyimage', title: __('Oabodyimage'), events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'sendrange', title: __('Sendrange'), searchList: {"all":__('Sendrange all'),"byuser":__('Sendrange byuser'),"bydepart":__('Sendrange bydepart')}, formatter: Table.api.formatter.normal},
//                        {field: 'csmding_dduser_ids', title: __('Csmding_dduser_ids')},
//                        {field: 'csmding_dddepartment_ids', title: __('Csmding_dddepartment_ids')},
//                        {field: 'msgtaskid', title: __('Msgtaskid')},
//                        {field: 'admin_id', title: __('Admin_id')},
                        {field: 'hassend', title: __('Hassend'), searchList: {"Y":__('Hassend y'),"N":__('Hassend n')}, formatter: Table.api.formatter.normal},
                        {field: 'sendtime', title: __('Sendtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'msgtaskerrcode', title:'发送成功', searchList: {"1":'发送失败',"0":'发送成功'}, formatter: Table.api.formatter.normal},
                        {field: 'msgtaskerrmsg', title: '发送失败原因'},
//                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
//                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        //{field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate},
                        {
                            field: 'operate',
                            width: "120px",
                            title: __('按钮组'),
                            table: table,
                            events: Table.api.events.operate,
                            buttons: [
                                {
                                    name: 'ajax',
                                    text: '推送',
                                    title: '推送',
                                    classname: 'btn btn-xs btn-success btn-magic btn-ajax',
                                    icon: 'fa fa-magic',
                                    url: 'csmding/msgsend/sendmsg',
                                    confirm: '确认推送',
                                    success: function (data, ret) {
                                        Layer.alert('推送成功！');
                                    },
                                    error: function (data, ret) {
                                        Layer.alert(ret.msg);
                                        return false;
                                    }
                                },
                            ],
                            formatter: Table.api.formatter.operate
                        }
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
            $("#c-sendrange").change(function(){
            	var val = $(this).val();
            	switch(val){
            	case "all":
            		$(".div-Csmding_dduser_ids").addClass("hidden");
            		$(".div-Csmding_dddepartment_ids").addClass("hidden");
            		break;
            	case "byuser":
            		$(".div-Csmding_dduser_ids").removeClass("hidden");
            		$(".div-Csmding_dddepartment_ids").addClass("hidden");
            		break;
            	case "bydepart":
            		$(".div-Csmding_dduser_ids").addClass("hidden");
            		$(".div-Csmding_dddepartment_ids").removeClass("hidden");
            		break;        		
            	}
            });
            
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});