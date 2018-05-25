/**
 * Created by river on 14-10-23.
 */
require.config(require_config);

define([
    'jquery',
    'components/kunrou',
    'components/collection',
    'components/model',
    'components/template-native',
    'dialog',
    'fineuploader',
    'nprogress'
], function( $, kunrou, Collection, Model, template, dialog, Uploader,  Nprogress ){
    Nprogress.start();

    var collection = new Collection();
    var parent_arr = new Array();
    collection.extend({
        id: '#data-list tbody',
        url: window._global.url.api + 'categorys',
        options : {
//            'limit' : 10,
//            'offset': 0,
//            'sort'  : 'listorder',
//            'order' : 'asc'
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
            parent_arr = new Array();
            $.each(this.data, function(index,value) {
                _this.render_item(value);
            });
        },
        render_item : function(item) {
            var _this = this;
            var html = template('data-item', item);
            if(item.pid == 0) {
                $(this.id).append(html);

                var _arr = new Array();
                _arr['name'] = item.name;
                _arr['id'] = item.id;
                parent_arr.push(_arr);

            }

            //二级添加
            $('#cate_'+item.id).find('.item-add').on('click', function (e) {
                add_cate(item.id,item.name,e);
            });

            $('#cate_'+item.id).find('.item-edit').on('click', function(e) {
                edit_cate(item);
            });

            //删除菜单
            $('#cate_'+item.id).find('.item-remove').on('click', function(e) {
                remove_cate(item.id , e);
            });


            //子类
            if(item.sub) {
                $.each(item.sub, function(index, value) {
                    value.tab = '&nbsp;&nbsp;&nbsp;&nbsp;|--';
                    var html = template('data-item', value);
                    $(_this.id).append(html);
                    //绑定事件
                    $(_this.id + ' tr:last').find('.item-edit').on('click', function() {
                        value.pname = item.name;
                        edit_cate(value);
                    });

                    $(_this.id + ' tr:last').find('.item-remove').on('click', function() {
                        remove_cate(value.id , value);
                    });
                });
            }
        }
    }).render();

    /**
     * 添加
     * @param parent
     */

    //一级添加
    $('#coupon-add-tab').on('click', function(e) {
        e.preventDefault();
        add_cate(0,0,e);
    });

    var add_cate = function(pid, pname, e) {
        //console.log('add');
        var tit_name = '【二级分类】';
        if (pname == 0){
            tit_name = '【一级分类】';
        }
        dialog({
            title : ' 添加' + tit_name,
            content: template('add-tpl', {pid:pid,pname:pname}),
            width : '450',
            ok: function() {
                var model = new Model();
                var icon = $('input[name="img_input"]').val();
                if(icon !== '' && icon !== undefined) {
                    icon = icon.split(':');
                    //console.log(icon);
                    model.data.icon = icon[0];
                }else{
                    model.data.icon = '';
                }

                model.data.pid = $('input[name="pid"]').val();
                model.data.pname = $('input[name="pname"]').val();
                model.data.name = $('input[name="name"]').val();
                model.data.listorder = $('input[name="listorder"]').val();
                if($('input[name="status"]').is(':checked')) {
                    model.data.status = 1;
                } else {
                    model.data.status = 0;
                }
                if(!model.extend({
                    url : window._global.url.api + 'category',
                    valid_config : {
                        'name' : 'required'
                    },
                    success: function() {
                        kunrou.alert({msg: '提交成功'});
                        collection.render();
                    }
                }).save()) {
                    return false;
                }
            },
            cancel: function() {

            }
        }).show();
        load_upload_img();
    };

    var edit_cate = function(item) {
        //console.log('edit');
        item.parent_arr = parent_arr;
        dialog({
            title : ' 编辑【' + ( item.name ? item.name : '父级') + '】分类信息',
            content: template('edit-tpl', item),
            width : '450',
            ok: function() {
                var model = new Model();
                var icon = $('input[name="img_input"]').val();
                if(icon !== '' && icon !== undefined) {
                    icon = icon.split(':');
                    //console.log(icon);
                    model.data.icon = icon[0];
                }else{
                    model.data.icon = '';
                }
                //alert(model.data.icon);
                model.data.id = item.id;
                model.data.pid = $('select[name="pid"]').val();
                model.data.name = $('input[name="name"]').val();
                model.data.listorder = $('input[name="listorder"]').val();
                if($('input[name="status"]').is(':checked')) {
                    model.data.status = 1;
                } else {
                    model.data.status = 0;
                }
                if(!model.extend({
                    url : window._global.url.api + 'category',
                    valid_config : {
                        'name' : 'required'
                    },
                    success: function() {
                        kunrou.alert({msg: '修改成功'});
                        collection.render();
                    }
                }).save()) {
                    return false;
                }
            },
            cancel: function() {

            }
        }).show();
        load_upload_img();
    };

    /**
     * 删除
     * @param id
     */
    var remove_cate = function(id, e) {
        dialog({
            content: '确认要删除吗?',
            ok: function() {
                var model = new Model();
                model.extend({
                    url : window._global.url.api + 'del_category',
                    data: {
                        id: id
                    },
                    success: function() {
                        kunrou.alert({msg: '删除成功'});
                        collection.render();
                    }
                }).del();
            }, cancel: function() {}
        }).show(e.target);
    };

    var load_upload_img = function(){
        //上传相关
        $("#J_UploadImgs").fineUploader({
            request: {
                endpoint: window._global.url.api+'attachment/upload',
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

                $('#J_ImgList').on('click', '.J_Delete', function(e) {
                    var _$item = $(this).parents('li')
                        ,img_id = _$item.attr('data-id');
                    $.ajax({
                        async:false,
                        url: window._global.url.api + 'news/img/'+img_id,
                        type: 'DELETE',
                        success: function(response) {
                            _$item.remove();
                            $('.qq-upload-button-selector').show();
                        }
                    });
                });

            }else{
                alert(response.msg);
            }
        });
    };

    Nprogress.done();
});
