<?php

if (! function_exists('human_filesize')) {
  function human_filesize($bytes, $decimals = 2) {
      $size = array('B','kB','MB','GB','TB','PB','EB','ZB','YB');
      $factor = floor((strlen($bytes) - 1) / 3);
      return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$size[$factor];
  }
}
