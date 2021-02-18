define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'csmding/dddepartment/index' + location.search,
                    add_url: 'csmding/dddepartment/add',
                    edit_url: 'csmding/dddepartment/edit',
                    del_url: 'csmding/dddepartment/del',
                    multi_url: 'csmding/dddepartment/multi',
                    table: 'csmding_dddepartment',
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
                        {field: 'departmentid', title: __('Departmentid')},
                        {field: 'name', title: __('Name')},
                        {field: 'parentid', title: __('Parentid')},
                        {field: 'order', title: __('Order')},
                        {field: 'createdeptgroup', title: __('Createdeptgroup'), searchList: {"true":__('Createdeptgroup true'),"false":__('Createdeptgroup false')}, formatter: Table.api.formatter.normal},
                        {field: 'autoadduser', title: __('Autoadduser'), searchList: {"true":__('Autoadduser true'),"false":__('Autoadduser false')}, formatter: Table.api.formatter.normal},
                        {field: 'depthiding', title: __('Depthiding'), searchList: {"true":__('Depthiding true'),"false":__('Depthiding false')}, formatter: Table.api.formatter.normal},
                        {field: 'deptperimits', title: __('Deptperimits')},
                        {field: 'deptpermits', title: __('Deptpermits')},
                        {field: 'userperimits', title: __('Userperimits')},
                        {field: 'userpermits', title: __('Userpermits')},
                        {field: 'outerdept', title: __('Outerdept'), searchList: {"true":__('Outerdept true'),"false":__('Outerdept false')}, formatter: Table.api.formatter.normal},
                        {field: 'outerpermitdepts', title: __('Outerpermitdepts')},
                        {field: 'outerpermitusers', title: __('Outerpermitusers')},
                        {field: 'orgdeptowner', title: __('Orgdeptowner')},
                        {field: 'deptmanageruseridlist', title: __('Deptmanageruseridlist')},
                        {field: 'sourceidentifier', title: __('Sourceidentifier')},
                        {field: 'groupcontainsubdept', title: __('Groupcontainsubdept')},
                        {field: 'deptgroupchatid', title: __('Deptgroupchatid')},
                        {field: 'ext', title: __('Ext')},
                        {field: 'status', title: __('Status'), searchList: {"normal":__('Normal'),"hidden":__('Hidden')}, formatter: Table.api.formatter.status},
                        {field: 'createtime', title: __('Createtime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'updatetime', title: __('Updatetime'), operate:'RANGE', addclass:'datetimerange', formatter: Table.api.formatter.datetime},
                        {field: 'csmadmin_depart_id', title: __('Csmadmin_depart_id')},
                        {field: 'b1', title: __('B1')},
                        {field: 'b2', title: __('B2')},
                        {field: 'b3', title: __('B3')},
                        {field: 'b4', title: __('B4')},
                        {field: 'b5', title: __('B5')},
                        {field: 'b6', title: __('B6')},
                        {field: 'b7', title: __('B7')},
                        {field: 'b8', title: __('B8')},
                        {field: 'b9', title: __('B9')},
                        {field: 'remoteversion', title: __('Remoteversion')},
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