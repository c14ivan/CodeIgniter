<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

$ci =& get_instance();
$ci->config->load('pdf');

require_once(APPPATH . 'third_party/tcpdf/tcpdf.php');

class Pdf extends TCPDF
{
    private $print_pagenumber = true;
    
    function __construct($orientation='P', $unit='mm', $format='A4', $unicode=true, $encoding='UTF-8', $diskcache=false, $pdfa=false,$extraoptions=array())
    {
        parent::__construct();
        $this->ci =& get_instance();
        $this->ci->config->load('pdf');
        
        $this->print_pagenumber=isset($extraoptions['noprint_numpage'])?false:true;
        
        $this->setHeaderMargin (8);
        $this->setHeaderFont(array('dejavusans', '', 11, '', true));
        $this->SetHeaderData('logo.jpg', 25, $this->ci->config->item('title', 'pdf'), $this->ci->config->item('subtitle', 'pdf'));
        $this->SetTopMargin(40);
        $this->SetAutoPageBreak(false);
        $this->SetAuthor($this->ci->config->item('apptitle'));
        $this->SetDisplayMode('real', 'default');
        
        $this->setFooterMargin(10);
        $this->setFooterData();
        
    }
    public function Header() {


        // get the current page break margin
        $bMargin = $this->getBreakMargin();
        // get current auto-page-break mode
        $auto_page_break = $this->AutoPageBreak;
        // disable auto-page-break
        $this->SetAutoPageBreak(false, 0);
        // set bacground image
        $img_file = asset_url().'imgapp/water_logo.jpg';
        $this->Image($img_file, 30, 80, 150, 150, '', '', '', false, 300, '', false, false, 0);
        // restore auto-page-break status
        $this->SetAutoPageBreak($auto_page_break, $bMargin);
        // set the starting point for the page content
        $this->setPageMark();
        
        if ($this->header_xobjid < 0) {
            // start a new XObject Template
            $this->header_xobjid = $this->startTemplate($this->w, $this->tMargin);
            $headerfont = $this->getHeaderFont();
            $headerdata = $this->getHeaderData();
            $this->y = $this->header_margin;
            if ($this->rtl) {
                $this->x = $this->w - $this->original_rMargin;
            } else {
                $this->x = $this->original_lMargin;
            }
            if (($headerdata['logo']) AND ($headerdata['logo'] != K_BLANK_IMAGE)) {
                $imgtype = TCPDF_IMAGES::getImageFileType(K_PATH_IMAGES.$headerdata['logo']);
                if (($imgtype == 'eps') OR ($imgtype == 'ai')) {
                    $this->ImageEps(K_PATH_IMAGES.$headerdata['logo'], '', '', $headerdata['logo_width']);
                } elseif ($imgtype == 'svg') {
                    $this->ImageSVG(K_PATH_IMAGES.$headerdata['logo'], '', '', $headerdata['logo_width']);
                } else {
                    $this->Image(K_PATH_IMAGES.$headerdata['logo'], '', '', $headerdata['logo_width']);
                }
                $imgy = $this->getImageRBY();
            } else {
                $imgy = $this->y;
            }
            $cell_height = round(($this->cell_height_ratio * $headerfont[2]) / $this->k, 2);
            // set starting margin for text data cell
            if ($this->getRTL()) {
                $header_x = $this->original_rMargin + ($headerdata['logo_width'] * 1.1);
            } else {
                $header_x = $this->original_lMargin + ($headerdata['logo_width'] * 1.1);
            }
            $cw = $this->w - $this->original_lMargin - $this->original_rMargin - ($headerdata['logo_width'] * 1.1);
            $this->SetTextColorArray($this->header_text_color);
            // header title
            $this->SetFont($headerfont[0], 'B', $headerfont[2] + 2);
            $this->SetX($header_x);
            $this->Cell($cw, $cell_height, $headerdata['title'], 0, 1, 'C', 0, '', 0);
            // header string
            $this->SetFont($headerfont[0], $headerfont[1], $headerfont[2]);
            $this->SetX($header_x);
            $this->MultiCell($cw, $cell_height, $headerdata['string'], 0, 'C', 0, 1, '', '', true, 0, false, true, 0, 'T', false);
            
        }
        // print header template
        $x = 0;
        $dx = 0;
        if (!$this->header_xobj_autoreset AND $this->booklet AND (($this->page % 2) == 0)) {
            // adjust margins for booklet mode
            $dx = ($this->original_lMargin - $this->original_rMargin);
        }
        if ($this->rtl) {
            $x = $this->w + $dx;
        } else {
            $x = 0 + $dx;
        }
        $this->printTemplate($this->header_xobjid, $x, 0, 0, 0, '', '', false);
        if ($this->header_xobj_autoreset) {
            // reset header xobject template at each page
            $this->header_xobjid = -1;
        }
    }
    public function Footer() {
        $headerfont = $this->getHeaderFont();
        $cur_y = $this->y;
        //$this->SetTextColorArray($this->footer_text_color);
        //set style for cell border
        //$line_width = (0.85 / $this->k);
        //$this->SetLineStyle(array('width' => $line_width, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => $this->footer_line_color));
        //print document barcode
        /*$barcode = $this->getBarcode();
        if (!empty($barcode)) {
            $this->Ln($line_width);
            $barcode_width = round(($this->w - $this->original_lMargin - $this->original_rMargin) / 3);
            $style = array(
                    'position' => $this->rtl?'R':'L',
                    'align' => $this->rtl?'R':'L',
                    'stretch' => false,
                    'fitwidth' => true,
                    'cellfitalign' => '',
                    'border' => false,
                    'padding' => 0,
                    'fgcolor' => array(0,0,0),
                    'bgcolor' => false,
                    'text' => false
            );
            //$this->write1DBarcode($barcode, 'C128', '', $cur_y + $line_width, '', (($this->footer_margin / 3) - $line_width), 0.3, $style, '');
        }*/
        $this->SetFont($headerfont[0], '', $headerfont[2] - 2);
        $this->SetY($cur_y);
        $this->Cell(0, 0, $this->ci->config->item('footer', 'pdf'), 'T', 0, 'C');
        //Print page number
        if($this->print_pagenumber){
            $w_page = isset($this->l['w_page']) ? $this->l['w_page'].' ' : '';
            if (empty($this->pagegroups)) {
                $pagenumtxt = $w_page.$this->getAliasNumPage().' / '.$this->getAliasNbPages();
            } else {
                $pagenumtxt = $w_page.$this->getPageNumGroupAlias().' / '.$this->getPageGroupAlias();
            }
            
            if ($this->getRTL()) {
                $this->SetX($this->original_rMargin);
                $this->Cell(0, 0, $pagenumtxt, 'T', 0, 'L');
            } else {
                $this->SetX($this->original_lMargin);
                $this->Cell(0, 0, $this->getAliasRightShift().$pagenumtxt, 'T', 0, 'R');
            }
        }
    }
}

/* End of file Pdf.php */
/* Location: ./application/libraries/Pdf.php */
?>