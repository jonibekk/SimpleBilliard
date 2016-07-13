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
        $allowed_actions = ['ajax_verify_code'];
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
     * verify email by verify code
     * [POST] method only allowed
     * 
     * required field is:
     * $this->request->data['code']
     * 
     * return value is json encoded
     * e.g.
     * {
     * error: false,//true or false,
     * message:"something is wrong",//if error is true then message exists. if no error, blank text
     * }
     *
     * TTL is 1 hour
     * 5 failed then lockout 5mins
     * compare input field and session stored
     * DB is not updated, it will be updated in final user registration part.
     *
     * @return CakeResponse
     */
    public function ajax_verify_code()
    {
        $this->_ajaxPreProcess();
        $this->request->allowMethod('post');
        //init response values
        $res = [
            'error'       => false,
            'message'     => "",
        ];

        try {
            if (!isset($this->request->data['code'])) {
                throw new RuntimeException(__('Param is incorrect'));
            }
            $input_code = $this->request->data['code'];
            $stored_code = $this->Session->read('email_verify_code');

            if(!$stored_code){
                throw new RuntimeException(__('Invalid screen transition'));
            }
            if($input_code != $stored_code){
                throw new RuntimeException(__('verification code was wrong'));
            }
            //success!
            $this->Session->delete('email_verify_code');

        } catch (RuntimeException $e) {
            $res['error'] = true;
            $res['message'] = $e->getMessage();
        }
        return $this->_ajaxGetResponse($res);
    }
}
