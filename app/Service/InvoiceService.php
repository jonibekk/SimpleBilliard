<?php
App::import('Service', 'AppService');
App::uses('Xml', 'Utility');
App::uses('InvoiceHistory', 'Model');
App::uses('InvoiceHistoriesChargeHistory', 'Model');
App::uses('Invoice', 'Model');
App::uses('Team', 'Model');

/**
 * Class InvoiceService
 * Invoice using atobarai.com
 * API documentation -> http://bit.ly/2wcSTwT
 */
class InvoiceService extends AppService
{
    const API_URL_REGISTER_ORDER = ATOBARAI_API_BASE_URL . '/api/order/rest';
    const API_URL_INQUIRY_CREDIT_STATUS = ATOBARAI_API_BASE_URL . "/api/status/rest";

    /**
     * register order for 1 team's invoice
     *
     * @param int    $teamId
     * @param array  $chargeHistories
     * @param string $orderDate
     *
     * @return bool
     */
    function registerOrder(int $teamId, array $chargeHistories, string $orderDate)
    {
        /** @var  Invoice $Invoice */
        $Invoice = ClassRegistry::init('Invoice');
        /** @var  InvoiceHistory $InvoiceHistory */
        $InvoiceHistory = ClassRegistry::init('InvoiceHistory');
        /** @var  InvoiceHistoriesChargeHistory $InvoiceHistoriesChargeHistory */
        $InvoiceHistoriesChargeHistory = ClassRegistry::init('InvoiceHistoriesChargeHistory');
        /** @var Team $Team */
        $Team = ClassRegistry::init('Team');
        $team = $Team->getById($teamId);
        $invoiceInfo = $Invoice->getByTeamId($teamId);
        $companyAddress = $invoiceInfo['company_region'] . $invoiceInfo['company_city'] . $invoiceInfo['company_street'];
        $contactName = $invoiceInfo['contact_person_last_name'] . $invoiceInfo['contact_person_first_name'];
        $amountTotal = 0;
        foreach ($chargeHistories as $history) {
            $amountTotal += $history['total_amount'] + $history['tax'];
        }

        // TODO: なぜか会社名が反映されない
        $data = [
            'O_ReceiptOrderDate'     => $orderDate,
            'O_ServicesProvidedDate' => $orderDate,
            'O_EnterpriseId'         => ATOBARAI_ENTERPRISE_ID,
            'O_SiteId'               => ATOBARAI_SITE_ID,
            'O_ApiUserId'            => ATOBARAI_API_USER_ID,
            'O_UseAmount'            => $amountTotal,
            'O_Ent_Note'             => "ご請求対象チーム名: " . $team['name'],
            'C_PostalCode'           => $invoiceInfo['company_post_code'],
            'C_UnitingAddress'       => $companyAddress,
            'C_CorporateName'        => $invoiceInfo['company_name'],
            'C_NameKj'               => $contactName,
            'C_Phone'                => $invoiceInfo['contact_person_tel'],
            'C_MailAddress'          => $invoiceInfo['contact_person_email'],
            'C_EntCustId'            => $teamId,
        ];

        $itemIndex = 1;
        foreach ($chargeHistories as $history) {
            // TODO: 商品名を正しくする
            $data["I_ItemNameKj_{$itemIndex}"] = "Goalous利用料金";
            $data["I_UnitPrice_{$itemIndex}"] = $history['total_amount'] + $history['tax'];
            $data["I_ItemNum_{$itemIndex}"] = 1;
            $itemIndex++;
        }

        $resAtobarai = $this->_postRequestForAtobaraiDotCom(self::API_URL_REGISTER_ORDER, $data);
        if ($resAtobarai['status'] == 'error') {
            $this->log(sprintf("Request to atobarai.com was failed. errorMsg: %s, teamId: %s, chargeHistories: %s, requestData: %s",
                AppUtil::varExportOneLine($resAtobarai['messages']),
                $teamId,
                AppUtil::varExportOneLine($chargeHistories),
                AppUtil::varExportOneLine($data)
            ));
            return false;
        }
        // TODO: saving invoice_history data to DB
        $invoiceHistory = $InvoiceHistory->save([
            'team_id'           => $teamId,
            'order_date'        => $orderDate,
            'system_order_code' => $resAtobarai['systemOrderId'],
            'order_status'      => $resAtobarai['orderStatus']['@cd'],
        ]);
        // TODO: invoices and chargeHistories
        $invoiceHistoryId = $InvoiceHistory->getLastInsertID();
        $invoiceHistoriesChargeHistories = [];
        foreach ($chargeHistories as $history) {
            $invoiceHistoriesChargeHistories[] = [
                'invoice_history_id' => $invoiceHistoryId,
                'charge_history_id'  => $history['id'],
            ];
        }
        $InvoiceHistoriesChargeHistory->saveAll($invoiceHistoriesChargeHistories);

    }

    /**
     * @param string $requestUrl
     * @param array  $data key value array
     *
     * @return array response is converted from xml to array
     */
    private function _postRequestForAtobaraiDotCom(string $requestUrl, array $data): array
    {
        $data = http_build_query($data);

        // header
        $header = [
            "Content-Type: application/x-www-form-urlencoded",
            "Content-Length: " . strlen($data)
        ];

        $context = [
            "http" => [
                "method"  => "POST",
                "header"  => implode("\r\n", $header),
                "content" => $data
            ]
        ];
        $ret = file_get_contents($requestUrl, false, stream_context_create($context));
        $xmlArray = Xml::toArray(Xml::build($ret));
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

}
