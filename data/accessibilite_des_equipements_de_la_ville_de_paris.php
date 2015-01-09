<?php

ini_set('display_errors', 'On');
error_reporting(-1);

//--------------------------------------------------------------------------------------------------------------------------
// accessibilite_des_equipements_de_la_ville_de_paris.geojson

$geo = json_decode(file_get_contents('accessibilite_des_equipements_de_la_ville_de_paris.geojson'), true);

$symbols = array(
    'http://www.paris.fr/pratique/paris-au-vert/parcs-jardins-squares/p4952' => 'park2',
    'http://www.paris.fr/pratique/paris-au-vert/cimetieres/p1702' => 'cemetery',
    'http://www.paris.fr/pratique/education-cours-pour-adultes/colleges-lycees/p120' => 'college',
    'http://www.paris.fr/pratique/education-cours-pour-adultes/ecoles/p119' => 'school',
    'http://www.paris.fr/politiques/paris-politiques/arrondissements/p193' => 'town-hall',
    'http://www.paris.fr/pratique/ou-faire-du-sport/nager-a-paris/p5021' => 'swimming',
    'http://www.paris.fr/pratique/pratiquer-un-sport/ou-faire-du-sport/p151' => 'pitch',
    'http://www.paris.fr/creches' => 'playground',
    'http://www.paris.fr/' => 'monument',
);

$labels = array(
    'http://www.paris.fr/pratique/paris-au-vert/parcs-jardins-squares/p4952' => 'Parcs & Jardins',
    'http://www.paris.fr/pratique/paris-au-vert/cimetieres/p1702' => 'Cimetière',
    'http://www.paris.fr/pratique/education-cours-pour-adultes/colleges-lycees/p120' => 'Collèges & Lycées',
    'http://www.paris.fr/pratique/education-cours-pour-adultes/ecoles/p119' => 'Ecoles',
    'http://www.paris.fr/politiques/paris-politiques/arrondissements/p193' => 'Mairie',
    'http://www.paris.fr/pratique/ou-faire-du-sport/nager-a-paris/p5021' => 'Piscines',
    'http://www.paris.fr/pratique/pratiquer-un-sport/ou-faire-du-sport/p151' => 'Sport',
    'http://www.paris.fr/creches' => 'Crèches',
    'http://www.paris.fr/' => 'Monument',
);

foreach ($geo['features'] as $key => &$feature) {
    if (!array_key_exists('handicap_moteur', $feature['properties']) || $feature['properties']['handicap_moteur'] < 0) {
        unset($geo['features'][$key]);
        continue;
    }

    switch ($feature['properties']['handicap_moteur']) {
        case 0:
            $color = '#ff0000';
            $description = 'Aucune accessibilité';
            break;
        case 1:
            $color = '#ffca7d';
            $description = 'Accessibilité minimale';
            break;
        case 2:
            $color = '#ffffbe';
            $description = 'Accessibilité d\'usage';
            break;
        case 3:
        case 4:
            $color = '#3bc353';
            $description = 'Accessibilité totale';
            break;
        default:
            $color = '#999999';
            $description = 'Pas d\'information';
            break;
    }

    $description = '<h3>' . $feature['properties']['nom'] . ' <small>(' . $labels[$feature['properties']['lien']] . ')</small></h3>';
    $description .= '<ul>';
    if ($feature['properties']['handicap_moteur'] > -1) {
      $description .= '<li>Handicap Moteur : ' . $feature['properties']['handicap_moteur'] . '/4</li>';
    }
    if ($feature['properties']['handicap_visuel'] > -1) {
      $description .= '<li>Handicap Visuel :   ' . $feature['properties']['handicap_visuel'] . '/4</li>';
    }
    if ($feature['properties']['handicap_auditif'] > -1) {
      $description .= '<li>Handicap Auditif :  ' . $feature['properties']['handicap_auditif'] . '/4</li>';
    }
    $description .= '</ul>';

    if (!empty($feature['properties']['remarques'])) {
        $description .= '<p><strong>Remarques :</strong> ' . $feature['properties']['remarques'] . '</p>';
    }

    $description .= '<p style="text-align:center"><small><a href="' . $feature['properties']['lien'] . '" target="_blank">Site internet</a></small></p>';


    $feature['properties']['marker-color'] = $color;
    $feature['properties']['marker-size'] = 'large';
    $feature['properties']['marker-symbol'] = $symbols[$feature['properties']['lien']];
    $feature['properties']['description'] = $description;
    $feature['properties']['handicap_moteur'] = min($feature['properties']['handicap_moteur'], 3);
}

$geo['features'] = array_values($geo['features']);


//--------------------------------------------------------------------------------------------------------------------------
// cartographie_des_etablissements_tourisme_handicap.csv

$handle = fopen('cartographie_des_etablissements_tourisme_handicap.csv', 'r');

$head = fgetcsv($handle, 1000, ';');

$symbols = array(
    'Lieu de visite' => 'museum',
    'Hôtel' => 'lodging',
    'Chambre d\'hôtes' => 'lodging',
    'Activités sportives de pleine nature' => 'pitch',
    'Office de tourisme' => 'town-hall',
    'Structure diverse' => 'town-hall',
    'Meublé de tourisme' => 'lodging',
    'Loisirs' => 'pitch',
    'Hébergement collectif' => 'lodging',
    'Camping' => 'lodging',
    'Restaurant' => 'restaurant',
    'Résidence de tourisme' => 'lodging',
    'Palais des Congrès' => 'town-hall',
);

while ($data = fgetcsv($handle, 1000, ';'))
{
    $data = array_combine($head, $data);

    if ($data['handicap_moteur'] != 'Oui') {
        continue;
    }
    $data['handicap_moteur'] = 3;

    $description = '<h3>' . $data['etablissement'] . ' <small>(' . $data['structure'] . ')</small></h3>';
    $description .= '<ul>';
    $description .= '<li>Handicap Moteur : ' . $data['handicap_moteur'] . '</li>';
    $description .= '<li>Handicap Visuel : ' . $data['handicap_visuel'] . '</li>';
    $description .= '<li>Handicap Auditif :  ' . $data['handicap_auditif'] . '</li>';
    $description .= '</ul>';

    $description .= '<p style="text-align:center"><small><a href="' . $data['siteweb'] . '" target="_blank">Site internet</a></small></p>';


    $data['marker-color'] = '#3bc353';
    $data['marker-size'] = 'large';
    $data['marker-symbol'] = $symbols[$data['categorie']];
    $data['description'] = $description;

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


//--------------------------------------------------------------------------------------------------------------------------
// Render
echo json_encode($geo);
