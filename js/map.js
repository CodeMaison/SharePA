"use strict";

L.mapbox.accessToken = 'pk.eyJ1Ijoic2hhcmVwYSIsImEiOiJmRGtNNjlZIn0.U1emYk6aJMKEM_OmqYdNLg';
var map = L.mapbox.map('map', 'sharepa.kmj6g6i8')
    .setView([48.876026, 2.337730], 12);

var featureLayer = L.mapbox.featureLayer()
    .loadURL('data/accessibilite_des_equipements_de_la_ville_de_paris.php')
    .addTo(map);

var $boxes = jQuery('input.map-layer');
var mapLayers = {};
function update() {
  $boxes.each(function (i, el) {
    mapLayers[el.id] = el.checked;
  });

  featureLayer.setFilter(function (feature) {
    return mapLayers[feature.properties['marker-symbol']];
  });
}
$boxes.on('change', update);
update();
