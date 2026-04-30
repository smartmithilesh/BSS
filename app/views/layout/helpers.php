<?php
// ── Pagination helper
function paginate($total, $page, $limit, $baseUrl) {
    $pages = (int) ceil($total / $limit);
    if ($pages <= 1) return '';
    $baseUrl = pretty_url_from_legacy($baseUrl);
    $joiner = strpos($baseUrl, '?') === false ? '?' : '&';
    $html = '<nav><ul class="pagination pagination-sm">';
    $html .= '<li'.($page<=1?' class="disabled"':'').'><a href="'.$baseUrl.$joiner.'page='.($page-1).'">«</a></li>';
    for ($i = 1; $i <= $pages; $i++) {
        $html .= '<li'.($i==$page?' class="active"':'').'><a href="'.$baseUrl.$joiner.'page='.$i.'">'.$i.'</a></li>';
    }
    $html .= '<li'.($page>=$pages?' class="disabled"':'').'><a href="'.$baseUrl.$joiner.'page='.($page+1).'">»</a></li>';
    $html .= '</ul></nav>';
    return $html;
}

// ── money
function money($v) { return AppSettings::get('currency_symbol','₹').number_format((float)$v, 2); }
