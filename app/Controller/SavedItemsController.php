<?php
App::uses('AppController', 'Controller');

/**
 * SavedItems Controller
 */
class SavedItemsController extends AppController
{
    /**
     * list page
     */
    public function index()
    {
        return $this->render("index");
    }

    /**
     * detail pages
     */
    public function detail()
    {
        return $this->render("index");
    }
}
