<?php
App::import('Service', 'AppService');
App::uses('Xml', 'Utility');
App::uses('InvoiceHistory', 'Model');
App::uses('InvoiceHistoriesChargeHistory', 'Model');
App::uses('Invoice', 'Model');
App::uses('Team', 'Model');
App::uses('GlRedis', 'Model');

/**
 * Class InvoiceService
 * Invoice using atobarai.com
 * API documentation -> http://bit.ly/2wcSTwT
 */
class InvoiceService extends AppService
{
    const API_URL_REGISTER_ORDER = ATOBARAI_API_BASE_URL . '/api/order/rest';
    const API_URL_INQUIRE_CREDIT_STATUS = ATOBARAI_API_BASE_URL . "/api/status/rest";

    /**
     * register order for 1 team's invoice via atobarai.com
     *
     * @param int    $teamId
     * @param array  $targetChargeHistories
     * @param array  $monthlyChargeHistory
     * @param string $orderDate
     *
     * @return array response from atobarai.com
     * @throws Exception
     */
    function registerOrder(
        int $teamId,
        array $targetChargeHistories,
        array $monthlyChargeHistory,
        string $orderDate
    ): array {
        /** @var  Invoice $Invoice */
        $Invoice = ClassRegistry::init('Invoice');
        /** @var Team $Team */
        $Team = ClassRegistry::init('Team');

        try {
            $team = $Team->getById($teamId);
            $timezone = $team['timezone'];
            $invoiceInfo = $Invoice->getByTeamId($teamId);

            // calc amount total
            $addedUserAmount = $this->getAddedUserAmount($targetChargeHistories);
            $amountTotal = $addedUserAmount + $monthlyChargeHistory['total_amount'] + $monthlyChargeHistory['tax'];

            $data = [
                'O_ReceiptOrderDate'     => $orderDate,
                'O_ServicesProvidedDate' => date('Y-m-d', REQUEST_TIMESTAMP + ($timezone * HOUR)),
                // provided date cannot be specified past date
                'O_EnterpriseId'         => ATOBARAI_ENTERPRISE_ID,
                'O_SiteId'               => ATOBARAI_SITE_ID,
                'O_ApiUserId'            => ATOBARAI_API_USER_ID,
                'O_UseAmount'            => $amountTotal,
                'O_Ent_Note'             => "ご請求対象チーム名: " . $team['name'],
                'C_PostalCode'           => $invoiceInfo['company_post_code'],
                'C_UnitingAddress'       => $this->getCompanyAddress($invoiceInfo),
                'C_CorporateName'        => $invoiceInfo['company_name'],
                'C_NameKj'               => $this->getContactNameKj($invoiceInfo),
                'C_NameKn'               => $this->getContactNameKana($invoiceInfo),
                'C_CpNameKj'             => $this->getContactNameKj($invoiceInfo),
                'C_Phone'                => $invoiceInfo['contact_person_tel'],
                'C_MailAddress'          => $invoiceInfo['contact_person_email'],
                'C_EntCustId'            => $teamId,
            ];

            // for added users charge
            $itemIndex = 0;
            if (!empty($targetChargeHistories)) {
                foreach ($targetChargeHistories as $targetChargeHistory) {
                    $chargeDate = date('n/j', $targetChargeHistory['charge_datetime'] + ($timezone * HOUR));
                    $data["I_ItemNameKj_{$itemIndex}"] = "{$chargeDate} Goalous追加利用料";
                    $data["I_UnitPrice_{$itemIndex}"] = $targetChargeHistory['total_amount'] + $targetChargeHistory['tax'];
                    $data["I_ItemNum_{$itemIndex}"] = 1;
                    $itemIndex++;
                }
            }

            // for monthly charge
            $monthlyStartDate = date('n/j', strtotime($monthlyChargeHistory['monthlyStartDate']));
            $monthlyEndDate = date('n/j', strtotime($monthlyChargeHistory['monthlyEndDate']));
            $data["I_ItemNameKj_{$itemIndex}"] = "Goalous月額利用料({$monthlyStartDate} - {$monthlyEndDate})";
            $data["I_UnitPrice_{$itemIndex}"] = $monthlyChargeHistory['total_amount'] + $monthlyChargeHistory['tax'];
            $data["I_ItemNum_{$itemIndex}"] = 1;

            // request to atobarai.com
            $resAtobarai = $this->_postRequestForAtobaraiDotCom(self::API_URL_REGISTER_ORDER, $data);
            $resAtobarai = am($resAtobarai, ['requestData' => $data]);
            return $resAtobarai;
        } catch (Exception $e) {
            throw $e;
        }
    }

    /**
     * Check credit status at Atobarai.com
     *
     * @param string $orderCode
     *
     * @return array
     */
    public function inquireCreditStatus(string $orderCode): array
    {
        $data = [
            'EnterpriseId' => ATOBARAI_ENTERPRISE_ID,
            'ApiUserId'    => ATOBARAI_API_USER_ID,
            'OrderId[]'    => $orderCode,
        ];

        $resAtobarai = $this->_postRequestForAtobaraiDotCom(self::API_URL_INQUIRE_CREDIT_STATUS, $data);
        return $resAtobarai;
    }

    /**
     * @param string $requestUrl
     * @param array  $data key value array
     *
     * @return array response is converted from xml to array
     */
    private function _postRequestForAtobaraiDotCom(string $requestUrl, array $data): array
    {
        $client = $this->getHttpClient();
        $response = $client->post($requestUrl, [
            'http_errors' => 'false',
            'headers' => [
                'Content-Type: application/x-www-form-urlencoded',
                'Content-Length: ' . http_build_query($data),
            ],
            'form_params' => $data,
        ]);
        if (200 !== $response->getStatusCode()) {
            throw new RuntimeException(sprintf('atobarai.com api error: %s', AppUtil::jsonOneLine([
                'status' => $response->getStatusCode(),
            ])));
        }

        $xmlArray = Xml::toArray(Xml::build($response->getBody()->getContents()));
        $ret = Hash::extract($xmlArray, 'response');
        return $ret;
    }

    /**
     * @param int    $teamId
     * @param string $date
     *
     * @return bool
     */
    function isSentInvoice(int $teamId, string $date): bool
    {
        /** @var InvoiceHistory $InvoiceHistory */
        $InvoiceHistory = ClassRegistry::init('InvoiceHistory');
        return (bool)$InvoiceHistory->getByOrderDate($teamId, $date);

    }

    /**
     * TODO.Payment: add space between field
     * @param array $invoice
     *
     * @return string
     */
    function getCompanyAddress(array $invoice): string
    {
        return $invoice['company_region'] . $invoice['company_city'] . $invoice['company_street'];
    }

    /**
     * TODO.Payment: add space between field
     * @param array $invoice
     *
     * @return string
     */
    function getContactNameKj(array $invoice): string
    {
        return $invoice['contact_person_last_name'] . $invoice['contact_person_first_name'];
    }

    /**
     * TODO.Payment: add space between field
     * @param array $invoice
     *
     * @return string
     */
    function getContactNameKana(array $invoice): string
    {
        if (!$invoice['contact_person_last_name_kana'] || !$invoice['contact_person_first_name_kana']) {
            return "";
        }
        return $invoice['contact_person_last_name_kana'] . $invoice['contact_person_first_name_kana'];
    }

    /**
     * @param array $targetChargeHistories
     *
     * @return int
     */
    function getAddedUserAmount(array $targetChargeHistories): int
    {
        $addedUserAmount = 0;
        foreach ($targetChargeHistories as $history) {
            $addedUserAmount += $history['total_amount'] + $history['tax'];
        }
        return $addedUserAmount;
    }

    /**
     * Update invoice and invoice history tables with credit status
     *
     * @param int $invoiceHistoryId
     * @param int $creditStatus
     *
     * @return bool
     */
    public function updateCreditStatus(int $invoiceHistoryId, int $creditStatus): bool
    {
        /** @var Invoice $Invoice */
        $Invoice = ClassRegistry::init('Invoice');
        /** @var  InvoiceHistory $InvoiceHistory */
        $InvoiceHistory = ClassRegistry::init('InvoiceHistory');
        /** @var GlRedis $GlRedis */
        $GlRedis = ClassRegistry::init("GlRedis");

        // Get invoice history
        $invoiceHistory = $InvoiceHistory->getById($invoiceHistoryId);
        if (empty($invoiceHistory)) {
            return false;
        }

        $teamId = $invoiceHistory['team_id'];

        $invoiceHistory['order_status'] = $creditStatus;
        $invoice = $Invoice->getByTeamId($teamId);
        if (empty($invoice)) {
            CakeLog::error("Invoice not found for invoice history. TeamId: " . $teamId);
            return false;
        }
        $invoice['credit_status'] = $creditStatus;

        try {
            $InvoiceHistory->begin();

            if (!$InvoiceHistory->save($invoiceHistory)) {
                throw new Exception(sprintf("Failed to save Invoice history. data: %s, validationErrors: %s",
                    AppUtil::varExportOneLine($invoiceHistory),
                    AppUtil::varExportOneLine($InvoiceHistory->validationErrors)));
            }

            $Invoice->clear();
            $Invoice->id = $invoice['id'];
            if (!$Invoice->save(['credit_status' => $creditStatus], false)) {
                throw new Exception(sprintf("Failed to save Invoice order status. data: %s, validationErrors: %s",
                    AppUtil::varExportOneLine($invoice),
                    AppUtil::varExportOneLine($Invoice->validationErrors)));
            }

            $InvoiceHistory->commit();
        } catch (Exception $e) {
            $InvoiceHistory->rollback();
            CakeLog::emergency(sprintf("[%s]%s", __METHOD__, $e->getMessage()));
            CakeLog::emergency($e->getTraceAsString());
            return false;
        }

        $GlRedis->dellKeys("*current_team:team:{$teamId}");

        return true;
    }

}
