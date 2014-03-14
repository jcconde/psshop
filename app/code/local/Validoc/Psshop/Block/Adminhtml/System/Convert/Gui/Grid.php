<?php
/**
 * Created by PhpStorm.
 * User: TOSHIBA
 * Date: 2/4/14
 * Time: 3:11 PM
 */ 
class Validoc_Psshop_Block_Adminhtml_System_Convert_Gui_Grid extends Mage_Adminhtml_Block_System_Convert_Gui_Grid {

    protected function _prepareCollection()
    {
        parent::_prepareCollection();
        $collection = Mage::getResourceModel('dataflow/profile_collection')
            ->addFieldToFilter('entity_type', array('notnull'=>''))
            ->addFieldToFilter('entity_type', array('neq'=>'synchronization'));

        $this->setCollection($collection);

        return $this;
    }
}