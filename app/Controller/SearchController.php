<?php
App::uses('AppController', 'Controller');

class SearchController extends AppController
{
    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    /**
     * Full text search page
     */
    public function index()
    {
        return $this->render("index");
    }
}