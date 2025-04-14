/*
 Navicat Premium Dump SQL

 Source Server         : MySql
 Source Server Type    : MySQL
 Source Server Version : 80030 (8.0.30)
 Source Host           : localhost:3306
 Source Schema         : tdkt5

 Target Server Type    : MySQL
 Target Server Version : 80030 (8.0.30)
 File Encoding         : 65001

 Date: 14/04/2025 17:23:09
*/

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ----------------------------
-- Table structure for migrations
-- ----------------------------
DROP TABLE IF EXISTS `migrations`;
CREATE TABLE `migrations`  (
  `id` int UNSIGNED NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of migrations
-- ----------------------------
INSERT INTO `migrations` VALUES (1, '2019_12_14_000001_create_personal_access_tokens_table', 1);
INSERT INTO `migrations` VALUES (2, '2025_03_11_074941_add_api_token_to_tbltaikhoan', 1);

-- ----------------------------
-- Table structure for personal_access_tokens
-- ----------------------------
DROP TABLE IF EXISTS `personal_access_tokens`;
CREATE TABLE `personal_access_tokens`  (
  `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT,
  `tokenable_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `tokenable_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(64) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `abilities` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `expires_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`) USING BTREE,
  UNIQUE INDEX `personal_access_tokens_token_unique`(`token` ASC) USING BTREE,
  INDEX `personal_access_tokens_tokenable_type_tokenable_id_index`(`tokenable_type` ASC, `tokenable_id` ASC) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_unicode_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of personal_access_tokens
-- ----------------------------

-- ----------------------------
-- Table structure for tblcanhan
-- ----------------------------
DROP TABLE IF EXISTS `tblcanhan`;
CREATE TABLE `tblcanhan`  (
  `PK_MaCaNhan` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `FK_MaDonVi` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `sTenCaNhan` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `sTenChucVu` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `bGioiTinh` tinyint(1) NULL DEFAULT NULL,
  `FK_MaTaiKhoan` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`PK_MaCaNhan`) USING BTREE,
  INDEX `FK_MaDonVi`(`FK_MaDonVi` ASC) USING BTREE,
  INDEX `FK_MaTaiKhoan`(`FK_MaTaiKhoan` ASC) USING BTREE,
  CONSTRAINT `tblcanhan_ibfk_1` FOREIGN KEY (`FK_MaDonVi`) REFERENCES `tbldonvi` (`PK_MaDonVi`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `tblcanhan_ibfk_2` FOREIGN KEY (`FK_MaTaiKhoan`) REFERENCES `tbltaikhoan` (`PK_MaTaiKhoan`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tblcanhan
-- ----------------------------
INSERT INTO `tblcanhan` VALUES ('CN0001', 'dv_1', 'Ngô Minh Phương', 'giảng viên', 0, 'user5');
INSERT INTO `tblcanhan` VALUES ('CN0002', 'dv_1', 'Phạm Duy Trường', 'hé hé', 0, 'user7');
INSERT INTO `tblcanhan` VALUES ('CN0003', 'dv_1', 'Nguyễn Thị Thương', 'fsdfsdfds', 0, 'user8');
INSERT INTO `tblcanhan` VALUES ('CN0004', 'dv_1', 'Nguyễn Đình Khương', 'giảng viên', 0, 'usercanhan04');
INSERT INTO `tblcanhan` VALUES ('CN0005', 'dv_2', 'Nguyễn Thị Bích', 'giảng viên', 1, 'usertcnh_nv01');

-- ----------------------------
-- Table structure for tblcapdanhhieu
-- ----------------------------
DROP TABLE IF EXISTS `tblcapdanhhieu`;
CREATE TABLE `tblcapdanhhieu`  (
  `PK_MaCap` int NOT NULL AUTO_INCREMENT,
  `sTenCap` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  PRIMARY KEY (`PK_MaCap`) USING BTREE
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tblcapdanhhieu
-- ----------------------------
INSERT INTO `tblcapdanhhieu` VALUES (1, 'Cấp trường');
INSERT INTO `tblcapdanhhieu` VALUES (2, 'Cấp Bộ');

-- ----------------------------
-- Table structure for tbldanhhieu
-- ----------------------------
DROP TABLE IF EXISTS `tbldanhhieu`;
CREATE TABLE `tbldanhhieu`  (
  `PK_MaDanhHieu` int NOT NULL AUTO_INCREMENT,
  `sTenDanhHieu` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `FK_MaHinhThuc` int NULL DEFAULT NULL,
  `FK_MaLoaiDanhHieu` int NULL DEFAULT NULL,
  `FK_MaCap` int NULL DEFAULT NULL,
  `bTrangThai` tinyint(1) NULL DEFAULT NULL,
  PRIMARY KEY (`PK_MaDanhHieu`) USING BTREE,
  INDEX `FK_MaHinhThuc`(`FK_MaHinhThuc` ASC) USING BTREE,
  INDEX `FK_MaLoaiDanhHieu`(`FK_MaLoaiDanhHieu` ASC) USING BTREE,
  INDEX `FK_MaCap`(`FK_MaCap` ASC) USING BTREE,
  CONSTRAINT `tbldanhhieu_ibfk_1` FOREIGN KEY (`FK_MaHinhThuc`) REFERENCES `tblhinhthuc` (`PK_MaHinhThuc`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `tbldanhhieu_ibfk_2` FOREIGN KEY (`FK_MaLoaiDanhHieu`) REFERENCES `tblloaidanhhieu` (`PK_MaLoaiDanhHieu`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `tbldanhhieu_ibfk_3` FOREIGN KEY (`FK_MaCap`) REFERENCES `tblcapdanhhieu` (`PK_MaCap`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 24 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbldanhhieu
-- ----------------------------
INSERT INTO `tbldanhhieu` VALUES (1, 'Lao động tiên tiến', 1, 1, 1, 1);
INSERT INTO `tbldanhhieu` VALUES (2, 'Chiến sĩ thi đua cơ sở', 1, 1, 1, 1);
INSERT INTO `tbldanhhieu` VALUES (3, 'Giấy khen của hiệu trưởng', 1, 1, 1, 1);
INSERT INTO `tbldanhhieu` VALUES (4, 'Tập thể lao động tiên tiến', 1, 2, 1, 1);
INSERT INTO `tbldanhhieu` VALUES (5, 'Tập thể lao động xuất sắc', 1, 2, 2, 1);
INSERT INTO `tbldanhhieu` VALUES (6, 'Giấy khen của hiệu trưởng', 1, 2, 1, 1);
INSERT INTO `tbldanhhieu` VALUES (15, 'Chiến sĩ thi đua toàn quốc', 2, 1, 2, 0);
INSERT INTO `tbldanhhieu` VALUES (16, 'Chiến sĩ thi đua Bộ, ban, ngành, tỉnh', 2, 1, 2, 0);
INSERT INTO `tbldanhhieu` VALUES (17, 'Cờ thi đua của Chính phủ', 2, 2, 2, 0);
INSERT INTO `tbldanhhieu` VALUES (18, 'Cờ thi đua của Bộ, ban, ngành, tỉnh', 2, 2, 2, 0);
INSERT INTO `tbldanhhieu` VALUES (22, 'Giấy khen của hiệu trưởng', 2, 1, 1, 1);
INSERT INTO `tbldanhhieu` VALUES (23, 'Giấy khen của hiệu trưởng', 2, 2, 1, 1);

-- ----------------------------
-- Table structure for tbldexuat
-- ----------------------------
DROP TABLE IF EXISTS `tbldexuat`;
CREATE TABLE `tbldexuat`  (
  `PK_MaDeXuat` int NOT NULL AUTO_INCREMENT,
  `FK_User` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `FK_MaHoiDong` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `FK_MaDanhHieu` int NULL DEFAULT NULL,
  `iSoNguoiBau` int NULL DEFAULT NULL,
  `dNgayTao` datetime NULL DEFAULT NULL,
  `FK_MaDotXuat` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `FK_NguoiTao` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`PK_MaDeXuat`) USING BTREE,
  INDEX `FK_User`(`FK_User` ASC) USING BTREE,
  INDEX `FK_MaDanhHieu`(`FK_MaDanhHieu` ASC) USING BTREE,
  INDEX `FK_MaHoiDong`(`FK_MaHoiDong` ASC) USING BTREE,
  INDEX `tbldexuat_ibfk_4`(`FK_MaDotXuat` ASC) USING BTREE,
  INDEX `tbldexuat_ibfk_5`(`FK_NguoiTao` ASC) USING BTREE,
  CONSTRAINT `tbldexuat_ibfk_1` FOREIGN KEY (`FK_User`) REFERENCES `tbltaikhoan` (`PK_MaTaiKhoan`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `tbldexuat_ibfk_2` FOREIGN KEY (`FK_MaDanhHieu`) REFERENCES `tbldanhhieu` (`PK_MaDanhHieu`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `tbldexuat_ibfk_3` FOREIGN KEY (`FK_MaHoiDong`) REFERENCES `tblhoidong` (`PK_MaHoiDong`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `tbldexuat_ibfk_4` FOREIGN KEY (`FK_MaDotXuat`) REFERENCES `tbldotxuat` (`PK_MaDotXuat`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `tbldexuat_ibfk_5` FOREIGN KEY (`FK_NguoiTao`) REFERENCES `tbltaikhoan` (`PK_MaTaiKhoan`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 51 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbldexuat
-- ----------------------------
INSERT INTO `tbldexuat` VALUES (8, 'user5', 'dv_1-2024-2025', 1, 4, '2025-04-11 07:26:20', NULL, NULL);
INSERT INTO `tbldexuat` VALUES (9, 'user7', 'dv_1-2024-2025', 1, 4, '2025-04-11 07:26:20', NULL, NULL);
INSERT INTO `tbldexuat` VALUES (10, 'usercanhan04', 'dv_1-2024-2025', 2, 4, '2025-04-11 07:26:20', NULL, NULL);
INSERT INTO `tbldexuat` VALUES (11, 'user8', 'dv_1-2024-2025', 3, 4, '2025-04-11 07:26:20', NULL, NULL);
INSERT INTO `tbldexuat` VALUES (12, 'user7', 'dv_1-2024-2025', 3, 4, '2025-04-11 07:26:20', NULL, NULL);
INSERT INTO `tbldexuat` VALUES (13, 'user2', 'dv_1-2024-2025', 4, 4, '2025-04-11 07:26:20', NULL, NULL);
INSERT INTO `tbldexuat` VALUES (39, 'user5', NULL, 22, NULL, '2025-04-13 15:23:57', '2024-2025-2025-04-13', 'user2');
INSERT INTO `tbldexuat` VALUES (40, 'user7', NULL, 22, NULL, '2025-04-13 15:23:57', '2024-2025-2025-04-13', 'user2');
INSERT INTO `tbldexuat` VALUES (41, 'user8', NULL, 22, NULL, '2025-04-13 15:23:57', '2024-2025-2025-04-13', 'user2');
INSERT INTO `tbldexuat` VALUES (42, 'user2', NULL, 23, NULL, '2025-04-13 15:23:57', '2024-2025-2025-04-13', 'user2');
INSERT INTO `tbldexuat` VALUES (47, 'usertcnh_nv01', 'dv_2-2024-2025', 1, 33, '2025-04-13 15:14:54', NULL, NULL);
INSERT INTO `tbldexuat` VALUES (48, 'usertcnh_nv01', 'dv_2-2024-2025', 3, 33, '2025-04-13 15:14:54', NULL, NULL);
INSERT INTO `tbldexuat` VALUES (49, 'user3', 'dv_2-2024-2025', 6, 33, '2025-04-13 15:14:54', NULL, NULL);
INSERT INTO `tbldexuat` VALUES (50, 'user3', 'dv_2-2024-2025', 4, 33, '2025-04-13 15:14:54', NULL, NULL);

-- ----------------------------
-- Table structure for tbldonvi
-- ----------------------------
DROP TABLE IF EXISTS `tbldonvi`;
CREATE TABLE `tbldonvi`  (
  `PK_MaDonVi` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `sTenDonVi` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `FK_MaTaiKhoan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`PK_MaDonVi`) USING BTREE,
  INDEX `FK_MaTaiKhoan`(`FK_MaTaiKhoan` ASC) USING BTREE,
  CONSTRAINT `tbldonvi_ibfk_1` FOREIGN KEY (`FK_MaTaiKhoan`) REFERENCES `tbltaikhoan` (`PK_MaTaiKhoan`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbldonvi
-- ----------------------------
INSERT INTO `tbldonvi` VALUES ('dulich', 'Quản trị du lịch và lữ hành', 'userdv_dulich');
INSERT INTO `tbldonvi` VALUES ('dv_1', 'Công nghệ thông tin', 'user2');
INSERT INTO `tbldonvi` VALUES ('dv_2', 'Tài chính ngân hàng', 'user3');
INSERT INTO `tbldonvi` VALUES ('ketoan', 'Kế toán', 'user6');

-- ----------------------------
-- Table structure for tbldotthiduakhenthuong
-- ----------------------------
DROP TABLE IF EXISTS `tbldotthiduakhenthuong`;
CREATE TABLE `tbldotthiduakhenthuong`  (
  `PK_MaDot` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `dNgayTao` datetime NULL DEFAULT NULL,
  `bTrangThai` tinyint(1) NULL DEFAULT NULL,
  `iNamBatDau` int NULL DEFAULT NULL,
  `iNamKetThuc` int NULL DEFAULT NULL,
  `dHanBienBanDonVi` date NULL DEFAULT NULL,
  `dHanNopMinhChung` date NULL DEFAULT NULL,
  `dHanBienBanHoiDong` date NULL DEFAULT NULL,
  PRIMARY KEY (`PK_MaDot`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbldotthiduakhenthuong
-- ----------------------------
INSERT INTO `tbldotthiduakhenthuong` VALUES ('1999-2000', '2025-03-26 15:55:43', 0, 1999, 2000, NULL, NULL, NULL);
INSERT INTO `tbldotthiduakhenthuong` VALUES ('2000-2001', '2025-04-06 00:48:15', 0, 2000, 2001, NULL, NULL, NULL);
INSERT INTO `tbldotthiduakhenthuong` VALUES ('2001-2002', '2025-04-06 01:12:52', 0, 2001, 2002, '2025-04-18', '2025-04-30', '2025-04-13');
INSERT INTO `tbldotthiduakhenthuong` VALUES ('2002-2003', '2025-04-12 00:57:58', 0, 2002, 2003, NULL, NULL, NULL);
INSERT INTO `tbldotthiduakhenthuong` VALUES ('2003-2004', '2025-03-26 16:17:01', 0, 2003, 2004, NULL, NULL, NULL);
INSERT INTO `tbldotthiduakhenthuong` VALUES ('2007-2008', '2025-03-27 00:37:49', 0, 2007, 2008, '2025-04-24', '2025-04-17', NULL);
INSERT INTO `tbldotthiduakhenthuong` VALUES ('2008-2009', '2025-03-27 00:38:17', 0, 2008, 2009, NULL, NULL, NULL);
INSERT INTO `tbldotthiduakhenthuong` VALUES ('2010-2011', '2025-03-26 08:52:39', 0, 2010, 2011, NULL, NULL, NULL);
INSERT INTO `tbldotthiduakhenthuong` VALUES ('2023-2024', '2025-03-26 04:49:16', 0, 2023, 2024, NULL, NULL, NULL);
INSERT INTO `tbldotthiduakhenthuong` VALUES ('2024-2025', '2025-03-26 16:07:49', 1, 2024, 2025, '2025-04-24', NULL, NULL);

-- ----------------------------
-- Table structure for tbldotxuat
-- ----------------------------
DROP TABLE IF EXISTS `tbldotxuat`;
CREATE TABLE `tbldotxuat`  (
  `PK_MaDotXuat` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `dNgayBatDau` date NULL DEFAULT NULL,
  `dNgayKetThuc` date NULL DEFAULT NULL,
  `FK_MaDot` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `dHanNopMinhChung` date NULL DEFAULT NULL,
  `dHanBienBanHoiDong` date NULL DEFAULT NULL,
  `dHanNopDeXuat` date NULL DEFAULT NULL,
  `bTrangThai` tinyint(1) NULL DEFAULT NULL,
  PRIMARY KEY (`PK_MaDotXuat`) USING BTREE,
  INDEX `FK_MaDot`(`FK_MaDot` ASC) USING BTREE,
  CONSTRAINT `tbldotxuat_ibfk_1` FOREIGN KEY (`FK_MaDot`) REFERENCES `tbldotthiduakhenthuong` (`PK_MaDot`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbldotxuat
-- ----------------------------
INSERT INTO `tbldotxuat` VALUES ('2024-2025-2025-04-12', '2025-04-12', '2025-04-12', '2024-2025', '2025-04-12', '2025-04-26', '2025-04-12', 0);
INSERT INTO `tbldotxuat` VALUES ('2024-2025-2025-04-13', '2025-04-13', '2025-04-13', '2024-2025', '2025-04-19', '2025-04-19', '2025-04-19', 1);

-- ----------------------------
-- Table structure for tblhinhthuc
-- ----------------------------
DROP TABLE IF EXISTS `tblhinhthuc`;
CREATE TABLE `tblhinhthuc`  (
  `PK_MaHinhThuc` int NOT NULL,
  `sTenHinhThuc` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`PK_MaHinhThuc`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tblhinhthuc
-- ----------------------------
INSERT INTO `tblhinhthuc` VALUES (1, 'Theo đợt');
INSERT INTO `tblhinhthuc` VALUES (2, 'Đột xuất');

-- ----------------------------
-- Table structure for tblhoidong
-- ----------------------------
DROP TABLE IF EXISTS `tblhoidong`;
CREATE TABLE `tblhoidong`  (
  `PK_MaHoiDong` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `FK_MaTaiKhoan` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `FK_MaDot` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `dNgayTao` datetime NULL DEFAULT NULL,
  `FK_ChuTri` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `FK_ThuKy` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `dThoiGianHop` datetime NULL DEFAULT NULL,
  `sDiaChi` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `iSoNguoiThamDu` int NULL DEFAULT NULL,
  `iSoThanhVien` int NULL DEFAULT NULL,
  `sDuongDanBienBan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `sTenBienBan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `sSoHD` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `sGhiChu` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `FK_MaLoaiHD` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `FK_MaHinhThuc` int NULL DEFAULT NULL,
  `FK_MaDotXuat` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `FK_PhoChuTich` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `FK_PhoChuTichTT` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `sDuongDanKiemPhieu` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `sTenKiemPhieu` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`PK_MaHoiDong`) USING BTREE,
  INDEX `FK_ChuTri`(`FK_ChuTri` ASC) USING BTREE,
  INDEX `FK_ThuKy`(`FK_ThuKy` ASC) USING BTREE,
  INDEX `FK_MaDot`(`FK_MaDot` ASC) USING BTREE,
  INDEX `FK_MaTaiKhoan`(`FK_MaTaiKhoan` ASC) USING BTREE,
  INDEX `FK_MaLoaiHD`(`FK_MaLoaiHD` ASC) USING BTREE,
  INDEX `FK_MaHinhThuc`(`FK_MaHinhThuc` ASC) USING BTREE,
  INDEX `FK_MaDotXuat`(`FK_MaDotXuat` ASC) USING BTREE,
  INDEX `tblhoidong_ibfk_8`(`FK_PhoChuTich` ASC) USING BTREE,
  INDEX `tblhoidong_ibfk_9`(`FK_PhoChuTichTT` ASC) USING BTREE,
  CONSTRAINT `tblhoidong_ibfk_1` FOREIGN KEY (`FK_ChuTri`) REFERENCES `tbltaikhoan` (`PK_MaTaiKhoan`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `tblhoidong_ibfk_2` FOREIGN KEY (`FK_ThuKy`) REFERENCES `tbltaikhoan` (`PK_MaTaiKhoan`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `tblhoidong_ibfk_3` FOREIGN KEY (`FK_MaDot`) REFERENCES `tbldotthiduakhenthuong` (`PK_MaDot`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `tblhoidong_ibfk_4` FOREIGN KEY (`FK_MaTaiKhoan`) REFERENCES `tbltaikhoan` (`PK_MaTaiKhoan`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `tblhoidong_ibfk_5` FOREIGN KEY (`FK_MaLoaiHD`) REFERENCES `tblloaihoidong` (`PK_MaLoaiHD`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `tblhoidong_ibfk_6` FOREIGN KEY (`FK_MaHinhThuc`) REFERENCES `tblhinhthuc` (`PK_MaHinhThuc`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `tblhoidong_ibfk_7` FOREIGN KEY (`FK_MaDotXuat`) REFERENCES `tbldotxuat` (`PK_MaDotXuat`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `tblhoidong_ibfk_8` FOREIGN KEY (`FK_PhoChuTich`) REFERENCES `tbltaikhoan` (`PK_MaTaiKhoan`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `tblhoidong_ibfk_9` FOREIGN KEY (`FK_PhoChuTichTT`) REFERENCES `tbltaikhoan` (`PK_MaTaiKhoan`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tblhoidong
-- ----------------------------
INSERT INTO `tblhoidong` VALUES ('dv_1-2024-2025', 'user2', '2024-2025', '2025-04-11 14:26:20', 'user5', 'user7', '2025-04-16 14:25:00', 'fre', 4, 4, 'vanBanHoiDong/2024-2025/R9loQiyRnHvys0Ns67VD59vczAmSDi6jaXX8ISaA.docx', '21A100100291_NgoMinhPhuong_BTTH.docx', '2346', 'ghi chú', '1', 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `tblhoidong` VALUES ('dv_2-2024-2025', 'user3', '2024-2025', '2025-04-13 22:14:54', 'usertcnh_nv01', 'usertcnh_nv01', '2025-04-07 22:00:00', 'Định công', 33, 33, 'vanBanHoiDong/2024-2025/sX0jRjcVbKt8HIoGXUnfd4ziiBx3PP5oqC5H3fJu.docx', '21A100100291_NgoMinhPhuong_BTTH.docx', '2346', 'fdsfsdf', '1', 1, NULL, NULL, NULL, NULL, NULL);
INSERT INTO `tblhoidong` VALUES ('tdkt-2024-2025', 'user4', '2024-2025', '2025-04-13 21:35:15', 'user5', 'usercanhan04', '2025-04-25 14:27:00', 'Phòng họp', 33, 33, 'vanBanHoiDong/2024-2025/QAURs3PZ40Y2H4iklPUIyfGuSstSLJauppWsjVr0.docx', '21A100100291_NgoMinhPhuong_BTTH.docx', '23467', 'Ghi chú', '2', 1, NULL, 'user8', 'user7', 'vanBanHoiDong/2024-2025/5knV6iJmiyyU0CUTmlXqyo1t1h0iYfdkN0vS2kpa.pdf', '21A100100291_NgoMinhPhuong_BTTH.pdf');
INSERT INTO `tblhoidong` VALUES ('tdkt-2024-2025-2025-04-13', 'user4', '2024-2025', '2025-04-13 17:24:26', 'user5', 'usercanhan04', '2025-04-10 17:19:00', 'sedf', 33, 33, 'vanBanHoiDong//fy6QSo7Flk0pe41tvCrw3RQ2sDiXJtTuFgD0YOpY.pdf', '21A100100291_NgoMinhPhuong_BTTH.pdf', '123333', 'sdfsdfsdf', '2', 1, '2024-2025-2025-04-13', 'user8', 'user7', 'vanBanHoiDong//Bb7etye3EXatUsldP0attOEhCWkFcn4MiXtdzYnr.docx', '21A100100291_NgoMinhPhuong_BTTH.docx');

-- ----------------------------
-- Table structure for tblketqua
-- ----------------------------
DROP TABLE IF EXISTS `tblketqua`;
CREATE TABLE `tblketqua`  (
  `PK_MaKetQua` int NOT NULL AUTO_INCREMENT,
  `FK_MaDeXuat` int NULL DEFAULT NULL,
  `FK_MaHoiDong` varchar(60) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `iSoNguoiBau` int NULL DEFAULT NULL,
  `bDuyet` tinyint(1) NULL DEFAULT NULL,
  `dNgayDuyet` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`PK_MaKetQua`) USING BTREE,
  INDEX `FK_MaDeXuat`(`FK_MaDeXuat` ASC) USING BTREE,
  INDEX `FK_MaHoiDong`(`FK_MaHoiDong` ASC) USING BTREE,
  CONSTRAINT `tblketqua_ibfk_1` FOREIGN KEY (`FK_MaDeXuat`) REFERENCES `tbldexuat` (`PK_MaDeXuat`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `tblketqua_ibfk_2` FOREIGN KEY (`FK_MaHoiDong`) REFERENCES `tblhoidong` (`PK_MaHoiDong`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 78 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tblketqua
-- ----------------------------
INSERT INTO `tblketqua` VALUES (64, 10, 'tdkt-2024-2025', 33, 1, '2025-04-11 14:28:08');
INSERT INTO `tblketqua` VALUES (65, 8, 'tdkt-2024-2025', 32, 1, '2025-04-11 14:28:08');
INSERT INTO `tblketqua` VALUES (66, 11, 'tdkt-2024-2025', 31, 1, '2025-04-11 14:28:08');
INSERT INTO `tblketqua` VALUES (67, 9, 'tdkt-2024-2025', 33, 1, '2025-04-11 14:28:08');
INSERT INTO `tblketqua` VALUES (68, 12, 'tdkt-2024-2025', 33, 1, '2025-04-11 14:28:08');
INSERT INTO `tblketqua` VALUES (69, 13, 'tdkt-2024-2025', 23, 1, '2025-04-11 14:28:08');
INSERT INTO `tblketqua` VALUES (74, 39, 'tdkt-2024-2025-2025-04-13', 11, 1, '2025-04-13 21:38:22');
INSERT INTO `tblketqua` VALUES (75, 41, 'tdkt-2024-2025-2025-04-13', 33, 1, '2025-04-13 21:38:22');
INSERT INTO `tblketqua` VALUES (76, 40, 'tdkt-2024-2025-2025-04-13', 33, 1, '2025-04-13 21:38:22');
INSERT INTO `tblketqua` VALUES (77, 42, 'tdkt-2024-2025-2025-04-13', 33, 1, '2025-04-13 21:38:22');

-- ----------------------------
-- Table structure for tblloaidanhhieu
-- ----------------------------
DROP TABLE IF EXISTS `tblloaidanhhieu`;
CREATE TABLE `tblloaidanhhieu`  (
  `PK_MaLoaiDanhHieu` int NOT NULL,
  `sTenLoaiDanhHieu` varchar(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`PK_MaLoaiDanhHieu`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tblloaidanhhieu
-- ----------------------------
INSERT INTO `tblloaidanhhieu` VALUES (1, 'Cá nhân');
INSERT INTO `tblloaidanhhieu` VALUES (2, 'Đơn vị');

-- ----------------------------
-- Table structure for tblloaihoidong
-- ----------------------------
DROP TABLE IF EXISTS `tblloaihoidong`;
CREATE TABLE `tblloaihoidong`  (
  `PK_MaLoaiHD` varchar(40) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `sTenLoaiHD` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`PK_MaLoaiHD`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tblloaihoidong
-- ----------------------------
INSERT INTO `tblloaihoidong` VALUES ('1', 'Đơn vị');
INSERT INTO `tblloaihoidong` VALUES ('2', 'Hội đồng');

-- ----------------------------
-- Table structure for tblminhchung
-- ----------------------------
DROP TABLE IF EXISTS `tblminhchung`;
CREATE TABLE `tblminhchung`  (
  `PK_MaMinhChung` int NOT NULL AUTO_INCREMENT,
  `sTenFile` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `sDuongDan` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci NULL DEFAULT NULL,
  `FK_MaDeXuat` int NULL DEFAULT NULL,
  `dNgayTao` datetime NULL DEFAULT NULL,
  PRIMARY KEY (`PK_MaMinhChung`) USING BTREE,
  INDEX `FK_MaDeXuat`(`FK_MaDeXuat` ASC) USING BTREE,
  CONSTRAINT `tblminhchung_ibfk_1` FOREIGN KEY (`FK_MaDeXuat`) REFERENCES `tbldexuat` (`PK_MaDeXuat`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 56 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tblminhchung
-- ----------------------------
INSERT INTO `tblminhchung` VALUES (42, '21A100100291_NgoMinhPhuong_BTTH.pdf', 'minhchung/2024-2025/dv_cntt/13/WC21uCaE4UONkVLCxxIpKXznqJsw66ZFLoXsI0Ha.pdf', 13, '2025-04-11 14:26:38');
INSERT INTO `tblminhchung` VALUES (43, 'DanhGiaLapTrinh.docx', 'minhchung/2024-2025/dv_cntt/13/Kj9HXVVsLH0brdd7TpN7pUBOTUNzgoxMEZpJpmr8.docx', 13, '2025-04-11 14:26:38');
INSERT INTO `tblminhchung` VALUES (44, 'DHL-SWD.Template.202502.G11.docx', 'minhchung/2024-2025/dv_cntt/13/fB5OWRB4MCR2FnFqITcXHoLgvTnj7KV0mxL3Kbqx.docx', 13, '2025-04-11 14:26:38');
INSERT INTO `tblminhchung` VALUES (52, '21A100100291_NgoMinhPhuong_BTTH.docx', 'minhchung/2024-2025/canhan01/8/sGf5RFzuBL5tY1g4GTBHEic27dNRJi6TVJLpvYOr.docx', 8, '2025-04-13 14:51:37');
INSERT INTO `tblminhchung` VALUES (53, '21A100100291_NgoMinhPhuong_BTTH.pdf', 'minhchung/2024-2025/canhan01/8/9Dzsh3zOHNYZsUzYrgPuiUbee3YAT2vsN2VsXDf2.pdf', 8, '2025-04-13 14:51:37');
INSERT INTO `tblminhchung` VALUES (54, 'DanhGiaLapTrinh.docx', 'minhchung/2024-2025/canhan01/8/jMfc8KCqGOqhfM56qmwequbJH7Uqb94uZD4HoeJ7.docx', 8, '2025-04-13 14:51:37');
INSERT INTO `tblminhchung` VALUES (55, 'DHL-SWD.Template.202502.G11.docx', 'minhchung/2024-2025/canhan01/8/OMNA3fq8LYRwDekuIEYQ88YYOwJr3V910nkAHvvY.docx', 8, '2025-04-13 14:51:37');

-- ----------------------------
-- Table structure for tblquyen
-- ----------------------------
DROP TABLE IF EXISTS `tblquyen`;
CREATE TABLE `tblquyen`  (
  `PK_MaQuyen` int NOT NULL,
  `sTenQuyen` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`PK_MaQuyen`) USING BTREE
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tblquyen
-- ----------------------------
INSERT INTO `tblquyen` VALUES (1, 'admin');
INSERT INTO `tblquyen` VALUES (2, 'TCHC');
INSERT INTO `tblquyen` VALUES (3, 'HĐTĐKT');
INSERT INTO `tblquyen` VALUES (4, 'Đơn vị');
INSERT INTO `tblquyen` VALUES (5, 'Cá nhân');

-- ----------------------------
-- Table structure for tbltaikhoan
-- ----------------------------
DROP TABLE IF EXISTS `tbltaikhoan`;
CREATE TABLE `tbltaikhoan`  (
  `PK_MaTaiKhoan` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `sUsername` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `sPassword` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `api_token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `FK_MaQuyen` int NOT NULL,
  `bTrangThai` tinyint(1) NOT NULL,
  `sEmail` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`PK_MaTaiKhoan`) USING BTREE,
  UNIQUE INDEX `sUsername`(`sUsername` ASC) USING BTREE,
  UNIQUE INDEX `sEmail`(`sEmail` ASC) USING BTREE,
  INDEX `FK_MaQuyen`(`FK_MaQuyen` ASC) USING BTREE,
  CONSTRAINT `tbltaikhoan_ibfk_1` FOREIGN KEY (`FK_MaQuyen`) REFERENCES `tblquyen` (`PK_MaQuyen`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tbltaikhoan
-- ----------------------------
INSERT INTO `tbltaikhoan` VALUES ('user1', 'tchc', '$2y$12$5k4zqwNrpcRgapv7bGWVb.7S7w2nJECYbDrKrD.0dInUuJpBDMxl6', '9Z3jxgTA8dONnMBQ4RsFQJKWreogPGyX8qqbxsciuE3LWBnozS0C1D1Cf9O0', 2, 1, 'tchc@gmail.com');
INSERT INTO `tbltaikhoan` VALUES ('user2', 'dv_cntt', '$2y$12$5k4zqwNrpcRgapv7bGWVb.7S7w2nJECYbDrKrD.0dInUuJpBDMxl6', NULL, 4, 1, 'cntt@gmail.com');
INSERT INTO `tbltaikhoan` VALUES ('user3', 'dv_tcnh', '$2y$12$5k4zqwNrpcRgapv7bGWVb.7S7w2nJECYbDrKrD.0dInUuJpBDMxl6', NULL, 4, 1, 'tcnh@gmail.com');
INSERT INTO `tbltaikhoan` VALUES ('user4', 'tdkt', '$2y$12$5k4zqwNrpcRgapv7bGWVb.7S7w2nJECYbDrKrD.0dInUuJpBDMxl6', NULL, 3, 1, 'tdkt@gmail.com');
INSERT INTO `tbltaikhoan` VALUES ('user5', 'canhan01', '$2y$12$5k4zqwNrpcRgapv7bGWVb.7S7w2nJECYbDrKrD.0dInUuJpBDMxl6', NULL, 5, 1, 'canhan01@gmail.com');
INSERT INTO `tbltaikhoan` VALUES ('user6', 'dv_ketoan', '$2y$12$5k4zqwNrpcRgapv7bGWVb.7S7w2nJECYbDrKrD.0dInUuJpBDMxl6', NULL, 4, 1, 'ketoan@hou.edu.vn');
INSERT INTO `tbltaikhoan` VALUES ('user7', 'canhan02', '$2y$12$5k4zqwNrpcRgapv7bGWVb.7S7w2nJECYbDrKrD.0dInUuJpBDMxl6', NULL, 5, 1, 'canhan02@gmail.com');
INSERT INTO `tbltaikhoan` VALUES ('user8', 'canhan03', '$2y$12$5k4zqwNrpcRgapv7bGWVb.7S7w2nJECYbDrKrD.0dInUuJpBDMxl6', NULL, 5, 1, 'canhan03@gmail.com');
INSERT INTO `tbltaikhoan` VALUES ('usercanhan04', 'canhan04', '$2y$12$5k4zqwNrpcRgapv7bGWVb.7S7w2nJECYbDrKrD.0dInUuJpBDMxl6', NULL, 5, 1, 'canhan04@gmail.com');
INSERT INTO `tbltaikhoan` VALUES ('userdv_dulich', 'dv_dulich', '$2y$12$5k4zqwNrpcRgapv7bGWVb.7S7w2nJECYbDrKrD.0dInUuJpBDMxl6', NULL, 4, 1, 'dulich@hou.edu.vn');
INSERT INTO `tbltaikhoan` VALUES ('usertcnh_nv01', 'tcnh_nv01', '$2y$12$Kx5tCXyXo6sLMJIlSCkvmOHhedyLRKR/B2xLWPvQ/4ipPcs.83f0O', NULL, 5, 1, 'tcnh_nv01@gmail.com');

-- ----------------------------
-- Table structure for tblthongbao
-- ----------------------------
DROP TABLE IF EXISTS `tblthongbao`;
CREATE TABLE `tblthongbao`  (
  `PK_MaThongBao` int NOT NULL AUTO_INCREMENT,
  `sTieuDe` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `sNoiDung` text CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  `dNgayTao` datetime NULL DEFAULT NULL,
  `FK_NguoiTao` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  PRIMARY KEY (`PK_MaThongBao`) USING BTREE,
  INDEX `FK_NguoiTao`(`FK_NguoiTao` ASC) USING BTREE,
  CONSTRAINT `tblthongbao_ibfk_1` FOREIGN KEY (`FK_NguoiTao`) REFERENCES `tbltaikhoan` (`PK_MaTaiKhoan`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 17 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tblthongbao
-- ----------------------------
INSERT INTO `tblthongbao` VALUES (9, 'Thông báo về thời hạn nộp biên bản đơn vị', 'Yêu cầu các đơn vị nộp biên bản bình bầu thi đua khen thưởng của đơn vị mình lên hệ thống trước: 01:13:00 18/04/2025', '2025-04-06 01:13:03', 'user1');
INSERT INTO `tblthongbao` VALUES (10, 'Thông báo về hạn nộp minh chứng', 'Đề nghị các đơn vị cùng toàn bộ cá nhân trong trường tiến hành cung cấp minh chứng về các danh hiệu đã được đề xuất theo biên bản bình xét thi đua tại đơn vị trước: 01:13:00 17/04/2025', '2025-04-06 01:13:28', 'user1');
INSERT INTO `tblthongbao` VALUES (11, 'Thông báo về thời hạn nộp biên bản đơn vị', 'Yêu cầu các đơn vị nộp biên bản bình bầu thi đua khen thưởng của đơn vị mình lên hệ thống trước: 01:13:00 18/04/2025', '2025-04-06 01:15:24', 'user1');
INSERT INTO `tblthongbao` VALUES (12, 'Thông báo về thời hạn nộp biên bản hội đồng', 'Hội đồng thi đua cần hoàn thành biên bản họp xét thi đua và nộp lên hệ thống trước: 01:15:00 12/04/2025', '2025-04-06 01:15:24', 'user1');
INSERT INTO `tblthongbao` VALUES (13, 'Thông báo về hạn nộp minh chứng', 'Đề nghị các đơn vị cùng toàn bộ cá nhân trong trường tiến hành cung cấp minh chứng về các danh hiệu đã được đề xuất theo biên bản bình xét thi đua tại đơn vị trước: 01:22:00 30/04/2025', '2025-04-06 01:22:55', 'user1');
INSERT INTO `tblthongbao` VALUES (14, 'Thông báo về thời hạn nộp biên bản đơn vị', 'Yêu cầu các đơn vị nộp biên bản bình bầu thi đua khen thưởng của đơn vị mình lên hệ thống trước: 01:23:00 24/04/2025', '2025-04-06 01:23:11', 'user1');
INSERT INTO `tblthongbao` VALUES (15, 'Thông báo về thời hạn nộp biên bản đơn vị', 'Yêu cầu các đơn vị nộp biên bản bình bầu thi đua khen thưởng của đơn vị mình lên hệ thống trước: 22:29:00 24/04/2025', '2025-04-08 22:29:39', 'user1');
INSERT INTO `tblthongbao` VALUES (16, 'Thông báo về thời hạn nộp biên bản hội đồng', 'Hội đồng thi đua cần hoàn thành biên bản họp xét thi đua và nộp lên hệ thống trước: 00:00:00 13/04/2025', '2025-04-12 17:01:42', 'user1');

-- ----------------------------
-- Table structure for tblthongbao_dadoc
-- ----------------------------
DROP TABLE IF EXISTS `tblthongbao_dadoc`;
CREATE TABLE `tblthongbao_dadoc`  (
  `FK_MaThongBao` int NOT NULL,
  `FK_MaTaiKhoan` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  PRIMARY KEY (`FK_MaTaiKhoan`, `FK_MaThongBao`) USING BTREE,
  INDEX `FK_MaThongBao`(`FK_MaThongBao` ASC) USING BTREE,
  CONSTRAINT `tblthongbao_dadoc_ibfk_1` FOREIGN KEY (`FK_MaTaiKhoan`) REFERENCES `tbltaikhoan` (`PK_MaTaiKhoan`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `tblthongbao_dadoc_ibfk_2` FOREIGN KEY (`FK_MaThongBao`) REFERENCES `tblthongbao` (`PK_MaThongBao`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tblthongbao_dadoc
-- ----------------------------
INSERT INTO `tblthongbao_dadoc` VALUES (9, 'user1');
INSERT INTO `tblthongbao_dadoc` VALUES (9, 'user2');
INSERT INTO `tblthongbao_dadoc` VALUES (9, 'user3');
INSERT INTO `tblthongbao_dadoc` VALUES (10, 'user1');
INSERT INTO `tblthongbao_dadoc` VALUES (10, 'user2');
INSERT INTO `tblthongbao_dadoc` VALUES (10, 'user3');
INSERT INTO `tblthongbao_dadoc` VALUES (10, 'user5');
INSERT INTO `tblthongbao_dadoc` VALUES (10, 'user8');
INSERT INTO `tblthongbao_dadoc` VALUES (11, 'user1');
INSERT INTO `tblthongbao_dadoc` VALUES (11, 'user2');
INSERT INTO `tblthongbao_dadoc` VALUES (11, 'user3');
INSERT INTO `tblthongbao_dadoc` VALUES (12, 'user1');
INSERT INTO `tblthongbao_dadoc` VALUES (12, 'user4');
INSERT INTO `tblthongbao_dadoc` VALUES (13, 'user1');
INSERT INTO `tblthongbao_dadoc` VALUES (13, 'user2');
INSERT INTO `tblthongbao_dadoc` VALUES (13, 'user3');
INSERT INTO `tblthongbao_dadoc` VALUES (13, 'user5');
INSERT INTO `tblthongbao_dadoc` VALUES (13, 'user8');
INSERT INTO `tblthongbao_dadoc` VALUES (14, 'user1');
INSERT INTO `tblthongbao_dadoc` VALUES (14, 'user2');
INSERT INTO `tblthongbao_dadoc` VALUES (14, 'user3');
INSERT INTO `tblthongbao_dadoc` VALUES (15, 'user1');
INSERT INTO `tblthongbao_dadoc` VALUES (15, 'user2');
INSERT INTO `tblthongbao_dadoc` VALUES (15, 'user3');
INSERT INTO `tblthongbao_dadoc` VALUES (16, 'user1');
INSERT INTO `tblthongbao_dadoc` VALUES (16, 'user4');

-- ----------------------------
-- Table structure for tblthongbao_quyen
-- ----------------------------
DROP TABLE IF EXISTS `tblthongbao_quyen`;
CREATE TABLE `tblthongbao_quyen`  (
  `FK_MaQuyen` int NOT NULL,
  `FK_MaThongBao` int NOT NULL,
  PRIMARY KEY (`FK_MaQuyen`, `FK_MaThongBao`) USING BTREE,
  INDEX `FK_MaThongBao`(`FK_MaThongBao` ASC) USING BTREE,
  CONSTRAINT `tblthongbao_quyen_ibfk_1` FOREIGN KEY (`FK_MaThongBao`) REFERENCES `tblthongbao` (`PK_MaThongBao`) ON DELETE RESTRICT ON UPDATE RESTRICT,
  CONSTRAINT `tblthongbao_quyen_ibfk_2` FOREIGN KEY (`FK_MaQuyen`) REFERENCES `tblquyen` (`PK_MaQuyen`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tblthongbao_quyen
-- ----------------------------
INSERT INTO `tblthongbao_quyen` VALUES (4, 9);
INSERT INTO `tblthongbao_quyen` VALUES (4, 10);
INSERT INTO `tblthongbao_quyen` VALUES (5, 10);
INSERT INTO `tblthongbao_quyen` VALUES (4, 11);
INSERT INTO `tblthongbao_quyen` VALUES (3, 12);
INSERT INTO `tblthongbao_quyen` VALUES (4, 13);
INSERT INTO `tblthongbao_quyen` VALUES (5, 13);
INSERT INTO `tblthongbao_quyen` VALUES (4, 14);
INSERT INTO `tblthongbao_quyen` VALUES (4, 15);
INSERT INTO `tblthongbao_quyen` VALUES (3, 16);

-- ----------------------------
-- Table structure for tblvanbandinhkem
-- ----------------------------
DROP TABLE IF EXISTS `tblvanbandinhkem`;
CREATE TABLE `tblvanbandinhkem`  (
  `PK_MaVanBan` int NOT NULL AUTO_INCREMENT,
  `sTenVanBan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `FK_MaDot` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `sTenFile` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL DEFAULT NULL,
  `dNgayTao` datetime NULL DEFAULT NULL,
  `sDuongDan` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NULL,
  PRIMARY KEY (`PK_MaVanBan`) USING BTREE,
  INDEX `FK_MaDot`(`FK_MaDot` ASC) USING BTREE,
  CONSTRAINT `tblvanbandinhkem_ibfk_1` FOREIGN KEY (`FK_MaDot`) REFERENCES `tbldotthiduakhenthuong` (`PK_MaDot`) ON DELETE RESTRICT ON UPDATE RESTRICT
) ENGINE = InnoDB AUTO_INCREMENT = 3 CHARACTER SET = utf8mb4 COLLATE = utf8mb4_0900_ai_ci ROW_FORMAT = Dynamic;

-- ----------------------------
-- Records of tblvanbandinhkem
-- ----------------------------
INSERT INTO `tblvanbandinhkem` VALUES (1, 'Văn bản thi đua khen thưởng', '2024-2025', 'formal-17-12-05.pdf', '2025-04-08 06:08:48', 'vanbandinhkem/2024-2025/FzqyrfAgCNYWCstYEsFT2Rq9pfCr6A1OIZSvOcY7.pdf');
INSERT INTO `tblvanbandinhkem` VALUES (2, 'Văn bản hướng dẫn', '2024-2025', '21A100100291_NgoMinhPhuong_BTTH.docx', '2025-04-11 07:25:01', 'vanbandinhkem/2024-2025/3Ck9Joi3UPR824lpDtwtVSxDW3NdxkexzzDOL8CC.docx');

SET FOREIGN_KEY_CHECKS = 1;
