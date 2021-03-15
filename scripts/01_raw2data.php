<?php
$basePath = dirname(__DIR__);
$rawPath = $basePath . '/raw';
if (!file_exists($rawPath)) {
    mkdir($rawPath, 0777);
}
$dataPath = $basePath . '/data';
if (!file_exists($dataPath)) {
    mkdir($dataPath, 0777);
}
$cities = [
    'TAIPEI' => '台北市',
    'NEW_TAIPEI' => '新北市',
    'KAOHSIUNG' => '高雄市',
    'TAICHUNG' => '台中市',
    'HSINCHU' => '新竹',
    'TAOYUAN' => '桃園縣',
    'TAINAN' => '台南市',
    'KEELUNG' => '基隆市',
    'CHIAYI' => '嘉義',
    'MIAOLI' => '苗栗縣',
    'CHANGHUA' => '彰化縣',
    'NANTOU' => '南投縣',
    'YUNLIN' => '雲林縣',
    'PINGTUNG' => '屏東縣',
    'ILAN' => '宜蘭縣',
    'HUALIEN' => '花蓮縣',
    'TAITUNG' => '台東縣',
];
foreach ($cities as $city => $label) {
    $rawFile = $rawPath . '/' . $city . '.html';
    file_put_contents($rawFile, file_get_contents('https://web-design.vip/eip.do?method=query.eip&city=' . $city));
    $raw = file_get_contents($rawFile);
    $pos = strpos($raw, 'var locations = ');
    if (false !== $pos) {
        $pos = strpos($raw, '[', $pos);
        $posEnd = strpos($raw, 'var infowindow', $pos);
        $codeBlock = trim(substr($raw, $pos, $posEnd - $pos));
        eval('$points = ' . $codeBlock);
        if (count($points) > 0) {
            $fh = fopen($dataPath . '/' . $city . '.csv', 'w');
            fputcsv($fh, ['社區', '地址', 'Latitude', 'Longitude', '樓層數', '企業主數', '人數預估', '安裝片數', '棟數', '落成', '坪價', '類型', '強調']);
            foreach ($points as $point) {
                if($point[11] == 'true') {
                    $point[11] = '商辦大樓';
                } else {
                    $point[11] = '住宅社區';
                }
                fputcsv($fh, $point);
            }
        }
    }
}
