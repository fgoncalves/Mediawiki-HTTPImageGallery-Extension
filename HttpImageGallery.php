<?php
if( !defined( 'MEDIAWIKI' ) ) {
  echo( "This is an extension to the MediaWiki package and cannot be run standalone.\n" );
  die( -1 );
}

$wgExtensionCredits['parserhook'][] = array(
  'path' => __FILE__,
  'name' => "HttpImageGallery",
  'description' => "Provides a way to create a mediawiki gallery of external images. One can specify internal images too.",
  'version' => 2,
  'author' => "Frederico Gonçalves",
  'url' => "https://github.com/fgoncalves/Midiawiki-HTTPImageGallery-Extension",
);

$wgAutoloadClasses['HttpImageGallery'] = dirname(__FILE__) . '/HttpImageGallery.body.php';
$wgHooks['BeforePageDisplay'][] = array('addHttpImageGalleryCSS');
$wgExtensionFunctions[] = 'efHttpImageGallery_Setup';

function efHttpImageGallery_Setup() {
  global $wgParser;
  $wfImageGalleryTag = 'httpimagegallery';
  $wfImageTag ='httpimage';

  $wgParser->setHook( $wfImageGalleryTag, array( 'HttpImageGallery', 'httpImageGallery_Render'));
  $wgParser->setHook( $wfImageTag, array( 'HttpImageGallery', 'httpImage_Render') );
  return true;
}


function addHttpImageGalleryCSS( &$m_pageObj ){
  return HttpImageGallery::addHttpImageGalleryCSS( $m_pageObj );
}
?>
