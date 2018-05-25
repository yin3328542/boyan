<div id="content-header">
    <div id="breadcrumb">
        <a href="/admin" class="tip-bottom" data-original-title="回到首页"><i class="glyphicon glyphicon-home"></i>首页</a>
    </div>
    <h1>装修方案列表</h1>
</div>

<div class="container-fluid">
    <hr>
    <div id="goods-view" >
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="pull-right">
                    <form class="form-horizontal form-search clearfix" role="form">
                        <div class="btn-group" id="search_stock">
                            <a href="javascript:" data-status="-1" class="btn btn-default">全部</a>
                            <a href="javascript:" data-status="1" class="btn btn-default">显示</a>
                            <a href="javascript:" data-status="2" class="btn btn-default">隐藏</a>
                        </div>
                    </form>
                </div>
                <div class="cmd-list">
                    <div class="btn-group">
                        <a id="coupon-main-tab" href="/admin/news" class="btn btn-default active">装修方案列表</a>
                        <a id="coupon-add-tab" href="/admin/news/add" class="btn btn-default">装修方案添加</a>
                    </div>
                </div>
            </div>
        <table class="table table-hover table-data" id="data-list">
            <thead>
            <tr>
                <th nowrap width="30"><input type="checkbox" data-toggle="chackall" data-target=".J_CheckItem"></th>
                <th nowrap>图片</th>
                <th nowrap>标题</th>
                <th nowrap>显示</th>
                <th nowrap>添加时间</th>
                <th nowrap width="200">管理</th>
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
    <tr>
        <td><input type="checkbox" class="J_CheckItem" value="<%=id%>"></td>
        <td><img src="<%=img_file%>" width="60"/></td>
        <td><%=title%></td>
        <td nowrap>
            <%if(status == 1) {%>
                <a href="#" class="btn btn-default status-display-none-<%=id%>" title="点击隐藏"><span class="glyphicon glyphicon-ok"></a>
            <%} else {%>
                <a href="#" class="btn btn-default status-display-block-<%=id%>" title="点击显示"><span class="glyphicon glyphicon-remove"></a>
            <%}%>
        </td>
        <td nowrap><%=dt_add%></td>
        <td nowrap>
            <a href="javascript:" title="删除" class="btn btn-default item-delete"><span class="glyphicon glyphicon-trash"></span></a>
            <a href="news/edit/<%=id%>" title="编辑" class="btn btn-default"><span class="glyphicon glyphicon-edit"></span></a>
        </td>
    </tr>
</script>