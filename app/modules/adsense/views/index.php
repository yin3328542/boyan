<div id="content-header">
    <div id="breadcrumb">
        <a href="/admin" class="tip-bottom" data-original-title="回到首页"><i class="glyphicon glyphicon-home"></i>首页</a>
    </div>
    <h1>广告位管理</h1>
</div>

<div class="container-fluid">
    <hr>
    <div id="banner-view" >
        <div class="panel panel-default">
            <table class="table table-hover table-data" id="data-list">
                <thead>
                <tr>
                    <th nowrap>广告标题</th>
                    <th nowrap>广告图片</th>
                    <th nowrap>显示</th>
                    <th nowrap width="200">操作</th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>

<script type="template/javascript" id="data-item">
    <tr id="banner_<%=id%>">
        <td><%=name%></td>
        <td><img src="<%=img_file%>" width="60"/></td>
        <td>
            <%if(status == 1) {%>
                <a href="#" class="btn btn-default status-display-none-<%=id%>" title="点击隐藏"><span class="glyphicon glyphicon-ok"></a>
            <%} else {%>
                <a href="#" class="btn btn-default status-display-block-<%=id%>" title="点击显示"><span class="glyphicon glyphicon-remove"></a>
            <%}%>
        </td>
        <td>
            <a href="/admin/adsense/edit/<%=id%>" title="编辑" class="btn btn-default"><span class="glyphicon glyphicon-edit"></span></a>
        </td>
    </tr>
</script>