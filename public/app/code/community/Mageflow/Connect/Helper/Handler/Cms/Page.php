<?php
/**
 * MageFlow
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@mageflow.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * If you wish to use the MageFlow Connect extension as part of a paid
 * service please contact licence@mageflow.com for information about
 * obtaining an appropriate licence.
 */

/**
 * Page.php
 *
 * PHP version 5
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Helper
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */

/**
 * Mageflow_Connect_Helper_Handler_Cms_Page
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Helper
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Helper_Handler_Cms_Page
    extends Mageflow_Connect_Helper_Handler_Cms_Abstract
{
    /**
     * update or create cms/page from data array
     *
     * @param $filteredData
     *
     * @return array
     */
    public function handle($filteredData)
    {
        $itemFoundByIdentifier = false;
        $itemFoundByMfGuid = false;
        $foundItemsMatch = false;
        $itemModel = null;
        $originalData = array();

        $itemModelByIdentifier = Mage::getModel('cms/page')
            ->load($filteredData['identifier'], 'identifier');
        $itemModelByMfGuid = Mage::getModel('cms/page')
            ->load($filteredData['mf_guid'], 'mf_guid');

        if ($itemModelByIdentifier->getPageId()) {
            $itemFoundByIdentifier = true;
        }
        if ($itemModelByMfGuid->getPageId()) {
            $itemFoundByMfGuid = true;
        }
        if ($itemFoundByIdentifier && $itemFoundByMfGuid) {
            $idByIdent = $itemModelByIdentifier->getPageId();
            $idByGuid = $itemModelByMfGuid->getPageId();

            Mage::helper('mageflow_connect/log')->log(
                'by mf_guid ' . $idByGuid
            );
            Mage::helper('mageflow_connect/log')->log('by ident ' . $idByIdent);

            if ($idByGuid == $idByIdent) {
                $foundItemsMatch = true;
            }
        }

        if ($itemFoundByIdentifier && !$itemFoundByMfGuid) {
            Mage::helper('mageflow_connect/log')->log('case 01');
            $itemModel = $itemModelByIdentifier;
            $filteredData['page_id'] = $itemModel->getPageId();
        }
        if (!$itemFoundByIdentifier && $itemFoundByMfGuid) {
            Mage::helper('mageflow_connect/log')->log('case 10');
            $itemModel = $itemModelByMfGuid;
            $filteredData['page_id'] = $itemModel->getPageId();
        }
        if (!$itemFoundByIdentifier && !$itemFoundByMfGuid) {
            Mage::helper('mageflow_connect/log')->log('case 00');
            $itemModel = Mage::getModel('cms/page');
        }
        if ($itemFoundByIdentifier && $itemFoundByMfGuid && $foundItemsMatch) {
            Mage::helper('mageflow_connect/log')->log('case 11-1');
            $itemModel = $itemModelByMfGuid;
            $filteredData['page_id'] = $itemModel->getPageId();
        }
        if ($itemFoundByIdentifier && $itemFoundByMfGuid && !$foundItemsMatch) {
            Mage::helper('mageflow_connect/log')->log('case 11-0');
            $itemModel = $itemModelByMfGuid;
            $filteredData['page_id'] = $itemModel->getPageId();
        }

        $originalData = null;
        if (!is_null($itemModel)) {
            $originalData = $itemModel->getData();
        }

        if (isset($filteredData['stores'])) {
            foreach ($filteredData['stores'] as $key => $storeCode) {
                if ($storeCode != "0") {
                    $storeEntity = Mage::getModel('core/store')
                        ->load($storeCode, 'code');
                    $filteredData['stores'][$key] = $storeEntity->getId();
                }
            }
        } else {
            $storeEntity = Mage::getModel('core/store')
                ->load('default', 'code');
            $filteredData['stores'][0] = $storeEntity->getId();
        }

        $savedEntity = $this->saveItem($itemModel, $filteredData);
        if ($savedEntity instanceof Mage_Cms_Model_Page) {
            return array(
                'entity'        => $savedEntity,
                'original_data' => $originalData
            );
        }
        Mage::helper('mageflow_connect/log')->log(
            "Error occurred while tried to save CMS page. Data follows:\n"
            . print_r($filteredData, true)
        );
        return null;
    }


}