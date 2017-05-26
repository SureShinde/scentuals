<?php
/**
 * 
 * @package Collinsharper_Canpost
 *
 * @author Maxim Nulman 
 */
class Collinsharper_Canpost_Block_Adminhtml_Manifest_Grid extends Mage_Adminhtml_Block_Widget_Grid {

    public function __construct() {
        parent::__construct();
        $this->setId('manifestGrid');
        $this->setDefaultSort('manifest_id');
        $this->setDefaultDir('ASC');
        $this->setSaveParametersInSession(true);
    }
//
//    protected function _prepareLayout() {
//        $this->setChild('create_btn', $this->getLayout()->createBlock('adminhtml/widget_button')
//                        ->setData(array(
//                            'label' => Mage::helper('adminhtml')->__('Create'),
//                            'onclick' => 'setLocation(\''.$this->getUrl('*/*/create').'\');',
//                            'class' => 'task'
//                        ))
//        );
//        return parent::_prepareLayout();
//    }
    
    public function getCreateButtonHtml()
    {
        return $this->getChildHtml('create_btn');
    }

    public function getMainButtonsHtml()
    {
        $html = parent::getMainButtonsHtml();
        $html.= $this->getCreateButtonHtml();
        return $html;
    }

    protected function _prepareCollection() {

        $collection = Mage::getModel('chcanpost2module/manifest')
                        ->getCollection()
                        ->setOrder('id', 'DESC');

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns() {

        $this->addColumn('id', array(
            'header' => Mage::helper('chcanpost2module')->__('Manifest ID #'),
            'index' => 'id',
            'type' => 'number',
            'filter' => false
        ));
        
        $this->addColumn('created_at', array(
            'header' => Mage::helper('chcanpost2module')->__('Date Created'),
            'index' => 'created_at',
            'type' => 'date',
            'filter' => false
        ));
        
        $this->addColumn('updated_at', array(
            'header' => Mage::helper('chcanpost2module')->__('Date Updated'),
            'index' => 'updated_at',
            'type' => 'date',
            'filter' => false
        ));
        
        $this->addColumn('status', array(
            'header' => Mage::helper('chcanpost2module')->__('Status'),
            'index' => 'status',
            'type' => 'text',
            'filter' => false
        ));
        
        return parent::_prepareColumns();
        
    }

    public function getRowUrl($row) {
        return $this->getUrl('*/*/view', array('manifest_id' => $row->getId()));        
    }

    protected function _prepareMassaction()
    {
        
        if (Mage::helper('chcanpost2module/rest_request')->isContract()) {
        
            $this->setMassactionIdField('id');

            $this->getMassactionBlock()->setFormFieldName('manifest_ids');

            $this->getMassactionBlock()->addItem('massTransmit', array(
                 'label'    => Mage::helper('chcanpost2module')->__('Transmit'),
                 'url'      => $this->getUrl('*/*/massTransmit'),
                 'confirm'  => Mage::helper('chcanpost2module')->__('Warning: You will be billed for any shipments transmitted as part of the manifest(s). Once created a manifest cannot be cancelled.'),
            ));
        
        }
        
        return $this;
        
    }

}