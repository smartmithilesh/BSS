<?php
class SchoolsaleController extends Controller {

    public function index() {
        $this->authCheck();
        $filters=['season_id'=>$_GET['season_id']??'','school_id'=>$_GET['school_id']??'','invoice_no'=>$_GET['invoice_no']??'','from_date'=>$_GET['from_date']??'','to_date'=>$_GET['to_date']??''];
        $page=max(1,(int)($_GET['page']??1)); $limit=15; $offset=($page-1)*$limit;
        $m=new SchoolSale();
        $this->render('school_sales/index',[
            'pageTitle' => 'School Sales',
            'sales'     => $m->getAll($filters,$limit,$offset),
            'total'     => $m->count($filters),
            'filters'   => $filters,
            'page'      => $page,
            'limit'     => $limit,
            'seasons'   => (new Season())->getAll(),
            'schools'   => (new School())->getAll(),
        ]);
    }

    public function create() {
        $this->authCheck();
        $this->render('school_sales/create',[
            'pageTitle'    => 'New School Sale',
            'seasons'      => (new Season())->getAll(),
            'schools'      => (new School())->getAll(),
            'classes'      => (new ClassModel())->getAll(),
            'activeSeason' => (new Season())->getActive(),
        ]);
    }

    public function store() {
        $this->authCheck();
        if($_SERVER['REQUEST_METHOD']!=='POST') $this->redirect('?controller=schoolsale&action=index');
        try {
            $id=(new SchoolSale())->create($_POST);
            $this->flash('success','Sale invoice created.');
            $this->redirect('?controller=schoolsale&action=view&id='.$id);
        } catch(Exception $e) {
            $this->flash('error',$e->getMessage());
            $this->redirect('?controller=schoolsale&action=create');
        }
    }

    public function view() {
        $this->authCheck();
        $id=(int)($_GET['id']??0);
        $m=new SchoolSale();
        $sale=$m->find($id);
        if(!$sale) $this->redirect('?controller=schoolsale&action=index');
        $this->render('school_sales/view',[
            'pageTitle' => 'Sale Invoice – '.$sale['invoice_no'],
            'sale'      => $sale,
            'items'     => $m->getItems($id),
            'payments'  => $m->getPayments($id),
        ]);
    }

    public function receivePayment() {
        $this->authCheck();
        $id=(int)($_GET['id']??0);
        $m=new SchoolSale();
        $sale=$id ? $m->find($id) : null;
        if($id && !$sale) $this->redirect('?controller=schoolsale&action=index');
        $this->render('school_sales/receive_payment',[
            'pageTitle'=>'Receive Payment',
            'sale'=>$sale,
            'outstandingSales'=>$id ? [] : $m->getOutstanding([]),
        ]);
    }

    public function storePayment() {
        $this->authCheck();
        if($_SERVER['REQUEST_METHOD']!=='POST') $this->redirect('?controller=schoolsale&action=index');
        $saleId=(int)($_POST['sale_id']??0);
        $returnTo=$_POST['return_to']??'invoice';
        try {
            (new SchoolSale())->receivePayment($_POST);
            $this->flash('success','Payment recorded.');
            if($returnTo==='payments') $this->redirect('?controller=schoolsale&action=payments');
            $this->redirect('?controller=schoolsale&action=view&id='.$saleId);
        } catch(Exception $e) {
            $this->flash('error',$e->getMessage());
            $this->redirect('?controller=schoolsale&action=receivePayment'.($saleId?'&id='.$saleId:''));
        }
    }

    // AJAX: books by class with stock
    public function getBooks() {
        header('Content-Type: application/json');
        try {
            $classId=(int)($_GET['class_id']??0);
            $seasonId=(int)($_GET['season_id']??0);
            if($classId<=0||$seasonId<=0) {
                echo json_encode(['error'=>'Invalid season or class ID']); exit;
            }
            $books=(new Book())->getByClassWithStock($classId,$seasonId);
            echo json_encode($books); exit;
        } catch(Exception $e) {
            echo json_encode(['error'=>$e->getMessage()]); exit;
        }
    }

    public function payments() {
        $this->authCheck();
        $filters=['season_id'=>$_GET['season_id']??'','school_id'=>$_GET['school_id']??'','from_date'=>$_GET['from_date']??'','to_date'=>$_GET['to_date']??''];
        $page=max(1,(int)($_GET['page']??1)); $limit=20; $offset=($page-1)*$limit;
        $m=new SchoolSale();
        $this->render('school_sales/payments',[
            'pageTitle'=>'School Payments',
            'payments'=>$m->getAllPayments($filters,$limit,$offset),
            'total'=>$m->countPayments($filters),
            'filters'=>$filters,'page'=>$page,'limit'=>$limit,
            'seasons'=>(new Season())->getAll(),
            'schools'=>(new School())->getAll(),
        ]);
    }

    public function outstanding() {
        $this->authCheck();
        $filters=['season_id'=>$_GET['season_id']??((new Season())->getActive()['id']??''),'school_id'=>$_GET['school_id']??''];
        $m=new SchoolSale();
        $this->render('school_sales/outstanding',[
            'pageTitle'=>'School Outstanding',
            'rows'=>$m->getOutstanding($filters),
            'filters'=>$filters,
            'seasons'=>(new Season())->getAll(),
            'schools'=>(new School())->getAll(),
        ]);
    }

    public function deletePayment() {
        $this->authCheck();
        try {
            (new SchoolSale())->deletePayment((int)($_GET['id']??0));
            $this->flash('success','Payment deleted.');
        } catch(Exception $e) {
            $this->flash('error',$e->getMessage());
        }
        $this->redirect('?controller=schoolsale&action=payments');
    }

    public function pdf() {
        $this->authCheck();
        $id=(int)($_GET['id']??0);
        $m=new SchoolSale();
        $sale=$m->find($id);
        if(!$sale) die('Not found');
        $items=$m->getItems($id);
        $payments=$m->getPayments($id);
        $this->generatePdf($sale,$items,$payments);
    }

    private function generatePdf($sale,$items,$payments) {
        require_once __DIR__.'/../../vendor/fpdf/fpdf.php';

        $pdf=new FPDF('P','mm','A4');
        $pdf->AddPage();
        $pdf->SetMargins(15,15,15);

        // ── Company Header
        $this->addPdfLogo($pdf);
        $pdf->SetFont('Arial','B',20);
        $pdf->Cell(0,10,AppSettings::get('shop_name',BASE_NAME),0,1,'C');
        $pdf->SetFont('Arial','',9);
        $pdf->Cell(0,5,'SCHOOL SALE INVOICE',0,1,'C');
        if(AppSettings::get('phone')||AppSettings::get('email')) {
            $pdf->Cell(0,5,trim(AppSettings::get('phone').' '.AppSettings::get('email')),0,1,'C');
        }
        $pdf->SetDrawColor(52,73,94);
        $pdf->SetLineWidth(0.5);
        $pdf->Line(15,$pdf->GetY(),195,$pdf->GetY());
        $pdf->Ln(4);

        // ── Invoice + School Info
        $pdf->SetFont('Arial','B',9);
        $pdf->Cell(95,6,'Bill To:',0,0);
        $pdf->Cell(95,6,'Invoice Details:',0,1,'R');

        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(95,6,$sale['school_name'],0,0);
        $pdf->SetFont('Arial','B',9);
        $pdf->Cell(50,6,'Invoice No:',0,0,'R');
        $pdf->Cell(45,6,$sale['invoice_no'],0,1,'R');

        $pdf->SetFont('Arial','',8);
        $addr=wordwrap($sale['school_address']??'',55,"\n",true);
        foreach(explode("\n",$addr) as $line) {
            $pdf->Cell(95,5,$line,0,0);
            $pdf->Ln(5);
        }

        $pdf->SetXY(15,$pdf->GetY()-5);
        $pdf->SetFont('Arial','',9);
        $pdf->Cell(145,6,'',0,0);
        $pdf->Cell(20,6,'Date:',0,0,'R');
        $pdf->Cell(25,6,date('d/m/Y',strtotime($sale['sale_date'])),0,1,'R');

        $pdf->Cell(145,6,'',0,0);
        $pdf->Cell(20,6,'Season:',0,0,'R');
        $pdf->Cell(25,6,$sale['season_name'],0,1,'R');
        $pdf->Ln(4);

        // ── Items Table
        $pdf->SetFillColor(44,62,80);
        $pdf->SetTextColor(255,255,255);
        $pdf->SetFont('Arial','B',9);
        $pdf->Cell(10,8,'#',1,0,'C',true);
        $pdf->Cell(80,8,'Book Name',1,0,'L',true);
        $pdf->Cell(28,8,'Class',1,0,'C',true);
        $pdf->Cell(14,8,'Qty',1,0,'C',true);
        $pdf->Cell(20,8,'Rate',1,0,'C',true);
        $pdf->Cell(14,8,'Disc%',1,0,'C',true);
        $pdf->Cell(14,8,'Amount',1,1,'C',true);

        $pdf->SetTextColor(0,0,0);
        $pdf->SetFont('Arial','',8);
        $pdf->SetFillColor(248,249,250);
        $fill=false;
        foreach($items as $i=>$it) {
            $pdf->Cell(10,7,(string)($i+1),1,0,'C',$fill);
            $pdf->Cell(80,7,$it['book_name'],1,0,'L',$fill);
            $pdf->Cell(28,7,$it['class_name'],1,0,'C',$fill);
            $pdf->Cell(14,7,(string)$it['qty'],1,0,'C',$fill);
            $pdf->Cell(20,7,number_format($it['rate'],2),1,0,'R',$fill);
            $pdf->Cell(14,7,number_format($it['discount_pct'],1).'%',1,0,'C',$fill);
            $pdf->Cell(14,7,number_format($it['amount'],2),1,1,'R',$fill);
            $fill=!$fill;
        }

        // ── Totals
        $pdf->Ln(2);
        $pdf->SetFont('Arial','',9);
        $pdf->Cell(158,6,'Gross Amount:',0,0,'R');
        $pdf->Cell(22,6,number_format($sale['gross_amount'],2),0,1,'R');
        $pdf->Cell(158,6,'Discount:',0,0,'R');
        $pdf->Cell(22,6,'(-) '.number_format($sale['discount_amount'],2),0,1,'R');
        $pdf->Line(138,$pdf->GetY(),195,$pdf->GetY());

        $pdf->SetFont('Arial','B',10);
        $pdf->SetFillColor(44,62,80);
        $pdf->SetTextColor(255,255,255);
        $pdf->Cell(158,8,'NET AMOUNT:',1,0,'R',true);
        $pdf->Cell(22,8,number_format($sale['net_amount'],2),1,1,'R',true);
        $pdf->SetTextColor(0,0,0);

        // ── Payment Summary
        if(!empty($payments)) {
            $pdf->Ln(5);
            $pdf->SetFont('Arial','B',9);
            $pdf->Cell(0,6,'Payment History:',0,1);
            $pdf->SetFont('Arial','',8);
            foreach($payments as $p) {
                $pdf->Cell(50,5,date('d/m/Y',strtotime($p['payment_date'])),0,0);
                $pdf->Cell(50,5,ucfirst($p['payment_mode']).(!empty($p['reference_no'])?' - '.$p['reference_no']:''),0,0);
                $pdf->Cell(0,5,AppSettings::get('currency_symbol','Rs. ').' '.number_format($p['amount'],2),0,1,'R');
            }
        }
        $pdf->SetFont('Arial','',9);
        $pdf->Cell(158,6,'Amount Paid:',0,0,'R');
        $pdf->Cell(22,6,number_format($sale['paid_amount'],2),0,1,'R');
        $pdf->SetFont('Arial','B',10);
        $outstanding=$sale['net_amount']-$sale['paid_amount'];
        if($outstanding>0) {
            $pdf->SetFillColor(231,76,60);
            $pdf->SetTextColor(255,255,255);
        } else {
            $pdf->SetFillColor(39,174,96);
            $pdf->SetTextColor(255,255,255);
        }
        $label=$outstanding>0?'BALANCE DUE:':'FULLY PAID:';
        $pdf->Cell(158,7,$label,1,0,'R',true);
        $pdf->Cell(22,7,number_format(abs($outstanding),2),1,1,'R',true);

        // Footer
        $pdf->SetTextColor(0,0,0);
        $pdf->SetFont('Arial','I',8);
        $pdf->Ln(10);
        $pdf->Cell(0,5,AppSettings::get('invoice_footer','Thank you for your business!'),0,1,'C');

        $pdf->Output('D','Invoice_'.$sale['invoice_no'].'.pdf');
        exit;
    }

    private function addPdfLogo($pdf) {
        $logo=AppSettings::logoPath();
        if(!$logo) return;
        $info=@getimagesize($logo);
        if(!$info || empty($info[0]) || empty($info[1])) return;
        $drawLogo=$logo;
        $tempLogo='';
        if(($info[2]??0)!==IMAGETYPE_JPEG) {
            if(!function_exists('imagecreatefromstring') || !function_exists('imagejpeg')) return;
            $imageData=@file_get_contents($logo);
            $image=$imageData!==false ? @imagecreatefromstring($imageData) : false;
            if(!$image) return;
            $tempLogo=tempnam(sys_get_temp_dir(),'bbd-logo-');
            if(!$tempLogo || !@imagejpeg($image,$tempLogo,90)) {
                imagedestroy($image);
                return;
            }
            imagedestroy($image);
            $drawLogo=$tempLogo;
        }

        $maxW=32;
        $maxH=20;
        $w=$maxW;
        $h=$info[1]*$w/$info[0];
        if($h>$maxH) {
            $h=$maxH;
            $w=$info[0]*$h/$info[1];
        }
        $x=(210-$w)/2;
        $pdf->Image($drawLogo,$x,12,$w,$h,'jpg');
        if($tempLogo) @unlink($tempLogo);
        $pdf->SetY(12+$h+2);
    }
}
