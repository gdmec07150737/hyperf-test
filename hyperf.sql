/*
 Navicat MySQL Data Transfer

 Source Server         : localhost
 Source Server Type    : MySQL
 Source Server Version : 50732
 Source Host           : localhost:3306
 Source Schema         : hyperf

 Target Server Type    : MySQL
 Target Server Version : 50732
 File Encoding         : 65001

 Date: 22/12/2020 22:56:16
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for user
-- ----------------------------
DROP TABLE IF EXISTS `user`;
CREATE TABLE `user`  (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `email` varchar(200) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '邮箱',
  `password` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '密码',
  `state` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'normal' COMMENT '用户状态（normal/正常、abnormal/异常）',
  `salt` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '加密盐',
  `created_at` varchar(20) CHARACTER SET utf32 COLLATE utf32_general_ci NOT NULL DEFAULT '' COMMENT '用户账号注册时间',
  `updated_at` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '用户账号修改时间',
  `token` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT '用户token',
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `email`(`email`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 29 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of user
-- ----------------------------
INSERT INTO `user` VALUES (1, '1354105797@qq.com', 'c6cc330860cc70f36e23599508752ce2', 'normal', '5fd9f1c4f0088', '2020-12-16 19:38:45', '2020-12-22 22:53:46', '');
INSERT INTO `user` VALUES (18, '1354105792@qq.com', '0693509d247ce08cdd0ce7753a1d8060', 'normal', '5fe1b349c48ac', '2020-12-22 16:50:17', '2020-12-22 22:28:15', '');
INSERT INTO `user` VALUES (28, '133@qq.com', '287b1b48511f559e0ae2b345b8b7852c', 'normal', '5fe1fbd8eaddd', '2020-12-22 21:59:52', '2020-12-22 22:50:04', 'eb70b145de4ae6780ea83c02fe3369c7');

SET FOREIGN_KEY_CHECKS = 1;
