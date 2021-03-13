/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50726
Source Host           : localhost:3306
Source Database       : fire

Target Server Type    : MYSQL
Target Server Version : 50726
File Encoding         : 65001

Date: 2021-03-11 10:26:29
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for buildingcategory
-- ----------------------------
DROP TABLE IF EXISTS `buildingcategory`;
CREATE TABLE `buildingcategory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `cate_name` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------

-- ----------------------------
-- Table structure for buildings
-- ----------------------------
DROP TABLE IF EXISTS `buildings`;
CREATE TABLE `buildings` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  `building_type` tinyint(5) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `max_capacity` varchar(64) DEFAULT NULL,
  `area` int(10) DEFAULT '0' COMMENT 'area',
  `limit_num` int(11) DEFAULT '0' COMMENT 'max subscribe',
  `is_del` tinyint(2) NOT NULL DEFAULT '0' COMMENT '1:delete,0:live',
  `building_code` varchar(32) DEFAULT '',
  `char_two_code` varchar(4) DEFAULT '',
  `create_time` datetime DEFAULT NULL,
  `update_time` datetime DEFAULT NULL COMMENT 'update_time',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of buildings
-- ----------------------------
INSERT INTO `buildings` VALUES ('1', 'Luddy Hall', '2', 'address：700 N Woodlawn Ave', '500', '130064', '3', '0', 'BL404', 'IF', '2021-02-24 10:32:16', '2021-02-24 10:32:16');
INSERT INTO `buildings` VALUES ('2', 'Hodge Hall', '2', 'address：1309 E 10th St', '700', '364918', '3', '0', 'BL451', 'HH', '2021-02-24 10:32:16', '2021-02-24 10:32:16');
INSERT INTO `buildings` VALUES ('3', 'Ballantine Hall', '2', 'address：1020 E Kirkwood Ave', '500', '250711', '3', '0', 'BL111', 'BH', '2021-02-24 10:32:16', '2021-02-24 10:32:16');
INSERT INTO `buildings` VALUES ('4', 'Teter Quad', '1', 'address：501 N Sunrise Dr', '800', '300927', '3', '0', 'BL243', 'TE', '2021-02-24 10:32:16', '2021-02-24 10:32:16');
INSERT INTO `buildings` VALUES ('5', 'Wells Library', '3', 'address：1320 E 10th St', '1000', '557911', '0', '0', 'BL209', 'LI', '2021-02-24 10:32:16', '2021-02-24 10:32:16');
INSERT INTO `buildings` VALUES ('6', 'Chemistry', '2', 'address：800 E Kirkwood Ave', '500', '183387', '3', '0', 'BL071', 'CH', '2021-02-24 10:32:16', '2021-02-24 10:32:16');
INSERT INTO `buildings` VALUES ('7', 'Lilly Library', '3', 'address：1200 E 7th St', '300', '52516', '3', '0', 'BL155', 'LL', '2021-02-24 10:32:16', '2021-02-24 10:32:16');

-- ----------------------------
-- Table structure for subscribe
-- ----------------------------
DROP TABLE IF EXISTS `subscribe`;
CREATE TABLE `subscribe` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `building_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT '0' COMMENT 'user_id',
  `note` varchar(255) DEFAULT '' COMMENT 'note',
  `status` tinyint(1) DEFAULT '0' COMMENT 'check status: 0：wait check，1：pass，2：fail',
  `create_time` datetime NOT NULL COMMENT 'create_time',
  `subscribe_time` datetime NOT NULL COMMENT 'subscribe time',
  `update_time` datetime DEFAULT NULL COMMENT 'update_time',
  `subscribe_name` varchar(255) DEFAULT NULL COMMENT 'subscribe username',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=9 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of subscribe
-- ----------------------------
INSERT INTO `subscribe` VALUES ('5', '7', '26', '5555', '0', '2021-03-05 17:43:52', '2021-02-05 18:20:00', '2021-03-05 17:43:52', 'users');
INSERT INTO `subscribe` VALUES ('6', '7', '26', 'yyeee', '0', '2021-03-05 17:57:26', '2021-02-01 17:57:00', '2021-03-05 17:57:26', 'users');
INSERT INTO `subscribe` VALUES ('7', '1', '3', '5555', '0', '2021-03-09 03:12:21', '2021-03-31 15:30:00', '2021-03-09 03:12:21', 'admins');
INSERT INTO `subscribe` VALUES ('8', '1', '1', '55555', '0', '2021-03-09 05:29:58', '2022-03-08 18:29:00', '2021-03-09 05:29:58', 'y l');

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user` (
  `uid` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(255) DEFAULT NULL,
  `password` varchar(255) DEFAULT '' COMMENT 'email',
  `email` varchar(255) DEFAULT NULL,
  `create_time` datetime DEFAULT NULL COMMENT 'create_time',
  `update_time` datetime DEFAULT NULL COMMENT 'update_time',
  `roles` varchar(128) NOT NULL COMMENT 'menus_id',
  `fname` varchar(255) DEFAULT '' COMMENT 'first_name',
  `lname` varchar(255) DEFAULT '' COMMENT 'last_name',
  PRIMARY KEY (`uid`)
) ENGINE=MyISAM AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES ('1', 'admin', 'c4ca4238a0b923820dcc509a6f75849b', '111@iu.edu', '2021-03-09 16:06:19', '2021-03-09 16:06:22', '1', 'y', 'l');
INSERT INTO `user` VALUES ('2', 'user', 'd41d8cd98f00b204e9800998ecf8427e', 'e@gmail.com', '2021-03-03 21:23:34', '2021-03-03 21:23:34', '', 'f1', 'l1');
INSERT INTO `user` VALUES ('3', 'admins', 'c4ca4238a0b923820dcc509a6f75849b', '1@iu.edu', '2021-03-05 14:08:45', '2021-03-05 14:08:45', '', 'f2', 'l2');

-- ----------------------------
-- Records of buildingcategory
-- ----------------------------
INSERT INTO `buildingcategory` VALUES ('1', 'Dormitary');
INSERT INTO `buildingcategory` VALUES ('2', ' Academic');
INSERT INTO `buildingcategory` VALUES ('3', 'Library');