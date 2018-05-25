require.config(require_config);

define([
    'jquery',
    'components/kunrou',
    'components/template-native',
    'fineuploader'
], function( $, kunrou, template, Uploader ){

    //上传相关
    $("#J_UploadImgs").fineUploader({
        request: {
            endpoint: window._global.url.api+'/attachment/upload',
            inputName: 'filedata',
            params: {
                app_key: window._global.app_key,
                access_token: window._global.access_token,
                folder: img_path
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

    $('#J_ImgList').on('click', '.J_Delete', function() {
        var _$item = $(this).parents('li')
            ,img_id = _$item.attr('data-id');
        $.ajax({
            async:false,
            url: window._global.url.api + '/goods/img/'+img_id,
            type: 'DELETE',
            success: function(response) {
                _$item.remove();
                $('.qq-upload-button-selector').show();
            }
        });
    });

});