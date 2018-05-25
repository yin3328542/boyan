<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title></title>
    <link rel="stylesheet" href="<?php echo base_url('assets/css/bootstrap/bootstrap.min.css');?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/app.css');?>">
    <link rel="stylesheet" href="<?php echo base_url('app/modules/siter/assets/css/'.$style_file.'.css');?>">
</head>
<body style="background: #fff">
<form class="form-horizontal" role="form" method="post">
    <div class="form-group">
        <div class="col-sm-12">
            <div id="J_UploadImgs">点击上传</div>
            <ul id="J_ImgList" class="imgs-list clearfix"></ul>
            <input type="hidden" name="imgs">
        </div>
    </div>
</form>

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

</body>
</html>
<script type="text/javascript">
    var img_path = '<?php echo $img_path; ?>';
    var _global = <?php echo json_encode($_global);?>;
</script>

<script src="<?php echo base_url('assets/js/require-config.js') ?>"></script>
<script data-main="<?php echo $app_assets_path;?>js/<?php echo $js_file;?>" src="<?php echo base_url('assets/js/require/require.js') ?>"></script>