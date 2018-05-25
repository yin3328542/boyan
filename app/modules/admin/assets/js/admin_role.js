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

    var main = function() {
        $('#main-view').html(template('main-tpl', {}));
        var collection = new Collection();
        collection.extend({
            id: '#data-list tbody',
            url: window._global.url.api + 'admin/roles',
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
                $(this.id + ' tr:last').find('.item-edit').on('click', function(e) {
                    edit(e, item);
                });

            }
        }).render();
    };

    //添加
    $("body").on('click', '#add-tab', function(e) {
        e.preventDefault();
        add();
    });

    //列表
    $("body").on('click', '#list-tab', function(e) {
        e.preventDefault();
        main();
    });
    //保存事件
    $("body").on('click', '#btn_save', function(e) {
        e.preventDefault();
        save();
    });

    var add = function() {
        model.data = {};
        var resource = [];
        //获取资源
        $.ajax({
            url: _global.url.api + 'admin/resource',
            type: 'get',
            async: false,
            success: function(result) {
                resource = result.data;
                console.log(resource);
            }
        });
        $('#main-view').html(template('add-tpl', {}));
        //输出资源
        for(var key in resource) {
            if(resource[key].parent == 'self') {
                $('#resource').append(template('resource-tpl', {key: key, resource: resource[key]}));
            }
        }
        for(var k in resource) {
            if(resource[k].parent != 'self') {
                $('#' + resource[k].parent).append(template('resource-child-tpl', {key: k, resource: resource[k]}));
            }
        }
        $('.parent_resource').on('click', function(e) {
            if($(this).is(':checked')) {
                console.log('checked');
                $('#' + $(this).val()).find('input[name="resource"]').prop('checked', true);
            } else {
                $('#' + $(this).val()).find('input[name="resource"]').prop('checked',false);
            }
        });
    };

    var edit = function(e, item) {
        model.data.id = item.id;
        e.preventDefault();
        var resource = [];
        //获取资源
        $.ajax({
            url: _global.url.api + 'admin/resource',
            type: 'get',
            async: false,
            success: function(result) {
                resource = result.data;
            }
        });
        $('#main-view').html(template('add-tpl', item));
        if(resource) {
            //输出资源
            for(var key in resource) {
                if(resource[key].parent == 'self') {
                    var ck = key.in_array(item.resource);
                    $('#resource').append(template('resource-tpl', {key: key, resource: resource[key], ck: ck}));
                }
            }
            for(var k in resource) {
                if(resource[k].parent != 'self') {
                    var ck = k.in_array(item.resource);
                    $('#' + resource[k].parent).append(template('resource-child-tpl', {key: k, resource: resource[k], ck: ck}));
                }
            }
        }

        $('.parent_resource').on('click', function(e) {
            if($(this).is(':checked')) {
                $('#' + $(this).val()).find('input[name="resource"]').prop('checked', true);
            } else {
                $('#' + $(this).val()).find('input[name="resource"]').prop('checked',false);
            }
        });
    };

    var save = function() {
        model.data.role_name = $('input[name="role_name"]').val();
        //resource
        var resource = [];
        $('input[name="resource"]').each(function() {
            if($(this).is(':checked')) {
                resource.push($(this).val());
            }
        });
        model.data.resource = resource;
        console.log(model);
        if(model.save()) {
            main();
        }
    };

    var remove = function(e, id) {
        dialog({
            content: '确定要删除吗',
            ok: function() {
                var model = new Model();
                model.extend({
                    url: _global.url.api + 'admin/role',
                    data: {id: id},
                    success: function() {
                        main();
                    }
                }).del();
            }, cancel: function() {}
        }).show(e.target);
    };

    /*初始模型*/
    var model = new Model();
    model.url = _global.url.api + 'admin/role';


    main();

    Nprogress.done();
});
