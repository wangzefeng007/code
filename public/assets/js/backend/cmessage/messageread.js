define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'cmessage/messageread/index' + location.search,
                    add_url: 'cmessage/messageread/add',
                    edit_url: 'cmessage/messageread/edit',
                    del_url: 'cmessage/messageread/del',
                    multi_url: 'cmessage/messageread/multi',
                    import_url: 'cmessage/messageread/import',
                    table: 's_company_message_reads',
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
                        {field: 'suid', title: __('Suid')},
                        {field: 'cmid', title: __('Cmid')},
                        {field: 'is_read', title: __('Is_read'), searchList: {"0":__('Is_read 0'),"1":__('Is_read 1')}, formatter: Table.api.formatter.normal},
                        {field: 'readtime', title: __('Readtime'), operate:'RANGE', addclass:'datetimerange', autocomplete:false, formatter: Table.api.formatter.datetime},
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