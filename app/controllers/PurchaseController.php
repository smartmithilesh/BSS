<?php
class PurchaseController extends Controller {

    public function index() {
        $this->authCheck();
        $filters=['season_id'=>$_GET['season_id']??'','company_id'=>$_GET['company_id']??'','invoice_no'=>$_GET['invoice_no']??'','from_date'=>$_GET['from_date']??'','to_date'=>$_GET['to_date']??''];
        $page=max(1,(int)($_GET['page']??1)); $limit=15; $offset=($page-1)*$limit;
        $m=new Purchase();
        $this->render('purchase/index',[
            'pageTitle'  => 'Purchases',
            'purchases'  => $m->getAll($filters,$limit,$offset),
            'total'      => $m->count($filters),
            'filters'    => $filters,
            'page'       => $page,
            'limit'      => $limit,
            'seasons'    => (new Season())->getAll(),
            'companies'  => (new Company())->getAll(),
        ]);
    }

    public function create() {
        $this->authCheck();
        $this->render('purchase/form',[
            'pageTitle'  => 'New Purchase',
            'seasons'    => (new Season())->getAll(),
            'companies'  => (new Company())->getAll(),
            'classes'    => (new ClassModel())->getAll(),
            'activeSeason' => (new Season())->getActive(),
        ]);
    }

    public function store() {
        $this->authCheck();
        if($_SERVER['REQUEST_METHOD']!=='POST') $this->redirect('?controller=purchase&action=index');
        try {
            $id=(new Purchase())->create($_POST);
            $this->flash('success','Purchase saved successfully.');
            $this->redirect('?controller=purchase&action=invoice&id='.$id);
        } catch(Exception $e) {
            $this->flash('error',$e->getMessage());
            $this->redirect('?controller=purchase&action=create');
        }
    }

    public function invoice() {
        $this->authCheck();
        $id=(int)($_GET['id']??0);
        $purchase=(new Purchase())->find($id);
        if(!$purchase) $this->redirect('?controller=purchase&action=index');
        $items=(new Purchase())->getItems($id);
        $this->render('purchase/invoice',['pageTitle'=>'Purchase Invoice','purchase'=>$purchase,'items'=>$items]);
    }

    public function pdf() {
        $this->authCheck();
        $id=(int)($_GET['id']??0);
        $purchase=(new Purchase())->find($id);
        if(!$purchase) die('Not found');
        $items=(new Purchase())->getItems($id);
        $this->generatePdf($purchase,$items);
    }

    private function generatePdf($purchase,$items) {
        require_once __DIR__.'/../../vendor/fpdf/fpdf.php';

        $pdf=new FPDF('P','mm','A4');
        $pdf->AddPage();
        $pdf->SetMargins(15,15,15);

        // Header
        $this->addPdfLogo($pdf);
        $pdf->SetFont('Arial','B',18);
        $pdf->Cell(0,10,AppSettings::get('shop_name',BASE_NAME),0,1,'C');
        $pdf->SetFont('Arial','',9);
        $pdf->Cell(0,5,'PURCHASE INVOICE',0,1,'C');
        if(AppSettings::get('phone')||AppSettings::get('email')) {
            $pdf->Cell(0,5,trim(AppSettings::get('phone').' '.AppSettings::get('email')),0,1,'C');
        }
        $pdf->Ln(3);

        // Invoice details box
        $pdf->SetFont('Arial','B',9);
        $pdf->Cell(30,6,'Invoice No:',0,0);
        $pdf->SetFont('Arial','',9);
        $pdf->Cell(60,6,$purchase['invoice_no'],0,0);
        $pdf->SetFont('Arial','B',9);
        $pdf->Cell(30,6,'Date:',0,0,'R');
        $pdf->SetFont('Arial','',9);
        $pdf->Cell(0,6,date('d/m/Y',strtotime($purchase['purchase_date'])),0,1,'R');

        $pdf->SetFont('Arial','B',9);
        $pdf->Cell(30,6,'Season:',0,0);
        $pdf->SetFont('Arial','',9);
        $pdf->Cell(60,6,$purchase['season_name'],0,0);
        $pdf->SetFont('Arial','B',9);
        $pdf->Cell(30,6,'Company:',0,0,'R');
        $pdf->SetFont('Arial','',9);
        $pdf->Cell(0,6,$purchase['company_name'],0,1,'R');
        $pdf->Ln(4);

        // Table header
        $pdf->SetFillColor(52,73,94);
        $pdf->SetTextColor(255,255,255);
        $pdf->SetFont('Arial','B',9);
        $pdf->Cell(8,8,'#',1,0,'C',true);
        $pdf->Cell(70,8,'Book Name',1,0,'C',true);
        $pdf->Cell(30,8,'Class',1,0,'C',true);
        $pdf->Cell(15,8,'Qty',1,0,'C',true);
        $pdf->Cell(22,8,'Rate',1,0,'C',true);
        $pdf->Cell(15,8,'Disc%',1,0,'C',true);
        $pdf->Cell(20,8,'Amount',1,1,'C',true);

        // Table rows
        $pdf->SetTextColor(0,0,0);
        $pdf->SetFont('Arial','',8);
        $pdf->SetFillColor(245,245,245);
        $fill=false;
        foreach($items as $i=>$it) {
            $pdf->Cell(8,7,(string)($i+1),1,0,'C',$fill);
            $pdf->Cell(70,7,$it['book_name'],1,0,'L',$fill);
            $pdf->Cell(30,7,$it['class_name'],1,0,'C',$fill);
            $pdf->Cell(15,7,(string)$it['qty'],1,0,'C',$fill);
            $pdf->Cell(22,7,number_format($it['rate'],2),1,0,'R',$fill);
            $pdf->Cell(15,7,number_format($it['discount_pct'],1).'%',1,0,'C',$fill);
            $pdf->Cell(20,7,number_format($it['amount'],2),1,1,'R',$fill);
            $fill=!$fill;
        }

        // Totals
        $pdf->Ln(2);
        $pdf->SetFont('Arial','',9);
        $pdf->Cell(150,6,'Gross Amount:',0,0,'R');
        $pdf->Cell(30,6,number_format($purchase['gross_amount'],2),0,1,'R');
        $pdf->Cell(150,6,'Discount:',0,0,'R');
        $pdf->Cell(30,6,'(-) '.number_format($purchase['discount_amount'],2),0,1,'R');
        $pdf->SetFont('Arial','B',10);
        $pdf->SetFillColor(52,73,94);
        $pdf->SetTextColor(255,255,255);
        $pdf->Cell(150,7,'NET AMOUNT:',1,0,'R',true);
        $pdf->Cell(30,7,number_format($purchase['net_amount'],2),1,1,'R',true);

        // Notes
        if(!empty($purchase['notes'])) {
            $pdf->SetTextColor(0,0,0);
            $pdf->SetFont('Arial','',8);
            $pdf->Ln(4);
            $pdf->Cell(0,6,'Notes: '.$purchase['notes'],0,1);
        }

        $pdf->Output('D','Purchase_'.$purchase['invoice_no'].'.pdf');
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

    public function delete() {
        $this->authCheck();
        try {
            (new Purchase())->delete((int)($_GET['id']??0));
            $this->flash('success','Purchase deleted.');
        } catch(Exception $e) {
            $this->flash('error',$e->getMessage());
        }
        $this->redirect('?controller=purchase&action=index');
    }

    // AJAX: books by company + class
    public function getBooks() {
        header('Content-Type: application/json');
        try {
            $companyId = (int)($_GET['company_id']??0);
            $classId = (int)($_GET['class_id']??0);
            
            if ($companyId <= 0 || $classId <= 0) {
                echo json_encode(['error' => 'Invalid company or class ID']);
                exit;
            }
            
            $books = (new Book())->getByCompanyAndClass($companyId, $classId);
            echo json_encode($books ?? []);
            exit;
        } catch (Exception $e) {
            echo json_encode(['error' => $e->getMessage()]);
            exit;
        }
    }
}
