<?php

// https://github.com/boyhagemann/Wave
require_once __DIR__ . '/vendor/autoload.php';

//use Phpml\Classification\KNearestNeighbors;
use Phpml\Clustering\KMeans;
use BoyHagemann\Wave\Wave;

$wave = new Wave();
$wave->setFilename('./samples/aa_converted.wav');

print_r($wave);
$metadata = $wave->getMetadata();
$metadata->getName();
$metadata->getSize();
$metadata->getFormat();
$metadata->getChannels();
$metadata->getSampleRate();
$metadata->getBytesPerSecond();
$metadata->getBlockSize();
$metadata->getBitsPerSample();
$metadata->getExtensionSize();
$metadata->getExtensionData();

// Assuming we already analyzed the wave...
$data = $wave->getWaveformData();

// Get the amplitude values for each channel
#foreach($data->getChannels() as $channel) {
#    $amplitudes[] = $channel->getValues();
#}