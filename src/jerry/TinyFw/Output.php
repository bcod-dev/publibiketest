<?php
namespace TinyFw;

use Exception;

class Output
{
    /*
     * Data
     */
    private $data = null;

    /*
     * Content Type
     */
    private $contentType = null;

    /**
     * Headers
     */
    private $headers = array();


    /**
     * Constructor
     * @param mixed $data
     * @param string $contentType
     */
    function __construct($data = null, $contentType = null)
    {
        $this->setOutputData($data, $contentType);
    }

    /**
     * Set output data
     * @param unknown $data
     * @param null $contentType
     */
    public function setOutputData($data, $contentType = null)
    {
        $this->data = $data;
        $this->contentType = $contentType;
    }

    /**
     * Add header to output
     * @param unknown $header
     * @param null $replace
     * @param null $code
     */
    public function addHeader($header, $replace = null, $code = null)
    {
        if( $replace != null && $code != null ){
            $header = array($header, $replace, $code);
        }

        $this->headers[] = $header;
    }


    public function send($exit = true)
    {
        switch ($this->contentType) {
            case 'application/json':
            case 'text/javascript':
            case 'application/x-javascript':
            case 'application/javascript':
            case 'application/jsonp':
                $this->processJSON();
                break;
            case 'application/redirect': 
                $this->processRedirect();
                break;
            default:
                break;
        }

        //Send headers
        $this->sendHeaders();

        echo $this->data;

        if ($exit == true) {
            exit(0);
        }
    }

    public function processRedirect()
    {
        if(isset($this->data['path']) &&  isset($this->data['code'])){
            $path = $this->data['path'];
            $code = $this->data['code'];
            $this->addHeader("Location: $path", true, $code);
            $this->data = "";
        } else {
            throw new Exception("Either \$path or \$code is missing");
        }
    }

    /**
     * Send headers
     */
    private function sendHeaders()
    {
        foreach ($this->headers as $header) {
            if( is_array($header) && count($header) >= 3){
                Header($header[0], $header[1], $header[2]);
            } else {
                Header($header);
            }
        }
    }

    public function setContentType($contentType)
    {
        $this->contentType = $contentType;
    }


    public function processJSON()
    {
        $this->addHeader("Content-Type: ".$this->contentType);
        $this->data = is_array($this->data) ? $this->data : array($this->data);
        if (is_array($this->data)) {
            $this->data = json_encode($this->data);
        }
    }

}

?>