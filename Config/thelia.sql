
# This is a fix for InnoDB in MySQL >= 4.1.x
# It "suspends judgement" for fkey relationships until are tables are set.
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- acl
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `acl`;

CREATE TABLE `acl`
(
    `module_id` INTEGER NOT NULL,
    `code` VARCHAR(55) NOT NULL,
    `id` INTEGER NOT NULL AUTO_INCREMENT,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`id`),
    UNIQUE INDEX `code_UNIQUE` (`code`),
    INDEX `acl_FI_1` (`module_id`),
    CONSTRAINT `acl_FK_1`
        FOREIGN KEY (`module_id`)
        REFERENCES `module` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- customer_group_acl
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `customer_group_acl`;

CREATE TABLE `customer_group_acl`
(
    `acl_id` INTEGER NOT NULL,
    `customer_group_id` INTEGER NOT NULL,
    `type` INTEGER NOT NULL,
    `activate` TINYINT,
    `created_at` DATETIME,
    `updated_at` DATETIME,
    PRIMARY KEY (`acl_id`,`customer_group_id`,`type`),
    INDEX `idx_acl_activate` (`activate`),
    INDEX `customer_group_acl_FI_2` (`customer_group_id`),
    CONSTRAINT `customer_group_acl_FK_1`
        FOREIGN KEY (`acl_id`)
        REFERENCES `acl` (`id`)
        ON DELETE CASCADE,
    CONSTRAINT `customer_group_acl_FK_2`
        FOREIGN KEY (`customer_group_id`)
        REFERENCES `customer_group` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- acl_i18n
-- ---------------------------------------------------------------------

DROP TABLE IF EXISTS `acl_i18n`;

CREATE TABLE `acl_i18n`
(
    `id` INTEGER NOT NULL,
    `locale` VARCHAR(5) DEFAULT 'en_US' NOT NULL,
    `title` VARCHAR(255) NOT NULL,
    `description` VARCHAR(255),
    PRIMARY KEY (`id`,`locale`),
    CONSTRAINT `acl_i18n_FK_1`
        FOREIGN KEY (`id`)
        REFERENCES `acl` (`id`)
        ON DELETE CASCADE
) ENGINE=InnoDB;

# This restores the fkey checks, after having unset them earlier
SET FOREIGN_KEY_CHECKS = 1;
