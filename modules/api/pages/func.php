<?php

function logThis($message, $line, $file){
    $handle = fopen('intlogs.txt', 'a+');
    fwrite($handle, date("l dS of F Y h:i:s A").' - '.$message."\r\nLine:" .$line."\r\nFile:".$file."\r\n");
    fclose($handle);
}