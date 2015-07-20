<?php
class test
{
    function show($ss)
    {
        return  $ss;
    }
}

function getUserInfo($name)
{
    return $name;
}

$server = new SoapServer(null, array('uri' =>'http://www.penghuang.de/translatortest/','location'=>'http://www.penghuang.de/translatortest/soapserver.php'));
$server->setClass('test');
$server->addFunction('getUserInfo');
$server->handle();
?>