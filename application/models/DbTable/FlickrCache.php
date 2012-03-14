<?php

class Model_DbTable_FlickrCache extends Zend_Db_Table_Abstract {
    protected $_name = 'flickr_cache';
    protected $_rowClass = 'Model_DbTable_Row_FlickrCache';
    protected $_dependentTables = array(
        'Model_DbTable_FlickrImage'
    );
    protected $_referenceMap = array();
}