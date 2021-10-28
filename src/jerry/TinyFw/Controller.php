<?php
namespace TinyFw;
class Controller
{
    /*
     * @var Input
     */
    protected $input = null;

    /*
     * @var string
     */
    protected $contentType = null;

    /*
     * This is not Output object, this is controller's output
     */
    protected $output = null;

    /*
     *@var string
     */
    protected $layout = 'default';

    /**
    * @var Lang
    */
    protected $lang;

    /**
     * Name of controller
     * @var string
     */
    protected $name = null;

    function __construct(Input $input, Lang $lang)
    {
        $this->input = $input;
        $this->name = $this->input->controllerName();
        $this->lang = $lang;
        $this->output = new Output();
    }

    public function process()
    {
        $actionName = $this->input->actionName();
        //$actionMethod = $actionName.'Action';
        $actionMethod = Kernel::buildClassNameFromRoutingParams($actionName, null, 'Action');
        $actionMethod = lcfirst($actionMethod);

        if (method_exists($this, $actionMethod)) {
            //$this->output = $this->{$actionMethod}();
            $this->output = call_user_func_array(array($this, $actionMethod), $this->input->getParams());
            return;
        }

        //TODO better exception handler
        throw new \Exception("Action $actionMethod doesn't exist.");
    }

    /**
     * Get layout
     * @return string
     */
    public function getLayout()
    {
        return $this->layout;
    }

    /**
     * Set layout
     * @param string $layout name
     */
    public function setLayout($layout)
    {
        $this->layout = $layout;
    }

    /**
     * Get output
     * @return mixed
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * Get content type of returned output
     * @return  string;
     */
    public function getContentType()
    {
        return $this->contentType;
    }

    /**
     * Set content type for returned output
     * @param string $contentType
     */
    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
    }

    /**
     * Mix data from action into template
     * @param mixed $data
     * @param string $template
     * @return string
     * @throws \Exception
     */
    public function render($data, $template = null)
    {
        $controllerName = Kernel::buildClassNameFromRoutingParams($this->name);
        $actionName = Kernel::buildClassNameFromRoutingParams($this->input->actionName());

        $data = is_array($data) ? $data : array('data' => $data);
        if ($template == null) {
            $template = $actionName . '.html.php';
        }
        $template = strtolower($template);
        $template = APP_DIR . "/Template/" . $controllerName . "/{$template}";

        if (file_exists($template)) {
            ob_start();
            extract($data);
            include $template;
            $result = ob_get_clean();
            return $result;
        }

        throw new \Exception("Template file {$template} is not found.");
    }

    /**
     * If render JSON, change contentype of docment
     * @param <array>|<json string> $data
     * @return null
     */
    public function renderJSON($data)
    {
        $this->contentType = 'application/json';
        
        $this->output = $data;
        $this->layout = false;
        return $this->output;
    }

    /**
     * Redirect by header
     * @param $path
     * @param int $code
     * @return null
     */
    public function redirect($path, $code = 301)
    {
        $this->contentType = 'application/redirect';
        $this->layout = false;
        $this->output = array( //TODO Refactor this
            'code' => $code,
            'path' => $path
        );
        return $this->output;
    }
}