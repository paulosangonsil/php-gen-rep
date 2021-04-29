<?php
require_once 'model/includes.inc';

$inCmd = Utils\General::getHTTPVar(_STR_IN_CMD);

$queryErr = TRUE;

if (!empty($inCmd)) {
    // $obj = new JSONCmdIn('{"username":"esp8266","userpasswd":"esp8266","appid":1,"data":"{\"relayid\":0, \"mode\":0}"}');
    $obj = new JSONCmdIn($inCmd);

    if ($obj->isValidJSON()) {
        $convObj = $obj->convObj();

        if ($convObj != NULL) {
            $appInfo = new AppInfo($convObj->getAppid());

            if ($appInfo->get_id() != UNDEFINED) {
                $entryOwner = new User($convObj->getUsername());

                if ( $entryOwner->compare_Pwd( $convObj->getUserpasswd() ) ) {
                    $newEntry = new Entry($entryOwner, $appInfo);

                    $newEntry->set_data($convObj->getData());

                    $newEntry->store();

                    $queryErr = FALSE;
                }
            }
        }
    }
}

if ($queryErr) {
    http_response_code(401);
}
