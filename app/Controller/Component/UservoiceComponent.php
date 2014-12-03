<?

class UservoiceComponent extends Object
{

    public $name = "Uservoice";

    public $user_data = [
        "guid"            => "",
        "expires"         => "",
        "display_name"    => "",
        "email"           => "",
        "url"             => "",
        "avatar_url"      => "",
        "updates"         => true,
        "comment_updates" => true,
    ];

    public $Controller;

    function initialize()
    {
    }

    function startup(&$controller)
    {
        $this->Controller = $controller;
        //トークンの有効期限は１ヶ月
        $this->user_data['expires'] = date('Y-m-d H:i:s', strtotime("+1 month"));
    }

    function beforeRender()
    {
    }

    function shutdown()
    {
    }

    function beforeRedirect()
    {
    }

    public function getToken()
    {
        $user = $this->Controller->Session->read('Auth');
        $data = array(
            'guid'         => $user['User']['id'],
            'display_name' => $user['User']['display_username'],
        );
        if (isset($user['PrimaryEmail']['email'])) {
            $data['email'] = $user['PrimaryEmail']['email'];
        }
        //アバター画像がある場合はセット
        if (isset($user['ProfileImage']['id'])) {
            App::uses('View', 'View');
            //HtmlHelperのインスタンス生成
            App::uses('HtmlHelper', 'View/Helper');
            $html = new HtmlHelper(new View($this->Controller));
            //UploadHelperのインスタンス生成
            App::uses('UploadHelper', 'View/Helper');
            //TODO 画像を変更した後はなぜかUploadHelperが使えない。一旦、クラスが見つからない場合はアバターを設定しないように変更。
            if (class_exists('UploadHelper')) {
                $upload = new UploadHelper(new View($this->Controller));
                $url = $upload->uploadUrl($user, 'User.photo', ['style' => 'small']);
                if (!ENV) {
                    //ローカルの場合
                    $url = FULL_BASE_URL . $url;
                }
                $data['avatar_url'] = $url;
            }
        }
        $this->user_data = array_merge($this->user_data, $data);
        $account_key = USERVOICE_SUBDOMAIN;
        $api_key = USERVOICE_API_KEY;

        $salted = $api_key . $account_key;
        $hash = hash('sha1', $salted, true);
        $saltedHash = substr($hash, 0, 16);
        $iv = "OpenSSL for Ruby";
        $data = json_encode($this->user_data);

        // double XOR first block
        for ($i = 0; $i < 16; $i++) {
            $data[$i] = $data[$i] ^ $iv[$i];
        }

        $pad = 16 - (strlen($data) % 16);
        $data = $data . str_repeat(chr($pad), $pad);

        $cipher = mcrypt_module_open(MCRYPT_RIJNDAEL_128, '', 'cbc', '');
        mcrypt_generic_init($cipher, $saltedHash, $iv);
        $encryptedData = mcrypt_generic($cipher, $data);
        mcrypt_generic_deinit($cipher);

        $encryptedData = urlencode(base64_encode($encryptedData));
        return $encryptedData;
    }

}
