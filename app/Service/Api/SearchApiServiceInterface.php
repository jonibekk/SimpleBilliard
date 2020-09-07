<?php

App::uses('SearchApiRequestDto', 'Model/Dto/Search');
App::uses('SearchApiResponseDto', 'Model/Dto/Search');

/**
 * Interface SearchApiServiceInterface
 */
interface SearchApiServiceInterface {
    /**
     * @param SearchApiRequestDto $searchApiRequestDto
     * @param SearchApiResponseDto $searchApiResponseDto
     */
    public function search(
        SearchApiRequestDto $searchApiRequestDto,
        SearchApiResponseDto $searchApiResponseDto
    ): void;
}
