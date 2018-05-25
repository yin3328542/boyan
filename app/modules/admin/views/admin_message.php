<div id="content-header">
    <div id="breadcrumb">
        <a href="/admin" class="tip-bottom" data-original-title="回到首页"><i class="glyphicon glyphicon-home"></i>首页</a>
    </div>
    <h1>发布消息</h1>
</div>

<div class="container-fluid">
    <hr>
    <div id="main-view" >

        <!--设置表单-->
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="cmd-list">
                    <div class="btn-group" id="btn-status">
                        <a href="/admin/message_list" class="btn btn-default">消息列表</a>
                        <a href="/admin/message" class="btn btn-default active">发布消息</a>
                    </div>
                </div>
            </div>

            <div class="panel-body">
                <form class="form-horizontal" role="form" method="post">
                    <div class="form-group">
                        <label class="col-sm-2 control-label">发送类型：</label>
                        <div class="col-sm-6">
                            <select name="type" style="border: none;">
                                <option value="1"<?php if($type==1)echo ' selected';?>>对所有代理商</option>
                                <?php if($type==2){?><option value="2"  selected >对单个代理商</option><?php }?>
                                <option value="3"<?php if($type==3)echo ' selected';?>>对所有站点</option>
                                <?php if($type==4){?><option value="4"  selected >对单个站点</option><?php }?>
                                <!--<option value="5"<?php /*if($type==5)echo ' selected';*/?>>对所有分销商</option>-->
                                <?php if($type==6){?><option value="6"  selected >对某个分销商</option><?php }?>
                                <!--<option value="7"<?php /*if($type==7)echo ' selected';*/?>>对所有客户</option>-->
                                <?php if($type==8){?><option value="8"  selected >对某个客户</option><?php }?>
                            </select>
                        </div>
                    </div>
                    <div class="form-group receive_info" <?php if($type < 1 || ($type>0 && $type%2!=0)){?>style="display: none;"<?php }?>>
                        <label class="col-sm-2 control-label">接收者ID：</label>
                        <div class="col-sm-6">
                            <?php echo $name;?>
                            <input type="text" class="form-control" style="width: 200px;display: none;" name="receive_id" value="<?php echo $receive_id?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">发送标题：</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" style="width: 200px;" name="title" >
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">图片</label>
                        <div class="col-sm-8">
                            <p>建议上传（图片比例1:1）</p>
                            <div id="J_UploadImgs">点击上传</div>
                            <ul id="J_ImgList" class="imgs-list clearfix"></ul>
                            <input type="hidden" name="imgs">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">发送简述：</label>
                        <div class="col-sm-6">
                            <textarea name="desc" class="form-control" cols="20" rows="5" ></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">发送内容：</label>
                        <div  class="col-sm-6">
                            <textarea name="content" class="form-control" cols="20" rows="5" ></textarea>
                        </div>
                    </div>
                </form>
            </div>

            <footer class="panel-footer">
                <button type="button" class="btn btn-primary ml15" id='btn_save' data-loading-text="发布中...">发布</button>
                <span class="font-red ml10"></span>
            </footer>
        </div>
    </div>
</div>


<!--上传图片-->
<script type="text/template" id="qq-template">
    <div class="qq-uploader-selector">
        <div class="qq-upload-button-selector btn btn-default">
            <div>上传图片</div>
        </div>
        <ul class="qq-upload-list-selector upload-progress clearfix">
            <li>
                <div class="qq-progress-bar-selector upload-progress-bar"></div>
            </li>
        </ul>
    </div>
</script>

<script type="text/template" id="img-item-tpl">
    <li data-id="<%= id %>">
        <div class="p-img">
            <img class="J_Preview" src="<% if(typeof(img)!='undefined'){ %><%= img %><% } %>">
        </div>
		<% if(typeof(id) != 'undefined' && id !=0){ %>
        <div class="ctrl-bar">
        <a class="J_Delete glyphicon glyphicon-trash pull-right" title="删除"></a>
    </div>
        <input type="hidden" class="J_ImgInput" name="img_input" value="<% if(typeof(img_file)!='undefined' && typeof(id)!='undefined'){ %><%= id %>:<%= img_file %><% } %>">
		<% } %>
    </li>
</script>