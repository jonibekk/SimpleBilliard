<?php
App::uses('AppController', 'Controller');

/**
 * Signup Controller
 *
 * @property Signup $Signup
 */
class SignupController extends AppController
{
    /**
     * beforeFilter callback
     *
     * @return void
     */
    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->layout = LAYOUT_ONE_COLUMN;
        $allowed_actions = ['ajax_validation_fields'];
        //ajaxのPOSTではフォーム改ざんチェック用のハッシュ生成ができない為、ここで改ざんチェックを除外指定
        if (in_array($this->request->params['action'], $allowed_actions)) {
            $this->Security->validatePost = false;
        }
    }

    public function auth()
    {
    }

    public function user()
    {
    }

    public function password()
    {
    }

    public function team()
    {
    }

    public function term()
    {
    }

    /**
     * validation fields
     * e.g.
     * $this->request->data['User']['first_name']
     *
     * @return CakeResponse|null
     */
    public function ajax_validation_fields()
    {
        $this->_ajaxPreProcess();
        $this->request->allowMethod('post');
        //init response values
        $res = [
            'error'   => false,
            'message' => "",
        ];
        //TODO WIP
        $white_list = [
            'User'  => [
                'first_name',
                'last_name'
            ],
            'Email' => [
                'email'
            ]
        ];
        try {
            if (empty($this->request->data)) {
                throw new RuntimeException(__('No Datas'));
            }
            //white list checking

        } catch (RuntimeException $e) {
            $res['error'] = true;
            $res['message'] = $e->getMessage();
        }
        return $this->_ajaxGetResponse($res);
    }
}
