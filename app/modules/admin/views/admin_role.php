<div id="content-header">
    <div id="breadcrumb">
        <a href="/admin" class="tip-bottom" data-original-title="回到首页"><i class="glyphicon glyphicon-home"></i>首页</a>
        <a href="/admin/user" class="tip-bottom" data-original-title="账号管理">账号管理</a>
    </div>
    <h1>角色管理</h1>
</div>

<div class="container-fluid">
    <hr>
    <div class="panel panel-default">
        <div class="panel-heading">

            <div class="cmd-list">
                <div class="btn-group">
                    <a id="list-tab" href="#" class="btn btn-default">角色列表</a>
                    <a id="add-tab" href="#" class="btn btn-default">添加角色</a>
                </div>
            </div>
        </div>
        <div id="main-view">

        </div>
    </div>
</div>

<script type="text/html" id="main-tpl">
    <table class="table table-hover table-data" id="data-list">
        <thead>
        <tr>
            <th>名称</th>
            <th width="160">管理</th>
        </tr>
        </thead>
        <tbody>

        </tbody>
    </table>
</script>

<script type="template/javascript" id="data-item">
    <tr>
        <td><%=role_name%></td>
        <td>
            <a href="#" class="btn btn-default item-edit"><span class="glyphicon glyphicon-edit"></span> 编辑</a>
            <a href="#" class="btn btn-default item-remove"><span class="glyphicon glyphicon-trash"></span> 删除</a>
        </td>
    </tr>
</script>

<script type="template/javascript" id="add-tpl">
    <div class="panel-body">
        <form class="form-horizontal" role="form" method="post">

            <div class="form-group">
                <label class="col-sm-3 control-label"><span class="color_red">*</span> 名称</label>
                <div class="col-sm-9">
                    <label><input type="text" name="role_name" class="form-control" value="<%=role_name%>"></label>
                </div>
            </div>
            <hr>
            <div class="form-group">
                <label class="col-sm-3 control-label">权限设置</label>
                <div class="col-sm-9">
                    <div id="resource"></div>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3 control-label"></label>
                <div class="col-sm-9">
                    <button id="btn_save" class="btn btn-primary">保存</button>
                </div>
            </div>
            <%if(typeof(id) !== 'undefined'){%><input type="hidden" name="id" value="<%=id%>"> <%}%>
        </form>
    </div>
</script>

<script type="text/html" id="resource-tpl">
    <div class="form-group" style="border: 1px solid #ddd; padding: 10px;">
        <h4><label><input type="checkbox" name="resource" class="parent_resource" <%if(ck){%>checked<%}%> value="<%=key%>"> <%=resource.name%> </label></h4>
        <div id="<%=key%>" class="">

        </div>
    </div>
</script>
<script type="text/html" id="resource-child-tpl">
    <label class="mr10"><input type="checkbox" name="resource" value="<%=key%>" <%if(ck){%>checked<%}%>> <%=resource.name%> </label>
</script>
