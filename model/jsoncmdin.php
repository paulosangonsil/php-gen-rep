<?php
require_once 'includes.inc';

/**
 *
 * @author Administrator
 *
 */
class JSONCmdIn extends \Abstract_JSONObj {
    protected /*String*/ $username;
    protected /*String*/ $userpasswd;
    protected /*int*/ $appid;
    protected /*String*/ $data;

    /**
     *
     * @param string $fromString
     */
    public function __construct($fromString) {
        parent::__construct($fromString);
    }

    /**
     */
    function __destruct() {
    }

    public function convObj() : ?JSONCmdIn {
        $mtdRet = NULL;

        $objFromStr = $this->get_objJSON();

        if ($objFromStr != NULL) {
            $reflect = [];

            $reflect[] = new ReflectionClass($this);
            $reflect[] = get_object_vars ($objFromStr);

            $props = [];

            $props[] = $reflect[0]->getProperties();

            $invalid = FALSE;

            foreach ($props[0] as $propThis) {
                $currProp = $propThis->getName();

                // Ignore the parent class members...
                if ( strcmp($propThis->class, 'Abstract_JSONObj') ) {
                    if ( ! isset($reflect[1][$currProp]) ) {
                        $invalid = TRUE;

                        break;
                    }
                }
            }

            if (! $invalid) {
                $mtdRet = Abstract_JSONObj::convertObjectClass($objFromStr, 'JSONCmdIn');
            }
        }

        return $mtdRet;
    }

    /**
     * username
     * @return string
     */
    public function getUsername() : ?string {
        return $this->username;
    }

    /**
     * username
     * @param string $username
     * @return JSONCmdIn
     */
    public function setUsername($username) : JSONCmdIn {
        $this->username = $username;
        return $this;
    }

    /**
     * userpasswd
     * @return string
     */
    public function getUserpasswd() : ?string {
        return $this->userpasswd;
    }

    /**
     * userpasswd
     * @param string $userpasswd
     * @return JSONCmdIn
     */
    public function setUserpasswd($userpasswd) : JSONCmdIn {
        $this->userpasswd = $userpasswd;
        return $this;
    }

    /**
     * appid
     * @return int
     */
    public function getAppid() : ?int {
        return $this->appid;
    }

    /**
     * appid
     * @param int $appid
     * @return JSONCmdIn
     */
    public function setAppid($appid) : JSONCmdIn {
        $this->appid = $appid;
        return $this;
    }

    /**
     * data
     * @return string
     */
    public function getData() : ?string {
        return $this->data;
    }

    /**
     * data
     * @param string $data
     * @return JSONCmdIn
     */
    public function setData($data) : JSONCmdIn {
        $this->data = $data;
        return $this;
    }
}
