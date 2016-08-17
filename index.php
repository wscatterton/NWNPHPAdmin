<?php
include_once("module.php");
include_once("character.php");
//##############################################################//
//								//
//			NWN-PHPAdmin 0.9.8			//
//								//
//	Neverwinter Nights Web Based Administrator		//
//	Written by Wayne Catterton				//
//	Previously by Tim Geiges				//
//								//
//	Last Modified 12/24/03					//
//								//
//	www.putercom.org					//
//	ciscoswitch@putercom.org				//
//								//
//##############################################################// 

//#########################//
//Declare and set variables//
//#########################//
$version = "0.9.8";
$authuser = $PHP_AUTH_USER;

global $version, $nwserverdir, $nwserver, $lsdir, $lndir, $mkdir, $rmdir, $awkdir, $grepdir, $psdir, $taildir, $pwddir, $tardir, $zipdir, $unzipdir, $htpasswd, $shadow, $pathwrong;
global $serverdir, $nwservername, $lspath, $lnpath, $mkdirpath, $rmdirpath, $awkpath, $greppath, $pspath, $tailpath, $tarpath, $unzippath, $htpasswdpath, $shadowpath;
global $adusers, $gamesettings, $userad, $sserver, $pchange, $runpass, $charad, $dcharacters, $udfile, $ufile, $savegame, $deletegame, $smessage, $ban, $backupvault;
global $adminusers, $gameset, $useradmin, $startserver, $passchange, $changerunpass, $charadmin, $delcharacters, $updownfile, $uploadfile, $saveagame, $deleteagame, $sendmessage, $bankick, $backup;
global $users, $invaliduser, $type, $dir, $ext, $crondir, $adminemail, $displaycommands;

//###############################//
//Check if NWN Config File Exists//
//###############################//

if (!file_exists("./nwn.config")) {
	$fp = fopen("./nwn.config", w);
        fwrite($fp, "10\n");
        fwrite($fp, "1\n");
        fwrite($fp, "20\n");
        fwrite($fp, "1\n");
        fwrite($fp, "2\n");
        fwrite($fp, "1\n");
        fwrite($fp, "1\n");
        fwrite($fp, "1\n");
        fwrite($fp, "0\n");
        fwrite($fp, "1\n");
	fwrite($fp, "1\n");
	fwrite($fp, "30\n");
	fwrite($fp, "\"\"\n");
	fwrite($fp, "\"\"\n");
	fwrite($fp, "\"\"\n");
	fwrite($fp, "Default NWN Server\n");
	fwrite($fp, "0\n");
	fwrite($fp, "0\n");
	fwrite($fp, "5121\n");
        fclose($fp);
}

loadpath();
checkpath();
createdir(); 
loadusers();
checkusers();
loadoptions();

//##########################//
//Load in the Path variables//
//##########################//

function loadpath() {
	global $nwserverdir, $nwserver, $lsdir, $lndir, $mkdir, $rmdir, $awkdir, $grepdir, $psdir, $taildir, $pwddir, $tardir, $zipdir, $unzipdir, $htpasswd, $shadow, $crondir;
	if (file_exists("./pathfile")) {
		$fp = file("./pathfile");
        	foreach ($fp as $key => $value) {
                	$a = $value;
                	$Option[$key] = $a;
        	}
		$nwserverdir = eregi_replace("[^0-9a-z.-/]", "", $Option[0]);
		$nwserver = "$nwserverdir/".eregi_replace("[^0-9a-z.-/]", "", $Option[1]);
		$lsdir = eregi_replace("[^0-9a-z.-/]", "", $Option[2]);
		$lndir = eregi_replace("[^0-9a-z.-/]", "", $Option[3]);
		$mkdir = eregi_replace("[^0-9a-z.-/]", "", $Option[4]);
		$rmdir = eregi_replace("[^0-9a-z.-/]", "", $Option[5]);
		$awkdir = eregi_replace("[^0-9a-z.-/]", "", $Option[6]);
		$grepdir = eregi_replace("[^0-9a-z.-/]", "", $Option[7]);
		$psdir = eregi_replace("[^0-9a-z.-/]", "", $Option[8]);
		$taildir = eregi_replace("[^0-9a-z.-/]", "", $Option[9]);
		$pwddir = eregi_replace("[^0-9a-z.-/]", "", $Option[10]);
		$tardir = eregi_replace("[^0-9a-z.-/]", "", $Option[11]);
		$zipdir = eregi_replace("[^0-9a-z.-/]", "", $Option[12]);
		$unzipdir = eregi_replace("[^0-9a-z.-/]", "", $Option[13]);
		$htpasswd = eregi_replace("[^0-9a-z.-/]", "", $Option[14]);
		$shadow = eregi_replace("[^0-9a-z.-/]", "", $Option[15]);
		$crondir = eregi_replace("[^0-9a-z.-/]", "", $Option[16]);
	} else {
		$fp = fopen("./pathfile", w);
        	fwrite($fp, "/opt/NWNServer\n");
		fwrite($fp, "nwserver\n");
		fwrite($fp, "/bin/ls\n");
		fwrite($fp, "/bin/ln\n");
		fwrite($fp, "/bin/mkdir\n");
		fwrite($fp, "/bin/rm\n");
		fwrite($fp, "/bin/awk\n");
		fwrite($fp, "/bin/grep\n");
		fwrite($fp, "/bin/ps\n");
		fwrite($fp, "/usr/bin/tail\n");
		fwrite($fp, "/bin/pwd\n");
		fwrite($fp, "/bin/tar\n");
		fwrite($fp, "/usr/bin/zip\n");
		fwrite($fp, "/usr/bin/unzip\n");
		fwrite($fp, "/usr/bin/htpasswd\n");
		fwrite($fp, "NONE\n");
		fwrite($fp, "/usr/bin/crontab\n");
        	fclose($fp);
		$nwserverdir = "/opt/NWNServer";
		$nwserver = "$nwserverdir/nwserver";
		$lsdir = "/bin/ls";
		$lndir = "/bin/ln";
		$mkdir = "/bin/mkdir";
		$rmdir = "/bin/rm";
		$awkdir = "/bin/awk";
		$grepdir = "/bin/grep";
		$psdir = "/bin/ps";
		$taildir = "/usr/bin/tail";
		$pwddir = "/bin/pwd";
		$tardir = "/bin/tar";
		$zipdir = "/usr/bin/zip";
		$unzipdir = "/usr/bin/unzip";
		$htpasswd = "/usr/bin/htpasswd";
		$shadow = "NONE";
		$crondir = "/usr/bin/crontab";
	}
}

//#######################################################//
//Check path's if something not good, bring up Path Page.//
//#######################################################//

function checkpath() {
	global $pathwrong,$nwserverdir,$nwserver,$lsdir,$lndir,$mkdir,$rmdir,$awkdir,$grepdir,$psdir,$taildir,$pwddir,$tardir,$zipdir,$unzipdir,$htpasswd,$shadow,$crondir;
	global $serverdir, $nwservername, $lspath, $lnpath, $mkdirpath, $rmdirpath, $awkpath, $greppath, $pspath, $tailpath, $pwdpath, $tarpath, $zippath, $unzippath, $htpasswdpath, $shadowpath, $cronpath;
	if (file_exists($nwserverdir)) {
		$pathwrong[0] = 0;
	} else {
		$pathwrong[0] = 1;
	}
	if (file_exists($nwserver)) {
		$pathwrong[1] = 0;
	} else {
		$pathwrong[1] = 1;
	}
	if (file_exists($lsdir)) {
		$pathwrong[2] = 0;
	} else {
		$pathwrong[2] = 1;
	}
	if (file_exists($lndir)) {
		$pathwrong[3] = 0;
	} else {
		$pathwrong[3] = 1;
	}
	if (file_exists($mkdir)) {
		$pathwrong[4] = 0;
	} else {
		$pathwrong[4] = 1;
	}
	if (file_exists($rmdir)) {
		$pathwrong[5] = 0;
	} else {
		$pathwrong[5] = 1;
	}
	if (file_exists($awkdir)) {
		$pathwrong[6] = 0;
	} else {
		$pathwrong[6] = 1;
	}
	if (file_exists($grepdir)) {
		$pathwrong[7] = 0;
	} else {
		$pathwrong[7] = 1;
	}
	if (file_exists($psdir)) {
		$pathwrong[8] = 0;
	} else {
		$pathwrong[8] = 1;
	}
	if (file_exists($taildir)) {
		$pathwrong[9] = 0;
	} else {
		$pathwrong[9] = 1;
	}
	if (file_exists($pwddir)) {
                $pathwrong[10] = 0;
        } else {
                $pathwrong[10] = 1;
        }
	if (file_exists($tardir)) {
		$pathwrong[11] = 0;
	} else {
		$pathwrong[11] = 1;
	}
	if (file_exists($zipdir)) {
                $pathwrong[12] = 0;
        } else {
                $pathwrong[12] = 1;
        }
	if (file_exists($unzipdir)) {
		$pathwrong[13] = 0;
	} else {
		$pathwrong[13] = 1;
	}
	if ($htpasswd !="NONE") {
		if (file_exists($htpasswd)) {
			$pathwrong[14] = 0;
		} else {
			$pathwrong[14] = 1;
		}
	} else {
		$pathwrong[14] = 0;
	}
	if (($shadow !="NONE") & ($htpasswd !="NONE")) {
		if (file_exists($shadow)) {
			$pathwrong[15] = 0;
		} else {
			preg_match("/^(.*)?([$\/]+)/", $shadow, $matches);
                        $tempshadow = $matches[1];
			if (file_exists($tempshadow)) {
				$pathwrong[15] = 0;
				$createfiles = 1;	
			} else {
				$pathwrong[15] = 1;
			}
		}
	} else {
		if ($shadow != "NONE") {
			$shadow = "NONE";
			$shadowpath = "NONE";
		}
		$pathwrong[15] = 0;
	}
	if (($shadow == "NONE") | ($htpasswd == "NONE")) {
		if (file_exists("./userpriv")) {
			turnoffauth();
		}
	}
	if ($crondir !="NONE") {
		if (file_exists($crondir)) {
                	$pathwrong[16] = 0;
        	} else {
                	$pathwrong[16] = 1;
        	}
	} else {
		$pathwrong[16] = 0;
	}
	foreach ($pathwrong as $key => $value) {
		if ($value != "0") {
			$temp = 1;
		}
	}
	if ($temp != "") {
		global $serverdir, $nwservername, $lspath, $lnpath, $mkdirpath, $rmdirpath, $awkpath, $greppath, $pspath, $tailpath, $pwdpath, $tarpath, $zippath, $unzippath, $htpasswdpath, $shadowpath, $cronpath;
		if (($serverdir != "") | ($nwservername !="") | ($lspath != "") | ($lnpath != "") | ($mkdirpath != "") | ($rmdirpath !="") | ($awkpath != "") | ($greppath != "") | ($pspath != "") | ($tailpath != "") | ($pwdpath != "") | ($tarpath != "") | ($zippath != "") | ($unzippath != "") | ($htpasswdpath != "") | ($shadowpath != "") | ($cronpath != "")) {  
			$GLOBALS[item] = "changepath";
		} else {
			$GLOBALS[item] = "programpath";
		}
	} else {
		if (($createfiles != 0) & ($GLOBALS[item] != "dohtfiles")) {
         	       $GLOBALS[item] = "htfilecreate";
        	}
	}
}

//##################//
//Create Directories//
//##################//

 function createdir() {
	global $nwserverdir, $mkdir, $lndir;
	if ((file_exists($nwserverdir)) & (file_exists($lndir)) & (file_exists($mkdir))) {
		if (!file_exists("$nwserverdir/zippedmodules")) {
			$command = "$mkdir \"$nwserverdir/zippedmodules\"";
			exec($command);
		}
		if (!file_exists("./ZIPPEDMODULES")) {
			$command = "$lndir -s \"$nwserverdir/zippedmodules\" ./ZIPPEDMODULES";
			exec($command);
		}
		if (!file_exists("./tempupload")) {
			$command = "$mkdir ./tempupload";
			exec($command);
		}
		if (!file_exists("$nwserverdir/moduleinfo")) {
			$command = "$mkdir \"$nwserverdir/moduleinfo\"";
			exec($command);
		}
		if (!file_exists("$nwserverdir/hak")) {
			$command = "$mkdir \"$nwserverdir/hak\"";
			exec($command);
		}
		if (!file_exists("$nwserverdir/movies")) {
			$command = "$mkdir \"$nwserverdir/movies\"";
			exec($command);
        	}
		if (!file_exists("$nwserverdir/music")) {
			$command = "$mkdir \"$nwserverdir/music\"";
                	exec($command);
        	}
		if (!file_exists("$nwserverdir/nwm")) {
			$command = "$mkdir \"$nwserverdir/nwm\"";
			exec($command);
		}
		if (!file_exists("$nwserverdir/modules")) {
       	         	$command = "$mkdir \"$nwserverdir/modules\"";
                	exec($command);
        	}
		if (!file_exists("./HAK")) {
	        	$command = "$lndir -s \"$nwserverdir/hak\" ./HAK";
	                exec($command);
       	 	}
	        if (!file_exists("./MOVIES")) {
                	$command = "$lndir -s \"$nwserverdir/movies\" ./MOVIES";
                	exec($command);
        	}
        	if (!file_exists("./MUSIC")) {
                	$command = "$lndir -s \"$nwserverdir/music\" ./MUSIC";
                	exec($command);
        	}
		if (!file_exists("./NWM")) {
			$command = "$lndir -s \"$nwserverdir/nwm\" ./NWM";
			exec($command);
		}
        	if (!file_exists("./MODULES")) {
                	$command = "$lndir -s \"$nwserverdir/modules\" ./MODULES";
                	exec($command);
        	}
		if ((!file_exists("./SERVERVAULT")) & (file_exists("$nwserverdir/servervault"))) {
			$command = "$lndir -s \"$nwserverdir/servervault\" ./SERVERVAULT";
			exec($command);
		}
		if (!file_exists("./NWNBackup")) {
			$command = "$mkdir ./NWNBackup";
			exec($command);
		}
	}
 } 

//##########//
//Load Users//
//##########//

 function loadusers() {
	global $shadow, $adminusers, $gamesettings, $userad, $sserver, $pchange, $runpass, $charad, $dcharacters, $ufile, $udfile, $savegame, $deletegame, $smessage, $ban, $backupvault;
 	if (($shadow != "NONE") & ($htpasswd != "NONE")) {
		if (file_exists("./userpriv")) {
        		$fp = file("./userpriv");
                		foreach ($fp as $key => $value) {
                        		$a = $value;
                        		$Option[$key] = $a;
                		}
			$adminusers = split(",", eregi_replace("[^0-9a-z, -]", "", $Option[0]));
			$gamesettings = split(",", eregi_replace("[^0-9a-z, -]", "", $Option[1]));
			$userad = split(",", eregi_replace("[^0-9a-z, -]", "", $Option[2]));
			$sserver = split(",", eregi_replace("[^0-9a-z, -]", "", $Option[3]));
			$pchange = split(",", eregi_replace("[^0-9a-z, -]", "", $Option[4]));
			$runpass = split(",", eregi_replace("[^0-9a-z, -]", "", $Option[5]));
			$charad = split(",", eregi_replace("[^0-9a-z, -]", "", $Option[6]));
			$dcharacters = split(",", eregi_replace("[^0-9a-z, -]", "", $Option[7]));
			$udfile = split(",", eregi_replace("[^0-9a-z, -]", "", $Option[8]));
			$ufile = split(",", eregi_replace("[^0-9a-z, -]", "", $Option[9]));
			$savegame = split(",", eregi_replace("[^0-9a-z, -]", "", $Option[10]));
			$deletegame = split(",", eregi_replace("[^0-9a-z, -]", "", $Option[11]));
			$smessage = split(",", eregi_replace("[^0-9a-z, -]", "", $Option[12]));
			$ban = split(",", eregi_replace("[^0-9a-z, -]", "", $Option[13]));
			$backupvault = split(",", eregi_replace("[^0-9a-z, -]", "", $Option[14]));
		} else {
			if (file_exists($shadow)) {
				$fp = file("$shadow");
				$adminusers[0] = substr($fp[0], 0, strlen($fp[0])-strlen (strstr ($fp[0],':')));
			} else {
				$adminusers[0] = "NONE";
			}
			$gamesettings[0] = "NONE";
			$userad[0] = "NONE";
			$sserver[0] = "NONE";
			$pchange[0] = "NONE";
			$runpass[0] = "NONE";
			$charad[0] = "NONE";
			$dcharacters[0] = "NONE";
			$udfile[0] = "NONE";
			$ufile[0] = "NONE";
			$savegame[0] = "NONE";
			$deletegame[0] = "NONE";
			$smessage[0] = "NONE";
			$ban[0] = "NONE";
			$backupvault[0] = "NONE";
			$fp = fopen("./userpriv", w);
		        fwrite($fp, "$adminusers[0]\n");
 			fwrite($fp, "$gamesettings[0]\n");
        		fwrite($fp, "$userad[0]\n");
        		fwrite($fp, "$sserver[0]\n");
        		fwrite($fp, "$pchange[0]\n");
        		fwrite($fp, "$runpass[0]\n");
        		fwrite($fp, "$charad[0]\n");
			fwrite($fp, "$dcharacters[0]\n");
        		fwrite($fp, "$udfile[0]\n");
			fwrite($fp, "$ufile[0]\n");
        		fwrite($fp, "$savegame[0]\n");
        		fwrite($fp, "$deletegame[0]\n");
        		fwrite($fp, "$smessage[0]\n");
        		fwrite($fp, "$ban[0]\n");
        		fwrite($fp, "$backupvault[0]\n");
        		fclose($fp);
		}
	} else {
		$adminusers[0] = "NONE";
	}
 }

//###########//
//Check Users//
//###########//

 function checkusers() {
	global $shadow, $users, $adminusers, $gamesettings, $userad, $sserver, $pchange, $runpass, $charad, $dcharacters, $ufile, $udfile, $savegame, $deletegame, $smessage, $ban, $backupvault, $invaliduser;
 	if (($shadow !="NONE") & ($htpasswd != "NONE") & (file_exists($shadow))) {
		$invaliduser = "";
		$fp = file("$shadow");
        	foreach ($fp as $key => $value) {
                	$a = substr($value, 0, strlen($value)-strlen (strstr ($value,':')));
			if ($temp == "") {
				$temp = $a;
			} else {
                       		$temp = $temp.",$a";
			}
        	}
		$users = split(",", $temp);
		if ($adminusers[0] != "NONE") {
			foreach ($adminusers as $key => $value) {
				if (!preg_grep("/^$value$/i", $users)) {
					if (preg_match("/^$value,$/i", $invaliduser) == "") {
						if ($invaliduser != "") {
							$invaliduser = $invaliduser.",$value";
						} else {
							$invaliduser = $value;
						}
					}
				}
			}
		}
		if ($gamesettings[0] != "NONE") {
			foreach ($gamesettings as $key => $value) {
                        	if (!preg_grep("/^$value$/i", $users)) {
                                	if (preg_match("/^$value,$/i", $invaliduser) == "") {
                                        	if ($invaliduser != "") {
                                                	$invaliduser = $invaliduser.",$value";
                                        	} else {
                                                	$invaliduser = $value;
                                        	}
                                	}
                        	}
                	}
		}
		if ($userad[0] != "NONE") {
			foreach ($userad as $key => $value) {
                        	if (!preg_grep("/^$value$/i", $users)) {
                                	if (preg_match("/^$value,$/i", $invaliduser) == "") {
                                        	if ($invaliduser != "") {
                                                	$invaliduser = $invaliduser.",$value";
                                        	} else {
                                                	$invaliduser = $value;
                                        	}
                                	}
                        	}
                	}
		}
		if ($sserver[0] != "NONE") {
			foreach ($sserver as $key => $value) {
                        	if (!preg_grep("/^$value$/i", $users)) {
                                	if (preg_match("/^$value,$/i", $invaliduser) == "") {
                                        	if ($invaliduser != "") {
                                                	$invaliduser = $invaliduser.",$value";
                                        	} else {
                                                	$invaliduser = $value;
                                        	}
                                	}
                        	}
                	}
		}
		if ($pchange[0] != "NONE") {
			foreach ($pchange as $key => $value) {
				if (!preg_grep("/^$value$/i", $users)) {
                                        if (preg_match("/^$value,$/i", $invaliduser) == "") {
                                                if ($invaliduser != "") {
                                                        $invaliduser = $invaliduser.",$value";
                                                } else {
                                                        $invaliduser = $value;
                                                }
                                        }
                                }
                        }
                }
		if ($runpass[0] != "NONE") {
			foreach ($runpass as $key => $value) {
                                if (!preg_grep("/^$value$/i", $users)) {
                                        if (preg_match("/^$value,$/i", $invaliduser) == "") {
                                                if ($invaliduser != "") {
                                                        $invaliduser = $invaliduser.",$value";
                                                } else {
                                                        $invaliduser = $value;
                                                }
                                        }
                                }
                        }
                }
		if ($charad[0] != "NONE") {
			foreach ($charad as $key => $value) {
                                if (!preg_grep("/^$value$/i", $users)) {
                                        if (preg_match("/^$value,$/i", $invaliduser) == "") {
                                                if ($invaliduser != "") {
                                                        $invaliduser = $invaliduser.",$value";
                                                } else {
                                                        $invaliduser = $value;
                                                }
                                        }
                                }
                        }
                }
		if ($dcharacters[0] != "NONE") {
                        foreach ($dcharacters as $key => $value) {
                                if (!preg_grep("/^$value$/i", $users)) {
                                        if (preg_match("/^$value,$/i", $invaliduser) == "") {
                                                if ($invaliduser != "") {
                                                        $invaliduser = $invaliduser.",$value";
                                                } else {
                                                        $invaliduser = $value;
                                                }
                                        }
                                }
                        }
                }
		if ($udfile[0] != "NONE") {
			foreach ($udfile as $key => $value) {
                                if (!preg_grep("/^$value$/i", $users)) {
                                        if (preg_match("/^$value,$/i", $invaliduser) == "") {
                                                if ($invaliduser != "") {
                                                        $invaliduser = $invaliduser.",$value";
                                                } else {
                                                        $invaliduser = $value;
                                                }
                                        }
                                }
                        }
                }
		if ($ufile[0] != "NONE") {
                        foreach ($ufile as $key => $value) {
                                if (!preg_grep("/^$value$/i", $users)) {
                                        if (preg_match("/^$value,$/i", $invaliduser) == "") {
                                                if ($invaliduser != "") {
                                                        $invaliduser = $invaliduser.",$value";
                                                } else {
                                                        $invaliduser = $value;
                                                }
                                        }
                                }
                        }
                }
		if ($savegame[0] != "NONE") {
			foreach ($savegame as $key => $value) {
                                if (!preg_grep("/^$value$/i", $users)) {
                                        if (preg_match("/^$value,$/i", $invaliduser) == "") {
                                                if ($invaliduser != "") {
                                                        $invaliduser = $invaliduser.",$value";
                                                } else {
                                                        $invaliduser = $value;
                                                }
                                        }
                                }
                        }
                }
		if ($deletegame[0] != "NONE") {
			foreach ($deletegame as $key => $value) {
                                if (!preg_grep("/^$value$/i", $users)) {
                                        if (preg_match("/^$value,$/i", $invaliduser) == "") {
                                                if ($invaliduser != "") {
                                                        $invaliduser = $invaliduser.",$value";
                                                } else {
                                                        $invaliduser = $value;
                                                }
                                        }
                                }
                        }
                }
		if ($smessage[0] != "NONE") {
			foreach ($smessage as $key => $value) {
                                if (!preg_grep("/^$value$/i", $users)) {
                                        if (preg_match("/^$value,$/i", $invaliduser) == "") {
                                                if ($invaliduser != "") {
                                                        $invaliduser = $invaliduser.",$value";
                                                } else {
                                                        $invaliduser = $value;
                                                }
                                        }
                                }
                        }
                }
		if ($ban[0] != "NONE") {
			foreach ($ban as $key => $value) {
                                if (!preg_grep("/^$value$/i", $users)) {
                                        if (preg_match("/^$value,$/i", $invaliduser) == "") {
                                                if ($invaliduser != "") {
                                                        $invaliduser = $invaliduser.",$value";
                                                } else {
                                                        $invaliduser = $value;
                                                }
                                        }
                                }
                        }
                }
		if ($backupvault[0] != "NONE") {
			foreach ($backupvault as $key => $value) {
                                if (!preg_grep("/^$value$/i", $users)) {
                                        if (preg_match("/^$value,$/i", $invaliduser) == "") {
                                                if ($invaliduser != "") {
                                                        $invaliduser = $invaliduser.",$value";
                                                } else {
                                                        $invaliduser = $value;
                                                }
                                        }
                                }
                        }
                }
		if ($invaliduser != "") {
			global $adusers, $gameset, $useradmin, $startserver, $passchange, $changerunpass, $charadmin, $delcharacters, $uploadfile, $updownfile, $saveagame, $deleteagame, $sendmessage, $bankick, $backup;
			if (($adusers !="") | ($gameset !="") | ($useradmin != "") | ($startserver != "") | ($passchange != "") | ($changerunpass != "") | ($charadmin != "") | ($delcharacters !="") | ($updownfile != "") | ($uploadfile !="") | ($saveagame != "") | ($deleteagame != "") | ($sendmessage !="") | ($bankick != "") | ($backup != "")) {
				$GLOBALS[item] = "changeadminsettings";
			} else {
				$GLOBALS[item]="adminsettings";
			}
		}
	} 
 }

//################################//
//Turn off Authentication Function//
//################################//

 function turnoffauth() {
	global $adminusers, $authuser, $serverdir, $nwservername, $lspath, $lnpath, $mkdirpath, $rmdirpath, $awkpath, $greppath, $pspath, $tailpath, $pwdpath, $tarpath, $zippath, $unzippath, $htpasswdpath, $shadowpath, $cronpath;
        global $shadow, $htpasswd, $nwserverdir, $nwserver, $lsdir, $lndir, $mkdir, $rmdir, $awkdir, $grepdir, $psdir, $taildir, $pwddir, $tardir, $zipdir, $unzipdir, $crondir;
	if (file_exists("./.htaccess")) {
		$command = "$rmdir -fr ./.htaccess";
        	exec($command);
        } else {
		echo "it does not exist";
		exit();
	}
	$adminusers[0] = "NONE";
	$gamesettings[0] = "NONE";
	$userad[0] = "NONE";
	$sserver[0] = "NONE";
	$pchange[0] = "NONE";
	$runpass[0] = "NONE";
	$charad[0] = "NONE";
	$dcharacters[0] = "NONE";
	$udfile[0] = "NONE";
	$ufile[0] = "NONE";
	$savegame[0] = "NONE";
	$deletegame[0] = "NONE";
	$smessage[0] = "NONE";
	$ban[0] = "NONE";
	$backupvault[0] = "NONE";	
	if (file_exists("./userpriv")) {
		$command = "$rmdir -fr ./userpriv";
		exec($command);
		$adminusers[0] = "NONE";
	}
        if (file_exists($shadow)) {
        	$command = "$rmdir -fr $shadow";
        	exec($command);
        }
        $shadow = "NONE";
        $htpasswd = "NONE";
        $fp = fopen("./pathfile", w);
        fwrite($fp, "$nwserverdir\n");
        fwrite($fp, "nwserver\n");
        fwrite($fp, "$lsdir\n");
        fwrite($fp, "$lndir\n");
        fwrite($fp, "$mkdir\n");
        fwrite($fp, "$rmdir\n");
        fwrite($fp, "$awkdir\n");
        fwrite($fp, "$grepdir\n");
        fwrite($fp, "$psdir\n");
        fwrite($fp, "$taildir\n");
        fwrite($fp, "$pwddir\n");
        fwrite($fp, "$tardir\n");
        fwrite($fp, "$zipdir\n");
        fwrite($fp, "$unzipdir\n");
        fwrite($fp, "$htpasswd\n");
        fwrite($fp, "$shadow\n");
        fwrite($fp, "$crondir\n");
        fclose($fp);
 }

//############//
//Load Options//
//############//
 function loadoptions() {
 	global $adminemail, $displaycommands;
	if (file_exists("./nwnsaoptions")) {
		$fp = file("./nwnsaoptions");
		foreach ($fp as $key => $value) {
                	$Option[$key] = $value;
        	}
		$adminemail = eregi_replace("[^0-9a-z.-_@/]", "", $Option[0]);
		$displaycommands = eregi_replace("[^0-9a-z]", "", $Option[1]);
	} else {
		$fp = fopen("./nwnsaoptions", w);
		fwrite($fp, "yourname@domain.com\n");
		fwrite($fp, "Show\n");
		fclose($fp);
		$adminemail = "yourname@domain.com";
		$displaycommands = "Show";
	}
 }
		
$checkprocess = "$psdir -ax | $grepdir -c nwserver";
$procnum = exec($checkprocess);

//###############//
//Build Main Menu//
//###############//

 $welcome = "Welcome to NWN Administration $version by Wayne Catterton<br>Based on NWN-PHPAdmin by Tim Geiges";
 $welcome2 = "NWN Server Administration"; 

 echo ("
 <HTML>
 <TITLE>$welcome</TITLE>
 <BODY BACKGROUND=images/nwnmainrock.gif BGPROPERTIES=fixed BGCOLOR=000000 text=00ffff alink=00ffff link=ffff00 vlink=ffff00>
 <CENTER>
 <TABLE WIDTH=80% BGCOLOR=ffcc33 CELLPADDING=1 CELLSPACING=0><TR><TD>
 <TABLE WIDTH=100% BACKGROUND=images/nwnrock.gif BGCOLOR=000000 cellpadding=30>
 <TR><TD><a href=http://nwn.bioware.com TARGET=_BLANK><IMG SRC=images/nwnlogo.gif BORDER=0></a></TD><TD><CENTER><h2><b>$welcome2</b></h2></CENTER></TD><TD><a href=http://nwn.bioware.com TARGET=_BLANK><IMG SRC=images/nwnlogo.gif BORDER=0></a></TD></TR>
 </TABLE>
 </TR></TD></TABLE> 
 </CENTER>
 <p>
 <CENTER>
 <TABLE WIDTH=90% BGCOLOR=ffcc33 CELLPADDING=.5 CELLSPACING=0><TR><TD>
 <TABLE WIDTH=100%>
 <!-- MENU HERE -->
 <TR>
 <TD BGCOLOR=808080 BACKGROUND=images/nwnwood.gif WIDTH=25% valign=top>
 Administration Menu
 <br>
 ");
 if ((preg_grep ("/^$authuser$/i", $adminusers)) | ($adminusers[0] == "NONE")) {
 	echo "<li><a href=index.php?item=programpath>Set Program Path's</a><br>";
 }
 if (($shadow != "NONE") & ($htpasswd != "NONE")) { 
	if ((preg_grep ("/^$authuser$/i", $adminusers)) | ($adminusers[0] == "NONE")) {
 		echo "<li><a href=index.php?item=adminsettings>Administration Settings</a><br>";
	}
 }
 if ((preg_grep ("/^$authuser$/i", $adminusers)) | ($adminusers[0] == "NONE")) {
	echo "<li><a href=index.php?item=nwnsaoptions>Options</a><br>";
 }
 if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $backupvault))) {
        echo "<li><a href=index.php?item=backup>Backup Administration</a>";
 } else {
	if ($displaycommands == "Show") {
        	echo "<li>Backup Administration";
	}
 }
 if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $gamesettings))) {
 	echo "<li><a href=index.php?item=settings>Game Settings</a>";
 } else {
	if ($displaycommands == "Show") {
 		echo "<li>Game Settings";
	}
 }
 if (($shadow != "NONE") & ($htpasswd != "NONE")) {
	if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $userad))) {
 		echo "<li><a href=index.php?item=useradmin>User Administration</a>";
 	} else {
		if ($displaycommands == "Show") {
			echo "<li>User Administration";
		}
 	}
 }
 if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $charad)) || (preg_grep ("/^$authuser$/i", $dcharacters))) {
	echo "<li><a href=index.php?item=characteradmin>Character Administration</a>";
 } else {
	if ($displaycommands == "Show") {
		echo "<li>Character Administration";
	}
 }
 if($procnum > 2) {
	if ($displaycommands == "Show") {
 		echo "<li>Start nwserver";
	}
 } else {
	if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $sserver))) {
 		echo "<li><a href=index.php?item=start>Start nwserver</a>";
	} else {
		if ($displaycommands == "Show") {
			echo "<li>Start nwserver";
		}
	}
 }
 if($procnum < 3) {
	if ($displaycommands == "Show") {
 		echo "<li>Kill nwserver nicely";
 		echo "<br>";
 		echo "<li>Kill nwserver immediately";
	}
 } else {
 	if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $sserver))) {
 		echo "<li><a href=index.php?item=stopnice>Kill nwserver nicely</a>";
 		echo "<br>";  
 		echo "<li><a href=index.php?item=stop>Kill nwserver immediately</a>";
 	} else {
		if ($displaycommands == "Show") {
 			echo "<li>Kill nwserver nicely";
 			echo "<br>";
 			echo "<li>Kill nwserver immediately";
		}
	}
       
 }
 echo ("
 <br>
 <hr width=100%>
 Additional Options
 <br>
 ");
 if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $pchange))) {
 	echo "<li><a href=index.php?item=changeuserpass>Change Your Password</a>";
 } else {
	if ($displaycommands == "Show") {
		echo "<li>Change Your Password";
	}
 }
if($procnum < 3) {
if ($displaycommands == "Show") {
	echo "<li>Change Running Passwords";
}
} else {
if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $runpass))) {
	echo "<li><a href=index.php?item=changepass>Change Running Passwords</a>";
} else {
	if ($displaycommands == "Show") {
		echo "<li>Change Running Passwords";
	}
}
}
if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $udfile)) || (preg_grep ("/^$authuser$/i", $ufile))) {
echo "<li><a href=index.php?item=hmmm>HAK, MOD, BIK & BMU</a>";
} else {
if ($displaycommands == "Show") {
	echo "<li>HAK, MOD, BIK & BMU";
}
}
if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $sserver))) {
echo "<li><a href=index.php?item=loadmod>Load New Module</a><br>";
echo "<li><a href=index.php?item=load>Load Saved Game</a>";
} else {
if ($displaycommands == "Show") {
	echo "<li>Load New Module<br>";
	echo "<li>Load Saved Game";
}
}
if($procnum < 3) {
if ($displaycommands == "Show") {
	echo "<li>Save Game";
}
} else {
if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $savegame))) { 
	echo "<li><a href=index.php?item=save>Save Game</a>";
} else {
	if ($displaycommands == "Show") {
		echo "<li>Save Game";
	}
}
}
if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $deletegame))) {
echo "<li><a href=index.php?item=deletemod>Delete a Save Game</a>";
echo "<br>";
} else {
if ($displaycommands == "Show") {
	echo "<li>Delete A Save Game";
}
}
if ($procnum < 3) {
if ($displaycommands == "Show") {
	echo "<li>Send Message To Players";
}
} else {
if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $smessage))) {
	echo "<li><a href=index.php?item=say>Send Message To Players</a>";
} else {
	if ($displaycommands == "Show") {
		echo "<li>Send Message To Players";
	}
}
}
if($procnum < 3) {
if ($displaycommands == "Show") {
	echo "<li>Ban Info";
}
} else {
if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $ban))) {
	echo "<li><a href=index.php?item=baninfo>Ban Info</a>";
} else {
	if ($displaycommands == "Show") {
		echo "<li>Ban Info";
	}
}
}
echo ("
<li><a href=index.php?item=status>Server Status</a>
<br>
<br>
<li><a href=index.php?item=>Info</a>
</TD>
<!-- END MENU -->



<!-- BODY HERE -->
<TD WIDTH=75% BACKGROUND=images/graysand.gif valign=top>
");


//########################################################//
//Info Section, defaults opening, when you first load page//
//########################################################//

if($item == "") {
echo "<BACKGROUND=/images/nwnrock.gif>";
echo $welcome;
if($procnum > 2) {
	echo "<br><br><h2>nwserver is running.</h2>";
	$starttime = "$grepdir \"Loading\" \"$nwserverdir/logs.0/nwserverLog1.txt\" | awk 'NR == 1{print $1,$2,$3,$4}'";
	$startedat = exec($starttime);
	$playeron = "$grepdir -c \"Joined as Player\" \"$nwserverdir/logs.0/nwserverLog1.txt\"";
	$playerquit = "$grepdir -c \"Left as a Player\" \"$nwserverdir/logs.0/nwserverLog1.txt\"";
	$playerson = exec($playeron);
	$playersquit = exec($playerquit);
	$playertot = $playerson - $playersquit;
	echo "<b>Server Started = $startedat</b><br>";
	echo "<b>Players that joined since $startedat = <a href=index.php?item=fulllist>$playerson</a></b><br>";
	echo "<b>Most Players Online at Once = $mostplayers</b><br>";
	echo "<b>Players Online Now= $playertot</b><br>";
	$grepnum = $playertot + 13;
	$runcmd = "$lsdir -d $nwserverdir/* | $grepdir 'logs.[1-9]'";
	unset ($statusResult);
	exec($runcmd, $statusResult, $statusReturnValue);
	reset ($statusResult);
	if (count($statusResult) > 0) {
		echo "<font color=ffff00><center><h2>Log File Error</h2></center><br>";
		echo "<center><h2>Please Save your game, then</h2></center><br>";
		echo "<center><h2>Stop/Start the Server</h2></center></font>";
		echo "<br><center>Removed player logs from server</center>";
		$runcmd = "$rmdir -fr $nwserverdir/logs.*";
		exec ($runcmd);
		exit();
	}
	$runcmd = "./clear";
	exec($runcmd);
	$status = "./status | $taildir -n $grepnum";
	unset ($statusResult);
	exec ($status, $statusResult, $statusReturnValue);
	reset ($statusResult);
	while (list($key,$val) = each($statusResult))
	{
		$fp = fopen("./playerlist", a);
		fwrite($fp, "$val\n");
	}
	fclose($fp);
	echo "<hr>";
	$serverstats = "$awkdir 'NR < 11{print $0}' ./playerlist";
	unset ($ssResult);
	exec ($serverstats, $ssResult, $ssReturnValue);
	reset ($ssResult);
	if ($procnum >=3) {
		if ((!preg_grep ("/Server Name/i", $ssResult)) || (!preg_grep ("/Maximum Clients/i", $ssResult)) || (!preg_grep ("/Server Port/i", $ssResult)) || (!preg_grep ("/Module Name/i", $ssResult)) || (!preg_grep ("/Module Status/i", $ssResult)) || (!preg_grep ("/PVP/i", $ssResult)) || (!preg_grep ("/Difficulty/i", $ssResult)) || (!preg_grep ("/ELC/i", $ssResult)) || (!preg_grep ("/One Party/i", $ssResult)) || (!preg_grep ("/Reload when Empty/i", $ssResult))) {
			echo "<center><h2>Status <font color=00ffff>NOT</font> Available!</h2></center><br>";
			echo "<center><h2>Reloading Status!</h2></center>";
			@unlink("./playerlist");
			echo "<META HTTP-EQUIV=Refresh content=10;URL=index.php>";
			exit();
		}
	}
	$temp = $ssResult[3];
	$runningmod = substr($temp, (strpos ($temp,': ') + 2), strlen($temp));
	while (list($key,$val1) = each($ssResult))
	{
		if (preg_match ("/Module Name/i", $ssResult[$key])) {
			echo "<br>Module Name: <a href=\"index.php?item=moduleinformation&runningmod=$runningmod\">$runningmod</a>";
		} else {
			echo "<br>$val1";
		}
	}
	echo "<br><br>";
} else {
	echo "<br><br><h2>nwserver is <font color=ff0000>NOT</font> running.</h2>";
}

echo "<br>Written by Wayne Catterton";
echo "<br><a href=http://www.putercom.org TARGET=_BLANK>PuterCom Home</a>";
echo "<br><a href=mailto:ciscoswitch@putercom.org>ciscoswitch@putercom.org</a>";
echo "<br><br>Originally by Tim Geiges";
echo "<br><a href=http://www.watchmefreak.com TARGET=_BLANK>www.watchmefreak.com</a>";
echo "<br><a href=mailto:tim@lvcm.com>tim@lvcm.com</a>";
if($show == "") {
	echo "<br><br><br><a href=index.php?item=&show=TRUE>Additional Help/Credits</a><br>";
}

if($show == "TRUE") {
	echo "<br><br>Module Info Integrated by Richard O'Doherty-Gregg";
	echo "<br>provided by zoligato's PHP Mod Hak Erf preview V0.1";
	echo "<br><a href=mailto:zoligato@hotmail.com>zoligato@hotmail.com</a>";
	echo "<br><br>Misc bug fixes/patches provided by Richard O'Doherty-Gregg";
	echo "<br><a href=mailto:OdGregg@bigpond.com>OdGregg@bigpond.com</a>";
}
@unlink("./playerlist");
}

//#################//
//Program Path Form//
//#################//

if($item == "programpath") {
global $adminusers, $authuser, $pathwrong,$nwserverdir,$nwserver,$lsdir,$lndir,$mkdir,$rmdir,$awkdir,$grepdir,$psdir,$taildir,$pwddir,$tardir,$zipdir,$unzipdir,$htpasswd,$shadow,$crondir;
global $serverdir, $nwservername, $lspath, $lnpath, $mkdirpath, $rmdirpath, $awkpath, $greppath, $pspath, $tailpath, $pwdpath, $tarpath, $zippath, $unzippath, $htpasswdpath, $shadowpath, $cronpath;
if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE")) {
	$fp = file("./pathfile");
	foreach ($fp as $key => $value) {
		$a = $value;
		$Option[$key] = $a;
	}
	foreach ($pathwrong as $key => $value) {
			if ($value == "1") {
				$Option[$key] = "*Invalid*Path*";
				$pathwrong[$key] = 0;
				$temp = 1;
			}
		}
		if ($temp == "1") {
			echo "<b>Incorrect Path, Please check them!</b><br>";
		}
 		echo "<h2><b>Program Path's</b></h2>";
		echo ("
		<TABLE>
		<form name=path method=post action=index.php>
		<input type=hidden name=item value=changepath>
		<TR><TD>Neverwinter Server Directory</TD><TD><input type=text name=serverdir size=15 maxlength=35 value=$Option[0]></TD><TD>Do not include <b>/nwserver</b> at the end.</TD></TR>
		<TR><TD>Neverwinter Server executable name</TD><TD><input type=text name=nwservername size=15 maxlength=25 value=$Option[1]></TD><TD>Name of NeverWinter Server executable.</TD></TR>
		<TR><TD>Path to ls</TD><TD><input type=text name=lspath size=15 maxlength=25 value=$Option[2]></TD><TD>Please include <b>ls</b> executable in path.</TD></TR>
		<TR><TD>Path to ln</TD><TD><input type=text name=lnpath size=15 maxlength=25 value=$Option[3]></TD><TD>Please include <b>ln</b> executable in path.</TD></TR>
		<TR><TD>Path to mkdir</TD><TD><input type=text name=mkdirpath size=15 maxlength=25 value=$Option[4]></TD><TD>Please include <b>mkdir</b> executable in path.</TD></TR>
		<TR><TD>Path to rm</TD><TD><input type=text name=rmdirpath size=15 maxlength=25 value=$Option[5]></TD><TD>Please include <b>rm</b> executable in path.</TD></TR>
		<TR><TD>Path to awk</TD><TD><input type=text name=awkpath size=15 maxlength=25 value=$Option[6]></TD><TD>Please include <b>awk</b> executable in path.</TD></TR>
		<TR><TD>Path to grep</TD><TD><input type=text name=greppath size=15 maxlength=25 value=$Option[7]></TD><TD>Please include <b>grep</b> executable in path.</TD></TR>
		<TR><TD>Path to ps</TD><TD><input type=text name=pspath size=15 maxlength=25 value=$Option[8]></TD><TD>Please include <b>ps</B> executable in path.</TD></TR>
		<TR><TD>Path to tail</TD><TD><input type=text name=tailpath size=15 maxlength=25 value=$Option[9]></TD><TD>Please include <b>tail</b> executable in path.</TD></TR>
		<TR><TD>Path to pwd</TD><TD><input type=text name=pwdpath size=15 maxlength=25 value=$Option[10]></TD><TD>Please include <b>pwd</b> executable in path.</TD></TR>
		<TR><TD>Path to tar</TD><TD><input type=text name=tarpath size=15 maxlength=25 value=$Option[11]></TD><TD>Please include <b>tar</b> executable in path.</TD></TR>
		<TR><TD>Path to zip</TD><TD><input type=text name=zippath size=15 maxlength=25 value=$Option[12]></TD><TD>Please include <b>zip</b> executable in path.</TD></TR>
		<TR><TD>Path to unzip</TD><TD><input type=text name=unzippath size=15 maxlength=25 value=$Option[13]></TD><TD>Please include <b>unzip</b> executable in path.</TD></TR>
		<TR><TD>Path to htpasswd</TD><TD><input type=text name=htpasswdpath size=15 maxlength=25 value=$Option[14]></TD><TD>Please include <b>htpasswd</b> executable in path. Set to NONE if not using htpasswd authentication.</TD></TR>
		<TR><TD>Path to htpasswd Authentication file</TD><TD><input type=text name=shadowpath size=15 maxlength=35 value=$Option[15]></TD><TD>Please include name of auth file in path. Set to NONE if not using any authentication.</TD></TR>
		<TR><TD>Path to crontab</TD><TD><input type=text name=cronpath size=15 maxlength=25 value=$Option[16]></TD><TD>Please include <b>crontab</b> executable in path. Set to NONE if you prefer to schedule your own cron job.</TD></TR>
		<TR><TD><input type=submit></form></TD><TD></TD></TR>
		</TABLE>
		");
	} else {
		echo "Nice Try, but you do not have access to this section!";
	}
 }

//####################//
//Change Path Settings//
//####################//

 if($item == "changepath") {
	global $adminusers, $authuser, $serverdir, $nwservername, $lspath, $lnpath, $mkdirpath, $rmdirpath, $awkpath, $greppath, $pspath, $tailpath, $pwdpath, $tarpath, $zippath, $unzippath, $htpasswdpath, $shadowpath, $cronpath;
	if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE")) {
        	if (!file_exists("./ZIPPEDMODULES")) {
                	$command = "$rmdir -fr ./ZIPPEDMODULES";
                	exec($command);
        	}
        	if (!file_exists("./HAK")) {
                	$command = "$rmdir -fr ./HAK";
                	exec($command);
        	}
        	if (!file_exists("./MOVIES")) {
                	$command = "$rmdir -fr ./MOVIES";
                	exec($command);
        	}
        	if (!file_exists("./MUSIC")) {
                	$command = "$rmdir -fr ./MUSIC";
                	exec($command);
        	}
		if (!file_exists("./NWM")) {
			$command = "$rmdir -fr ./NWM";
			exec($command);
		}	
        	if (!file_exists("./MODULES")) {
                	$command = "$rmdir -fr ./MODULES";
                	exec($command);
        	}
        	if ((!file_exists("./SERVERVAULT")) & (file_exists("$nwserverdir/servervault"))) {
                	$command = "$rmdir -fr ./SERVERVAULT";
                	exec($command);
       		}
		$nwserverdir = $serverdir;
		$nwserver = $nwservername;
		$lsdir = $lspath;
		$lndir = $lnpath;
		$mkdir = $mkdirpath;
		$rmdir = $rmdirpath;
		$awkdir = $awkpath;
		$grepdir = $greppath;
		$psdir = $pspath;
		$taildir = $tailpath;
		$pwddir = $pwdpath;
		$tardir = $tarpath;
		$zipdir = $zippath;
		$unzipdir = $unzippath;
		$htpasswd = $htpasswdpath;
		$shadow = $shadowpath;
		$crondir = $cronpath;
		$fp = fopen("./pathfile", w);
        	fwrite($fp, "$nwserverdir\n");
        	fwrite($fp, "$nwserver\n");
        	fwrite($fp, "$lsdir\n");
		fwrite($fp, "$lndir\n");
		fwrite($fp, "$mkdir\n");
		fwrite($fp, "$rmdir\n");
        	fwrite($fp, "$awkdir\n");
        	fwrite($fp, "$grepdir\n");
        	fwrite($fp, "$psdir\n");
        	fwrite($fp, "$taildir\n");
		fwrite($fp, "$pwddir\n");
        	fwrite($fp, "$tardir\n");
		fwrite($fp, "$zipdir\n");
		fwrite($fp, "$unzipdir\n");
        	fwrite($fp, "$htpasswd\n");
        	fwrite($fp, "$shadow\n");
		fwrite($fp, "$crondir\n");
        	fclose($fp);
		$serverdir = "";
		$nwservername = "";
		$lspath = "";
		$lnpath = "";
		$mkdirpath = "";
		$rmdirpath = "";
		$awkpath = "";
		$greppath = "";
		$pspath = "";
		$tailpath = "";
		$pwdpath = "";
		$tarpath = "";
		$zippath = "";
		$unzippath = "";
		$htpasswdpath = "";
		$shadowpath = "";
		$cronpath = "";
		if (($shadow != "NONE") & ($htpasswd != "NONE") & (file_exists($shadow))) {
			if (file_exists("./.htaccess")) {
                		$command = "$rmdir -fr ./.htaccess";
                		exec($command);
                	}
                	$fp = fopen("./.htaccess", w);
                	fwrite($fp, "AuthName \"NWN Server Administration Login\"\n");
                	fwrite($fp, "AuthType Basic\n");
                	fwrite($fp, "AuthUserfile $shadow\n");
                	fwrite($fp, "Require valid-user\n");
                	fclose($fp);
		}
		echo "Path's Changed and Saved.";
		echo "<META HTTP-EQUIV=Refresh content=1;URL=index.php>";
	} else {
		echo "Nice Try, but you do not have access to this section!";
	}
 }

//############################################//
//Create .htaccess & user authentication files//
//############################################//

if($item == "htfilecreate") {
	global $adminusers, $authuser, $serverdir, $nwservername, $lspath, $lnpath, $mkdirpath, $rmdirpath, $awkpath, $greppath, $pspath, $tailpath, $pwdpath, $tarpath, $zippath, $unzippath, $htpasswdpath, $shadowpath, $cronpath;
        if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE")) {
		echo "<h2><b>Create Authentication File</b></h2>";
                echo ("
		Setting up User Authentication for the first time.  We will<br>
		need to create an initial Username and Password, and grant that<br>
		 user Administration priviledges.  Also we will need to create the<br>
		 <b>.htaccess</b> file to enable for logons on Apache.  Please enter your<br>
		 <b>UserName</b> and <b>Password</b> below.<br>
		<br>If you wish to cancel, click on the cancel button, then press OK.<br>
		or fill in <b>NONE</b> for both Username and Password and click OK.<br><br>
                <form name=createfiles method=post action=index.php>
                <input type=hidden name=item value=dohtfiles>
                UserName &nbsp; &nbsp; <input type=text name=htuser size=15 maxlength=35>
		&nbsp; &nbsp; &nbsp; &nbsp;
		Password &nbsp; &nbsp; <input type=password name=htpassword size=15 maxlength=35>
		<br><br>
		&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;
		&nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp;
		<input type=submit value=OK>&nbsp; &nbsp;<input type=button name=htcancel value=Cancel onclick=htcancelbutton();>
		<script>
		function htcancelbutton() {
                        document.createfiles.htuser.value = \"NONE\";
                        document.createfiles.htpassword.value = \"NONE\";
		}
                </script>
		</form>
		");
        } else {
                echo "Nice Try, but you do not have access to this section!";
        }
}

//##############################//
//Creating .htaccess & user auth//
//##############################//

if($item == "dohtfiles") {
	global $adminusers, $authuser, $serverdir, $nwservername, $lspath, $lnpath, $mkdirpath, $rmdirpath, $awkpath, $greppath, $pspath, $tailpath, $pwdpath, $tarpath, $zippath, $unzippath, $htpasswdpath, $shadowpath, $cronpath;
	global $shadow, $htpasswd, $nwserverdir, $nwserver, $lsdir, $lndir, $mkdir, $rmdir, $awkdir, $grepdir, $psdir, $taildir, $pwddir, $tardir, $zipdir, $unzipdir, $crondir;
        if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE")) {
		if (($htuser != "") & ($htpassword != "")) {
			if (($htuser == "NONE") & ($htpassword == "NONE")) {
				turnoffauth();
				echo "You must close this browser and reopen a new one!";
        			exit();
			} else {
				$command = "$htpasswd -cb $shadow $htuser $htpassword";
				exec($command);
				echo "$shadow file created<br>";
				echo "User <b>$htuser</b> added to $shadow<br>";
				if (file_exists("./.htaccess")) {
					$command = "$rmdir -fr ./.htaccess";
					exec($command);
				}
				$fp = fopen("./.htaccess", w);
	        		fwrite($fp, "AuthName \"NWN Server Administration Login\"\n");
        			fwrite($fp, "AuthType Basic\n");
        			fwrite($fp, "AuthUserfile $shadow\n");
				fwrite($fp, "Require valid-user\n");
        			fclose($fp);
				echo "<b>.htaccess</b> file created<br>";
				if (file_exists("./userpriv")) {
					$command = "$rmdir -fr ./userpriv";
					exec($command);
				}
				if (file_exists($shadow)) {
                                	$fp = file("$shadow");
                                	$adminusers[0] = substr($fp[0], 0, strlen($fp[0])-strlen (strstr ($fp[0],':')));
                        	} else {
                                	$adminusers[0] = "NONE";
                       	 	}
                        	$fp = fopen("./userpriv", w);
                        	fwrite($fp, "$adminusers[0]\n");
                        	fwrite($fp, "NONE\n");
                        	fwrite($fp, "NONE\n");
                        	fwrite($fp, "NONE\n");
                        	fwrite($fp, "NONE\n");
                        	fwrite($fp, "NONE\n");
                        	fwrite($fp, "NONE\n");
                        	fwrite($fp, "NONE\n");
                        	fwrite($fp, "NONE\n");
                        	fwrite($fp, "NONE\n");
                        	fwrite($fp, "NONE\n");
                        	fwrite($fp, "NONE\n");
                        	fwrite($fp, "NONE\n");
                        	fclose($fp);
				echo "<b>$htuser</b> added as Administrator<br>";
				echo "<b>Completed</b><br>";
				echo "<br><b>NOTE:</b> Now you must close your browser and reopen at new browser, and you<br>";
				echo "should be able to login with your new username and password.";
			}
		} else {
			echo "You need to enter a <b>UserName</b> and <b>Password</b>!!";
			$GLOBALS[item]="htfilecreate";
			$createfiles = 1;
			echo "<META HTTP-EQUIV=Refresh content=1;URL=index.php?item=htfilecreate>";
		}
	} else {
                echo "Nice Try, but you do not have access to this section!";
        }
}

//#######################//
//Administration Settings//
//#######################//

 if($item == "adminsettings") {
	global $authuser, $adminusers, $gamesettings, $userad, $sserver, $pchange, $runpass, $charad, $dcharacters, $udfile, $ufile, $savegame, $deletegame, $smessage, $ban, $backupvault, $area;
	global $adusers, $gameset, $useradmin, $startserver, $passchange, $changerunpass, $charadmin, $updownfile, $uploadfile, $delcharacters, $saveagame, $deleteagame, $sendmessage, $bankick, $backup, $invaliduser;
	if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE")) {
		if ($invaliduser != "") {
			echo "Invalid Users: <b>$invaliduser</b>";
			echo "<br>Please Remove them from the permissions list!!<br><br>";
		}
        	echo "<h2><b>Admin Settings:</b></h2>";
        	echo ("
		<b>Listed Below are the sections that can be granted access to.  Only Admin Users can get to Administration Settings, Program Path's and Character Administration for Everyone. List usernames in areas that you want to grant them access to, seperate users with a {COMMA}. If Admin Users is set to 'NONE' then everyone will have <b>ALL</b> privledges.</b>
		<br><br>
		Current User List: 
        	");
		$fp = file("./userpriv");
        	foreach ($fp as $key => $value) {
			if ($value == "NONE\n") { $value = ""; }
                	$a = $value;
                	$AdminOption[$key] = $a;
		}
		$fp = file("$shadow");
        	foreach ($fp as $key => $value) {
                	$a = substr($value, 0, strlen($value)-strlen (strstr ($value,':')));
			if ($fp[$key+1] != "") {
        	        	echo "<b>$a</b>, ";
			} else {
			echo "<b>$a</b";
		}
	}
	echo ("
	<br><br><br>
	<TABLE>
	<form name=adminsetting method=post action=index.php>
	<input type=hidden name=item value=changeadminsettings>
	<input type=hidden name=area value=$area>
	<TR><TD>Admin Users</TD><TD><input type=text name=adusers size=25 maxlength=255 value='$AdminOption[0]'></TD><TD>Access to everything!</TD></TR>
	<TR><TD>Game Settings</TD><TD><input type=text name=gameset size=25 maxlength=255 value='$AdminOption[1]'></TD><TD>Access to Game Settings</TD></TR>
	<TR><TD>User Administration</TD><TD><input type=text name=useradmin size=25 maxlength=255 value='$AdminOption[2]'></TD><TD>Access to User Administration</TD></TR>
	<TR><TD>Start/Stop Server - Load Module/Save</TD><TD><input type=text name=startserver size=25 maxlength=255 value='$AdminOption[3]'></TD><TD>Access to Start/Stop Server, as well as Loading a Module or Save</TD></TR>
	<TR><TD>Change Password</TD><TD><input type=text name=passchange size=25 maxlength=255 value='$AdminOption[4]'></TD><TD>Access to Change Password</TD></TR>
	<TR><TD>Change Running Passwords</TD><TD><input type=text name=changerunpass size=25 maxlength=255 value='$AdminOption[5]'></TD><TD>Access to change the Running Passwords</TD></TR>
	<TR><TD>Character Administration</TD><TD><input type=text name=charadmin size=25 maxlength=255 value='$AdminOption[6]'></TD><TD>Access to Upload/Download Characters in <b>your</b> directory</TD></TR>
	<TR><TD>Delete Characters</TD><TD><input type=text name=delcharacters size=25 maxlength=255 value='$AdminOption[7]'></TD><TD>Access to delete your own characters</TD></TR>
	<TR><TD>Download Module/HAK/Music/Movie</TD><TD><input type=text name=updownfile size=25 maxlength=255 value='$AdminOption[8]'></TD><TD>Access to Download Modules/HAK/Music/Movie files</TD></TR>
	<TR><TD>Upload Module/HAK/Music/Movie</TD><TD><input type=text name=uploadfile size=25 maxlength=255 value='$AdminOption[9]'></TD><TD>Access to Upload Modules/HAK/Music/Movie files</TD></TR>
	<TR><TD>Save a Game</TD><TD><input type=text name=saveagame size=25 maxlength=255 value='$AdminOption[10]'></TD><TD>Access to Save Games</TD></TR>
	<TR><TD>Delete Files</TD><TD><input type=text name=deleteagame size=25 maxlength=255 value='$AdminOption[11]'></TD><TD>Access to Delete NWN Files</TD></TR>
	<TR><TD>Send Message</TD><TD><input type=text name=sendmessage size=25 maxlength=255 value='$AdminOption[12]'></TD><TD>Access to Send a Message to Players</TD></TR>
	<TR><TD>Ban/Kick/UnBan</TD><TD><input type=text name=bankick size=25 maxlength=255 value='$AdminOption[13]'></TD><TD>Access to Ban/Kick/UnBan a user</TD></TR>
	<TR><TD>Backup Administration</TD><TD><input type=text name=backup size=25 maxlength=255 value='$AdminOption[14]'></TD><TD>Access to Backup Administration</TD></TR>
	<TR><TD><input type=submit></form></TD><TD></TD></TR>
	</TABLE>
	");
} else {
	echo "Nice Try, but you do not have access to this section!";
}
}

//###############################//
//Change Admininstration Settings//
//###############################//

if($item == "changeadminsettings") {
global $authuser, $adminusers, $adusers, $gameset, $useradmin, $startserver, $passchange, $changerunpass, $charadmin, $updownfile, $uploadfile, $delcharacters, $saveagame, $deleteagame, $sendmessage, $bankick, $backup, $area;
if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE")) {
	$adusers = eregi_replace(", *", ",", $adusers);
	$gameset = eregi_replace(", *", ",", $gameset);
	$useradmin = eregi_replace(", *", ",", $useradmin);
	$startserver = eregi_replace(", *", ",", $startserver);
	$passchange = eregi_replace(", *", ",", $passchange);
	$changerunpass = eregi_replace(", *", ",", $changerunpass);
	$charadmin = eregi_replace(", *", ",", $charadmin);
	$updownfile = eregi_replace(", *", ",", $updownfile);
	$uploadfile = eregi_replace(", *", ",", $uploadfile);
	$delcharacters = eregi_replace(", *", ",", $delcharacters);
	$saveagame = eregi_replace(", *", ",", $saveagame);
	$deleteagame = eregi_replace(", *", ",", $deleteagame);
	$sendmessage = eregi_replace(", *", ",", $sendmessage);
	$bankick = eregi_replace(", *", ",", $bankick);
	$backup = eregi_replace(", *", ",", $backup);
	if ($adusers == "") { $adusers = "NONE"; }
	if ($gameset == "") { $gameset = "NONE"; }
	if ($useradmin == "") { $useradmin = "NONE"; }
	if ($startserver == "") { $startserver = "NONE"; }
	if ($passchange == "") { $passchange = "NONE"; }
	if ($changerunpass == "") { $changerunpass = "NONE"; }
	if ($charadmin == "") { $charadmin = "NONE"; }
	if ($updownfile == "") { $updownfile = "NONE"; }
	if ($uploadfile == "") { $uploadfile = "NONE"; }
	if ($delcharacters == "") { $delcharacters = "NONE"; }
	if ($saveagame == "") { $saveagame = "NONE"; }
	if ($deleteagame == "") { $deleteagame = "NONE"; }
	if ($sendmessage == "") { $sendmessage = "NONE"; }
	if ($bankick == "") { $bankick = "NONE"; }
	if ($backup == "") { $backup = "NONE"; }
	$adminusers = split(",", $adusers);
	$gamesettings = split(",", $gameset);
	$userad = split(",", $useradmin);
	$sserver = split(",", $startserver);
	$pchange = split(",", $passchange);
	$runpass = split(",", $changerunpass);
	$charad = split(",", $charadmin);
	$udfile = split(",", $updownfile);
	$ufile = split(",", $uploadfile);
	$dcharacters = split(",", $delcharacters);
	$savegame = split(",", $saveagame);
	$deletegame = split(",", $deleteagame);
	$smessage = split(",", $sendmessage);
	$ban = split(",", $bankick);
	$backupvault = split(",", $backup);
	$fp = fopen("./userpriv", w);
	fwrite($fp, "$adusers\n");
	fwrite($fp, "$gameset\n");
	fwrite($fp, "$useradmin\n");
	fwrite($fp, "$startserver\n");
	fwrite($fp, "$passchange\n");
	fwrite($fp, "$changerunpass\n");
	fwrite($fp, "$charadmin\n");
	fwrite($fp, "$delcharacters\n");
	fwrite($fp, "$updownfile\n");
	fwrite($fp, "$uploadfile\n");
	fwrite($fp, "$saveagame\n");
	fwrite($fp, "$deleteagame\n");
	fwrite($fp, "$sendmessage\n");
	fwrite($fp, "$bankick\n");
	fwrite($fp, "$backup\n");
	fclose($fp);
	$adusers = "";
	$gameset = "";
	$useradmin = "";
	$startserver = "";
	$passchange = "";
	$changerunpass = "";
	$chardmin = "";
	$delcharacters = "";
	$updownfile = "";
	$uploadfile = "";
	$saveagame = "";
	$deleteagame = "";
	$sendmessage = "";
	$bankick = "";
	$backup = "";
	echo "User Privledges Changed and Saved.";
	if ($area == "userset") {
		echo "<META HTTP-EQUIV=Refresh content=1;URL=index.php?item=useradmin>";
	} else {
		echo "<META HTTP-EQUIV=Refresh content=1;URL=index.php>";
	}
} else {
	echo "Nice Try, but you do not have access to this section!";
}
}

//#################################//
//NWN Server Administration Options//
//#################################//
if($item == "nwnsaoptions") {
global $authuser, $adminusers, $adminemail, $displaycommands, $aemail, $dcommands;
if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE")) {
	$fp = file("./nwnsaoptions");
	foreach ($fp as $key => $value) {
		$Option[$key] = $value;
	}
	$adminemail = eregi_replace("[^0-9a-z.-_@/]", "", $Option[0]);
        $displaycommands = eregi_replace("[^0-9a-z]", "", $Option[1]);
	echo "<h2>NWN Server Administration Options</h2>";
	echo "<form name=options method=post action=index.php>";
        echo "<input type=hidden name=item value=optionsave>";
        echo "<TABLE width=75%><TR>";
	echo "<TD>Administrators Email Address</TD><TD><input type=text name=aemail size=25 maxsize=50 value=\"$adminemail\"></TD></TR>"; 
        echo "<TD>Display Non-Authorized Commands</TD><TD><b>SHOW</b><input type=RADIO name=dcommands value=Show";
        if($displaycommands == "Show") { echo " CHECKED"; }
	echo "></TD><TD><b>HIDE</b><input type=RADIO name=dcommands value=Hide";
        if($displaycommands == "Hide") { echo " CHECKED"; }
        echo "></TD></TR></TABLE>";
	echo "<br><br><br><center><input type=submit value=\"Save Settings\" name=optionsave></center></form>";
} else {
        echo "Nice Try, but you do not have access to this section!";
}
}

//############//
//Save Options//
//############//
if($item == "optionsave") {
global $authuser, $adminusers, $adminemail, $displaycommands, $aemail, $dcommands;
if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE")) {
	$adminemail = $aemail;
	$displaycommands = $dcommands;
	if (!preg_match("/^[0-9a-z]*\@[0-9a-z]*\.[0-9a-z]*.*$/", $adminemail)) {
		$adminemail = "Invalid address";
		echo "<b>Invalid Email Address</b>";
		echo "<META HTTP-EQUIV=Refresh content=1;URL=index.php?item=nwnsaoptions>";
	} else {
		$fp = fopen("./nwnsaoptions", w);
        	fwrite($fp, "$adminemail\n");
        	fwrite($fp, "$displaycommands\n");
        	fclose($fp);
		echo "<b>NWN Server Administration Options Saved</b>";
		echo "<META HTTP-EQUIV=Refresh content=1;URL=index.php>";
	}
} else {
        echo "Nice Try, but you do not have access to this section!";
}
}

//#####################//
//Backup Administration//
//#####################//

if($item == "backup") {
	global $authuser, $adminusers, $backupvault;
	if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $backupvault))) {
		if (file_exists("./backupconf")) {
			echo "<h2>Backup Administration - Displaying <b>Current</b> Settings</h2>";
			$fp = file("./backupconf");
			foreach ($fp as $key => $value) {
				$Option[$key] = $value;
			}
			$backuponoff = eregi_replace("[^0-9a-z/]", "", $Option[0]);
			$arctype = eregi_replace("[^0-9a-z/]", "", $Option[1]);
			$svc = eregi_replace("[^0-9a-z/]", "", $Option[2]);
			$gsaves = eregi_replace("[^0-9a-z/]", "", $Option[3]);
			$nwcamp = eregi_replace("[^0-9a-z/]", "", $Option[4]);
			$mods = eregi_replace("[^0-9a-z/]", "", $Option[5]);
			$haks = eregi_replace("[^0-9a-z/]", "", $Option[6]);
			$mus = eregi_replace("[^0-9a-z/]", "", $Option[7]);
                        $mov = eregi_replace("[^0-9a-z/]", "", $Option[8]);
                        $zm = eregi_replace("[^0-9a-z/]", "", $Option[9]);
                        $backupfilename = eregi_replace("[^0-9a-z/]", "", $Option[10]);
                        $generations = eregi_replace("[^0-9a-z/]", "", $Option[11]);
			$hour = eregi_replace("[^0-9a-z*/]", "", $Option[12]);
                        $minute = eregi_replace("[^0-9a-z*/]", "", $Option[13]);
                        $dom = eregi_replace("[^0-9a-z*/]", "", $Option[14]);
                        $month = eregi_replace("[^0-9a-z*/]", "", $Option[15]);
                        $day = eregi_replace("[^0-9a-z*/]", "", $Option[16]);
		} else {
			echo "<h2>Backup Administration - Displaying <b>Default</b> Settings</h2>";
			$backuponoff = "off";
			$arctype = "tararc";
			$svc = "SVC";
			$gsaves = "SAVES";
			$nwcamp = "NONE";
			$mods = "MODS";
			$haks = "NONE";
			$mus = "NONE";
			$mov = "NONE";
			$zm = "NONE";
			$backupfilename = "NWNServerBackup";
			$generations = "3";
			$hour = "1";
			$minute = "0";
			$dom = "*";
			$month = "*";
			$day = "0";
		}
		echo "<form name=backadmin method=post action=index.php>";
        	echo "<input type=hidden name=item value=backupgo>";
		echo "<TABLE width=75%><TR>";
		echo "<TD>Backup:</TD><TD><b>ON</b><input type=RADIO name=backuponoff value=on";
		if($backuponoff == "on") { echo " CHECKED"; }
		echo "></TD><TD><b>OFF</b><input type=RADIO name=backuponoff value=off";
		if($backuponoff == "off") { echo " CHECKED"; }
		echo "></TD></TR>";
		echo "<TR><TD>Archive Type:</TD><TD><b>ZIP</b><input type=RADIO name=arctype value=ziparc";
		if($arctype == "ziparc") { echo " CHECKED"; }
		echo "></TD><TD><b>TAR</b><input type=RADIO name=arctype value=tararc";
		if($arctype == "tararc") { echo " CHECKED"; }
		echo "></TD>";
		echo "<TD><b>TAR/ZIP</b><input type=RADIO name=arctype value=tarziparc";
		if($arctype == "tarziparc") { echo " CHECKED"; }
		echo "></TD><TD><b>TAR & ZIP</b><input type=RADIO name=arctype value=tarandzip";
		if($arctype == "tarandzip") { echo " CHECKED"; }
		echo "></TD>";
		echo "<TD><b>TAR/ZIP & ZIP</b><input type=RADIO name=arctype value=tarzipandzip";
		if($arctype == "tarzipandzip") { echo " CHECKED"; }
		echo "></TD>";
		echo "</TR></TABLE><hr>";
		echo "<h2>What do you wish to Backup?</h2>";
		echo "<TABLE width=30% border=1 cellpadding=2 cellspacing=0><TR>";
		echo "<TD>Server Vault Characters:</TD><TD><input type=checkbox name=svc value=SVC";
		if($svc == "SVC") { echo " CHECKED"; }
		echo "></TD></TR>";
		echo "<TD>Save Games:</TD><TD><input type=checkbox name=gsaves value=SAVES";
		if($gsaves == "SAVES") { echo " CHECKED"; }
		echo "></TD></TR>";
		echo "<TD>NeverWinter Campaigns:</TD><TD><input type=checkbox name=nwcamp value=NWM";
		if($nwcamp == "NWM") { echo " CHECKED"; }
		echo "></TD></TR>";
		echo "<TR><TD>Modules:</TD><TD><input type=checkbox name=mods value=MODS";
		if($mods == "MODS") { echo " CHECKED"; }
		echo "></TD></TR>";
		echo "<TR><TD>HAK:</TD><TD><input type=checkbox name=haks value=HAKS";
		if($haks == "HAKS") { echo " CHECKED"; }
		echo "></TD></TR>";
		echo "<TR><TD>Music:</TD><TD><input type=checkbox name=mus value=MUS";
		if($mus == "MUS") { echo " CHECKED"; }
		echo "></TD></TR>";
		echo "<TR><TD>Movies:</TD><TD><input type=checkbox name=mov value=MOV";
		if($mov == "MOV") { echo " CHECKED"; }
		echo "></TD></TR>";
		echo "<TR><TD>Zipped Modules:</TD><TD><input type=checkbox name=zm value=ZM";
		if($zm == "ZM") { echo " CHECKED"; }
		echo "></TD></TR>";
		echo "</TABLE>";
		echo "<hr>";
		echo "<h2>FileName and Generations</h2>";
		echo "Do not add <b>.zip</b> or <b>.tar</b> to Filename<br><br>";
		echo "<b>Filename (No Spaces):</b><input type=text name=backupfilename value=\"$backupfilename\" size=25 maxsize=35>";
		echo "<b>Generations:</b>";
		echo "<Select Name=generations>";
		for ($i = 1; $i < 11; $i++) {
			echo "<OPTION Value=$i";
			if($generations == $i) { echo " SELECTED"; }
			echo ">$i";
		}
		echo "</Select>";
		echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href=\"index.php?item=backuparchive\">List/Download/Delete Backup Archive Files</a>";
		echo "<br><hr>";
		echo "<h2>When do you wish to Backup?</h2>";
		echo "<b>HOUR:</b>";
		echo "<Select Name=hour>";
		echo "<OPTION Value=*";
		if($hour == "*") { echo " SELECTED"; }
		echo ">Any";
		for ($i = 0; $i < 24; $i++) {
			echo "<OPTION Value=$i";
			if(($hour == $i) & ($hour != "*")) { echo " SELECTED"; }
			echo ">$i";
		}
		echo "</Select>";
		echo "<b>MINUTE:</b>";
		echo "<Select Name=minute>";
		echo "<OPTION Value=*";
		if($minute == "*") { echo " SELECTED"; }
		echo ">Any";
		for ($i = 0; $i < 60; $i++) {
			echo "<OPTION Value=$i";
			if(($minute == $i) & ($minute != "*")) { echo " SELECTED"; }
			echo ">$i";
		}
		echo "</Select>";
		echo "<b>Day Of Month:</b>";
		echo "<Select Name=dom>";
		echo "<OPTION Value=*>Any";
		for ($i = 1; $i < 32; $i++) {
			echo "<OPTION Value=$i";
			if($dom == $i) { echo " SELECTED"; }
			echo ">$i";
		}
		echo "</Select>";
		echo "<b>Month:</b>";
		echo "<Select Name=month>";
		echo "<OPTION Value=*";
		if($month == "*") { echo " SELECTED"; }
		echo ">Any";
		echo "<OPTION Value=1";
		if($month == "1") { echo " SELECTED"; }
		echo ">January<OPTION Value=2";
		if($month == "2") { echo " SELECTED"; }
		echo ">Febuarary<OPTION Value=3";
		if($month == "3") { echo " SELECTED"; }
		echo ">March<OPTION Value=4";
		if($month == "4") { echo " SELECTED"; }
		echo ">April<OPTION Value=5";
		if($month == "5") { echo " SELECTED"; }
		echo ">May<OPTION Value=6";
		if($month == "6") { echo " SELECTED"; }
		echo ">June<Option Value=7";
		if($month == "7") { echo " SELECTED"; }
		echo ">July<OPTION Value=8";
		if($month == "8") { echo " SELECTED"; }
		echo ">August<OPTION Value=9";
		if($month == "9") { echo " SELECTED"; }
		echo ">September<OPTION Value=10";
		if($month == "10") { echo " SELECTED"; }
		echo ">October<OPTION Value=11";
		if($month == "11") { echo " SELECTED"; }
		echo ">November<OPTION Value=12";
		if($month == "12") { echo " SELECTED"; }
		echo ">December";
		echo "</Select>";
		echo "<b>DAY:</b>";
		echo "<Select Name=day>";
		echo "<OPTION Value=*";
		if($day == "*") { echo " SELECTED"; }
		echo ">Any";
		echo "<OPTION Value=0";
		if($day == "0") { echo " SELECTED"; }
		echo ">Sunday<OPTION Value=1";
		if($day == "1") { echo " SELECTED"; }
		echo ">Monday<OPTION Value=2";
		if($day == "2") { echo " SELECTED"; }
		echo ">Tuesday<OPTION Value=3";
		if($day == "3") { echo " SELECTED"; }
		echo ">Wednesday<OPTION Value=4";
		if($day == "4") { echo " SELECTED"; }
		echo ">Thursday<OPTION Value=5";
		if($day == "5") { echo " SELECTED"; }
		echo ">Friday<OPTION Value=6";
		if($day == "6") { echo " SELECTED"; }
		echo ">Saturday";
		echo "</Select>";
		echo "<br><br><br><center><input type=submit value=\"Schedule Backup\" name=schedback></center></form>";
	} else {
		echo "Nice Try, but you do not have access to this section!";
	}
}

//###############//
//Schedule Backup//
//###############//

if($item == "backupgo") {
	global $authuser, $adminusers, $backupvault;
        if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $backupvault))) {
		if($schedback == "Schedule Backup") {
			$backupfilename = eregi_replace(".tar|.zip", "", $backupfilename);
			$backupfilename = eregi_replace(" ", "", $backupfilename);
			if($backupfilename == "") { $backupfilename = "NWNServerBackup"; }
			if($svc != "SVC") { $svc = "NONE"; }
			if($gsaves != "SAVES") { $gsaves = "NONE"; }
			if($nwcamp != "NWM") { $nwcamp = "NONE"; }
			if($mods != "MODS") { $mods = "NONE"; }
			if($haks != "HAKS") { $haks = "NONE"; }
			if($mus != "MUS") { $mus = "NONE"; }
			if($mov != "MOV") { $mov = "NONE"; }
			if($zm != "ZM") { $zm = "NONE"; }
			$fp = fopen("./backupconf", w);	
			fwrite($fp, "$backuponoff\n");
			fwrite($fp, "$arctype\n");
			fwrite($fp, "$svc\n");
			fwrite($fp, "$gsaves\n");
			fwrite($fp, "$nwcamp\n");
			fwrite($fp, "$mods\n");
			fwrite($fp, "$haks\n");
			fwrite($fp, "$mus\n");
			fwrite($fp, "$mov\n");
			fwrite($fp, "$zm\n");
			fwrite($fp, "$backupfilename\n");
			fwrite($fp, "$generations\n");
			fwrite($fp, "$hour\n");
			fwrite($fp, "$minute\n");
			fwrite($fp, "$dom\n");
			fwrite($fp, "$month\n");
			fwrite($fp, "$day\n");
			fclose($fp);
			$fp = fopen("./includelist", w);
			if($svc == "SVC") { fwrite($fp, "servervault/*\n"); }
			if($gsaves == "SAVES") { fwrite($fp, "saves/*\n"); }
			if($nwcamp == "NWM") { fwrite($fp, "nwm/*\n"); }
			if($mods == "MODS") { fwrite($fp, "modules/*\n"); }
			if($haks == "HAKS") { fwrite($fp, "hak/*\n"); }
			if($mus == "MUS") { fwrite($fp, "music/*\n"); }
			if($mov == "MOV") { fwrite($fp, "movies/*\n"); }
			if($zm == "ZM") { fwrite($fp, "zippedmodules/*\n"); }
			fclose($fp);
			$command = "$lsdir -1 $nwserverdir";
			unset ($Result);
			exec($command, $Result);
			$fp = fopen("./excludelist", w);
			foreach ($Result as $key => $value) {
				if(($value != "servervault") & ($value != "modules") & ($value != "hak") & ($value != "music") & ($value != "movies") & ($value != "zippedmodules") & ($value != "saves") & ($value != "nwm")) { 
					fwrite($fp, "$value\n"); 
				} else {
					if(($value == "servervault") & ($svc == "NONE")) { fwrite($fp, "$value\n"); }
					if(($value == "saves") & ($gsaves == "NONE")) { fwrite($fp, "$value\n"); }
					if(($value == "nwm") & ($nwcamp == "NONE")) { fwrite($fp, "$value\n"); }
					if(($value == "modules") & ($mods == "NONE")) { fwrite($fp, "$value\n"); }
					if(($value == "hak") & ($haks == "NONE")) { fwrite($fp, "$value\n"); }
					if(($value == "music") & ($mus == "NONE")) { fwrite($fp, "$value\n"); }
					if(($value == "movies") & ($mov == "NONE")) { fwrite($fp, "$value\n"); }
					if(($value == "zippedmodules") & ($zm == "NONE")) { fwrite($fp, "$value\n"); }
				}
			}
			fclose($fp);
			$fp = fopen("./backupscript", w);
			fwrite($fp, "#!/bin/sh\n");
			fwrite($fp, "# NWNPHP Admin Backup and Rotate Files Script\n");
			fwrite($fp, "# Written by Wayne Catterton\n");
			fwrite($fp, "#\n");
			$localdir = `$pwddir`;
			$localdir = eregi_replace("[^0-9a-z*/]", "", $localdir);
			if($arctype == "ziparc") {
				fwrite($fp, "# ZIP the specified files\n");
				fwrite($fp, "cd $nwserverdir\n");
				fwrite($fp, "$zipdir -rq $localdir/NWNBackup/$backupfilename.zip ./ -i@$localdir/includelist\n");
				fwrite($fp, "\n");
			}
			if($arctype == "tararc") {
				fwrite($fp, "# TAR the specified files\n");
				fwrite($fp, "cd $nwserverdir\n");
				fwrite($fp, "$tardir -cvf $localdir/NWNBackup/$backupfilename.tar * -X $localdir/excludelist\n");
				fwrite($fp, "\n");
			}
			if($arctype == "tarziparc") {
				fwrite($fp, "# TAR the specified files\n");
                                fwrite($fp, "cd $nwserverdir\n");
                                fwrite($fp, "$tardir -cvf $localdir/NWNBackup/$backupfilename.tar * -X $localdir/excludelist\n");
                                fwrite($fp, "# ZIP the TAR file\n");
				fwrite($fp, "$zipdir -mq $localdir/NWNBackup/$backupfilename.tar.zip $localdir/NWNBackup/$backupfilename.tar\n");
				fwrite($fp, "\n");
			}
			if($arctype == "tarandzip") {
				fwrite($fp, "# TAR the specified files\n");
                                fwrite($fp, "cd $nwserverdir\n");
                                fwrite($fp, "$tardir -cvf $localdir/NWNBackup/$backupfilename.tar * -X $localdir/excludelist\n");
                                fwrite($fp, "# ZIP the specified files\n");
                                fwrite($fp, "$zipdir -rq $localdir/NWNBackup/$backupfilename.zip ./ -i@$localdir/includelist\n");
                                fwrite($fp, "\n");
			}
			if($arctype == "tarzipandzip") {
				fwrite($fp, "# TAR the specified files\n");
                                fwrite($fp, "cd $nwserverdir\n");
                                fwrite($fp, "$tardir -cvf $localdir/NWNBackup/$backupfilename.tar * -X $localdir/excludelist\n");
                                fwrite($fp, "# ZIP the TAR file\n");
                                fwrite($fp, "$zipdir -mq $localdir/NWNBackup/$backupfilename.tar.zip $localdir/NWNBackup/$backupfilename.tar\n");
				fwrite($fp, "# ZIP the specified files\n");
                                fwrite($fp, "$zipdir -rq $localdir/NWNBackup/$backupfilename.zip ./ -i@$localdir/includelist\n");
                                fwrite($fp, "\n");
			}
			fwrite($fp, "# Rotate the Backup Files\n\n");
			fwrite($fp, "BKDIR=$localdir/NWNBackup\n");
			fwrite($fp, "if test -d \$BKDIR\n");
			fwrite($fp, "then\n");
			fwrite($fp, "	cd \$BKDIR\n");
			if(($arctype == "ziparc") | ($arctype == "tarziparc") | ($arctype == "tarzipandzip")) { fwrite($fp, "	for file in *.zip\n"); }
			if($arctype == "tararc") { fwrite($fp, "	for file in *.tar\n"); }
			if(($arctype == "ziparc") | ($arctype == "tarziparc") | ($arctype == "tararc") | ($arctype == "tarzipandzip")) {
				fwrite($fp, "	do\n");
				fwrite($fp, "		BKFILE=\$file\n");
				for($i = ($generations - 1); $i > 0; $i--) {
					$ii = $i - 1;
					fwrite($fp, "		test -f \$BKFILE.$ii && mv \$BKFILE.$ii \$BKFILE.$i\n");
				}
				fwrite($fp, "		mv \$BKFILE \$BKFILE.0\n");
				fwrite($fp, "	done\n");
				fwrite($fp, "fi\n");
			} else {
				fwrite($fp, "	for file in *.zip\n");
				fwrite($fp, "	do\n");
                                fwrite($fp, "		BKFILE=\$file\n");
                                for($i = ($generations - 1); $i > 0; $i--) {
                                        $ii = $i - 1;
                                        fwrite($fp, "		test -f \$BKFILE.$ii && mv \$BKFILE.$ii \$BKFILE.$i\n");
                                }
                                fwrite($fp, "		mv \$BKFILE \$BKFILE.0\n");
                                fwrite($fp, "	done\n");
				fwrite($fp, "	for file in *.tar\n");
				fwrite($fp, "	do\n");
                                fwrite($fp, "		BKFILE=\$file\n");
                                for($i = ($generations - 1); $i > 0; $i--) {
                                        $ii = $i - 1;
                                        fwrite($fp, "		test -f \$BKFILE.$ii && mv \$BKFILE.$ii \$BKFILE.$i\n");
                                }
                                fwrite($fp, "		mv \$BKFILE \$BKFILE.0\n");
                                fwrite($fp, "	done\n");
                                fwrite($fp, "fi\n");
			}
			fclose($fp);
			chmod ("./backupscript", 0755);  
			$commandline = "$minute $hour $dom $month $day $localdir/backupscript";
			$command = "$crondir -l";
			unset ($Result);
			exec($command, $Result);
			reset ($Result);
			if(($Result != "") | ($Result[0] != "")) {
				$i = 0;
				$fp = fopen("./crontemp", w);
				while ($i < count($Result)) {
					if($i == 0) {
						if((preg_match("/^#.*/", $Result[$i])) & (!preg_match("/BEGIN NWN/i", $Result[$i]))) {
							while ((preg_match("/^#.*/", $Result[$i])) & (!preg_match("/BEGIN NWN/i", $Result[$i]))) {
								$i++;
							}
						}
					}
					if(preg_match("/BEGIN NWN/i", $Result[$i])) {
						while (!preg_match("/END NWN/i", $Result[$i])) {
							$i++;
						}
						$i++;
					} else {
						if($i < count($Result)) { fwrite($fp, "$Result[$i]\n"); }
						$i++;
					}
				}
			} 
			if($backuponoff == "on") { 
				fwrite($fp, "# BEGIN NWN Admin (Do Not Remove)\n");
				fwrite($fp, "$commandline\n"); 
				fwrite($fp, "# END NWN Admin (Do Not Remove)\n");
			}
			fclose($fp);
			if($crondir != "NONE") {
				$command = "$crondir $localdir/crontemp";
				exec($command);
			} else {
				$command = "$rmdir -fr $localdir/crontemp";
				exec($command);
				echo "You do not have a path to the <b>crontab</b> program set up<br>";
				echo "So the backup job could not be scheduled.  You can, however,<br>";
				echo "Schedule it manually, the path to the script that was written<br>";
				echo "is: <b>$localdir/backupscript</b>.";
				exit();
			}
			$command = "$rmdir -fr $localdir/crontemp";
			exec($command);
			for($i = $generations; $i < 10; $i++) {
                                $command = "$rmdir -fr ./NWNBackup/*.$i";
                                exec($command);
                        }
		}
		echo "Backup Schedule Saved and Scheduled";
		echo "<META HTTP-EQUIV=Refresh content=2;URL=index.php>";
	} else {
		echo "Nice Try, but you do not have access to this section!";
        }       
}

//##############//
//Backup Archive//
//##############//

if($item == "backuparchive") {
	global $authuser, $adminusers, $backupvault;
        if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $backupvault))) {
		echo "<h2>Backup File Archive</h2>";
		echo "Click on name of file to Download<br><br>";
		echo "<b>Warning</b> Clicking DELETE will instantly delete the file without confirmation!<br><br>";
		echo "<TABLE width=100% border=1 cellpadding=2 cellspacing=0><TR>";
                echo "<CENTER><TD><b>Filename</b></TD><TD><b>Size of File</b></TD><TD><b>Date of File</b></TD><TD><b>Delete File</b></TD></CENTER></TR>";
		unset($Result);
		$command = "$lsdir -A --full-time -h NWNBackup";
		exec($command, $Result);
		reset($Result);
		for($i = 1; $i < count($Result); $i++) {
			unset($temp);
			$temp = preg_split("/ \s*/", $Result[$i], 50);
			reset($temp);
			echo "<TD><a href=\"./NWNBackup/$temp[10]\">$temp[10]</a></TD><TD>$temp[4]</TD><TD>$temp[8] $temp[5] $temp[6] $temp[7], $temp[9]</TD><TD>";
			if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $deletegame))) {
				echo "<a href=\"index.php?item=delbackfile&backfile=$temp[10]\"><b>DELETE</b></a></TD></TR>";
			} else {
				echo "NO DELETE</TD></TR>";
			}
		}
	} else {
                echo "Nice Try, but you do not have access to this section!";
        }
}

//##################//
//Delete Backup File//
//##################//

if($item == "delbackfile") {
	global $authuser, $adminusers, $backupvault;
	if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $deletegame))) {
		$command = "$rmdir -fr ./NWNBackup/$backfile";
		exec($command);
		echo "<b>$backfile</b> Deleted!";
		echo "<META HTTP-EQUIV=Refresh content=2;URL=index.php?item=backuparchive>";
	} else {
                echo "Nice Try, but you do not have access to this section!";
        }
}
//#############//
//Game Settings//
//#############//

if($item == "settings") {
global $authuser, $adminusers, $gamesettings;
if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $gamesettings))) {
	$fp = file("./nwn.config");
	foreach ($fp as $key => $value) {
                	$a = $value;
                	$Option[$key] = $a;
        	}
		if ($Option[12] == "\"\"\n") { $Option[12] = ""; }
		if ($Option[13] == "\"\"\n") { $Option[13] = ""; }
		if ($Option[14] == "\"\"\n") { $Option[14] = ""; }
        	echo "<h2><b>Settings:</b></h2>";
        	echo ("
        	<TABLE>
        	<form name=setting method=post action=index.php>
        	<input type=hidden name=item value=changesettings>
        	<TR><TD><b>Max Players</b></TD><TD><input type=text name=maxplayers size=2 maxlength=2 value=$Option[0]></TD></TR>
        	<TR><TD><b>Min Level</b></TD><TD><input type=text name=minlevel size=2 maxlength=2 value=$Option[1]></TD></TR>
        	<TR><TD><b>Max Level</b></TD><TD><input type=text name=maxlevel size=2 maxlength=2 value=$Option[2]></TD></TR>
        	<TR><TD><b>Pause And Play (0-1)</b></TD><TD><input type=text name=pauseandplay size=1 maxlength=1 value=$Option[3]></TD></TR>
		<TD><li>0 = Game can only be paused by DM<br><li>1 = Game can be paused by players</TD>
        	<TR><TD><b>PVP (0-2)</b></TD><TD><input type=text name=pvp size=1 maxlength=1 value=$Option[4]></TD></TR>
		<TD><li>0 = None<br><li>1 = Party<br><li>2 = Full</TD>
        	<TR><TD><b>Server Vault (0-1)</b></TD><TD><input type=text name=servervault size=1 maxlength=1 value=$Option[5]></TD></TR>
		<TD><li>0 = Local Characters Only<br><li>1 = Server Characters Only</TD>
        	<TR><TD><b>Enforce Legal Characters (0-1)</b></TD><TD><input type=text name=elc size=1 maxlength=1 value=$Option[6]></TD></TR>
		<TD><li>0 = Don't enforce Legal Characters<br><li>1 = Enforce Legal Characters</TD>
        	<TR><TD><b>Item Level Restrictions (0-1)</b></TD><TD><input type=text name=ilr size=1 maxlength=1 value=$Option[7]></TD></TR>
		<TD><li>0 = Don't enforce Item Level Restrictions<br><li>1 = Enforce Item Level Restrictions</TD>
        	<TR><TD><b>Game Type (0-12)</b></TD><TD><input type=text name=gametype size=2 maxlength=2 value=$Option[8]></TD></TR>
		<TD><li>0 = Action <li>1 = Story <li>2 = Story Lite <li>3 = Role Play <li>4 = Team <li>5 = Melee</TD>
		<TD><li>6 = Arena <li>7 = Social <li>8 = Alternative <li>9 = PW Action <li>10 = PW Story <li>11 = Solo <li>12 = Tech Support</TD>
        	<TR><TD>One Party (0-1)</TD><TD><input type=text name=oneparty size=1 maxlength=1 value=$Option[9]></TD></TR>
		<TD><li>0 = Allow multiple parties<br><li>1 = Only allow one party</TD>
        	<TR><TD>Difficulty (1-4)</TD><TD><input type=text name=difficulty size=1 maxlength=1 value=$Option[10]></TD></TR>
		<TD><li>1 = Easy <li>2 = Normal</TD>
		<TD><li>3 = D & D Hardcore <li>4 = Very Difficult</TD>
        	<TR><TD>Auto Save Interval</TD><TD><input type=text name=autosaveinterval size=2 maxlength=2 value=$Option[11]></TD><TD>0 = Disable</TD></TR>
        	<TR><TD>Player Password</TD><TD><input type=password name=playerpassword size=25 maxlength=25 value=\"$Option[12]\"></TD><TD>Blank to Disable</TD></TR>
        	<TR><TD>DM Password</TD><TD><input type=password name=dmpassword size=25 maxlength=25 value=\"$Option[13]\"></TD><TD>Blank to Disable</TD></TR>
        	<TR><TD>Admin Password</TD><TD><input type=password name=adminpassword size=25 maxlength=25 value=\"$Option[14]\"></TD><TD>Blank to Disable</TD></TR>
        	<TR><TD>Server Name</TD><TD><input type=text name=servername size=25 maxlength=255 value=\"$Option[15]\"></TD></TR>
        	<TR><TD>Public Server (0-1)</TD><TD><input type=text name=publicserver size=1 maxlength=1 value=$Option[16]></TD></TR>
		<TD><li>0 = Do not list server on internet<br><li>1 = List server on internet</TD>
        	<TR><TD>Reload When Empty (0-1)</TD><TD><input type=text name=reloadwhenempty size=1 maxlength=1 value=$Option[17]></TD></TR>
		<TD><li>0 = Module state is persistant as long as server is running<br><li>1 = Module state is reset when the server becomes empty</TD>
        	<TR><TD>Port</TD><TD><input type=text name=port size=5 maxlength=5 value=$Option[18]></TD><TD>Default = 5121</TD></TR>
        	<TR><TD><input type=submit></form></TD><TD></TD></TR>
        	</TABLE>
        	");
	} else {
		echo "Nice Try, but you do not have access to this section!";
	}
 }

//####################//
//Change Game Settings//
//####################//

 if($item == "changesettings") {
	global $authuser, $adminusers, $gamesettings;
        if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $gamesettings))) {
        	@unlink("./nwn.config");
        	$fp = fopen("./nwn.config", a);
        	$maxplayers = eregi_replace("[^0-9]", "", $maxplayers);
        	$minlevel = eregi_replace("[^0-9]", "", $minlevel);
        	$maxlevel = eregi_replace("[^0-9]", "", $maxlevel);
        	$pauseandplay = eregi_replace("[^0-9]", "", $pauseandplay);
        	$pvp = eregi_replace("[^0-9]", "", $pvp);
        	$servervault = eregi_replace("[^0-9]", "", $servervault);
        	$elc = eregi_replace("[^0-9]", "", $elc);
        	$ilr = eregi_replace("[^0-9]", "", $ilr);
        	$gametype = eregi_replace("[^0-9]", "", $gametype);
        	$oneparty = eregi_replace("[^0-9]", "", $oneparty);
        	$difficulty = eregi_replace("[^0-9]", "", $difficulty);
        	$autosaveinterval = eregi_replace("[^0-9]", "", $autosaveinterval);
        	$playerpassword = eregi_replace("[^0-9a-z ]", "", $playerpassword);
        	$dmpassword = eregi_replace("[^0-9a-z ]", "", $dmpassword);
        	$adminpassword = eregi_replace("[^0-9a-z ]", "", $adminpassword);
        	$servername = eregi_replace("[^0-9a-z ]", "", $servername);
        	$publicserver = eregi_replace("[^0-9]", "", $publicserver);
        	$reloadwhenempty = eregi_replace("[^0-9]", "", $reloadwhenempty);
        	$port = eregi_replace("[^0-9]", "", $port);
		if ($playerpassword == "") { $playerpassword = "\"\""; }
		if ($dmpassword == "") { $dmpassword = "\"\""; }
		if ($adminpassword == "") { $adminpassword = "\"\""; }
        	fwrite($fp, "$maxplayers\n");
        	fwrite($fp, "$minlevel\n");
        	fwrite($fp, "$maxlevel\n");
        	fwrite($fp, "$pauseandplay\n");
        	fwrite($fp, "$pvp\n");
        	fwrite($fp, "$servervault\n");
        	fwrite($fp, "$elc\n");
        	fwrite($fp, "$ilr\n");
        	fwrite($fp, "$gametype\n");
        	fwrite($fp, "$oneparty\n");
        	fwrite($fp, "$difficulty\n");
        	fwrite($fp, "$autosaveinterval\n");
        	fwrite($fp, "$playerpassword\n");
        	fwrite($fp, "$dmpassword\n");
        	fwrite($fp, "$adminpassword\n");
        	fwrite($fp, "$servername\n");
        	fwrite($fp, "$publicserver\n");
        	fwrite($fp, "$reloadwhenempty\n");
        	fwrite($fp, "$port\n");
		if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $runpass))) {
			$cmdopts = "maxclients,$maxplayers:minlevel,$minlevel:maxlevel,$maxlevel:pauseandplay,$pauseandplay:elc,$elc:ilr,$ilr:oneparty,$oneparty:difficulty,$difficulty:autosaveinterval,$autosaveinterval:playerpassword,$playerpassword:dmpassword,$dmpassword:adminpassword,$adminpassword:servername,\"$servername\"";
		} else {
			$cmdopts = "maxclients,$maxplayers:minlevel,$minlevel:maxlevel,$maxlevel:pauseandplay,$pauseandplay:elc,$elc:ilr,$ilr:oneparty,$oneparty:difficulty,$difficulty:autosaveinterval,$autosaveinterval:servername,\"$servername\"";
		}
	 	if ($procnum >=3) {
			$allopts = split (":", $cmdopts);
			while (list($key,$val) = each($allopts)) {
				$temp = split (",", $allopts[$key]);
				$runcmd = "./changeset $temp[0] $temp[1]";
				exec ($runcmd);
			}
		}	
		echo "Settings have been saved and applied to server.<br>";
		echo "<META HTTP-EQUIV=Refresh content=2;URL=index.php>";
	} else {
		echo "Nice Try, but you do not have access to this section!";
	}
 }

//###################//
//User Administration//
//###################//

 if($item == "useradmin") {
	global $userad, $authuser, $adminusers, $area;
	if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $userad))) {
		$temp = "";
		echo "<TABLE>";
   	     	echo "To Change a user's Password or Delete a user, click on the user's name.<br><br>";
        	echo "<h2>Current Users</h2>";
        	echo "<th align=left>USERNAME</th><th align=left>PERMISSIONS</th><br>";
        	$fp = file("$shadow");
        	foreach ($fp as $key => $value) {
                	$a = substr($value, 0, strlen($value)-strlen (strstr ($value,':')));
			echo "<tr>";
                	echo "<td><a href=\"index.php?item=deluserpass&username=$a\">$a</a></td>";
			if (preg_grep("/^$a$/i", $adminusers)) {
				if (preg_grep("/^$authuser$/i", $adminusers) || ($adminusers[0] == "NONE")) {
					echo "<td><a href=\"index.php?item=adminsettings\">Administrator</a></td>";
				} else {
					echo "<td>Administrator</td>";
				}
			}
			if ((preg_grep ("/^$a$/i", $gamesettings)) & (!preg_grep ("/^$a$/i", $adminusers))) {
				if ($temp !="") {
					$temp = $temp.", Game Settings";
				} else {
					$temp = "GameSettings";
				}
			}
			if ((preg_grep ("/^$a$/i", $userad)) & (!preg_grep ("/^$a$/i", $adminusers))) {
                                if ($temp !="") {
                                        $temp = $temp.", User Administration";
                                } else {
                                        $temp = "User Administration";
                                }
                        }
			if ((preg_grep ("/^$a$/i", $sserver)) & (!preg_grep ("/^$a$/i", $adminusers))) {
                                if ($temp !="") {
                                        $temp = $temp.", Start Stop Server, Load Module/Save";
                                } else {
                                        $temp = "Start Stop Server, Load Module/Save";
                                }
                        }
			if ((preg_grep ("/^$a$/i", $pchange)) & (!preg_grep ("/^$a$/i", $adminusers))) {
                                if ($temp !="") {
                                        $temp = $temp.", Change Password";
                                } else {
                                        $temp = "Change Password";
                                }
                        }
			if ((preg_grep ("/^$a$/i", $runpass)) & (!preg_grep ("/^$a$/i", $adminusers))) {
                                if ($temp !="") {
                                        $temp = $temp.", Change Running Passwords";
                                } else {
                                        $temp = "Change Running Passwords";
                                }
                        }
			if ((preg_grep ("/^$a$/i", $charad)) & (!preg_grep ("/^$a$/i", $adminusers))) {
                                if ($temp !="") {
                                        $temp = $temp.", Character Administration";
                                } else {
                                        $temp = "Character Administration";
                                }
                        }
			if ((preg_grep ("/^$a$/i", $udfile)) & (!preg_grep ("/^$a$/i", $adminusers))) {
                                if ($temp !="") {
                                        $temp = $temp.", Upload/Download File";
                                } else {
                                        $temp = "Upload/Download Files";
                                }
                        }
			if ((preg_grep ("/^$a$/i", $savegame)) & (!preg_grep ("/^$a$/i", $adminusers))) {
                                if ($temp !="") {
                                        $temp = $temp.", Save Games";
                                } else {
                                        $temp = "Save Games";
                                }
                        }
			if ((preg_grep ("/^$a$/i", $deletegame)) & (!preg_grep ("/^$a$/i", $adminusers))) {
                                if ($temp !="") {
                                        $temp = $temp.", Delete Saved Games";
                                } else {
                                        $temp = "Delete Saved Games";
                                }
                        }
			if ((preg_grep ("/^$a$/i", $smessage)) & (!preg_grep ("/^$a$/i", $adminusers))) {
                                if ($temp !="") {
                                        $temp = $temp.", Send Players Messages";
                                } else {
                                        $temp = "Send Players Messages";
                                }
                        }
			if ((preg_grep ("/^$a$/i", $ban)) & (!preg_grep ("/^$a$/i", $adminusers))) {
                                if ($temp !="") {
                                        $temp = $temp.", Kick/Ban/Unban Players";
                                } else {
                                        $temp = "Kick/Ban/Unban Players";
                                }
                        }
			if ((preg_grep ("/^$a$/i", $backupvault)) & (!preg_grep ("/^$a$/i", $adminusers))) {
                                if ($temp !="") {
                                        $temp = $temp.", Backup Administration";
                                } else {
                                        $temp = "Backup Administration";
                                }
                        }
			if (($temp == "") & (!preg_grep ("/^$a$/i", $adminusers))) { $temp = "Basic Access"; }
			if (preg_grep("/^$authuser$/i", $adminusers) || ($adminusers[0] == "NONE")) {
				echo "<td><a href=\"index.php?item=adminsettings&area=userset\">$temp</a></td>";
                        } else {
                                echo "<td>$temp</td>";
                        }
			$temp = "";
			echo "</tr>";
        	}
		echo "</table>";
        	echo "<hr><br>";
        	echo "<a href=index.php?item=adduser>Add a User</a>";
        	echo "<br>";
	} else {
		echo "Nice Try, but you do not have access to this section!";
	}
 }

//#############################################//
//Delete a User or Change a Users Password form//
//#############################################//

 if($item == "deluserpass") {
	global $userad, $authuser, $adminusers;
        if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $userad))) {
 		$yourpass = "";
		echo "Change Password for User: <b>$username</b><br>";
		echo "<br>";
        	echo ("
        	<TABLE>
        	<form name=passdelete method=post action=index.php>
        	<input type=hidden name=item value=passchange>
        	<input type=hidden name=username value=\"$username\">
        	<TR><TD>New Password</TD><TD><input type=password name=password size=25 maxlength=25 value=$password></TD></TR>
        	<TR><TD><input type=submit></form></TD><TD></TD></TR>
        	</TABLE>
        	");
        	echo "<hr><br>";
        	echo "<a href=\"index.php?item=userdelete&username=$username\">Delete User <b>$username</b></a>";
	} else {
		echo "Nice Try, but you do not have access to this section!";
	}
 }

//########################//
//Change a User's Password//
//########################//

 if($item == "passchange") {
	global $userad, $authuser, $adminusers, $pchange;
        if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $userad)) || (preg_grep ("/^$authuser$/i", $pchange))) {
        	if ($password == "") {
                	echo "You must supply a new password if you wish to change it!";
                	exit();
        	}
        	$cmd = "$htpasswd -b $shadow \"$username\" $password";
        	exec($cmd);
        	echo "Password Changed for User $username<br>";
		if ($yourpass == "yourpass") {
			$yourpass = "";
			echo "<META HTTP-EQUIV=Refresh content=2;URL=index.php?>";
		} else {
			echo "<META HTTP-EQUIV=Refresh content=2;URL=index.php?item=useradmin>";
		}
	} else {
		echo "Nice Try, but you do not have access to this section!";
	}
 }

//#############//
//Delete a User//
//#############//

 if($item == "userdelete") {
	global $userad, $authuser, $adminusers;
	global $gamesettings, $userad, $sserver, $pchange, $runpass, $charad, $dcharacters, $ufile, $udfile, $savegame, $deletegame, $smessage, $ban, $backupvault;
        if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $userad))) {
		if (!preg_grep ("/^$username$/i", $adminusers)) {
        		$fp = file("$shadow");
        		$handle = fopen("$shadow",w);
        		fclose($handle);
        		foreach ($fp as $key => $value) {
                		if ($username != substr($value, 0, strlen($value)-strlen (strstr ($value,':')))) {
                        		$handle = fopen("$shadow",a);
                        		fwrite($handle, "$value");
                        		fclose($handle);
                		}
        		}
			$gamesettings = preg_grep("/^$username$/i", $gamesettings, PREG_GREP_INVERT);
                        $userad = preg_grep("/^$username$/i", $userad, PREG_GREP_INVERT);
                        $sserver = preg_grep("/^$username$/i", $sserver, PREG_GREP_INVERT);
                        $pchange = preg_grep("/^$username$/i", $pchange, PREG_GREP_INVERT);
                        $runpass = preg_grep("/^$username$/i", $runpass, PREG_GREP_INVERT);
                        $charad = preg_grep("/^$username$/i", $charad, PREG_GREP_INVERT);
			$dcharacters = preg_grep("/^$username$/i", $dcharacters, PREG_GREP_INVERT);
                        $udfile = preg_grep("/^$username$/i", $udfile, PREG_GREP_INVERT);
			$ufile = preg_grep("/^$username$/i", $ufile, PREG_GREP_INVERT);
                        $savegame = preg_grep("/^$username$/i", $savegame, PREG_GREP_INVERT);
                        $deletegame = preg_grep("/^$username$/i", $deletegame, PREG_GREP_INVERT);
                        $smessage = preg_grep("/^$username$/i", $smessage, PREG_GREP_INVERT);
                        $ban = preg_grep("/^$username$/i", $ban, PREG_GREP_INVERT);
                        $backupvault = preg_grep("/^$username$/i", $backupvault, PREG_GREP_INVERT);
			$adminusers2 = ""; $gamesettings2 = ""; $userad2 = ""; $sserver2 = ""; $pchange2 = ""; $runpass2 = ""; 
			$charad2 = ""; $udfile2 = ""; $savegame2 = ""; $deletegame2 = ""; $smessage2 = ""; $ban2 = ""; 
			$backupvault2 = ""; $dcharacters2 = ""; $ufile2 = "";
			foreach ($adminusers as $key => $value) {
                                if ($value != "") { 
                                        if ($adminusers2 != "") { 
                                                $adminusers2 = $adminusers2.",".$value;
                                        } else {
                                                $adminusers2 = $value;
                                        }
                                }
                        }
			foreach ($gamesettings as $key => $value) {
				if ($value != "") { 
					if ($gamesetting2 != "") { 
						$gamesettings2 = $gamesettings2.",".$value;
					} else {
						$gamesettings2 = $value;
					}
				}
			}
			foreach ($userad as $key => $value) {
                                if ($value != "") { 
                                        if ($userad2 != "") { 
                                                $userad2 = $userad2.",".$value;
                                        } else {
                                                $userad2 = $value;
                                        }
                                }
                        }
			foreach ($sserver as $key => $value) {
                                if ($value != "") { 
                                        if ($sserver2 != "") { 
                                                $sserver2 = $sserver2.",".$value;
                                        } else {
                                                $sserver2 = $value;
                                        }
                                }
                        }
			foreach ($pchange as $key => $value) {
                                if ($value != "") { 
                                        if ($pchange2 != "") { 
                                                $pchange2 = $pchange2.",".$value;
                                        } else {
                                                $pchange2 = $value;
                                        }
                                }
                        }
			foreach ($runpass as $key => $value) {
                                if ($value != "") { 
                                        if ($runpass2 != "") { 
                                                $runpass2 = $runpass2.",".$value;
                                        } else {
                                                $runpass2 = $value;
                                        }
                                }
                        }
			foreach ($charad as $key => $value) {
                                if ($value != "") { 
                                        if ($charad2 != "") { 
                                                $charad2 = $charad2.",".$value;
                                        } else {
                                                $charad2 = $value;
                                        }
                                }
                        }
			foreach ($dcharacters as $key => $value) {
                                if ($value != "") {
                                        if ($dcharacters2 != "") {
                                                $dcharacters2 = $dcharacters2.",".$value;
                                        } else {
                                                $dcharacters2 = $value;
                                        }
                                }
                        }
			foreach ($udfile as $key => $value) {
                                if ($value != "") { 
                                        if ($udfile2 != "") { 
                                                $udfile2 = $udfile2.",".$value;
                                        } else {
                                                $udfile2 = $value;
                                        }
                                }
                        }
			foreach ($ufile as $key => $value) {
                                if ($value != "") {
                                        if ($ufile2 != "") {
                                                $ufile2 = $ufile2.",".$value;
                                        } else {
                                                $ufile2 = $value;
                                        }
                                }
                        }
			foreach ($savegame as $key => $value) {
                                if ($value != "") { 
                                        if ($savegame2 != "") { 
                                                $savegame2 = $savegame2.",".$value;
                                        } else {
                                                $savegame2 = $value;
                                        }
                                }
                        }
			foreach ($deletegame as $key => $value) {
                                if ($value != "") { 
                                        if ($deletegame2 != "") { 
                                                $deletegame2 = $deletegame2.",".$value;
                                        } else {
                                                $deletegame2 = $value;
                                        }
                                }
                        }
			foreach ($smessage as $key => $value) {
                                if ($value != "") { 
                                        if ($smessage2 != "") { 
                                                $smessage2 = $smessage2.",".$value;
                                        } else {
                                                $smessage2 = $value;
                                        }
                                }
                        }
			foreach ($ban as $key => $value) {
                                if ($value != "") { 
                                        if ($ban2 != "") { 
                                                $ban2 = $ban2.",".$value;
                                        } else {
                                                $ban2 = $value;
                                        }
                                }
                        }
			foreach ($backupvault as $key => $value) {
                                if ($value != "") { 
                                        if ($backupvault2 != "") { 
                                                $backupvault2 = $backupvault2.",".$value;
                                        } else {
                                                $backupvault2 = $value;
                                        }
                                }
                        }
			if ($gamesettings2 == "") { $gamesettings2 = "NONE"; }
			if ($userad2 == "") { $userad2 = "NONE"; }
			if ($sserver2 == "") { $sserver2 = "NONE"; }
			if ($pchange2 == "") { $pchange2 = "NONE"; }
			if ($runpass2 == "") { $runpass2 = "NONE"; }
			if ($charad2 == "") { $charad2 = "NONE"; }
			if ($dcharacters2 == "") { $dcharacters2 = "NONE"; }
			if ($udfile2 == "") { $udfile2 = "NONE"; }
			if ($ufile2 == "") { $ufile2 = "NONE"; }
			if ($savegame2 == "") { $savegame2 = "NONE"; }
			if ($deletegame2 == "") { $deletegame2 = "NONE"; }
			if ($smessage2 == "") { $smessage2 = "NONE"; }
			if ($ban2 == "") { $ban2 = "NONE"; }
			if ($backupvault2 == "") { $backupvault2 = "NONE"; }
			$fp = fopen("./userpriv", w);
                	fwrite($fp, "$adminusers2\n");
                	fwrite($fp, "$gamesettings2\n");
                	fwrite($fp, "$userad2\n");
                	fwrite($fp, "$sserver2\n");
                	fwrite($fp, "$pchange2\n");
                	fwrite($fp, "$runpass2\n");
                	fwrite($fp, "$charad2\n");
			fwrite($fp, "$dcharacters2\n");
                	fwrite($fp, "$udfile2\n");
			fwrite($fp, "$ufile2\n");
                	fwrite($fp, "$savegame2\n");
                	fwrite($fp, "$deletegame2\n");
                	fwrite($fp, "$smessage2\n");
                	fwrite($fp, "$ban2\n");
                	fwrite($fp, "$backupvault2\n");
                	fclose($fp);
                       	$fp = file("./userpriv");
                       	foreach ($fp as $key => $value) {
                        	$a = $value;
                                $Option[$key] = $a;
                        }
                        $adminusers = split(",", eregi_replace("[^0-9a-z, ]", "", $Option[0]));
                        $gamesettings = split(",", eregi_replace("[^0-9a-z, ]", "", $Option[1]));
                        $userad = split(",", eregi_replace("[^0-9a-z, ]", "", $Option[2]));
                        $sserver = split(",", eregi_replace("[^0-9a-z, ]", "", $Option[3]));
                        $pchange = split(",", eregi_replace("[^0-9a-z, ]", "", $Option[4]));
                        $runpass = split(",", eregi_replace("[^0-9a-z, ]", "", $Option[5]));
                        $charad = split(",", eregi_replace("[^0-9a-z, ]", "", $Option[6]));
			$dcharacters = split(",", eregi_replace("[^0-9a-z, ]", "", $Option[7]));
                        $udfile = split(",", eregi_replace("[^0-9a-z, ]", "", $Option[8]));
			$ufile = split(",", eregi_replace("[^0-9a-z, ]", "", $Option[9]));
                        $savegame = split(",", eregi_replace("[^0-9a-z, ]", "", $Option[10]));
                        $deletegame = split(",", eregi_replace("[^0-9a-z, ]", "", $Option[11]));
                        $smessage = split(",", eregi_replace("[^0-9a-z, ]", "", $Option[12]));
                        $ban = split(",", eregi_replace("[^0-9a-z, ]", "", $Option[13]));
                        $backupvault = split(",", eregi_replace("[^0-9a-z, ]", "", $Option[14]));
			echo "User <b>$username</b> Deleted!";
        		echo "<META HTTP-EQUIV=Refresh content=2;URL=index.php?item=useradmin>";
		} else {
			echo "You Cannot Delete an Admin User";
			echo "<META HTTP-EQUIV=Refresh content=2;URL=index.php?item=useradmin>";
		}
	} else {
		echo "Nice Try, but you do not have access to this section!";
	}
 }

//###############//
//Add a User Form//
//###############//

 if($item == "adduser") {
	global $userad, $authuser, $adminusers;
        if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $userad))) {
        	echo "<h2><b>Add User</b></h2>";
        	echo ("
        	<TABLE>
        	<form name=adduser method=post action=index.php>
        	<input type=hidden name=item value=useradd>
        	<TR><TD>Username</TD><TD><input type=text name=username size=25 maxlength=25 value=$username></TD></TR>
        	<TR><TD>Password</TD><TD><input type=password name=password size=25 maxlength=25 value=$password></TD></TR>
        	<TR><TD><input type=submit></form></TD><TD></TD></TR>
        	</TABLE>
        	");
	} else {
		echo "Nice Try, but you do not have access to this section!";
	}
 }

//##########//
//Add a User//
//##########//

 if($item == "useradd") {
	global $userad, $authuser, $adminusers;
        if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $userad))) {
        	if ($username == "") {
                	echo "You must enter a username!<br>";
                	exit();
        	}
        	if ($password == "") {
                	echo "You must enter a password!<br>";
                	exit();
        	}
        	$fp = file("$shadow");
        	foreach ($fp as $key => $value) {
                	if ($username == substr($value, 0, strlen($value)-strlen (strstr ($value,':')))) {
                        	echo "username already exists<br>";
                        	exit();
                	}
        	}
        	$useraddcmd = "$htpasswd -b \"$shadow\" \"$username\" $password";
        	exec($useraddcmd);
	echo "<META HTTP-EQUIV=Refresh content=0;URL=index.php?item=useradmin>";
} else {
	echo "Nice Try, but you do not have access to this section!";
}
}

//########################//
//Character Administration//
//########################//

 if($item == "characteradmin") {
 	global $authuser, $adminusers;
 	if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $charad)) || (preg_grep ("/^$authuser$/i", $dcharacters))) {
		echo "<h2><b>Character Administration</b></h2><br>";
		if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE")) {
                        echo "Select users characters you wish to edit:<br><br>";
			$command = "$lsdir $nwserverdir/servervault";
			unset ($Result);
			exec ($command, $Result, $ReturnValue);
                	reset ($Result);
			if ($ReturnValue == "1") {
                        	echo "You do not have any User Character files on the server!";
                        	exit();
                	}
			echo "<TABLE width=50%>";
			$count = 0;
                	while (list($key,$val) = each($Result)) {
				echo "<TR><TD><b><a href=\"index.php?item=listchar&charsel=".$Result[$key+$count]."\"><FONT COLOR=00FFFF>".$Result[$key+$count]."</FONT></a></b></TD>";
				$count++;
				echo "<TD><b><a href=\"index.php?item=listchar&charsel=".$Result[$key+$count]."\"><FONT COLOR=00FFFF>".$Result[$key+$count]."</FONT></a></b></TD>";
				$count++;
				echo "<TD><b><a href=\"index.php?item=listchar&charsel=".$Result[$key+$count]."\"><FONT COLOR=00FFFF>".$Result[$key+$count]."</FONT></a></b></TD>";
                        }
			echo "</TR></TABLE>";
			
                } else {
			echo "<META HTTP-EQUIV=Refresh content=\"0;URL=index.php?item=listchar&charsel=$authuser\">";
		}

 	} else {
		echo "Nice Try, but you do not have access to this section!";
        }
 }

//###############//
//List Characters//
//###############//

 if($item == "listchar") {
	if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $charad)) || (preg_grep ("/^$authuser$/i", $dcharacters))) {
		echo "<h2><b>Character Administration</b></h2>";
		echo "<PRE>You are editing Character for User: <b>$charsel</b>";
		if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE")) {
			echo "          <a href=\"index.php?item=deletevault&vault=$charsel\">Delete $charsel's Vault</a></PRE><hr>";
		} else {
			echo "</PRE><hr>";
		}
		$command = "$lsdir $nwserverdir/servervault";
		unset ($Result);
		exec($command, $Result, $ReturnValue);
		reset ($Result);
		if ($ReturnValue == "1") {
			echo "You do not have any User Character files on the server!";
			exit();
		}
		if (!preg_grep ("/^$charsel$/i", $Result)) {
			echo "You do not have any characters on this server under the name of <b>$charsel</b><br>";
			echo "Make sure your login for this page is the same as your NeverWinter Nights Login!<br>";
			echo "If you need a new account, please contact <a href=mailto:$adminemail>The Administrator</a>";	
		} else {
			echo ("
                	<h2>Upload a Character</h2>
                	<form method=post action=$PHP_SELF enctype=multipart/form-data>
                	<input type=hidden name=item value=uploadcharacter>
                	<input type=hidden name=charsel value=\"$charsel\">
                	<table>
                	<tr><td>Browse Local Hard Drive</td><td><input type=file name=localfile size=26></td></tr>
                	<TR><TD><input type=submit name=submit value=SEND></TD></TR>
                	</form>
                	</table>
                	<hr>
                	");
			$command = "$lsdir \"$nwserverdir/servervault/$charsel\"";
			unset ($Result);
			exec ($command, $Result, $ReturnValue);
			reset ($Result);
			if ($ReturnValue == "1") {
                        	echo "You do not have any Character files on the server under user: <b>$charsel</b>!<br>";
                        	exit();
                	}
			echo "Click on <b>Character name</b> you wish to <b>download</b><br>";
			if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $dcharacters))) {
				echo "<b>OR</b><br>Click on <b>Delete</b> to remove character from the server.<br><br>";
			}
			echo "<TABLE width=100% border=1 cellpadding=2 cellspacing=0><TR>";
			echo "<TD><b>Character</b></TD><TD><b>View</b></TD>";
			if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $dcharacters))) {
				echo "<TD><b>Delete</b></TD>";
			}
			echo "</TR>";
			echo "<TR>";
			while (list($key,$val) = each($Result)) {
				$temp = eregi_replace(".bic$", "", $Result[$key + $count]);
				$charsel = eregi_replace("ö", "%F6", $charsel);
				echo "<TD><a href=\"./SERVERVAULT/$charsel/$val\">$temp</a></TD>";
				echo "<TD><a href=\"index.php?item=viewchar&characterfile=$val&user=$charsel\"><b>View Character</b></a></TD>";
				if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $dcharacters))) {
					echo "<TD><a href=\"index.php?item=delcharconf&characterfile=$val&user=$charsel\"><b>Delete Character</b></a></TD>";
				}
				echo "</TR>";
			}
			echo "</TABLE>";
		}
	
	} else {
		echo "Nice Try, but you do not have access to this section!";
        }
 }

//##############//
//View Character//
//##############//

if($item == "viewchar") {
	if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $charad)) || (preg_grep ("/^$authuser$/i", $dcharacters))) {
		$characterfile = eregi_replace("[\]", "", $characterfile);
		$bic= new character;
		$bic->loadfromfile("$nwserverdir/servervault/$user/$characterfile");
		$charname = $bic->GetVariable("FirstName") . " " . $bic->GetVariable("LastName");
		$gender = $bic->GetSex($bic->GetVariable("Gender"));
		$race = $bic->GetRace($bic->GetVariable("Race"));
		$age = $bic->GetVariable("Age");
		$deity = $bic->GetVariable("Deity");
		if($deity == "") { $deity = "NONE"; }
		$ac = $bic->GetVariable("ArmorClass");
		$curhitpoints = $bic->GetVariable("CurrentHitPoints");
		$maxhitpoints = $bic->GetVariable("MaxHitPoints");
		if($curhitpoints > $maxhitpoints) { $curhitpoints = $maxhitpoints; }
		$gold = $bic->GetVariable("Gold");
		$goodevil = $bic->GetVariable("GoodEvil");
		$lawfulchaotic = $bic->GetVariable("LawfulChaotic");
		if ($lawfulchaotic <= 15) { $align1 = "Chaotic"; }
		if (($lawfulchaotic > 15) && ($lawfulchaotic < 85) && ($goodevil >= 85) || ($goodevil <= 15)) { $align1 = "Neutral"; }
		if (($lawfulchaotic > 15) && ($lawfulchaotic < 85) && ($goodevil < 85) && ($goodevil > 15)) { $align1 = "True"; }
		if ($lawfulchaotic >= 85) { $align1 = "Lawful"; }
		if ($goodevil <= 15) { $align2 = "Evil"; }
		if (($goodevil > 15) && ($goodevil < 85)) { $align2 = "Neutral"; }
		if ($goodevil >= 85) { $align2 = "Good"; }
		$alignment = "$align1 $align2";
		$portrait = $bic->GetVariable("Portrait");
		$portrait = $portrait . ".jpg";
		$exp = $bic->GetVariable("Experience");
		$str = $bic->GetVariable("Str");
		$dex = $bic->GetVariable("Dex");
		$int = $bic->GetVariable("Int");
		$wis = $bic->GetVariable("Wis");
		$con = $bic->GetVariable("Con");
		$chr = $bic->GetVariable("Cha");
		$class = "";
		$level = 0;
                $cl = $bic->GetPos("ClassList");
                for ($c=1; $c<=$bic->Elements[$cl][NumItems]; $c++) {
                        $class = $class . $bic->GetClass($bic->Elements[$bic->Entries[$bic->Elements[$cl][$c]][1]][Data]);
                        $class = $class . " (" . $bic->Elements[$bic->Entries[$bic->Elements[$cl][$c]][2]][Data] . ")";
                        $level = $level + $bic->Elements[$bic->Entries[$bic->Elements[$cl][$c]][2]][Data];
                        if($c<$bic->Elements[$cl][NumItems]) {
                                $class = $class . " / ";
                        }
                }
		echo "<TABLE cellpadding=0 cellspacing=4 border=0 width=100%>";
		echo "<tr>";
		echo "<TD><IMG SRC=\"./portraits/$portrait\"></IMG></TD></TR><TR>";
		echo "<TD><font color=ffff00><b>Player Name:</b> </font>$user</TD>";
		echo "<TD><font color=ffff00><b>Character Name:</b> </font>$charname</TD>";
		echo "<TD><font color=ffff00><b>Alignment:</b> </font>$alignment</TD></TR>";
		echo "<TR><TD><font color=ffff00><b>Gender:</b> </font>$gender</TD>";
		echo "<TD><font color=ffff00><b>Age:</b> </font>$age</TD>";
		echo "<TD><font color=ffff00><b>Deity:</b> </font>$deity</TD></TR>";
		echo "<TR><TD><font color=ffff00><b>Race:</b> </font>$race</TD>";
		echo "<TD><font color=ffff00><b>Class:</b> </font>$class</TD>";
		echo "<TD><font color=ffff00><b>Level:</b> </font>$level</TD>";
		echo "<TR><TD><font color=ffff00><b>Gold:</b> </font>$gold</TD>";
		echo "<TD><font color=ffff00><b>HitPoints:</b> </font>$curhitpoints/$maxhitpoints</TD>";
		echo "<TD><font color=ffff00><b>Experience:</b> </font>$exp</TD></TR>";
		echo "<TR></TR>";
		echo "<TR><TD><font color=ffff00><b>Armor Class:</b> </font>$ac</TD></TR>";
		echo "<TR><TD><font color=ffff00><b>Str:</b> </font>$str</TD></TR>";
		echo "<TR><TD><font color=ffff00><b>Dex:</b> </font>$dex</TD></TR>";
		echo "<TR><TD><font color=ffff00><b>Int:</b> </font>$int</TD></TR>";
		echo "<TR><TD><font color=ffff00><b>Wis:</b> </font>$wis</TD></TR>";
		echo "<TR><TD><font color=ffff00><b>Con:</b> </font>$con</TD></TR>";
		echo "<TR><TD><font color=ffff00><b>Chr:</b> </font>$chr</TD></TR>";
		echo "<TR></TR><TR></TR></TABLE>";
		echo "<form name=OK method=post action=index.php>";
                echo "<input type=hidden name=item value=listchar>";
		echo "<input type=hidden name=charsel value=\"$user\">";
		echo "<CENTER><input type=submit value=\"Return to Character List\" name=OK></CENTER></form>";
	} else {
                echo "Nice Try, but you do not have access to this section!";
        }
 }

//###################################//
//Delete Character Vault Confirmation//
//###################################//

 if($item == "deletevault") {
	if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE")) {
		echo "Are you sure you want to delete <b>$vault</b>'s character vault?<br><br>";
                echo "<a href=\"index.php?item=delcharvaultnow&user=$vault\">YES</a>&nbsp; &nbsp;<a href=index.php?item=>NO</a>";
	} else {
                echo "Nice Try, but you do not have access to this section!";
        }
 }

//######################//
//Delete Character Vault//
//######################//

 if($item == "delcharvaultnow") {
	if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE")) {
		$command = "$rmdir -fr \"$nwserverdir/servervault/$user\"";
		exec($command);
		echo "<META HTTP-EQUIV=Refresh content=\"0;URL=index.php?item=characteradmin\">";
	} else {
		echo "Nice Try, but you do not have access to this section!";
        }
 }

//################//
//Upload Character//
//################//

 if($item == "uploadcharacter") {
	if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $charad))) {
		if (is_uploaded_file($localfile)) {
                        $newfile = "$nwserverdir/servervault/$charsel/$localfile_name";
                        if (!preg_match ("/bic/i", $localfile_name)) {
                                echo "You can only upload <b>.bic</b> files for characters!<br>";
				exit();
			} else {
				if (file_exists($newfile)) {
					echo "<b>$localfile_name</b> already exists in the user: <b>$charsel</b>'s Directory!<br>";
				} else {
					if (!copy($localfile, $newfile)) {
                                        	echo "Error Uploading File <b>$localfile_name</b>.";
                                        	exit();
                                	} else {
						chmod ("$newfile", 0777);
						$temp = eregi_replace(" ", "%20", $charsel);
						echo "<META HTTP-EQUIV=Refresh content=\"0;URL=index.php?item=listchar&charsel=$charsel\">";
					}
				}
			}
		} else {
                        echo "Maximum Upload Size of 100 Meg exceeded, file not uploaded!";
		}
	} else {
		echo "Nice Try, but you do not have access to this section!";
        }
 }

//#############################//
//Delete Character Confirmation//
//#############################//

 if($item == "delcharconf") {
 	if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $dcharacters))) {
		echo "Are you sure you want to delete <b>$characterfile</b> in user: <b>$user</b>'s Directory?<br><br>";
                echo "<a href=\"index.php?item=delcharnow&file=$characterfile&user=$user\">YES</a>&nbsp; &nbsp;<a href=index.php?item=>NO</a>";
	} else {
		echo "Nice Try, but you do not have access to this section!";
        }
 }

//################//
//Delete Character//
//################//

 if($item == "delcharnow") {
	if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $dcharacters))) {
		$file = eregi_replace("\\\\", "", $file);
		$deletefile = "rm -fr \"$nwserverdir/servervault/$user/$file\"";
                exec ($deletefile);
		echo "<META HTTP-EQUIV=Refresh content=\"0;URL=index.php?item=listchar&charsel=$user\">";
	} else {
		echo "Nice Try, but you do not have access to this section!";
        }
 }

//#################//
//Start Server Form//
//#################//

 if($item == "start") {
	global $authuser, $adminusers, $ssever;
	if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $sserver))) {
       		echo "Which Module Would You Like To Load?";
        	echo "<br><br>";
        	if($expandinfo == "TRUE") {
                	echo "<a href=index.php?item=start&expandinfo=FALSE>Collapse Info</a>";
        	} else {
                	echo "<a href=index.php?item=start&expandinfo=TRUE>More Info</a>";
        	}
        	echo "<br><br>";
		echo "<h2><b><center>NeverWinter Nights Installed Campaigns:</center></b></h2><br>";
        	echo "<table width=100% border=1 cellpadding=2 cellspacing=0>";
		$Query1 = "$lsdir \"$nwserverdir/nwm\"";
                unset ($Result1);
                exec ($Query1, $Result1, $ReturnValue1);
                reset ($Result1);
                while (list($key,$val) = each($Result1))
                {
                        if($expandinfo == "TRUE") {
                                $module = new module;
                                $module->loadfromfile("$nwserverdir/modules/$val");
                        }
                        $val = eregi_replace(".nwm$", "", $val);
                        print "<tr><td valign=top><font color=ffffff><a href=\"index.php?item=startserver&startserverwith=$val\">$val</a></font></td>";
                        if($expandinfo == "TRUE") {
                                for ($i=0; $i < count($module->Description);$i++)
                                {
                                        $para = eregi_replace("\n","<BR>",$module->Description[$i]);
					echo "<td valign=top>";
                                        print $para;
                                        if ($i+1 !=count($module->Description)) { echo "<br>";}
                                }
				echo "</td>";
                        }
                        print "</tr>";
                }
                echo "</table>";
		echo "<br><br><h2><b><center>Other Installed Modules:</center></b></h2><br>";
		echo "<table width=100% border=1 cellpadding=2 cellspacing=0>";
        	$Query1 = "$lsdir \"$nwserverdir/modules\"";
		unset ($Result1);
        	exec ($Query1, $Result1, $ReturnValue1);
        	reset ($Result1);
        	while (list($key,$val) = each($Result1))
        	{
                	if($expandinfo == "TRUE") {
                        	$module = new module;
                        	$module->loadfromfile("$nwserverdir/modules/$val");
                	}
                	$val = eregi_replace(".mod$", "", $val);
                	print "<tr><td valign=top><font color=ffffff><a href=\"index.php?item=startserver&startserverwith=$val\">$val</a></font></td>";
                	if($expandinfo == "TRUE") {
                        	for ($i=0; $i < count($module->Description);$i++)
                        	{
                                	$para = eregi_replace("\n","<BR>",$module->Description[$i]);
					echo "<td valign=top>";
                                	print $para;
                                	if ($i+1 !=count($module->Description)) { echo "<br>";}
                        	}
				echo "</td>";
                	}
                	print "</tr>";
        	}
        	echo "</table>";
 	} else {
		echo "Nice Try, but you do not have access to this section!";
	}
 }

//############//
//Start Server//
//############//

 if($item == "startserver") {
	global $authuser, $adminusers, $ssever;
        if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $sserver))) {
        	if($procnum >= 3) {
			$startserver = "./changemod \"$startserverwith\"";
        	} else {
        		$fp = file("./nwn.config");
        		foreach ($fp as $key => $value) {
                		$a = $value;
                		$a = substr($a, 0, -1);
                		$Option[$key] = $a;
        		}
               		$playerpass = "$Option[12]";
               		$startserver = "./startserver $Option[0] $Option[1] $Option[2] $Option[3] $Option[4] $Option[5] $Option[6] $Option[7] $Option[8] $Option[9] $Option[10] $Option[11] \"$Option[12]\" $Option[13] $Option[14] \"$Option[15]\" $Option[16] $Option[17] $Option[18] \"$startserverwith\" \"$nwserverdir\"";
		}
        	shell_exec($startserver);
        	echo "<META HTTP-EQUIV=Refresh content=0;URL=index.php>";
        	$fp = fopen("./currentmod", w);
        	fwrite($fp, "$startserverwith\n");
        	fclose($fp);
	} else {
		echo "Nice Try, but you do not have access to this section!";
        }
 }

//################//
//Stop Server Form//
//################//

 if($item == "stop") {
	global $authuser, $adminusers, $ssever;
        if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $sserver))) {
        	echo "<h2>Are you sure you want to stop nwserver?</h2>";
        	echo "<a href=index.php?item=stopnow>YES</a>&nbsp; &nbsp;<a href=index.php?item=>NO</a>";
	} else {
		echo "Nice Try, but you do not have access to this section!";
        }
 }

//#######################//
//Stop Server immediately//
//#######################//

 if($item == "stopnow") {
	global $authuser, $adminusers, $ssever;
        if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $sserver))) {
        	$killnwserver = "./killserver";
        	exec($killnwserver);
		$command = "$rmdir -fr $nwserverdir/logs.*";
		exec($command);	
        	echo "<br><br>nwserver should be down now, check <a href=index.php?item=status>status</a>";
        	echo "<META HTTP-EQUIV=Refresh content=0;URL=index.php>";
	} else {
		echo "Nice Try, but you do not have access to this section!";
        }
 }

//#######################//
//Stop Server Nicely Form//
//#######################//

 if($item == "stopnice") {
	global $authuser, $adminusers, $ssever;
        if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $sserver))) {
        	echo "<h2>Are you sure you want to stop nwserver?</h2><font color=ffff00>This way will send a count of 10 to all players before shutdown</font><br><br>";
        	echo "<a href=index.php?item=stopnownice>YES</a>&nbsp; &nbsp;<a href=index.php?item=>NO</a>";
	} else {
		echo "Nice Try, but you do not have access to this section!";
        }
 }

//##################//
//Stop Server Nicely//
//##################//

 if($item == "stopnownice") {
	global $authuser, $adminusers, $ssever;
        if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $sserver))) {
        	$killnwserver = "./killallsay";
        	exec($killnwserver);
		$command = "$rmdir -fr $nwserverdir/logs.*";
                exec($command);
        	echo "<br><br>nwserver should be down now, check <a href=index.php?item=status>status</a>";
        	echo "<META HTTP-EQUIV=Refresh content=0;URL=index.php>";
	} else {
		echo "Nice Try, but you do not have access to this section!";
        }
 }

//####################//
//Change your password//
//####################//

 if($item == "changeuserpass") {
	global $authuser, $adminusers, $pchange;
	if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $pchange))) {
	 	echo "Change Password for <b>$authuser</b><br>";
  		echo "<br>";
  		echo ("
  		<TABLE>
  		<form name=passdelete method=post action=index.php>
 	 	<input type=hidden name=item value=passchange>
  		<input type=hidden name=username value=\"$authuser\">
		<input type=hidden name=yourpass value=yourpass>
  		<TR><TD>New Password</TD><TD><input type=password name=password size=25 maxlength=25 value=$password></TD></TR>
  		<TR><TD><input type=submit></form></TD><TD></TD></TR>
  		</TABLE>
  		");
	} else {
		echo "Nice Try, but you do not have access to this section!";
        }
 }

//#############################//
//Change Running Passwords Form//
//#############################//

 if($item == "changepass") {
	global $adminusers, $authuser, $runpass;
	if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $runpass))) {
        	echo ("
        	<font color=ff0000><b>Note:</b></font> if you restart, load a new module, or load a saved game, the passwords that will be set are those located in the settings screen. This screen allows you to change the password of the running server without restarting.<br><br><font color=ffcc33><b>This only works with Server Ver. 1.21 or greater<br><br><font color=ff0000><b>Note:</b></font>CHARACTERS OTHER THAN ALPHA-NUMERIC WILL BE STRIPPED AND IGNORED</b></font><br><br>
        	<table>
        	<form name=changepass method=post action=index.php>
        	<input type=hidden name=item value=changepasswds>
        	<tr><td>Change Player Password</td><td><input type=password name=playerpassword></td><td>Remove Player Password</td><td>
        	<input type=checkbox name=disableplayer value=yes></td></tr>
        	<tr><td>Change DM Password</td><td><input type=password name=dmpassword></td><td>Disable DM Login</td><td>
        	<input type=checkbox name=disabledm value=yes></td></tr>
        	<tr><td>Change Admin Password</td><td><input type=password name=adminpassword></td><td>Disable Admin Login</td><td>
        	<input type=checkbox name=disableadmin value=yes></td></tr>
        	<tr><td><input type=submit></form></td></tr></table>
        	");
	} else {
		echo "Nice Try, but you do not have access to this section!";
        }
 }

//########################//
//Change Running Passwords//
//########################//

 if($item == "changepasswds") {
	global $adminusers, $authuser, $runpass;
        if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $runpass))) {
        	if($playerpassword != "") {
                	$playerpassword = eregi_replace("[^0-9a-z ]", "", $playerpassword);
                	$player = "./passchange playerpassword $playerpassword";
                	exec($player);
                	echo "Player password has changed<br>";
        	}
        	if($disableplayer == "yes") {
                	$player = "./passchange playerpassword";
                	exec($player);
                	echo "Player password has been removed<br>";
        	}
        	if($dmpassword != "") {
                	$dmpassword = eregi_replace("[^0-9a-z ]", "", $dmpassword);
                	$dm = "./passchange dmpassword $dmpassword";
                	exec($dm);
                	echo "DM password has changed<br>";
        	}
        	if($disabledm == "yes") {
                	$dm = "./passchange dmpassword";
                	exec($dm);
                	echo "DM password has been disabled<br>";
        	}
        	if($adminpassword != "") {
                	$adminpassword = eregi_replace("[^0-9a-z ]", "", $adminpassword);
                	$admin = "./passchange adminpassword $adminpassword";
                	exec($admin);
                	echo "Admin password has changed<br>";
        	}
        	if($disableadmin == "yes") {
                	$admin = "./passchange adminpassword";
                	exec($admin);
                	echo "Admin password has been disabled<br>";
        	}
		echo "<META HTTP-EQUIV=Refresh content=2;URL=index.php?item=status>";
	} else {
		echo "Nice Try, but you do not have access to this section!";
        }
 }

//#################################//
//HAK, Modules, Movies & Music Form//
//#################################//

 if($item == "hmmm") {
	global $authuser, $adminusers, $udfile, $ufile, $type, $dir, $ext;
        if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $udfile)) || (preg_grep ("/^$authuser$/i", $ufile))) {
		echo "<h2>HAK, Modules, Movies & Music Menu</h2>";
		echo "<b>HAK</b> files must end in <b>.hak</b><br>";
		echo "<b>Module</b> files must end in <b>.mod</b><br>";
		echo "<b>Movie</b> files must end in <b>.bik</b><br>";
		echo "<b>Music</b> files must end in <b>.bmu</b><br>";
		echo "<b>ZIP</b> files must end in <b>.zip</b><br>";
		if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $ufile))) {
			echo "<br>If you upload a .zip file, the system will automatically unzip the file and move all HAK, Modules, Movies and Music files to the appropriate directories.<br>";
		}
		echo "<hr>";
		if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $ufile))) {
			echo "<h2>Upload File</h2>";
			echo ("
                	<font color=ff0000><b>Note: </b></font>This form will allow you to Upload new $type files from your hard drive.
                	<br><br>
                	<form method=post action=$PHP_SELF enctype=multipart/form-data>
                	<input type=hidden name=item value=installmod>
                	<table>
                	<tr><td>Browse Local Hard Drive</td><td><input type=file name=localfile size=26></td></tr>
                	<TR><TD><input type=submit name=submit value=SEND></TD></TR>
                	</form>
                	</table>
                	");
			echo "<hr>";
		}
		if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $deletegame))) {
			echo "<li><a href=index.php?item=filelist&type=hak&dir=hak&ext=.hak>List/Download/Delete a HAK File</a>";
			echo "<li><a href=index.php?item=filelist&type=module&dir=modules&ext=.mod>List/Download/Delete a Module File</a>";
			echo "<li><a href=index.php?item=filelist&type=movie&dir=movies&ext=.bk>List/Download/Delete a Movie File</a>";
			echo "<li><a href=index.php?item=filelist&type=music&dir=music&ext=.bmu>List/Download/Delete a Music File</a>";
			echo "<li><a href=index.php?item=filelist&type=zip&dir=zippedmodules&ext=.zip>List/Download/Delete a ZIP File</a>";
		} else {
			echo "<li><a href=index.php?item=filelist&type=hak&dir=hak&ext=.hak>List/Download a HAK File</a>";
                	echo "<li><a href=index.php?item=filelist&type=module&dir=modules&ext=.mod>List/Download a Module File</a>";
                	echo "<li><a href=index.php?item=filelist&type=movie&dir=movies&ext=.bk>List/Download a Movie File</a>";
                	echo "<li><a href=index.php?item=filelist&type=music&dir=music&ext=.bmu>List/Download a Music File</a>";
                	echo "<li><a href=index.php?item=filelist&type=zip&dir=zippedmodules&ext=.zip>List/Download a ZIP File</a>";
		}
	} else {
		echo "Nice Try, but you do not have access to this section!";
        }
 }

//################################//
//List/Download/Delete extra Files//
//################################//
 
 if($item == "filelist") {
	global $authuser, $adminusers, $udfile, $ufile, $type, $dir, $ext;
	if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $udfile)) || (preg_grep ("/^$authuser$/i", $ufile))) {
		echo "<h2><b>Download a ".strtoupper($type)." File</b></h2><br>";
		if($type == "module") {
			$Query = "$lsdir \"$nwserverdir/nwm\"";
			unset ($Result);
			exec ($Query, $Result, $ReturnValue);
			reset ($Result);
			echo "<h2><b><center>NeverWinter Nights Installed Campaigns:</center></b></h2><br>";
			echo "<TABLE width=100% border=1 cellpadding=2 cellspacing=0>";
	                while (list($key,$val) = each($Result)) {
                        	if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $deletegame))) {
                                
					echo "<TR><TD><a href=\"./NWM/$val\">$val</a></TD><TD><a href=\"index.php?item=delfileconf&file=$val&dir=nwm&type=$type\"><font color=00ffff>DELETE</font></a></TD>";
                        	} else {
                                	echo "<TR><TD><a href=\"./NWM/$val\">$val</a></TD><TD></TD>";
                        	}
                	}
                	echo "</TR></TABLE>";
		}
		if($type == "module") {
			echo "<h2><b><center>Other Installed Modules:</center></b></h2><br>";
		}
		$Query = "$lsdir \"$nwserverdir/$dir\"";
		unset ($Result);
		exec ($Query, $Result, $ReturnValue);
                reset ($Result);
		echo "<TABLE width=100% border=1 cellpadding=2 cellspacing=0>";
                while (list($key,$val) = each($Result)) {
			if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $deletegame))) {
                		echo "<TR><TD><a href=\"./".strtoupper($dir)."/$val\">$val</a></TD><TD><a href=\"index.php?item=delfileconf&file=$val&dir=$dir&type=$type\"><font color=00ffff>DELETE</font></a></TD>";
			} else {
				echo "<TR><TD><a href=\"./".strtoupper($dir)."/$val\">$val</a></TD><TD></TD>";
			}
		}		
		echo "</TR></TABLE>";
	} else {
		echo "Nice Try, but you do not have access to this section!";
	}
 }


//#############//
//Upload a File//
//#############//

 if($item == "installmod") {
	global $authuser, $adminusers, $udfile, $ufile, $type, $dir, $ext;
        if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $ufile))) {
		if (is_uploaded_file($localfile)) {
			if (preg_match ("/.hak/i", $localfile_name)) { $ext = ".hak"; $type = "hak"; $dir="hak"; }
			if (preg_match ("/.bmu/i", $localfile_name)) { $ext = ".bmu"; $type = "music"; $dir = "music"; }
			if (preg_match ("/.nwm/i", $localfile_name)) { $ext = ".nwm"; $type = "NW module"; $dir = "nwm"; }
			if (preg_match ("/.mod/i", $localfile_name)) { $ext = ".mod"; $type = "module"; $dir = "modules"; }
			if (preg_match ("/.bik/i", $localfile_name)) { $ext = ".bik"; $type = "movie"; $dir = "movies"; }
			if (preg_match ("/.zip/i", $localfile_name)) { $ext = ".zip"; $type = "zip"; $dir = "zippedmodules"; }
			if (preg_match ("/\\\\/", $localfile_name)) { $localfile_name = preg_replace("/\\\\/", "", $localfile_name); }
			if ((!preg_match ("/.hak/i", $localfile_name)) & (!preg_match ("/.bmu/i", $localfile_name)) & (!preg_match ("/.nwm/i", $localfile_name)) & (!preg_match ("/.mod/i", $localfile_name)) & (!preg_match ("/.bik/i", $localfile_name)) & (!preg_match ("/.zip/i", $localfile_name))) { $ext = ""; $type = ""; $dir = ""; }
			if ($type != "zip") {
                		$newfile = "$nwserverdir/$dir/" . $localfile_name;
			} else {
				$newfile = "./tempupload/" . $localfile_name;
			}
                	if ($ext == "") {
                        	echo "You can only upload <b>.hak, .bmu, .mod, .bik, or .zip</b> files";
				echo "<META HTTP-EQUIV=Refresh content=2;URL=index.php?item=hmmm>";
                        	exit();
                	}
                 	if (file_exists($newfile)) {
                        	echo "<b>$localfile_name</b> already exists on server!";
				echo "<META HTTP-EQUIV=Refresh content=2;URL=index.php?item=hmmm>";
                        	exit();
                	}
			if ($type != "zip") {
                		if (!copy($localfile, $newfile)) {
                        		echo "Error Uploading File <b>$localfile_name</b>.";
					echo "<META HTTP-EQUIV=Refresh content=2;URL=index.php?item=hmmm>";
                        		exit();
                		} else {
                        		echo "<li><b>$localfile_name</b> was uploaded successfully!<br>";
                		}
			} else {
				if (!copy($localfile, $newfile)) {
					echo "Error Uploading File <b>$localfile_name</b>.";
					echo "<META HTTP-EQUIV=Refresh content=2;URL=index.php?item=hmmm>";
					exit();
				} else {
					echo "<b>$localfile_name</b> was uploaded successfully!<br>";
					if (file_exists("$nwserverdir/zippedmodules/$localfile_name")) {
						echo "<b>$localfile_name</b> already exists on server!";
						echo "<br>No further action taken";
						$command = "$rmdir -fr \"$newfile\"";
						exec($command);
						echo "<META HTTP-EQUIV=Refresh content=4;URL=index.php?item=hmmm>";
						exit();
					} else {
						$command = "$unzipdir -j -d ./tempupload/ \"$newfile\"";
						exec($command);
						echo "<li><b>$localfile_name</b> successfully unzipped!<br><br>";
						$Query = "$lsdir ./tempupload";
						unset ($Result);
                				exec ($Query, $Result, $ReturnValue);
                				reset ($Result);
						$cparc = 0;
                				while (list($key,$val) = each($Result)) {
							if (preg_match ("/.hak/i", $val)) {
								if (file_exists("$nwserverdir/hak/$val")) {
									echo "<b>$val</b> already exists on server!<br>";
								} else {
									copy("./tempupload/$val", "$nwserverdir/hak/$val");
									$cparc = 1;
									echo "<li><b>$val</b> successfully copied to HAK<br>";
								}
							}
							if (preg_match ("/.nwm/i", $val)) {
                                                                if (file_exists("$nwserverdir/nwm/$val")) {
                                                                        echo "<b>$val</b> already exists on server!<br>";
                                                                } else {
                                                                        copy("./tempupload/$val", "$nwserverdir/nwm/$val");
                                                                        $cparc = 1;
                                                                        echo "<li><b>$val</b> successfully copied to modules<br>";
                                                                }
                                                        }
							if (preg_match ("/.mod/i", $val)) {
								if (file_exists("$nwserverdir/modules/$val")) {
									echo "<b>$val</b> already exists on server!<br>";
								} else {
									copy("./tempupload/$val", "$nwserverdir/modules/$val");
									$cparc = 1;
									echo "<li><b>$val</b> successfully copied to modules<br>";
								}
							}
							if (preg_match ("/.bik/i", $val)) {
								if (file_exists("$nwserverdir/movies/$val")) {
									echo "<b>$val</b> already exists on server!<br>";
								} else {
									copy("./tempupload/$val", "$nwserverdir/movies/$val");
									$cparc = 1;
									echo "<li><b>$val</b> successfully copied to movies<br>";
								}
							}
							if (preg_match ("/.bmu/i", $val)) {
								if (file_exists("$nwserverdir/music/$val")) {
									echo "<b>$val</b> already exists on server!<br>";
								} else {
									copy("./tempupload/$val", "$nwserverdir/music/$val");
									$cparc = 1;
									echo "<li><b>$val</b> successfully copied to music<br>";
								}
							}
							if ((preg_match ("/readme.txt/i", $val)) | (preg_match ("/read me.txt/i", $val)) && ($cparc == 1)) {
								$temp = eregi_replace(".zip$", "", $localfile_name);
								if (file_exists("$nwserverdir/moduleinfo/$temp.txt")) {
									echo "<b>$temp.txt</b> already exists on server!<br>";
								} else {
									copy("./tempupload/$val", "$nwserverdir/moduleinfo/$temp.txt");
									echo "<li><b>$temp.txt</b> successfully copied to moduleinfo<br>";
								}
							} else {
								if ((preg_match ("/.txt/i", $val)) | (preg_match ("/.pdf/i", $val)) | (preg_match ("/.htm/i", $val)) | (preg_match("/.jpg/i", $val)) | (preg_match ("/.doc/i", $val)) | (preg_match ("/.gif/i", $val)) | (preg_match ("/.bmp/i", $val)) && ($cparc == 1)) {
									if (file_exists("$nwserverdir/moduleinfo/$val")) {
										echo "<b>$val</b> already exists on server!<br>";
									} else {
										copy("./tempupload/$val", "$nwserverdir/moduleinfo/$val");
										echo "<li><b>$val</b> successfully copied to moduleinfo<br>";
									}
								}
							}
                				}
						if ($cparc == 1) {
							$temp = "$nwserverdir/zippedmodules/$localfile_name";
							if (copy($newfile, $temp)) {
								echo "<br><li><b>$localfile_name</b> successfully copied to ZIP Archive!<br>";
							} else {
								echo "<br><b>$localfile_name</b> could not be copied to the ZIP Archive!<br>";
							}
						} else {
							echo "<br><b>$localfile_name</b> was not copied to ZIP Archive as it does <b>NOT</b> contain NWN Files!<br>";
						}
						$command = "$rmdir -fr ./tempupload/*";
						exec($command);
					}
				}
			}
			echo "<META HTTP-EQUIV=Refresh content=4;URL=index.php?item=hmmm>";
       		} else {
                	echo "Maximum Upload Size of 100 Meg exceeded, file not uploaded!";
        	}
	} else {
		echo "Nice Try, but you do not have access to this section!";
        }
 }

//##########################//
//Delete a File Confirmation//
//##########################//

 if($item == "delfileconf") {
        global $authuser, $adminusers, $deletegame, $type, $dir;
        if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $deletegame))) {
		if (preg_match("/\\\\/", $file)) {
			$file = preg_replace("/\\\\/", "", $file);
		}
        	echo "Are you sure you want to delete $file<br><br>";
                echo "<a href=\"index.php?item=delfilenow&file=$file&dir=$dir&type=$type\">YES</a>&nbsp; &nbsp;<a href=index.php?item=filelist&dir=$dir&type=$type>NO</a>";
        } else {
                echo "Nice Try, but you do not have access to this section!";
        }
 }

//#################//
//Delete a File Now//
//#################//

 if($item == "delfilenow") {
        global $authuser, $adminusers, $deletegame;
        if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $deletegame))) {
		if (preg_match("/\\\\/", $file)) {
                        $file = preg_replace("/\\\\/", "", $file);
                }
                $deletefile = "rm -fr \"$nwserverdir/$dir/$file\"";
                exec ($deletefile);
                echo "$file Deleted<br><br>";
		if ($dir == "nwm") { $dir = "modules"; }
		echo "<META HTTP-EQUIV=Refresh content=2;URL=index.php?item=filelist&dir=$dir&type=$type>";
        } else {
                echo "Nice Try, but you do not have access to this section!";
        }
 }

//###########//
//Load Module//
//###########//

 if($item == "loadmod") {
	global $authuser, $adminusers, $sserver;
	if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $sserver))) {
        	echo "<h2>Which Module Would You Like To Load?</h2>";
        	if($expandinfo == "TRUE") {
                	echo "<a href=index.php?item=loadmod&expandinfo=FALSE>Collapse Info</a>";
        	} else {
                	echo "<a href=index.php?item=loadmod&expandinfo=TRUE>More Info</a>";
        	}
        	echo "<br><br>";
        	echo "<h2><b><center>NeverWinter Nights Installed Campaigns:</center></b></h2><br>";
                echo "<table width=100% border=1 cellpadding=2 cellspacing=0>";
                $Query1 = "$lsdir \"$nwserverdir/nwm\"";
                unset ($Result1);
                exec ($Query1, $Result1, $ReturnValue1);
                reset ($Result1);
                while (list($key,$val) = each($Result1))
                {
                        if($expandinfo == "TRUE") {
                                $module = new module;
                                $module->loadfromfile("$nwserverdir/modules/$val");
                        }
                        $val = eregi_replace(".nwm$", "", $val);
                        print "<tr><td valign=top><font color=ffffff><a href=\"index.php?item=startserver&startserverwith=$val\">$val</a></font></td>";
                        if($expandinfo == "TRUE") {
                                for ($i=0; $i < count($module->Description);$i++)
                                {
                                        $para = eregi_replace("\n","<BR>",$module->Description[$i]);
                                        echo "<td valign=top>";
                                        print $para;
                                        if ($i+1 !=count($module->Description)) { echo "<br>";}
                                }
                                echo "</td>";
                        }
                        print "</tr>";
                }
                echo "</table>";
                echo "<br><br><h2><b><center>Other Installed Modules</center></b></h2><br>";
                echo "<table width=100% border=1 cellpadding=2 cellspacing=0>";
                $Query1 = "$lsdir \"$nwserverdir/modules\"";
                unset ($Result1);
                exec ($Query1, $Result1, $ReturnValue1);
                reset ($Result1);
                while (list($key,$val) = each($Result1)) {
                        if($expandinfo == "TRUE") {
                                $module = new module;
                                $module->loadfromfile("$nwserverdir/modules/$val");
                        }
                        $val = eregi_replace(".mod$", "", $val);
                        print "<tr><td valign=top><font color=ffffff><a href=\"index.php?item=startserver&startserverwith=$val\">$val</a></font></td>";
                        if($expandinfo == "TRUE") {
                                for ($i=0; $i < count($module->Description);$i++) {
                                        $para = eregi_replace("\n","<BR>",$module->Description[$i]);
                                        echo "<td valign=top>";
                                        print $para;
                                        if ($i+1 !=count($module->Description)) { echo "<br>";}
                                }
                                echo "</td>";
                        }
                        print "</tr>";
                }
        	echo "</table>";
	} else {
		echo "Nice Try, but you do not have access to this section!";
        }
 }

//##############//
//Load Game Form//
//##############//

 if($item == "load") {
	global $authuser, $adminusers, $sserver;
        if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $sserver))) {
        	echo "<h2>Choose a slot to load</h2>";
        	$Query = "$lsdir \"$nwserverdir/saves\"";
		unset ($Result);
        	exec ($Query, $Result, $ReturnValue);
        	reset ($Result);
        	while (list($key,$val) = each($Result))
        	{
                	$val2 = eregi_replace(" - .*", "", $val);
                	$val2 = substr($val,4,2);
                	print "<font color=ffffff><a href=index.php?item=loadslot&slot=$val2>$val</a></font><BR>";
        	}
	} else {
		echo "Nice Try, but you do not have access to this section!";
        }
 }

//#########//
//Load Game//
//#########//

 if($item == "loadslot") {
	global $authuser, $adminusers, $sserver;
        if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $sserver))) {
		if ($procnum >= 3) {
			$startserver = "./changegame $slot";
		} else {
        		$fp = file("./nwn.config");
        		foreach ($fp as $key => $value) {
                		$a = $value;
                		$a = substr($a, 0, -1);
                		$Option[$key] = $a;
        		}
               		$playerpass = "$Option[12]";
               		$startserver = "./startserverloadsave $Option[0] $Option[1] $Option[2] $Option[3] $Option[4] $Option[5] $Option[6] $Option[7] $Option[8] $Option[9] $Option[10] $Option[11] $Option[12] $Option[13] $Option[14] \"$Option[15]\" $Option[16] $Option[17] $Option[18] $slot \"$nwserverdir\"";
		}
        	exec($startserver);
		echo "<META HTTP-EQUIV=Refresh content=0;URL=index.php>";
                $fp = fopen("./currentmod", w);
                fwrite($fp, "$startserverwith\n");
                fclose($fp);
	} else {
		echo "Nice Try, but you do not have access to this section!";
        }
 }

//##############//
//Save Game Form//
//##############//

 if($item == "save") {
	global $authuser, $adminusers, $savegame;
	if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $savegame))) {
        	echo "Enter a number to save as, The list on the left are existing slot numbers:<br>";
		echo "<b>If you select an existing slot number, and you are an administrator, then it will overwrite that slot.</b><br>";
		echo "If you are not an administrator, then you must select an empty slot.<br>";
		echo "If you click a link on the left, it will automatically fill in the Slot # and Name.<br><br>";
        	echo "<table width=100%><tr><td>";
        	$Query = "$lsdir \"$nwserverdir/saves\"";
		unset ($Result);
        	exec ($Query, $Result, $ReturnValue);
        	reset ($Result);
        	while (list($key,$val) = each($Result))
        	{
                	$val2 = eregi_replace(" - .*", "", $val);
                	$val2 = substr($val2,4,2);
			$val3 = substr($val,9,strlen($val));
                	print "<font color=ffffff><a href=\"javascript:fillin('$val2','$val3')\">$val2 - $val3</a></font><BR>";
        	}
        	echo ("
        	</td>
        	<td valign=top>
        	<form name=save method=post action=index.php>
        	<input type=hidden name=item value=savenow>
        	&nbsp; &nbsp; Slot# <input type=text name=slot size=2 maxlength=2> Short Name For the Save
        	<input type=text name=\"slotname\" maxlength=25><br>
        	<br>&nbsp; &nbsp; <input type=submit> &nbsp; &nbsp; <input type=button value=Clear onclick=\"clearform();\"></form>
  		<script>
                var saveslot, saveslotname;
                function fillin(saveslot, saveslotname) {
                        document.save.slot.value = saveslot;
                        document.save.slotname.value = saveslotname;
                }
                function clearform() {
                        document.save.slot.value = \"\";
                        document.save.slotname.value = \"\";
                }
                </script>
	      	</td>
        	</tr></table>
        	");
	} else {
		echo "Nice Try, but you do not have access to this section!";
        }
 }

//#########//
//Save Game//
//#########//

 if($item == "savenow") {
	global $authuser, $adminusers, $savegame;
        if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $savegame))) {
        	$slot = eregi_replace("[^0-9]", "", $slot);
        	$slotname = eregi_replace("[^0-9a-z ]", "", $slotname);
		if ($slot == "01") {
			echo "You cannot overwrite the AutoSave Slot";
			exit();
		} else {
			if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE")) {
				$save = "./forcesave $slot \"$slotname\"";
				unset ($saveResult);
				exec ($save,$saveResult);
				if (preg_grep ("/Server: Save complete/i", $saveResult)) {
					echo "Server: Save complete";
				} else {
					echo "<font color=ffff00>Server: Unable to Save Game!</font>";
				}
			} else {
        			$save = "./save $slot \"$slotname\"";
				unset ($saveResult);
        			exec ($save,$saveResult);
				if (preg_grep ("/Server: Save complete/i", $saveResult)) {
					echo "Server: Save complete";
				}
				if (preg_grep ("/Server: Specified slot is in use/i", $saveResult)) {
					echo "<font color=ffff00>Server: Specified slot is in use</font>"; 
				}
				if ((!preg_grep ("/Server: Specified slot is in use/i", $saveResult)) & (!preg_grep ("/Server: Save complete/i", $saveResult))) {
					echo "<font color=ffff00>Server: Unable to Save Game!</font>";
				}
			}
		}
		echo "<META HTTP-EQUIV=Refresh content=2;URL=index.php?item=status>";
	} else {
		echo "Nice Try, but you do not have access to this section!";
        }
 }


//#####################//
//Delete Save Game Form//
//#####################//

 if($item == "deletemod") {
	global $authuser, $adminusers, $deletegame;
	if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $deletegame))) {
        	echo "Choose a slot to delete<br><br>";
        	$Query = "$lsdir \"$nwserverdir/saves\"";
		unset ($Result);
        	exec ($Query, $Result, $ReturnValue);
        	reset ($Result);
        	while (list($key,$val) = each($Result))
        	{
                	$val2 = eregi_replace(" - .*", "", $val);
                	$val2 = substr($val,4,2);
                	print "<font color=ffffff><a href=\"index.php?item=deleteslot&slot=$val\">$val</a></font><BR>";
        	}
	} else {
		echo "Nice Try, but you do not have access to this section!";
        }
 }

//##############################//
//Delete Save Game Confirmation//
//##############################//

 if($item == "deleteslot") {
	global $authuser, $adminusers, $deletegame;
        if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $deletegame))) {
        	if ($slot != "000001 - Auto Save") {
                	echo "Are you sure you want to delete $slot?<br><br>";
                	echo "<a href=\"index.php?item=deletenow&slot=$slot\">YES</a>&nbsp; &nbsp;<a href=index.php?item=>NO</a>";
        	} else {
                	echo "You cannot delete $slot";
        		exit();
        	}
	} else {
		echo "Nice Try, but you do not have access to this section!";
        }
 }

//################//
//Delete Save Game//
//################//

 if($item == "deletenow") {
	global $authuser, $adminusers, $deletegame;
        if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $deletegame))) {
        	$deletefile = "rm -fr \"$nwserverdir/saves/$slot/\"";
        	exec ($deletefile);
        	echo "$slot Deleted<br><br>";
		echo "<META HTTP-EQUIV=Refresh content=2;URL=index.php?item=deletemod>";
	} else {
		echo "Nice Try, but you do not have access to this section!";
        }
 }

//##############################//
//Send a message to players Form//
//##############################//

 if($item == "say") {
	global $authuser, $adminusers, $smessage;
	if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $smessage))) {
        	echo "Send Server Message:<br>";
        	echo "<form name=say method=post action=index.php>";
        	echo "<input type=hidden name=item value=sayit>";
        	echo "<input type=text name=msg><br>";
        	echo "<input type=submit></form>";
	} else {
		echo "Nice Try, but you do not have access to this section!";
        }
 }

//#########################//
//Send a message to players//
//#########################//

 if($item == "sayit") {
	global $authuser, $adminusers, $smessage;
        if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $smessage))) {
        	$msg = eregi_replace("[^0-9a-z ]", "", $msg);
        	$say = "./say \"$msg\"";
        	echo exec($say);
		echo "<META HTTP-EQUIV=Refresh content=2;URL=index.php?item=status>";
	} else {
		echo "Nice Try, but you do not have access to this section!";
        }
 }

//########//
//Ban Info//
//########//

 if($item == "baninfo") {
	global $authuser, $adminusers, $ban;
	if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $ban))) {
		if($procnum < 3) {
			echo "<h2>NWServer Not Running!</h2>";
			exit();
		}
		echo "<h2>Manually Ban User/IP/CDKEY</h2>";
		echo "<TABLE width=50% fgcolor=ffff00><TR>";
		echo "<form name=banman method=post action=index.php>";
		echo "<input type=hidden name=item value=banmanchange>";
		echo "<TD><input type=text name=bandata size=25 maxlength=35></TD><TD><b>USER</b><input type=RADIO name=bantype value=name CHECKED></TD><TD><b>IP</b><input type=RADIO name=bantype value=ip></TD><TD><b>CDKEY</b><input type=RADIO name=bantype value=key></TD></TR>";
		echo "<TR><TD><input type=submit value=BAN></form></TD></TR>";
		echo "</TABLE>";
		echo "<hr>";
        	echo "<h2>Ban Info</h2><font color=ffff00>Click an item to unban</font><br><br>";
		$runcmd = "./clear";
		exec ($runcmd);
        	$listbans = "./listbans";
		unset ($listbanResult);
        	exec ($listbans, $listbansResult, $listbansReturnValue);
        	reset ($listbansResult);
        	while (list($key,$val1) = each($listbansResult))
        	{
                	$fp = fopen("./banlist", a);
                	fwrite($fp, "$val1\n");
        	}
        	fclose($fp);
        	$keys = `$awkdir '/CD Keys/{print $3}' ./banlist | tail -n 1`;
        	$ips = `$awkdir '/IP Addresses/{print $3}' ./banlist | tail -n 1`;
        	$names = `$awkdir '/Player Names/{print $3}' ./banlist | tail -n 1`;
        	$tot = $keys + $names + $ips + 4;
        	echo "<b><font color=ffff00>Banned Keys:</font></b><br>";
        	$keylist = "$awkdir '/^[A-Z0-9][A-Z0-9][A-Z0-9][A-Z0-9][A-Z0-9][A-Z0-9][A-Z0-9][A-Z0-9]*/{print}' ./banlist | tail -n $keys";
        	exec ($keylist, $keyResult, $keyReturnValue);
        	reset ($keyResult);
        	while (list($key,$val) = each($keyResult))
        	{
                	$val = eregi_replace(".*detached.*", "", $val);
                	echo ("
                	<a href=\"index.php?item=unban&key=$val\"><font color=00ffff>$val</font></a><br>
                	");
        	}
        	echo "<br><b><font color=ffff00>Banned IPs:</font></b><br>";
        	$iplist = "$awkdir '/[0-9].[0-9].*[0-9].*[0-9]$/{print $1}' ./banlist | tail -n $ips";
       		exec ($iplist, $ipResult, $ipReturnValue);
        	reset ($ipResult);
        	while (list($key,$val) = each($ipResult))
        	{
                	$val = eregi_replace(".*detached.*", "", $val);
                	echo ("
                	<a href=\"index.php?item=unban&ip=$val\"><font color=00ffff>$val</font></a><br>
                	");
        	}
        	echo "<br><b><font color=ffff00>Banned Players:</font></b><br>";
        	$playerlist = "$awkdir '/^[A-Z0-9a-z].*$/{print $0}' ./banlist | tail -n $names";
        	exec ($playerlist, $playerResult, $playerReturnValue);
        	reset ($playerResult);
        	while (list($key,$val) = each($playerResult))
        	{
                	$val = eregi_replace(".*Player.*", "", $val);
                	$val = eregi_replace(".*detached.*", "", $val);
                	echo ("
                	<a href=\"index.php?item=unban&name=$val\"><font color=00ffff>$val</font></a><br>
                	");
        	}
        	@unlink("./banlist");
	} else {
		echo "Nice Try, but you do not have access to this section!";
        }
 }

//###################//
//Ban Manually Change//
//###################//

if($item == "banmanchange"){
	global $authuser, $adminusers, $ban;
	if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $ban))) {
		$runcmd = "";
		if ($bandata == "") {
			echo "<META HTTP-EQUIV=Refresh content=0;URL=index.php?item=baninfo>";
			exit();
		}
		if ($bantype == "ip") {
			$temp = (ereg_replace("[^0-9\.]", "", $bandata));
			if (ereg('^([0-9]{1,3})\.([0-9]{1,3})\.' .'([0-9]{1,3})\.([0-9]{1,3})$', $bandata, $part)) {
    				if ($part[1] <= 255 && $part[2] <= 255 && $part[3] <= 255 && $part[4] <= 255) {
					$runcmd = "./banplayer $bantype \"$bandata\"";
				} else {
					echo "IP Not Valid!";
                                	echo "<META HTTP-EQUIV=Refresh content=1;URL=index.php?item=baninfo>";
                                	exit();
				}
			} else {
				echo "IP Not Valid!";
				echo "<META HTTP-EQUIV=Refresh content=1;URL=index.php?item=baninfo>";
				exit();
			}
		}
		if ($bantype == "name") {
			$runcmd = "./banplayer $bantype \"$bandata\"";
		}
		if ($bantype == "key") {
			if (ereg('^([A-Z0-9]{8})\ ([A-Z0-9]{8})$', $bandata, $part)) { 
				$runcmd = "./banplayer $bantype \"$bandata\"";
			} else {
				echo "Invalid Key";
				echo "<META HTTP-EQUIV=Refresh content=1;URL=index.php?item=baninfo>";
                                exit();
			}
		}
		if (!$runcmd == "") {
			exec($runcmd);
			echo "$bandata Banned from Server!";
		}
                echo "<META HTTP-EQUIV=Refresh content=1;URL=index.php?item=baninfo>";
	} else {
		echo "Nice Try, but you do not have access to this section!";
        }
 }

//##############//
//UnBan a Player//
//##############//

 if($item == "unban") {
	global $authuser, $adminusers, $ban;
        if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $ban))) {
        	if($key == "" && $ip == "" && $name == "") {
                	echo "None Chosen";
        	}
        	if($key) {
                	$unbankey = "./unban key \"$key\"";
                	exec($unbankey);
                	echo "$key unbanned";
			echo "<META HTTP-EQUIV=Refresh content=1;URL=index.php?item=baninfo>";
        	}
        	if($ip) {
                	$unbankey = "./unban ip $ip";
                	exec($unbankey);
                	echo "$ip unbanned";
			echo "<META HTTP-EQUIV=Refresh content=1;URL=index.php?item=baninfo>";
        	}
        	if($name) {
                	$unbankey = "./unban name \"$name\"";
                	exec($unbankey);
                	echo "$name unbanned";
			echo "<META HTTP-EQUIV=Refresh content=1;URL=index.php?item=baninfo>";
        	}
	} else {
		echo "Nice Try, but you do not have access to this section!";
        }
 }

//######//
//Status//
//######//

 if($item == "status") {
	global $authuser, $adminusers, $ban;
  	$mostp = "0";
  	$most = "$grepdir \"Joined as Player\" \"$nwserverdir/logs.0/nwserverLog1.txt\"";
  	exec ($most, $pResult, $pReturnValue);
  	reset ($pResult);
  	while (list($key,$mostp) = each($pResult))
  	{
    		$mostp = eregi_replace(".*Player ","",$mostp); 
    		if($mostp > $mostplayers) {
      			$mostplayers = $mostp;
    		} 
  	}
  	$temp = 0;
  	$starttime = "$grepdir \"Loading\" \"$nwserverdir/logs.0/nwserverLog1.txt\" | awk 'NR == 1{print $1,$2,$3,$4}'";
  	$startedat = exec($starttime);
  	$playeron = "$grepdir -c \"Joined as Player\" \"$nwserverdir/logs.0/nwserverLog1.txt\"";
  	$playerquit = "$grepdir -c \"Left as a Player\" \"$nwserverdir/logs.0/nwserverLog1.txt\"";
  	$playerson = exec($playeron);
  	$playersquit = exec($playerquit);
  	$playertot = $playerson - $playersquit;
  	echo "<b>Server Started = $startedat</b><br>";
  	echo "<b>Players that joined since $startedat = <a href=index.php?item=fulllist>$playerson</a></b><br>";
  	echo "<b>Most Players Online at Once = $mostplayers</b><br>";
  	echo "<b>Players Online Now= $playertot</b><br>";
  	$grepnum = $playertot + 13;
	$runcmd = "$lsdir -d $nwserverdir/* | $grepdir 'logs.[1-9]'";
	unset ($statusResult);
	exec($runcmd, $statusResult, $statusReturnValue);
	reset ($statusResult);
	if (count($statusResult) > 0) {
		echo "<font color=ffff00><center><h2>Log File Error</h2></center><br>";
		echo "<center><h2>Please Save your game, then</h2></center><br>";
		echo "<center><h2>Stop/Start the Server</h2></center></font>";	
		echo "<br><center>Removed player logs from server</center>";
		$runcmd = "$rmdir -fr $nwserverdir/logs.*";
		exec ($runcmd);
		exit();
	}
	$runcmd = "./clear";
	exec($runcmd);
  	$status = "./status | $taildir -n $grepnum";
	unset ($statusResult);
  	exec($status, $statusResult, $statusReturnValue);
  	reset ($statusResult);
  	while (list($key,$val) = each($statusResult))
  	{
    		$fp = fopen("./playerlist", a);
    		fwrite($fp, "$val\n");
  	}
    	fclose($fp);
  	echo "<hr>";
  	$serverstats = "$awkdir 'NR < 11{print $0}' ./playerlist";
	unset ($ssResult);
  	exec ($serverstats, $ssResult, $ssReturnValue);
  	reset ($ssResult);
	if ($procnum >=3) {
		if ((!preg_grep ("/Server Name/i", $ssResult)) || (!preg_grep ("/Maximum Clients/i", $ssResult)) || (!preg_grep ("/Server Port/i", $ssResult)) || (!preg_grep ("/Module Name/i", $ssResult)) || (!preg_grep ("/Module Status/i", $ssResult)) || (!preg_grep ("/PVP/i", $ssResult)) || (!preg_grep ("/Difficulty/i", $ssResult)) || (!preg_grep ("/ELC/i", $ssResult)) || (!preg_grep ("/One Party/i", $ssResult)) || (!preg_grep ("/Reload when Empty/i", $ssResult))) {
			echo "<center><h2>Status <font color=00ffff>NOT</font> Available!</h2></center><br>";
			echo "<center><h2>Reloading Status!</h2></center>";
			@unlink("./playerlist");
			echo "<META HTTP-EQUIV=Refresh content=10;URL=index.php?item=status>";
			exit();
		}
	}
	$temp = $ssResult[3];
	$runningmod = substr($temp, (strpos ($temp,': ') + 2), strlen($temp));
  	while (list($key,$val1) = each($ssResult))
  	{
       		if ($procnum >= 3) {
			if (preg_match ("/Module Name/i", $ssResult[$key])) {
				echo "<br>Module Name: <a href=\"index.php?item=moduleinformation&runningmod=$runningmod\">$runningmod</a>";
			} else {
         			echo "<br>$val1";
			}
		}  
 	}
  	if ($procnum < 3) {
     		echo "<br><br><h2>nwserver is <font color=ff0000>NOT</font> running.</h2>";
  	}
  	echo "<br><br><font color=ffcc33>Click ID, UserName, IP Address, Player Name, or CDKey to Kick/Ban by that field.</font>";
  	echo "<table width=100% bgcolor=222222>";
  	echo "<th align=left>ID</th><th align=left>USERNAME</th><th align=left>IP ADDRESS</th><th align=left>PLAYERNAME</th><th align=left>CDKEY</th>";
  	$playerlist = "$awkdir -F\| '/[0-9].[0-9].*[0-9].*[0-9]/{print $0}' ./playerlist";
	unset ($Result);
  	exec ($playerlist, $Result, $ReturnValue);
  	reset ($Result);
  	while (list($key,$val) = each($Result))
 	{
    		list($id,$user,$ip,$playername,$cdkey)= split ("\|", $val, 5);
    		$id = trim($id);
    		$user = trim($user);
    		$ip = trim($ip);
    		$playername = trim($playername);
    		$cdkey = trim($cdkey);
        	if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $ban))) {
    			echo ("
    			<tr>
    			<td bgcolor=000000><a href=\"index.php?item=kickban&type=key&user=$user&ip=$ip&id=$id&playername=$playername&useforkick=$id&useforban=$cdkey\">$id</a></td>
    			<td bgcolor=000000><a href=\"index.php?item=kickban&type=name&user=$user&ip=$ip&id=$id&playername=$playername&useforkick=$user&useforban=$user\">$user</a></td>
    			<td bgcolor=000000><a href=\"index.php?item=kickban&type=ip&user=$user&ip=$ip&id=$id&playername=$playername&useforkick=$user&useforban=$ip\">$ip</a></td>
    			<td bgcolor=000000><a href=\"index.php?item=kickban&type=name&user=$user&ip=$ip&id=$id&playername=$playername&useforkick=$user&useforban=$user\">$playername</a></td>
    			<td bgcolor=000000><a href=\"index.php?item=kickban&type=key&user=$user&ip=$ip&id=$id&playername=$playername&useforkick=$user&useforban=$cdkey\">$cdkey</a></td>
    			</tr>
    			");
		} else {
			echo ("
                        <tr>
                        <td bgcolor=000000>$id</td>
                        <td bgcolor=000000>$user</td>
                        <td bgcolor=000000>$ip</td>
                        <td bgcolor=000000>$playername</td>
                        <td bgcolor=000000>$cdkey</td>
                        </tr>
                        ");
		}
  	}
  	echo "</table>";
  	if($procnum < 3) {
    		echo "nwserver might not be running or is not responding at this time.";
  	}
	@unlink("./playerlist");
 }

//##################//
//Module Information//
//##################//

if($item == "moduleinformation") {
	echo "<h2>Information for module: <b>$runningmod</b></h2><br>";
	$module = new module;
        $module->loadfromfile("$nwserverdir/modules/$runningmod.mod");
	if ($module->Description[0] == "") { echo "No Description for this module available!"; }
        for ($i=0; $i < count($module->Description);$i++) {
        	$para = eregi_replace("\n","<BR>",$module->Description[$i]);
                echo "$para";
                if ($i+1 !=count($module->Description)) { echo "<br>";}
        }
}
//################//
//Full Player List//
//################//

 if($item == "fulllist") {
  	$fulllist = "$grepdir \"Joined as Player\" \"$nwserverdir/logs.0/nwserverLog1.txt\" | awk '{print $0}'";
  	exec ($fulllist, $fulllistResult, $fulllistReturnValue);
  	reset ($fulllistResult);
  	while (list($key,$val) = each($fulllistResult))
  	{
    		$val = eregi_replace("\.", "", $val);
    		$val = eregi_replace("\[.*\]", "", $val);
    		$val = eregi_replace("Joined as Player.*", "", $val);
    		$val = eregi_replace("^ ", "", $val);
    		$val1 = trim($val);
    		$val1 = eregi_replace("\'", ".*", $val1);
    		$val1 = eregi_replace("\ ", ".*", $val1);
    		echo "<font color=ffffff><a href=\"index.php?item=usertime&player=$val1\">$val</a></font><BR>";
  	}
 }

//#########//
//User Time//
//#########//

 if($item == "usertime") {
  	$fulllist = "$grepdir -i \"$player\" \"$nwserverdir/logs.0/nwserverLog1.txt\" | awk '{print $0}'";
  	exec ($fulllist, $fulllistResult, $fulllistReturnValue);
  	reset ($fulllistResult);
  	while (list($key,$val) = each($fulllistResult))
  	{
    		$val = eregi_replace("\.", "", $val);
    		echo "<font color=ffffff>$val</font><BR>";
  	}
 }

//###########//
//Kick or Ban//
//###########//

 if($item == "kickban") {
	global $authuser, $adminusers, $ban;
        if ((preg_grep ("/^$authuser$/i", $adminusers)) || ($adminusers[0] == "NONE") || (preg_grep ("/^$authuser$/i", $ban))) {
  		if($kickban == "") { 
    			echo "<h2>What would you like to do with this player?</h2>";
			echo "<b>Client Information:</b>";
                        $clientinfo = "./clientinfo \"$useforkick\" | tail -n 5";
                        exec ($clientinfo, $clientResult, $clientReturnValue);
                        reset ($clientResult);
                        while (list($key,$val) = each($clientResult))
                        {
				$temp = split(":", $clientResult[$key]);
                                echo "<br>$temp[0]:<font color=ffff00>$temp[1]</font>";
                        }
			echo "<br><br>";
			echo "<table width=25%><TR>";
    			echo "<TD><h2><a href=\"index.php?item=kickban&kickban=kick&tokick=$playername&useforkick=$useforkick\">Kick</a></h2></TD>";
    			echo "<TD><h2><a href=\"index.php?item=kickban&kickban=ban&type=$type&toban=$useforban&useforban=$useforban&useforkick=$useforkick\">Ban</a></h2></TD>";
			echo "</TR></TABLE>";
  		}
  		if($kickban == "kick") {
    			$kickplayer = "./kickplayer \"$useforkick\"";
    			exec($kickplayer);
    			echo "$tokick has been kicked off server!";
			echo "<META HTTP-EQUIV=Refresh content=2;URL=index.php?item=status>";
  		}
  		if($kickban == "ban") {
    			$banplayer = "./banplayer $type \"$useforban\"";
    			exec($banplayer);
    			$kickplayer = "./kickplayer \"$useforkick\"";
    			exec($kickplayer);
    			echo "$toban has been kicked and banned, \"Used ban$type $toban\"";
			echo "<META HTTP-EQUIV=Refresh content=2;URL=index.php?item=status>";
  		}
	} else {
		echo "Nice Try, but you do not have access to this section!";
        }
 }


echo ("
</TD></TR>
<!-- END BODY -->
</TABLE>
</TR></TD></TABLE>
</CENTER>
</BODY>
</HTML>
");
?>
