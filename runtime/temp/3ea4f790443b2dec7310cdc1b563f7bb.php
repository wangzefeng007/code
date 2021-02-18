<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:63:"/data/code/public/../application/admin/view/calendar/index.html";i:1608705783;s:53:"/data/code/application/admin/view/layout/default.html";i:1602168705;s:50:"/data/code/application/admin/view/common/meta.html";i:1602168705;s:52:"/data/code/application/admin/view/common/script.html";i:1602168705;}*/ ?>
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
                                <link rel="stylesheet" href="/assets/addons/calendar/fullcalendar/dist/fullcalendar.css">
<style>
    .fc-day.selected {
        background:#e0f2be!important;
    }
    .fc-event.fc-completed {
        text-decoration:line-through;
    }
    .fc-event.fc-expired {
        background:#999!important;
        border-color:#999!important;
    }
    .calendar-trash {
        position:absolute;top:0;left:0;
        display: inline-block;
        width:100%;
        height:52px;
        text-align:center;
        line-height:52px;
        color: #e74c3c;
        background-color: #f2dede;
        display:none;
    }
    .fc .fc-toolbar .calendar-trash .fa{font-size: 20px;display:inline-block;vertical-align:middle;}
    .fc .fc-toolbar .calendar-trash > * {float:none}
    body .dialog-event .layui-layer-content{
        padding:15px;
    }
    #add-form .msg-box {
        position:absolute;
        top:-25px;
        right:0;
    }
    #add-form.n-default .msg-wrap {
        position:relative;
    }
    ul.fc-color-picker li a {
        position:relative;
    }
    ul.fc-color-picker li a.active:after {
        font-family: FontAwesome;
        content: "\f00c";
        position: absolute;
        z-index: 1000;
        top: 4px;
        left: 7px;
        font-size: 11px;
        color: #fff;
    }
</style>

<section class="">
    <div class="row">
        <div class="col-md-3">
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h4 class="box-title"><?php echo __('Exist event'); ?></h4>
                </div>

                <div class="box-body">
                    <!-- the events -->
                    <div id="external-events">
                        <?php foreach($eventList as $k=>$v): ?>
                        <div class="external-event" data-id="<?php echo $v['id']; ?>" data-title="<?php echo $v['title']; ?>" data-background="<?php echo $v['background']; ?>" style="background-color:<?php echo $v['background']; ?>;color:#fff;"><?php echo $v['title']; ?></div>
                        <?php endforeach; ?>
                        <div class="checkbox">
                            <label for="drop-remove">
                                <input type="checkbox" id="drop-remove">
                                <?php echo __('Remove after drop'); ?>
                            </label>
                        </div>
                    </div>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /. box -->
            <div class="box box-solid">
                <div class="box-header with-border">
                    <h3 class="box-title"><?php echo __('Add event'); ?></h3>
                </div>
                <div class="box-body">
                    <form id="add-form" action="calendar/addevent" role="form" method="post">
                        <div class="btn-group" style="width: 100%; margin-bottom: 10px;">
                            <input type="hidden" name="row[background]" value="#18bc9c" />
                            <ul class="fc-color-picker" id="color-chooser">
                                <li><a class="text-green active" href="#"><i class="fa fa-square"></i></a></li>
                                <li><a class="text-aqua" href="#"><i class="fa fa-square"></i></a></li>
                                <li><a class="text-blue" href="#"><i class="fa fa-square"></i></a></li>
                                <li><a class="text-light-blue" href="#"><i class="fa fa-square"></i></a></li>
                                <li><a class="text-teal" href="#"><i class="fa fa-square"></i></a></li>
                                <li><a class="text-yellow" href="#"><i class="fa fa-square"></i></a></li>
                                <li><a class="text-orange" href="#"><i class="fa fa-square"></i></a></li>
                                <li><a class="text-red" href="#"><i class="fa fa-square"></i></a></li>
                                <li><a class="text-purple" href="#"><i class="fa fa-square"></i></a></li>
                                <li><a class="text-fuchsia" href="#"><i class="fa fa-square"></i></a></li>
                                <li><a class="text-muted" href="#"><i class="fa fa-square"></i></a></li>
                                <li><a class="text-navy" href="#"><i class="fa fa-square"></i></a></li>
                            </ul>
                        </div>
                        <div class="input-group" style="margin-bottom:10px;">
                            <span class="input-group-addon"><i class="fa fa-list-ol fa-fw"></i></span>
                            <input id="event-title" name="row[title]" data-rule="required" type="text" class="form-control" placeholder="<?php echo __('Title tips'); ?>">
                        </div>
                        <div class="input-group" style="margin-bottom:10px;">
                            <span class="input-group-addon"><i class="fa fa-link fa-fw"></i></span>
                            <input id="event-url" name="row[url]" class="form-control" type="text" placeholder="<?php echo __('Link tips'); ?>">
                        </div>
                        <div class="input-group" style="margin-bottom:10px;">
                            <span class="input-group-addon"><i class="fa fa-tags fa-fw"></i></span>
                            <select name="row[classname]" class="form-control" id="">
                                <option value=""><?php echo __('None'); ?></option>
                                <option value="btn-dialog"><?php echo __('New Dialog'); ?></option>
                                <option value="btn-addtabs"><?php echo __('New Addtabs'); ?></option>
                                <option value="btn-ajax"><?php echo __('New Ajax'); ?></option>
                            </select>
                        </div>
                        <div class="input-group" style="margin-bottom:10px;">
                            <label for="c-type-event"> <input id="c-type-event" name="type" value="event" type="radio" checked=""> <?php echo __('Add to event'); ?></label> &nbsp;
                            <label for="c-type-calendar"> <input id="c-type-calendar" name="type" value="calendar" type="radio"> <?php echo __('Add to calendar'); ?></label>
                        </div>
                        <div class="input-group" id="daterange" style="margin-bottom:10px;display:none;">
                            <span class="input-group-addon"><i class="fa fa-calendar fa-fw"></i></span>
                            <input id="c-starttime" style="margin-bottom:-1px;" data-rule="required" class="form-control datetimepicker" data-date-format="YYYY-MM-DD HH:mm:ss" data-use-current="true" name="row[starttime]" type="text" value="<?php echo date('Y-m-d 00:00:00'); ?>">
                            <input id="c-endtime" data-rule="required" class="form-control datetimepicker" data-date-format="YYYY-MM-DD 00:00:00" data-use-current="true" name="row[endtime]" type="text" value="<?php echo date('Y-m-d 00:00:00'); ?>">
                        </div>
                        <div class="">
                            <button type="submit" class="btn btn-primary"><?php echo __('Add'); ?></button>
                            <button type="reset" class="btn btn-default"><?php echo __('Reset'); ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- /.col -->
        <div class="col-md-9">
            <div class="box box-solid">
                <div class="box-body no-padding">
                    <div id="calendar"></div>
                </div>
                <!-- /.box-body -->
            </div>
            <!-- /. box -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
</section>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <script src="/assets/js/require<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js" data-main="/assets/js/require-backend<?php echo \think\Config::get('app_debug')?'':'.min'; ?>.js?v=<?php echo htmlentities($site['version']); ?>"></script>
    </body>
</html>
