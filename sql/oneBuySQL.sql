/*
Navicat MySQL Data Transfer

Source Server         : 一元购正式
Source Server Version : 50543
Source Host           : 119.29.84.51:3306
Source Database       : oneBuy

Target Server Type    : MYSQL
Target Server Version : 50543
File Encoding         : 65001

Date: 2016-03-10 10:25:38
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for advertisement
-- ----------------------------
DROP TABLE IF EXISTS `advertisement`;
CREATE TABLE `advertisement` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL,
  `adv_name` varchar(60) DEFAULT NULL COMMENT '轮播图名称',
  `adv_img` varchar(255) DEFAULT NULL COMMENT '轮播图片',
  `adv_url` varchar(255) DEFAULT NULL COMMENT '轮播图url路劲',
  `sort_order` int(2) DEFAULT NULL COMMENT '排序',
  `add_time` int(11) DEFAULT NULL COMMENT '轮播图创建时间',
  `update_time` int(11) DEFAULT NULL COMMENT '轮播图修改时间',
  `is_on` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for article
-- ----------------------------
DROP TABLE IF EXISTS `article`;
CREATE TABLE `article` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL COMMENT '标题',
  `content` text COMMENT '内容',
  `pic` varchar(255) DEFAULT NULL COMMENT '图片',
  `is_show` tinyint(4) DEFAULT NULL COMMENT '是否显示',
  `add_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `is_on` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for bill
-- ----------------------------
DROP TABLE IF EXISTS `bill`;
CREATE TABLE `bill` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `address_id` int(11) DEFAULT NULL,
  `thematic_id` int(11) DEFAULT NULL,
  `record_id` int(11) DEFAULT NULL COMMENT 'record表id',
  `company_id` int(11) DEFAULT NULL,
  `goods_id` int(11) DEFAULT NULL,
  `bill_sn` varchar(50) DEFAULT NULL COMMENT '订单编号',
  `code` varchar(50) DEFAULT NULL,
  `status` int(11) DEFAULT '0' COMMENT '0待确认1已确认,2已发货,3已收货,4已晒单',
  `gameID` varchar(50) DEFAULT NULL COMMENT '点卡或代金券id',
  `game_img` varchar(255) DEFAULT NULL COMMENT '点卡充值快照',
  `is_confirm` tinyint(4) DEFAULT '0' COMMENT '确认订单(发货,确认此交易)',
  `is_post` tinyint(4) DEFAULT '0' COMMENT '是否发送',
  `is_cancel` tinyint(4) DEFAULT '0' COMMENT '是否取消订单',
  `add_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `is_on` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for code
-- ----------------------------
DROP TABLE IF EXISTS `code`;
CREATE TABLE `code` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goods_id` int(11) DEFAULT NULL,
  `thematic_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `code` varchar(50) DEFAULT NULL,
  `key` int(11) DEFAULT NULL COMMENT '抽码的排序字段',
  `is_use` tinyint(1) DEFAULT '0',
  `is_get` int(11) DEFAULT '0',
  `is_lucky` tinyint(1) DEFAULT '0',
  `add_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `is_on` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `select` (`is_on`,`goods_id`,`code`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=597078 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for company
-- ----------------------------
DROP TABLE IF EXISTS `company`;
CREATE TABLE `company` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_name` varchar(255) DEFAULT NULL COMMENT '专区名字',
  `QR_code` varchar(255) DEFAULT NULL COMMENT '二维码',
  `add_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `is_on` int(11) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for goods
-- ----------------------------
DROP TABLE IF EXISTS `goods`;
CREATE TABLE `goods` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goods_sn` varchar(50) DEFAULT NULL COMMENT '商品编号',
  `company_id` int(11) DEFAULT NULL COMMENT '专区id',
  `thematic_id` int(11) DEFAULT NULL COMMENT '专题的id',
  `goods_name` varchar(100) DEFAULT NULL COMMENT '商品的名称',
  `goods_title` varchar(100) DEFAULT NULL COMMENT '商品标题',
  `upload_date` int(11) DEFAULT NULL COMMENT '商品的上架时间',
  `cost_price` int(11) DEFAULT NULL COMMENT '成本价',
  `price` int(11) DEFAULT NULL COMMENT '销售价格，整数',
  `nature` int(11) DEFAULT '0' COMMENT '0实物1代金券2点卡',
  `limit_num` int(11) DEFAULT NULL COMMENT '限购人数',
  `goods_desc` text COMMENT '商品详情//百度编辑器',
  `goods_album` varchar(500) DEFAULT NULL COMMENT '商品相册,6张相片',
  `goods_thumb` varchar(255) DEFAULT NULL COMMENT '商品缩略图',
  `goods_img` varchar(255) DEFAULT NULL COMMENT '商品原图',
  `free_post` tinyint(4) NOT NULL COMMENT '是否包邮1包0不包',
  `sort_order` int(11) DEFAULT NULL COMMENT '排序',
  `is_show` tinyint(4) DEFAULT '1' COMMENT '是否前台显示',
  `add_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `is_on` int(11) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=64 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for logistics
-- ----------------------------
DROP TABLE IF EXISTS `logistics`;
CREATE TABLE `logistics` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL,
  `logistics_number` varchar(20) DEFAULT NULL COMMENT '物流单号',
  `logistics_name` varchar(50) DEFAULT NULL COMMENT '物流名称',
  `logistics_status` int(11) DEFAULT '0' COMMENT '0已发送，1揽件，2疑难，3签收，4退签，5派件，6退回',
  `bill_id` int(11) DEFAULT NULL COMMENT '订单表id',
  `user_address_id` int(11) DEFAULT NULL COMMENT '用户id',
  `add_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `is_on` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for logistics_data
-- ----------------------------
DROP TABLE IF EXISTS `logistics_data`;
CREATE TABLE `logistics_data` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `logistics_number` varchar(100) DEFAULT NULL,
  `data` text,
  `update_time` int(11) DEFAULT NULL,
  `is_on` int(11) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=44 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for manager
-- ----------------------------
DROP TABLE IF EXISTS `manager`;
CREATE TABLE `manager` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `manager_name` varchar(20) DEFAULT NULL COMMENT '管理员名',
  `manager_auth` varchar(20) DEFAULT NULL COMMENT '权限名称',
  `manager_email` varchar(50) DEFAULT NULL COMMENT 'email',
  `manager_phone` bigint(12) DEFAULT NULL COMMENT '手机',
  `manager_pwd` varchar(255) DEFAULT NULL COMMENT '密码',
  `role_id` int(11) DEFAULT NULL COMMENT '角色id',
  `manager_endlogin` int(11) DEFAULT NULL COMMENT '管理员最后登录时间',
  `last_ip` int(11) DEFAULT NULL,
  `add_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `is_on` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for manager_auth
-- ----------------------------
DROP TABLE IF EXISTS `manager_auth`;
CREATE TABLE `manager_auth` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `mold` varchar(255) DEFAULT NULL COMMENT '模块',
  `mold_name` varchar(255) DEFAULT NULL COMMENT '模块名称',
  `pid` int(11) DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for manager_role
-- ----------------------------
DROP TABLE IF EXISTS `manager_role`;
CREATE TABLE `manager_role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(30) DEFAULT NULL COMMENT '角色名称',
  `auth` text COMMENT '权限明细',
  `add_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `is_on` tinyint(2) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for media
-- ----------------------------
DROP TABLE IF EXISTS `media`;
CREATE TABLE `media` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` tinyint(4) NOT NULL DEFAULT '0' COMMENT '媒体类型：0,图片;',
  `path` varchar(1000) DEFAULT NULL COMMENT '原图路径',
  `w_h` varchar(255) DEFAULT NULL COMMENT '原图长宽高',
  `thumb_200_path` varchar(1000) DEFAULT NULL COMMENT '200宽缩略图路径',
  `thumb_200_w_h` varchar(255) DEFAULT NULL COMMENT '200宽缩略图长宽高',
  `thumb_360_path` varchar(1000) DEFAULT NULL COMMENT '360宽缩略图路径',
  `thumb_360_w_h` varchar(255) DEFAULT NULL COMMENT '360宽缩略图长宽高',
  `category` tinyint(2) DEFAULT '0' COMMENT '分类:1,图文素材;2,banner;3,ueditor图片',
  `media_id` varchar(255) DEFAULT '0' COMMENT '微信media_id',
  `is_admin` tinyint(1) DEFAULT '0' COMMENT '是否管理员上传',
  `add_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT '0',
  `is_on` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `is_admin` (`is_admin`,`is_on`,`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=COMPACT;

-- ----------------------------
-- Table structure for purchase
-- ----------------------------
DROP TABLE IF EXISTS `purchase`;
CREATE TABLE `purchase` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL COMMENT '用户id',
  `goods_id` int(11) DEFAULT NULL COMMENT '商品id',
  `thematic_id` int(11) DEFAULT NULL COMMENT '专题id',
  `record_id` int(11) DEFAULT NULL COMMENT 'record_id',
  `code` varchar(50) DEFAULT NULL COMMENT '认购码',
  `add_time` int(11) DEFAULT NULL,
  `ms_time` varchar(3) DEFAULT NULL COMMENT '毫秒',
  `update_time` int(11) DEFAULT NULL,
  `is_on` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `索引` (`id`,`user_id`,`goods_id`,`record_id`,`code`,`add_time`,`ms_time`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=1016 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for record
-- ----------------------------
DROP TABLE IF EXISTS `record`;
CREATE TABLE `record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goods_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `thematic_id` int(11) DEFAULT NULL,
  `wxPay_sn` varchar(255) DEFAULT NULL COMMENT '微信支付流水号',
  `num` int(11) DEFAULT NULL COMMENT '商品购买数量',
  `add_time` int(11) DEFAULT NULL,
  `ms_time` varchar(3) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `is_on` int(11) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=126 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for role
-- ----------------------------
DROP TABLE IF EXISTS `role`;
CREATE TABLE `role` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role_name` varchar(20) DEFAULT NULL COMMENT '权限名称',
  `role_status` int(11) DEFAULT NULL COMMENT '权限状态1为启用',
  `role_operation` int(11) DEFAULT NULL COMMENT '1为增加 2为删除 3为修改 4为查看',
  `role_remark` varchar(255) DEFAULT NULL COMMENT '备注',
  `add_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `is_on` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for roll
-- ----------------------------
DROP TABLE IF EXISTS `roll`;
CREATE TABLE `roll` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `goods_id` int(11) NOT NULL,
  `is_on` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for roll_record
-- ----------------------------
DROP TABLE IF EXISTS `roll_record`;
CREATE TABLE `roll_record` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `goods_id` int(11) DEFAULT NULL,
  `time` int(11) DEFAULT NULL,
  `shishicai` bigint(12) DEFAULT NULL,
  `B` int(11) DEFAULT NULL,
  `ms_time` varchar(3) DEFAULT NULL,
  `is_on` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=345 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for system
-- ----------------------------
DROP TABLE IF EXISTS `system`;
CREATE TABLE `system` (
  `id` int(11) NOT NULL,
  `buy_limit` int(11) DEFAULT '10' COMMENT '认购上限设置(百分数)',
  `roll_rule` int(11) DEFAULT NULL COMMENT '抽奖规则设置(从所有参与用户中随机抽取一位)',
  `buy_rule` int(11) DEFAULT NULL COMMENT '认购规则(认购人次随机发码，号码不连号)',
  `Bvalue` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for thematic
-- ----------------------------
DROP TABLE IF EXISTS `thematic`;
CREATE TABLE `thematic` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `company_id` int(11) DEFAULT NULL COMMENT '专区id',
  `thematic_name` varchar(100) DEFAULT NULL,
  `nature` int(11) DEFAULT NULL COMMENT '商品性质0实物／1虚拟券',
  `status` int(11) DEFAULT '0' COMMENT '0进行中,1即将揭晓,2已揭晓',
  `poster` varchar(255) DEFAULT NULL COMMENT '海报',
  `is_show` tinyint(4) DEFAULT '1',
  `add_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `is_on` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `phone` bigint(12) DEFAULT NULL COMMENT '手机',
  `openid` varchar(100) DEFAULT NULL COMMENT 'Openid',
  `user_img` varchar(255) DEFAULT NULL COMMENT '用户微信头像',
  `area` varchar(30) DEFAULT NULL,
  `last_login` int(11) DEFAULT NULL COMMENT '最后一次登陆时间',
  `last_ip` int(11) DEFAULT NULL COMMENT '最后一次登录ip',
  `nickname` varchar(100) DEFAULT NULL COMMENT '昵称',
  `is_follow` tinyint(1) DEFAULT NULL,
  `is_froze` tinyint(1) DEFAULT '0',
  `add_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `is_on` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for user_address
-- ----------------------------
DROP TABLE IF EXISTS `user_address`;
CREATE TABLE `user_address` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL COMMENT '用户id取值于用户表',
  `province` varchar(20) DEFAULT NULL COMMENT '地区 省',
  `city` varchar(20) DEFAULT NULL COMMENT '地区 市',
  `area` varchar(20) DEFAULT NULL COMMENT '地区 区',
  `street` varchar(255) DEFAULT NULL COMMENT '地区 街道',
  `postcode` int(7) DEFAULT NULL COMMENT '邮编',
  `is_default` tinyint(1) DEFAULT '0' COMMENT '选择默认地址',
  `mobile` bigint(12) DEFAULT NULL COMMENT '手机号码',
  `name` varchar(50) DEFAULT NULL COMMENT '收货人姓名',
  `add_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `is_on` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=23 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for user_time
-- ----------------------------
DROP TABLE IF EXISTS `user_time`;
CREATE TABLE `user_time` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `phone` bigint(12) DEFAULT NULL,
  `count` tinyint(2) DEFAULT NULL,
  `time` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for wechat
-- ----------------------------
DROP TABLE IF EXISTS `wechat`;
CREATE TABLE `wechat` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `msg_style` tinyint(1) DEFAULT NULL COMMENT '1:普通消息;2:事件消息;3:客服信息',
  `from_name` varchar(100) DEFAULT NULL COMMENT '发送者openid',
  `to_name` varchar(100) DEFAULT NULL COMMENT '接收公众号',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `area` varchar(20) DEFAULT NULL COMMENT '发送者地区',
  `msg_id` bigint(32) DEFAULT NULL COMMENT '消息ID',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for wechat_menu
-- ----------------------------
DROP TABLE IF EXISTS `wechat_menu`;
CREATE TABLE `wechat_menu` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `url` varchar(255) DEFAULT NULL COMMENT '关联URL',
  `keyword` varchar(100) DEFAULT NULL COMMENT '关联关键词',
  `title` varchar(50) DEFAULT NULL COMMENT '菜单名',
  `pid` int(10) DEFAULT '0' COMMENT '菜单级别：0：一级菜单',
  `sort` tinyint(4) DEFAULT '0' COMMENT '排序号',
  `add_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `is_on` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=495 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for wechat_news
-- ----------------------------
DROP TABLE IF EXISTS `wechat_news`;
CREATE TABLE `wechat_news` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(100) DEFAULT NULL COMMENT '文章标题',
  `author` varchar(30) DEFAULT NULL COMMENT '文章作者',
  `content` text COMMENT '文章内容',
  `img` varchar(255) DEFAULT NULL COMMENT '文章图片',
  `img_thumb` varchar(255) DEFAULT NULL COMMENT '文章缩略图',
  `url` varchar(1000) DEFAULT NULL COMMENT '文章链接',
  `desc` varchar(255) DEFAULT NULL COMMENT '文章描述',
  `sort` tinyint(4) DEFAULT '0' COMMENT '排序号',
  `add_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `is_on` tinyint(1) unsigned zerofill DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for wechat_receive
-- ----------------------------
DROP TABLE IF EXISTS `wechat_receive`;
CREATE TABLE `wechat_receive` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `msg_style` tinyint(1) DEFAULT NULL COMMENT '1:普通消息;2:事件消息;3:客服信息',
  `to_name` varchar(100) DEFAULT NULL COMMENT '接收公众号',
  `from_name` varchar(100) DEFAULT NULL COMMENT '发送者openid',
  `create_time` int(11) DEFAULT NULL COMMENT '创建时间',
  `msg_type` varchar(20) DEFAULT NULL COMMENT '消息类型',
  `content` text COMMENT '消息内容，文本消息',
  `pic_url` varchar(300) DEFAULT NULL COMMENT '图片网址，图片消息',
  `location_x` decimal(11,7) DEFAULT NULL COMMENT '地图x,位置消息、事件',
  `location_y` decimal(11,7) DEFAULT NULL COMMENT '地图y，位置消息、事件',
  `scale` int(11) DEFAULT NULL COMMENT '地图缩放，位置消息、事件',
  `lable` varchar(50) DEFAULT NULL COMMENT '地图标签，位图消息、事件',
  `title` varchar(100) DEFAULT NULL COMMENT '标题，链接消息',
  `description` varchar(200) DEFAULT NULL COMMENT '描述、链接消息',
  `url` varchar(300) DEFAULT NULL COMMENT '网址、链接消息',
  `event` varchar(20) DEFAULT NULL COMMENT '事件名称',
  `event_key` varchar(100) DEFAULT NULL COMMENT '事件Key值，菜单事件、参数二维码事件',
  `ticket` varchar(100) DEFAULT NULL COMMENT '二维码ticket、参数二维码关注、扫描事件',
  `msg_id` bigint(32) DEFAULT NULL COMMENT '消息ID',
  `scene_id` varchar(40) DEFAULT NULL COMMENT '二维码场景ID',
  `kf_account` varchar(255) DEFAULT NULL COMMENT '客服时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1557 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Table structure for wechat_response
-- ----------------------------
DROP TABLE IF EXISTS `wechat_response`;
CREATE TABLE `wechat_response` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `type` tinyint(1) DEFAULT NULL COMMENT '回复类型:0,关键字;1,自动回复;2,关注回复;',
  `rsp_type` tinyint(1) DEFAULT NULL COMMENT '回复类型:0,文本类型;1,图文类型;2,关键字',
  `keywords` varchar(100) DEFAULT NULL COMMENT '关联关键词',
  `text` text COMMENT '答复内容',
  `news` varchar(600) DEFAULT NULL,
  `add_time` int(11) DEFAULT NULL,
  `update_time` int(11) DEFAULT NULL,
  `is_on` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
