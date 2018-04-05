<?php
function getFile($steamid)
{
    if(isset($steamid)) {
        $filename = 'cache/' . $steamid;
        if (file_exists($filename)) {
            $elapsedTime = time() - filemtime($filename);
            if ((($elapsedTime < (4 * 3600)) ? '0' : '1') == 1) {
                //it has been over 4 hours since refreshed inventory load
                $myfile = fopen($filename, "w");
                $text   = file_get_contents('http://steamcommunity.com/profiles/' . $steamid . '/inventory/json/730/2');
                fwrite($myfile, $text);
                fclose($myfile);
                return $text;
            } else {
                //it hasnt been over 4 hours since refreshed inventory load
                $text = file_get_contents($filename);
                return $text;
            }
        } else {
            $myfile = fopen($filename, "w");
            $text   = file_get_contents('http://steamcommunity.com/profiles/' . $steamid . '/inventory/json/730/2');
            fwrite($myfile, $text);
            fclose($myfile);
            return $text;
        }
    }
}
?>