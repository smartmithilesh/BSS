<?php
class StockController extends Controller {
    public function index() {
        $this->authCheck();
        $seasonId=(int)($_GET['season_id']??((new Season())->getActive()['id']??0));
        $this->render('stock/index',[
            'pageTitle' => 'Stock Report',
            'stocks'    => (new Stock())->getAll($seasonId),
            'seasons'   => (new Season())->getAll(),
            'seasonId'  => $seasonId,
        ]);
    }
}
