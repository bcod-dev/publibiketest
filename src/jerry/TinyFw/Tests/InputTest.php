<?php
namespace TinyFw\Test;

use TinyFw\Input;

class InputTest extends \PHPUnit_Framework_TestCase
{
    private $input = null;

    public function setup()
    {
        $this->input = Input::instance();
    }

    /**
     * Simple test :)
     */
    public function testControlAndActionName()
    {
        $controllerName = $this->input->controllerName();
        $this->assertEquals('default', $controllerName);

        $actionName = $this->input->actionName();
        $this->assertEquals('index', $actionName);

        $_GET['_contr_'] = 'testing';
        $_GET['_act_'] = 'tumlum';

        $this->input->setGet($_GET);
        $controllerName = $this->input->controllerName();
        $this->assertEquals($controllerName, 'testing');

        $actionName = $this->input->actionName();
        $this->assertEquals('tumlum', $actionName);

        $this->assertEquals($this->input->cookieVarialbe('testing'), null);
        $this->assertEquals($this->input->getVariable('testing'), null);
        $this->assertEquals($this->input->postVariable('testing'), null);

        $this->assertEquals($this->input->getVariable('_contr_'), 'testing');

    }


}