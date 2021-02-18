define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'csmding/msgsendtask/index' + location.search,
                    add_url: 'csmding/msgsendtask/add',
                    edit_url: 'csmding/msgsendtask/edit',
                    del_url: 'csmding/msgsendtask/del',
                    multi_url: 'csmding/msgsendtask/multi',
                    table: 'csmding_msgsendtask',
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
                        {field: 'csmding_msgsend_id', title: __('Csmding_msgsend_id')},
                        {field: 'msgtype', title: __('Msgtype'), searchList: {"oa":__('Oa')}, formatter: Table.api.formatter.normal},
                        {field: 'agent_id', title: __('Agent_id')},
                        {field: 'userid_list', title: __('Userid_list')},
                        {field: 'dept_id_list', title: __('Dept_id_list')},
                        {field: 'to_all_user', title: __('To_all_user')},
                        {field: 'msgtaskid', title: __('Msgtaskid')},
                        {field: 'msgtaskerrcode', title: __('Msgtaskerrcode')},
                        {field: 'msgtaskerrmsg', title: __('Msgtaskerrmsg')},
                        {field: 'msgtaskprogress', title: __('Msgtaskprogress')},
                        {field: 'msgtaskstatus', title: __('Msgtaskstatus'), searchList: {"0":__('Msgtaskstatus 0'),"1":__('Msgtaskstatus 1'),"2":__('Msgtaskstatus 2')}, formatter: Table.api.formatter.status},
                        {field: 'sendtime', title: __('Sendtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
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
        }
    };
    return Controller;
});