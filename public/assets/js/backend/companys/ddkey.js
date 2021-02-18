define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'companys/ddkey/index' + location.search,
                    add_url: 'companys/ddkey/add',
                    edit_url: 'companys/ddkey/edit',
                    del_url: 'companys/ddkey/del',
                    multi_url: 'companys/ddkey/multi',
                    import_url: 'companys/ddkey/import',
                    table: 's_ddkey',
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
                        {field: 'ddkey', title: __('Ddkey'), operate: 'LIKE'},
                        {field: 'ddst', title: __('Ddst'), operate: 'LIKE'},
                        {field: 'company_id', title: __('Company_id')},
                        {field: 'agentId', title: __('Agentid'), operate: 'LIKE'},
                        {field: 'appSecret', title: __('Appsecret'), operate: 'LIKE'},
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