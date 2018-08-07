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
    public function getDefaultValidationRule(): array
    {
        $rules = [
            "body" => [validator::notEmpty()::max(10000)],
            "type" => [validator::intType()::between(Post::TYPE_NORMAL, Post::TYPE_MESSAGE)],
        ];
        return $rules;
    }

    public function getPostEditValidationRule(): array
    {
        $rules = [
            "body" => [validator::notEmpty()::max(10000)],
        ];
        return $rules;
    }

    public function getCirclePostValidationRule(): array
    {
        $rules = [
            "circle_id" => [validator::intType()]
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

}