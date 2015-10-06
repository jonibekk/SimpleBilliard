<?php

/**
 * redisからキーを指定して削除するshell
 * Console/cake dell_redis_keys -p "key pattern"
 *
 * @property GlRedis $GlRedis
 */
class DellRedisKeysShell extends AppShell
{
    public $uses = array(
        'GlRedis',
    );

    public function startup()
    {
        parent::startup();
    }

    public function getOptionParser()
    {
        $parser = parent::getOptionParser();
        $options = [
            'pattern' => ['short' => 'p', 'help' => 'キーのパターン(?*[]の指定が可能)', 'required' => true,],
            'force'   => ['short' => 'f', 'help' => '強制的に削除', 'required' => false,],
        ];
        $parser->addOptions($options);
        return $parser;
    }

    public function main()
    {
        //parameter
        $pattern = $this->params['pattern'];
        if (!array_key_exists('force', $this->params)) {
            $key_count = $this->GlRedis->getKeyCount($pattern);
            $ret_prompt = $this->in("{$key_count} keys will be deleted. would you like to delete them?[y/N]");
            if (!in_array(strtolower($ret_prompt), ['y', 'yes'])) {
                return $this->out("Bye!");
            }
        }
        try {
            $deleted_count = $this->GlRedis->dellKeys($pattern);
        } catch (RuntimeException $e) {
            return $this->out($e->getMessage());
        }
        return $this->out("$deleted_count keys were deleted!");
    }

}
