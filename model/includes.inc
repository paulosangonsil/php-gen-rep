<?php
    $includePaths = _genIncludePath(['controller', 'php-common', 'php-common/model', 'model/cmds', 'view', 'utils', 'model']);

    set_include_path (get_include_path () . PATH_SEPARATOR . $includePaths);

    const UNDEFINED   = -1;

    const DB_USERNAME       = "psgsilva";
    const DB_USERPASS       = "psgsilva";
    const DB_HOSTNAME       = "localhost";
    const DB_NAME           = "gen_rep";
    const DB_PREFIX_NAME    = "gen_rep_";

    const _STR_IN_CMD   = "in";

    const _OPER_LOGOFF    = "5";

    const FLD_OPER          = "cmpOper";
    const FLD_USRDATEZN     = "dateZone";
    const FLD_USRNAME       = "txtUsrName";
    const FLD_USRPWD        = "txtUserPass";
    const FLD_HOST          = "hostid";

    const _SESSION_LOGIN_NAME = 'usrname';
    const _SESSION_LOGIN_PWD  = 'usrpwd';
    const _SESSION_DATEZONE   = 'datezone';

    const PAG_DISP_CONSOLE    = "console.php";

    session_start();

    require_once 'abstract_dbaccess.php';
    require_once 'abstract_tbrec.php';
    require_once 'abstract_jsonobj.php';
    require_once 'mysql_pdodriver.php';
    require_once 'user.php';
    require_once 'appinfo.php';
    require_once 'utils.php';
    require_once 'jsoncmdin.php';
    require_once 'entry.php';
    
    function _strset($str, $char, $count) {
        $mtdRet = $str;

        for ($iCnt = 0; $iCnt < $count; $iCnt++) {
            $mtdRet .= $char;
        }

        return $mtdRet;
    }

    function _genIncludePath($folderNames): string {
        $MAXDEPTH = 3;

        $mtdRet = '';

        foreach ($folderNames as $folderName) {
            for ($iCnt = 0; $iCnt < $MAXDEPTH + 1; $iCnt++) {
                $mtdRet .= PATH_SEPARATOR;

                if ($iCnt == 0) {
                    $mtdRet .= './';
                }
                else {
                    $mtdRet = _strset($mtdRet, '../', $iCnt);
                }

                $mtdRet .= $folderName;
            }
        }

        return $mtdRet;
    }

    function _checkLogonCust(&$newLogin = NULL): string {
        if ( ! is_null($newLogin) ) {
            $newLogin = ( ! array_key_exists(_SESSION_LOGIN_NAME, $_SESSION) );
        }

        return Utils\General::_checkLogon();
    }
