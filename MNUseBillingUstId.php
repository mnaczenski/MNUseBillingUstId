<?php
namespace MNUseBillingUstId;

use Shopware\Components\Plugin\Context\ActivateContext;
use Shopware\Components\Plugin\Context\DeactivateContext;

class MNUseBillingUstId extends \Shopware\Components\Plugin
{

    public function activate(ActivateContext $context)
    {
        $context->scheduleClearCache(ActivateContext::CACHE_LIST_DEFAULT);
    }

    public function deactivate(DeactivateContext $context)
    {
        $context->scheduleClearCache(DeactivateContext::CACHE_LIST_DEFAULT);
    }

    public static function getSubscribedEvents()
    {
        return [
            'Shopware_Modules_Admin_GetUserData_FilterResult' => 'onFilterUserData'
        ];
    }

    /**
     * @param \Enlight_Event_EventArgs $args
     * @return array
     */
    public function onFilterUserData($args)
    {
        $userData = $args->getReturn();
        /**
         * Wenn In der Lieferadresse keine Umsatzsteuer-ID hinterlegt ist
         * und  das Lieferland auf "Steuerfrei für Unternehmen steht"
         * und  in der Rechnungsadresse eine Umsatzsteuer-ID hinterlegt ist
         * und  das Rechnungsland auf "Steuerfrei für Unternehmen steht"
         * dann ersetze die UmsatzsteuerID aus der Lieferadresse mit der aus der Rechnungsadresse
         */
        if (empty($userData['shippingaddress']['ustid']) && $userData['additional']['countryShipping']['taxfree_ustid'] == 1
            && !empty($userData['billingaddress']['ustid']) && $userData['additional']['country']['taxfree_ustid'] == 1) {
            $userData['shippingaddress']['ustid'] = $userData['billingaddress']['ustid'];
        }
        return $userData;
    }
}
