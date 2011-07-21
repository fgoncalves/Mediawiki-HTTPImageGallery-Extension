<?php

class HttpImageGallery{
  //const
  const OUTSIDE_HTTP_IMAGE_GALLERY_TAG_STATE = 0;
  const INSIDE_HTTP_IMAGE_GALLERY_TAG_STATE = 1;

  private static $evParserState = self::OUTSIDE_HTTP_IMAGE_GALLERY_TAG_STATE;
  private static $evColumnCounter = 0;
  private static $evNumberOfColumns = 3;

  public static function httpImageGallery_Render( $input, $args, $parser, $frame = null ) {
    $tableValidArgs = array();
    
    //Initialize defaults
    $tableValidArgs['cellpadding'] = 0;
    $tableValidArgs['cellspacing'] = 0;
    $tableValidArgs['class'] = 'httpimagegallery';

    //Add table attrs
    foreach($args as $name => $value){
      switch ($name){
	case 'cellspacing':
	  if(self::sanity_check($value,'integer'))
	    $tableValidArgs['cellspacing'] = $value;
	  break;
	case 'cellpadding':
	  if(self::sanity_check($value,'integer'))
	    $tableValidArgs['cellpadding'] = $value;
	case 'ncolumns':
	  if(self::sanity_check($value,'integer') && $value > 0)
	    self::$evNumberOfColumns = $value;
      }
    }

    $output = Xml::openElement('table', $tableValidArgs);

    //Set appropriate state
    self::$evParserState = self::INSIDE_HTTP_IMAGE_GALLERY_TAG_STATE;
    
    $output .= $parser->recursiveTagParse($input) ;
    
    if(self::$evColumnCounter == 0)
	$output .= Xml::closeElement('tr');
    $output .= Xml::closeElement('table');

    //Reset state
    self::$evParserState = self::OUTSIDE_HTTP_IMAGE_GALLERY_TAG_STATE;
    
    return  $output;
  }

  public static function httpImage_Render( $input, $args, $parser, $frame = null ) {
    //If we haven't parsed a <httpimagegallery> tag yet, than output $input escaped.
    if(self::$evParserState == self::OUTSIDE_HTTP_IMAGE_GALLERY_TAG_STATE)
      return htmlspecialchars($input);
  
    $imgValidAttrs = array();
    $anchorValidAttrs = array();
    $text = null;

    //setup defaults
    $imgValidAttrs['border'] = 0;
    $imgValidAttrs['alt'] = '';
    $imgValidAttrs['width'] = '96';  
    $imgValidAttrs['height'] = '120';

    $foundSrcTag = false;

    //$output = '<img width="96" height="120" border="0" ';
    foreach($args as $name => $value){
      switch($name){
	case 'alt':
	  $imgValidAttrs['alt'] = htmlspecialchars($value);
	  break;
	case 'src':
	  $imgValidAttrs['src'] = self::check_image_url(htmlspecialchars($value));
	  $foundSrcTag = true;
	  break;
	case 'link': 
	  if(self::sanity_check($value,'link'))
	    $anchorValidAttrs['href'] = htmlspecialchars($value); 
	  break;      
	case 'border':
	  if(self::sanity_check($value,'integer'))
	    $imgValidAttrs['border'] = $value;
	  break;
	case 'text':
	  //We let Xml:element function escape html special chars, so here we just do the assignment
	  $text = $value; 
	  continue;
      }
    }

    if(!$foundSrcTag)
      return htmlspecialchars($input);

    $output = Xml::element('img', $imgValidAttrs);

    if(!empty($anchorValidAttrs)){
      $anchorValidAttrs['target'] = '_blank';
      $output = Xml::openElement('a', $anchorValidAttrs) . $output . Xml::closeElement('a');
    }

    $output = Xml::openElement('div', array('class' => 'httpimagegallerythumb')) . $output . Xml::closeElement('div');
    if($text != null){
      $text = Xml::element('p', array('class' => 'httpimagegalleryp', 'align' => 'justify'), $text);
      $output .= Xml::openElement('div', array('class' => 'httpimagegallerytextbox')) . $text . Xml::closeElement('div');
    }

    $output = Xml::openElement('div', array('class' => 'httpimagegallerybox')) . $output . Xml::closeElement('div');

    $output = Xml::openElement('td', array('class' => 'httpimagegallerycell')) . $output . Xml::closeElement('td');
    if(self::$evColumnCounter == 0)
	$output = Xml::openElement('tr', array('class' => 'httpimagegalleryrow')) . $output;
    self::$evColumnCounter++;
    if(self::$evColumnCounter == self::$evNumberOfColumns){
	$output .= Xml::closeElement('tr');
	self::$evColumnCounter = 0;
    }

    return $output;
  }

  private static function sanity_check($value, $type){
    global $wgUrlProtocols;
    switch($type){
	case 'link':
	  $regex = sprintf("/^%s/", wfUrlProtocols());
	  if(!preg_match($regex,$value))
	    return false;
	  return true;
	case 'integer':
	  if(!preg_match('/^\d+$/',$value))
	    return false;
	  return true;
	default:
	  return false;
    }
  }

  //At this point $url can be a local name
  private static function check_image_url( $url ){
    //is it an external image?
    if(self::sanity_check($url,'link'))
      return $url;
    //At this point we assume its an internal image. Let's try to load it.
    return Image::newFromName( $url )->getURL();
  }
}

?>