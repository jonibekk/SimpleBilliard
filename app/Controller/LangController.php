<?
App::uses('AppController', 'Controller');

/**
 * Created by PhpStorm.
 * User: daikihirakata
 * Date: 2014/05/20
 * Time: 0:06
 */
class LangController extends AppController
{
    /**
     * This controller does not use a model
     *
     * @var array
     */
    public $uses = [];

    public function change($lang)
    {
        $lang_list = $this->Lang->getAvailLangList();
        if (array_key_exists($lang, $lang_list)) {
            $this->Cookie->write('language', $lang);
            if ($lang == "eng") {
                $this->redirect('/');
            }
            else {
                $this->redirect("/" . $lang);
            }
        }
        else {
            throw new NotFoundException(__d('gl', "無効なページです。"));
        }

    }

    public function beforeFilter()
    {
        parent::beforeFilter();
    }

}
