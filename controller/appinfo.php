<?php
require_once 'includes.inc';

/**
 * @author Administrator
 *
 */
class AppInfo extends \Abstract_TbRec {
    private /*string*/  $_nameApp;

    const   COL_NAME_ID         = 0,
            COL_NAME_TEXT       = 1,

            TB_NAME_APPINFO    = 0;

    const   /*Array<string>*/   COL_NAMES   = array("id", "name"),
                                TB_NAMES    = array(DB_PREFIX_NAME . "appinfos");

    protected function _init() {
        $resConn = Abstract_TbRec::_getDefaultDBObj()->get_creationException();

        if ( is_numeric($resConn) && intval($resConn) ) {
            die ( "AppInfo: erro na conexao c/ o BD $resConn" );
        }

        $resQuery = FALSE;

        if ($this->get_nameApp() != NULL) {
            $resQuery = Abstract_TbRec::_getDefaultDBObj()->query( AppInfo::TB_NAMES[AppInfo::TB_NAME_APPINFO],
                AppInfo::COL_NAMES[AppInfo::COL_NAME_ID],
                array( AppInfo::COL_NAMES[AppInfo::COL_NAME_TEXT] => $this->get_nameApp() ) );
        }
        else if ($this->get_id() != UNDEFINED) {
            $resQuery = Abstract_TbRec::_getDefaultDBObj()->
                query( AppInfo::TB_NAMES[AppInfo::TB_NAME_APPINFO], '*',
                array(AppInfo::COL_NAMES[AppInfo::COL_NAME_ID] => $this->get_id() ) );
        }

        if ($resQuery) {
            $queryResObj = Abstract_TbRec::_getDefaultDBObj()->getLastQueryResult();

            if ($queryResObj != FALSE) {
                $currObj = current($queryResObj);

                $this->set_id($currObj[AppInfo::COL_NAMES[AppInfo::COL_NAME_ID]]);

                if ($this->get_nameApp() == NULL) {
                    $this->set_nameApp($currObj[AppInfo::COL_NAMES[AppInfo::COL_NAME_TEXT]]);
                }
            }
        }
        else {
            // die ("AppInfo: falta parametros na criacao objeto");
            $this->set_id(UNDEFINED);
        }
    }

    /**
     */
    public function __construct(/*int*/ $appId) {
        if ( ! is_numeric($appId) ) {
            parent::__construct ( UNDEFINED, Abstract_TbRec::_getDefaultDBObj() );

            $this->set_nameApp($appId);
        }
        else {
            parent::__construct ( $appId, Abstract_TbRec::_getDefaultDBObj() );

            if ($appId < UNDEFINED) {
                die ("AppInfo: id da categoria negativo");
            }
        }

        $this->_init();
    }

    /**
     * (non-PHPdoc)
     *
     * @see Abstract_TbRec::store()
     */
    public function store(): bool {
        $mtdRet = FALSE;

        $valuesMap  = array();

        $valuesMap[AppInfo::COL_NAMES[AppInfo::COL_NAME_TEXT]] = $this->get_nameApp();

        if ( $this->get_id() != UNDEFINED ) {
            $condsMap  = array();

            $condsMap[AppInfo::COL_NAMES[AppInfo::COL_NAME_ID]] = $this->get_id();

            $mtdRet = Abstract_TbRec::_getDefaultDBObj()->
                        update(AppInfo::TB_NAMES[AppInfo::TB_NAME_APPINFO], $valuesMap, $condsMap);
        }
        else {
            $valuesMap[AppInfo::COL_NAMES[AppInfo::COL_NAME_ID]] = 0;

            $mtdRet = Abstract_TbRec::_getDefaultDBObj()->
                insert(AppInfo::TB_NAMES[AppInfo::TB_NAME_APPINFO], $valuesMap);

            if ($mtdRet) {
                $this->_init();
            }
        }

        return $mtdRet;
    }

    /**
     * _nameApp
     * @return string
     */
    public function get_nameApp(){
        return $this->_nameApp;
    }

    /**
     * _nameApp
     * @param string $_nameApp
     * @return AppInfo
     */
    public function set_nameApp($_nameApp){
        $this->_nameApp = $_nameApp;
    }

    static public /*Array<AppInfo>*/ function listAll ($cond = NULL, $offsetPage = NULL) {
        $mtdRet = NULL;

        if ( Abstract_TbRec::_getDefaultDBObj()->
                query( AppInfo::TB_NAMES[AppInfo::TB_NAME_APPINFO], AppInfo::COL_NAMES[AppInfo::COL_NAME_TEXT] ) ) {
            $mtdRet = array();

            foreach (Abstract_TbRec::_getDefaultDBObj()->getLastQueryResult() as $itemValue) {
                $mtdRet[] = new AppInfo( $itemValue[AppInfo::COL_NAMES[AppInfo::COL_NAME_ID]] );
            }
        }

        return $mtdRet;
    }

    /**
     *
     */
    //+idsToDelete(user: int; list: List<int>) => (static)
    public /*int*/ function idsToDelete (/*List<int>*/ $listIds) {
        return  Abstract_TbRec::_getDefaultDBObj()->
            delete(AppInfo::TB_NAMES[AppInfo::TB_NAME_APPINFO], $listIds);
    }
}
