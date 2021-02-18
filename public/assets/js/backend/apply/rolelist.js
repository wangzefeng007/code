define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'apply/rolelist/index' + location.search,
                    add_url: 'apply/rolelist/add',
                    edit_url: 'apply/rolelist/edit',
                    del_url: 'apply/rolelist/del',
                    multi_url: 'apply/rolelist/multi',
                    import_url: 'apply/rolelist/import',
                    table: 's_rolelist',
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
                        {field: 'ddid', title: __('Ddid')},
                        {field: 'name', title: __('Name'), operate: 'LIKE'},
                        {field: 'company_id', title: __('Company_id')},
                        {field: 'groupId', title: __('Groupid')},
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