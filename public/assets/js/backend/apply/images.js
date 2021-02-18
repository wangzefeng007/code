define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'apply/images/index' + location.search,
                    add_url: 'apply/images/add',
                    edit_url: 'apply/images/edit',
                    del_url: 'apply/images/del',
                    multi_url: 'apply/images/multi',
                    import_url: 'apply/images/import',
                    table: 's_images',
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
                        {field: 'local_path_image', title: __('Local_path_image'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'oss_path_image', title: __('Oss_path_image'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'type_status', title: __('Type_status'), searchList: {"1":__('Type_status 1'),"2":__('Type_status 2'),"3":__('Type_status 3')}, formatter: Table.api.formatter.status},
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