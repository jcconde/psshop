<?php

class Validoc_Psshop_Block_Adminhtml_Synchronization_Edit_Tab_Edit extends Mage_Adminhtml_Block_Widget_Form
{
    /**
     * Class constructor
     *
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setTemplate('validoc/synchronization/form.phtml');
    }

    public function initForm()
    {
        $this->setId('syncFilterForm');
        $form = new Varien_Data_Form();
        $htmlIdPrefix = 'sync_profile_synchronization_';
        $form->setHtmlIdPrefix($htmlIdPrefix);

        $model = Mage::registry('current_convert_profile');

        $fieldset = $form->addFieldset('base_fieldset', array(
            'legend'=>Mage::helper('adminhtml')->__('General Information'),
            'class'=>'fieldset-wide'
        ));

        $fieldset->addField('name', 'text', array(
            'name' => 'name',
            'label' => Mage::helper('adminhtml')->__('Profile Name'),
            'title' => Mage::helper('adminhtml')->__('Profile Name'),
            'required' => true,
        ));

        $fieldset->addField('actions_xml', 'textarea', array(
            'name' => 'actions_xml',
            'label' => Mage::helper('adminhtml')->__('Actions XML'),
            'title' => Mage::helper('adminhtml')->__('Actions XML'),
            'style' => 'height:30em',
            'required' => true,
        ));

        $form->setValues($model->getData());

        $this->setForm($form);

        return $this;
    }

    /**
     * get js object name by block name
     * @param string $blockName
     * @return null
     */
    protected function getJsObjectName() {
        return $this->getId().'JsObject';
    }

//    protected function _beforeToHtml()
//    {
//        $block = $this->getLayout()->createBlock('core/text');
//        $block->setText('
//            <script type="text/javascript">
//                var '.$this->getJsObjectName().' = new Fibos.Synchronization();
//                jQuery(document).ready(function() {
//                    '.$this->getJsObjectName().'.addEventTooltipTipsy("a[rel=tipsy]");
//                    '.$this->getJsObjectName().'.addEventClickToPublishProduct("a#id_publish_product", '.Mage::getUrl("*/*/publishProduct").'");
//                });
//            </script>
//        ');
//        $this->getChild('form_after')->setChild('psshop.synchronization',$block);
//        return parent::_beforeToHtml();
//    }


}
