<?php 

    include_once 'ConnectDB.php';
    class QuerySP extends Db{
        private $QuerySanPhamChiTiet;
        private $SPCT_Id;
        private $QueryTableHinhAnhSPCT;
        private $HMTDM_Id;
        private $DisplayProduct;
        private $ImageSource;
        public function getImageSource() {
            return $this->ImageSource;
        }
        public function getDisplayProduct() {
            return $this->DisplayProduct;
        }
        public function getQuerySanPhamChiTiet() {
            return $this->QuerySanPhamChiTiet;
        }
        public function getQueryTableHinhAnhSPCT() {
            return $this->QueryTableHinhAnhSPCT;
        }
        public function queryTableSPCT() {
            $QueryTableSPCT = $this->_pdo->prepare("SELECT * FROM sanphamchitietcuahang");
            $QueryTableSPCT->execute();
            $this->QuerySanPhamChiTiet = $QueryTableSPCT->fetchAll();
            $QueryTableSPCT->closeCursor();
        }
        public function queryTableImageSPCT() {
            $QueryTableImageSPCT = $this->_pdo->prepare("SELECT * FROM hinhanhsanphamchitiet");
            $QueryTableImageSPCT->execute();
            $this->QueryTableHinhAnhSPCT = $QueryTableImageSPCT->fetchAll();
            $QueryTableImageSPCT->closeCursor();
        }
        //Query hinh anh cua san pham theo SPCT_Id
        public function queryTableImageSPCTTID($SPCT_Id) {
            $TimKiemHinhAnhTheoSPCT_ID = $this->_pdo->prepare("SELECT * FROM hinhanhsanphamchitiet WHERE SPCT_Id = ?");
            $TimKiemHinhAnhTheoSPCT_ID->bindParam(1, $SPCT_Id);
            $TimKiemHinhAnhTheoSPCT_ID->execute();
            $this->ImageSource = $TimKiemHinhAnhTheoSPCT_ID->fetch();
            $TimKiemHinhAnhTheoSPCT_ID->closeCursor();
        }
        //Tim kiem SPCT_Id bang ten
        public function findIdForSPCT($Name) {
            $FindIdForSPCT = $this->_pdo->prepare("SELECT SPCT_Id FROM sanphamchitietcuahang WHERE TenSPCT = ?");
            $FindIdForSPCT->bindParam(1, $Name);
            $FindIdForSPCT->execute();
            $this->SPCT_Id = $FindIdForSPCT->fetch();
            $FindIdForSPCT->closeCursor();
        }
        //Update trang thai cua san pham
        public function updateStatus($SPCT_Id, $Value) {
            $UpadteSPCTTableFromId = $this->_pdo->prepare("UPDATE sanphamchitietcuahang SET Status = ? WHERE SPCT_Id = ?");
            $UpadteSPCTTableFromId->bindParam(1, $Value);
            $UpadteSPCTTableFromId->bindParam(2, $SPCT_Id);
            $UpadteSPCTTableFromId->execute();
            $UpadteSPCTTableFromId->closeCursor();
            return 1;
        }
        //Tim kiem HMTDM_Id qua $TenHMTDM
        public function findIDForHMTDM($TenHMTDM) {
            $TimKiemTheoTen = $this->_pdo->prepare("SELECT HMTDM_Id FROM hangmaytinhcuadanhmuc WHERE TenHMTDM = ?");
            $TimKiemTheoTen->bindParam(1, $TenHMTDM);
            $TimKiemTheoTen->execute();
            $this->HMTDM_Id = $TimKiemTheoTen->fetch();
            $TimKiemTheoTen->closeCursor();
            return 1;
        }
        //Hien thi san pham theo HMTDM_Id
        public function DisplayProductForID() {
            $HienThiSanPhamTheoId = $this->_pdo->prepare("SELECT * FROM sanphamchitietcuahang WHERE HMTDM_Id = ?");
            $HienThiSanPhamTheoId->bindParam(1, $this->HMTDM_Id['HMTDM_Id']);
            $HienThiSanPhamTheoId->execute();
            $this->DisplayProduct = $HienThiSanPhamTheoId->fetchAll();
            $HienThiSanPhamTheoId->closeCursor();
        }
        //Uplaod hinh anh cho san pham
        public function uploadImg($ImageSubmit) {
            $fileName = $ImageSubmit['name'];
            $fileSize = $ImageSubmit['size'];
            $fileType = $ImageSubmit['type'];
            $fileError = $ImageSubmit['error'];
            $fileTmpName = $ImageSubmit['tmp_name'];
            $fileExt = explode('.', $fileName);
            $fileActualExt = strtolower(end($fileExt));
            $allowed = array('jpg' , 'jpeg' , 'png');
            if(in_array($fileActualExt, $allowed)) {
                if($fileError == 0) {
                    if($fileSize<1000000) {
                        $fileNameNew = "SPCT". $this->SPCT_Id['SPCT_Id'] .$fileExt[0]."." .$fileActualExt;
                        $fileDestination = 'Public/ImageSPCT/'.$fileNameNew;
                        move_uploaded_file($fileTmpName, $fileDestination);
                        // Set status
                        $ImgName1 = $fileExt[0];
                        $st = $this->_pdo->prepare('INSERT INTO hinhanhsanphamchitiet(SPCT_Id, Name, Type, Full) VALUES (?, ?, ?, ?)');
                        $st->bindParam(1, $this->SPCT_Id['SPCT_Id']);
                        $st->bindParam(2, $ImgName1);
                        $st->bindParam(3, $fileActualExt);
                        $st->bindParam(4, $fileNameNew);
                        $st->execute();
                        $st->closeCursor();
                        return 1;
                    }
                    else {
                        return 0;
                    }
                }
            }
        }
    }


?>