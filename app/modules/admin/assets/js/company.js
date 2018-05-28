/**
 * Created by river on 14-10-23.
 */

require.config(require_config);

define([
    'jquery',
    'components/kunrou',
    'components/model',
    'components/template-native',
    'fineuploader',
    'dialog',
    'calendar',
    'nprogress'
], function( $, kunrou, Model, template, Uploader, dialog,calendar ,Nprogress ){
    Nprogress.start();

    var model = new Model();
    var main_view = function() {
        model.data.id = _global.admin_id;
        model.extend({
            url : window._global.url.api + 'admin/company'
        });

        if(model.fetch() == false) {
            $('#edit-view').html('记录不存在或网络错误。');
            return false;
        }
        $('#intro').html(model.data.description);
        $('#btn_save').on('click', function() {
            $(this).button('loading');
            save_description();
            $(this).button('reset');
        });
    };


    //保存
    var save_description = function() {
        model.data.description = $('#intro').val();//$('#intro').val();
        if(!model.extend({
            url : window._global.url.api + 'admin/company',
            success: function() {
                kunrou.alert({msg: '保存成功'});
            }
        }).save()) {
            return false;
        }
        return this;
    };

    main_view();

    Nprogress.done();
});
