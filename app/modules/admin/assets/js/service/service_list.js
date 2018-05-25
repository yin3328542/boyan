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
        url: window._global.url.api + 'admin/services',
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

            //删除菜单
            $('.item-remove-'+item.id).on('click', function(e) {
                e.preventDefault();
                remove(item.id, e);
            });

        }
    }).render();

    //search event
    $('#btn-search').on('click', function(e) {
        e.preventDefault();
        var keyword = $('[name="keyword"]').val();
        var type = $("#type  option:selected").val();
        $('#btn-status a').removeClass('active');
        collection.options.keyword = keyword;
        collection.options.type = type;
        collection.render();
    });

    //删除栏目
    var remove = function(id, e) {
        dialog({
            content: '确定要继续吗？',
            ok: function() {
                var change_model = new Model();
                change_model.data.id = id;
                change_model.url = window._global.url.api + 'admin/service';
                change_model.ajax_type = 'delete';
                if(change_model.save()){
                    collection.fetch();
                }
            },cancel: function() {}
        }).show(e.target);
    };

    Nprogress.done();
});
