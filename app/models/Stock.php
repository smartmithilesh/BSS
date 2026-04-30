<?php
class Stock extends Model {
    public function getQty($seasonId,$bookId) {
        $st=$this->db->prepare("SELECT qty FROM stocks WHERE season_id=? AND book_id=?");
        $st->execute([$seasonId,$bookId]); return (int)($st->fetchColumn()??0);
    }
    public function add($seasonId,$bookId,$qty) {
        $this->db->prepare("INSERT INTO stocks(season_id,book_id,qty)VALUES(?,?,?) ON DUPLICATE KEY UPDATE qty=qty+VALUES(qty)")
            ->execute([$seasonId,$bookId,$qty]);
    }
    public function reduce($seasonId,$bookId,$qty) {
        $this->db->prepare("UPDATE stocks SET qty=qty-? WHERE season_id=? AND book_id=?")
            ->execute([$qty,$seasonId,$bookId]);
    }
    public function getAll($seasonId) {
        $st=$this->db->prepare("
            SELECT b.name AS book_name,cl.name AS class_name,co.name AS company_name,
                   COALESCE(s.qty,0) AS qty
            FROM books b
            JOIN classes cl ON cl.id=b.class_id
            JOIN companies co ON co.id=b.company_id
            LEFT JOIN stocks s ON s.book_id=b.id AND s.season_id=?
            WHERE b.is_active=1
            ORDER BY cl.sort_order,b.name");
        $st->execute([$seasonId]); return $st->fetchAll();
    }
}
