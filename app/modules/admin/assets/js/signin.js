/**
 * Created by river on 14-11-10.
 */

require.config(require_config);

define([
    'jquery',
    'components/kunrou',
    'nprogress'
], function( $, kunrou, Nprogress ){
    Nprogress.start();

    $('#btn_signin').on('click', function(e) {
        sign(e);
    });

    var sign = function(e) {
        e.preventDefault(),
            $('#btn_signin').button('loading');
        var datas = {
            username: $('#username').val(),
            password: $('#password').val()
        };
        $.ajax({
            url: window._global.url.base + 'admin/auth/signin',
            type: 'POST',
            data: datas,
            dataType: 'json'
        }).done(function(response) {
            $('#btn_signin').button('reset');
            if (response.status.code == 200) {
                window.location = '/admin';
            } else {
                $('#msg-container').removeClass('hide').text('账号或者密码错误');
                $('#btn_signin').button('reset');
            }
        }).fail(function() {
            $('#msg-container').removeClass('hide').text('系统错误，请重试。');
            $('#btn_signin').button('reset');
        })
    };

    Nprogress.done();

});