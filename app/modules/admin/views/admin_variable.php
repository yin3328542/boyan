<div id="content-header">
    <div id="breadcrumb">
        <a href="/admin" class="tip-bottom" data-original-title="回到首页"><i class="glyphicon glyphicon-home"></i>首页</a>
    </div>
    <h1>基本参数</h1>
</div>

<div class="container-fluid">
    <hr>
    <div id="main-view" >
        <div class="panel panel-default">
            <table class="table table-hover table-data" id="data-list">
                <thead>
                <tr>
                    <th>参数说明</th>
                    <th>参数值</th>
                    <th>变量名</th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
    </div>
</div>

<script type="text/template" id="data-item">
    <tr>
        <td>
            <%if (name=='by_location') {%>
            <a target="_blank" href="http://lbs.qq.com/tool/getpoint/index.html"><%=remark%></a>
            <%}else{%>
            <%=remark%>
            <%}%>
        </td>
        <td>
            <%if (type==1) {%>
                <input type="text" rel="<%=type%>" name="<%=name%>" class="form-control value-list value-list-<%=name%>" style="width:300px;" value="<%=value%>"/>
            <%}else{%>
                <textarea rel="<%=type%>" name="<%=name%>" class="form-control value-list value-list-<%=name%>" cols="30" rows="4" style="width: 350px;"><%=value%></textarea>
            <%}%>
        </td>
        <td><%=name%></td>
    </tr>
</script>

<script type="template/javascript" id="add-tpl">
    <form class="form-horizontal" role="form" method="post">
        <div class="form-group">
            <label class="col-sm-3 control-label"><span class="color_red">*</span>参数说明：</label>
            <div class="col-sm-9">
                <input type="text" name="remark" class="form-control" value="">（例如：站点名称）
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label"><span class="color_red">*</span>表单类型：</label>
            <div class="col-sm-9">
                <input type="radio" name="text_type" value="1" checked>文本框(纯文本)
                <input type="radio" name="text_type" value="2">文本域(含代码)
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label"><span class="color_red">*</span>参数值：</label>
            <div id="tp1" class="col-sm-9">
                <input type="text" name="value_text" class="form-control" value="">（例如：121店）
            </div>
            <div id="tp2" class="col-sm-9" style="display:none;">
                <textarea name="value_text_area" id="value_text_area" class="form-control" cols="40" rows="4" ></textarea>（例如：&lt;div&gt; 示例文本&lt;/div&gt;）
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label"><span class="color_red">*</span>变量名：</label>
            <div class="col-sm-9">
                <input type="text" name="name" class="form-control" value="">（例如：kr_webname）
            </div>
        </div>
    </form>
</script>