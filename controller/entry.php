<?php
require_once 'includes.inc';

/**
 *
 * @author Administrator
 *
 */
class Entry extends \Abstract_TbRec {
    const   COL_NAME_ID             = 0;
    const   COL_NAME_USER           = 1;
    const   COL_NAME_APP            = 2;
    const   COL_NAME_DATA           = 3;
    const   COL_NAME_TSTAMP         = 4;

    const   TB_NAME_ENTRY    = 0;

    /*Array<string>*/
    const   COL_NAMES   = array("id", "idusr", "idapp", "data", "tstamp");
    const   TB_NAMES    = array(DB_PREFIX_NAME . "entry");

    const   TIMESTAMP_FORMAT    = "YmdHis";
    const   TIMESTAMP_FORMAT_DB = "Y-m-d H:i:s";

    protected /*AppInfo*/ $_objApp;
    protected /*User*/ $_objUser;
    protected /*int*/ $_timestamp;
    protected /*string*/ $_data;

    /**
     */
    public function __construct($objUser, $objAppInfo, $connId = UNDEFINED) {
        parent::__construct($connId, Abstract_TbRec::_getDefaultDBObj() );

        if ( ( ($objUser == NULL) && ($objAppInfo == NULL) ) &&
            ($connId == UNDEFINED) ) {
                die("Entry: a User and a AppInfo or a valid Id is needed");
            }

        $this->set_objUser($objUser);
        $this->set_objAppInfo($objAppInfo);

        $this->_init();
    }

    /**
     * (non-PHPdoc)
     *
     * @see Abstract_TbRec::_init()
     */
    protected function _init() {
        $newRec = FALSE;

        $resConn = Abstract_TbRec::_getDefaultDBObj()->get_creationException();

        if ( is_numeric($resConn) && intval($resConn) ) {
            die ( "Entry: connection error with the DB $resConn" );
        }

        $resQuery = FALSE;

        if ($this->get_id() != UNDEFINED) {
            $resQuery = Abstract_TbRec::_getDefaultDBObj()->query( Entry::TB_NAMES[Entry::TB_NAME_ENTRY],
                "*", array(Entry::COL_NAMES[Entry::COL_NAME_ID] => $this->get_id() ) );
        }
        else {
            $newRec = TRUE;

            $this->set_timestamp( new DateTime() );
        }

        if (! $newRec) {
            if ($resQuery) {
                $queryResObj = Abstract_TbRec::_getDefaultDBObj()->getLastQueryResult();

                if ($queryResObj != FALSE) {
                    $currObj = current($queryResObj);

                    $this->set_objUser(new User($currObj[Entry::COL_NAMES[Entry::COL_NAME_USRID]]));
                    $this->set_objAppInfo(new AppInfo($currObj[Entry::COL_NAMES[Entry::COL_NAME_APP]]));

                    $this->set_timestamp( DateTime::createFromFormat(Entry::TIMESTAMP_FORMAT_DB,
                        $currObj[Entry::COL_NAMES[Entry::COL_NAME_TSTAMP]]) );
                }
            }
            else {
                die("Entry: invalid identification");
            }
        }
    }

    /**
     * (non-PHPdoc)
     *
     * @see Abstract_TbRec::idsToDelete()
     */
    protected function idsToDelete($listIds) {
        // TODO - Insert your code here
    }

    /**
     * (non-PHPdoc)
     *
     * @see Abstract_TbRec::store()
     */
    public function store(): bool {
        $mtdRet = FALSE;

        $valuesMap  = array();

        if ( ($this->get_objAppInfo() != NULL) &&
            ($this->get_objUser() != NULL) ) {
                $valuesMap[Entry::COL_NAMES[Entry::COL_NAME_DATA]] = $this->get_data();

                if ( $this->get_id() != UNDEFINED ) {
                    $condsMap = array();

                    $condsMap[Entry::COL_NAMES[Entry::COL_NAME_ID]] = $this->get_id();

                    $mtdRet = Abstract_TbRec::_getDefaultDBObj()->
                        update(Entry::TB_NAMES[Entry::TB_NAME_CMD_CONN], $valuesMap, $condsMap);
                }
                else {
                    $valuesMap[Entry::COL_NAMES[Entry::COL_NAME_ID]] = 0;
                    $valuesMap[Entry::COL_NAMES[Entry::COL_NAME_APP]] = $this->get_objAppInfo()->get_id();
                    $valuesMap[Entry::COL_NAMES[Entry::COL_NAME_USER]] = $this->get_objUser()->get_id();

                    $valuesMap[Entry::COL_NAMES[Entry::COL_NAME_TSTAMP]] =
                        (new DateTime() )->format(Entry::TIMESTAMP_FORMAT_DB);

                    $mtdRet = Abstract_TbRec::_getDefaultDBObj()->
                        insert(Entry::TB_NAMES[Entry::TB_NAME_ENTRY], $valuesMap);

                    // Refresh the object data's
                    if ($mtdRet) {
                        $this->_init();
                    }
                }
            }

        return $mtdRet;
    }
    
    /**
     * (non-PHPdoc)
     *
     */
    static public function lastConnections($objAppInfo, $objUser, $maxConns = 0) {
        $mtdRet = NULL;

        $cond = array(Entry::COL_NAMES[Entry::COL_NAME_APP] => $objAppInfo->get_id(),
                        Entry::COL_NAMES[Entry::COL_NAME_USER] => $objUser->get_id());

        $order = array(Entry::COL_NAMES[Entry::COL_NAME_TSTAMP] => Abstract_TbRec::LIST_ORDER_DESC);

        if ( Abstract_TbRec::_getDefaultDBObj()->query( Entry::TB_NAMES[Entry::TB_NAME_CMD_CONN],
            Entry::COL_NAMES[Entry::COL_NAME_ID], $cond, $order, 0, $maxConns) ) {
            $mtdRet = array();

            foreach (Abstract_TbRec::_getDefaultDBObj()->getLastQueryResult() as $itemValue) {
                $mtdRet[] = new Entry(NULL, NULL, $itemValue[Entry::COL_NAMES[Entry::COL_NAME_ID]]);
            }
        }

        return $mtdRet;
    }

    /**
     * (non-PHPdoc)
     *
     * @see Abstract_TbRec::listAll()
     */
    static protected function listAll($cond = NULL, $offsetPage = NULL) {
        $mtdRet = NULL;

        if ( Abstract_TbRec::_getDefaultDBObj()->query(
            AppInfo::TB_NAMES[Entry::TB_NAME_CMD_CONN], Entry::COL_NAMES[Entry::COL_NAME_ID]) ) {
            $mtdRet = array();

            foreach (Abstract_TbRec::_getDefaultDBObj()->getLastQueryResult() as $itemValue) {
                $mtdRet[] = new Entry( NULL, NULL, $itemValue[Entry::COL_NAMES[Entry::COL_NAME_ID]]);
            }
        }

        return $mtdRet;
    }

    /**
     */
    function __destruct() {
        // TODO - Insert your code here
    }

    /**
     * _objApp
     * @return AppInfo
     */
    public function get_objAppInfo(): AppInfo {
        return $this->_objApp;
    }

    /**
     * _objApp
     * @param AppInfo $_objApp
     * @return Entry
     */
    public function set_objAppInfo($_objApp){
        $this->_objApp = $_objApp;
        return $this;
    }

    /**
     * _objUser
     * @return User
     */
    public function get_objUser(): User {
        return $this->_objUser;
    }

    /**
     * _objUser
     * @param User $_objUser
     * @return Entry
     */
    public function set_objUser($_objUser){
        $this->_objUser = $_objUser;
        return $this;
    }

    protected function _genToken(): string {
        $mtdRet = '';

        $toEncode = $this->get_Command();

        if ( /*( ! is_null($toEncode) ) &&*/
            ( ($this->get_objUser() != NULL) &&
                ($this->get_objAppInfo() != NULL) ) ) {
            $auxStr = $this->get_objAppInfo()->get_nameAlias();

            if ( ! is_null($auxStr) ) {
                $toEncode .= $auxStr . $this->get_objUser()->get_id();
            }

            $mtdRet = sha1($toEncode);
        }

        return $mtdRet;
    }

    /**
     * _token
     * @return string
     */
    public function get_token(): string {
        if  (is_null($this->_token) ) {
            $this->set_token($this->_genToken());
        }

        return $this->_token;
    }

    /**
     * _token
     * @param int $_token
     * @return Entry
     */
    public function set_token($_token) {
        $this->_token = $_token;
        return $this;
    }

    /**
     * _timestamp
     * @return int
     */
    public function get_timestamp(): int {
        return $this->_timestamp;
    }

    /**
     * _timestamp
     * @param int $_timestamp
     * @return Entry
     */
    public function set_timestamp($_timestamp) {
        $this->_timestamp = $_timestamp;
        return $this;
    }

    /**
     * _data
     * @return string
     */
    public function get_data(){
        return $this->_data;
    }

    /**
     * _data
     * @param string $_data
     * @return Entry
     */
    public function set_data($_data){
        $this->_data = $_data;
        return $this;
    }
}
