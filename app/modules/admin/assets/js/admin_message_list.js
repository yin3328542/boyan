/**
 * Created by river on 14-10-30.
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
        url: window._global.url.api + 'messages',
        options : {
            'limit' : 10,
            'offset': 0,
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
            $(this.id + ' tr:last').find('.item-hide').on('click', function(e) {
                e.preventDefault();
                change_status(item.id, e);
            });

        }
    }).render();

    var change_status = function(id, e) {
        if(e !== '') {
            dialog({
                content: '确定要继续吗？',
                ok: function() {
                    var change_model = new Model();
                    change_model.data.id = id;
                    change_model.url = window._global.url.api + '/message';
                    if(change_model.save()){
                        collection.fetch();
                    }
                },cancel: function() {}
            }).show(e.target);
        } else {
            var change_model = new Model();
            change_model.data.id = id;
            change_model.url = window._global.url.api + '/message';
            if(change_model.save()){
                collection.fetch();
            }
        }
    };


    Nprogress.done();
});