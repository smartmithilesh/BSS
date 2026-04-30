<?php
class AppSettings {
    private static $settings;

    public static function all() {
        if(self::$settings!==null) return self::$settings;
        $defaults=[
            'shop_name'=>defined('BASE_NAME')?BASE_NAME:'Bharat Book Depot',
            'brand_logo'=>'',
            'tagline'=>'Book Depot Management System',
            'phone'=>'',
            'email'=>'',
            'address'=>'',
            'currency_symbol'=>'₹',
            'purchase_prefix'=>'PUR',
            'sale_prefix'=>'INV',
            'invoice_footer'=>'Thank you for your business!',
            'timezone'=>defined('APP_TIMEZONE')?APP_TIMEZONE:'Asia/Kolkata',
        ];
        try {
            $db=Database::connect();
            $rows=$db->query("SELECT setting_key,setting_value FROM site_settings")->fetchAll();
            foreach($rows as $r) $defaults[$r['setting_key']]=$r['setting_value'];
        } catch(Exception $e) {
            // Keep defaults available before migrations or during setup.
        }
        self::$settings=$defaults;
        return self::$settings;
    }

    public static function get($key,$default='') {
        $all=self::all();
        return $all[$key]??$default;
    }

    public static function logoUrl() {
        $logo=self::get('brand_logo','');
        return $logo ? BASE_URL.ltrim($logo,'/') : '';
    }

    public static function logoPath() {
        $logo=self::get('brand_logo','');
        if(!$logo) return '';
        $path=__DIR__.'/../../public/'.ltrim($logo,'/');
        return is_file($path) ? $path : '';
    }

    public static function clear() {
        self::$settings=null;
    }
}
