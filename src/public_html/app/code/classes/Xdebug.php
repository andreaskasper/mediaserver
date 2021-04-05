<?php

class Xdebug {

    public static function show_Exception(Exception $ex) {
        echo('<table class="xdebug-error" dir="ltr" border="1" cellspacing="0" cellpadding="1" style="font-family: \'Open Sans\', Arial, Sans-Serif;">
        <tbody><tr><th align="left" bgcolor="#f57900" colspan="6"><span style="background-color: #cc0000; color: #fce94f; font-size: x-large;">( ! )</span> Xdebug8: '.$ex->getMessage().' in <b><i>'.$ex->getFile().'</i></b> on line <b>'.$ex->getLine().'</b></th></tr>
        <tr><th align="left" bgcolor="#e9b96e" colspan="6">Call Stack</th></tr>
        <tr><th align="center" bgcolor="#eeeeec">#</th><th align="left" bgcolor="#eeeeec">Class</th><th align="left" bgcolor="#eeeeec"></th><th align="left" bgcolor="#eeeeec">Function</th><th align="left" bgcolor="#eeeeec">Arguments</th><th align="left" bgcolor="#eeeeec">Location</th></tr>');
        $rows = $ex->getTrace();
        $i = 0;
        foreach ($rows as $row) {
            $i++;
            echo('<tr>');
            echo('<td bgcolor="#eeeeec" align="center">'.$i.'</td>');
            echo('<td bgcolor="#eeeeec">'.$row["class"].'</td>');
            echo('<td bgcolor="#eeeeec">'.$row["type"].'</td>');
            echo('<td bgcolor="#eeeeec">'.$row["function"].'</td>');
            echo('<td bgcolor="#eeeeec">'.json_encode($row["args"]).'</td>');
            echo('<td title="'.$row["file"].'" bgcolor="#eeeeec">'.$row["file"].'<b>:</b>'.$row["line"].'</td>');
            echo('</tr>');
        }
        echo('</tbody></table>');
    }






}