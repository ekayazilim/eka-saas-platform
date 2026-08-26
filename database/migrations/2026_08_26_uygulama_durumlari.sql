ALTER TABLE `uygulamalar`
    MODIFY COLUMN `durum` enum('pending','ready','deploying','running','stopped','error','suspended') NOT NULL DEFAULT 'pending';