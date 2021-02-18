define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'diytable/conventionaltype/index' + location.search,
                    add_url: 'diytable/conventionaltype/add',
                    edit_url: 'diytable/conventionaltype/edit',
                    del_url: 'diytable/conventionaltype/del',
                    multi_url: 'diytable/conventionaltype/multi',
                    import_url: 'diytable/conventionaltype/import',
                    table: 's_diy_table_conventional_type',
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
                        {field: 'type_name', title: __('Type_name'), operate: 'LIKE'},
                        {field: 'type_annotation', title: __('Type_annotation'), operate: 'LIKE'},
                        {field: 'type_key', title: __('Type_key'), operate: 'LIKE'},
                        {field: 'type_value', title: __('Type_value'), operate: 'LIKE'},
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