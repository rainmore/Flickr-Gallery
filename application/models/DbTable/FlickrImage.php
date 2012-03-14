<?php

class Model_DbTable_FlickrImage extends Zend_Db_Table_Abstract {
    protected $_name = 'flickr_image';
    protected $_rowClass = 'Model_DbTable_Row_FlickrImage';
    protected $_dependentTables = array(
        
    );
    protected $_referenceMap = array(
        'FlickrCache' => array(
            'columns'       => 'company_level_id',
            'refTableClass' => 'Model_DbTable_FlickrCache',
            'refColumns'    => 'id'
        ),
    );
}