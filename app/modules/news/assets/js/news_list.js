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

    var collection = new Collection();
    collection.extend({
        id: '#data-list tbody',
        url: window._global.url.api + 'admin/newses',
        options : {
            'limit' : 10,
            'offset': 0,
            'sort'  : 'dt_add',
            'order' : 'desc'
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

            //绑定事件
            $(this.id + ' tr:last').find('.item-delete').on('click', function(e) {
                e.preventDefault();
                delete_news(item.id, e);
            });

            //新闻隐藏
            $('.status-display-none-'+item.id).on('click', function(e) {
                e.preventDefault();
                var data = {};
                data.id = item.id;
                data.status = '0';
                change_status(data, e);
            });
            //新闻显示
            $('.status-display-block-'+item.id).on('click', function(e) {
                e.preventDefault();
                var data = {};
                data.id = item.id;
                data.status = '1';
                change_status(data, e);
            });
        }
    }).render();

    var delete_news = function(id, e) {
        dialog({
            content: '确定要继续吗？',
            ok: function() {
                var change_model = new Model();
                change_model.data.id = id;
                change_model.url = window._global.url.api + 'admin/news';
                change_model.ajax_type = 'delete';
                if(change_model.save()){
                    collection.fetch();
                }
            },cancel: function() {}
        }).show(e.target);
    };

    var change_status = function(data, e) {
        if(e !== '') {
            dialog({
                content: '确定要继续吗？',
                ok: function() {
                    var change_model = new Model();
                    change_model.data = data;
                    change_model.url = window._global.url.api + 'admin/news_status';
                    change_model.ajax_type = 'get';
                    if(change_model.save()){
                        collection.fetch();
                    }
                },cancel: function() {}
            }).show(e.target);
        } else {
            var change_model = new Model();
            change_model.data = data;
            change_model.url = window._global.url.api + 'news_status';
            change_model.ajax_type = 'get';
            if(change_model.save()){
                collection.fetch();
            }
        }
    };
    //bind event
    $('#search_stock').on('click', 'a', function(e) {
        e.preventDefault();
        collection.options.status = $(this).attr('data-status');
        collection.options.offset = 0;
        collection.render();
        $(this).addClass('active').siblings().removeClass('active');
    });

    Nprogress.done();
});
