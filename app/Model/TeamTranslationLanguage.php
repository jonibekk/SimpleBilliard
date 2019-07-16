<?php
App::uses('AppModel', 'Model');
App::import('Model/Entity', 'TeamTranslationLanguageEntity');

use Goalous\Enum\DataType\DataType as DataType;

class TeamTranslationLanguage extends AppModel
{
    public $modelConversionTable = [
        'team_id' => DataType::INT
    ];

    /**
     * Get all enabled translation languages of the team
     *
     * @param int $teamId
     *
     * @return TeamTranslationLanguageEntity[]
     */
    public function getLanguagesByTeam(int $teamId): array
    {
        $option = [
            'conditions' => [
                'team_id' => $teamId
            ]
        ];

        $return = $this->useType()->useEntity()->find('all', $option);
        return $return;
    }

    /**
     * Check whether the team has translation enabled
     *
     * @param int $teamId
     *
     * @return bool
     */
    public function canTranslate(int $teamId): bool
    {
        $option = [
            'conditions' => [
                'team_id' => $teamId
            ]
        ];

        return $this->find('count', $option) > 0;
    }

    /**
     * Get ids of all teams with translation feature
     *
     * @return int[]
     */
    public function getAllTeamIds(): array
    {
        $option = [
            'fields' => [
                'DISTINCT TeamTranslationLanguage.team_id'
            ]
        ];

        return Hash::extract($this->useType()->find('all', $option), '{n}.{*}.team_id') ?: [];
    }

    /**
     * Whether given translation language is supported in the team
     *
     * @param int    $teamId
     * @param string $languageCode
     *
     * @return bool
     */
    public function supportTranslationLanguage(int $teamId, string $languageCode): bool
    {
        $option = [
            'conditions' => [
                'team_id'  => $teamId,
                'language' => $languageCode
            ]
        ];

        return $this->find('count', $option) > 0;
    }
}