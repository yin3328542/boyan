/**
 * Created by river on 14-4-26.
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
     var type = _global.type;
    var collection = new Collection();
    collection.extend({
        id: '#data-list tbody',
        url: window._global.url.api + 'banners',
        options : {
            'limit' : 10,
            'offset': 0,
            'sort'  : 'ad_time',
            'order' : 'desc',
            'type':type
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
            var html = template('data-item', item);
            $(this.id).append(html);

            //修改排序
            $('.banner-list-'+item.id).on('change', function(e) {
                edit_order(item.id, e);
            });

            //删除菜单
            $('.item-remove-'+item.id).on('click', function(e) {
                e.preventDefault();
                remove(item.id, e);
            });

            //banner隐藏
            $('.status-display-none-'+item.id).on('click', function(e) {
                e.preventDefault();
                var data = {};
                data.id = item.id;
                data.status = '0';
                change_status(data, e);
            });
            //banner显示
            $('.status-display-block-'+item.id).on('click', function(e) {
                e.preventDefault();
                var data = {};
                data.id = item.id;
                data.status = '1';
                change_status(data, e);
            });
        }
    }).render();

    //删除栏目
    var remove = function(id, e) {
        dialog({
            content: '确定要继续吗？',
            ok: function() {
                var change_model = new Model();
                change_model.data.id = id;
                change_model.url = window._global.url.api + 'del_banner';
                change_model.ajax_type = 'delete';
                if(change_model.save()){
                    collection.fetch();
                }
            },cancel: function() {}
        }).show(e.target);
    };

    //修改排序
    var edit_order = function(id, e) {
        var model = new Model();
        model.extend({
            url: window._global.url.api + 'order',
            data: {
                id: id,
                listorder: $(e.target).val()
            },
            success: function() {
                kunrou.alert({msg: '修改成功'});
                collection.render();
            }
        }).save();
    };

    var change_status = function(data, e) {
        if(e !== '') {
            dialog({
                content: '确定要继续吗？',
                ok: function() {
                    var change_model = new Model();
                    change_model.data = data;
                    change_model.url = window._global.url.api + 'banner_status';
                    change_model.ajax_type = 'get';
                    if(change_model.save()){
                        collection.fetch();
                    }
                },cancel: function() {}
            }).show(e.target);
        } else {
            var change_model = new Model();
            change_model.data = data;
            change_model.url = window._global.url.api + 'banner_status';
            change_model.ajax_type = 'get';
            if(change_model.save()){
                collection.fetch();
            }
        }
    };

    Nprogress.done();
});
