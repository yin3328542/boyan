/**
 * Created by mxb on 15-02-12.
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
        //上传相关
        $("#J_UploadImgs").fineUploader({
            request: {
                endpoint: window._global.url.api+'attachment/upload',
                inputName: 'filedata',
                params: {
                    app_key: window._global.app_key,
                    access_token: window._global.access_token,
                    folder: 'banner'
                }
            },
            multiple: false,
            deleteFile: {
                enabled: true,
                endpoint: window._global.url.api+'attachment'
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

        $('#J_ImgList').on('click', '.J_Delete', function(e){
            var _$item = $(this).parents('li');
            var img_id = _$item.attr('data-id');
            dialog({
                content: '确定要继续吗？',
                ok: function() {
                    $.ajax({
                        async:false,
                        url: window._global.url.api + 'banner/img/'+img_id,
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

        //事件绑定
        $('#btn_save').on('click', function() {
            $(this).button('loading');
            save_banner();
            $(this).button('reset');
        });
    };
    var type = _global.type;

    //保存
    var save_banner = function() {

        var name = $('input[name="name"]').val();
        var url = $('input[name="url"]').val();

        //图片
        var img = $('input[name="img_input"]').val();
        if(img==''){
            alert('请上传图片');
            return false;
        }
        if(img !== '' && img !== undefined) {
            img_str = img.split(':');
            model.data.img = img_str[0];
        }else{
            model.data.img =0;
        }
        model.data.name = $('input[name="name"]').val();
        model.data.alt = $('input[name="alt"]').val();
        model.data.url = $('input[name="url"]').val();
        model.data.listorder = $('input[name="listorder"]').val();
        model.data.status = $('input:radio[name="status"]:checked').val();
        //console.log(model.data);
        model.data.type = type;;
        if(!model.extend({
            url : window._global.url.api + 'banner',
            valid_config : {
                'img_input' : 'required',
            },
            success: function() {
                location.href = '/admin/banner';
            }
        }).save()) {
            return false;
        }
        return this;
    };
    main_view();
    Nprogress.done();
});
