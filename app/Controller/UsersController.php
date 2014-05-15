<?php
App::uses('AppController', 'Controller');

/**
 * Users Controller
 *
 * @property User $User
 */
class UsersController extends AppController
{
    public function beforeFilter()
    {
        parent::beforeFilter();
    }

    /**
     * index method
     *
     * @return $this->render()
     */
    public function index()
    {
        $this->set('users', $this->Paginator->paginate());
        User::$GENDER_TYPE;
        return $this->render();
    }

    /**
     * view method
     *
     * @throws NotFoundException
     *
     * @param string $id
     *
     * @return $this->render()
     */
    public function view($id = null)
    {
        if (!$this->User->exists($id)) {
            throw new NotFoundException(__('Invalid user'));
        }
        $options = ['conditions' => ['User.' . $this->User->primaryKey => $id]];
        $this->set('user', $this->User->find('first', $options));
        return $this->render();
    }

    /**
     * add method
     *
     * @return mixed
     */
    public function add()
    {
        if ($this->request->is('post')) {
            $this->User->create();
            if ($this->User->save($this->request->data)) {
                $this->Session->setFlash(__('The user has been saved.'));
                /** @noinspection PhpVoidFunctionResultUsedInspection */
                return $this->redirect(['action' => 'index']);
            }
            else {
                $this->Session->setFlash(__('The user could not be saved. Please, try again.'));
            }
        }
        return $this->render();
    }

    /**
     * edit method
     *
     * @throws NotFoundException
     *
     * @param string $id
     *
     * @return mixed
     */
    public function edit($id = null)
    {
        if (!$this->User->exists($id)) {
            throw new NotFoundException(__('Invalid user'));
        }
        if ($this->request->is(['post', 'put'])) {
            if ($this->User->save($this->request->data)) {
                $this->Session->setFlash(__('The user has been saved.'));
                /** @noinspection PhpVoidFunctionResultUsedInspection */
                return $this->redirect(['action' => 'index']);
            }
            else {
                $this->Session->setFlash(__('The user could not be saved. Please, try again.'));
            }
        }
        else {
            $options = ['conditions' => ['User.' . $this->User->primaryKey => $id]];
            $this->request->data = $this->User->find('first', $options);
        }
        return $this->render();
    }

    /**
     * delete method
     *
     * @throws NotFoundException
     *
     * @param string $id
     *
     * @return mixed
     */
    public function delete($id = null)
    {
        $this->User->id = $id;
        if (!$this->User->exists()) {
            throw new NotFoundException(__('Invalid user'));
        }
        $this->request->onlyAllow('post', 'delete');
        if ($this->User->delete()) {
            $this->Session->setFlash(__('The user has been deleted.'));
        }
        else {
            $this->Session->setFlash(__('The user could not be deleted. Please, try again.'));
        }
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        return $this->redirect(['action' => 'index']);
    }
}
