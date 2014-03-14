<?php

class Validoc_Psshop_Block_Adminhtml_Synchronization_Edit_Filter_Action
    extends Mage_Adminhtml_Block_System_Convert_Profile_Edit_Filter_Action
{
    public function getHtml()
    {
        $values = array(
            ''       => '',
            'create' => Mage::helper('adminhtml')->__('Create'),
            'run'    => Mage::helper('adminhtml')->__('Run'),
            'update' => Mage::helper('adminhtml')->__('Update'),
        );
        $value = $this->getValue();

        $html  = '<select name="' . ($this->getColumn()->getName() ? $this->getColumn()->getName() : $this->getColumn()->getId()) . '" ' . $this->getColumn()->getValidateClass() . '>';
        foreach ($values as $k => $v) {
            $html .= '<option value="'.$k.'"' . ($value == $k ? ' selected="selected"' : '') . '>'.$v.'</option>';
        }
        $html .= '</select>';
        return $html;
    }
}
