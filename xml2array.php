<?php

/**
 * XML to PHP-Array
 * 
 * @param SimpleXMLElement $xml
 * @param boolean $root
 * @return array
 */
function xml2array($xml, $root = true)
{
  if (!$xml->children()) {
    return $xml->__toString();
  }

  $array = array();
  foreach ($xml->children() as $element => $node) {
    $totalElement = $xml->{$element}->count();

    if (!isset($array[$element])) {
      $array[$element] = "";
    }

    // Has attributes
    if ($node->attributes()) {
      $data['attributes'] = array();

      foreach ($node->attributes() as $attr => $value) {
        $data['attributes'][$attr] = $value->__toString();
      }

      if ($node->count() > 0) {
        $items = xml2array($node, false);
        if ($node->count() > 1) {
          $data['items'] = $items;
        } else {
          $data[$node->children()->getName()] = $items[$node->children()->getName()];
        }
      } else {
        $data['value'] = $node->__toString();
      }


      if ($totalElement > 1) {
        $array[$element][] = $data;
      } else {
        $array[$element] = $data;
      }

      // Just a value
    } else {
      if ($totalElement > 1) {
        $array[$element][] = xml2array($node, false);
      } else {
        $array[$element] = xml2array($node, false);
      }
    }
  }

  if ($root) {
    $data['attributes'] = null;
    if ($xml->attributes()) {
      foreach ($xml->attributes() as $attr => $value) {
        $data['attributes'][$attr] = $value->__toString();
      }
    }


    if (count($array) > 1) {
      return array($xml->getName() => array(
              'attributes' => $data['attributes'],
              'value'      => $array
      ));
    } else {
      return array($xml->getName() => array(
              'attributes'                => $data['attributes'],
              $xml->children()->getName() => $array[$xml->children()->getName()]
      ));
    }
  } else {
    return $array;
  }
}

/**
 * run
 */
$res = xml2array(simplexml_load_file('./data/accommodation.xml'));

echo '<pre style="background:#444;color:#fff;padding:10px;">';
print_r($res);
echo '</pre>';