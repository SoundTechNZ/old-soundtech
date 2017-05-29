<?php

class PoliError extends SimpleXMLElement {

    var $Code;
    //string
    var $Field;
    //string
    var $Message;

    //string
}

class PoliInitiateTransactionInput {

    var $CurrencyAmount;
    //decimal
    var $CurrencyCode;
    //string
    var $MerchantCheckoutURL;
    //string
    var $MerchantCode;
    //string
    var $MerchantData;
    //string
    var $MerchantDateTime;
    //dateTime
    var $MerchantHomePageURL;
    //string
    var $MerchantRef;
    //string
    var $NotificationURL;
    //string
    var $SelectedFICode;
    //string
    var $SuccessfulURL;
    //string
    var $Timeout;
    //int
    var $UnsuccessfulURL;
    //string
    var $UserIPAddress;

    //string
}

class PoliInitiateTransactionOutput {

    var $NavigateURL;
    //string
    var $TransactionRefNo;
    //string
    var $TransactionToken;

    //string
}

/* FinancialInstutions */

class PoliFinancialInstitution extends SimpleXMLElement {

    var $FinancialInstitutionCode;
    //string
    var $FinancialInstitutionName;

    //string
}

/* GetTransaction */

class PoliTransaction {

    var $AmountPaid;
    //decimal
    var $BankReceipt;
    //string
    var $BankReceiptDateTime;
    //string
    var $CountryCode;
    //string
    var $CountryName;
    //string
    var $CurrencyCode;
    //string
    var $CurrencyName;
    //string
    var $EndDateTime;
    //dateTime
    var $ErrorCode;
    //string
    var $ErrorMessage;
    //string
    var $EstablishedDateTime;
    //dateTime
    var $FinancialInstitutionCode;
    //string
    var $FinancialInstitutionCountryCode;
    //string
    var $FinancialInstitutionName;
    //string
    var $MerchantAcctName;
    //string
    var $MerchantAcctNumber;
    //string
    var $MerchantAcctSortCode;
    //string
    var $MerchantAcctSuffix;
    //string
    var $MerchantDefinedData;
    //string
    var $MerchantEstablishedDateTime;
    //dateTime
    var $MerchantReference;
    //string
    var $PaymentAmount;
    //decimal
    var $StartDateTime;
    //dateTime
    var $TransactionID;
    //guid
    var $TransactionRefNo;

    //string
}

/**
 * class PoliRestClient
 *
 *
 */
final class PoliRestClient {

    var $MerchantCode;
    var $AuthenticationCode;
    var $test;

    /**
     *
     * @param string $merchantcode
     * @param string $authcode
     * @param boolean $test
     */
    public function PoliRestClient($merchantcode, $authcode, $test = true) {
        $this->MerchantCode = $merchantcode;
        $this->AuthenticationCode = htmlentities($authcode);
        $this->test = $test;
        libxml_use_internal_errors(true);
    }

    private function post($url, $data) {
        $ch = curl_init();

        $data = preg_replace("/[\r\n]/", '', $data);
		
		curl_setopt($ch, CURLOPT_CAINFO, Mage::getBaseDir('lib')."/polipayment/cacert.pem");
        curl_setopt($ch, CURLOPT_TIMEOUT, 100);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        //curl_setopt($ch, CURLOPT_HTTPHEADER, array ('Accept: text/xml'));
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_VERBOSE, "1");
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-type: text/xml"));

        $response = curl_exec($ch);
		
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($response === false) {
			echo 'Curl error: ' . curl_error($ch) . '</br>Content:</br>';
			print_r($response);
            throw new Exception("A fatal unexpected error when communicating with POLi: ".curl_error($ch)."</br>");
        }

		curl_close($ch);
		
        if ($code != 200) {
            throw new Exception("An error occurred while communicating with POLi: ".curl_error($ch)." :: ".$code." :: ".$response);
        }

        return $response;
    }

    /**
     *
     * @param string $token
     * @param $errorout
     * @param string $code
     * @return PoliTransaction
     */
    function getTransaction($token, &$errorout, &$code) {

        if ($this->test) {
            $url = 'https://merchantapi.apac.paywithpoli.com/MerchantAPIService.svc/Xml/transaction/query';
        } else
            $url = 'https://merchantapi.apac.paywithpoli.com/MerchantAPIService.svc/Xml/transaction/query';

        $str = <<<HTML
<GetTransactionRequest xmlns="http://schemas.datacontract.org/2004/07/Centricom.POLi.Services.MerchantAPI.Contracts" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
<AuthenticationCode>{$this->AuthenticationCode}</AuthenticationCode>
<MerchantCode>{$this->MerchantCode}</MerchantCode>
<TransactionToken>$token</TransactionToken>
</GetTransactionRequest>
HTML;
        $str = $this->post($url, $str);
        $xml = simplexml_load_string($str, null, LIBXML_NOERROR);
        ; //,'InitiateTransactionResult');
        //print_r($xml->Transaction);
        libxml_use_internal_errors(true);
        $xml->registerXPathNamespace('m', 'http://schemas.datacontract.org/2004/07/Centricom.POLi.Services.MerchantAPI.Contracts');

        $xml->registerXPathNamespace('a', 'http://schemas.datacontract.org/2004/07/Centricom.POLi.Services.MerchantAPI.DCO');

        $errors = $xml->xpath("//m:Errors/a:Error");
        if (is_array($errors)) {
            $errorout = array();
            $l = count($errors);
            if ($l) {
                for ($i = 0; $i < $l; $i++) {
                    $xml = simplexml_load_string($errors[$i]->asXML(), 'PoliError', LIBXML_NOERROR, null);
                    /* @var $xml Error */
                    $errorout[] = $xml;
                }
                return null;
            }
        } else {
            return null;
        }
        $code = (string) $xml->TransactionStatusCode;

		$txml = new PoliTransaction;
		$txml->AmountPaid = (string)$xml->xpath('//a:AmountPaid')[0];
		$txml->BankReceipt = (string)$xml->xpath('//a:BankReceipt')[0];
		$txml->BankReceiptDateTime = (string)$xml->xpath('//a:BankReceiptDateTime')[0];
		$txml->CountryCode = (string)$xml->xpath('//a:CountryCode')[0];
		$txml->CountryName = (string)$xml->xpath('//a:CountryName')[0];
		$txml->CurrencyCode = (string)$xml->xpath('//a:CurrencyCode')[0];
		$txml->CurrencyName = (string)$xml->xpath('//a:CurrencyName')[0];
		$txml->EndDateTime = (string)$xml->xpath('//a:EndDateTime')[0];
		$txml->ErrorCode = (string)$xml->xpath('//a:ErrorCode')[0];
		$txml->ErrorMessage = (string)$xml->xpath('//a:ErrorMessage')[0];
		$txml->EstablishedDateTime = (string)$xml->xpath('//a:EstablishedDateTime')[0];
		$txml->FinancialInstitutionCode = (string)$xml->xpath('//a:FinancialInstitutionCode')[0];
		$txml->FinancialInstitutionCountryCode = (string)$xml->xpath('//a:FinancialInstitutionCountryCode')[0];
		$txml->FinancialInstitutionName = (string)$xml->xpath('//a:FinancialInstitutionName')[0];
		$txml->MerchantAcctName = (string)$xml->xpath('//a:MerchantAcctName')[0];
		$txml->MerchantAcctNumber = (string)$xml->xpath('//a:MerchantAcctNumber')[0];
		$txml->MerchantAcctSortCode = (string)$xml->xpath('//a:MerchantAcctSortCode')[0];
		$txml->MerchantAcctSuffix = (string)$xml->xpath('//a:MerchantAcctSuffix')[0];
		$txml->MerchantDefinedData = (string)$xml->xpath('//a:MerchantDefinedData')[0];
		$txml->MerchantEstablishedDateTime = (string)$xml->xpath('//a:MerchantEstablishedDateTime')[0];
		$txml->MerchantReference = (string)$xml->xpath('//a:MerchantReference')[0];
		$txml->PaymentAmount = (string)$xml->xpath('//a:PaymentAmount')[0];
		$txml->StartDateTime = (string)$xml->xpath('//a:StartDateTime')[0];
		$txml->TransactionID = (string)$xml->xpath('//a:TransactionID')[0];
		$txml->TransactionRefNo = (string)$xml->xpath('//a:TransactionRefNo')[0];
		
        return $txml;
    }

    /**
     * @param $errorout
     * @return array
     * array of financial institution object.
     */
    function getfinancialinstitutions(&$errorout) {
        if ($this->test) {
            $url = 'https://merchantapi.apac.paywithpoli.com/MerchantAPIService.svc/Xml/banks';
        } else
            $url = 'https://merchantapi.apac.paywithpoli.com/MerchantAPIService.svc/Xml/banks';

        $str = <<<HTML
<?xml version="1.0" encoding="utf-8"?>
<GetFinancialInstitutionsRequest xmlns="http://schemas.datacontract.org/2004/07/Centricom.POLi.Services.MerchantAPI.Contracts" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
<AuthenticationCode>{$this->AuthenticationCode}</AuthenticationCode>
<MerchantCode>{$this->MerchantCode}</MerchantCode>
</GetFinancialInstitutionsRequest>
HTML;
        $str = $this->post($url, $str);
        $xml = simplexml_load_string($str, null, LIBXML_NSCLEAN);
        ; //,'InitiateTransactionResult');
        //print_r($xml->Transaction);
        $xml->registerXPathNamespace('m', 'http://schemas.datacontract.org/2004/07/Centricom.POLi.Services.MerchantAPI.Contracts');

        $xml->registerXPathNamespace('a', 'http://schemas.datacontract.org/2004/07/Centricom.POLi.Services.MerchantAPI.DCO');

        $errors = $xml->xpath("//m:Errors/a:Error");
        if (is_array($errors)) {
            $l = count($errors);
            if ($l) {
                for ($i = 0; $i < $l; $i++) {
                    $xml = simplexml_load_string($errors[$i]->asXML(), 'Error', LIBXML_NOERROR | LIBXML_NSCLEAN, null);
                    /* @var $xml ErrorXML */
                    $errorout = $xml;
                    return null;
                }
            }
        } else {
            return null;
        }

        //		$el=array_shift($xml->xpath("//m:FinancialInstitutionList/FinancialInstution"));
        $fins = array();

        foreach ($xml->xpath("//m:FinancialInstitutionList/a:FinancialInstitution") as $el) {
            $xml1 = simplexml_load_string($el->asXML(), 'PoliFinancialInstitution');
            /* @var $fin FinancialInstitution */
            $fins[] = $xml1;
        }
        return $fins;
    }

    /**
     *
     * @param InitiateTransactionInput  $input
     *
     * @param $error
     * @param string $code
     * transaction status code
     *
     * @return PoliInitiateTransactionOutput
     * only $input to be passed as non null, rest passed as  null and used for output.
     */
    function initiatetransaction($input, &$errorout, &$code) {


        if ($this->test) {
            $url = "https://merchantapi.apac.paywithpoli.com/MerchantAPIService.svc/Xml/transaction/initiate";
        } else {
            $url = "https://merchantapi.apac.paywithpoli.com/MerchantAPIService.svc/Xml/transaction/initiate";
        }

        $str = <<<HTML
<InitiateTransactionRequest xmlns="http://schemas.datacontract.org/2004/07/Centricom.POLi.Services.MerchantAPI.Contracts" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">
<AuthenticationCode>{$this->AuthenticationCode}</AuthenticationCode>
<Transaction xmlns:dco="http://schemas.datacontract.org/2004/07/Centricom.POLi.Services.MerchantAPI.DCO">
<dco:CurrencyAmount>$input->CurrencyAmount</dco:CurrencyAmount>
<dco:CurrencyCode>$input->CurrencyCode</dco:CurrencyCode>
<dco:MerchantCheckoutURL>$input->MerchantCheckoutURL</dco:MerchantCheckoutURL>
<dco:MerchantCode>{$this->MerchantCode}</dco:MerchantCode>
<dco:MerchantData>$input->MerchantData</dco:MerchantData>
<dco:MerchantDateTime>$input->MerchantDateTime</dco:MerchantDateTime>
<dco:MerchantHomePageURL>$input->MerchantHomePageURL</dco:MerchantHomePageURL>
<dco:MerchantRef>$input->MerchantRef</dco:MerchantRef>
<dco:NotificationURL>$input->NotificationURL</dco:NotificationURL>
HTML;

        if ($input->SelectedFICode) {
            $str .= '<dco:SelectedFICode>' . $input->SelectedFICode . '</dco:SelectedFICode>';
        } else {
            $str .= '<dco:SelectedFICode i:nil="true"/>';
        }
        $str .= <<<HTML
<dco:SuccessfulURL>$input->SuccessfulURL</dco:SuccessfulURL>
<dco:Timeout>$input->Timeout</dco:Timeout>
<dco:UnsuccessfulURL>$input->UnsuccessfulURL</dco:UnsuccessfulURL>
<dco:UserIPAddress>$input->UserIPAddress</dco:UserIPAddress>
</Transaction>
</InitiateTransactionRequest>
HTML;

        $str = $this->post($url, $str);
		
		Mage::log($str, null, 'payment_polipay.log', true);
		
        $xml = simplexml_load_string($str, null);
        ; //,'InitiateTransactionResult');
        //print_r($xml->Transaction);
        $xml->registerXPathNamespace('m', 'http://schemas.datacontract.org/2004/07/Centricom.POLi.Services.MerchantAPI.Contracts');

        $xml->registerXPathNamespace('a', 'http://schemas.datacontract.org/2004/07/Centricom.POLi.Services.MerchantAPI.DCO');

        $errors = $xml->xpath("//m:Errors/a:Error");
		$polidebug = $xml->xpath('//a:NavigateURL');
		
        if (is_array($errors)) {
            $l = count($errors);
            if ($l > 0) {
                $errorout = array();
                for ($i = 0; $i < $l; $i++) {
                    $xml = simplexml_load_string($errors[$i]->asXML(), 'PoliError', LIBXML_NOERROR | LIBXML_NSCLEAN);
                    /* @var $xml Error */
                    $errorout[] = $xml;
                }
                throw new Exception("POLi Error : ".$l." : ".print_r($xml));
				return null;
            }
        } else {
            return null;
        }
		
		if(is_null($polidebug) == true) {
			throw new Exception("POLi Debug information: NavigateURL is null");
		}

        $code = (string) $xml->TransactionStatusCode;
		
		$txml = new PoliInitiateTransactionOutput;
		$txml->NavigateURL = (string)$xml->xpath('//a:NavigateURL')[0];
		$txml->TransactionRefNo = (string)$xml->xpath('//a:TransactionRefNo')[0];
		$txml->TransactionToken = (string)$xml->xpath('//a:TransactionToken')[0];
		
        return $txml;
    }

}
