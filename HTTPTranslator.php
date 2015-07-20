<?php
/*
 * Class:HTTPTranslator
 *
 * Processing the translator request.
 */
Class HTTPTranslator
{
    /*
     * Create and execute the HTTP CURL request.
     *
     * @param string $url        HTTP Url.
     * @param string $authHeader Authorization Header string.
     * @param string $postData   Data to post.
     *
     * @return string.
     *
     */
    function curlRequest($url, $authHeader, $postData='')
    {
        //Initialize the Curl Session.
        $ch = curl_init();
        //Set the Curl url.
        curl_setopt ($ch, CURLOPT_URL, $url);
        //Set the HTTP HEADER Fields.
        curl_setopt ($ch, CURLOPT_HTTPHEADER, array($authHeader,"Content-Type: text/xml"));
        //CURLOPT_RETURNTRANSFER- TRUE to return the transfer as a string of the return value of curl_exec().
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, TRUE);
        //CURLOPT_SSL_VERIFYPEER- Set FALSE to stop cURL from verifying the peer's certificate.
        curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, False);
        if($postData) {
            //Set HTTP POST Request.
            curl_setopt($ch, CURLOPT_POST, TRUE);
            //Set data to POST in HTTP "POST" Operation.
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        }
        //Execute the  cURL session.
        $curlResponse = curl_exec($ch);
        //Get the Error Code returned by Curl.
        $curlErrno = curl_errno($ch);
        if ($curlErrno) {
            $curlError = curl_error($ch);
            throw new Exception($curlError);
        }
        //Close a cURL session.
        curl_close($ch);
        return $curlResponse;
    }
    /*
     * Create Request XML Format.
     *
     * @param string $languageCode  Language code
     *
     * @return string.
     */
    function createReqXML($languageCode)
    {
        //Create the Request XML.
        $requestXml = '<ArrayOfstring xmlns="http://schemas.microsoft.com/2003/10/Serialization/Arrays" xmlns:i="http://www.w3.org/2001/XMLSchema-instance">';
        if($languageCode) {
            $requestXml .= "<string>$languageCode</string>";
        } else {
            throw new Exception('Language Code is empty.');
        }
        $requestXml .= '</ArrayOfstring>';
        return $requestXml;
    }

    function detectLanguageCode($inputStr, $authHeader)
    {
        //HTTP Detect Method URL.
        $detectMethodUrl = "http://api.microsofttranslator.com/V2/Http.svc/Detect?text=".urlencode($inputStr);
        //Call the curlRequest.
        $strResponse = $this->curlRequest($detectMethodUrl, $authHeader);
        //Interprets a string of XML into an object.
        $xmlObj = simplexml_load_string($strResponse);
        $languageCode = '';
        foreach((array)$xmlObj[0] as $val){
            $languageCode = $val;
        }
        return $languageCode;
    }

    function detectAllLanguageCodes($inputStr, $authHeader)
    {
        //HTTP Detect Method URL.
        $detectMethodUrl = "http://api.microsofttranslator.com/V2/Http.svc/Detect?text=".urlencode($inputStr);
        //Call the curlRequest.
        $strResponse = $this->curlRequest($detectMethodUrl, $authHeader);
        //Interprets a string of XML into an object.
        $xmlObj = simplexml_load_string($strResponse);
        $languageCode = '';
        foreach ((array)$xmlObj as $val){
            $languageCode .= $val;
        }
        return $languageCode;
    }

    function translate($inputStr, $fromLanguage, $toLanguage, $authHeader)
    {
        $contentType  = 'text/plain';
        $category     = 'general';

        $params = "text=".urlencode($inputStr)."&to=".$toLanguage."&from=".$fromLanguage;
        $translateUrl = "http://api.microsofttranslator.com/v1/Http.svc/Translate?$params";
        //Get the curlResponse.
        $curlResponse = $this->curlRequest($translateUrl, $authHeader);
        return $curlResponse;
    }
}

?>