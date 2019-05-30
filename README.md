# nagios-nagflux2db
Asychronous method to upload nagios performance data from nagflux to mysql.

1. I am sure at this point of time you have already installed nagflux and it is up and running. If not, please refer to https://github.com/Griesbacher/nagflux

2. Decide where you want to store the performance data generated, Example: /staging/raw/

3. Edit /etc/nagflux/config.gcfg as below, assuming you want to store the perf data in /staging/raw/
[JSONFileExport "one"]
    Enabled = true
    Path = "/staging/raw"
    AutomaticFileRotation = "10"
    
4. Save and restart the nagflux daemon "/usr/local/bin/nagflux -configPath /etc/nagflux/config.gcfg"

5. Create a table to store the perf data as below. Its just a sample. You can improvised as your wish.
CREATE TABLE `nagflux2db` (
  `id` int(20) NOT NULL AUTO_INCREMENT,
  `Time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `Hostname` varchar(20) NOT NULL DEFAULT '',
  `Service` varchar(100) NOT NULL DEFAULT '',
  `PerformanceLabel` varchar(100) NOT NULL DEFAULT '',
  `Unit` varchar(10) DEFAULT NULL,
  `value` decimal(10,3) DEFAULT NULL,
  PRIMARY KEY (`id`,`Time`,`Hostname`,`Service`,`PerformanceLabel`),
  KEY `Time_Hostname_PerformanceLabel` (`Time`,`Hostname`,`PerformanceLabel`)
) ENGINE=InnoDB AUTO_INCREMENT=390782980 DEFAULT ROW_FORMAT=COMPRESSED COMMENT='Historical Perfdata'

6. By now you should know the database name, tablename, username and password. If you stuck here, refer to "https://www.w3schools.com/php/php_mysql_create_table.asp"

7. 2 scripts involved  A) daemon B) importer. Please check in repository.
