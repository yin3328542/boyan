<div id="content-header">
    <div id="breadcrumb">
        <a href="/admin" class="tip-bottom" data-original-title="回到首页"><i class="glyphicon glyphicon-home"></i>首页</a>
    </div>
    <h1>后台菜单</h1>
</div>

<div class="container-fluid">
    <hr>
    <div id="main-view" >
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="cmd-list pull-right">
                    <a href="#" class="btn btn-default" id="btn_add_menu"><span class="glyphicon glyphicon-plus"></span>添加菜单</a>
                </div>
                <div class="cmd-list">
                    <div class="btn-group" id="btn-status">
                        <a href="#" rel="admin" class="btn btn-default active">总后台</a>
                        <!--
                        <a href="#" rel="shoper" class="btn btn-default">分销商后台</a>
                        <a href="#" rel="siter" class="btn btn-default">商家后台</a>
                        -->
                    </div>
                </div>
            </div>
            <table class="table table-hover table-data" id="data-list">
                <thead>
                    <tr>
                        <th>排序</th>
                        <th>菜单名称(ID)</th>
                        <th>url</th>
                        <th>英文名称</th>
                        <th>icon</th>
                        <th width="200">操作</th>
                    </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
            <!--<footer class="panel-footer"></footer>-->
        </div>
    </div>
</div>

<script type="template/javascript" id="data-item">
    <tr id="menu_<%=id%>">
        <td>
            <input type="text" name="menu-list-<%=id%>" class="form-control menu-list menu-list-<%=id%>" style="width:50px;" maxlength="3" value="<%=listorder%>"/></td>
            <!--<%if(typeof(tab) !== 'undefined') {%><%=tab%><%}%><%=listorder%>-->
        <td>
            <%if(typeof(tab) !== 'undefined') {%><%=tab%><%}%><%=name%>(<%=id%>)
            <input type="hidden" name="menu-name-<%=name%>" id="menu-name-<%=name%>" class="menu-name menu-name-<%=name%>" value="<%=name%>">
        </td>
        <td><%=url%></td>
        <td><%=alias%></td>
        <td><%=icon%></td>
        <td>
            <a href="javascript:void(0);" class="btn btn-sm btn-default item-remove"><span class="glyphicon glyphicon-trash"></span></a>
            <a href="javascript:void(0);" class="btn btn-sm btn-default item-edit"><span class="glyphicon glyphicon-edit"></span></a>
            <% if(parent_id == 0) { %>
            <a href="javascript:void(0);" class="btn btn-sm btn-default item-add" title="添加子菜单"><span class="glyphicon glyphicon-plus"></span></a>
            <% } %>
        </td>
    </tr>
</script>

<script type="template/javascript" id="add-tpl">
    <form class="form-horizontal" role="form" method="post">
        <input type="hidden" name="parent_id" id="parent_id" value="<%if(typeof(parent_id) != 'undefined'){%><%=parent_id%><%}%>">
        <input type="hidden" name="parent_name" id="parent_name" value="<%if(typeof(parent_name) != 'undefined'){%><%=parent_name%><%}%>">
        <input type="hidden" name="menu_type" id="menu_type" value="<%if(typeof(menu_type) != 'undefined'){%><%=menu_type%><%}%>">
        <%if (parent_name != 'edit') {%>
        <div class="form-group">
            <label class="col-sm-3 control-label">
                <%if (parent_name == 0) {%>
                    <%if(typeof(menu_type) != 'undefined'){%>所属<%}%>
                <%}else{%>
                    父菜单
                <%}%>
            </label>
            <div class="col-sm-6">
            <%if (parent_name == 0) {%>
                <%if(menu_type == 'admin'){%>总后台<%}%>
                <%if(menu_type == 'shoper'){%>分销商后台<%}%>
                <%if(menu_type == 'siter'){%>商家后台<%}%>
            <%}else{%>
                <%=parent_name%>
            <%}%>
            </div>
        </div>
        <%}%>
        <div class="form-group">
            <label class="col-sm-3 control-label">菜单名称</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="name" id="name" value="<%=name%>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">URL</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="url" id="url" value="<%=url%>">(例如：/admin/menu)
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">英文名称</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="alias" id="alias" value="<%=alias%>">
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">图标class</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="icon" id="icon" value="<%=icon%>">(例如：home)
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">排序</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="listorder" id="listorder" style="width: 50px;" value="<%=listorder%>">
            </div>
        </div>
    </form>
</script>