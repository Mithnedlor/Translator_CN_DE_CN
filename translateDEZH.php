<?php
header("Access-Control-Allow-Origin:*");

require 'AccessTokenAuthentication.php';
require 'HTTPTranslator.php';

   class returnObj {
       public $inputStr = "";
       public $translated  = "";
       public $languageCode  = "";
       function deAndZh() {
           $authObj      = new AccessTokenAuthentication();
           //Get the Access token.
           $accessToken  = $authObj->getTokens();
           //Create the authorization Header string.
           $authHeader = "Authorization: Bearer ". $accessToken;
           //Create the Translator Object.
           $translatorObj = new HTTPTranslator();

           $this->languageCode = $translatorObj->detectLanguageCode($this->inputStr, $authHeader);

           $fromLanguage = "de";
           $toLanguage   = "zh";
           if(stripos($this->languageCode,"zh") === 0)
           {
               $fromLanguage = "zh";
               $toLanguage   = "de";
           }

           $translatedStr =  $translatorObj->translate($this->inputStr, $fromLanguage, $toLanguage, $authHeader);
           $this->translated  = $translatedStr;
        }
   }
   $e = new returnObj();
   if(isset($_POST['inputStr']))
   {
       $e->inputStr = $_POST['inputStr'];
       if(ctype_space($_POST['inputStr'])==false){
          $e->deAndZh();
       }
   }
   echo json_encode($e);
?>