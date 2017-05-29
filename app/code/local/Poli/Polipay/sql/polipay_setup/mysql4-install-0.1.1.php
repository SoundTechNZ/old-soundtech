<?php

$installer = $this;

$installer->startSetup();

$installer->run("




 DROP TABLE if exists {$this->getTable('polipay_transactions')};
CREATE TABLE {$this->getTable('polipay_transactions')} (
 `orderno` varchar(50) NOT NULL,
  `refno` char(12) NOT NULL,
  `amount` float NOT NULL,
  `currency` char(3) NOT NULL,
  `status` tinyint(4) NOT NULL DEFAULT '0',
  `token` varchar(50) NOT NULL,
   `transtime` timestamp NOT NULL, 
  PRIMARY KEY (`orderno`) 
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

 DROP TABLE if exists {$this->getTable('polipay_receipts')};
CREATE TABLE {$this->getTable('polipay_receipts')} (
  `AmountPaid` float DEFAULT NULL,
  `BankReceipt` varchar(50) DEFAULT NULL,
  `BankReceiptDateTime` datetime DEFAULT NULL,
  `CountryCode` char(3) DEFAULT NULL,
  `CountryName` varchar(30) DEFAULT NULL,
  `CurrencyCode` char(3) DEFAULT NULL,
  `CurrencyName` varchar(30) DEFAULT NULL,
  `EndDateTime` datetime DEFAULT NULL,
  `ErrorCode` varchar(10) DEFAULT NULL,
  `ErrorMessage` text,
  `EstablishedDateTime` datetime DEFAULT NULL,
  `FinancialInstitutionCode` varchar(10) DEFAULT NULL,
  `FinancialInstitutionCountryCode` char(3) DEFAULT NULL,
  `FinancialInstitutionName` varchar(30) DEFAULT NULL,
  `MerchantAcctName` varchar(30) DEFAULT NULL,
  `MerchantAcctNumber` varchar(20) DEFAULT NULL,
  `MerchantAcctSortCode` varchar(10) DEFAULT NULL,
  `MerchantAcctSuffix` varchar(30) DEFAULT NULL,
  `MerchantDefinedData` text,
  `MerchantEstablishedDateTime` datetime DEFAULT NULL,
  `MerchantReference` varchar(30) DEFAULT NULL,
  `PaymentAmount` float NOT NULL,
  `StartDateTime` datetime DEFAULT NULL,
  `TransactionID` varchar(40) NOT NULL,
  `TransactionRefNo` char(12) NOT NULL,
  PRIMARY KEY (`TransactionID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


    ");

$installer->endSetup(); 
