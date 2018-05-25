/**
 * Created by river on 14-10-25.
 */
require.config(require_config);

define([
    'jquery',
    'components/kunrou',
    'components/model',
    'components/template-native',
    'fineuploader',
    'ckeditor',
    'dialog',
    'bootstrap',
    'nprogress'
], function( $, kunrou, Model, template, Uploader, cheditor, dialog, Bootstrap, Nprogress ){
    Nprogress.start();

    var model = new Model();

    model.extend({
        url: window._global.url.api+'admin_password',
        options: {}
    });

    $('#btn_save').on('click', function() {
        $(this).button('loading');
        save();
        $(this).button('reset');
    });

    //保存
    var save = function() {
        model.data.old_pwd = $('input[name="old_pwd"]').val();
        model.data.new_pwd = $('input[name="new_pwd"]').val();
        model.data.pwd = $('input[name="pwd"]').val();
        model.success = function() {
            $('input[name="old_pwd"]').val('');
            $('input[name="new_pwd"]').val('');
            $('input[name="pwd"]').val('');
            alert('修改成功,请重新登录！');
            top.location.href = '/admin/auth/signout';
            //kunrou.alert({msg: '修改成功，下次请使用新密码登录'});
        };
        model.save();
    };


    Nprogress.done();
});