<?php
namespace TinyFw;

use TinyFw\DB\MySQLPDO;

class Kernel
{
    /**
     * @var Input
     */
    private $input = null;

    /**
     * @var Output
     */
    private $output = null;

    /**
    * @var Lang
    */
    private $lang;


    private $controller = null;

    /**
     * @var MySQLPDO
     */
    private static $database;


    function __construct()
    {
        $this->input = Input::instance();
        $this->output = new Output();
        $this->lang = Lang::instance();

    }

    /**
     * Connect to database
     */
    public static function connectDatabase()
    {
        self::$database = new MySQLPDO(DATABASE_DSN, DATABASE_USER, DATABASE_PASS);
        return self::$database;
    }

    /**
     * @return MySQLPDO
     */
    public static function getDatabase()
    {
        if (!self::$database) {
            return self::connectDatabase();
        }
        return self::$database;
    }

    /**
     * Build class name
     * @param string $route
     * @param null $prefix
     * @param null $suffix
     * @return string
     */
    public static function buildClassNameFromRoutingParams($route, $prefix = null, $suffix = null)
    {
        $route = strtolower($route);
        $tmp = explode('-', $route);

        $name = "";
        foreach ($tmp as $part) {
            $name .= ucfirst($part);
        }

        if ($prefix != null) {
            $name = $prefix . $name;
        }

        if ($suffix != null) {
            $name .= $suffix;
        }

        return $name;
    }

    public function init()
    {

    }

    private function &findController()
    {
        $controllerName = $this->input->controllerName();
        $controllerClass = self::buildClassNameFromRoutingParams($controllerName, null, 'Controller');
        $controllerClassFull = "Controller\\{$controllerClass}";

        $this->controller = new $controllerClassFull($this->input, $this->lang);

        return $this->controller;
    }

    /**
     * Find controller & actions depend on input routing
     */
    public function process()
    {
        $controller = $this->findController();

        $controller->process();
        $output = $controller->getOutput();

        $layout = $this->getLayoutPath();
        if ($this->getLayoutPath() !== false) {
            if (!file_exists($layout)) {
                throw new \Exception("Layout template file {$layout} is not found.");
            }

            ob_start();
            include $layout;
            $output = ob_get_clean();
        }

        $this->output->setOutputData($output, $controller->getContentType());
    }

    private function getLayoutPath()
    {
        if ($this->controller->getLayout() !== false) {
            $layoutName = self::buildClassNameFromRoutingParams($this->controller->getLayout());
            $layoutName = strtolower($layoutName);
            $layout = APP_DIR . "/Template/{$layoutName}.layout.html.php";
            return $layout;
        }
        return false;
    }


    public function render()
    {
        return $this->output->send();
    }

    public function terminate()
    {
        exit(0);
    }
}
?>