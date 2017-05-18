<?php


class Webgriffe_BlueknowRecPopulator_Block_Populator extends Mage_Core_Block_Template
{
    const RENDER_ITEMS_COMMAND_NAME = 'renderItems';
    const DEFAULT_COLLECTION_SIZE = 12;
    const COLLECTION_SIZE_PARAMETER_NAME = 'reclimit';

    /**
     * @inheritdoc
     */
    public function _toHtml() {
        if (Webgriffe_BlueknowRecPopulator_Helper_Data::isEnabled()) {
            return parent::_toHtml();
        }
        return '';
    }

    /**
     * @return string
     */
    public function generatePopulatorCommand()
    {
        $_products = [];
        $_productCollection = $this->getProductCollection($this->getCollectionSize());
        foreach ($_productCollection as $_product) {
            $_productUrl = $_product->getProductUrl();
            $_productImage = Mage::helper('catalog/image')->init($_product, 'small_image');
            $_products[] = [
                'id' => $_product->getId(),
                'name' => $_product->getName(),
                'url' => $_productUrl,
                'image' => (string) $_productImage,
                'price' => $_product->getFinalPrice()
            ];
        }
        $_jsonString = json_encode($_products);
        return self::RENDER_ITEMS_COMMAND_NAME . "($_jsonString)";
    }

    /**
     * @param int $collectionSize
     * @return \Iterator|Mage_Catalog_Model_Resource_Product_Collection
     */
    private function getProductCollection($collectionSize)
    {
        /** @var Mage_Catalog_Model_Resource_Product_Collection|\Iterator $_productCollection */
        $_productCollection = Mage::getModel('catalog/product')
            ->getCollection()
            ->addAttributeToFilter(
                'visibility',
                array('neq' => Mage_Catalog_Model_Product_Visibility::VISIBILITY_NOT_VISIBLE)
            )
            ->addAttributeToFilter(
                'status',
                array('eq' => Mage_Catalog_Model_Product_Status::STATUS_ENABLED)
            )
            ->addAttributeToSelect(['name', 'small_image', 'price'])
        ;
        $_productCollection->getSelect()->order(new Zend_Db_Expr('RAND()'))->limit($collectionSize);
        return $_productCollection;
    }

    /**
     * @return int
     */
    private function getCollectionSize()
    {
        $collectionSize = $this->getRequest()->getParam(
            self::COLLECTION_SIZE_PARAMETER_NAME,
            self::DEFAULT_COLLECTION_SIZE
        );
        return is_numeric($collectionSize) ? $collectionSize : self::DEFAULT_COLLECTION_SIZE;
    }
}
