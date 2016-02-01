<?php
// Author: Simon East - https://yump.com.au or tweet @SimoEast
namespace Concrete\Package\RelatedPages;
use Package;
use \Concrete\Core\Attribute\Key\Category as AttributeKeyCategory;
use \Concrete\Core\Attribute\Type as AttributeType;
// use BlockType;
use AssetList;
use Asset;

class Controller extends Package {

    protected $pkgHandle = 'related_pages';
    protected $appVersionRequired = '5.7.3';
    protected $pkgVersion = '0.0.6';

    public function getPackageName() {
        return t('Related Pages');
    }
    
    public function getPackageDescription() {
        return t('Adds a new attribute type called "Related Pages" which allows the selection of one pages that relate to the base one. Allows the establishment of many-to-many relationships between pages.');
    }

    public function install() {
        $pkg = parent::install();
        $this->installAttributeType($pkg);
    }

    protected function installAttributeType($pkg) {
        $attributeHandle = 'related_pages';
        \Loader::model('attribute/categories/collection');
        AttributeType::add($attributeHandle, t('Related Pages'), \Package::getByHandle($this->pkgHandle));
        AttributeKeyCategory::getByHandle('collection')->associateAttributeKeyType(AttributeType::getByHandle($attributeHandle));
    }
    
    
    // TODO: Cleanup on uninstall
    
    /**
     * Setup the necessary CSS and JS assets we need for this attribute
     */
    public function on_start() {
        
        $al = AssetList::getInstance();

        $al->register('javascript', 'bsmSelect', 'attributes/related_pages/js/bsmSelect/js/jquery.bsmselect.js', array('version' => '1.4.7', 'position' => Asset::ASSET_POSITION_FOOTER, 'minify' => true, 'combine' => false), $this);
        $al->register('javascript', 'bsmSelect.sortable', 'attributes/related_pages/js/bsmSelect/js/jquery.bsmselect.sortable.js', array('version' => '1.4.7', 'position' => Asset::ASSET_POSITION_FOOTER, 'minify' => true, 'combine' => false), $this);
        $al->register('css', 'bsmSelect', 'attributes/related_pages/js/bsmSelect/css/jquery.bsmselect.css', array('version' => '1.4.7', 'position' => Asset::ASSET_POSITION_HEADER, 'minify' => true, 'combine' => false), $this);
        $al->registerGroup('bsmSelect',
            array(
                // array('javascript', 'jquery'),
                array('javascript', 'bsmSelect'),
                array('javascript', 'bsmSelect.sortable'),
                array('css', 'bsmSelect'),
            )
        );

    }


}
