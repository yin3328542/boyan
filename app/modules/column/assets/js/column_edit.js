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
    'ckeditor',
    'dialog',
    'calendar',
    'nprogress'
], function( $, kunrou, Model, template, Uploader, cheditor, dialog,calendar ,Nprogress ){
    Nprogress.start();

    var model = new Model();

    var main_view = function() {
        model.extend({
            url: window._global.url.api+'column/'+id,
            options: {}
        }).fetch();

        $('#column-view').html(template('column-edit-tpl', model.data));

        //上传相关

        $("#J_UploadImgs").fineUploader({
            request: {
                endpoint: window._global.url.api+'attachment/upload',
                inputName: 'filedata',
                params: {
                    app_key: window._global.app_key,
                    access_token: window._global.access_token,
                    folder: 'column'
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

        if(model.data.attachmen_id > 0) {
            $('#J_ImgList').append(template('img-item-tpl',{img_file:model.data.img, img:model.data.img, id:model.data.attachmen_id}));
            $('.qq-upload-button-selector').hide();
        }

        $('#J_ImgList').on('click', '.J_Delete', function(e){
            var _$item = $(this).parents('li');
            var img_id = _$item.attr('data-id');
            dialog({
                content: '确定要继续吗？',
                ok: function() {
                    $.ajax({
                        async:false,
                        url: window._global.url.api + 'column/img/'+img_id,
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
            save_column();
            $(this).button('reset');
        });
    };

    //保存
    var save_column = function() {

        var name = $('input[name="name"]').val();
        var url = $('input[name="url"]').val();
        var title = $('input[name="title"]').val();
        var keywords = $('input[name="keywords"]').val();
        var description = $('textarea[name="description"]').val();

        if(name==''){
            alert('请填写栏目名称');
            return false;
        }
        //图片
        var img = $('input[name="img_input"]').val();
        if(img !== '' && img !== undefined) {
            img_str = img.split(':');
            model.data.img = img_str[0];
        }else{
            model.data.img =0;
        }
        model.data.pid = $('input[name="pid"]').val();
        model.data.name = $('input[name="name"]').val();
        model.data.name_en = $('input[name="name_en"]').val();
        model.data.url = $('input[name="url"]').val();
        model.data.title = $('input[name="title"]').val();
        model.data.keywords = $('input[name="keywords"]').val();
        model.data.description = $('textarea[name="description"]').val();
        model.data.listorder = $('input[name="listorder"]').val();
        model.data.status = $('input:radio[name="status"]:checked').val();
        //console.log(model.data);

        if(!model.extend({
            url : window._global.url.api + 'column',
            valid_config : {
                'name' : 'required',
            },
            success: function() {
                location.href = '/admin/column';
            }
        }).save()) {
            return false;
        }
        return this;
    };

    main_view();

    Nprogress.done();
});
