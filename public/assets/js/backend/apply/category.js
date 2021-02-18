define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'apply/category/index' + location.search,
                    add_url: 'apply/category/add',
                    edit_url: 'apply/category/edit',
                    del_url: 'apply/category/del',
                    multi_url: 'apply/category/multi',
                    import_url: 'apply/category/import',
                    table: 's_apply_category',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'weigh',
                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id')},
                        {field: 'cate_name', title: __('Cate_name'), operate: 'LIKE'},
                        {field: 'company_id', title: __('Company_id')},
                        {field: 'weigh', title: __('Weigh'), operate: false},
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