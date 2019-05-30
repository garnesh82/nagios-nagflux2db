#!/usr/bin/php

<?php

// EXIT CODE TABLE
// 1 = Couln't connect to DB
// 2 = MYSQL update record error
// 3 = Invalid json file

        $dbserver = "127.0.0.1";
        $dbuser = "";			// db username
        $dbpass = "";			// db password
        $dbname = "";			// db name
        $jfname = "$argv[1]";   // accept filename as argument, ignore this as taken care by daemon from the other side :)

        $con = mysql_connect("$dbserver","$dbuser","$dbpass") or exit (1);
        mysql_select_db("$dbname", $con);

        $jsondata = file_get_contents("$jfname") or exit (3);

        $data = json_decode($jsondata, true);

        json_decode($jsondata);
        switch (json_last_error()) {
                case JSON_ERROR_NONE:
                        echo " - Great. No errors\n";
                break;
                case JSON_ERROR_DEPTH:
                        echo ' - Maximum stack depth exceeded';
                exit (3);
                break;
                case JSON_ERROR_STATE_MISMATCH:
                        echo ' - Underflow or the modes mismatch';
                exit (3);
                break;
                case JSON_ERROR_CTRL_CHAR:
                        echo ' - Unexpected control character found';
                exit (3);
                break;
                case JSON_ERROR_SYNTAX:
                        echo ' - Syntax error, malformed JSON';
                exit (3);
                break;
                case JSON_ERROR_UTF8:
                        echo ' - Malformed UTF-8 characters, possibly incorrectly encoded';
                exit (3);
                break;
                default:
                        echo ' - Unknown error';
                exit (3);
                break;
        }

        $inserts = array();
        foreach ($data as $row) {

                $Filter = $row['Filter'];
                $Hostname = $row['Hostname'];
                $Service = $row['Service'];
                $Command = $row['Command'];
                $PerformanceLabel = $row['PerformanceLabel'];
                $_PerformanceLabel = mysql_real_escape_string($PerformanceLabel);
                $Unit = $row['Unit'];
                $Time = mb_substr($row['Time'], 0, 10);
                $value = $row['Fields']['value'];
                $inserts[] = "(FROM_UNIXTIME('$Time'), '$Hostname', '$Service', '$_PerformanceLabel', '$Unit', '$value')";
        }

        $query = "INSERT INTO nagios_perfdata(Time, Hostname, Service, PerformanceLabel, Unit, value) VALUES ".implode(", ", $inserts);
        if(!mysql_query($query,$con))
        {
                exit (2);
        }
?>
