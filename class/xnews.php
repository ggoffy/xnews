<?php
/*
 You may not change or alter any portion of this comment or credits
 of supporting developers from this source code or any supporting source code
 which is considered copyrighted (c) material of the original comment or credit authors.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 */
/**
 *  XnewsXnews class
 *
 * @copyright       The XUUPS Project http://sourceforge.net/projects/xuups/
 * @license         http://www.fsf.org/copyleft/gpl.html GNU public license
 * @package         Class
 * @subpackage      Utils
 * @since           2.00
 * @author          trabis <lusopoemas@gmail.com>
 * @version         $Id:$
 */
defined('XOOPS_ROOT_PATH') || die('XOOPS root path not defined');

/**
 * Class XnewsXnews
 */
class XnewsXnews
{
    public $dirname;
    public $module;
    public $handler;
    public $config;
    public $debug;
    public $debugArray = [];

    /**
     * @param $debug
     */
    protected function __construct($debug)
    {
        $this->debug   = $debug;
        $this->dirname = basename(dirname(__DIR__));
    }

    /**
     * @param bool $debug
     *
     * @return XnewsXnews
     */
    public static function getInstance($debug = false)
    {
        static $instance = false;
        if (!$instance) {
            $instance = new self($debug);
        }

        return $instance;
    }

    public function &getModule()
    {
        if (null === $this->module) {
            $this->initModule();
        }

        return $this->module;
    }

    /**
     * @param null $name
     *
     * @return null
     */
    public function getConfig($name = null)
    {
        if (null === $this->config) {
            $this->initConfig();
        }
        if (!$name) {
            $this->addLog('Getting all config');

            return $this->config;
        }
        if (!isset($this->config[$name])) {
            $this->addLog("ERROR :: CONFIG '{$name}' does not exist");

            return null;
        }
        $this->addLog("Getting config '{$name}' : " . print_r($this->config[$name], true));

        return $this->config[$name];
    }

    /**
     * @param null $name
     * @param null $value
     *
     * @return mixed
     */
    public function setConfig($name = null, $value = null)
    {
        if (null === $this->config) {
            $this->initConfig();
        }
        $this->config[$name] = $value;
        $this->addLog("Setting config '{$name}' : " . $this->config[$name]);

        return $this->config[$name];
    }

    /**
     * @param $name
     *
     * @return mixed
     */
    public function &getHandler($name)
    {
        $this->initHandler($name);
        if (!isset($this->handler[$name . 'Handler'])) {
            $this->initHandler($name);
        }
        $this->addLog("Getting handler '{$name}'");

        return $this->handler[$name . 'Handler'];
    }

    public function initModule()
    {
        global $xoopsModule;
        if (isset($xoopsModule) && is_object($xoopsModule) && $xoopsModule->getVar('dirname') == $this->dirname) {
            $this->module = $xoopsModule;
        } else {
            $hModule      = xoops_getHandler('module');
            $this->module = $hModule->getByDirname($this->dirname);
        }
        $this->addLog('INIT MODULE');
    }

    public function initConfig()
    {
        $this->addLog('INIT CONFIG');
        $hModConfig   = xoops_getHandler('config');
        $this->config = $hModConfig->getConfigsByCat(0, $this->getModule()->getVar('mid'));
    }

    /**
     * @param $name
     */
    public function initHandler($name)
    {
        $this->addLog('INIT ' . $name . ' HANDLER');
        $this->handler[$name . 'Handler'] = xoops_getModuleHandler($name, $this->dirname);
    }

    /**
     * @param $log
     */
    public function addLog($log)
    {
        if ($this->debug) {
            if (is_object($GLOBALS['xoopsLogger'])) {
                $GLOBALS['xoopsLogger']->addExtra($this->module->name(), $log);
            }
        }
    }
}
