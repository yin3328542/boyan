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
        url: window._global.url.api+'message',
        options: {}
    });

    $('#btn_save').on('click', function() {
        $(this).button('loading');
        save();
        $(this).button('reset');
    });

    $('select[name="type"]').change(function(){
        var val = $(this).val();
        console.log(val);
        if(val%2 == 0){
            $('.receive_info').show();
        }else{
            $('.receive_info').hide();
            $('input[name="receive_id"]').val('');
        }
    });

    //保存
    var save = function() {

        var pics = $('input[name="img_input"]').val();
        if(pics !== '' && pics !== undefined) {
            pics = pics.split(':');
            console.log(pics);
            model.data.picurl = pics[0];
        }else{
            model.data.picurl = '';
        }

        model.data.title = $('input[name="title"]').val();
        model.data.desc = $('textarea[name="desc"]').val();
        model.data.content = $('textarea[name="content"]').val();
        model.data.receive_id = $('input[name="receive_id"]').val();
        model.data.type = $('select[name="type"]').val();
        model.success = function() {
            kunrou.alert({msg: '发布成功'});
        };
        model.save();
    };

    $('textarea[name="content"]').ckeditor({
        baseHref:window._global.url.editor_base_url,
        filebrowserUploadUrl:window._global.url.api+'attachment/editor_upload?a=1&app_key='+window._global.app_key
    });

    $('textarea[name="desc"]').ckeditor({toolbar: []});


    //上传相关
    $("#J_UploadImgs").fineUploader({
        request: {
            endpoint: window._global.url.api+'/attachment/upload',
            inputName: 'filedata',
            params: {
                app_key: window._global.app_key,
                access_token: window._global.access_token,
                folder: 'logo'
            }
        },
        multiple: false,
        deleteFile: {
            enabled: true,
            endpoint: window._global.url.api+'/attachment'
        },
        progress: function(id, filename, loaded, total){
            console.log(loaded);
            var progress = parseInt(loaded / total * 100, 10);
            $('#qq-file-id-'+id+' .upload-progress-bar').css('width',progress+ '%');
        }
    }).on('complete', function(event, id, fileName, response) {
        if (response.success) {
            $('#J_ImgList').append(template('img-item-tpl',{img_file:response.data.filepath, img:response.data.file_url, id:response.data.id}));
            $('.qq-upload-button-selector').hide();
        }else{
            alert(response.msg);
        }
    });

    $('#J_ImgList').on('click', '.J_Delete', function(e) {
        var _$item = $(this).parents('li')
            ,img_id = _$item.attr('data-id');
        dialog({
            content: '确定要删除吗？',
            ok: function() {
                $.ajax({
                    async:false,
                    url: window._global.url.api + '/goods/img/'+img_id,
                    type: 'DELETE',
                    success: function(response) {
                        _$item.remove();
                        $('.qq-upload-button-selector').show();
                    }
                });
            },cancel: function() {}
        }).show(e.target);
    });
    //end上传



    Nprogress.done();
});