<?php
/*------------------------------------------------------------------------
# com_guru
# ------------------------------------------------------------------------
# author    iJoomla
# copyright Copyright (C) 2013 ijoomla.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.ijoomla.com
# Technical Support:  Forum - http://www.ijoomla.com.com/forum/index/
-------------------------------------------------------------------------*/

defined( '_JEXEC' ) or die( 'Restricted access' );

require (JPATH_SITE.DIRECTORY_SEPARATOR."components".DIRECTORY_SEPARATOR."com_guru".DIRECTORY_SEPARATOR."helpers".DIRECTORY_SEPARATOR."fpdf1.php");
/************************************/
/* global functions */
/************************************/

function hex2dec($color = "#000000"){
    $tbl_color = array();
    $tbl_color['R']=hexdec(substr($color, 1, 2));
    $tbl_color['G']=hexdec(substr($color, 3, 2));
    $tbl_color['B']=hexdec(substr($color, 5, 2));
    return $tbl_color;
}

function px2mm($px){
    return $px*25.4/72;
}

function txtentities($html){
    $trans = get_html_translation_table(HTML_ENTITIES);
    $trans = array_flip($trans);
    return strtr($html, $trans);
}


/************************************/
/* main class createPDF             */
/************************************/
class createPDF {

    function __construct($_html) {
        // main vars
        $this->html=$_html;               // html text to convert to PDF
        $this->from='iso-8859-2';         // input encoding
        $this->to='cp1250';               // output encoding
        $this->useiconv=false;            // use iconv
        $this->bi=true;                   // support bold and italic tags
    }

    function _convert($s) {
        if ($this->useiconv) 
            return iconv($this->from,$this->to,$s); 
        else 
            return $s;
    }

    function run() {
        // change some win codes, and xhtml into html
        $str=array(
        '<br />' => '<br>',
        '<hr />' => '<hr>',
        '[r]' => '<red>',
        '[/r]' => '</red>',
        '[l]' => '<blue>',
        '[/l]' => '</blue>',
        '&#8220;' => '"',
        '&#8221;' => '"',
        '&#8222;' => '"',
        '&#8230;' => '...',
        '&#8217;' => '\''
        );
        foreach ($str as $_from => $_to) $this->html = str_replace($_from,$_to,$this->html);

        $pdf=new PDF('P','mm','A4');
        $pdf->SetDisplayMode('real');
        $pdf->AddPage();


        // html
		
        $pdf->WriteHTML($this->_convert(stripslashes($this->html)),$this->bi);

        // output
        $pdf->Output();

        // stop processing
        exit;
    }
} 

/************************************/
/* class PDF                        */
/************************************/
class PDF extends FPDF
{
    var $B;
    var $I;
    var $U;
    var $HREF;
    var $fontList;
    var $issetfont;
    var $issetcolor;

    function __construct($orientation='P',$unit='mm',$format='A4')
    {
        $this->duplicate_construct($orientation,$unit,$format);
        $this->B=0;
        $this->I=0;
        $this->U=0;
        $this->HREF='';
        $this->PRE=false;
        $this->SetFont('Times','',12);
        $this->fontlist=array("Times","Courier");
        $this->issetfont=false;
        $this->issetcolor=false;
    }

    function WriteHTML($html,$bi)
    {

        //remove all unsupported tags
        $this->bi=$bi;
        if ($bi)
            $html=strip_tags($html,"<a><img><p><br><font><tr><blockquote><h1><h2><h3><h4><pre><red><blue><ul><li><hr><b><i><u><strong><em>"); 
        else
            $html=strip_tags($html,"<a><img><p><br><font><tr><blockquote><h1><h2><h3><h4><pre><red><blue><ul><li><hr>"); 
        $html=str_replace("\n",' ',$html); //replace carriage returns by spaces
        // debug
        if (isset($this->debug) && $this->debug){
			echo $html;
			exit;
		}

        $html = str_replace('&trade;','™',$html);
        $html = str_replace('&copy;','©',$html);
        $html = str_replace('&euro;','€',$html);

        $a=preg_split('/<(.*)>/U',$html,-1,PREG_SPLIT_DELIM_CAPTURE);
        $skip=false;
        foreach($a as $i=>$e)
        {
            if (!$skip) {
                if($this->HREF)
                    $e=str_replace("\n","",str_replace("\r","",$e));
                if($i%2==0)
                {
                    // new line
					if(isset($this->ALIGN) && $this->ALIGN == 'left'){	
						$this->Cell(100, 4, $e, 0, 1, 'L');
			   		}
                    if($this->PRE)
                        $e=str_replace("\r","\n",$e);
                    else
                        $e=str_replace("\r","",$e);
                    //Text
                    if($this->HREF) {
                        $this->PutLink($this->HREF,$e);
                        $skip=true;
                    } else 
                        $this->Write(5,stripslashes(txtentities($e)));
                } else {
                    //Tag
                    if (substr(trim($e),0,1)=='/')
                        $this->CloseTag(strtoupper(substr($e,strpos($e,'/'))));
                    else {
                        //Extract attributes
                        $a2=explode(' ',$e);
                        $tag=strtoupper(array_shift($a2));
                        $attr=array();
                        foreach($a2 as $v) {
                            if(preg_match('/([^=]*)=["\']?([^"\']*)/',$v,$a3))
                                $attr[strtoupper($a3[1])]=$a3[2];
                        }
                        $this->OpenTag($tag,$attr);
                    }
                }
            } else {
                $this->HREF='';
                $skip=false;
            }
        }
    }

    function OpenTag($tag,$attr)
    {
        //Opening tag
        switch($tag){
            case 'STRONG':
            case 'B':
                if ($this->bi)
                    $this->SetStyle('B',true);
                else
                    $this->SetStyle('U',true);
                break;
            case 'H1':
                $this->Ln(5);
                $this->SetTextColor(150,0,0);
                $this->SetFontSize(22);
                break;
            case 'H2':
                $this->Ln(5);
                $this->SetFontSize(18);
                $this->SetStyle('U',true);
                break;
            case 'H3':
                $this->Ln(5);
                $this->SetFontSize(16);
                $this->SetStyle('U',true);
                break;
            case 'H4':
                $this->Ln(5);
                $this->SetTextColor(102,0,0);
                $this->SetFontSize(14);
                if ($this->bi)
                    $this->SetStyle('B',true);
                break;
            case 'PRE':
                $this->SetFont('Courier','',11);
                $this->SetFontSize(11);
                $this->SetStyle('B',false);
                $this->SetStyle('I',false);
                $this->PRE=true;
                break;
            case 'RED':
                $this->SetTextColor(255,0,0);
                break;
            case 'BLOCKQUOTE':
                $this->mySetTextColor(100,0,45);
                $this->Ln(3);
                break;
            case 'BLUE':
                $this->SetTextColor(0,0,255);
                break;
            case 'I':
            case 'EM':
                if ($this->bi)
                    $this->SetStyle('I',true);
                break;
            case 'U':
                $this->SetStyle('U',true);
                break;
            case 'A':
                $this->HREF=$attr['HREF'];
                break;
            case 'IMG':
                if(isset($attr['SRC']) && (isset($attr['WIDTH']) || isset($attr['HEIGHT']))) {
                    if(!isset($attr['WIDTH']))
                        $attr['WIDTH'] = 0;
                    if(!isset($attr['HEIGHT']))
                        $attr['HEIGHT'] = 0;
                    $this->Image($attr['SRC'], $this->GetX(), $this->GetY(), px2mm($attr['WIDTH']), px2mm($attr['HEIGHT']));
                    $this->Ln(3);
                }
                break;
            case 'LI':
                $this->Ln(2);
                $this->SetTextColor(190,0,0);
                $this->Write(5,'     » ');
                $this->mySetTextColor(-1);
                break;
            case 'TR':
                $this->Ln(7);
                $this->PutLine();
                break;
            case 'BR':
                $this->Ln(4);
                break;
            case 'P':
                $this->Ln(10);
				$this->SetLeftMargin(60);
                break;
            case 'HR':
                $this->PutLine();
                break;
            case 'FONT':
                if (isset($attr['COLOR']) && $attr['COLOR']!='') {
                    $coul=hex2dec($attr['COLOR']);
                    $this->mySetTextColor($coul['R'],$coul['G'],$coul['B']);
                    $this->issetcolor=true;
                }
                if (isset($attr['FACE']) && in_array(strtolower($attr['FACE']), $this->fontlist)) {
                    $this->SetFont(strtolower($attr['FACE']));
                    $this->issetfont=true;
                }
                break;
        }
    }

    function CloseTag($tag)
    {
        //Closing tag
        if ($tag=='H1' || $tag=='H2' || $tag=='H3' || $tag=='H4'){
            $this->Ln(6);
            $this->SetFont('Times','',12);
            $this->SetFontSize(12);
            $this->SetStyle('U',false);
            $this->SetStyle('B',false);
            $this->mySetTextColor(-1);
        }
        if ($tag=='PRE'){
            $this->SetFont('Times','',12);
            $this->SetFontSize(12);
            $this->PRE=false;
        }
        if ($tag=='RED' || $tag=='BLUE')
            $this->mySetTextColor(-1);
        if ($tag=='BLOCKQUOTE'){
            $this->mySetTextColor(0,0,0);
            $this->Ln(3);
        }
        if($tag=='STRONG')
            $tag='B';
        if($tag=='EM')
            $tag='I';
        if((!$this->bi) && $tag=='B')
            $tag='U';
        if($tag=='B' || $tag=='I' || $tag=='U')
            $this->SetStyle($tag,false);
        if($tag=='A')
            $this->HREF='';
        if($tag=='FONT'){
            if ($this->issetcolor==true) {
                $this->SetTextColor(0,0,0);
            }
            if ($this->issetfont) {
                $this->SetFont('Times','',12);
                $this->issetfont=false;
            }
        }
    }

    function Footer()
    {
        //Go to 1.5 cm from bottom
        $this->SetY(-15);
        //Select Arial italic 8
        $this->SetFont('Times','',8);
        //Print centered page number
        $this->SetTextColor(0,0,0);
        $this->SetTextColor(0,0,180);
        $this->mySetTextColor(-1);
    }

    function Header()
    {
        //Select Arial bold 15
        $this->SetTextColor(0,0,0);
        $this->SetFont('Times','',10);
        $this->Ln(4);
        $this->Ln(7);
        $this->Line($this->GetX(),$this->GetY(),$this->GetX()+187,$this->GetY());
        //Line break
        $this->Ln(12);
        $this->SetFont('Times','',12);
        $this->mySetTextColor(-1);
    }

    function SetStyle($tag,$enable)
    {
        $this->$tag+=($enable ? 1 : -1);
        $style='';
        foreach(array('B','I','U') as $s) {
            if($this->$s>0)
                $style.=$s;
        }
        $this->SetFont('',$style);
    }

    function PutLink($URL,$txt)
    {
        //Put a hyperlink
        $this->SetTextColor(0,0,255);
        $this->SetStyle('U',true);
        $this->Write(5,$txt,$URL);
        $this->SetStyle('U',false);
        $this->mySetTextColor(-1);
    }

    function PutLine()
    {
        $this->Ln(2);
        $this->Line($this->GetX(),$this->GetY(),$this->GetX()+187,$this->GetY());
        $this->Ln(3);
    }

    function mySetTextColor($r,$g=0,$b=0){
        static $_r=0, $_g=0, $_b=0;

        if ($r==-1) 
            $this->SetTextColor($_r,$_g,$_b);
        else {
            $this->SetTextColor($r,$g,$b);
            $_r=$r;
            $_g=$g;
            $_b=$b;
        }
    }

    function PutMainTitle($title) {
        if (strlen($title)>55)
            $title=substr($title,0,55)."...";
        $this->SetTextColor(33,32,95);
        $this->SetFontSize(20);
        $this->SetFillColor(255,204,120);
        $this->Cell(0,20,$title,1,1,"C",1);
        $this->SetFillColor(255,255,255);
        $this->SetFontSize(12);
        $this->Ln(5);
    }

    function PutMinorHeading($title) {
        $this->SetFontSize(12);
        $this->Cell(0,5,$title,0,1,"C");
        $this->SetFontSize(12);
    }

    function PutMinorTitle($title,$url='') {
        $title=str_replace('http://','',$title);
        if (strlen($title)>70)
            if (!(strrpos($title,'/')==false))
                $title=substr($title,strrpos($title,'/')+1);
        $title=substr($title,0,70);
        $this->SetFontSize(16);
        if ($url!='') {
            $this->SetStyle('U',false);
            $this->SetTextColor(0,0,180);
            $this->Cell(0,6,$title,0,1,"C",0,$url);
            $this->SetTextColor(0,0,0);
            $this->SetStyle('U',false);
        } else
            $this->Cell(0,6,$title,0,1,"C",0);
        $this->SetFontSize(12);
        $this->Ln(4);
    }
} // class PDF

?>