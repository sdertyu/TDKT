CREATE TABLE `tblTaiKhoan` (
  `PK_MaTaiKhoan` varchar(20) PRIMARY KEY,
  `sUsername` varchar(50) UNIQUE NOT NULL,
  `sPassword` varchar(255) NOT NULL,
  `FK_MaQuyen` int NOT NULL,
  `sTrangThai` bool NOT NULL,
  `sEmail` varchar(100)
);

CREATE TABLE `tblCaNhan` (
  `PK_MaCaNhan` varchar(20) PRIMARY KEY,
  `FK_MaDonVi` varchar(20),
  `sTenCaNhan` varchar(50),
  `sTenChucVu` varchar(100),
  `bGioiTinh` bool,
  `FK_MaTaiKhoan` varchar(20)
);

CREATE TABLE `tblDonVi` (
  `PK_MaDonVi` varchar(20) PRIMARY KEY,
  `sTenDonVi` varchar(50),
  `FK_MaTaiKhoan` varchar(255)
);

CREATE TABLE `tblQuyen` (
  `PK_MaQuyen` int PRIMARY KEY,
  `sTenQuyen` varchar(50)
);

CREATE TABLE `tblDotThiDuaKhenThuong` (
  `PK_MaDot` varchar(50) PRIMARY KEY,
  `dNgayTao` datetime,
  `bTrangThai` bool,
  `iNamBatDau` int,
  `iNamKetThuc` int,
  `dHanBienBanDonVi` datetime,
  `dHanNopMinhChung` datetime,
  `dHanBienBanHoiDong` datetime
);

CREATE TABLE `tblDotXuat` (
  `PK_MaDotXuat` varchar(255) PRIMARY KEY,
  `dNgayBatDau` datetime,
  `dNgayKetThuc` datetime,
  `FK_MaDot` varchar(50),
  `dHanNopMinhChung` datetime,
  `dHanBienBanHoiDong` datetime
);

CREATE TABLE `tblVanBanDinhKem` (
  `PK_MaVanBan` int PRIMARY KEY AUTO_INCREMENT,
  `sTenVanBan` varchar(255),
  `FK_MaDot` varchar(255),
  `sTenFile` varchar(255),
  `dNgayTao` datetime,
  `sDuongDan` longtext
);

CREATE TABLE `tblHoiDong` (
  `PK_MaHoiDong` varchar(60) PRIMARY KEY,
  `FK_MaTaiKhoan` varchar(20),
  `FK_MaDot` varchar(255),
  `dNgayTao` datetime,
  `FK_ChuTri` varchar(20),
  `FK_ThuKy` varchar(20),
  `dThoiGianHop` datetime,
  `sDiaChi` varchar(255),
  `iSoNguoiThamDu` int,
  `iSoThanhVien` int,
  `sDuongDan` varchar(255),
  `sTenFile` varchar(255),
  `sSoHD` varchar(50),
  `sGhiChu` varchar(255),
  `FK_MaLoaiHD` varchar(40),
  `FK_MaHinhThuc` int,
  `FK_MaDotXuat` varchar(255)
);

CREATE TABLE `tblLoaiHoiDong` (
  `PK_MaLoaiHD` varchar(40) PRIMARY KEY,
  `sTenLoaiHD` varchar(50)
);

CREATE TABLE `tblDeXuat` (
  `PK_MaDeXuat` int PRIMARY KEY AUTO_INCREMENT,
  `FK_User` varchar(255),
  `FK_MaHoiDong` varchar(60),
  `FK_MaDanhHieu` int,
  `iSoNguoiBau` int,
  `sLink` longtext,
  `dNgayTao` datetime
);

CREATE TABLE `tblKetQua` (
  `PK_MaKetQua` int PRIMARY KEY AUTO_INCREMENT,
  `FK_MaDeXuat` int,
  `FK_MaHoiDong` varchar(60),
  `iSoNguoiBau` int,
  `bDuyet` bool,
  `dNgayDuyet` datetime
);

CREATE TABLE `tblDanhHieu` (
  `PK_MaDanhHieu` int PRIMARY KEY AUTO_INCREMENT,
  `sTenDanhHieu` varchar(255),
  `FK_MaHinhThuc` int,
  `FK_MaLoaiDanhHieu` int,
  `FK_MaCap` int,
  `sTrangThai` bool
);

CREATE TABLE `tblLoaiDanhHieu` (
  `PK_MaLoaiDanhHieu` int PRIMARY KEY,
  `sTenLoaiDanhHieu` varchar(80)
);

CREATE TABLE `tblHinhThuc` (
  `PK_MaHinhThuc` int PRIMARY KEY,
  `sTenHinhThuc` varchar(40)
);

CREATE TABLE `tblCapDanhHieu` (
  `PK_MaCap` int PRIMARY KEY AUTO_INCREMENT,
  `sTenCap` nvarchar(255)
);

CREATE TABLE `tblMinhChung` (
  `PK_MaMinhChung` int PRIMARY KEY AUTO_INCREMENT,
  `sTenFile` nvarchar(255),
  `sDuongDan` nvarchar(255),
  `FK_MaDeXuat` int
);

CREATE TABLE `tblThongBao` (
  `PK_MaThongBao` INT PRIMARY KEY AUTO_INCREMENT,
  `sTieuDe` VARCHAR(255),
  `sNoiDung` TEXT,
  `dNgayTao` DATETIME,
  `FK_NguoiTao` VARCHAR(20)
);

CREATE TABLE `tblThongBao_Quyen` (
  `FK_MaQuyen` int,
  `FK_MaThongBao` int
);

ALTER TABLE `tblTaiKhoan` ADD FOREIGN KEY (`FK_MaQuyen`) REFERENCES `tblQuyen` (`PK_MaQuyen`);

ALTER TABLE `tblHoiDong` ADD FOREIGN KEY (`FK_ChuTri`) REFERENCES `tblTaiKhoan` (`PK_MaTaiKhoan`);

ALTER TABLE `tblHoiDong` ADD FOREIGN KEY (`FK_ThuKy`) REFERENCES `tblTaiKhoan` (`PK_MaTaiKhoan`);

ALTER TABLE `tblHoiDong` ADD FOREIGN KEY (`FK_MaDot`) REFERENCES `tblDotThiDuaKhenThuong` (`PK_MaDot`);

ALTER TABLE `tblDeXuat` ADD FOREIGN KEY (`FK_User`) REFERENCES `tblTaiKhoan` (`PK_MaTaiKhoan`);

ALTER TABLE `tblDeXuat` ADD FOREIGN KEY (`FK_MaDanhHieu`) REFERENCES `tblDanhHieu` (`PK_MaDanhHieu`);

ALTER TABLE `tblDeXuat` ADD FOREIGN KEY (`FK_MaHoiDong`) REFERENCES `tblHoiDong` (`PK_MaHoiDong`);

ALTER TABLE `tblDonVi` ADD FOREIGN KEY (`FK_MaTaiKhoan`) REFERENCES `tblTaiKhoan` (`PK_MaTaiKhoan`);

ALTER TABLE `tblVanBanDinhKem` ADD FOREIGN KEY (`FK_MaDot`) REFERENCES `tblDotThiDuaKhenThuong` (`PK_MaDot`);

ALTER TABLE `tblCaNhan` ADD FOREIGN KEY (`FK_MaDonVi`) REFERENCES `tblDonVi` (`PK_MaDonVi`);

ALTER TABLE `tblCaNhan` ADD FOREIGN KEY (`FK_MaTaiKhoan`) REFERENCES `tblTaiKhoan` (`PK_MaTaiKhoan`);

ALTER TABLE `tblHoiDong` ADD FOREIGN KEY (`FK_MaTaiKhoan`) REFERENCES `tblTaiKhoan` (`PK_MaTaiKhoan`);

ALTER TABLE `tblKetQua` ADD FOREIGN KEY (`FK_MaDeXuat`) REFERENCES `tblDeXuat` (`PK_MaDeXuat`);

ALTER TABLE `tblKetQua` ADD FOREIGN KEY (`FK_MaHoiDong`) REFERENCES `tblHoiDong` (`PK_MaHoiDong`);

ALTER TABLE `tblDanhHieu` ADD FOREIGN KEY (`FK_MaHinhThuc`) REFERENCES `tblHinhThuc` (`PK_MaHinhThuc`);

ALTER TABLE `tblDanhHieu` ADD FOREIGN KEY (`FK_MaLoaiDanhHieu`) REFERENCES `tblLoaiDanhHieu` (`PK_MaLoaiDanhHieu`);

ALTER TABLE `tblHoiDong` ADD FOREIGN KEY (`FK_MaLoaiHD`) REFERENCES `tblLoaiHoiDong` (`PK_MaLoaiHD`);

ALTER TABLE `tblDanhHieu` ADD FOREIGN KEY (`FK_MaCap`) REFERENCES `tblCapDanhHieu` (`PK_MaCap`);

ALTER TABLE `tblMinhChung` ADD FOREIGN KEY (`FK_MaDeXuat`) REFERENCES `tblDeXuat` (`PK_MaDeXuat`);

ALTER TABLE `tblHoiDong` ADD FOREIGN KEY (`FK_MaHinhThuc`) REFERENCES `tblHinhThuc` (`PK_MaHinhThuc`);

ALTER TABLE `tblDotXuat` ADD FOREIGN KEY (`FK_MaDot`) REFERENCES `tblDotThiDuaKhenThuong` (`PK_MaDot`);

ALTER TABLE `tblHoiDong` ADD FOREIGN KEY (`FK_MaDotXuat`) REFERENCES `tblDotXuat` (`PK_MaDotXuat`);

ALTER TABLE `tblThongBao_Quyen` ADD FOREIGN KEY (`FK_MaThongBao`) REFERENCES `tblThongBao` (`PK_MaThongBao`);

ALTER TABLE `tblThongBao_Quyen` ADD FOREIGN KEY (`FK_MaQuyen`) REFERENCES `tblQuyen` (`PK_MaQuyen`);

ALTER TABLE `tblThongBao` ADD FOREIGN KEY (`FK_NguoiTao`) REFERENCES `tblTaiKhoan` (`PK_MaTaiKhoan`);
