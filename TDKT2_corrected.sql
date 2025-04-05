
CREATE TABLE `tblTaiKhoan` (
  `PK_MaTaiKhoan` varchar(255) PRIMARY KEY,
  `sUsername` varchar(50) UNIQUE NOT NULL,
  `sPassword` varchar(255) NOT NULL,
  `FK_MaQuyen` int NOT NULL,
  `bTrangThai` bool NOT NULL
);

CREATE TABLE `tblQuyen` (
  `PK_MaQuyen` int PRIMARY KEY,
  `sTenQuyen` nvarchar(255)
);

CREATE TABLE `tblDonVi` (
  `PK_MaDonVi` varchar(20) PRIMARY KEY,
  `sTenDonVi` nvarchar(255),
  `FK_MaTaiKhoan` varchar(255)
);

CREATE TABLE `tblCaNhan` (
  `PK_MaCaNhan` varchar(20) PRIMARY KEY,
  `FK_MaDonVi` varchar(20),
  `sTenCaNhan` nvarchar(255),
  `sEmail` varchar(100),
  `sTenChucVu` nvarchar(100),
  `bGioiTinh` bool,
  `FK_MaTaiKhoan` varchar(255)
);

CREATE TABLE `tblDotThiDuaKhenThuong` (
  `PK_MaDot` varchar(255) PRIMARY KEY,
  `dNgayTao` datetime,
  `bTrangThai` bool,
  `iNamBatDau` int,
  `iNamKetThuc` int
);

CREATE TABLE `tblVanBanDinhKem` (
  `PK_MaVanBan` int PRIMARY KEY AUTO_INCREMENT,
  `sTenVanBan` nvarchar(255),
  `FK_MaDot` varchar(255),
  `sTenFile` nvarchar(255),
  `dNgayTao` datetime,
  `sDuongDan` longtext
);

CREATE TABLE `tblKhenThuongDotXuat` (
  `PK_MaKTDX` int PRIMARY KEY AUTO_INCREMENT,
  `FK_MaDot` varchar(255),
  `FK_MaTaiKhoan` varchar(255),
  `dNgayTao` datetime
);

CREATE TABLE `tblCuocHop` (
  `PK_MaCuocHop` varchar(60) PRIMARY KEY,
  `FK_MaTaiKhoan` varchar(255),
  `FK_MaDot` varchar(255),
  `dNgayTao` datetime,
  `FK_ChuTri` varchar(255),
  `FK_ThuKy` varchar(255),
  `dThoiGianHop` datetime,
  `sDiaChi` nvarchar(255),
  `iSoNguoiThamDu` int
);

CREATE TABLE `tblDeXuat` (
  `PK_MaDeXuat` int PRIMARY KEY AUTO_INCREMENT,
  `FK_User` varchar(255),
  `FK_MaCuocHop` varchar(60),
  `FK_MaDanhHieu` int,
  `FK_MaKTDX` int,
  `iSoNguoiBau` int,
  `sLink` longtext,
  `dNgayTao` datetime
);

CREATE TABLE `tblKetQua` (
  `PK_MaKetQua` int PRIMARY KEY AUTO_INCREMENT,
  `FK_MaDeXuat` int,
  `FK_MaCuocHop` varchar(60),
  `iSoNguoiBau` int,
  `bDuyet` bool,
  `dNgayDuyet` datetime
);

CREATE TABLE `tblDanhHieu` (
  `PK_MaDanhHieu` int PRIMARY KEY AUTO_INCREMENT,
  `FK_MaLoaiDanhHieu` int,
  `sTenDanhHieu` nvarchar(255)
);

CREATE TABLE `tblLoaiDanhHieu` (
  `PK_MaLoaiDanhHieu` int PRIMARY KEY,
  `sTenLoaiDanhHieu` nvarchar(80)
);

ALTER TABLE `tblTaiKhoan` ADD FOREIGN KEY (`FK_MaQuyen`) REFERENCES `tblQuyen` (`PK_MaQuyen`);

ALTER TABLE `tblCuocHop` ADD FOREIGN KEY (`FK_ChuTri`) REFERENCES `tblTaiKhoan` (`PK_MaTaiKhoan`);
ALTER TABLE `tblCuocHop` ADD FOREIGN KEY (`FK_ThuKy`) REFERENCES `tblTaiKhoan` (`PK_MaTaiKhoan`);
ALTER TABLE `tblCuocHop` ADD FOREIGN KEY (`FK_MaDot`) REFERENCES `tblDotThiDuaKhenThuong` (`PK_MaDot`);

ALTER TABLE `tblDeXuat` ADD FOREIGN KEY (`FK_User`) REFERENCES `tblTaiKhoan` (`PK_MaTaiKhoan`);
ALTER TABLE `tblDeXuat` ADD FOREIGN KEY (`FK_MaDanhHieu`) REFERENCES `tblDanhHieu` (`PK_MaDanhHieu`);
ALTER TABLE `tblDeXuat` ADD FOREIGN KEY (`FK_MaCuocHop`) REFERENCES `tblCuocHop` (`PK_MaCuocHop`);
ALTER TABLE `tblDeXuat` ADD FOREIGN KEY (`FK_MaKTDX`) REFERENCES `tblKhenThuongDotXuat` (`PK_MaKTDX`);

ALTER TABLE `tblDanhHieu` ADD FOREIGN KEY (`FK_MaLoaiDanhHieu`) REFERENCES `tblLoaiDanhHieu` (`PK_MaLoaiDanhHieu`);
ALTER TABLE `tblDonVi` ADD FOREIGN KEY (`FK_MaTaiKhoan`) REFERENCES `tblTaiKhoan` (`PK_MaTaiKhoan`);
ALTER TABLE `tblVanBanDinhKem` ADD FOREIGN KEY (`FK_MaDot`) REFERENCES `tblDotThiDuaKhenThuong` (`PK_MaDot`);

ALTER TABLE `tblCaNhan` ADD FOREIGN KEY (`FK_MaDonVi`) REFERENCES `tblDonVi` (`PK_MaDonVi`);
ALTER TABLE `tblCaNhan` ADD FOREIGN KEY (`FK_MaTaiKhoan`) REFERENCES `tblTaiKhoan` (`PK_MaTaiKhoan`);

ALTER TABLE `tblKetQua` ADD FOREIGN KEY (`FK_MaDeXuat`) REFERENCES `tblDeXuat` (`PK_MaDeXuat`);
ALTER TABLE `tblKetQua` ADD FOREIGN KEY (`FK_MaCuocHop`) REFERENCES `tblCuocHop` (`PK_MaCuocHop`);

ALTER TABLE `tblKhenThuongDotXuat` ADD FOREIGN KEY (`FK_MaDot`) REFERENCES `tblDotThiDuaKhenThuong` (`PK_MaDot`);
ALTER TABLE `tblKhenThuongDotXuat` ADD FOREIGN KEY (`FK_MaTaiKhoan`) REFERENCES `tblTaiKhoan` (`PK_MaTaiKhoan`);
