<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:71:"/data/code/public/../application/admin/view/csmding/ddlogin/dobydd.html";i:1606618984;s:53:"/data/code/application/admin/view/layout/default.html";i:1602168705;s:50:"/data/code/application/admin/view/common/meta.html";i:1602168705;s:52:"/data/code/application/admin/view/common/script.html";i:1602168705;}*/ ?>
<!DOCTYPE html>
<html lang="<?php echo $config['language']; ?>">
    <head>
        <meta charset="utf-8">
<title><?php echo (isset($title) && ($title !== '')?$title:''); ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
<meta name="renderer" content="webkit">
<meta name="referrer" content="never">

<link rel="shortcut icon" href="/assets/img/favicon.ico" />
<!-- Loading Bootstrap -->
<link href="/assets/css/backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.css?v=<?php echo \think\Config::get('site.version'); ?>" rel="stylesheet">

<?php if(\think\Config::get('fastadmin.adminskin')): ?>
<link href="/assets/css/skins/<?php echo \think\Config::get('fastadmin.adminskin'); ?>.css?v=<?php echo \think\Config::get('site.version'); ?>" rel="stylesheet">
<?php endif; ?>

<!-- HTML5 shim, for IE6-8 support of HTML5 elements. All other JS at the end of file. -->
<!--[if lt IE 9]>
  <script src="/assets/js/html5shiv.js"></script>
  <script src="/assets/js/respond.min.js"></script>
<![endif]-->
<script type="text/javascript">
    var require = {
        config:  <?php echo json_encode($config); ?>
    };
</script>

    </head>

    <body class="inside-header inside-aside <?php echo defined('IS_DIALOG') && IS_DIALOG ? 'is-dialog' : ''; ?>">
        <div id="main" role="main">
            <div class="tab-content tab-addtabs">
                <div id="content">
                    <div class="row">
                        <div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
                            <section class="content-header hide">
                                <h1>
                                    <?php echo __('Dashboard'); ?>
                                    <small><?php echo __('Control panel'); ?></small>
                                </h1>
                            </section>
                            <?php if(!IS_DIALOG && !\think\Config::get('fastadmin.multiplenav') && \think\Config::get('fastadmin.breadcrumb')): ?>
                            <!-- RIBBON -->
                            <div id="ribbon">
                                <ol class="breadcrumb pull-left">
                                    <?php if($auth->check('dashboard')): ?>
                                    <li><a href="dashboard" class="addtabsit"><i class="fa fa-dashboard"></i> <?php echo __('Dashboard'); ?></a></li>
                                    <?php endif; ?>
                                </ol>
                                <ol class="breadcrumb pull-right">
                                    <?php foreach($breadcrumb as $vo): ?>
                                    <li><a href="javascript:;" data-url="<?php echo $vo['url']; ?>"><?php echo $vo['title']; ?></a></li>
                                    <?php endforeach; ?>
                                </ol>
                            </div>
                            <!-- END RIBBON -->
                            <?php endif; ?>
                            <div class="content">
                                <style>
.container {
	max-width: 720px;
	background-color: white;
	border: 1px solid #c9c9c9;
	padding: 20px;
}

.subtitle {
	font-size: 18px;
	padding-left: 10px;
}

.logon-tab {
	font-size: 16px;
	font-weight: bold;
	padding-bottom: 20px;
}
.login-nav{
	padding-top:20px;
}
.nav a{
	color:black;
}
.nav .active a{
	color:#3c8dbc !important;
}
.agreement{
	padding-left:10px;
}
.fa{
	padding-right:10px;
}
.bigtitle{
	color:#3c8dbc;
	font-weight:bold;
}
.div-left{
	border-right:1px solid #e7e7e7;
	height:100%;
}
.div-right{
	padding: 30px 0px 0px 20px;	
}
</style>
<div id="content-container" class="container">
	<div class="user-section login-section col-xs-12">
		<div class="logon-tab clearfix bigtitle">
			<h2><i class="fa fa-address-card" aria-hidden="true"></i>用户登录</h2>
		</div>	
		<div class="logon-tab clearfix ">
			<ul class="nav nav-tabs">
				<?php if(is_array($ulmenu) || $ulmenu instanceof \think\Collection || $ulmenu instanceof \think\Paginator): $i = 0; $__LIST__ = $ulmenu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$row): $mod = ($i % 2 );++$i;?>
				<li role="presentation" class="<?php if($row['code']=='dobydd'): ?>active<?php endif; ?>"><a href="<?php echo $row['url']; ?>"><?php echo $row['name']; ?></a></li>
				<?php endforeach; endif; else: echo "" ;endif; if(is_array($ulmenu2) || $ulmenu2 instanceof \think\Collection || $ulmenu2 instanceof \think\Paginator): $i = 0; $__LIST__ = $ulmenu2;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$row): $mod = ($i % 2 );++$i;?>
				<li role="presentation" class="<?php if($row['code']=='dobydd'): ?>active<?php endif; ?>"><a href="<?php echo $row['url']; ?>"><?php echo $row['name']; ?></a></li>
				<?php endforeach; endif; else: echo "" ;endif; ?>
			</ul>
		</div>
		<div class="login-main div-left col-xs-9">
			<form name="form" id="login-form" role="form" class="form-vertical"
				method="POST" action="">
				<input type='hidden' id='ddappid' value='<?php echo $ddappid; ?>'/>
				<input type='hidden' id='hosturl' value='<?php echo $hosturl; ?>'/>
				<div class="form-group" style="text-align:center;">
					<div id='login_container'>正在加载二维码，请等待！</div>	
					
				</div>
			</form>
		</div>
		<div class="col-xs-3 div-right">
			<a href='#' id="csmadmin-gotologin">使用帐号密码登录</a>
		</div>
	</div>
</div>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="/assets/js/require<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js" data-main="/assets/js/require-backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js?v=<?php echo htmlentities($site['version']); ?>"></script>
    </body>
</html>
