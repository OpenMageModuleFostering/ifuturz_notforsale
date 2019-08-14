<?php
/**
 * @package Ifuturz_Notforsale
*/
$installer = $this;
$installer->startSetup();

$attributeInstaller = new Mage_Catalog_Model_Resource_Setup();

$attributeInstaller->addAttribute('catalog_product', 'not_for_sale_to', array(
  'type'              => 'varchar',
  'backend'           => '',
  'frontend'          => '',
  'label'             => 'Not For Sale To',
  'input'             => 'multiselect',
  'class'             => '',
  'backend'            => 'eav/entity_attribute_backend_array',   
  'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_GLOBAL,
  'visible'           => true,
  'required'          => false,
  'user_defined'      => false,
  'default'           => '',
  'searchable'        => false,
  'filterable'        => false,
  'comparable'        => false,
  'visible_on_front'  => true,
  'unique'            => false,
  'group'             => 'General',
  'apply_to'		  => 'simple'
));

$attributeId = $attributeInstaller->getAttributeId('catalog_product', 'not_for_sale_to');

$aOption = array();
$aOption['attribute_id'] = $attributeId;
$regionCollection = Mage::getModel('directory/region_api')->items('US');

$i=0;
foreach($regionCollection as $regionData)
{
	$aOption['value']['option'.$i][0] = $regionData['code'];
	$i++;
}
$attributeInstaller->addAttributeOption($aOption);


foreach ($attributeInstaller->getAllAttributeSetIds('catalog_product') as $attributeSetId)
{
	try {
		$attributeGroupId = $attributeInstaller->getAttributeGroupId('catalog_product', $attributeSetId, 'General');
	} catch (Exception $e) {
		$attributeGroupId = $attributeInstaller->getDefaultAttributeGroupId('catalog_product', $attributeSetId);
	}
	$attributeInstaller->addAttributeToSet('catalog_product', $attributeSetId, $attributeGroupId, $attributeId);
}

$installer->run("

-- DROP TABLE IF EXISTS {$this->getTable('notforsale_lck')};
CREATE TABLE {$this->getTable('notforsale_lck')} ( 	
	`flag` varchar(4),
	`value` ENUM('0','1') DEFAULT '0' NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `{$installer->getTable('notforsale_lck')}` VALUES ('LCK','1');
");

$installer->endSetup();