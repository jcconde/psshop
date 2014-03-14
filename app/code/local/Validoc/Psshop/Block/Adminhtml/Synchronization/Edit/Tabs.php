<?php

class Validoc_Psshop_Block_Adminhtml_Synchronization_Edit_Tabs extends Mage_Adminhtml_Block_Widget_Tabs
{
    public function __construct()
    {
        parent::__construct();
        $this->setId('convert_profile_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(Mage::helper('adminhtml')->__('Import/Export Profile'));
    }

    protected function _beforeToHtml()
    {
        $new = !Mage::registry('current_convert_profile')->getId();

        $this->addTab('edit', array(
            'label'     => Mage::helper('adminhtml')->__('Profile Actions XML'),
            'content'   => $this->getLayout()->createBlock('psshop/adminhtml_synchronization_edit_tab_edit')->initForm()->toHtml(),
            'active'    => true,
        ));

        if (!$new) {
            $this->addTab('run', array(
                'label'     => Mage::helper('adminhtml')->__('Run Profile'),
                'content'   => $this->getLayout()->createBlock('psshop/adminhtml_synchronization_edit_tab_run')->toHtml(),
            ));

            $this->addTab('history', array(
                'label'     => Mage::helper('adminhtml')->__('Profile History'),
                'content'   => $this->getLayout()->createBlock('psshop/adminhtml_synchronization_edit_tab_history')->toHtml(),
            ));
        }

        return parent::_beforeToHtml();
    }
}
