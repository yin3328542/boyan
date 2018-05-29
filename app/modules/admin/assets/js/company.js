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

        $('#intro').css('height','400px');
        $('#intro').css('width','100%');
        $('#intro').css('border','none');
        $('#intro').parent().css('height','500px');

        UE.getEditor('intro');
        //images浏览器start===============

        var img_list_total = 1;
        var img_list_start = 0;
        var img_list_size = 20;
        var img_page_height = 0;
        var img_list_height = 0;
        var img_list_api_url = window._global.url.api+'attachment/ueditor_list?a=1&app_key='+window._global.app_key;

        var select_img_list = function(){
            img_list_height = 0;
            img_list_total = 1;
            img_list_start = 0;
            img_page_height = 0;

            var _dialog = dialog({
                id : 'sel_img_list_id',
                title: '选择图片',
                content: '<style>ul.sel_img_list_id{width: 410px; display: block; list-style: none; margin: 0; padding: 0;}ul.sel_img_list_id li{float:left; display: block; list-style: none; padding: 0; width: 90px;  height: 90px; margin: 0 0 2px 2px; background-color: #eee;cursor: pointer; position: relative;}ul.sel_img_list_id li .icon{cursor: pointer; width: 90px;height: 90px;position: absolute; top: 0; left: 0; z-index: 2; border: 0; background-repeat: no-repeat;}ul.sel_img_list_id li:hover img{border: 3px solid #1094fa; }ul.sel_img_list_id li.selected .icon{background-image: url(/assets/js/ueditor/dialogs/image/images/success.png);background-position: 55px 55px;}ul.sel_img_list_id li img{cursor:pointer; margin:2px; width:90px;height:90px;}</style><ul class="sel_img_list_id"></ul>',
                width: 410,
                ok: function() {
                    $('#J_ImgList').html('');
                    $('.item-sel-img-select').each(  function(index){
                        var is_ok = $(this).parent().hasClass('selected');
                        if(is_ok){
                            var data = [];
                            data.id = $(this).attr('data-id');//这类图片不能删除，$(this).attr('data-id');
                            data.file_url = $(this).attr('data-filesrc');
                            data.filepath = $(this).attr('data-filepath');
                            $('#J_ImgList').append(template('img-item-tpl',{img_file:data.filepath,imgs:data.filepath, img:data.file_url, id:data.id}));
                            move();
                            $(".up").off().on("click",function(e){
                                e.stopPropagation();
                                $(this).parents("li").insertBefore($(this).parents("li").prev());
                                move();
                                return false;
                            });
                            $(".down").off().on("click",function(e){
                                e.stopPropagation();
                                $(this).parents("li").insertAfter($(this).parents("li").next());
                                move();
                                return false;
                            });
                            $("#J_ImgList li").mouseover(function(){
                                $(".showmove").show();
                            });
                            $("#J_ImgList li").mouseout(function(){
                                $(".showmove").hide();
                            });
                            if(data.file_url == model.data.img){
                                $('#J_ImgList li:last img').css('border','2px solid #1094fa').attr('title','商品主图');
                            }
                        }
                    });
                },
                cancel:function(){

                }
            }).show();

            load_sel_img_list(img_list_start, img_list_size);

        };
        var load_sel_img_list = function(start, size){
            $.ajax({
                url: img_list_api_url  + '&size=' + size  + '&start=' + start ,
                async: false,
                type: 'get',
                data: {},
                success: function(result) {
                    if(result.state == 'SUCCESS'){
                        img_list_total = result.total;

                        if(img_list_total < 1){
                            return false;
                        }

                        var imgs = '';
                        $('input[name="img_input"]').each(function() {
                            if($(this).val() !== '') {
                                var a = $(this).val().split(":");
                                imgs += a[0] + ',';
                            }
                        });

                        $.each(result.list, function(index,item) {
                            html = '<li><img class="item-sel-img-select" id="item_sel_img_select_' + item.id + '" src="' + item.file_url + '"  data-filesrc="' + item.file_url + '"  data-id="' + item.id + '"   data-filepath="' + item.filepath + '" /><span class="icon"></span></li>';
                            $('.sel_img_list_id').append(html);

                            if(imgs.indexOf(item.id+',') !== -1){
                                $('#item_sel_img_select_' + item.id).parent().addClass('selected');
                            }

                            $('#item_sel_img_select_' + item.id).parent().on('click', function() {
                                if($(this).hasClass('selected')){
                                    $(this).removeClass('selected');
                                }else{
                                    $(this).addClass('selected');
                                }
                                return false;
                            });

                        });

                        if(img_list_height == 0){
                            img_page_height = img_list_size/4 * 90;

                            img_list_height = img_page_height * (img_list_total/size);

                            $('.sel_img_list_id').css('height',  (img_page_height/2) + 'px');
                            $('.sel_img_list_id').css('overflow-y',  'scroll');

                            $('.sel_img_list_id').scroll(function(){
                                var scrTop = $('.sel_img_list_id').scrollTop();
                                var h = img_page_height * (img_list_start / size);

                                if( (scrTop + img_page_height/2) >=  h){
                                    img_list_start = img_list_start + size;
                                    if(img_list_start < img_list_total){
                                        load_sel_img_list( img_list_start , size);
                                    }
                                }
                            });

                        }
                    }
                }
            });
        };
    };


    //保存
    var save_description = function() {
        model.data.description = UE.getEditor('intro').getContent();//$('#intro').val();
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
