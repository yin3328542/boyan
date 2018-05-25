<script>
    var pid = <?php echo $pid?>;
    var id = <?php echo $id?>;
</script>
<div id="content-header">
    <div id="breadcrumb">
        <a href="/admin" class="tip-bottom" data-original-title="回到首页"><i class="glyphicon glyphicon-home"></i>首页</a>
    </div>
    <h1>编辑菜单</h1>
</div>

<div class="container-fluid">
    <hr>
    <div id="column-view" >
    </div>
</div>

<script type="text/template" id="column-edit-tpl">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="cmd-list">
                <div class="btn-group">
                    <a id="coupon-main-tab" href="/admin/column" class="btn btn-default">菜单列表</a>
                </div>
            </div>
        </div>
        <div class="panel-body">
            <form class="form-horizontal" role="form" method="post">
                <?php if($pid>0) {?>
                <div class="form-group">
                    <label class="col-sm-3 control-label">上级栏目</label>
                    <div class="col-sm-6"><?php echo $column['name'] ?></div>
                    <input type="hidden" name="pid" id="pid" value="<?php echo $pid ?>"/>
                </div>
                <?php }?>
                <div class="form-group">
                    <label class="col-sm-3 control-label">启用</label>
                    <div class="col-sm-6">
                        <label class="radio-inline"><input type="radio" name="status" value="0" <% if(status==0){%>checked<%}%>>否</label>
                        <label class="radio-inline"><input type="radio" name="status" value="1" <% if(status==1){%>checked<%}%>>是</label>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">菜单名称</label>
                    <div class="col-sm-2">
                        <input type="text" class="form-control" name="name" id="name" value="<%=name%>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-3 control-label">菜单图片</label>
                    <div class="col-sm-6">
                        <p>建议上传图片尺寸： 宽60px 高60px</p>
                        <div id="J_UploadImgs">点击上传</div>
                        <ul id="J_ImgList" class="imgs-list clearfix"></ul>
                        <input type="hidden" name="imgs">
                    </div>
                </div>

            </form>
        </div>

        <footer class="panel-footer">
            <button type="button" class="btn btn-primary ml15" id='btn_save' data-loading-text="保存中...">保存</button>
            <span class="font-red ml10"></span>
        </footer>
    </div>
</script>

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
    <li data-id="<%=id%>">
        <div class="p-img">
            <img class="J_Preview" src="<% if(typeof(img)!='undefined'){ %><%= img %><% } %>">
        </div>
		<% if(typeof(id) != 'undefined' && id !=0){ %>
        <div class="ctrl-bar">
        <a class="J_Delete glyphicon glyphicon-trash pull-right" title="删除"></a>
    </div>
        <input type="hidden" class="J_ImgInput" name="img_input" value="<% if(typeof(img_file)!='undefined' && typeof(id)!='undefined'){ %><%=id%>:<%=img_file%><% }%>">
		<% } %>
    </li>
</script>