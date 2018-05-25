<div id="content-header">
    <div id="breadcrumb">
        <a href="/admin" class="tip-bottom" data-original-title="回到首页"><i class="glyphicon glyphicon-home"></i>首页</a>
    </div>
    <h1>编辑装修方案</h1>
</div>

<script>
    var news_id = <?php echo $id?>;
</script>

<div class="container-fluid">
    <hr>
    <div id="news-view" >

    </div>
</div>

<script type="text/template" id="news-edit-tpl">
    <div class="panel panel-default">
        <div class="panel-heading">
            <div class="cmd-list">
                <div class="btn-group">
                    <a id="coupon-main-tab" href="/admin/news" class="btn btn-default">装修方案列表</a>
                    <a id="coupon-add-tab" href="/admin/news/add" class="btn btn-default active">装修方案添加</a>
                </div>
            </div>
        </div>
        <div class="panel-body">
            <form class="form-horizontal" role="form" method="post">
                <div class="form-group">
                    <label class="col-sm-2 control-label">方案封面图片</label>
                    <div class="col-sm-8">
                        <div id="J_UploadImgs">点击上传</div>
                        <ul id="J_ImgList" class="imgs-list clearfix"></ul>
                        <input type="hidden" name="imgs">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label"><span class="color_red">*</span>标题</label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" name="title" id="title" value="<%=title%>">
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label"><span class="color_red">*</span>文章详情</label>
                    <div class="col-sm-10">
                        <textarea name="intro" id="intro" class="form-control" cols="60" rows="5" ><%=intro%></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label">状态</label>
                    <div class="col-sm-5">
                        <label class="radio-inline"><input type="radio" name="status" value="0" <% if(status==0){%> checked <%}%>>关闭</label>
                        <label class="radio-inline"><input type="radio" name="status" value="1" <% if(status==1){%> checked <%}%> >开启</label>
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

<script type="text/template" id="cate-item-tpl">
    <div> <%if(typeof(tab) !== 'undefined') {%><%=tab%><%}%>
     <a href="javascript:" class="item-select"  id="<%=id%>"><%=name%></a>
    </div>
</script>