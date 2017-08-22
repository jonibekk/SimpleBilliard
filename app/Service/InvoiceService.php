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
     * register order for 1 team's invoice via atobarai.com
     *
     * @param int    $teamId
     * @param array  $targetChargeHistories
     * @param array  $monthlyChargeHistory
     * @param string $orderDate
     *
     * @return array responce from atobarai.com
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
        $team = $Team->getById($teamId);
        $invoiceInfo = $Invoice->getByTeamId($teamId);

        // calc amount total
        $addedUserAmount = $this->getAddedUserAmount($targetChargeHistories);
        $amountTotal = $addedUserAmount + $monthlyChargeHistory['total_amount'] + $monthlyChargeHistory['tax'];

        $data = [
            'O_ReceiptOrderDate'     => $orderDate,
            'O_ServicesProvidedDate' => date('Y-m-d', REQUEST_TIMESTAMP + (9 * HOUR)),
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
            'C_Phone'                => $invoiceInfo['contact_person_tel'],
            'C_MailAddress'          => $invoiceInfo['contact_person_email'],
            'C_EntCustId'            => $teamId,
        ];

        // for added users charge
        $data["I_ItemNameKj_0"] = "Goalous追加利用料";
        $data["I_UnitPrice_0"] = $addedUserAmount;
        $data["I_ItemNum_0"] = 1;

        // for monthly charge
        $monthlyStartDate = date('n/j', strtotime($monthlyChargeHistory['monthlyStartDate']));
        $monthlyEndDate = date('n/j', strtotime($monthlyChargeHistory['monthlyEndDate']));
        $data["I_ItemNameKj_1"] = "Goalous月額利用料({$monthlyStartDate} - {$monthlyEndDate})";
        $data["I_UnitPrice_1"] = $monthlyChargeHistory['total_amount'] + $monthlyChargeHistory['tax'];
        $data["I_ItemNum_1"] = 1;
        $resAtobarai = $this->_postRequestForAtobaraiDotCom(self::API_URL_REGISTER_ORDER, $data);
        $resAtobarai = am($resAtobarai, ['requestData' => $data]);
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
        /** @var  Invoice $Invoice */
        $Invoice = ClassRegistry::init('Invoice');
        // TODO.Payment: This is bad know how. We should use mock on testing. We can try to use https://packagist.org/packages/phake/phake#v2.3.2
        // see detail -> #18 on http://bit.ly/2g1MkWR
        if ($Invoice->useDbConfig == 'test') {
            return $this->getAtobaraiResponseForTest();
        }

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
     * This is for only testing!
     * Don't use it for production.
     *
     * @return array
     */
    function getAtobaraiResponseForTest()
    {
        $data = [
            'status'        => 'success',
            'orderId'       => '',
            'systemOrderId' => 'AK23553506',
            'messages'      => '',
            'orderStatus'   => [
                '@cd' => '0',
                '@'   => '与信中'
            ]
        ];
        return $data;
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
     * @param array $invoice
     *
     * @return string
     */
    function getCompanyAddress(array $invoice): string
    {
        return $invoice['company_region'] . $invoice['company_city'] . $invoice['company_street'];
    }

    /**
     * @param array $invoice
     *
     * @return string
     */
    function getContactNameKj(array $invoice): string
    {
        return $invoice['contact_person_last_name'] . $invoice['contact_person_first_name'];
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

}
