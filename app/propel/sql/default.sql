
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- dog
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `dog`;

CREATE TABLE `dog`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `name` VARCHAR(100),
    `sex` CHAR(1),
    `dob` DATE,
    `breed_id` INTEGER,
    PRIMARY KEY (`id`),
    INDEX `dog_FI_1` (`breed_id`)
) ENGINE=MyISAM;

-- ---------------------------------------------------------------------
-- breed
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `breed`;

CREATE TABLE `breed`
(
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `breed` VARCHAR(100),
    `description` TEXT,
    PRIMARY KEY (`id`)
) ENGINE=MyISAM;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
