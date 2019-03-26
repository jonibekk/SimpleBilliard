<?php
App::uses('AbstractErrorType', 'Lib/Network/Response/ErrorResponseBody');

use Goalous\Enum as Enum;

class ErrorTypeValidation extends AbstractErrorType
{
    /**
     * @var string
     */
    private $field = '';

    public function __construct(string $field, string $message)
    {
        $this->message = $message;
        $this->field = $field;
    }

    private function currentLanguage(): string
    {
        $lang = Configure::read("Config.language") ?? 'en';
        switch ($lang) {
            case 'ja':
            case 'jpn':
                return 'ja';
            case 'en':
            case 'eng':
            default:
                return 'en';
        }
    }

    private function buildMessage(): string
    {
        $fieldTranslations = Configure::read("translation_validation_fields");
        $lang = $this->currentLanguage();
        $filedName = $fieldTranslations[$this->field][$lang];
        return str_replace('{{field}}', $filedName, $this->getMessage());
    }

    public function toArray():array
    {
        return [
            'type' => Enum\Network\Response\ErrorType::VALIDATION,
            'field' => $this->field,
            'message' => $this->buildMessage(),
        ];
    }
}
