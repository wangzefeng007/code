
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


--
-- 表的结构 `__PREFIX__csmding_dddepartment`
--
CREATE TABLE IF NOT EXISTS `__PREFIX__csmding_dddepartment` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `departmentid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '部门id',
  `name` varchar(200) NOT NULL COMMENT '部门名称',
  `parentid` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '父部门id，根部门为1',
  `order` varchar(50) DEFAULT '0' COMMENT '当前部门在父部门下的所有子部门中的排序值',
  `createdeptgroup` enum('true','false') DEFAULT NULL COMMENT '是否同步创建一个关联此部门的企业群:true=是,false=不是',
  `autoadduser` enum('true','false') DEFAULT NULL COMMENT '是否有新人加入部门会自动加入该群:true=是,false=不是',
  `depthiding` enum('true','false') DEFAULT NULL COMMENT '是否隐藏部门:true=是,false=不是',
  `deptperimits` varchar(200) DEFAULT NULL,
  `deptpermits` varchar(200) DEFAULT NULL COMMENT '可以查看指定隐藏部门的其他部门列表',
  `userperimits` varchar(200) DEFAULT NULL,
  `userpermits` varchar(200) DEFAULT NULL COMMENT '可以查看指定隐藏部门的其他人员列表',
  `outerdept` enum('true','false') DEFAULT NULL COMMENT '是否本部门的员工仅可见员工自己:true=是,false=不是',
  `outerpermitdepts` varchar(200) DEFAULT NULL COMMENT 'outerpermitdepts:true=是,false=不是',
  `outerpermitusers` varchar(200) DEFAULT NULL COMMENT 'outerpermitusers:true=是,false=不是',
  `orgdeptowner` varchar(200) DEFAULT NULL COMMENT '企业群群主',
  `deptmanageruseridlist` varchar(200) DEFAULT NULL COMMENT '部门的主管列表',
  `sourceidentifier` varchar(200) DEFAULT NULL COMMENT '部门标识字段',
  `groupcontainsubdept` varchar(200) DEFAULT NULL COMMENT '部门群是否包含子部门',
  `deptgroupchatid` varchar(200) DEFAULT NULL,
  `ext` varchar(200) DEFAULT NULL COMMENT '部门自定义字段',
  `status` enum('normal','hidden') NOT NULL DEFAULT 'normal' COMMENT '状态',
  `createtime` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updatetime` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '更新时间',
  `csmadmin_depart_id` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '对应组织架构',
  `b1` varchar(100) DEFAULT NULL COMMENT '备用字段1',
  `b2` varchar(100) DEFAULT NULL COMMENT '备用字段2',
  `b3` varchar(100) DEFAULT NULL COMMENT '备用字段3',
  `b4` varchar(100) DEFAULT NULL COMMENT '备用字段4',
  `b5` varchar(100) DEFAULT NULL COMMENT '备用字段5',
  `b6` varchar(100) DEFAULT NULL COMMENT '备用字段6',
  `b7` varchar(100) DEFAULT NULL COMMENT '备用字段7',
  `b8` varchar(100) DEFAULT NULL COMMENT '备用字段8',
  `b9` varchar(100) DEFAULT NULL COMMENT '备用字段9',
  `remoteversion` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`,`createtime`)
) ENGINE=InnoDB AUTO_INCREMENT=120 DEFAULT CHARSET=utf8 COMMENT='钉钉部门表'

;
 
--
-- 表的结构 `__PREFIX__csmding_dduser`
--
CREATE TABLE IF NOT EXISTS `__PREFIX__csmding_dduser` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `userid` varchar(200) NOT NULL COMMENT '员工在当前企业内的唯一标识',
  `unionid` varchar(200) NOT NULL COMMENT '员工在当前开发者企业账号范围内的唯一标识',
  `name` varchar(200) NOT NULL COMMENT '员工名字',
  `tel` varchar(200) DEFAULT NULL COMMENT '分机号',
  `workplace` varchar(200) DEFAULT NULL COMMENT '办公地点',
  `remark` text DEFAULT NULL COMMENT '备注',
  `mobile` varchar(200) DEFAULT NULL COMMENT '手机号码',
  `email` varchar(200) DEFAULT NULL COMMENT '员工的电子邮箱',
  `orgemail` varchar(200) DEFAULT NULL COMMENT '员工的企业邮箱',
  `orderindepts` varchar(200) DEFAULT NULL COMMENT '在对应的部门中的排序',
  `active` enum('true','false') DEFAULT NULL COMMENT '是否已经激活:true=是,false=不是',
  `isadmin` enum('true','false') DEFAULT NULL COMMENT '是否为企业的管理员:true=是,false=不是',
  `isboss` enum('true','false') DEFAULT NULL COMMENT '是否为企业的老板:true=是,false=不是',
  `isleaderindepts` varchar(200) DEFAULT NULL COMMENT '在对应的部门中是否为主管',
  `ishide` enum('true','false') DEFAULT NULL COMMENT '是否号码隐藏:true=是,false=不是',
  `position` varchar(200) DEFAULT NULL COMMENT '职位信息',
  `avatar` varchar(200) DEFAULT NULL COMMENT '头像url',
  `hireddate` int(10) unsigned DEFAULT 0 COMMENT '入职时间',
  `jobnumber` varchar(200) DEFAULT NULL COMMENT '员工工号',
  `extattr` text DEFAULT NULL COMMENT '扩展属性',
  `issenior` enum('true','false') DEFAULT NULL COMMENT '是否是高管:true=是,false=不是',
  `statecode` varchar(200) DEFAULT NULL COMMENT '国家地区码',
  `order` varchar(50) DEFAULT NULL COMMENT '表示人员在此部门中的排序',
  `isleader` enum('true','false') DEFAULT NULL COMMENT '是否是部门的主管',
  `status` enum('normal','hidden') NOT NULL DEFAULT 'normal' COMMENT '状态',
  `createtime` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updatetime` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '更新时间',
  `faadmin_id` int(10) DEFAULT 0 COMMENT '管理员帐号',
  `fauser_id` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '会员帐号',
  `faisactivie` enum('true','false') DEFAULT 'false' COMMENT 'FA是否激活:true=是,false=不是',
  `faisfilladmin` enum('true','false') DEFAULT 'false' COMMENT 'FA是否写入帐号:true=是,false=不是',
  `b1` varchar(100) DEFAULT NULL COMMENT '备用字段1',
  `b2` varchar(100) DEFAULT NULL COMMENT '备用字段2',
  `b3` varchar(100) DEFAULT NULL COMMENT '备用字段3',
  `b4` varchar(100) DEFAULT NULL COMMENT '备用字段4',
  `b5` varchar(100) DEFAULT NULL COMMENT '备用字段5',
  `b6` varchar(100) DEFAULT NULL COMMENT '备用字段6',
  `b7` varchar(100) DEFAULT NULL COMMENT '备用字段7',
  `b8` varchar(100) DEFAULT NULL COMMENT '备用字段8',
  `b9` varchar(100) DEFAULT NULL COMMENT '备用字段9',
  `remoteversion` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=282 DEFAULT CHARSET=utf8 COMMENT='钉钉员工表'

;
 
 
 --
-- 表的结构 `__PREFIX__csmding_dduser2depart`
--
CREATE TABLE IF NOT EXISTS `__PREFIX__csmding_dduser2depart` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `csmding_dduser_id` int(10) unsigned NOT NULL COMMENT '员工',
  `csmding_dddepartment_id` int(10) unsigned NOT NULL COMMENT '部门',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1481 DEFAULT CHARSET=utf8 COMMENT='钉钉员工任职部门表'
;
 
 
 --
-- 表的结构 `__PREFIX__csmding_msgmedia`
--
CREATE TABLE IF NOT EXISTS `__PREFIX__csmding_msgmedia` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `image` varchar(255) DEFAULT NULL COMMENT '图片',
  `mediaid` varchar(200) DEFAULT NULL COMMENT 'media',
  `createtime` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updatetime` int(10) unsigned DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COMMENT='Media'
;
 
 
 --
-- 表的结构 `__PREFIX__csmding_msgsend`
--
CREATE TABLE IF NOT EXISTS `__PREFIX__csmding_msgsend` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `name` varchar(200) NOT NULL COMMENT '消息名称',
  `msgtype` enum('oa') NOT NULL COMMENT '消息类型',
  `oaheadtext` varchar(200) DEFAULT NULL COMMENT '消息体头部',
  `oabodytitle` varchar(200) DEFAULT NULL COMMENT '消息体标题',
  `oaheadbgcolor` varchar(200) DEFAULT NULL COMMENT '标题颜色',
  `oamessageurl` varchar(200) DEFAULT NULL COMMENT '消息链接',
  `oabodyimage` varchar(200) DEFAULT NULL COMMENT '图片',
  `oabodycontent` text DEFAULT NULL COMMENT '正文内容',
  `oabodyform_json` varchar(255) DEFAULT NULL COMMENT '表单',
  `oabodyrich_json` varchar(255) DEFAULT NULL COMMENT '富文本',
  `sendrange` enum('all','byuser','bydepart') DEFAULT NULL COMMENT '发送范围:all=全部,byuser=按人员,bydepart=按部门',
  `csmding_dduser_ids` varchar(200) DEFAULT NULL COMMENT '发送用户',
  `csmding_dddepartment_ids` varchar(200) DEFAULT NULL COMMENT '发送部门',
  `msgtaskid` varchar(200) DEFAULT NULL COMMENT '发送任务',
  `admin_id` int(10) DEFAULT NULL COMMENT '操作人',
  `hassend` enum('Y','N') DEFAULT 'N' COMMENT '是否推送:Y=已经发送,N=未发送',
  `sendtime` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '推送时间',
  `createtime` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updatetime` int(10) unsigned DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COMMENT='消息推送'
;
 
 
 --
-- 表的结构 `__PREFIX__csmding_msgsendtask`
--
CREATE TABLE IF NOT EXISTS `__PREFIX__csmding_msgsendtask` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `csmding_msgsend_id` int(11) DEFAULT NULL COMMENT '推送消息',
  `msgtype` enum('oa') NOT NULL COMMENT '消息类型:oa=OA',
  `agent_id` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '应用agentId',
  `userid_list` varchar(200) DEFAULT NULL COMMENT '发送用户dduserids',
  `dept_id_list` varchar(200) DEFAULT NULL COMMENT '发送部门dddepartids',
  `to_all_user` varchar(200) DEFAULT NULL COMMENT '是否发送给企业全部用户',
  `msg` text DEFAULT NULL COMMENT '消息内容',
  `msgtaskid` varchar(200) DEFAULT NULL COMMENT '创建的发送任务id',
  `msgtaskerrcode` varchar(200) DEFAULT NULL COMMENT '任务返回码',
  `msgtaskerrmsg` varchar(200) DEFAULT NULL COMMENT '任务结果描述',
  `msgtaskprogress` int(10) DEFAULT NULL COMMENT '任务处理的百分比',
  `msgtaskstatus` enum('0','1','2') DEFAULT NULL COMMENT '任务执行状态:0=未开始,1=处理中,2=处理完毕',
  `sendtime` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '推送时间',
  `createtime` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updatetime` int(10) unsigned DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COMMENT='消息推送任务'
;
 
 
 --
-- 表的结构 `__PREFIX__csmding_porletarticle`
--
CREATE TABLE IF NOT EXISTS `__PREFIX__csmding_porletarticle` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `title` varchar(200) NOT NULL COMMENT '资讯标题',
  `intro` text DEFAULT NULL COMMENT '资讯简介',
  `type` enum('content','url') DEFAULT 'content' COMMENT '资讯类型:content=文章类型,url=链接类型',
  `url` varchar(255) NOT NULL COMMENT '链接地址',
  `image` varchar(200) NOT NULL COMMENT '资讯图片',
  `content` text DEFAULT NULL COMMENT '资讯详情',
  `weigh` int(11) DEFAULT NULL COMMENT '排序',
  `status` enum('normal','hidden') NOT NULL DEFAULT 'normal' COMMENT '状态',
  `createtime` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updatetime` int(10) unsigned DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='porlet资讯'

;
 
 
 --
-- 表的结构 `__PREFIX__csmding_porletbanner`
--
CREATE TABLE IF NOT EXISTS `__PREFIX__csmding_porletbanner` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `image` varchar(200) NOT NULL COMMENT '图片',
  `url` varchar(255) DEFAULT NULL COMMENT '链接',
  `weigh` int(11) DEFAULT NULL COMMENT '排序',
  `status` enum('normal','hidden') NOT NULL DEFAULT 'normal' COMMENT '状态',
  `createtime` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updatetime` int(10) unsigned DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='porlet banner'
;
 
 
 --
-- 表的结构 `__PREFIX__csmding_porletfunc`
--
CREATE TABLE IF NOT EXISTS `__PREFIX__csmding_porletfunc` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'id',
  `name` varchar(200) NOT NULL COMMENT '名称',
  `url` varchar(255) NOT NULL COMMENT '链接地址',
  `image` varchar(200) NOT NULL COMMENT '图片',
  `content` text DEFAULT NULL COMMENT '描述',
  `weigh` int(11) DEFAULT NULL COMMENT '排序',
  `status` enum('normal','hidden') NOT NULL DEFAULT 'normal' COMMENT '状态',
  `createtime` int(10) unsigned NOT NULL DEFAULT 0 COMMENT '创建时间',
  `updatetime` int(10) unsigned DEFAULT 0 COMMENT '更新时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='porlet功能点'
;


insert  into `__PREFIX__csmding_porletarticle`(`id`,`title`,`intro`,`type`,`url`,`image`,`content`,`weigh`,`status`,`createtime`,`updatetime`) values (1,'插件使用技巧一：快速调用钉钉接口','一款简洁强大的富文本编辑器，拥有远程下载图片、QQ粘贴上传、拖拽上传、Word粘贴上传图片、涂鸦上传、多媒体支持、附件上传、插入动态地图等功能','content','','https://cdn.fastadmin.net/uploads/addons/vote.png!/fwfh/300x200','<p>本插件提供了后台接口和前端接口的调用示例，具体见【钉钉对接套件-微应用管理-微应用首页】，切记用钉钉手机访问</p><p><br /></p><p>另外本插件还提供了调用钉钉SDK的代码生成器，具体见【钉钉对接套件-代码生成器】</p>',2,'normal',1589603804,1590154606),(2,'插件功能清单','将钉钉的PHP SDK按照fastadmin的标准导入，提供登录、推送消息等功能','content','','https://cdn.fastadmin.net/uploads/addons/geetest.png!/fwfh/300x200','<p>1. 将钉钉的PHP SDK按照fastadmin的标准导入</p><p>2. 钉钉通讯录同步到本地</p><p>3. 支持钉钉PC扫码登录，钉钉移动端免登陆</p><p>4. 钉钉服务端API 和 JSAPI 接口调用示例</p><p>5. 提供钉钉的微应用门户</p><p>6. 钉钉图文消息推送，可全员推送，按照部门推送和按照人员推送</p><p>7. 生成钉钉SDK调用代码</p>',3,'normal',1589603869,1590077512);
insert  into `__PREFIX__csmding_porletbanner`(`id`,`image`,`url`,`weigh`,`status`,`createtime`,`updatetime`) values (1,'https://cdn.fastadmin.net/uploads/20191204/99995ef99ee436224565f73406eeecc4.png!/fwfh/300x200','https://www.aliyun.com/activity/daily/cloud',3,'normal',1589602633,1590154266),(3,'https://cdn.fastadmin.net/uploads/addons/blog.png!/fwfh/300x200','https://www.fastadmin.net/',2,'normal',1589602644,1590154372);
insert  into `__PREFIX__csmding_porletfunc`(`id`,`name`,`url`,`image`,`content`,`weigh`,`status`,`createtime`,`updatetime`) values (1,'个人信息','general/profile','https://cdn.fastadmin.net/assets/img/icon/pencil.svg','NULL ',3,'normal',1589634485,1589731406),(2,'JSAPI示例','csmding/dingtest/jstest','https://cdn.fastadmin.net/assets/img/icon/browser.svg','NULL ',2,'normal',1589634524,1590074332),(3,'接口示例','csmding/dingtest/webservicetest','https://cdn.fastadmin.net/assets/img/icon/airplane.svg','',1,'normal',1589732147,1590074344);


 
COMMIT;