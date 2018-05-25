/**
 * Created by river on 15-1-29.
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
    'fineuploader',
    'nprogress',
], function( $, kunrou, Collection, Model, template, dialog, calendar, Uploader, Nprogress ){
    Nprogress.start();

    /**
     *  列表
     */
    var collection;

    var main_view = function() {
        collection = new Collection();
        //载入列表
        collection.extend({
            id: '#data-list tbody',
            url: window._global.url.api + 'admin/contact',
            options : {
                'limit' : 10,
                'offset': 0
            },
            sync: function() {
                this.render_list();
                this.paginated();
            },
            render : function() {
                this.options.page = 1;
                this.options.offset = 0;
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
                $(this.id).append(html);

                //绑定事件
                $(this.id + ' tr:last').find('.item-edit').on('click', function(e) {
                    e.preventDefault();
                    edit(item,e);
                });
            }
        }).render();
    };

    //编辑
    var edit = function(item,e) {
        dialog({
            content: '确定要标识吗',
            ok: function() {
                var model = new Model();
                model.extend({
                    url: window._global.url.api + 'admin/contact',
                    data: {id: item.id},
                    success: function(res) {
                        kunrou.alert({msg:'标识成功'});
                        collection.render();
                    }
                }).save();
            },cancel: function() {}
        }).show(e.target);
    };

    main_view();

    Nprogress.done();
});
