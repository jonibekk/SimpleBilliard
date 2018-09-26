<?php
App::uses('BaseApiController', 'Controller/Api');
App::uses('Team', 'Model');
class E2eController extends BaseApiController
{

    /**
     * For only e2e test
     * Reset db data
     *
     * @ignoreRestriction
     * @skipAuthentication
     */
    public function post_reset_data()
    {

        if (ENV_NAME !== 'e2e') {
            return ErrorResponse::notFound()->getResponse();
        }
        // Any model is ok, creating model instance is to just get db config
        /** @var Team $Team */
        $Team = ClassRegistry::init('Team');
        $dbConfig = $Team->getDataSource()->config;
        $mysqlCmdBase = "mysql -h {$dbConfig['host']} -u {$dbConfig['login']} -p{$dbConfig['password']} -D {$dbConfig['database']}";
        exec($mysqlCmdBase." < ". ROOT. "/etc/e2e/truncate_all_tbls.sql");
        exec($mysqlCmdBase." < ". ROOT. "/etc/e2e/dump_only_insert.sql");
        return ApiResponse::ok()->getResponse();
    }

}