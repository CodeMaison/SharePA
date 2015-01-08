"use strict";

L.mapbox.accessToken = 'pk.eyJ1Ijoic2hhcmVwYSIsImEiOiJmRGtNNjlZIn0.U1emYk6aJMKEM_OmqYdNLg';
var map = L.mapbox.map('map', 'sharepa.kmj6g6i8')
    .setView([48.876026, 2.337730], 12);

function detailsFromLevel (level) {
  switch (level) {
    case 0:
      return {
        color: '#999999',
        description: 'Aucune accessibilité',
      }
    case 1:
      return {
        color: '#ffca7d',
        description: 'Accessibilité minimale',
      }
    case 2:
      return {
        color: '#ffffbe',
        description: 'Accessibilité d\'usage',
      }
    case 3:
      return {
        color: '#b5e681',
        description: 'Accessibilité totale',
      }
    case 4:
      return {
        color: '#3bc353',
        description: 'Accessibilité totale + locaux de travail',
      }
    default:
      return {
        color: '#999999',
        description: 'Pas d\'information',
      }
  }
}

var featureLayer1 = L.mapbox.featureLayer()
    .loadURL('data/accessibilite_des_equipements_de_la_ville_de_paris.geojson')
    // Once this layer loads, we set a timer to load it again in a few seconds.
    .on('ready', function (layer) {
      this.eachLayer(function(marker) {
        var props = marker.toGeoJSON().properties;
        var details = detailsFromLevel(props.handicap_moteur);
        marker.setIcon(L.mapbox.marker.icon({
            'marker-color': details.color,
            'marker-size': 'large'
            //'marker-symbol': 'star'
        }));

        var popup = '<h3>' + props.nom + '</h3>';

        // popup += '<p><small>' + props.numero + ' ' + props.voie + '</small></p>';
        popup += '<p>' + details.description + '</p>';

        /*
        popup += '<ul>';
        if (props.handicap_moteur > -1) {
          popup += '<li>Mobilité : ' + props.handicap_moteur + '/4</li>';
        }
        if (props.handicap_visuel > -1) {
          popup += '<li>Visuel :   ' + props.handicap_visuel + '/4</li>';
        }
        if (props.handicap_auditif > -1) {
          popup += '<li>Auditif :  ' + props.handicap_auditif + '/4</li>';
        }
        popup += '</ul>';
        */

        if (props.remarques) {
          popup += '<p><strong>Remarques :</strong> ' + props.remarques + '</p>';
        }
        popup += '<p style="text-align:center"><small><a href="' + props.lien + '" target="_blank">Site internet</a></small></p>';
        marker.bindPopup(popup);
      });
    })
    .addTo(map);


var featureLayer2 = L.mapbox.featureLayer()
    .loadURL('data/cartographie_des_etablissements_tourisme_handicap.geojson')
    // .setFilter(function (feature) {
    //   return (feature.properties.handicap_moteur > 0);
    // })
    .on('ready', function (layer) {
      this.eachLayer(function(marker) {
        var props = marker.toGeoJSON().properties;
        marker.setIcon(L.mapbox.marker.icon({
            'marker-color': '#b5e681',
            'marker-size': 'large',
            //'marker-symbol': 'star'
        }));

        var popup = '<h3>' + props.etablissement + '</h3>';

        // popup += '<p><small>' + props.numero + ' ' + props.voie + '</small></p>';
        // popup += '<p>' + details.description + '</p>';

        /*
        popup += '<ul>';
        if (props.handicap_moteur > -1) {
          popup += '<li>Mobilité : ' + props.handicap_moteur + '/4</li>';
        }
        if (props.handicap_visuel > -1) {
          popup += '<li>Visuel :   ' + props.handicap_visuel + '/4</li>';
        }
        if (props.handicap_auditif > -1) {
          popup += '<li>Auditif :  ' + props.handicap_auditif + '/4</li>';
        }
        popup += '</ul>';
        */

        if (props.remarques) {
          popup += '<p><strong>Remarques :</strong> ' + props.remarques + '</p>';
        }
        popup += '<p style="text-align:center"><small><a href="' + props.siteweb + '" target="_blank">Site internet</a></small></p>';
        marker.bindPopup(popup);
      });
    })
    .addTo(map);


var $accessibilityLevelsCheckBoxes = jQuery('.js-access');
var accessibilityLevels = {};

function refreshAccessibilityLevels()
{
  $accessibilityLevelsCheckBoxes.each(function (i, el) {
    accessibilityLevels[el.name] = el.checked;
  });

  featureLayer1.setFilter(function (feature) {
    // TODO check type

    if (accessibilityLevels['accessibilite_'+feature.properties.handicap_moteur]) {
      return true;
    }

    return false;
  });
}

$accessibilityLevelsCheckBoxes.on('change', refreshAccessibilityLevels);
refreshAccessibilityLevels();