<?php

class IndexController extends Zend_Controller_Action {

    /**
     * @var Form_FlickrSearch 
     */
    private $_form;
    
    /**
     * @var Model_Mapper_FlickrSearch 
     */
    private $_mapper;
    
    public function init() {
        $this->_mapper = Model_Mapper_FlickrSearch::getInstance();
        $this->_form = $this->_mapper->getSearchForm(); 
        
    }

    public function indexAction() {
        /* Initialize action controller here */

        if ($this->getRequest()->isGet()) {
            $data = $this->_getAllParams();
            if ($this->_form->isValid($data)) {
                $this->view->data = $this->_mapper->getSearchImage();
            }
        }
        
        $this->view->form = $this->_form;
    }
}

