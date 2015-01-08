<?php

$handle = fopen('cartographie_des_etablissements_tourisme_handicap.csv', 'r');

$geo = array(
    'type' => 'FeatureCollection',
    'features' => array(),
);

$head = fgetcsv($handle, 1000, ';');
// $head = array_flip($head);

while ($data = fgetcsv($handle, 1000, ';'))
{
    $data = array_combine($head, $data);
    $geo['features'][] = array(
        'type' => 'Feature',
        'geometry' => array(
            'type' => 'Point',
            'coordinates' => array(
                (float) $data['lng'],
                (float) $data['lat'],
            ),
        ),
        'properties' => $data,
    );
}

fclose($handle);

file_put_contents('cartographie_des_etablissements_tourisme_handicap.geojson', json_encode($geo));
