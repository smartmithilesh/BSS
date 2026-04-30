<?php
class MigrationRunner {
    private $db;
    private $dir;

    public function __construct() {
        $this->db=Database::connect();
        $this->dir=__DIR__.'/../../database/migrations';
        $this->ensureTable();
    }

    public function ensureTable() {
        $this->db->exec("CREATE TABLE IF NOT EXISTS system_migrations (
            id INT AUTO_INCREMENT PRIMARY KEY,
            migration VARCHAR(190) NOT NULL UNIQUE,
            batch INT NOT NULL DEFAULT 1,
            executed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    }

    public function all() {
        $files=$this->files();
        $done=$this->executed();
        $rows=[];
        foreach($files as $file) {
            $name=basename($file);
            $rows[]=['migration'=>$name,'status'=>isset($done[$name])?'executed':'pending','executed_at'=>$done[$name]['executed_at']??null];
        }
        return $rows;
    }

    public function pending() {
        $done=$this->executed();
        return array_values(array_filter($this->files(),fn($file)=>!isset($done[basename($file)])));
    }

    public function runPending() {
        $pending=$this->pending();
        if(!$pending) return 0;
        $batch=(int)$this->db->query("SELECT COALESCE(MAX(batch),0)+1 FROM system_migrations")->fetchColumn();
        foreach($pending as $file) {
            $this->runFile($file);
            $st=$this->db->prepare("INSERT INTO system_migrations(migration,batch)VALUES(?,?)");
            $st->execute([basename($file),$batch]);
        }
        return count($pending);
    }

    private function files() {
        if(!is_dir($this->dir)) return [];
        $files=array_merge(glob($this->dir.'/*.sql')?:[],glob($this->dir.'/*.php')?:[]);
        sort($files);
        return $files;
    }

    private function executed() {
        $rows=$this->db->query("SELECT migration,executed_at FROM system_migrations")->fetchAll();
        $done=[];
        foreach($rows as $r) $done[$r['migration']]=$r;
        return $done;
    }

    private function runFile($file) {
        if(substr($file,-4)==='.php') {
            $db=$this->db;
            require $file;
            return;
        }
        $sql=file_get_contents($file);
        foreach($this->splitSql($sql) as $statement) {
            if(trim($statement)!=='') $this->db->exec($statement);
        }
    }

    private function splitSql($sql) {
        $sql=preg_replace('/^\s*--.*$/m','',$sql);
        return array_filter(array_map('trim',explode(';',$sql)));
    }
}
