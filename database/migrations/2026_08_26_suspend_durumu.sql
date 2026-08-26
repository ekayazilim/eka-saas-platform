ALTER TABLE `uygulamalar`
    ADD COLUMN `onceki_durum` varchar(30) DEFAULT NULL AFTER `durum`;