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
    'calendar',
    'fineuploader',
    'nprogress',
], function( $, kunrou, Collection, Model, template, dialog, calendar, uploader, Nprogress ){
    Nprogress.start();

    var collection = new Collection();
    collection.extend({
        id: '#data-list tbody',
        url: window._global.url.api + 'admin_variable',
        options : {
            'limit' : 10,
            'offset': 0,
            'sort'  : 'name',
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
            //修改变量值
            $(this.id + ' tr:last').find('.value-list').on('change', function() {
                edit_variable(item.name);
            });
        }
    }).render();

    //添加
    $("#variable-add-tab").on('click', function(e) {
        e.preventDefault();
        dialog({
            title: '添加新变量',
            width: 450,
            content: template('add-tpl'),
            ok: function() {
                var res = add();
                if(res) {
                    collection.render();
                }
                return res;
            }, cancel: function() {}
        }).show();
        $('input[name="text_type"]').on('click', function() {
            var input_value=$(this).val();
            if (input_value==1){
                document.getElementById("tp1").style.display='block';
                document.getElementById("tp2").style.display='none';
            }else{
                document.getElementById("tp1").style.display='none';
                document.getElementById("tp2").style.display='block';
            }
        });
    });

    var add = function() {
        var model = new Model();
        model.url = _global.url.api + 'add_variable';
        model.data.name = $('input[name="name"]').val();
        model.data.text_type = $('input[name="text_type"]:checked').val();
        if (model.data.text_type==1){
            model.data.value = $('input[name="value_text"]').val();
        }else{
            model.data.value = $('textarea[name="value_text_area"]').val();
        }
        model.data.remark = $('input[name="remark"]').val();
        return model.save();
    };

    var edit_variable = function(name) {
        var model = new Model();
        model.extend({
            url: window._global.url.api + 'edit_variable',
            data: {
                name: name,
                type: $('.'+'value-list-'+name).attr('rel'),
                value: $('.'+'value-list-'+name).val()
            },
            success: function() {
                kunrou.alert({msg: '修改成功'});
                collection.render();
            }
        }).save();
    };



    Nprogress.done();
});
