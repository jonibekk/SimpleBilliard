<?php
App::uses('AppController', 'Controller');

/**
 * Emails Controller
 *
 * @property Email              $Email
 * @property PaginatorComponent $Paginator
 * @property SessionComponent   $Session
 */
class EmailsController extends AppController
{

    public function beforeFilter()
    {
        parent::beforeFilter();
        $this->Auth->allow('contact_send', 'contact_validate');
    }

    /**
     * delete method
     *
     * @throws NotFoundException
     *
     * @param string $id
     *
     * @return void
     */
    public function delete($id = null)
    {
        $this->Email->id = $id;
        if (!$this->Email->exists()) {
            throw new NotFoundException(__('gl', "このメールアドレスは存在しません。"));
        }
        if (!$this->Email->isOwner($this->Auth->user('id'))) {
            throw new NotFoundException(__('gl', "このメールアドレスはあなたのものではありません。"));
        }

        $this->request->allowMethod('post', 'delete');
        $this->Email->delete();
        $this->Pnotify->outSuccess(__d('gl', "メールアドレス変更をキャンセルしました。"));
        /** @noinspection PhpInconsistentReturnPointsInspection */
        /** @noinspection PhpVoidFunctionResultUsedInspection */
        return $this->redirect($this->referer());
    }

    public function contact_validate()
    {
        $this->request->allowMethod('post');
        $this->Email->validate = $this->Email->contact_validate;
        $this->Email->set($this->request->data);
        $data = Hash::extract($this->request->data, 'Email');
        if (!$this->Email->validates()) {
            $this->Pnotify->outError(__d('validate', '問題が発生したため、処理が完了しませんでした。'));
            return $this->redirect($this->referer());
        }

        $this->Session->write('contact_form_data', $data);
        return $this->redirect('/contact_confirm');
    }

    public function contact_send()
    {

        $data = $this->Session->read('contact_form_data');
        if (empty($data)) {
            $this->Pnotify->outError(__d('validate', '問題が発生したため、処理が完了しませんでした。'));
            return $this->redirect($this->referer());
        }
        $this->Session->delete('contact_form_data');
        //メール送信処理
        App::uses('CakeEmail', 'Network/Email');
        if (ENV_NAME === "local") {
            $config = 'default';
        }
        else {
            $config = 'amazon';
        }

        // 送信処理
        $email = new CakeEmail($config);
        $email
            ->template('contact', 'default')
            ->viewVars(['data' => $data])
            ->emailFormat('text')
            ->to([$data['email'] => $data['email']])
            //TODO SES側の設定を行う必要あり
//            ->from(['Goalous返信専用' => 'noreply@goalous.com'])
//            ->bcc(['contact@goalous.com' => 'contact@goalous.com'])
            ->subject(__d('gl', 'お問い合わせいただきありがとうございます。'))
            ->send();

        return $this->redirect('/contact_thanks');
    }

}
