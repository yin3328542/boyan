<div id="content-header">
    <div id="breadcrumb">
        <a href="/admin" class="tip-bottom" data-original-title="回到首页"><i class="glyphicon glyphicon-home"></i>首页</a>
    </div>
    <h1>服务项目列表</h1>
</div>

<div class="container-fluid">
    <hr>
    <div id="imgtext-view" >
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="cmd-list">
                    <div class="btn-group">
                        <a id="coupon-main-tab" href="/admin/service" class="btn btn-default active">服务项目列表</a>
                        <a id="coupon-add-tab" href="/admin/service/add" class="btn btn-default"><span class="glyphicon glyphicon-plus"></span>服务项目添加</a>
                    </div>
                </div>
            </div>
            <table class="table table-hover table-data" id="data-list">
                <thead>
                <tr>
                    <th nowrap width="30"><input type="checkbox" data-toggle="chackall" data-target=".J_CheckItem"></th>
                    <th nowrap>图标</th>
                    <th nowrap>名称</th>
                    <th style="word-break: break-all;width:15%">描述</th>
                    <th nowrap>排序</th>
                    <th nowrap>添加时间</th>
                    <th nowrap width="200">操作</th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
            <footer class="panel-footer clearfix">
                <div id="pagination" class="pull-right"></div>
            </footer>
        </div>
    </div>
</div>

<script type="template/javascript" id="data-item">
    <tr id="imgtext_<%=id%>">
        <td><input type="checkbox" class="J_CheckItem" value="<%=id%>"></td>
        <td><img src="<%=img_file%>" width="60" height="60" border="0"/></td>
        <td><%=title%></td>
        <td><%=description%></td>
        <td><%=listorder%></td>
        <td><%=dt_add%></td>
        <td>
            <a href="javascript:void(0);" class="btn btn-sm btn-default item-remove-<%=id%>"><span class="glyphicon glyphicon-trash"></span></a>
            <a href="/admin/service/edit/<%=id%>" title="编辑" class="btn btn-default"><span class="glyphicon glyphicon-edit"></span></a>
        </td>
    </tr>
</script>