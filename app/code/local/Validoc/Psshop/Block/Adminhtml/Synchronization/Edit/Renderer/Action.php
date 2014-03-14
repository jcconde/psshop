<?php

class Validoc_Psshop_Block_Adminhtml_Synchronization_Edit_Renderer_Action
    extends Mage_Adminhtml_Block_System_Convert_Profile_Edit_Renderer_Action
{
    public function render(Varien_Object $row)
    {
        $values = array(
            'create' => Mage::helper('adminhtml')->__('Create'),
            'run'    => Mage::helper('adminhtml')->__('Run'),
            'update' => Mage::helper('adminhtml')->__('Update'),
        );
        $value = $row->getData($this->getColumn()->getIndex());
        return $values[$value];
    }
}
