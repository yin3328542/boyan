/**
 * Created by river on 14-12-26.
 */
require.config(require_config);

define([
    'jquery',
    'components/kunrou',
    'components/collection',
    'components/model',
    'components/template-native',
    'dialog',
    'nprogress'
], function( $, kunrou, Collection, Model, template, dialog, Nprogress ){
    Nprogress.start();

    var collection = new Collection();
    collection.extend({
        id: '#data-list tbody',
        url: window._global.url.api + 'admins',
        options : {},
        sync: function() {
            this.render_list();
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
            var html = template('data-item', item);
            $(this.id).append(html);
            //绑定事件
            $(this.id + ' tr:last').find('.item-remove').on('click', function(e) {
                e.preventDefault();
                remove(e, item.id);
            });
            $(this.id + ' tr:last').find('.item-enabled').on('click', function(e) {

            });
            $(this.id + ' tr:last').find('.item-disabled').on('click', function(e) {

            });

        }
    }).render();

    //添加
    $("#siter-add-tab").on('click', function(e) {
        e.preventDefault();
        //获取角色
        var roles = [];
        $.ajax({
            url: _global.url.api + 'admin/roles',
            type: 'get',
            async: false,
            success: function(result) {
                roles = result.data;
            }
        });

        dialog({
            title: '添加管理账号',
            width: 400,
            content: template('add-tpl', {roles: roles}),
            ok: function() {
                var res = add();
                if(res) {
                    collection.render();
                }
                return res;
            }, cancel: function() {}
        }).show();
    });
    var add = function() {
        var model = new Model();
        model.url = _global.url.api + 'admin';
        model.data.role_id = $('[name="role_id"]').val();
        model.data.real_name = $('input[name="real_name"]').val();
        model.data.username = $('input[name="username"]').val();
        model.data.email = $('input[name="email"]').val();
        model.data.password = $('input[name="password"]').val();
        model.data.confirm_password = $('input[name="confirm_password"]').val();
        return model.save();
    };
    var remove = function(e,id) {
        dialog({
            content: '确定要删除吗',
            ok: function() {
                var model = new Model();
                model.url = _global.url.api + 'admin';
                model.data.id = id;
                model.del();
                collection.render();
            }, cancel: function() {}
        }).show(e.target);
    };

    Nprogress.done();
});
