<?php
//require_once 'Phlickr/Api.php';
//require_once 'Phlickr/PhotoList.php';
//require_once 'Phlickr/PhotoListIterator.php';

class Model_Mapper_FlickrSearch {
    /**
     * @var Model_DbTable_FlickrCache 
     */
    protected $_tableCache;
    
    /**
     * @var Model_DbTable_FlickrImage
     */
    protected $_tableImage;
    
    /**
     * @var Form_FlickrSearch 
     */
    protected $_searchForm;
    
    /**
     * @var Phlickr_Api 
     */
    protected $_phlickr;
    
    private static $_instance;
    
    protected function __clone() {}
    protected function __construct() {
        $this->init();
    }
    
    /**
     * @return Model_Mapper_FlickrSearch 
     */
    public static function getInstance() {
        if (!self::$_instance) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }
    
    public function init() {
        
    }
    
    /**
     * @return Model_DbTable_FlickrCache 
     */
    public function getTableCache() {
        if (!$this->_tableCache) {
            $this->_tableCache = new Model_DbTable_FlickrCache();
        }
        return $this->_tableCache;
    }
    
    /**
     * @return Model_DbTable_FlickrImage 
     */
    public function getTableImage() {
        if (!$this->_tableImage) {
            $this->_tableImage = new Model_DbTable_FlickrImage();
        }
        return $this->_tableImage;
    }
    
    /**
     * @return Form_FlickrSearch 
     */
    public function getSearchForm() {
        if (!$this->_searchForm) {
            $this->_searchForm = new Form_FlickrSearch();
        }
        return $this->_searchForm;
    }
    
    public function getPhlickr() {
        if (!$this->_phlickr) {
            $this->_phlickr = new Phlickr_Api($this->getFlickrConfig()->api->key, $this->getFlickrConfig()->api->secret);
        }
        return $this->_phlickr;
    }
    
    public function getSearchResults() {
        if (!$this->getSearchForm()->getSearch()) {
            return null;
        }
    }
    
    /**
     * @return Zend_Config
     */
    public function getFlickrConfig() {
        $options = Zend_Registry::get('config');
        return $options->flickr;
    }
    
    public function getSearchCache() {
        $tmp = array();
        $search = $this->getSearchForm()->getSearch();
        $page = $this->getSearchForm()->getPage();
        $perPage = $this->getSearchForm()->getPerPage();
        $cacheTable = $this->getTableCache();
        $db = $cacheTable->getAdapter();
        $where = $db->quoteInto('search_time + ' . $this->getFlickrConfig()->cache->time . ' > ' . time() . ' AND search_term = ?', $search);

        $cacheRow = $cacheTable->fetchRow($where, 'id DESC');
        
        if ($cacheRow) {
            $imageTable = $this->getTableImage();
            $where = $db->quoteInto('page > ' .$page . ' AND per_page = ' . $perPage . ' AND flickr_cache_id = ?', $cacheRow->id);
            
            $results = $imageTable->fetchAll($where);
            foreach ($results as $photo) {
                $data = array(
                    'flickr_cache_id' => $cacheRow->id,
                    'api_key'         => $photo->getId(),
                    'title'           => $photo->getTitle(),
                    'url'             => $photo->buildImgUrl(),
                    'page'            => $page,
                    'per_page'        => $perPage,
                );
                $tmp[] = $data;
            }

        } 
        
        return $tmp;
    }
    
    public function doSearch() {
        $search = $this->getSearchForm()->getSearch();
        $page = $this->getSearchForm()->getPage();
        $perPage = $this->getSearchForm()->getPerPage();
        $tmp = array();
        
        $request = $this->getPhlickr()->createRequest(
                'flickr.photos.search',
                array(
                    'tags' => $search,
                    'tag_mode' => 'all',
                    'page' => $page,
                    'per_page' => $perPage
                )
            );
        $photolist = new Phlickr_PhotoList($request, $perPage);
        
        $iterator = new Phlickr_PhotoListIterator($photolist);
        
        try {
            
            $cacheTable = $this->getTableCache();
            $imageTable = $this->getTableImage();
            $db = $cacheTable->getAdapter();
            
            $db->beginTransaction();
            
            $cacheRow = $cacheTable->createRow(array(
                'search_term' => $search,
                'search_time' => time(),
            ));
            $cacheRow->save();
        
            foreach ($iterator->current() as $photo) {
                
                $data = array(
                    'flickr_cache_id' => $cacheRow->id,
                    'api_key'         => $photo->getId(),
                    'title'           => $photo->getTitle(),
                    'url'             => $photo->buildImgUrl(),
                    'page'            => $page,
                    'per_page'        => $perPage,
                );
                
                $row = $imageTable->createRow($data);
                $row->save();
                
                $tmp[] = $data;
            }
            $db->commit();
        } catch (Exception $e) {
            $db->rollBack();
        }
        
        return $tmp;
    }
    
    public function getSearchImage($flagCache = true) {
        $tmp = array();
        if ($flagCache) {
            $tmp = $this->getSearchCache();
        }
        if (!$tmp) {
            $tmp = $this->doSearch();
        }
        return $tmp;
    }
}