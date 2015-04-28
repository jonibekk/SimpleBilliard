<?

/**
 * Class RedisComponent
 *
 * @property Redis $Db
 */
class RedisComponent extends Object
{

    public $name = "Redis";
    public $Db;

    function initialize()
    {
        App::uses('ConnectionManager', 'Model');
        $this->Db = ConnectionManager::getDataSource('redis');
    }

    function startup()
    {
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

}
