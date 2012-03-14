<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    /**
     * Init Db -- register db adapter
     */
    protected function _initDb() {
        $resource = $this->getPluginResource('db');

        Zend_Db_Table::setDefaultAdapter($resource->getDbAdapter());
        Zend_Registry::set('db', $resource->getDbAdapter());

        return $resource->getDbAdapter();
    }
    
    protected function _initRegisterConfig() {
        Zend_Registry::set('config', new Zend_Config($this->getOptions(), true));
    }
}

