<?php
/** 
 *
 * @category    Collinsharper
 * @package     Collinsharper_Canpost
 * @author      Maxim Nulman
 */
class Collinsharper_Canpost_Helper_Rest_Pdf extends Collinsharper_Canpost_Helper_Rest_Request
{
  
    public function load($url, $media_type, $filename='labels', $return_transfer = 1)
    {
        $headers = array(
            'Accept: ' . $media_type
        );
        
        if ($return_transfer) {

            header('Content-type: '.$media_type);

            header('Content-Disposition: attachment; filename="'.$filename.'-'.date('Y-m-d--H-i-s').'.pdf"');
            
            $this->send($url, '', 1, $headers);

            return true;

        } else {

            return $this->send($url, '', 1, $headers);

        }
        
    }
    
    
    public function addPage($pdf, $pdfString)
    {

        $extractor = new Zend_Pdf_Resource_Extractor();

        $temp_pdf = Zend_Pdf::parse($pdfString);

        $page = $extractor->clonePage($temp_pdf->pages[0]);

        $pdf->pages[] = $page;
        
    }
    
    
}
