<?php

class Webgriffe_BlueknowRecPopulator_Helper_Data extends Mage_Core_Helper_Abstract
{
    const XML_PATH_POPULATOR_ENABLED = 'blueknow/populator/enabled';

    /**
     * @return bool
     */
    public static function isEnabled()
    {
        return (bool) Mage::getStoreConfigFlag(self::XML_PATH_POPULATOR_ENABLED);
    }
}
