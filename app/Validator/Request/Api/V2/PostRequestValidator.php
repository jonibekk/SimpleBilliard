<?php
App::uses('BaseValidator', 'Validator');

/**
 * Created by PhpStorm.
 * User: StephenRaharja
 * Date: 2018/06/18
 * Time: 15:44
 */

use Respect\Validation\Validator as validator;

class PostRequestValidator extends BaseValidator
{
    private function getResourceValidator() {
        return [
            // Could not use max(10) on array
            validator::arrayType()->length(null, 10),
            "optional"
        ];
    }
    private function getBodyValidator() {
        return [
            validator::stringType()->length(1, 10000)->notEmpty()
        ];
    }
    private function getOgpValidator() {
        return [
            validator::optional(validator::arrayType()),
            "optional"
        ];
    }

    public function getDefaultValidationRule(): array
    {
        $rules = [
            "body"      => $this->getBodyValidator(),
            "type"      => [validator::digit()::between(Post::TYPE_NORMAL, Post::TYPE_MESSAGE)],
            "site_info" => $this->getOgpValidator(),
            "file_ids"  => $this->getResourceValidator(),
        ];
        return $rules;
    }

    public function getPostEditValidationRule(): array
    {
        $rules = [
            "body" => $this->getBodyValidator(),
        ];
        return $rules;
    }

    public function getCirclePostValidationRule(): array
    {
        $rules = [
            "circle_id" => [validator::digit()]
        ];

        return $rules;
    }

    public function getPostReadValidationRule(): array
    {
        $rules = [
            "posts_ids" => [validator::arrayType()->length(null, 1000)]
        ];
        return $rules;
    }

    /**
     * Validation rules for both adding and removing like from a post
     *
     * @return array
     */
    public function getPostLikeValidationRule(): array
    {
        $rules = [
            "post_id" => [validator::intType()]
        ];

        return $rules;
    }

    /**
     * Validation rules for the file IDs in file upload
     * Workaround for a bug in the library
     *
     * @return array
     */
    public function getFileUploadValidationRule(): array
    {
        $rules = [
            "resources" => $this->getResourceValidator(),
        ];

        return $rules;
    }

    /**
     * Validation rules for uploading file during post edit
     *
     * @return array
     */
    public function getPostEditFileValidationRule(): array
    {
        $rules = [
            "resources" => $this->getResourceValidator(),
        ];

        return $rules;
    }

    /**
     * Validation rules for posting comments into a post
     *
     * @return array
     */
    public function getPostCommentValidationRule(): array
    {
        $rules = [
            "body"      => [validator::notEmpty()::length(1, 5000)],
            "file_ids"  => $this->getResourceValidator(),
            "site_info" => $this->getOgpValidator(),
        ];

        return $rules;
    }

    public static function createDefaultPostValidator(): self
    {
        $self = new self();
        $self->addRule($self->getDefaultValidationRule());
        return $self;
    }

    public static function createPostEditValidator(): self
    {
        $self = new self();
        $self->addRule($self->getPostEditValidationRule(), true);
        return $self;
    }

    public static function createCirclePostValidator(): self
    {
        $self = new self();
        $self->addRule($self->getCirclePostValidationRule());
        return $self;
    }

    public static function createPostLikeValidator(): self
    {
        $self = new self();
        $self->addRule($self->getPostLikeValidationRule());
        return $self;
    }

    public static function createPostReadValidator(): self
    {
        $self = new self();
        $self->addRule($self->getPostReadValidationRule(), true);
        return $self;
    }

    public static function createFileUploadValidator(): self
    {
        $self = new self();
        $self->addRule($self->getFileUploadValidationRule(), true);
        return $self;
    }

    public static function createPostCommentValidator(): self
    {
        $self = new self();
        $self->addRule($self->getPostCommentValidationRule(), true);
        return $self;
    }

    public static function createPostEditFileValidator(): self
    {
        $self = new self();
        $self->addRule($self->getPostEditFileValidationRule(), true);
        return $self;
    }
}
