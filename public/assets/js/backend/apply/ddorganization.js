define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'apply/ddorganization/index' + location.search,
                    add_url: 'apply/ddorganization/add',
                    edit_url: 'apply/ddorganization/edit',
                    del_url: 'apply/ddorganization/del',
                    multi_url: 'apply/ddorganization/multi',
                    import_url: 'apply/ddorganization/import',
                    table: 's_user_organization',
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
                        {field: 'dd_userid', title: __('Dd_userid'), operate: 'LIKE'},
                        {field: 'dd_unionid', title: __('Dd_unionid'), operate: 'LIKE'},
                        {field: 'dd_orgid', title: __('Dd_orgid'), operate: 'LIKE'},
                        {field: 'company_id', title: __('Company_id')},
                        {field: 'name', title: __('Name'), operate: 'LIKE'},
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