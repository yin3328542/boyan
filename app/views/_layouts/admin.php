<!DOCTYPE html>
<html>
<head>
    <title>总后台管理中心</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="kunrou.INC">
    <link rel="shortcut icon" href="<?php echo base_url('assets/img/default/favicon.ico');?>" type="image/x-icon"/>
    <link rel="icon" href="<?php echo base_url('assets/img/default/favicon.ico');?>" type="image/x-icon"/>
    <link rel="Bookmark" href="<?php echo base_url('assets/img/default/favicon.ico');?>" type="image/x-icon"/>
    <link rel="stylesheet" href="<?php echo base_url('assets/css/bootstrap/bootstrap.min.css');?>">
    <link rel="stylesheet" href="<?php echo base_url('assets/css/bootstrap/matrix-style.css');?>" />
    <link rel="stylesheet" href="<?php echo base_url('assets/css/bootstrap/matrix-media.css');?>" />
    <link rel="stylesheet" href="<?php echo base_url('assets/css/app.css');?>">
    <link rel="stylesheet" href="<?php echo base_url('app/modules/admin/assets/css/'.$style_file.'.css');?>">
    <style>
        #content-header h1 {
            color: #555555;
            font-size: 20px;
            font-family: "microsoft yahei";
            font-weight: normal;
            float: none;
            text-shadow: 0 1px 0 #ffffff;
            margin-left: 20px;
            position: relative;
        }
        .imgs-list li .ctrl-bar {
            background: none repeat scroll 0 0 rgba(0,0,0,0.5);
            bottom: 0;
            height: 25px;
            padding-top: 5px;
            position: absolute;
            width: 100%;
        }
    </style>
</head>
<body>

<!--Header-part-->
<div id="header">
    <h1><a href="/admin">121dian.com</a></h1>
</div>
<!--close-Header-part-->


<!--top-Header-menu-->
<div id="user-nav" class="navbar navbar-inverse">
    <ul class="nav">
        <li  class="dropdown" id="profile-messages" ><a title="" href="#" data-toggle="dropdown" data-target="#profile-messages" class="dropdown-toggle">
                <i class="glyphicon glyphicon-user"></i><span class="text"><?php echo $admin_info['username'];?></span><b class="caret"></b></a>
            <ul class="dropdown-menu">
                <li><a href="/admin/admin_password"><i class="glyphicon glyphicon-pencil"></i> 修改密码</a></li>
                <li><a href="javascript:;" onclick="if(confirm('您确定要离开？'))top.location.href='<?php echo site_url('admin/auth/signout');?>'"><i class="glyphicon glyphicon-share-alt"></i> 离开</a></li>
            </ul>
        </li>
        <li class=""><a title="" href="javascript:;" onclick="if(confirm('您确定要离开？'))top.location.href='<?php echo site_url('admin/auth/signout');?>'"><i class="glyphicon glyphicon-share-alt"></i> <span class="text">离开</span></a></li>
    </ul>
</div>
<!--close-top-Header-menu-->

<!--sidebar-menu-->
<div id="sidebar">
    <ul>
        <?php foreach($top_nav as $_nav) :?>
            <li <?php if($top_active == $_nav['alias'] && $_nav['sub_nav']):?>class="open"<?php endif;?><?php if($top_active == $_nav['alias'] && !$_nav['sub_nav']):?>class="active"<?php endif;?>>
                <a href="<?php echo site_url($_nav['url']);?>"><i class="icon glyphicon glyphicon-<?php echo $_nav['icon'];?>"></i><?php echo $_nav['name'];?></a>
                <?php if($_nav['sub_nav']): ?>
                    <ul>
                        <?php foreach($_nav['sub_nav'] as $_sub_nav):?>
                            <li <?php if($aside_active == $_sub_nav['alias']):?>class="active"<?php endif;?>>
                                <a href="<?php echo site_url($_sub_nav['url']);?>"><?php echo $_sub_nav['name'];?></a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </li>
        <?php endforeach;?>


    </ul>
</div>
<!--sidebar-menu-->

<!--main-container-part-->
<div id="content">
    <?php echo $block_content;?>
</div>
</div>

<!--end-main-container-part-->

<!--Footer-part-->

<div class="row-fluid">
    <div id="footer" class="span12">Copyright &copy; 2013-<?php echo date("Y")?> <?php echo $kr_copyright;?></div>
</div>


</body>
<script type="text/javascript">
    var _global = <?php echo json_encode($_global);?>;
</script>

<script src="<?php echo base_url('assets/js/require-config.js') ?>"></script>
<script data-main="<?php echo $app_assets_path;?>js/<?php echo $js_file;?>" src="<?php echo base_url('assets/js/require/require.js') ?>"></script>

</html>