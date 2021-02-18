define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'apply/rolegroup/index' + location.search,
                    add_url: 'apply/rolegroup/add',
                    edit_url: 'apply/rolegroup/edit',
                    del_url: 'apply/rolegroup/del',
                    multi_url: 'apply/rolegroup/multi',
                    import_url: 'apply/rolegroup/import',
                    table: 's_rolegroup',
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
                        {field: 'company_id', title: __('Company_id')},
                        {field: 'name', title: __('Name'), operate: 'LIKE'},
                        {field: 'groupId', title: __('Groupid')},
                        {field: 'scompany.name', title: __('Scompany.name'), operate: 'LIKE'},
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