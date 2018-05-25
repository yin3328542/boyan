<div id="content-header">
    <div id="breadcrumb">
        <a href="/admin" class="tip-bottom" data-original-title="回到首页"><i class="glyphicon glyphicon-home"></i>首页</a>
    </div>
    <h1>分类列表</h1>
</div>

<div class="container-fluid">
    <hr>
    <div id="main-view" >
        <div class="panel panel-default">
            <div class="panel-heading">

                <div class="cmd-list">
                    <div class="btn-group">
                        <a id="coupon-main-tab" href="/admin/category" class="btn btn-default active">分类列表</a>
                        <a id="coupon-add-tab" href="javascript:;" class="btn btn-default">添加</a>
                    </div>
                </div>
            </div>
            <table class="table table-hover table-data" id="data-list">
                <thead>
                <tr>
                    <th width="30"><input type="checkbox" data-toggle="chackall" data-target=".J_CheckItem"></th>
                    <th>名称(ID)</th>
                    <th>图片</th>
                    <th>排序</th>
                    <th>前端显示</th>
                    <th>管理</th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
            <footer class="panel-footer"></footer>
        </div>


    </div>
</div>

<script type="template/javascript" id="data-item">
    <tr id="cate_<%=id%>">
        <td><input type="checkbox" class="J_CheckItem" value="<%=id%>"></td>
        <td><%if(typeof(tab) !== 'undefined') {%><%=tab%><%}%><%=name%>(<%=id%>)</td>
        <td><%if(icon){%><img src="<%=icon%>" width="50"/><%}%></td>
        <td><%=listorder%></td>
        <td><%if(status == 1){%><span class="label label-info">是</span><%}%></td>
        <td>
            <a href="javascript:" class="btn btn-sm btn-default item-remove"><span class="glyphicon glyphicon-trash"></span></a>
            <a href="javascript:" class="btn btn-sm btn-default item-edit"><span class="glyphicon glyphicon-edit"></span></a>
            <% if(pid == 0) { %>
                <!--<a href="javascript:" class="btn btn-sm btn-default item-add" title="添加子类"><span class="glyphicon glyphicon-plus"></span></a>-->
            <% } %>
        </td>
    </tr>
</script>

<script type="template/javascript" id="add-tpl">
    <form class="form-horizontal" role="form" method="post">
        <input type="hidden" name="pid" id="pid" value="<%if(typeof(pid) != 'undefined'){%><%=pid%><%}%>">
        <input type="hidden" name="pname" id="pname" value="<%if(typeof(pname) != 'undefined'){%><%=pname%><%}%>">
        <%if (pname != 'edit') {%>
        <div class="form-group">
            <%if (pname == 0) {%>

            <%}else{%>
                <label class="col-sm-3 control-label">
                        上级分类
                </label>
            <%}%>

            <%if (pname == 0) {%>

            <%}else{%>
                <div class="col-sm-6"><%=pname%></div>
            <%}%>
        </div>
        <%}%>
        <div class="form-group">
            <label class="col-sm-3 control-label">名称</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="name" id="name" value="">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">icon<p>(图标)</p></label>
            <div class="col-sm-6">
                <div id="J_UploadImgs">点击上传</div>
                <ul id="J_ImgList" class="imgs-list clearfix"></ul>
                <input type="hidden" name="imgs">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">排序</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="listorder" id="listorder" value="">
            </div>
        </div>

    </form>
</script>

<script type="template/javascript" id="edit-tpl">
    <form class="form-horizontal" role="form" method="post">
        <input type="hidden" name="pid" id="pid" value="<%=pid%>">
        <!--
        <div class="form-group">
            <label class="col-sm-3 control-label">上级分类</label>
            <div class="col-sm-6">
                <select name="pid" class="form-control" style="width: auto;">
                    <option value="0">顶级分类</option>
                 <%for(var i=0; i<parent_arr.length; i++){ %>
                  <% if(parent_arr[i].id != id){%>  <option value="<%=parent_arr[i].id%>" <% if(parent_arr[i].id==pid){ %>selected<% }%>><%=parent_arr[i].name%></option><% }%>
                 <% }%>
             </select>
            </div>
        </div>
        -->
        <div class="form-group">
            <label class="col-sm-3 control-label">名称</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="name" id="name" value="<%=name%>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">icon<p>(图标)</p></label>
            <div class="col-sm-6">
                <div id="J_UploadImgs">点击上传</div>
                <ul id="J_ImgList" class="imgs-list clearfix"></ul>
                <input type="hidden" name="imgs">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">导航显示</label>
            <div class="col-sm-6">
                <input type="checkbox" class="form-checkbox" name="status" <%if(status == 1) {%>checked<%}%> id="status" value="1">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">排序</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="listorder" id="listorder" value="<%=listorder%>">
            </div>
        </div>

    </form>
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