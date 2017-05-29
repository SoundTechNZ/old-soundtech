<?php
class Poli_Polipay_Model_Observer{
	
	public  function runCron($schedule){
		$resource = Mage::getSingleton('core/resource');
		$writeConnection = $resource->getConnection('core_write');
		$table = $resource->getTableName('polipay_transactions');
		 
		$query="delete from $table where adddate(transtime,interval 1 day)< now() and status=0 ";
		$writeConnection->query($query);
		 
	}
	
	
	
}