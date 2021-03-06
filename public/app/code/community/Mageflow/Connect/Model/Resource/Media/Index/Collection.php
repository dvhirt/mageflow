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
 * Collection.php
 *
 * PHP version 5
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */

/**
 * Mageflow_Connect_Model_Resource_Media_Index_Collection
 *
 * @category   MFX
 * @package    Mageflow_Connect
 * @subpackage Model
 * @author     MageFlow OÜ, Estonia <info@mageflow.com>
 * @copyright  Copyright (C) 2014 MageFlow OÜ, Estonia (http://mageflow.com) 
 * @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 * @link       http://mageflow.com/
 */
class Mageflow_Connect_Model_Resource_Media_Index_Collection
    extends Mage_Core_Model_Resource_Db_Collection_Abstract
{

    /**
     * Initialize resource model collection
     */
    protected function _construct()
    {
        $this->_init('mageflow_connect/media_index');
    }

    /**
     * Checks if file on the disk has changes compared
     * to file in index
     *
     * @param $file
     *
     * @return bool
     */
    public function fileIsCurrent($file)
    {
        $foundFile = $this->findFile($file);
        return (
            $foundFile !== null
            && $foundFile->getMtime() == $file->getMtime()
            && $foundFile->getSize() == filesize($file->getFilename())
        );
    }

    /**
     * Searches for file from Media Index by its ID (hash)
     *
     * @param $file
     *
     * @return Mageflow_Connect_Model_Media_Index
     */
    public function findFile($file)
    {
        /**
         * @var Mageflow_Connect_Model_Media_Index $item
         */
        foreach ($this->getItems() as $item) {
            if ($item->getHash() == $file->getId()) {
                return $item;
            }
        }
        return null;
    }

    /**
     * Checks if given file exists in current Media Index
     *
     * @param $file
     *
     * @return bool
     */
    public function fileExists($file)
    {
        return $this->findFile($file) !== null;
    }
}
