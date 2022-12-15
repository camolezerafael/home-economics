-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
-- -----------------------------------------------------
-- Schema finances
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema finances
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `finances` DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ;
USE `finances` ;

-- -----------------------------------------------------
-- Table `finances`.`account_types`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `finances`.`account_types` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(30) CHARACTER SET 'utf8mb3' NOT NULL,
  `description` VARCHAR(60) CHARACTER SET 'utf8mb3' NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
AUTO_INCREMENT = 6
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = 'types of accounts';


-- -----------------------------------------------------
-- Table `finances`.`users`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `finances`.`users` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(60) CHARACTER SET 'utf8mb3' NOT NULL,
  `email` VARCHAR(60) CHARACTER SET 'utf8mb3' NOT NULL,
  `password` VARCHAR(256) CHARACTER SET 'utf8mb3' NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `finances`.`accounts`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `finances`.`accounts` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `name` VARCHAR(60) CHARACTER SET 'utf8mb3' NOT NULL,
  `description` VARCHAR(60) CHARACTER SET 'utf8mb3' NOT NULL,
  `initial_balance` BIGINT NOT NULL DEFAULT '0',
  `decimal_precision` INT NOT NULL,
  `type_id` INT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `account_user_fk` (`user_id` ASC) VISIBLE,
  INDEX `account_type_fk` (`type_id` ASC) VISIBLE,
  CONSTRAINT `account_type_fk`
    FOREIGN KEY (`type_id`)
    REFERENCES `finances`.`account_types` (`id`)
    ON DELETE CASCADE,
  CONSTRAINT `account_user_fk`
    FOREIGN KEY (`user_id`)
    REFERENCES `finances`.`users` (`id`)
    ON DELETE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci
COMMENT = 'table of accounts: wallet, bank, credit cards, cryptos';


-- -----------------------------------------------------
-- Table `finances`.`from_to`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `finances`.`from_to` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(30) CHARACTER SET 'utf8mb3' NOT NULL,
  `type` VARCHAR(3) CHARACTER SET 'utf8mb3' NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `finances`.`payment_types`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `finances`.`payment_types` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(30) CHARACTER SET 'utf8mb3' NOT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `finances`.`categories`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `finances`.`categories` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(45) NOT NULL,
  `description` TEXT NULL,
  PRIMARY KEY (`id`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `finances`.`transactions`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `finances`.`transactions` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `user_id` INT NOT NULL,
  `account_id` INT NOT NULL,
  `transaction_type` VARCHAR(5) CHARACTER SET 'utf8mb3' NOT NULL,
  `description` VARCHAR(30) CHARACTER SET 'utf8mb3' NOT NULL,
  `from_id` INT NULL DEFAULT NULL,
  `to_id` INT NULL DEFAULT NULL,
  `category_id` INT NULL DEFAULT NULL,
  `payment_type_id` INT NOT NULL,
  `value` BIGINT NOT NULL DEFAULT '0',
  `status` TINYINT(1) NOT NULL DEFAULT '0',
  `date_due` TIMESTAMP NULL DEFAULT NULL,
  `date_payment` TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  INDEX `transaction_user_fk_idx` (`user_id` ASC) VISIBLE,
  INDEX `transaction_account_fk_idx` (`account_id` ASC) VISIBLE,
  INDEX `transaction_category_fk_idx` (`category_id` ASC) VISIBLE,
  INDEX `transaction_from_fk_idx` (`from_id` ASC) VISIBLE,
  INDEX `transaction_to_fk_idx` (`to_id` ASC) VISIBLE,
  INDEX `transaction_payment_type_fk_idx` (`payment_type_id` ASC) VISIBLE,
  CONSTRAINT `transaction_user_fk`
    FOREIGN KEY (`user_id`)
    REFERENCES `finances`.`users` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `transaction_account_fk`
    FOREIGN KEY (`account_id`)
    REFERENCES `finances`.`accounts` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `transaction_category_fk`
    FOREIGN KEY (`category_id`)
    REFERENCES `finances`.`categories` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `transaction_from_fk`
    FOREIGN KEY (`from_id`)
    REFERENCES `finances`.`from_to` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `transaction_to_fk`
    FOREIGN KEY (`to_id`)
    REFERENCES `finances`.`from_to` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `transaction_payment_type_fk`
    FOREIGN KEY (`payment_type_id`)
    REFERENCES `finances`.`payment_types` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8
COLLATE = utf8_unicode_ci;


-- -----------------------------------------------------
-- Table `finances`.`transfers`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `finances`.`transfers` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `from_account_id` INT NOT NULL,
  `to_account_id` INT NOT NULL,
  `value` BIGINT NOT NULL,
  PRIMARY KEY (`id`),
  INDEX `transfer_account_to_fk_idx` (`to_account_id` ASC) VISIBLE,
  INDEX `transfer_account_from_fk_idx` (`from_account_id` ASC) VISIBLE,
  CONSTRAINT `transfer_account_to_fk`
    FOREIGN KEY (`to_account_id`)
    REFERENCES `finances`.`accounts` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `transfer_account_from_fk`
    FOREIGN KEY (`from_account_id`)
    REFERENCES `finances`.`accounts` (`id`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
