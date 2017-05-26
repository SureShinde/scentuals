<?php
/**
 *
 * @category    Collinsharper
 * @package     Collinsharper_Canpost
 * @author      Shane Harper
 */
class Collinsharper_Canpost_Adminhtml_AdvreportController extends Mage_Adminhtml_Controller_Report_Abstract //Mage_Adminhtml_Controller_Action
{
    protected function _isAllowed()
    {
        return Mage::getSingleton('admin/session')->isAllowed('sales/manifest');
    }

    public function ShippingAction()
    {
        $this->loadLayout();

        $block = $this->getLayout()->createBlock(
            'Collinsharper_Canpost_Block_Adminhtml_Report_Shipping',
            'AdvShipping',
            array()
        );

        $this->getLayout()->getBlock('content')->append($block);

        $this->renderLayout();

    }

    public function exportShippingExcelAction()
    {
        $fileName   = 'shipping.xml';
        $content    = $this->getLayout()->createBlock('Collinsharper_Canpost_Block_Adminhtml_Report_Shipping_Grid')
            ->getExcel($fileName);

        $this->_prepareDownloadResponse($fileName, $content);
    }

    public function exportShippingCsvAction()
    {
        $fileName   = 'standardsales.csv';
        $content    = $this->getLayout()->createBlock('Collinsharper_Canpost_Block_Adminhtml_Report_Shipping_Grid')
            ->getCsv();

        $this->_prepareDownloadResponse($fileName, $content);
    }
}
