define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'csmding/dduser2depart/index' + location.search,
                    add_url: 'csmding/dduser2depart/add',
                    edit_url: 'csmding/dduser2depart/edit',
                    del_url: 'csmding/dduser2depart/del',
                    multi_url: 'csmding/dduser2depart/multi',
                    table: 'csmding_dduser2depart',
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
                        {field: 'csmding_dduser_id', title: __('Csmding_dduser_id')},
                        {field: 'csmding_dddepartment_id', title: __('Csmding_dddepartment_id')},
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