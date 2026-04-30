<?php
class SiteSetting extends Model {
    public function getAllAssoc() {
        return AppSettings::all();
    }

    public function saveAll($data) {
        $allowed=['shop_name','tagline','phone','email','address','currency_symbol','purchase_prefix','sale_prefix','invoice_footer','brand_logo','timezone'];
        $st=$this->db->prepare("INSERT INTO site_settings(setting_key,setting_value)VALUES(?,?) ON DUPLICATE KEY UPDATE setting_value=VALUES(setting_value)");
        foreach($allowed as $key) {
            if(array_key_exists($key,$data)) $st->execute([$key,trim((string)$data[$key])]);
        }
        AppSettings::clear();
    }
}
