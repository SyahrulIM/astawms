ALTER TABLE `instock`
	ADD COLUMN `no_manual` VARCHAR(200) NULL DEFAULT NULL AFTER `kategori`;

ALTER TABLE `outstock`
	ADD COLUMN `no_manual` VARCHAR(200) NULL DEFAULT NULL AFTER `kategori`;

CREATE TABLE `customer` (
	`idcustomer` INT NOT NULL AUTO_INCREMENT,
	`name_customer` VARCHAR(50) NULL DEFAULT NULL COLLATE 'armscii8_bin',
	`email` VARCHAR(50) NULL DEFAULT NULL COLLATE 'armscii8_bin',
	`handphone` INT NULL DEFAULT NULL,
	`foto` VARCHAR(50) NULL DEFAULT NULL COLLATE 'armscii8_bin',
	`created_by` VARCHAR(50) NULL DEFAULT NULL COLLATE 'armscii8_bin',
	`created_date` DATETIME NULL DEFAULT NULL,
	`update_by` VARCHAR(50) NULL DEFAULT NULL COLLATE 'armscii8_bin',
	`update_date` DATETIME NULL DEFAULT NULL,
	`status` INT NULL DEFAULT NULL,
	PRIMARY KEY (`idcustomer`) USING BTREE
)
COLLATE='armscii8_bin'
ENGINE=InnoDB
;

CREATE TABLE `delivery_note` (
	`iddelivery_note` INT NOT NULL AUTO_INCREMENT,
	`no_manual` VARCHAR(50) NOT NULL COLLATE 'armscii8_bin',
	`idreceived` INT NOT NULL,
	`send_date` DATETIME NOT NULL,
	`iduser` INT NOT NULL DEFAULT '0',
	`created_by` VARCHAR(50) NOT NULL COLLATE 'armscii8_bin',
	`created_date` DATETIME NOT NULL,
	`updated_by` VARCHAR(50) NOT NULL COLLATE 'armscii8_bin',
	`updated_date` DATETIME NOT NULL,
	`status` INT NOT NULL,
	PRIMARY KEY (`iddelivery_note`) USING BTREE
)
COLLATE='armscii8_bin'
ENGINE=InnoDB
;

CREATE TABLE `delivery_note` (
	`iddelivery_note` INT NOT NULL AUTO_INCREMENT,
	`no_manual` VARCHAR(200) NOT NULL COLLATE 'armscii8_bin',
	`foto` VARCHAR(200) NOT NULL COLLATE 'armscii8_bin',
	`send_date` DATETIME NOT NULL,
	`iduser` INT NOT NULL,
	`created_by` VARCHAR(200) NOT NULL COLLATE 'armscii8_bin',
	`created_date` DATETIME NOT NULL,
	`updated_by` VARCHAR(200) NOT NULL COLLATE 'armscii8_bin',
	`updated_date` DATETIME NOT NULL,
	`status` INT NOT NULL,
	PRIMARY KEY (`iddelivery_note`) USING BTREE
)
COLLATE='armscii8_bin'
ENGINE=InnoDB
;

CREATE TABLE `delivery_note_progress_log` (
    `idlog` INT NOT NULL AUTO_INCREMENT,
    `iddelivery_note` INT NOT NULL,
    `progress` INT NOT NULL, -- nilai status saat itu ( 0=Delete, 1=Prosess, 2=Delivered, 3=Received)
    `description` VARCHAR(255) NULL,
    `log_date` DATETIME NOT NULL,
    `logged_by` VARCHAR(50) NOT NULL,
    PRIMARY KEY (`idlog`),
    FOREIGN KEY (`iddelivery_note`) REFERENCES `delivery_note`(`iddelivery_note`) ON DELETE CASCADE
);

ALTER TABLE delivery_note
ADD COLUMN kategori INT NOT NULL DEFAULT 1;

CREATE TABLE `delivery_note` (
	`iddelivery_note` INT NOT NULL AUTO_INCREMENT,
	`no_manual` VARCHAR(200) NOT NULL COLLATE 'armscii8_bin',
	`foto` VARCHAR(200) NOT NULL COLLATE 'armscii8_bin',
	`send_date` DATETIME NOT NULL,
	`iduser` INT NOT NULL,
	`created_by` VARCHAR(200) NOT NULL COLLATE 'armscii8_bin',
	`created_date` DATETIME NOT NULL,
	`updated_by` VARCHAR(200) NOT NULL COLLATE 'armscii8_bin',
	`updated_date` DATETIME NOT NULL,
	`kategori` INT NOT NULL DEFAULT '1',
	`status` INT NOT NULL,
	PRIMARY KEY (`iddelivery_note`) USING BTREE
)
COLLATE='armscii8_bin'
ENGINE=InnoDB
AUTO_INCREMENT=41
;
