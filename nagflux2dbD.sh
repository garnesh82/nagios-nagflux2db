#!/bin/bash

workspace=/staging
spoolfile=/staging/raw
importer=importer.php
interval=10		# sleep time
max_ses=500		# flood gate, help to reduce disk io

instruction=/staging/flag		# daemon flag file


logger() {

        date_v=`date`
        message=$1
        log_path=${workspace}/nagflux2db.log
        echo "${date_v} | ${message}" >> $log_path
}

process_import() {

        jason_file=$1
        logger "Processing $jason_file"
        ${workspace}/${importer} ${spoolfile}/${jason_file}

        case "$?" in
        0)
                rm ${spoolfile}/${jason_file}
                logger "${jason_file} processed"
                ;;
        1)
                logger "Couln't connect to DB"
                ;;
        2)
                logger "MYSQL update record error"
                ;;
        3)
                logger "Invalid json file"
                rm ${spoolfile}/${jason_file}
                ;;
        *)
                logger "Your importer isn't working fine"
        esac
}

while true
do
        if [ "`cat $instruction`" -eq "1" ]; then
				# cleaner way to tell daemon to exit; 1 to quit the loop
                exit
        fi

        flist=$(ls -1 $spoolfile | head -${max_ses})
        if [ "$flist" = "" ]; then
                logger "Spool directory is empty"
                sleep $interval
        else
                logger "Analyzing.."
                for i in ${flist}
                do
                        process_import $i
                done
        logger "Snooze.."
        sleep $interval
        fi
done
