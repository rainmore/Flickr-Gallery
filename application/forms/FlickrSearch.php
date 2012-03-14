<?php

class Form_FlickrSearch extends Zend_Form {
    public function init() {
        // search input
        $searchInput = new Zend_Form_Element_Text('search');
        $searchInput->setLabel('Search Key Word');
        $searchInput->setRequired(true);
        $searchInput->addValidator('NotEmpty', true);
        $this->addElement($searchInput);
        
        // pageSize 
        $pageSize = $this->_getHiddenElement('per_page');
        $pageSize->setValue(self::getDefaultFlickrPageSize());
        $this->addElement($pageSize);
        
        // page
        $page = $this->_getHiddenElement('page');
        $page->setValue(1);
        $this->addElement($page);
        
        // search button
        $searchButton = new Zend_Form_Element_Submit('submit');
        $searchButton->setLabel('Search');
        
        $this->addElement($searchButton);
        
        // reset button
        $resetButton = new Zend_Form_Element_Reset('reset');
        $resetButton->setLabel('Reset');
        $this->addElement($resetButton);
        
        $this->setMethod(Zend_Form::METHOD_GET);
        $this->setAttrib('id', 'search-form');
    }
    
    /**
     * @param string $name
     * @return \Zend_Form_Element_Hidden 
     */
    protected function _getHiddenElement($name) {
        $hidden = new Zend_Form_Element_Hidden($name);
        $hidden->setDecorators(array('ViewHelper'));
        return $hidden;
    }
    
    public static function getDefaultFlickrPageSize() {
        $options = Zend_Registry::get('config');
        return $options->flickr->paginator->pageSize;
    }
    
    public function getPerPage() {
        $data = (int) $this->getElement('per_page')->getValue();
        if ($data <= 0) {
            $data = self::getDefaultFlickrPageSize();
        }
        return $data;
    }
    
    public function getPage() {
        $data = (int) $this->getElement('page')->getValue();
        if ($data <= 0) {
            $data = 1;
        }
        return $data;
    }
    
    public function getSearch() {
        return htmlentities($this->getElement('search')->getValue());
    }
}