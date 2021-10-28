<?php
namespace TinyFw;
class Input
{
    /*
     * Instance holder
     */
    private static $instance = null;

    /**
     * Create instance
     * @return \TinyFw\Input
     */
    public static function & instance()
    {
        if (self::$instance == null) {
            self::$instance = new self();
            self::$instance->processRouting();
            self::$instance->processRequest();
        }
        return self::$instance;
    }

    private $get, $post, $cookie, $request, $requestMethod;

    /**
     * Constructor
     * @param array $get
     * @param array $post
     * @param array $cookie
     * @param array $request
     */
    function __construct($get = null, $post = null, $cookie = null, $request = null)
    {
        $this->requestMethod = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : "GET";
        $this->get = $get == null ? $_GET : $get;
        $this->post = $post == null ? $_POST : $post;
        $this->cookie = $cookie == null ? $_COOKIE : $cookie;
        $this->request = $request == null ? $_REQUEST : $request;
    }

    /**
     * Process request ($_REQUEST / php://input
     */
    private function processRequest()
    {
        $stdInput = file_get_contents("php://input");
        //TODO Improve this to match with headers
        if($stdInput){
            if( isset($_REQUEST[$stdInput])){
                unset($_REQUEST[$stdInput]);
            }
            $stdInput = json_decode($stdInput, true);
        }


        if(is_array($stdInput)){
            $this->request = is_array($this->request) ? $this->request : array();
            $this->request = array_merge($this->request, $stdInput);
        }
    }

    /**
     * Process routing
     */
    private function processRouting()
    {
        //For nginx or cgi applications
        if (!$this->getVariable('_contr_')) {
            $route = $this->getVariable('_route_');
            if ($route) {
                $tmp = explode("/", trim($route, " /"));
                if (isset($tmp[0])) {
                    $_GET['_contr_'] = $tmp[0];
                    $this->get['_contr_'] = $tmp[0];

                    if (isset($tmp[1])) {
                        $_GET['_act_'] = $tmp[1];
                        $this->get['_act_'] = $tmp[1];
                    }

                    for ($i = 2; $i < count($tmp); $i++) {
                        if (isset($tmp[$i])) {
                            $this->get['_params_'][] = $tmp[$i];
                        }
                    }
                }
            }
        }
    }


    /**
     * Get request method
     * @return string
     */
    public function requestMethod()
    {
        return $this->requestMethod;
    }

    /**
     * @param array $request
     */
    public function setRequest(array $request)
    {
        $this->request = $request;
    }

    /**
     * @return array|null
     */
    public function getRequest()
    {
        return $this->request;
    }

    /**
     * Set custom $_GET variable
     * @param unknown $get
     */
    public function setGet($get)
    {
        $this->get = $get;
    }


    /**
     * $_POST
     * @param string $key
     * @return mixed
     */
    public function postVariable($key)
    {
        if (isset($this->post[$key])) {
            return $this->post[$key];
        }
        return null;
    }

    /**
     * $_GET
     * @param string $key
     * @return mixed
     */
    public function getVariable($key)
    {
        if (isset($this->get[$key])) {

            return $this->get[$key];
        }
        return null;
    }

    /**
     * $_REQUEST + php://input json sring
     * @param $key
     * @return null
     */
    public function requestVariable($key)
    {
        if(isset($this->request[$key])){
            return $this->request[$key];
        }
        return null;
    }

    /**
     * $_COOKIE
     * @param string $key
     * @return mixed
     */
    public function cookieVarialbe($key)
    {
        if (isset($this->cookie[$key])) {

            return $this->cookie[$key];
        }
        return null;
    }


    /**
     * Get controller name
     * @return string
     */
    public function controllerName()
    {
        $controllerName = $this->getVariable('_contr_');
        $controllerName = $controllerName == null ? 'default' : $controllerName;
        return $controllerName;
    }

    /**
     * Get action name
     * @return string
     */
    public function actionName()
    {
        $actionName = $this->getVariable('_act_');
        $actionName = $actionName == null ? 'index' : $actionName;
        return $actionName;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return isset($this->get['_params_']) ? $this->get['_params_'] : array();
    }

    /**
     * @param $index
     * @return null
     */
    public function getParam($index)
    {
        $params = $this->getParams();
        return isset($params[$index]) ? $params[$index] : null;
    }
}

?>