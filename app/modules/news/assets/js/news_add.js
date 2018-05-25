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
    'ueditor',
    'dialog',
    'calendar',
    'nprogress'
], function( $, kunrou, Model, template, Uploader, ueditor, dialog,calendar ,Nprogress ){
    Nprogress.start();

    var model = new Model();
    var main_view = function() {
        //上传相关
        $("#J_UploadImgs").fineUploader({
            request: {
                endpoint: window._global.url.api+'/attachment/upload',
                inputName: 'filedata',
                params: {
                    app_key: window._global.app_key,
                    access_token: window._global.access_token,
                    folder: 'news'
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
            $.ajax({
                async:false,
                url: window._global.url.api + '/news/img/'+img_id,
                type: 'DELETE',
                success: function(response) {
                    _$item.remove();
                    $('.qq-upload-button-selector').show();
                }
            });
        });
        //end上传


        $('#btn_save').on('click', function() {
            $(this).button('loading');
            save_news();
            $(this).button('reset');
        });

        $('#intro').css('height','400px');
        $('#intro').css('width','100%');
        $('#intro').css('border','none');
        $('#intro').parent().css('height','500px');

        UE.getEditor('intro');
    };

    //保存
    var save_news = function() {
        var img = $('input[name="img_input"]').val();
        var title = $('input[name="title"]').val();
        var intro = $('input[name="intro"]').val();

        if(title==''){
            alert('请填写标题');
            return false;
        }
        if(intro==''){
            alert('请填写方案内容');
            return false;
        }
        if(img !== '' && img !== undefined) {
            img_a = img.split(':');
            model.data.img = img_a[0];
        }else{
            model.data.img =0;
        }

        model.data.title = $('input[name="title"]').val();
        model.data.intro = UE.getEditor('intro').getContent();
        model.data.status = $('input:radio[name="status"]:checked').val();

        if(!model.extend({
            url : window._global.url.api + 'admin/news',
            valid_config : {
                'title' : 'required',
                'content': 'required'
            },
            success: function() {
                location.href = '/admin/news';
            }
        }).save()) {
            return false;
        }
        return this;
    };

    main_view();

    Nprogress.done();
});
