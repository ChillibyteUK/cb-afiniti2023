<?php

require_once($_SERVER['DOCUMENT_ROOT'] . "/wp-load.php");

function array2csv(array &$array)
{
   if (count($array) == 0) {
     return null;
   }
   ob_start();
   $df = fopen("php://output", 'w');
   fputcsv($df, array_keys(reset($array)));
   foreach ($array as $row) {
      fputcsv($df, $row);
   }
   fclose($df);
   return ob_get_clean();
}

$q = new WP_Query(array(
    'post_type' => 'cra',
    'posts_per_page' => -1,
    'date_query' => array(
        array(
            'after' => $_POST['start'],
            'before' => $_POST['end'],
            'inclusive' => true
        )
    )
));

$_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

$date = gmdate("Y-m-d\TH:i:s\Z");
$filename = 'cra-results_' . $date . '.csv';

ob_start();

// disable caching
$now = gmdate("D, d M Y H:i:s");
header("Expires: Tue, 03 Jul 2001 06:00:00 GMT");
header("Cache-Control: max-age=0, no-cache, must-revalidate, proxy-revalidate");
header("Last-Modified: {$now} GMT");

// force download  
header("Content-Type: application/force-download");
header("Content-Type: application/octet-stream");
header("Content-Type: application/download");

// disposition / encoding on response body
header("Content-Disposition: attachment;filename={$filename}");
header("Content-Transfer-Encoding: binary");

$fp = fopen('php://output','w');

$header = array('Report URL','Contact Name','Contact Title','Organisation Name','Contact Phone','Contact Email','Change in Progress?','Change Detail','Change Role','Culture','Leadership','Method','Engagement','Drivers','Capability');

fputcsv($fp,$header);

while ($q->have_posts()) {
    $q->the_post();
    $data = get_field('data');
    $scores = get_field('scores');

    $results = array();

    $results[] = get_the_permalink();
    $results[] = $data['contactName'];
    $results[] = $data['contactTitle'];
    $results[] = $data['orgName'];
    $results[] = "=\"" . $data['contactPhone'] . "\"";
    $results[] = $data['contactEmail'];
    $results[] = $data['changeInProgress'];
    $results[] = $data['changeDetail'];
    $results[] = $data['changeRole'];
    $results[] = $scores['Culture'];
    $results[] = $scores['Leadership'];
    $results[] = $scores['Method'];
    $results[] = $scores['Engagement'];
    $results[] = $scores['Drivers'];
    $results[] = $scores['Capability'];

    fputcsv($fp,$results);
}

ob_end_flush();
