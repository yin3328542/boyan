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
        url: window._global.url.api + 'admin/columns',
        options : {
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
        }
    }).render();

    //删除栏目
    var remove = function(id, e) {
        dialog({
            content: '确定要继续吗？',
            ok: function() {
                var change_model = new Model();
                change_model.data.id = id;
                change_model.url = window._global.url.api + 'del_column';
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
            url: window._global.url.api + 'listorder',
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

    Nprogress.done();
});
