<?php
$developmentMode = false;

switch ($developmentMode) {
    case true :
        $env['URL'] = 'https://test-dmz.param.com.tr:4443/turkpos.ws/service_turkpos_test.asmx?WSDL';
        $env['CLIENT_USERNAME'] = 'test';
        $env['CLIENT_CODE'] = 10738;
        $env['CLIENT_PASSWORD'] = 'test';
        $env['GUID'] = '0c13d406-873b-403b-9c09-a5766840d98c';
        break;
    default:
        $env['GUID'] = 'C6B28805-21A5-4505-848B-3F49B9445AC7';
        $env['CLIENT_USERNAME'] = 'TP10101216';
        $env['CLIENT_PASSWORD'] = '8D5CDD697A9A8577';
        $env['CLIENT_CODE'] = 77502;
        $env['URL'] = 'https://posws.param.com.tr/turkpos.ws/service_turkpos_prod.asmx?WSDL';
}