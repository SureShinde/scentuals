<?php
class ARW_ProductTab_Adminhtml_ProducttabController extends Mage_Adminhtml_Controller_Action
{

	protected function _initAction() {
		$this->loadLayout()
			->_setActiveMenu('arexworks/producttab')
			->_addBreadcrumb(Mage::helper('adminhtml')->__('Tabs Manager'), Mage::helper('adminhtml')->__('Tabs Manager'));
	//	$this->getLayout()->getBlock('head')->setCanLoadTinyMce(true);
		return $this;
	}   
 
	public function indexAction() {
		$this->_initAction()
			->_addJs(
					$this->getLayout()->createBlock('adminhtml/template')
										->setIsPopup((bool)$this->getRequest()->getParam('popup'))
										->setTemplate('arw/producttab/js.phtml'))
			->renderLayout();
	}
	public function informationTabAction(){
		if($this->getRequest()->getPost('arw_information')){
			$popupInfor=$this->getLayout()->createBlock('producttab/adminhtml_producttab_information_edit');
			$htmlInfor.= $popupInfor->toHtml();
			$result = array(
				'popup_infor_tab'   =>  $htmlInfor, 
			  );
			$this->getResponse()->setBody(Zend_Json::encode($result));
		 }
	}
	public function popupTabAction(){
		if($this->getRequest()->getPost('param')){	
			$popup=Mage::app()->getLayout()->createBlock('producttab/adminhtml_producttab_importTab','arw_import_tab');
			$popupInfor=$this->getLayout()->createBlock('producttab/adminhtml_producttab_information_edit');
            $html = "";
			$html .= $popup->toHtml();
			$html .= '<div id="arw_information_site_you">'.$popupInfor->toHtml().'</div>';
			$result = array(
				'popup_import_tab'   =>  $html, 
			  );
		 }
       	$this->getResponse()->setBody(Zend_Json::encode($result));
	}
	public function importTabAction(){
		if (!isset($_FILES['csv_tabs'])) {
            Mage::getSingleton('core/session')->addError('Not selected file!');
            $this->_redirect('*/*/index');
            return;
        }
		$oFile = new Varien_File_Csv();
        $data = $oFile->getData($_FILES['csv_tabs']['tmp_name']);
		$tabs = Mage::getModel('producttab/tab');
        $tabsData = array();
        try {
            $total = 0;
            foreach ($data as $col => $row) {
                if ($col == 0) {
                    $index_row = $row;
                } else {

                    for ($i = 0; $i < count($row); $i++) {
                        $tabsData[$index_row[$i]] = $row[$i];
                    }
					$tabs->setData($tabsData);
					$tabs->setId(null);
					if ($tabs->import())
						$total++;
					}
            }
			
            $this->_redirect('*/adminhtml_producttab/index');
            if ($total != 0)
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('producttab')->__($total.' Tab Imported successfully.'));
            else
                Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('producttab')->__('No Tab imported.'));
        } catch (Exception $e) {
            Mage::getSingleton('core/session')->addError($e->getMessage());
            $this->_redirect('*/adminhtml_producttab/index');
        }
		
	}
	 public function categoriesJsonAction()
    {
        $this->getResponse()->setBody(
            $this->getLayout()->createBlock('producttab/adminhtml_producttab_edit_tab_category_categoriestree')
                    ->getCategoryChildrenJson($this->getRequest()->getParam('category'))
        );
    }
	public function editAction() {
		$id     = $this->getRequest()->getParam('id');
		$model  = Mage::getModel('producttab/tab')->load($id);

		if ($model->getId() || $id == 0) {
			$data = Mage::getSingleton('adminhtml/session')->getFormData(true);
			if (!empty($data)) {
				$model->setData($data);
			}

			Mage::register('tab_data', $model);

			$this->loadLayout();
			$this->_setActiveMenu('arexworks/producttab');

			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item Manager'), Mage::helper('adminhtml')->__('Item Manager'));
			$this->_addBreadcrumb(Mage::helper('adminhtml')->__('Item News'), Mage::helper('adminhtml')->__('Item News'));

			$this->getLayout()->getBlock('head')->setCanLoadExtJs(true);

			$this->_addContent($this->getLayout()->createBlock('producttab/adminhtml_producttab_edit'))
				->_addLeft($this->getLayout()->createBlock('producttab/adminhtml_producttab_edit_tabs'));

			$this->renderLayout();
		} else {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('producttab')->__('Slider does not exist'));
			$this->_redirect('*/*/');
		}
	}
	  public function duplicateAction() {
        $id = (int) $this->getRequest()->getParam('id');

        try {
            $newId = Mage::getResourceModel('producttab/tab')->duplicate($id);
         
        } catch (Exception $e) {
            if ($e->getMessage()) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                $this->_redirect('*/*/edit', array('id' => $newId));
            }
        }

        Mage::getSingleton('adminhtml/session')->addSuccess($this->__('tab were successfully duplicated'));
        
        $this->_redirect('*/*/edit', array('id' => $newId));
    }
 
	public function newAction() {
		$this->_forward('edit');
	}
	public function deleteAction() {
		if( $this->getRequest()->getParam('id') > 0 ) {
			try {
				
			    $id     = $this->getRequest()->getParam('id');
				$model = Mage::getModel('producttab/tab');
				$model->setId($this->getRequest()->getParam('id'));
				Mage::getModel('producttab/tab')->deleteStores($id);
				Mage::getModel('producttab/tab')->deleteProduct($id);
				$model->delete();
				 
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('adminhtml')->__('Tab was successfully deleted'));
				$this->_redirect('*/*/');
			} catch (Exception $e) {
				Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
				$this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
			}
		}
		$this->_redirect('*/*/');
	}
	 public function massDeleteAction() {
        $Ids = $this->getRequest()->getParam('producttab');
        if(!is_array($Ids)) {
			Mage::getSingleton('adminhtml/session')->addError(Mage::helper('adminhtml')->__('Please select Category(s)'));
        } else {
            try {
                foreach ($Ids as $Id) {
                    
					$categoryslider = Mage::getModel('producttab/tab')->load($Id);	
					Mage::getModel('producttab/tab')->deleteStores($Id);
					Mage::getModel('producttab/tab')->deleteProduct($Id);
                    $categoryslider->delete();			
                }
                Mage::getSingleton('adminhtml/session')->addSuccess(
                    Mage::helper('adminhtml')->__(
                        'Total of %d record(s) were successfully deleted', count($Ids)
                    )
                );
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
            }
        }
        $this->_redirect('*/*/index');
    }
	public function saveAction() {
		
		if ($data = $this->getRequest()->getPost()) {
			$model = Mage::getModel('producttab/tab');
			$model->setData($data)
				->setId($this->getRequest()->getParam('id'));	
		if($this->getRequest()->getPost('product_type')==ARW_ProductTab_Model_Product_Type::NONE){
			  $arw_products = $this->getRequest()->getPost('in_products');
				 $_POST['productIds'] = trim($arw_products);
			 }else{	
				unset($_POST['productIds']);
			 }
			try {
				$model->save();
				
				Mage::getSingleton('adminhtml/session')->addSuccess(Mage::helper('producttab')->__('Tab was successfully saved'));
				Mage::getSingleton('adminhtml/session')->setFormData(false);

				if ($this->getRequest()->getParam('back')) {
					$this->_redirect('*/*/edit', array('id' => $model->getId()));
					return;
				}
				$this->_redirect('*/*/');
				return;
            } catch (Exception $e) {
                Mage::getSingleton('adminhtml/session')->addError($e->getMessage());
                Mage::getSingleton('adminhtml/session')->setFormData($data);
                $this->_redirect('*/*/edit', array('id' => $this->getRequest()->getParam('id')));
                return;
            }
        }
        Mage::getSingleton('adminhtml/session')->addError(Mage::helper('producttab')->__('Unable to find Tab to save'));
        $this->_redirect('*/*/');
	}
	public function productGridAction() {
        $this->loadLayout();
        $this->getResponse()->setBody(
                $this->getLayout()->createBlock('producttab/adminhtml_producttab_edit_tab_product_grid_grid')->toHtml()
        );
    }
	public function exportCsvAction() {
        $fileName = 'producttabs.csv';
		$content = Mage::getModel('producttab/exporter')->exportTabs();
        $this->_sendUploadResponse($fileName, $content);
    }
	public function exportXmlAction() {
        $fileName = 'producttabs.xml';
        $content = Mage::getModel('producttab/exporter')->getXmlTabs();
        $this->_sendUploadResponse($fileName, $content);
    }
	protected function _sendUploadResponse($fileName, $content, $contentType = 'application/octet-stream') {
        $response = $this->getResponse();
        $response->setHeader('HTTP/1.1 200 OK', '');
        $response->setHeader('Pragma', 'public', true);
        $response->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
        $response->setHeader('Content-Disposition', 'attachment; filename=' . $fileName);
        $response->setHeader('Last-Modified', date('r'));
        $response->setHeader('Accept-Ranges', 'bytes');
        $response->setHeader('Content-Length', strlen($content));
        $response->setHeader('Content-type', $contentType);
        $response->setBody($content);
        $response->sendResponse();
        die;
    }
}
