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
        url: window._global.url.api + '/members',
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

            $(this.id + ' tr:last').find('.item-in').on('click', function(e) {
                e.preventDefault();
                update_blacklist(e,item,'in');
            });
            $(this.id + ' tr:last').find('.item-out').on('click', function(e) {
                e.preventDefault();
                update_blacklist(e,item,'out');
            });
        }
    }).render();

    //bind event
    $('#btn-status').on('click', 'a', function(e) {
        e.preventDefault();
        collection.options.keyword = '';
        $('[name="keyword"]').val('');
        collection.options.status = $(this).attr('rel');
        collection.render();
        $(this).addClass('active').siblings().removeClass('active');
    });
    //search event
    $('#btn-search').on('click', function(e) {
        e.preventDefault();
        var keyword = $('[name="keyword"]').val();
        $('#btn-status a').removeClass('active');
        if(keyword != '') {
            collection.options.keyword = keyword;
        }
        collection.render();
    });

    var update_blacklist = function(e,item,action) {
        dialog({
            content: '确定要继续吗?',
            ok: function() {
                var model = new Model();
                model.extend({
                    url: window._global.url.api + 'member',
                    data: {
                        action: action,
                        id: item.id
                    },
                    success: function() {
                        collection.render();
                    }
                }).save();
            },
            cancel: function() {}
        }).show(e.target);
    };
    //发送消息
    var send_msg = function(id,name) {
        dialog({
            title: '发送消息给：'+name,
            content: $('#send_msg-tpl').html(),
            width: 500,
            ok: function() {
                var send_msg_model = new Model();
                send_msg_model.url = window._global.url.api + '/message';
                send_msg_model.data.title = $('input[name="title"]').val();
                send_msg_model.data.content = $('textarea[name="content"]').val();
                send_msg_model.data.receive_id = id;
                send_msg_model.data.type = 8;//对单个客户发送消息
                send_msg_model.success = function() {
                    kunrou.alert({msg: '发送成功'});
                };
                send_msg_model.save();
            },cancel: function() {}
        }).show();
    };

    //微信企业付款
    var pay = function(item) {
        dialog({
            title: '发送消息给：'+item.name,
            content: $('#pay-tpl').html(),
            width: 500,
            ok: function() {
                var _model = new Model();
                _model.url = window._global.url.api + 'wx_transfer';
                _model.data.amount = $('input[name="amount"]').val();
                _model.data.remark = $('textarea[name="remark"]').val();
                _model.data.open_id = item.open_id;
                _model.success = function() {
                    kunrou.alert({msg: '发送成功'});
                };
                _model.save();
            },cancel: function() {}
        }).show();
    };

    //模拟登录
    var view_site = function(data) {
        var html = template('view-site-tpl', data);
        dialog({
            id : 'view-site-windows',
            title: '模拟登录：'+data.name,
            content: html,
            modal: true,
            position: 'center',
            width: 320,
            height: 480,
            cancel: function() {
                $('body').css('overflow-y','');
            }
        }).show();
        $('body').css('overflow-y','hidden');
        $('.ui-dialog-button').remove();
    };


    Nprogress.done();
});