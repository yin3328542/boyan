<div id="content-header">
    <div id="breadcrumb">
        <a href="/admin" class="tip-bottom" data-original-title="回到首页"><i class="glyphicon glyphicon-home"></i>首页</a>
    </div>
    <h1>首页菜单</h1>
</div>

<div class="container-fluid">
    <hr>
    <div id="column-view" >
        <div class="panel panel-default">
            <table class="table table-hover table-data" id="data-list">
                <thead>
                <tr>
                    <th nowrap>图标</th>
                    <th nowrap>菜单名称</th>
                    <th nowrap>导航显示</th>
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
    <tr id="column_<%=id%>">
        <td>
            <%if (filepath) {%>
                <img src="<%=img_file%>" width="50"/>
            <% }else{%>
                无
            <% }%>
        </td>
        <td>
            <%if(typeof(tab) !== 'undefined') {%><%=tab%><%}%><%=name%>
        </td>
        <td><%if(status==1) {%>是<%}else{%><span class="color_red">否</span><%}%></td>
        <td>
            <a href="column/edit/<%=id%>" title="编辑" class="btn btn-default"><span class="glyphicon glyphicon-edit"></span></a>
        </td>
    </tr>
</script>