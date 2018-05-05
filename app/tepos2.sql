-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='TRADITIONAL,ALLOW_INVALID_DATES';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
-- -----------------------------------------------------
-- Schema tepos
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema tepos
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS 'tepos' DEFAULT CHARACTER SET utf8 ;
USE 'tepos' ;

-- -----------------------------------------------------
-- Table 'tepos'.'status'
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS 'tepos'.'status' (
  'id' INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  'name' VARCHAR(30) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_unicode_ci' NOT NULL,
  'created_at' TIMESTAMP NULL DEFAULT NULL,
  'updated_at' TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY ('id'))
ENGINE = InnoDB
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table 'tepos'.'account'
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS 'tepos'.'account' (
  'id' INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  'username' VARCHAR(50) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_unicode_ci' NOT NULL,
  'password' VARCHAR(60) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_unicode_ci' NOT NULL,
  'email' VARCHAR(255) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_unicode_ci' NOT NULL,
  'name' VARCHAR(30) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_unicode_ci' NOT NULL,
  'phone' VARCHAR(30) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_unicode_ci' NOT NULL,
  'last_login' DATETIME NULL DEFAULT NULL,
  'remember_token' VARCHAR(60) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_unicode_ci' NULL DEFAULT NULL,
  'verification_token' VARCHAR(60) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_unicode_ci' NULL DEFAULT NULL,
  'verification_token_end' DATETIME NULL DEFAULT NULL,
  'changeemail_token' VARCHAR(60) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_unicode_ci' NULL DEFAULT NULL,
  'changeemail_token_end' DATETIME NULL DEFAULT NULL,
  'forgot_token' VARCHAR(60) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_unicode_ci' NULL DEFAULT NULL,
  'forgot_token_end' DATETIME NULL DEFAULT NULL,
  'status_id' INT(10) UNSIGNED NOT NULL,
  'child_id' INT(10) UNSIGNED NOT NULL,
  'child_type' VARCHAR(255) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_unicode_ci' NOT NULL,
  'created_at' TIMESTAMP NULL DEFAULT NULL,
  'updated_at' TIMESTAMP NULL DEFAULT NULL,
  'deleted_at' TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY ('id'),
  UNIQUE INDEX 'account_child_id_child_type_unique' ('child_id' ASC, 'child_type' ASC),
  UNIQUE INDEX 'account_username_unique' ('username' ASC),
  INDEX 'account_status_id_foreign' ('status_id' ASC),
  CONSTRAINT 'account_status_id_foreign'
    FOREIGN KEY ('status_id')
    REFERENCES 'tepos'.'status' ('id'))
ENGINE = InnoDB
AUTO_INCREMENT = 5
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table 'tepos'.'admin'
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS 'tepos'.'admin' (
  'id' INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  'created_at' TIMESTAMP NULL DEFAULT NULL,
  'updated_at' TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY ('id'))
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table 'tepos'.'category'
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS 'tepos'.'category' (
  'id' INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  'name' VARCHAR(50) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_unicode_ci' NOT NULL,
  'created_at' TIMESTAMP NULL DEFAULT NULL,
  'updated_at' TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY ('id'))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table 'tepos'.'owner'
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS 'tepos'.'owner' (
  'id' INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  'subs_end' DATETIME NULL DEFAULT NULL,
  'created_at' TIMESTAMP NULL DEFAULT NULL,
  'updated_at' TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY ('id'))
ENGINE = InnoDB
AUTO_INCREMENT = 2
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table 'tepos'.'store'
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS 'tepos'.'store' (
  'id' INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  'name' VARCHAR(50) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_unicode_ci' NOT NULL,
  'owner_id' INT(10) UNSIGNED NOT NULL,
  'created_at' TIMESTAMP NULL DEFAULT NULL,
  'updated_at' TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY ('id'),
  INDEX 'store_owner_id_foreign' ('owner_id' ASC),
  CONSTRAINT 'store_owner_id_foreign'
    FOREIGN KEY ('owner_id')
    REFERENCES 'tepos'.'owner' ('id')
    ON DELETE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table 'tepos'.'cost'
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS 'tepos'.'cost' (
  'id' INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  'name' VARCHAR(50) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_unicode_ci' NOT NULL,
  'nominal' INT(11) NOT NULL,
  'date' DATETIME NOT NULL,
  'store_id' INT(10) UNSIGNED NOT NULL,
  'created_at' TIMESTAMP NULL DEFAULT NULL,
  'updated_at' TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY ('id'),
  INDEX 'cost_store_id_foreign' ('store_id' ASC),
  CONSTRAINT 'cost_store_id_foreign'
    FOREIGN KEY ('store_id')
    REFERENCES 'tepos'.'store' ('id')
    ON DELETE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table 'tepos'.'migrations'
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS 'tepos'.'migrations' (
  'id' INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  'migration' VARCHAR(255) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_unicode_ci' NOT NULL,
  'batch' INT(11) NOT NULL,
  PRIMARY KEY ('id'))
ENGINE = InnoDB
AUTO_INCREMENT = 144
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table 'tepos'.'payment_method'
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS 'tepos'.'payment_method' (
  'id' INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  'name' VARCHAR(30) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_unicode_ci' NOT NULL,
  'created_at' TIMESTAMP NULL DEFAULT NULL,
  'updated_at' TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY ('id'))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table 'tepos'.'sub_category'
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS 'tepos'.'sub_category' (
  'id' INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  'name' VARCHAR(50) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_unicode_ci' NOT NULL,
  'category_id' INT(10) UNSIGNED NOT NULL,
  'created_at' TIMESTAMP NULL DEFAULT NULL,
  'updated_at' TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY ('id'),
  INDEX 'sub_category_category_id_foreign' ('category_id' ASC),
  CONSTRAINT 'sub_category_category_id_foreign'
    FOREIGN KEY ('category_id')
    REFERENCES 'tepos'.'category' ('id')
    ON DELETE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table 'tepos'.'product'
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS 'tepos'.'product' (
  'id' INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  'name' VARCHAR(50) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_unicode_ci' NOT NULL,
  'sku' VARCHAR(30) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_unicode_ci' NULL DEFAULT NULL,
  'note' VARCHAR(50) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_unicode_ci' NULL DEFAULT NULL,
  'capital_price' INT(11) NULL DEFAULT NULL,
  'sub_category_id' INT(10) UNSIGNED NOT NULL,
  'owner_id' INT(10) UNSIGNED NOT NULL,
  'created_at' TIMESTAMP NULL DEFAULT NULL,
  'updated_at' TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY ('id'),
  INDEX 'product_sub_category_id_foreign' ('sub_category_id' ASC),
  INDEX 'product_owner_id_foreign' ('owner_id' ASC),
  CONSTRAINT 'product_owner_id_foreign'
    FOREIGN KEY ('owner_id')
    REFERENCES 'tepos'.'owner' ('id')
    ON DELETE CASCADE,
  CONSTRAINT 'product_sub_category_id_foreign'
    FOREIGN KEY ('sub_category_id')
    REFERENCES 'tepos'.'sub_category' ('id')
    ON DELETE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table 'tepos'.'staff'
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS 'tepos'.'staff' (
  'id' INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  'salary' INT(11) NULL DEFAULT NULL,
  'owner_id' INT(10) UNSIGNED NOT NULL,
  'created_at' TIMESTAMP NULL DEFAULT NULL,
  'updated_at' TIMESTAMP NULL DEFAULT NULL,
  'deleted_at' TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY ('id'),
  INDEX 'staff_owner_id_foreign' ('owner_id' ASC),
  CONSTRAINT 'staff_owner_id_foreign'
    FOREIGN KEY ('owner_id')
    REFERENCES 'tepos'.'owner' ('id'))
ENGINE = InnoDB
AUTO_INCREMENT = 3
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table 'tepos'.'staff_store'
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS 'tepos'.'staff_store' (
  'id' INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  'store_id' INT(10) UNSIGNED NOT NULL,
  'staff_id' INT(10) UNSIGNED NOT NULL,
  'created_at' TIMESTAMP NULL DEFAULT NULL,
  'updated_at' TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY ('id'),
  UNIQUE INDEX 'staff_store_store_id_staff_id_unique' ('store_id' ASC, 'staff_id' ASC),
  INDEX 'staff_store_staff_id_foreign' ('staff_id' ASC),
  CONSTRAINT 'staff_store_staff_id_foreign'
    FOREIGN KEY ('staff_id')
    REFERENCES 'tepos'.'staff' ('id')
    ON DELETE CASCADE,
  CONSTRAINT 'staff_store_store_id_foreign'
    FOREIGN KEY ('store_id')
    REFERENCES 'tepos'.'store' ('id'))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table 'tepos'.'store_product'
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS 'tepos'.'store_product' (
  'id' INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  'selling_price' INT(11) NOT NULL,
  'store_id' INT(10) UNSIGNED NOT NULL,
  'product_id' INT(10) UNSIGNED NOT NULL,
  'created_at' TIMESTAMP NULL DEFAULT NULL,
  'updated_at' TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY ('id'),
  UNIQUE INDEX 'store_product_store_id_product_id_unique' ('store_id' ASC, 'product_id' ASC),
  INDEX 'store_product_product_id_foreign' ('product_id' ASC),
  CONSTRAINT 'store_product_product_id_foreign'
    FOREIGN KEY ('product_id')
    REFERENCES 'tepos'.'product' ('id')
    ON DELETE CASCADE,
  CONSTRAINT 'store_product_store_id_foreign'
    FOREIGN KEY ('store_id')
    REFERENCES 'tepos'.'store' ('id')
    ON DELETE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table 'tepos'.'subs_plan'
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS 'tepos'.'subs_plan' (
  'id' INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  'name' VARCHAR(30) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_unicode_ci' NOT NULL,
  'duration_day' INT(11) NOT NULL,
  'price' INT(11) NOT NULL,
  'created_at' TIMESTAMP NULL DEFAULT NULL,
  'updated_at' TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY ('id'))
ENGINE = InnoDB
AUTO_INCREMENT = 4
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table 'tepos'.'subs_transaction'
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS 'tepos'.'subs_transaction' (
  'id' INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  'date' DATETIME NOT NULL,
  'qty' INT(11) NOT NULL,
  'payment_method_id' INT(10) UNSIGNED NOT NULL,
  'owner_id' INT(10) UNSIGNED NOT NULL,
  'subs_plan_id' INT(10) UNSIGNED NOT NULL,
  'created_at' TIMESTAMP NULL DEFAULT NULL,
  'updated_at' TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY ('id'),
  INDEX 'subs_transaction_payment_method_id_foreign' ('payment_method_id' ASC),
  INDEX 'subs_transaction_owner_id_foreign' ('owner_id' ASC),
  INDEX 'subs_transaction_subs_plan_id_foreign' ('subs_plan_id' ASC),
  CONSTRAINT 'subs_transaction_owner_id_foreign'
    FOREIGN KEY ('owner_id')
    REFERENCES 'tepos'.'owner' ('id')
    ON DELETE CASCADE,
  CONSTRAINT 'subs_transaction_payment_method_id_foreign'
    FOREIGN KEY ('payment_method_id')
    REFERENCES 'tepos'.'payment_method' ('id'),
  CONSTRAINT 'subs_transaction_subs_plan_id_foreign'
    FOREIGN KEY ('subs_plan_id')
    REFERENCES 'tepos'.'subs_plan' ('id'))
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table 'tepos'.'transaction'
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS 'tepos'.'transaction' (
  'id' INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  'invoice' VARCHAR(20) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_unicode_ci' NOT NULL,
  'date' DATETIME NOT NULL,
  'note' VARCHAR(50) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_unicode_ci' NOT NULL,
  'status_id' INT(10) UNSIGNED NOT NULL,
  'staff_id' INT(10) UNSIGNED NOT NULL,
  'store_id' INT(10) UNSIGNED NOT NULL,
  'created_at' TIMESTAMP NULL DEFAULT NULL,
  'updated_at' TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY ('id'),
  INDEX 'transaction_status_id_foreign' ('status_id' ASC),
  INDEX 'transaction_staff_id_foreign' ('staff_id' ASC),
  INDEX 'transaction_store_id_foreign' ('store_id' ASC),
  CONSTRAINT 'transaction_staff_id_foreign'
    FOREIGN KEY ('staff_id')
    REFERENCES 'tepos'.'staff' ('id')
    ON DELETE CASCADE,
  CONSTRAINT 'transaction_status_id_foreign'
    FOREIGN KEY ('status_id')
    REFERENCES 'tepos'.'status' ('id'),
  CONSTRAINT 'transaction_store_id_foreign'
    FOREIGN KEY ('store_id')
    REFERENCES 'tepos'.'store' ('id')
    ON DELETE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


-- -----------------------------------------------------
-- Table 'tepos'.'transaction_product'
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS 'tepos'.'transaction_product' (
  'id' INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  'quantity' INT(11) NOT NULL,
  'note' VARCHAR(50) CHARACTER SET 'utf8mb4' COLLATE 'utf8mb4_unicode_ci' NULL DEFAULT NULL,
  'product_id' INT(10) UNSIGNED NOT NULL,
  'transaction_id' INT(10) UNSIGNED NOT NULL,
  'created_at' TIMESTAMP NULL DEFAULT NULL,
  'updated_at' TIMESTAMP NULL DEFAULT NULL,
  PRIMARY KEY ('id'),
  UNIQUE INDEX 'transaction_product_product_id_transaction_id_unique' ('product_id' ASC, 'transaction_id' ASC),
  INDEX 'transaction_product_transaction_id_foreign' ('transaction_id' ASC),
  CONSTRAINT 'transaction_product_product_id_foreign'
    FOREIGN KEY ('product_id')
    REFERENCES 'tepos'.'product' ('id')
    ON DELETE CASCADE,
  CONSTRAINT 'transaction_product_transaction_id_foreign'
    FOREIGN KEY ('transaction_id')
    REFERENCES 'tepos'.'transaction' ('id')
    ON DELETE CASCADE)
ENGINE = InnoDB
DEFAULT CHARACTER SET = utf8mb4
COLLATE = utf8mb4_unicode_ci;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
