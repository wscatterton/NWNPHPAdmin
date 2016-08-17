<?
/*
    File Name			: module.php
    Author 				: zoligato (zoligato@hotmail.com)
    Creation Date    	: 17/07/2002
    Version	 			: V0.1
    Date Modification	: 21/07/2002
    Description			: read and extract mod/hak/erf file   	
*/

class module {

	var  $FileName,     /* module file name */
	     $FileSize,     /* Size of the file	 */
	     $NumRes,   	/* num resources in mod file */
	     $FileType,     /* File type : "ERF" or "MOD" or "HAK" */
	     $FileVersion,	/* Version of file */
	     $Description,	/* description only for MOD and HAK */
	     $URL,          /* url only for HAK */
	     $Title;        /* url only for HAK */
		
	var $DataOffset,	/* pointer to resource data */
	    $Resources;		/* resources list */

	/* Main Function */
	function Module() 
	{
		/* init variable */
		$this->FileName 	= "";
		$this->FileType 	= "";
		$this->FileFersion 	= "";
				
		$this->FileSize   = 0;
		$this->DataOffset = 0;

		$this->NumRes 	 = 0;
		$this->Resources = array();
	} /* End function Module */

	/* Load MOD HAK or ERF File return true or false*/
	function LoadFromFile($a_filename) 
	{
		$l_filename = $a_filename;
		
		if ($l_filename == "") 
		{
			return(False);
			exit;			
		} /* end if $a_filename not empty */
		
		/* file exist ? */
		if (!file_exists($l_filename))
		{
			return(False);
			exit;
		} /* end if exists */

		/* can read it ? */
 		if (!is_readable($l_filename))
		{
			return(False);
			exit;
		} /* end if readable */

		$this->FileSize = filesize($l_filename);
		$this->FileName = $l_filename;

		/* opening file in binray read mode */ 
		$file = fopen($l_filename, "rb");

		/* can open it ? */
		if (!$file)
		{
			return(False);
			exit;
		} /* end if !$file */

    	fseek($file,0);

		/* reading File Header */
		$header[Signature]  = fread($file,4);
		$header[Version] 	= fread($file,4);
		$header[Const1]  	= $this->bin2int(fread($file,4)); /* 0x01000000  = 1*/
		$header[Unknow1] 	= $this->bin2int(fread($file,4));
		$header[NumRes]  	= $this->bin2int(fread($file,4));
		$header[Const2]  	= $this->bin2int(fread($file,4)); /* 0xA0000000 = 160 */
		$header[EntrieOffset]	= $this->bin2int(fread($file,4));
		$header[FileEntrie]  	= $this->bin2int(fread($file,4));
		$header[ResData] 	= $this->bin2int(fread($file,4));
		$header[Const3]  	= $this->bin2int(fread($file,4)); /* 0x660000000 = 102 */
		$header[Const4]   	= $this->bin2int(fread($file,4)); /* 0x000000000 = 0 */
		$header[Const5]  	= $this->bin2int(fread($file,4)); /* 0xFFFFFFFF */
		$header[Vide]	    = fread($file,116);

		if (($header[ResData] <=0) || ($header[NumRes] <= 0) || (($header[Signature] != 'MOD ') && ($header[Signature] != 'HAK ') && ($header[Signature] != 'ERF ')))
		{
	   		return(FALSE);
			fclose($file);			
	   		exit;
		} /* end if numres < 0 or resdata <0 */

    	$this->FileType    = str_replace(' ','',$header[Signature]);
    	$this->FileVersion = $header[Version];
    	$this->DataOffset  = $header[ResData];
    	$this->NumRes	   = $header[NumRes];

		// if file type is ERF no URL,TITLE,DESCRIPTION
		// if file type is MOD only a description
		// if file type is HAK we have URL Title and description
 		if ($this->FileType != 'ERF')
		{
      		fseek($file,164);
  		}

		/* reading MOD Description */
    	if ($this->FileType == 'MOD')
    	{
			fseek($file,160);
			$offset = 160;
			$tmp 	= $this->bin2int(fread($file,4));
			
			if ($tmp == 0) {
				$DesLength = $this->bin2int(fread($file,4));
				$this->Description[] = fread($file,$DesLength); 
			}/* end if tmp = 0 */
			else 
			{
				fseek($file,160);
				$offset = 160;			
		  		while (	$offset < $header[EntrieOffset]) 
				{
					$DesLength 	  = 0;
					$DesLanguage  = 0;
					$DesLanguage  = $this->bin2int(fread($file,4));
					$DesLength    = $this->bin2int(fread($file,4));
	   	   			$this->Description[] = fread($file,$DesLength); 
					$offset = $offset + 8 + $DesLength;
				}  /*end While in description */
			} /* end else == 0 */
    	} /* end if = MOD */

		/* reading HAK file Description URL Title */
		if ($this->FileType == 'HAK')
    	{
	  		$length = $this->bin2int(fread($file,4));
      		$fileinfo = fread($file,$length);
			list( $this->Title, $this->URL, $this->Description ) = split( chr(10), $fileinfo );
			if ((strlen($this->Title) + strlen($this->URL) + strlen($this->Description) + 2) != strlen($fileinfo)) 
			{
				$pos = strlen($this->Title) + strlen($this->URL) + 2;
				$length = strlen($fileinfo) - $pos; 
				$this->Description[] = substr($fileinfo,$pos,$length);
			
			} /* end if length < */
    	} /* end if = HAK */

		/* reading general resource info */
    	/*fseek($file,$header[EntrieOffset]);
    	for ($i =0; $i < $header[NumRes]; $i++)
    	{
			$this->Resources[$i][ResName]    = str_replace(chr(00),'',fread($file,16));
      		$this->Resources[$i][Index]      = $this->bin2int(fread($file,4));
      		$this->Resources[$i][Type]       = strtoupper($this->extension_id2char($this->bin2int(fread($file,4))));
      		$this->Resources[$i][DataOffset] = 0;
      		$this->Resources[$i][FileLength] = 0;
    	}*/ /* end for Numres */

		/* reading resource data info */
/*    	fseek($file,$header[FileEntrie]);
    	for ($i = 0; $i < $header[NumRes]; $i++)
    	{
      		$this->Resources[$i][ResDataOffset] = $this->bin2int(fread($file,4));
      		$this->Resources[$i][FileLength] = $this->bin2int(fread($file,4));
    	}*/ /* end for Numres */
		
		/* closing file */
		fclose($file);

		return(true);
	} /* End function loadfromfile */

	/* Get extension by id retrun -1 if not found*/
	function extension_id2char($a_extensionid)
	{
		switch($a_extensionid) 
		{
 			case 2063   : return('bik');
						  break;
      		case 2061   : return('ssf');
						  break;
      		case 2060	: return('utw');
						  break;
      		case 2059	: return('4pc');
						  break;
      		case 2056	: return('jrl');
						  break;
      		case 2055	: return('utg');
						  break;
      		case 2054	: return('btg');
						  break;
      		case 2053	: return('pwk');
						  break;
      		case 2052	: return('dwk');
						  break;
      		case 2051	: return('utm');
						  break;
      		case 2050	: return('btm');
						  break;
      		case 2049	: return('ccs');
						  break;
      		case 2048	: return('css');
						  break;
      		case 2047	: return('gui');
						  break;
      		case 2046	: return('gic');
						  break;
      		case 2045	: return('dft');
						  break;
      		case 2044	: return('utp');
						  break;
      		case 2043	: return('btp');
						  break;
      		case 2042	: return('utd');
						  break;
      		case 2041	: return('btd');
						  break;
      		case 2040	: return('ute');
						  break;
      		case 2039	: return('bte');
						  break;
      		case 2038	: return('fac');
						  break;
      		case 2037	: return('gff');
						  break;
      		case 2036	: return('ltr');
						  break;
      		case 2035	: return('uts');
						  break;
      		case 2034	: return('bts');
						  break;
      		case 2033	: return('dds');
						  break;
      		case 2030	: return('itp');
						  break;
      		case 2029	: return('dlg');
						  break;
      		case 2023	: return('git');
						  break;
      		case 2032	: return('utt');
						  break;
      		case 2031	: return('btt');
						  break;
      		case 2027	: return('utc');
						  break;
      		case 2026	: return('btc');
						  break;
      		case 2025	: return('uti');
						  break;
      		case 2024	: return('bti');
						  break;
      		case 9	    : return('mpg');
						  break;
      		case 2018	: return('tlk');
						  break;
      		case 2017	: return('2da');
						  break;
      		case 2005	: return('fnt');
						  break;
      		case 6	    : return('plt');
						  break;
      		case 2016	: return('wok');
						  break;
      		case 2015	: return('bic');
						  break;
      		case 2014	: return('ifo');
						  break;
      		case 2013	: return('set');
						  break;
      		case 2012	: return('are');
						  break;
      		case 2010	: return('ncs');
						  break;
      		case 2009	: return('nss');
						  break;
      		case 2008	: return('slt');
						  break;
      		case 2003	: return('thg');
						  break;
      		case 2007	: return('lua');
						  break;
      		case 2002	: return('mdl');
						  break;
      		case 2001	: return('tex');
						  break;
      		case 2000	: return('plh');
						  break;
      		case 9998	: return('bif');
						  break;
      		case 9999	: return('key');
						  break;
      		case 2022	: return('txi');
						  break;
      		case 10		: return('txt');
						  break;
      		case 7	    : return('ini');
						  break;
      		case 4	    : return('wav');
						  break;
      		case 3	    : return('tga');
						  break;
      		case 2	    : return('mve');
						  break;
      		case 1	    : return('bmp');
						  break;
      		case 0	    : return('res');		
						  break;
			default		: return(-1);
		} /* end switch id */
	} /* End function extension_id2char */

	/* Get extension by name without point return -1 if not found  */
	function extension_char2id($a_extension)
	{
		$l_extension = strtolower($a_extension);
		
		switch($l_extension) 
		{
 			case 'bik'  : return(2063);
						  break;
      		case 'ssf'  : return(2061);
						  break;
      		case '4pc'	: return(2059);
						  break;
      		case 'utw'	: return(2060);
						  break;
      		case 'jrl'	: return(2056);
						  break;
      		case 'utg'	: return(2055);
						  break;
      		case 'btg'	: return(2054);
						  break;
      		case 'pwk'	: return(2053);
						  break;
      		case 'dwk'	: return(2052);
						  break;
      		case 'utm'	: return(2051);
						  break;
      		case 'btm'	: return(2050);
						  break;
      		case 'ccs'	: return(2049);
						  break;
      		case 'css'	: return(2048);
						  break;
      		case 'gui'	: return(2047);
						  break;
      		case 'gic'	: return(2046);
						  break;
      		case 'dft'	: return(2045);
						  break;
      		case 'utp'	: return(2044);
						  break;
      		case 'btp'	: return(2043);
						  break;
      		case 'utd'	: return(2042);
						  break;
      		case 'btd'	: return(2041);
						  break;
      		case 'ute'	: return(2040);
						  break;
      		case 'bte'	: return(2039);
						  break;
      		case 'fac'	: return(2038);
						  break;
      		case 'gff'	: return(2037);
						  break;
      		case 'ltr'	: return(2036);
						  break;
      		case 'uts'	: return(2035);
						  break;
      		case 'bts'	: return(2034);
						  break;
      		case 'dds'	: return(2033);
						  break;
      		case 'itp'	: return(2030);
						  break;
      		case 'dlg'	: return(2029);
						  break;
      		case 'git'	: return(2023);
						  break;
      		case 'utt'	: return(2032);
						  break;
      		case 'btt'	: return(2031);
						  break;
      		case 'utc'	: return(2027);
						  break;
      		case 'btc'	: return(2026);
						  break;
      		case 'uti'	: return(2025);
						  break;
      		case 'bti'	: return(2024);
						  break;
      		case 'mpg'	: return(9);
						  break;
      		case 'tlk'	: return(2018);
						  break;
      		case '2da'	: return(2017);
						  break;
      		case 'fnt'	: return(2005);
						  break;
      		case 'plt'  : return(6);
						  break;
      		case 'wok'	: return(2016);
						  break;
      		case 'bic'	: return(2015);
						  break;
      		case 'ifo'	: return(2014);
						  break;
      		case 'set'	: return(2013);
						  break;
      		case 'are'	: return(2012);
						  break;
      		case 'ncs'	: return(2010);
						  break;
      		case 'nss'	: return(2009);
						  break;
      		case 'slt'	: return(2008);
						  break;
      		case 'thg'	: return(2003);
						  break;
      		case 'lua'	: return(2007);
						  break;
      		case 'mdl'	: return(2002);
						  break;
      		case 'tex'	: return(2001);
						  break;
      		case 'plh'	: return(2000);
						  break;
      		case 'bif'	: return(9998);
						  break;
      		case 'key'	: return(9999);
						  break;
      		case 'txi'	: return(2022);
						  break;
      		case 'txt'  : return(10);
						  break;
      		case 'ini'  : return(7);
						  break;
      		case 'wav'  : return(4);
						  break;
      		case 'tga'  : return(3);
						  break;
      		case 'mve'  : return(2);
						  break;
      		case 'bmp'  : return(1);
						  break;
      		case 'res'  : return(0);		
						  break;
			default		: return(-1);
		} /* end switch extension */
	} /* End function extension_char2id */

	/* convert hex to int */
	function bin2int($value)
	{
		$x = 1;
		$tmp = 0;
		for ($i = 0; $i < strlen($value);$i++)
		{
			$tmp = $tmp + ($x * ord($value{$i}));
			$x = $x *256;
		
		}
		return($tmp);
	} /* end function bin2int */

	/* extract a file return true or false*/
	function extractfile($a_file_id,$a_destination) 
	{
		if (($a_file_id < 0) || ($a_file_id > $this->NumRes)) 
		{
			return(false);
			exit;
		}
		
		$dest_filename = $a_destination.$this->Resources[$a_file_id][ResName].".".$this->Resources[$a_file_id][Type];
		$file = fopen($this->FileName, "rb");
		fseek($file, $this->Resources[$a_file_id][ResDataOffset]);
		$filedest = fopen($dest_filename, "w");
		fwrite($filedest,fread($file,$this->Resources[$a_file_id][FileLength]),$this->Resources[$a_file_id][FileLength]);
		fclose($filedest);
		fclose($file);
		return(true);
	} /* End function extractfile */

	/* extract all file return true or false*/
	function extractallfile($a_destination) 
	{
		for ($i =0; ($i < $this->NumRes); $i++)
		{
			$dest_filename = $a_destination.$this->Resources[$i][ResName].".".$this->Resources[$i][Type];
			$file = fopen($this->FileName, "rb");
			fseek($file, $this->Resources[$i][ResDataOffset]);
			$filedest = fopen($dest_filename, "w");
			fwrite($filedest,fread($file,$this->Resources[$i][FileLength]),$this->Resources[$i][FileLength]);
			fclose($filedest);
			fclose($file);
		}
		return(true);
	} /* End function extractallfile */

	/* extract a file to a variable return an empty string if id not good*/
	function extractfiletostring($a_fileID) 
	{
		if (($a_file_id < 0) || ($a_file_id > $this->NumRes)) 
		{
			return(false);
			exit;
		}
		
		$dest_filename = $a_destination.$this->Resources[$a_file_id][ResName].".".$this->Resources[$a_file_id][Type];
		$file = fopen($this->FileName, "rb");
		fseek($file, $this->Resources[$a_file_id][ResDataOffset]);
		$filedest = fopen($dest_filename, "w");
		return(fread($file,$this->Resources[$a_file_id][FileLength]));
		fclose($file);
	}  /* End function extracfiletostring*/

	/* close the file */
	function destructor()
	{
		unset($Resources,$FileName,$FileType,$FileSize,$NumRes,$FileVersion,$Description,$DataOffset);
	} /* End function close */
} /* End class module */


?>
