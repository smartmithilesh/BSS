<?php
/*******************************************************************************
* FPDF - Free PDF (simplified version for Bharat Book Depot)
* Based on FPDF 1.86 by Olivier Plathey
*******************************************************************************/

if(!defined('FPDF_FONTPATH'))
    define('FPDF_FONTPATH', dirname(__FILE__).'/font/');

class FPDF
{
    var $page;
    var $n;
    var $offsets;
    var $buffer;
    var $pages;
    var $state;
    var $compress;
    var $k;
    var $DefOrientation;
    var $CurOrientation;
    var $StdPageSizes;
    var $DefPageSize;
    var $CurPageSize;
    var $CurRotation;
    var $PageInfo;
    var $wPt,$hPt;
    var $w,$h;
    var $lMargin;
    var $tMargin;
    var $rMargin;
    var $cMargin;
    var $x,$y;
    var $lasth;
    var $LineWidth;
    var $fontpath;
    var $CoreFonts;
    var $fonts;
    var $FontFiles;
    var $CurrentFont;
    var $encodings;
    var $cmaps;
    var $FontFamily;
    var $FontStyle;
    var $FontSizePt;
    var $FontSize;
    var $DrawColor;
    var $FillColor;
    var $TextColor;
    var $ColorFlag;
    var $WithAlpha;
    var $ws;
    var $images;
    var $PageLinks;
    var $links;
    var $AutoPageBreak;
    var $bMargin;
    var $PageBreakTrigger;
    var $InHeader;
    var $InFooter;
    var $AliasNbPages;
    var $ZoomMode;
    var $LayoutMode;
    var $metadata;
    var $PDFVersion;
    var $underline;

    function __construct($orientation='P', $unit='mm', $size='A4')
    {
        // Initialization
        $this->state       = 0;
        $this->page        = 0;
        $this->n           = 2;
        $this->buffer      = '';
        $this->pages       = [];
        $this->PageInfo    = [];
        $this->fonts       = [];
        $this->FontFiles   = [];
        $this->encodings   = [];
        $this->cmaps       = [];
        $this->images      = [];
        $this->links       = [];
        $this->PageLinks   = [];
        $this->InHeader    = false;
        $this->InFooter    = false;
        $this->lasth       = 0;
        $this->FontFamily  = '';
        $this->FontStyle   = '';
        $this->FontSizePt  = 12;
        $this->underline   = false;
        $this->DrawColor   = '0 G';
        $this->FillColor   = '0 g';
        $this->TextColor   = '0 g';
        $this->ColorFlag   = false;
        $this->WithAlpha   = false;
        $this->ws          = 0;
        $this->AliasNbPages= '';
        $this->PDFVersion  = '1.3';

        // Units
        if($unit=='pt')      $this->k=1;
        elseif($unit=='mm')  $this->k=72/25.4;
        elseif($unit=='cm')  $this->k=72/2.54;
        elseif($unit=='in')  $this->k=72;
        else $this->Error('Incorrect unit: '.$unit);

        // Page sizes
        $this->StdPageSizes = [
            'a3'=>[841.89,1190.55],
            'a4'=>[595.28,841.89],
            'a5'=>[420.94,595.28],
            'letter'=>[612,792],
            'legal'=>[612,1008]
        ];
        $size = $this->_getpagesize($size);
        $this->DefPageSize = $size;
        $this->CurPageSize = $size;

        // Orientation
        $orientation = strtolower($orientation);
        if($orientation=='p' || $orientation=='portrait') {
            $this->DefOrientation = 'P';
            $this->w = $size[0]; $this->h = $size[1];
        } elseif($orientation=='l' || $orientation=='landscape') {
            $this->DefOrientation = 'L';
            $this->w = $size[1]; $this->h = $size[0];
        } else $this->Error('Incorrect orientation: '.$orientation);
        $this->CurOrientation = $this->DefOrientation;
        $this->wPt = $this->w*$this->k;
        $this->hPt = $this->h*$this->k;
        $this->CurRotation = 0;

        // Margins (1 cm by default)
        $margin = 28.35/$this->k;
        $this->SetMargins($margin,$margin);
        $this->cMargin = $margin/10;
        $this->LineWidth = .567/$this->k;
        $this->SetAutoPageBreak(true,2*$margin);
        $this->SetDisplayMode('default');
        $this->SetCompression(true);
        $this->metadata = ['Producer'=>'FPDF', 'CreationDate'=>'D:'.@date('YmdHis')];

        // Core fonts
        $this->CoreFonts = [
            'courier','courierB','courierI','courierBI',
            'helvetica','helveticaB','helveticaI','helveticaBI',
            'times','timesB','timesI','timesBI',
            'symbol','zapfdingbats','arial','arialB','arialI','arialBI'
        ];
        $this->fontpath = FPDF_FONTPATH;
    }

    function SetMargins($left,$top,$right=-1)
    {
        $this->lMargin = $left;
        $this->tMargin = $top;
        $this->rMargin = ($right==-1) ? $left : $right;
    }

    function SetLeftMargin($margin)     { $this->lMargin = $margin; if($this->page>0&&$this->x<$margin) $this->x=$margin; }
    function SetTopMargin($margin)      { $this->tMargin = $margin; }
    function SetRightMargin($margin)    { $this->rMargin = $margin; }
    function SetAutoPageBreak($auto,$margin=0) { $this->AutoPageBreak=$auto; $this->bMargin=$margin; $this->PageBreakTrigger=$this->h-$margin; }
    function SetDisplayMode($zoom,$layout='default') { $this->ZoomMode=$zoom; $this->LayoutMode=$layout; }
    function SetCompression($compress)  { $this->compress = $compress && function_exists('gzcompress'); }
    function SetTitle($title,$isUTF8=false)   { $this->metadata['Title']   = $isUTF8 ? $title : utf8_encode($title); }
    function SetAuthor($author,$isUTF8=false) { $this->metadata['Author']  = $isUTF8 ? $author : utf8_encode($author); }
    function SetCreator($creator,$isUTF8=false){ $this->metadata['Creator']= $isUTF8 ? $creator : utf8_encode($creator); }

    function SetFont($family,$style='',$size=0)
    {
        $family = strtolower($family);
        if($family=='') $family=$this->FontFamily;
        if($family=='arial') $family='helvetica';
        elseif($family=='symbol'||$family=='zapfdingbats') $style='';
        $style = strtoupper($style);
        if(strpos($style,'U')!==false) { $this->underline=true; $style=str_replace('U','',$style); } else $this->underline=false;
        if($style=='IB') $style='BI';
        if($size==0) $size=$this->FontSizePt;
        if($this->FontFamily==$family&&$this->FontStyle==$style&&$this->FontSizePt==$size) return;
        $fontkey = $family.$style;
        if(!isset($this->fonts[$fontkey])) {
            // Standard core font metrics (widths for Helvetica)
            $name = $this->_fontname($family,$style);
            $cw   = $this->_corefontwidths($family,$style);
            $i    = count($this->fonts)+1;
            $this->fonts[$fontkey] = ['i'=>$i,'type'=>'core','name'=>$name,'up'=>-100,'ut'=>50,'cw'=>$cw];
        }
        $this->FontFamily  = $family;
        $this->FontStyle   = $style;
        $this->FontSizePt  = $size;
        $this->FontSize    = $size/$this->k;
        $this->CurrentFont = &$this->fonts[$fontkey];
        if($this->page>0) $this->_out(sprintf('BT /F%d %.2F Tf ET',$this->CurrentFont['i'],$this->FontSizePt));
    }

    function _fontname($family,$style)
    {
        $names = [
            'helvetica'  => [''=>'Helvetica',  'B'=>'Helvetica-Bold',  'I'=>'Helvetica-Oblique',  'BI'=>'Helvetica-BoldOblique'],
            'courier'    => [''=>'Courier',     'B'=>'Courier-Bold',    'I'=>'Courier-Oblique',    'BI'=>'Courier-BoldOblique'],
            'times'      => [''=>'Times-Roman', 'B'=>'Times-Bold',      'I'=>'Times-Italic',       'BI'=>'Times-BoldItalic'],
            'symbol'     => [''=>'Symbol'],
            'zapfdingbats'=>[''=>'ZapfDingbats'],
        ];
        return $names[$family][$style] ?? 'Helvetica';
    }

    function _corefontwidths($family,$style)
    {
        // Helvetica character widths (used for all fonts as approximation)
        $cw=array_fill(0,256,278);
        $cw[32]=278;$cw[33]=278;$cw[34]=355;$cw[35]=556;$cw[36]=556;$cw[37]=889;$cw[38]=667;$cw[39]=191;
        $cw[40]=333;$cw[41]=333;$cw[42]=389;$cw[43]=584;$cw[44]=278;$cw[45]=333;$cw[46]=278;$cw[47]=278;
        $cw[48]=556;$cw[49]=556;$cw[50]=556;$cw[51]=556;$cw[52]=556;$cw[53]=556;$cw[54]=556;$cw[55]=556;
        $cw[56]=556;$cw[57]=556;$cw[58]=278;$cw[59]=278;$cw[60]=584;$cw[61]=584;$cw[62]=584;$cw[63]=556;
        $cw[64]=1015;$cw[65]=667;$cw[66]=667;$cw[67]=722;$cw[68]=722;$cw[69]=667;$cw[70]=611;$cw[71]=778;
        $cw[72]=722;$cw[73]=278;$cw[74]=500;$cw[75]=667;$cw[76]=556;$cw[77]=833;$cw[78]=722;$cw[79]=778;
        $cw[80]=667;$cw[81]=778;$cw[82]=722;$cw[83]=667;$cw[84]=611;$cw[85]=722;$cw[86]=667;$cw[87]=944;
        $cw[88]=667;$cw[89]=667;$cw[90]=611;$cw[91]=278;$cw[92]=278;$cw[93]=278;$cw[94]=469;$cw[95]=556;
        $cw[96]=333;$cw[97]=556;$cw[98]=556;$cw[99]=500;$cw[100]=556;$cw[101]=556;$cw[102]=278;$cw[103]=556;
        $cw[104]=556;$cw[105]=222;$cw[106]=222;$cw[107]=500;$cw[108]=222;$cw[109]=833;$cw[110]=556;$cw[111]=556;
        $cw[112]=556;$cw[113]=556;$cw[114]=333;$cw[115]=500;$cw[116]=278;$cw[117]=556;$cw[118]=500;$cw[119]=722;
        $cw[120]=500;$cw[121]=500;$cw[122]=500;$cw[123]=334;$cw[124]=260;$cw[125]=334;$cw[126]=584;
        // Bold adjustments
        if(strpos($style,'B')!==false) foreach($cw as $k=>$v) $cw[$k]=(int)($v*1.05);
        return $cw;
    }

    function GetStringWidth($s)
    {
        $s  = (string)$s;
        $cw = &$this->CurrentFont['cw'];
        $w  = 0;
        $l  = strlen($s);
        for($i=0;$i<$l;$i++) $w += $cw[ord($s[$i])];
        return $w*$this->FontSize/1000;
    }

    function SetDrawColor($r,$g=-1,$b=-1)
    {
        if(($r==0&&$g==0&&$b==0)||$g==-1) $this->DrawColor=sprintf('%.3F G',$r/255);
        else $this->DrawColor=sprintf('%.3F %.3F %.3F RG',$r/255,$g/255,$b/255);
        if($this->page>0) $this->_out($this->DrawColor);
    }

    function SetFillColor($r,$g=-1,$b=-1)
    {
        if(($r==0&&$g==0&&$b==0)||$g==-1) $this->FillColor=sprintf('%.3F g',$r/255);
        else $this->FillColor=sprintf('%.3F %.3F %.3F rg',$r/255,$g/255,$b/255);
        $this->ColorFlag=($this->FillColor!=$this->TextColor);
        if($this->page>0) $this->_out($this->FillColor);
    }

    function SetTextColor($r,$g=-1,$b=-1)
    {
        if(($r==0&&$g==0&&$b==0)||$g==-1) $this->TextColor=sprintf('%.3F g',$r/255);
        else $this->TextColor=sprintf('%.3F %.3F %.3F rg',$r/255,$g/255,$b/255);
        $this->ColorFlag=($this->FillColor!=$this->TextColor);
    }

    function SetLineWidth($width) { $this->LineWidth=$width; if($this->page>0) $this->_out(sprintf('%.2F w',$width*$this->k)); }

    function Line($x1,$y1,$x2,$y2)
    {
        $this->_out(sprintf('%.2F %.2F m %.2F %.2F l S',$x1*$this->k,($this->h-$y1)*$this->k,$x2*$this->k,($this->h-$y2)*$this->k));
    }

    function Rect($x,$y,$w,$h,$style='')
    {
        if($style=='F') $op='f';
        elseif($style=='FD'||$style=='DF') $op='B';
        else $op='S';
        $this->_out(sprintf('%.2F %.2F %.2F %.2F re %s',$x*$this->k,($this->h-$y-$h)*$this->k,$w*$this->k,$h*$this->k,$op));
    }

    function Image($file,$x=null,$y=null,$w=0,$h=0,$type='',$link='')
    {
        if($file=='') $this->Error('Image file name is empty');
        if(!isset($this->images[$file])) {
            if($type=='') {
                $pos=strrpos($file,'.');
                if($pos===false) $this->Error('Image file has no extension and no type was specified: '.$file);
                $type=substr($file,$pos+1);
            }
            $type=strtolower($type);
            if($type=='jpeg') $type='jpg';
            if($type!='jpg') $this->Error('Unsupported image type: '.$type);
            $info=$this->_parsejpg($file);
            $info['i']=count($this->images)+1;
            $this->images[$file]=$info;
        } else {
            $info=$this->images[$file];
        }
        if($w==0 && $h==0) {
            $w=$info['w']*25.4/96;
            $h=$info['h']*25.4/96;
        } elseif($w==0) {
            $w=$h*$info['w']/$info['h'];
        } elseif($h==0) {
            $h=$w*$info['h']/$info['w'];
        }
        if($x===null) $x=$this->x;
        if($y===null) {
            if($this->y+$h>$this->PageBreakTrigger && !$this->InHeader && !$this->InFooter && $this->AutoPageBreak) {
                $x2=$this->x;
                $this->AddPage($this->CurOrientation,$this->CurPageSize,$this->CurRotation);
                $this->x=$x2;
            }
            $y=$this->y;
            $this->y+=$h;
        }
        $this->_out(sprintf('q %.2F 0 0 %.2F %.2F %.2F cm /I%d Do Q',
            $w*$this->k,$h*$this->k,$x*$this->k,($this->h-($y+$h))*$this->k,$info['i']));
    }

    function AddPage($orientation='',$size='',$rotation=0)
    {
        if($this->state==3) $this->Error('The document is closed');
        $family=$this->FontFamily; $style=$this->FontStyle; $fontsize=$this->FontSizePt;
        $lw=$this->LineWidth; $dc=$this->DrawColor; $fc=$this->FillColor; $tc=$this->TextColor; $cf=$this->ColorFlag;
        if($this->page>0) { $this->InFooter=true; $this->Footer(); $this->InFooter=false; $this->_endpage(); }
        $this->_beginpage($orientation,$size,$rotation);
        $this->_out('2 J'); $this->LineWidth=$lw; $this->_out(sprintf('%.2F w',$lw*$this->k));
        if($family) $this->SetFont($family,$style,$fontsize);
        if($this->DrawColor!='0 G') { $this->DrawColor=$dc; $this->_out($dc); }
        if($this->FillColor!='0 g') { $this->FillColor=$fc; $this->_out($fc); }
        $this->TextColor=$tc; $this->ColorFlag=$cf;
        $this->InHeader=true; $this->Header(); $this->InHeader=false;
        if($this->lasth==0) $this->lasth=$this->FontSize*1.5;
    }

    function Header() {}
    function Footer() {}

    function PageNo() { return $this->page; }

    function SetX($x) { $this->x=($x>=0)?$x:$this->w+$x; }
    function SetY($y,$resetX=true) { if($y>=0) $this->y=$y; else $this->y=$this->h+$y; if($resetX) $this->x=$this->lMargin; }
    function SetXY($x,$y) { $this->SetX($x); $this->SetY($y,false); }
    function GetX() { return $this->x; }
    function GetY() { return $this->y; }

    function Cell($w,$h=0,$txt='',$border=0,$ln=0,$align='',$fill=false,$link='')
    {
        $k=$this->k;
        if($this->y+$h>$this->PageBreakTrigger&&!$this->InHeader&&!$this->InFooter&&$this->AutoPageBreak) {
            $x=$this->x; $ws=$this->ws;
            if($ws>0) { $this->ws=0; $this->_out('0 Tw'); }
            $this->AddPage($this->CurOrientation,$this->CurPageSize,$this->CurRotation);
            $this->x=$x;
            if($ws>0) { $this->ws=$ws; $this->_out(sprintf('%.3F Tw',$ws*$k)); }
        }
        if($w==0) $w=$this->w-$this->rMargin-$this->x;
        $s='';
        if($fill||$border==1) {
            if($fill) $op=($border==1)?'B':'f';
            else $op='S';
            $s=sprintf('%.2F %.2F %.2F %.2F re %s ',$this->x*$k,($this->h-$this->y)*$k,$w*$k,-$h*$k,$op);
        }
        if(is_string($border)) {
            $x=$this->x; $y=$this->y;
            if(strpos($border,'L')!==false) $s.=sprintf('%.2F %.2F m %.2F %.2F l S ',$x*$k,($this->h-$y)*$k,$x*$k,($this->h-($y+$h))*$k);
            if(strpos($border,'T')!==false) $s.=sprintf('%.2F %.2F m %.2F %.2F l S ',$x*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-$y)*$k);
            if(strpos($border,'R')!==false) $s.=sprintf('%.2F %.2F m %.2F %.2F l S ',($x+$w)*$k,($this->h-$y)*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
            if(strpos($border,'B')!==false) $s.=sprintf('%.2F %.2F m %.2F %.2F l S ',$x*$k,($this->h-($y+$h))*$k,($x+$w)*$k,($this->h-($y+$h))*$k);
        }
        if($txt!=='') {
            $txt=(string)$txt;
            if(!isset($this->CurrentFont)) $this->Error('No font has been set');
            $dx=0;
            if($align=='R') $dx=$w-$this->cMargin-$this->GetStringWidth($txt);
            elseif($align=='C') $dx=($w-$this->GetStringWidth($txt))/2;
            else $dx=$this->cMargin;
            if($this->ColorFlag) $s.='q '.$this->TextColor.' ';
            $s.=sprintf('BT %.2F %.2F Td /F%d %.2F Tf (%s) Tj ET',
                ($this->x+$dx)*$k,($this->h-$this->y-0.5*$h-0.3*$this->FontSize)*$k,
                $this->CurrentFont['i'],$this->FontSizePt,$this->_escape($txt));
            if($this->underline) $s.=' '.$this->_dounderline($this->x+$dx,$this->y+0.5*$h+0.3*$this->FontSize,$txt);
            if($this->ColorFlag) $s.=' Q';
        }
        if($s) $this->_out($s);
        $this->lasth=$h;
        if($ln>0) { $this->y+=$h; if($ln==1) $this->x=$this->lMargin; }
        else $this->x+=$w;
    }

    function MultiCell($w,$h,$txt,$border=0,$align='J',$fill=false)
    {
        $cw=&$this->CurrentFont['cw'];
        if($w==0) $w=$this->w-$this->rMargin-$this->x;
        $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
        $s=str_replace("\r",'',$txt);
        $nb=strlen($s);
        if($nb>0&&$s[$nb-1]=="\n") $nb--;
        $b=0;
        if($border) { if($border==1){$border='LTRB';$b='LRT';$b2='LR';} else{$b2='';if(strpos($border,'L')!==false)$b2.='L';if(strpos($border,'R')!==false)$b2.='R';$b=(strpos($border,'T')!==false)?$b2.'T':$b2;}}
        $sep=-1; $i=0; $j=0; $l=0; $ns=0; $nl=1;
        while($i<$nb) {
            $c=$s[$i];
            if($c=="\n") { if($this->ws>0){$this->ws=0;$this->_out('0 Tw');} $this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill); $i++;$sep=-1;$j=$i;$l=0;$ns=0;$nl++;if($border&&$nl==2)$b=$b2;continue; }
            if($c==' '){$sep=$i;$ls=$l;$ns++;}
            $l+=$cw[ord($c)];
            if($l>$wmax) {
                if($sep==-1) { if($i==$j) $i++; if($this->ws>0){$this->ws=0;$this->_out('0 Tw');} $this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill); }
                else { if($align=='J'){$this->ws=($ns>1)?(($wmax-$ls)/1000*$this->FontSize/($ns-1)):0;$this->_out(sprintf('%.3F Tw',$this->ws*$this->k));} $this->Cell($w,$h,substr($s,$j,$sep-$j),$b,2,$align,$fill);$i=$sep+1; }
                $sep=-1;$j=$i;$l=0;$ns=0;$nl++;if($border&&$nl==2)$b=$b2;
            } else $i++;
        }
        if($this->ws>0){$this->ws=0;$this->_out('0 Tw');}
        if($border&&strpos($border,'B')!==false) $b.='B';
        $this->Cell($w,$h,substr($s,$j,$i-$j),$b,2,$align,$fill);
        $this->x=$this->lMargin;
    }

    function Write($h,$txt,$link='')
    {
        $cw=&$this->CurrentFont['cw'];
        $w=$this->w-$this->rMargin-$this->x;
        $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
        $s=str_replace("\r",'',$txt);
        $nb=strlen($s);
        $sep=-1;$i=0;$j=0;$l=0;$nl=1;
        while($i<$nb) {
            $c=$s[$i];
            if($c=="\n"){$this->Cell($w,$h,substr($s,$j,$i-$j),0,2,'',false,$link);$i++;if($nl==1){$this->x=$this->lMargin;$w=$this->w-$this->rMargin-$this->x;$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;}$nl++;$j=$i;$sep=-1;$l=0;continue;}
            if($c==' ') $sep=$i;
            $l+=$cw[ord($c)];
            if($l>$wmax) {
                if($sep==-1){if($this->x>$this->lMargin){$this->AddPage($this->CurOrientation,$this->CurPageSize,$this->CurRotation);$w=$this->w-2*$this->lMargin;$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;$nl++;$j=$i;$sep=-1;$l=0;continue;}if($i==$j)$i++;$this->Cell($w,$h,substr($s,$j,$i-$j),0,2,'',false,$link);}
                else{$this->Cell($w,$h,substr($s,$j,$sep-$j),0,2,'',false,$link);$i=$sep+1;}
                $this->x=$this->lMargin;$w=$this->w-$this->rMargin-$this->x;$wmax=($w-2*$this->cMargin)*1000/$this->FontSize;$sep=-1;$j=$i;$l=0;if($nl==1)$nl++;
            }else $i++;
        }
        if($i!=$j) $this->Cell($this->GetStringWidth(substr($s,$j)),$h,substr($s,$j),0,0,'',false,$link);
    }

    function Ln($h=null) { $this->x=$this->lMargin; if($h===null) $this->y+=$this->lasth; else $this->y+=$h; }

    function Output($dest='',$name='',$isUTF8=false)
    {
        $this->Close();
        if(strlen($name)==1&&strlen($dest)!=1){ $tmp=$dest;$dest=$name;$name=$tmp; }
        $dest=strtoupper($dest);
        if($dest=='') { if(php_sapi_name()!='cli') $dest='I'; else $dest='F'; if($name=='') $name='doc.pdf'; }
        switch($dest) {
            case 'I':
                $this->_checkoutput();
                header('Content-Type: application/pdf');
                header('Content-Disposition: inline; filename="'.addslashes(basename($name)).'"');
                header('Cache-Control: private, max-age=0, must-revalidate');
                echo $this->buffer;
                break;
            case 'D':
                $this->_checkoutput();
                header('Content-Type: application/x-download');
                header('Content-Disposition: attachment; filename="'.addslashes(basename($name)).'"');
                header('Cache-Control: private, max-age=0, must-revalidate');
                header('Pragma: public');
                echo $this->buffer;
                break;
            case 'F':
                if(!file_put_contents($name,$this->buffer)) $this->Error('Unable to create output file: '.$name);
                break;
            case 'S':
                return $this->buffer;
            default:
                $this->Error('Incorrect output destination: '.$dest);
        }
        return '';
    }

    function Close()
    {
        if($this->state==3) return;
        if($this->page==0) $this->AddPage();
        $this->InFooter=true; $this->Footer(); $this->InFooter=false;
        $this->_endpage(); $this->_enddoc();
    }

    function Error($msg) { throw new Exception('FPDF error: '.$msg); }

    // ─── Protected/Private ─────────────────────────────────

    function _checkoutput() { if(PHP_SAPI!='cli') { if(headers_sent($file,$line)) $this->Error("Some data has already been output, can't send PDF file (output started at $file:$line)"); } if(ob_get_length()) { if(ob_get_contents()===false) $this->Error("Some data has already been output, can't send PDF file"); ob_end_clean(); } }

    function _getpagesize($size)
    {
        if(is_string($size)) {
            $size=strtolower($size);
            if(!isset($this->StdPageSizes[$size])) $this->Error('Unknown page size: '.$size);
            $a=$this->StdPageSizes[$size];
            return [$a[0]/$this->k,$a[1]/$this->k];
        } else return [$size[0]/$this->k,$size[1]/$this->k]; // already in user units
    }

    function _beginpage($orientation,$size,$rotation)
    {
        $this->page++;
        $this->pages[$this->page]='';
        $this->PageLinks[$this->page]=[];
        $this->state=2;
        $this->x=$this->lMargin; $this->y=$this->tMargin;
        $this->lasth=0;
        // Page size
        if($size=='') $size=$this->DefPageSize; else $size=$this->_getpagesize($size);
        // Orientation
        if($orientation=='') $orientation=$this->DefOrientation;
        else $orientation=strtoupper($orientation[0]);
        if($orientation=='P') { $this->w=$size[0];$this->h=$size[1]; }
        else { $this->w=$size[1];$this->h=$size[0]; }
        $this->wPt=$this->w*$this->k; $this->hPt=$this->h*$this->k;
        $this->PageBreakTrigger=$this->h-$this->bMargin;
        $this->CurOrientation=$orientation; $this->CurPageSize=$size; $this->CurRotation=$rotation;
        $this->PageInfo[$this->page]=['size'=>[$this->wPt,$this->hPt],'rotation'=>$rotation];
    }

    function _endpage() { $this->state=1; }

    function _escape($s) { $s=str_replace('\\','\\\\',$s); $s=str_replace(')','\\)',$s); $s=str_replace('(','\\(',$s); $s=str_replace("\r","\\r",$s); return $s; }

    function _dounderline($x,$y,$txt) { $up=$this->CurrentFont['up']; $ut=$this->CurrentFont['ut']; $w=$this->GetStringWidth($txt)+$this->ws*substr_count($txt,' '); return sprintf('%.2F %.2F %.2F %.2F re f',$x*$this->k,($this->h-($y-$up/1000*$this->FontSize))*$this->k,$w*$this->k,-$ut/1000*$this->FontSizePt); }

    function _out($s) { if($this->state==2) $this->pages[$this->page].=$s."\n"; elseif($this->state==1) $this->_put($s); }
    function _put($s) { $this->buffer.=$s."\n"; }
    function _getoffset() { return strlen($this->buffer); }

    function _newobj($n=null)
    {
        if($n===null) $n=++$this->n;
        $this->offsets[$n]=$this->_getoffset();
        $this->_put($n.' 0 obj');
        return $n;
    }

    function _putstream($data) { $this->_put('stream'); $this->_put($data); $this->_put('endstream'); }

    function _putstreamobject($data)
    {
        if($this->compress) { $entries='/Filter /FlateDecode '; $data=gzcompress($data); } else $entries='';
        $entries.='/Length '.strlen($data);
        $this->_newobj(); $this->_put('<<'.$entries.'>>'); $this->_putstream($data); $this->_put('endobj');
    }

    function _putpages()
    {
        $nb=$this->page;
        for($n=1;$n<=$nb;$n++) {
            $this->PageInfo[$n]['n']=$this->n+1+2*($n-1);
        }
        for($n=1;$n<=$nb;$n++) {
            $this->_putpage($n);
        }
        // Pages root
        $this->_newobj(1); $this->_put('<</Type /Pages'); $kids='/Kids [';
        for($n=1;$n<=$nb;$n++) $kids.=$this->PageInfo[$n]['n'].' 0 R ';
        $this->_put($kids.']');
        $this->_put('/Count '.$nb);
        $this->_put('>>'); $this->_put('endobj');
    }

    function _putpage($n)
    {
        $this->_newobj();
        $this->_put('<</Type /Page');
        $this->_put('/Parent 1 0 R');
        $this->_put(sprintf('/MediaBox [0 0 %.2F %.2F]',$this->PageInfo[$n]['size'][0],$this->PageInfo[$n]['size'][1]));
        if($this->PageInfo[$n]['rotation']!=0) $this->_put('/Rotate '.$this->PageInfo[$n]['rotation']);
        $this->_put('/Resources 2 0 R');
        $this->_put('/Contents '.($this->n+1).' 0 R>>');
        $this->_put('endobj');
        // Page content
        $p=$this->compress ? gzcompress($this->pages[$n]) : $this->pages[$n];
        $this->_newobj();
        $this->_put('<<'.($this->compress?'/Filter /FlateDecode':'').'/Length '.strlen($p).'>>');
        $this->_putstream($p);
        $this->_put('endobj');
    }

    function _putfonts()
    {
        foreach($this->fonts as $k=>$font) {
            $this->fonts[$k]['n']=$this->n+1;
            $this->_newobj();
            $this->_put('<</Type /Font');
            $this->_put('/Subtype /Type1');
            $this->_put('/BaseFont /'.$font['name']);
            if($font['name']!='Symbol'&&$font['name']!='ZapfDingbats') $this->_put('/Encoding /WinAnsiEncoding');
            $this->_put('>>');
            $this->_put('endobj');
        }
    }

    function _parsejpg($file)
    {
        $a=@getimagesize($file);
        if(!$a) $this->Error('Missing or incorrect image file: '.$file);
        if($a[2]!=2) $this->Error('Not a JPEG file: '.$file);
        if(!isset($a['channels']) || $a['channels']==3) $colspace='DeviceRGB';
        elseif($a['channels']==4) $colspace='DeviceCMYK';
        else $colspace='DeviceGray';
        $data=@file_get_contents($file);
        if($data===false) $this->Error('Unable to read image file: '.$file);
        return ['w'=>$a[0],'h'=>$a[1],'cs'=>$colspace,'bpc'=>8,'f'=>'DCTDecode','data'=>$data];
    }

    function _putimages()
    {
        foreach(array_keys($this->images) as $file) {
            $this->images[$file]['n']=$this->n+1;
            $info=$this->images[$file];
            $this->_newobj();
            $this->_put('<</Type /XObject');
            $this->_put('/Subtype /Image');
            $this->_put('/Width '.$info['w']);
            $this->_put('/Height '.$info['h']);
            $this->_put('/ColorSpace /'.$info['cs']);
            $this->_put('/BitsPerComponent '.$info['bpc']);
            $this->_put('/Filter /'.$info['f']);
            $this->_put('/Length '.strlen($info['data']).'>>');
            $this->_putstream($info['data']);
            $this->_put('endobj');
        }
    }

    function _putresourcedict()
    {
        $this->_put('/ProcSet [/PDF /Text /ImageB /ImageC /ImageI]');
        $this->_put('/Font <<');
        foreach($this->fonts as $font) $this->_put('/F'.$font['i'].' '.$font['n'].' 0 R');
        $this->_put('>>');
        $this->_put('/XObject <<');
        foreach($this->images as $image) $this->_put('/I'.$image['i'].' '.$image['n'].' 0 R');
        $this->_put('>>');
    }

    function _putresources()
    {
        $this->_putfonts();
        $this->_putimages();
        $this->_newobj(2);
        $this->_put('<<');
        $this->_putresourcedict();
        $this->_put('>>');
        $this->_put('endobj');
    }

    function _putinfo()
    {
        $this->metadata['Producer']="\xfe\xff".iconv('UTF-8','UTF-16BE','FPDF');
        $this->metadata['CreationDate']='D:'.@date('YmdHis');
        foreach($this->metadata as $key=>$value) {
            $this->_put('/'.str_replace(' ','',$key).' ('.$this->_escape($value).')');
        }
    }

    function _putcatalog()
    {
        $this->_put('/Type /Catalog');
        $this->_put('/Pages 1 0 R');
        if($this->ZoomMode=='fullpage')     $this->_put('/OpenAction [3 0 R /Fit]');
        elseif($this->ZoomMode=='fullwidth') $this->_put('/OpenAction [3 0 R /FitH null]');
        elseif($this->ZoomMode=='real')      $this->_put('/OpenAction [3 0 R /XYZ null null 1]');
        elseif(!is_string($this->ZoomMode)) $this->_put('/OpenAction [3 0 R /XYZ null null '.sprintf('%.2F',$this->ZoomMode/100).']');
        if($this->LayoutMode=='single')     $this->_put('/PageLayout /SinglePage');
        elseif($this->LayoutMode=='continuous') $this->_put('/PageLayout /OneColumn');
        elseif($this->LayoutMode=='two')    $this->_put('/PageLayout /TwoColumnLeft');
    }

    function _puttrailer()
    {
        $this->_put('/Size '.($this->n+1));
        $this->_put('/Root '.$this->n.' 0 R');
        $this->_put('/Info '.($this->n-1).' 0 R');
    }

    function _enddoc()
    {
        $this->_put('%PDF-'.$this->PDFVersion);
        $this->_putpages();
        $this->_putresources();
        // Info
        $this->_newobj(); $this->_put('<<'); $this->_putinfo(); $this->_put('>>'); $this->_put('endobj');
        // Catalog
        $this->_newobj(); $this->_put('<<'); $this->_putcatalog(); $this->_put('>>'); $this->_put('endobj');
        // Cross-ref table
        $offset=$this->_getoffset();
        $this->_put('xref');
        $this->_put('0 '.($this->n+1));
        $this->_put('0000000000 65535 f ');
        for($i=1;$i<=$this->n;$i++) $this->_put(sprintf('%010d 00000 n ',$this->offsets[$i]));
        // Trailer
        $this->_put('trailer'); $this->_put('<<'); $this->_puttrailer(); $this->_put('>>');
        $this->_put('startxref'); $this->_put($offset); $this->_put('%%EOF');
        $this->state=3;
    }
}
