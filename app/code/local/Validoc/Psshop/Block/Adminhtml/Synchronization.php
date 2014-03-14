<?php

class Validoc_Psshop_Block_Adminhtml_Synchronization extends Mage_Adminhtml_Block_Widget_Grid_Container
{
    public function __construct()
    {
        $this->_blockGroup = 'psshop';
        $this->_controller = 'adminhtml_synchronization';
        $this->_headerText = Mage::helper('adminhtml')->__('Synchronization');
        $this->_addButtonLabel = Mage::helper('adminhtml')->__('Add New Profile');

        parent::__construct();
    }
}

