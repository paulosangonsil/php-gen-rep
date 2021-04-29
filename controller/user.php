<?php
require_once 'includes.inc';

/**
 * @author Administrator
 *
 */
class User extends \Abstract_TbRec {
    private /*string*/  $_nameUser;
    private /*string*/  $_displayName;
    private /*int*/     $_hashPwd = 0;
    private /*byte[]*/  $_role          = User::ROLES_USER_USER;
    private /*byte[]*/  $_preferences;
    private /*string*/  $_geoLocation;
    private /*int*/     $_lastAccess;

    const   COL_NAME_ID         = 0,
            COL_NAME_USRNAME    = 1,
            COL_NAME_PASSWD     = 2,
            COL_NAME_TEXTNAME   = 3,
            COL_NAME_ROLE       = 4,
            COL_NAME_PREFERENC  = 5,
            COL_NAME_LASTACCES  = 6,

            TB_NAME_USER        = 0,

            /*Array<string>*/
            COL_NAMES   = array("id", "usrname", "passwd", "textname", "roleinapp", "prefs", "last_access"),
            TB_NAMES    = array(DB_PREFIX_NAME . "user"),

            ROLES_USER_ADMIN    = 1,
            ROLES_USER_USER     = 2,
            ROLES_NAMES = array(User::ROLES_USER_ADMIN => "Administrador", User::ROLES_USER_USER => "Usu&aacute;rio");

    protected function _init() {
        $resConn = Abstract_TbRec::_getDefaultDBObj()->get_creationException();

        if ( is_numeric($resConn) && intval($resConn) ) {
            die ( "User: erro na conexao c/ o BD $resConn" );
        }

        $resQuery = FALSE;

        if ($this->get_nameUser() != NULL) {
            $resQuery = Abstract_TbRec::_getDefaultDBObj()->query( User::TB_NAMES[User::TB_NAME_USER],
                "*", array(User::COL_NAMES[User::COL_NAME_USRNAME] => $this->get_nameUser() ) );
        }
        else if ($this->get_id() != UNDEFINED) {
            $resQuery = Abstract_TbRec::_getDefaultDBObj()->query( User::TB_NAMES[User::TB_NAME_USER],
                "*", array(User::COL_NAMES[User::COL_NAME_ID] => $this->get_id() ) );
        }

        if ($resQuery) {
            $queryResObj = Abstract_TbRec::_getDefaultDBObj()->getLastQueryResult();

            if ($queryResObj != FALSE) {
                $currObj = current($queryResObj);

                $this->set_displayName($currObj[User::COL_NAMES[User::COL_NAME_TEXTNAME]]);
                $this->set_id($currObj[User::COL_NAMES[User::COL_NAME_ID]]);
                $this->set_Pwd($currObj[User::COL_NAMES[User::COL_NAME_PASSWD]], TRUE);
                $this->set_nameUser($currObj[User::COL_NAMES[User::COL_NAME_USRNAME]]);
                $this->set_role($currObj[User::COL_NAMES[User::COL_NAME_ROLE]]);
                $this->set_preferences(json_decode($currObj[User::COL_NAMES[User::COL_NAME_PREFERENC]], TRUE));
                $this->set_lastAccess($currObj[User::COL_NAMES[User::COL_NAME_LASTACCES]]);
            }
        }
    }

    /**
     */
    public function __construct($id_or_nameUser) {
        if ( is_numeric($id_or_nameUser) ) {
            parent::__construct ( $id_or_nameUser, Abstract_TbRec::_getDefaultDBObj() );
        }
        else {
            parent::__construct ( UNDEFINED, Abstract_TbRec::_getDefaultDBObj() );

            $this->set_nameUser($id_or_nameUser);
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

        $valuesMap[User::COL_NAMES[User::COL_NAME_PASSWD]] = $this->get_hashPwd() ;
        $valuesMap[User::COL_NAMES[User::COL_NAME_TEXTNAME]] = $this->get_displayName();
        $valuesMap[User::COL_NAMES[User::COL_NAME_ROLE]] = $this->get_role();
        $valuesMap[User::COL_NAMES[User::COL_NAME_PREFERENC]] = json_encode($this->get_preferences());
        $valuesMap[User::COL_NAMES[User::COL_NAME_LASTACCES]] = $this->get_lastAccess();

        if ( $this->get_id() != UNDEFINED ) {
            $condsMap  = array();

            $condsMap[User::COL_NAMES[User::COL_NAME_ID]] = $this->get_id();

            $mtdRet = Abstract_TbRec::_getDefaultDBObj()->
                            update(User::TB_NAMES[User::TB_NAME_USER], $valuesMap, $condsMap);
        }
        else {
            $valuesMap[User::COL_NAMES[User::COL_NAME_ID]] = 0;

            $valuesMap[User::COL_NAMES[User::COL_NAME_USRNAME]] = $this->get_nameUser();

            $mtdRet = Abstract_TbRec::_getDefaultDBObj()->insert(User::TB_NAMES[User::TB_NAME_USER], $valuesMap);

            // Atualizar os dados
            if ($mtdRet) {
                $this->_init();
            }
        }

        return $mtdRet;
    }

    /**
     * _nameUser
     * @return string
     */
    public function get_nameUser(): ?string {
        return $this->_nameUser;
    }

    /**
     * _nameUser
     * @param string $_nameUser
     * @return User
     */
    public function set_nameUser($_nameUser){
        $this->_nameUser = strtolower($_nameUser);
    }

    /**
     * _hashPwd
     * @return int
     */
    public function get_hashPwd() {
        return $this->_hashPwd;
    }

    /**
     * 
     * @param string $pwdGuess
     * @return boolean
     */
    public /*boolean*/ function compare_Pwd(/*string*/ $pwdGuess, bool $hashed = FALSE): bool {
        $pwdsha1 = ($hashed) ? $pwdGuess : sha1($pwdGuess);

        return strcmp($this->get_hashPwd(), $pwdsha1) == 0;
    }

    /**
     * _hashPwd
     * @param int $_hashPwd
     */
    public function set_Pwd($_hashPwd, /*bool*/ $alreadyHashed = FALSE){
        $this->_hashPwd = ($alreadyHashed) ? $_hashPwd : sha1 ($_hashPwd);
    }

    /**
     * @return List<User>
     */
    //+listAllUsersIds(): List<int> => (static)
    static /*List<User>*/ public function listAllUsers(): array {
        $mtdRet = NULL;

        if ( Abstract_TbRec::_getDefaultDBObj()->query( User::TB_NAMES[User::TB_NAME_USER],
                User::COL_NAMES[User::COL_NAME_USRNAME] ) ) {
            $mtdRet = array();

            foreach (Abstract_TbRec::_getDefaultDBObj()->getLastQueryResult() as $itemValue) {
                $mtdRet[] = new User( $itemValue[User::COL_NAMES[User::COL_NAME_USRNAME]]);
            }
        }

        return $mtdRet;
    }

    static protected function listAll($cond = NULL, $offsetPage = NULL) {}

    /**
     * @return List<User>
     */
    //+listAllUsersNames(): List<string> => (static)
    static /*List<string>*/ public function listAllUsersNames(): array {
        Abstract_TbRec::_getDefaultDBObj()->query( User::TB_NAMES[User::TB_NAME_USER],
            User::COL_NAMES[User::COL_NAME_USRNAME] );

        return Abstract_TbRec::_getDefaultDBObj()->getLastQueryResult();
    }

    /*int*/ protected function idsToDelete(/*List<int>*/ $idList) {}

    /**
     * @param List<int> $idList
     * @return int
     */
    //+idsToDelete(List<int>): int => (static)
    static /*int*/ public function userIdsToDelete(/*List<int>*/ $idList) {
        return Abstract_TbRec::_getDefaultDBObj()->delete(User::TB_NAMES[User::TB_NAME_USER], $idList);
    }

    /**
     * _displayName
     * @return string
     */
    public function get_displayName(): ?string {
        return $this->_displayName;
    }

    /**
     * _displayName
     * @param string $_displayName
     * @return User
     */
    public function set_displayName($_displayName){
        $this->_displayName = $_displayName;
    }

    /**
     * _role
     * @return array(string)
     */
    public /*int*/ function get_role(): ?array {
        return $this->_role;
    }

    /**
     * _role
     * @param int $role
     */
    public function set_role(/*array(string)*/$role){
        $this->_role = $role;
    }

    /**
     * _preferences
     * @return array(string)
     */
    public /*array(string)*/ function get_preferences(): ?array {
        return $this->_preferences;
    }

    /**
     * _preferences
     * @param array(string) $prefs
     */
    public function set_preferences(/*array(string)*/$prefs){
        $this->_preferences = $prefs;
    }

    /**
     * _geoLocation
     * @return string
     */
    public function get_geoLocation() : ?string {
        return $this->_geoLocation;
    }

    /**
     * _geoLocation
     * @param string $_geoLocation
     * @return string
     */
    public function set_geoLocation($_geoLocation): ?string{
        $this->_geoLocation = $_geoLocation;
        return $this;
    }

    /**
     * _lastAccess
     * @return int
     */
    public function get_lastAccess(): int {
        return $this->_lastAccess;
    }

    /**
     * _lastAccess
     * @param int $lastAccess
     */
    public function set_lastAccess(/*int*/ $lastAccess){
        $this->_lastAccess = $lastAccess;
    }
}

/*$userLogged = new User('admin');
$userLogged->set_Pwd('paulo');
$userLogged->set_displayName('Administrator');
$userLogged->store();
$userLogged = new User('psgsilva');
$userLogged->set_Pwd('psgsilva');
$userLogged->set_displayName('Paulo Santos Gonzaga Silva');
$userLogged->store();
/*User::idsToDelete(array(User::COL_NAMES[User::COL_NAME_ID] => 1));
print_r ( User::listAllUsers() );*/
