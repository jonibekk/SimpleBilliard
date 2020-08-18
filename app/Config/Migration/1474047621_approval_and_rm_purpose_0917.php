<?php

class ApprovalAndRmPurpose0917 extends CakeMigration
{
    /**
     * Migration description
     *
     * @var string
     */
    public $description = 'approval_and_rm_purpose_0917';

    /**
     * Actions to be performed
     *
     * @var array $migration
     */
    public $migration = array(
        'up' => array(
            'create_field' => array(
                'collaborators' => array(
                    'is_wish_approval' => array('type' => 'boolean', 'null' => false, 'default' => '1', 'comment' => '認定対象希望フラグ', 'after' => 'approval_status'),
                    'is_target_evaluation' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '評価対象フラグ', 'after' => 'is_wish_approval'),
                ),
            ),
            'alter_field' => array(
                'collaborators' => array(
                    'approval_status' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => '認定ステータス(0: 新規,1: 再認定依頼中,2: コーチが認定処理済み,3: コーチーが取り下げた)'),
                ),
                'labels' => array(
                    'name' => array('type' => 'string', 'null' => false, 'default' => null, 'length' => 128, 'collate' => 'utf8mb4_general_ci', 'comment' => 'ラベル', 'charset' => 'utf8mb4'),
                ),
            ),
            'drop_table' => array(
                'purposes'
            ),
        ),
        'down' => array(
            'drop_field' => array(
                'collaborators' => array('is_wish_approval', 'is_target_evaluation'),
            ),
            'alter_field' => array(
                'collaborators' => array(
                    'approval_status' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => false, 'comment' => '認定ステータス(0 = 処理前,1 = 承認, 2 = 保留,3 = 修正依頼, 4 = 差し戻し)'),
                ),
                'labels' => array(
                    'name' => array('type' => 'string', 'null' => false, 'length' => 128, 'collate' => 'utf8mb4_general_ci', 'comment' => 'ラベル', 'charset' => 'utf8mb4'),
                ),
            ),
            'create_table' => array(
                'purposes' => array(
                    'id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'primary', 'comment' => '目的ID'),
                    'name' => array('type' => 'text', 'null' => false, 'default' => null, 'collate' => 'utf8mb4_general_ci', 'comment' => '目的', 'charset' => 'utf8mb4'),
                    'user_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '作成者ユーザID(belongsToでUserモデルに関連)'),
                    'team_id' => array('type' => 'biginteger', 'null' => false, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => 'チームID(belongsToでTeamモデルに関連)'),
                    'goal_count' => array('type' => 'integer', 'null' => false, 'default' => '0', 'unsigned' => true, 'comment' => 'ゴール数'),
                    'del_flg' => array('type' => 'boolean', 'null' => false, 'default' => '0', 'comment' => '削除フラグ'),
                    'deleted' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '削除した日付時刻'),
                    'created' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'key' => 'index', 'comment' => '追加した日付時刻'),
                    'modified' => array('type' => 'integer', 'null' => true, 'default' => null, 'unsigned' => true, 'comment' => '更新した日付時刻'),
                    'indexes' => array(
                        'PRIMARY' => array('column' => 'id', 'unique' => 1),
                        'user_id' => array('column' => 'user_id', 'unique' => 0),
                        'team_id' => array('column' => 'team_id', 'unique' => 0),
                        'created' => array('column' => 'created', 'unique' => 0),
                    ),
                    'tableParameters' => array('charset' => 'utf8mb4', 'collate' => 'utf8mb4_general_ci', 'engine' => 'InnoDB'),
                ),
            ),
        ),
    );


    /**
     * Before migration callback
     *
     * @param string $direction Direction of migration process (up or down)
     *
     * @return bool Should process continue
     */
    public function before($direction)
    {
        return true;
    }

    /**
     * After migration callback
     *
     * @param string $direction Direction of migration process (up or down)
     *
     * @return bool Should process continue
     */
    public function after($direction)
    {
        if ($direction == 'up') {
            //データ移行
            /**
             * ## 認定ステータス&target_evaluation_flg
             * - (旧) 0 = 処理前
             *  - (新) 0: 新規, target_evaluation_flg=0
             * - (旧) 1 = 承認
             *  - (新) 2: コーチが認定処理済み, target_evaluation_flg=1
             * - (旧) 2 = 保留(否認)
             *  - (新) 2: [変更なし]コーチが認定処理済み, target_evaluation_flg=0
             * - (旧) 3 = 修正依頼
             *  - (新) 2: コーチが認定処理済み, target_evaluation_flg=0
             * - (旧) 4 = 差し戻し
             *  - (新) 3: コーチーが取り下げた, target_evaluation_flg=0
             */

            /** @var Collaborator $Collaborator */
            $Collaborator = ClassRegistry::init('Collaborator');
            $Collaborator->unbindModel([
                'belongsTo' => [
                    'Team',
                    'Goal',
                    'User',
                ]
            ]);
            //approval_status = 1のデータをtarget_evaluation_flg = 1 にする
            $Collaborator->updateAll(['Collaborator.is_target_evaluation' => true],
                ['Collaborator.approval_status' => 1, 'Collaborator.priority >' => 0]);

            //認定ステータス変更
            $old1 = $Collaborator->find('list', ['conditions' => ['approval_status' => 1]]);
            $old3 = $Collaborator->find('list', ['conditions' => ['approval_status' => 3]]);
            $old4 = $Collaborator->find('list', ['conditions' => ['approval_status' => 4]]);
            //1 --> 2
            $Collaborator->updateAll(['Collaborator.approval_status' => 2], ['Collaborator.id' => $old1]);
            //3 --> 2
            $Collaborator->updateAll(['Collaborator.approval_status' => 2], ['Collaborator.id' => $old3]);
            //4 --> 3
            $Collaborator->updateAll(['Collaborator.approval_status' => 3], ['Collaborator.id' => $old4]);
        }

        return true;
    }
}