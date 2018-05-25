/**
 * Created by mxb on 14-4-26.
 */

require.config(require_config);

define([
    'jquery',
    'components/kunrou',
    'components/collection',
    'components/model',
    'components/template-native',
    'dialog',
    'calendar',
    'nprogress'
], function( $, kunrou, Collection, Model, template, dialog, calendar, Nprogress ){
    Nprogress.start();

    var collection = new Collection();
    collection.extend({
        id: '#data-list tbody',
        url: window._global.url.api + 'menu_list',
        options : {
//            'limit' : 10,
//            'offset': 0,
//            'sort'  : 'listorder',
//            'order' : 'asc'
        },
        sync: function() {
            this.render_list();
            this.paginated();
        },
        render : function() {
            this.fetch();
        },
        render_list : function() {
            var _this = this;
            $.each(this.data, function(index,value) {
                _this.render_item(value);
            });
        },
        render_item : function(item) {
            var _this = this;
            var html = template('data-item', item);
            if(item.parent_id == 0) {
                $(this.id).append(html);
            } else {
                item.tab = '&nbsp;&nbsp;&nbsp;&nbsp;|--';
                html = template('data-item', item);
                //console.log(html);
                //console.log(item);
                $('#menu_'+item.parent_id).after(html);
                //$(html).insertAfter('#menu_'+item.pid);
            }

            var table = collection.options.menu_type;
            if (typeof(table) != 'undefined'){
                table = collection.options.menu_type;
            }
            else{
                table = 'admin';
            }

            //添加菜单
            var tr = $('#menu_'+item.id);
            tr.find('.item-add').on('click', function (e) {
                add_menu(table,item.id,item.name,e);
            });
            //修改菜单
            var parent_name = 'edit';
            tr.find('.item-edit').on('click', function(e) {
                //console.log(item);
                edit_menu(table,parent_name,item.id,item.parent_id,item.name,item.url,item.alias,item.icon,item.listorder, e);
            });
            //修改排序
            tr.find('.menu-list').on('change', function(e) {
                edit_order(table,item.id, e);
            });
            //删除菜单
            tr.find('.item-remove').on('click', function(e) {
                e.preventDefault();
                remove(table,item.id, e);
            });
        }
    }).render();

    //筛选菜单分类
    $('#btn-status').on('click', 'a', function(e) {
        e.preventDefault();
        collection.options.menu_type = $(this).attr('rel');
        collection.render();
        $(this).addClass('active').siblings().removeClass('active');
    });

    //添加
    $('#btn_add_menu').on('click', function(e) {
        e.preventDefault();
        var table = collection.options.menu_type;
        if (typeof(table) != 'undefined'){
            table = collection.options.menu_type;
        }
        else{
            table = 'admin';
        }
        add_menu(table,0,0,e);
    });

    var add_menu = function(table, parent_id, parent_name, e) {
        //console.log(add_menu);
        var tit_name = '子';
        if (parent_name == 0){
            tit_name = '父';
        }
        dialog({
            title : '添加'+tit_name+'菜单',
            content: template('add-tpl', {menu_type:table,parent_id:parent_id,parent_name:parent_name,listorder:255}),
            width : '500',
            ok: function(e) {
                var model = new Model();
                model.data.menu_type = $('input[name="menu_type"]').val();
                model.data.parent_id = $('input[name="parent_id"]').val();
                model.data.parent_name = $('input[name="parent_name"]').val();
                model.data.name = $('input[name="name"]').val();
                model.data.alias = $('input[name="alias"]').val();
                model.data.icon = $('input[name="icon"]').val();
                model.data.url = $('input[name="url"]').val();
                model.data.listorder = $('input[name="listorder"]').val();

                console.log(model.data);

                if(!model.extend({
                    url : window._global.url.api + 'add_menu',
                    valid_config : {
                        'name' : 'required',
                        'alias' : 'required',
                        'url' : 'required',
                        'listorder' : 'required'
                    },
                    success: function() {
                        kunrou.alert({msg: '提交成功'});
                        collection.render();
                    }
                }).save()) {
                    return false;
                }
            },
            cancel: function() {

            }
        }).show();
    };

    var edit_menu = function(table,parent_name,id,parent_id,name,url,alias,icon,listorder,e) {
        //console.log('edit_menu');
        dialog({
            title : ' 编辑【' + name + '】菜单',
            content: template('add-tpl',{menu_type:table,parent_name:parent_name,id:id,parent_id:parent_id,name:name,url:url,alias:alias,icon:icon,listorder:listorder}),
            width : '500',
            ok: function(e) {
                var model = new Model();
                model.data.id = id;
                model.data.menu_type = $('input[name="menu_type"]').val();
                model.data.parent_id = $('input[name="parent_id"]').val();
                model.data.parent_name = $('input[name="parent_name"]').val();
                model.data.name = $('input[name="name"]').val();
                model.data.alias = $('input[name="alias"]').val();
                model.data.icon = $('input[name="icon"]').val();
                model.data.url = $('input[name="url"]').val();
                model.data.listorder = $('input[name="listorder"]').val();

                if(!model.extend({
                    url : window._global.url.api + 'edit_menu',
                    valid_config : {
                        'name' : 'required',
                        'alias' : 'required',
                        'url' : 'required',
                        'listorder' : 'required'
                    },
                    success: function() {
                        kunrou.alert({msg: '修改成功'});
                        collection.render();
                    }
                }).save()) {
                    return false;
                }
            },
            cancel: function() {

            }
        }).show();
    };

    var edit_order = function(table, id, e) {
        //var listorder = $(e.target).val();
        var model = new Model();
        model.extend({
            url: window._global.url.api + 'edit_listorder',
            data: {
                id: id,
                listorder: $(e.target).val(),
                menu_type: table
            },
            success: function() {
                kunrou.alert({msg: '修改成功'});
                collection.render();
            }
        }).save();
    };

    var remove = function(table, id, e) {
        dialog({
            content: '确认要删除吗?',
            ok: function(e) {
                var model = new Model();
                model.extend({
                    url : window._global.url.api + 'del_menu',
                    data: {
                        id: id,
                        menu_type: table
                    },
                    success: function() {
                        kunrou.alert({msg: '删除成功'});
                        collection.render();
                    }
                }).del();
            }, cancel: function() {}
        }).show(e.target);
    };

    $('.form_datetime').calendar();
    Nprogress.done();
});