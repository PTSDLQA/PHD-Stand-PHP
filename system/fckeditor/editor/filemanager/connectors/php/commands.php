<?php
/*
 * FCKeditor - The text editor for Internet - http://www.fckeditor.net
 * Copyright (C) 2003-2008 Frederico Caldeira Knabben
 *
 * == BEGIN LICENSE ==
 *
 * Licensed under the terms of any of the following licenses at your
 * choice:
 *
 *  - GNU General Public License Version 2 or later (the "GPL")
 *    http://www.gnu.org/licenses/gpl.html
 *
 *  - GNU Lesser General Public License Version 2.1 or later (the "LGPL")
 *    http://www.gnu.org/licenses/lgpl.html
 *
 *  - Mozilla Public License Version 1.1 or later (the "MPL")
 *    http://www.mozilla.org/MPL/MPL-1.1.html
 *
 * == END LICENSE ==
 *
 * This is the File Manager Connector for PHP.
 */


function GetFolders( $resourceType, $currentFolder )
{
	// Map the virtual path to the local server path.
	$sServerDir = ServerMapFolder( $resourceType, $currentFolder, 'GetFolders' ) ;

	// Array that will hold the folders names.
	$aFolders	= array() ;

	$oCurrentFolder = opendir( $sServerDir ) ;

	while ( $sFile = readdir( $oCurrentFolder ) )
	{
		if ( $sFile != '.' && $sFile != '..' && is_dir( $sServerDir . $sFile ) )
			$aFolders[] = '<Folder name="' . ConvertToXmlAttribute( $sFile ) . '" />' ;
	}

	closedir( $oCurrentFolder ) ;

	// Open the "Folders" node.
	echo "<Folders>" ;

	natcasesort( $aFolders ) ;
	foreach ( $aFolders as $sFolder )
		echo $sFolder ;

	// Close the "Folders" node.
	echo "</Folders>" ;
}

function GetFoldersAndFiles( $resourceType, $currentFolder )
{
	global $Config;

	// Map the virtual path to the local server path.
	$sServerDir = ServerMapFolder( $resourceType, $currentFolder, 'GetFoldersAndFiles' ) ;
	$sCurrentPath = GetUrlFromPath( $resourceType, $currentFolder, 'GetFoldersAndFiles' );

	// Arrays that will hold the folders and files names.
	$aFolders	= array() ;
	$aFiles		= array() ;

	$oCurrentFolder = opendir( $sServerDir ) ;

	while ( $sFile = readdir( $oCurrentFolder ) )
	{
		if ( $sFile != '.' && $sFile != '..' )
		{
			if ( is_dir( $sServerDir . $sFile ) )
				$aFolders[] = '<Folder name="' . ConvertToXmlAttribute( $sFile ) . '" size="'. filemanager_dirsize($sServerDir.$sFile) .'"/>' ;
			else
			{
				$iFileSize = @filesize( $sServerDir . $sFile ) ;
				if ( !$iFileSize ) {
					$iFileSize = 0 ;
				}
				if ( $iFileSize > 0 )
				{
					$iFileSize = filemanager_size($iFileSize);
				}
				if ($resourceType=='Image' && $Config['ThumbList']) {
					$t='/image.php?src='.urlencode($sServerDir.$sFile).'&x=100&y=100&b=3';
					list($w, $h) = getimagesize($sServerDir.$sFile);
					$add = 'thumb="' . ConvertToXmlAttribute($t) . '" width="'.$w.'" height="'.$h.'"';
				} else {
					$add = '';
				}

				$aFiles[] = '<File name="' . ConvertToXmlAttribute( $sFile ) . '" size="' . $iFileSize . '" '.$add.'/>' ;
			}
		}
	}

	// Send the folders
	natcasesort( $aFolders ) ;
	echo '<Folders>' ;

	foreach ( $aFolders as $sFolder )
		echo $sFolder ;

	echo '</Folders>' ;

	// Send the files
	natcasesort( $aFiles ) ;
	echo '<Files>' ;

	foreach ( $aFiles as $sFiles )
		echo $sFiles ;

	echo '</Files>' ;
}

function CreateFolder( $resourceType, $currentFolder )
{
	if (!isset($_GET)) {
		global $_GET;
	}
	$sErrorNumber	= '0' ;
	$sErrorMsg		= '' ;

	if ( isset( $_GET['NewFolderName'] ) )
	{
		$sNewFolderName = translit($_GET['NewFolderName']) ;
		$sNewFolderName = SanitizeFolderName( $sNewFolderName ) ;

		if ( mb_strpos( $sNewFolderName, '..' ) !== FALSE )
			$sErrorNumber = '102' ;		// Invalid folder name.
		else
		{
			// Map the virtual path to the local server path of the current folder.
			$sServerDir = ServerMapFolder( $resourceType, $currentFolder, 'CreateFolder' ) ;

			if ( is_writable( $sServerDir ) )
			{
				$sServerDir .= $sNewFolderName ;

				$sErrorMsg = CreateServerFolder( $sServerDir ) ;

				switch ( $sErrorMsg )
				{
					case '' :
						$sErrorNumber = '0' ;
						break ;
					case 'Invalid argument' :
					case 'No such file or directory' :
						$sErrorNumber = '102' ;		// Path too long.
						break ;
					default :
						$sErrorNumber = '110' ;
						break ;
				}
			}
			else
				$sErrorNumber = '103' ;
		}
	}
	else
		$sErrorNumber = '102' ;

	// Create the "Error" node.
	echo '<Error number="' . $sErrorNumber . '" originalDescription="' . ConvertToXmlAttribute( $sErrorMsg ) . '" />' ;
}

function FileUpload( $resourceType, $currentFolder, $sCommand )
{
	if (!isset($_FILES)) {
		global $_FILES;
	}
	$sErrorNumber = '0' ;
	$sFileName = '' ;

	if ( isset( $_FILES['NewFile'] ) && !is_null( $_FILES['NewFile']['tmp_name'] ) )
	{
		global $Config ;

		$oFile = $_FILES['NewFile'] ;

		// Map the virtual path to the local server path.
		$sServerDir = ServerMapFolder( $resourceType, $currentFolder, $sCommand ) ;


		// Get the uploaded file name.
		$sFileName = filemanager_translit($oFile['name']);
		$sFileName = SanitizeFileName( $sFileName ) ;

		$sOriginalFileName = $sFileName ;

		// Get the extension.
		$sExtension = mb_substr( $sFileName, ( strrpos($sFileName, '.') + 1 ) ) ;
		$sExtension = mb_strtolower( $sExtension ) ;

		if ( isset( $Config['SecureImageUploads'] ) )
		{
			if ( ( $isImageValid = IsImageValid( $oFile['tmp_name'], $sExtension ) ) === false )
			{
				$sErrorNumber = '202' ;
			}
		}

		if ( isset( $Config['HtmlExtensions'] ) )
		{
			if ( !IsHtmlExtension( $sExtension, $Config['HtmlExtensions'] ) &&
				( $detectHtml = DetectHtml( $oFile['tmp_name'] ) ) === true )
			{
				$sErrorNumber = '202' ;
			}
		}

		// Check if it is an allowed extension.
		if ( !$sErrorNumber && IsAllowedExt( $sExtension, $resourceType ) )
		{
			$iCounter = 0 ;

			while ( true )
			{
				$sFilePath = $sServerDir . $sFileName ;

				if ( is_file( $sFilePath ) )
				{
					$iCounter++ ;
					$sFileName = RemoveExtension( $sOriginalFileName ) . '(' . $iCounter . ').' . $sExtension ;
					$sErrorNumber = '201' ;
				}
				else
				{
					move_uploaded_file( $oFile['tmp_name'], $sFilePath ) ;

					if ( is_file( $sFilePath ) )
					{
						if ( isset( $Config['ChmodOnUpload'] ) && !$Config['ChmodOnUpload'] )
						{
							break ;
						}

						$permissions = 0777;

						if ( isset( $Config['ChmodOnUpload'] ) && $Config['ChmodOnUpload'] )
						{
							$permissions = $Config['ChmodOnUpload'] ;
						}

						$oldumask = umask(0) ;
						chmod( $sFilePath, $permissions ) ;
						umask( $oldumask ) ;
					}

					break ;
				}
			}

			if ( file_exists( $sFilePath ) )
			{
				//previous checks failed, try once again
				if ( isset( $isImageValid ) && $isImageValid === -1 && IsImageValid( $sFilePath, $sExtension ) === false )
				{
					@unlink( $sFilePath ) ;
					$sErrorNumber = '202' ;
				}
				else if ( isset( $detectHtml ) && $detectHtml === -1 && DetectHtml( $sFilePath ) === true )
				{
					@unlink( $sFilePath ) ;
					$sErrorNumber = '202' ;
				}
			}
		}
		else
			$sErrorNumber = '202' ;
	}
	else
		$sErrorNumber = '202' ;


	$sFileUrl = CombinePaths( GetResourceTypePath( $resourceType, $sCommand ) , $currentFolder ) ;
	$sFileUrl = CombinePaths( $sFileUrl, $sFileName ) ;

	SendUploadResults( $sErrorNumber, $sFileUrl, $sFileName ) ;

	exit ;
}

// SergiusD add

function FileDelete($resourceType, $currentFolder, $Command) {
	global $Config;
	if ($resourceType=='Image' && $Config['ThumbList']) {
		@unlink(CombinePaths($_SERVER['DOCUMENT_ROOT'].GetResourceTypePath('ImageThumb', $Command), filemanager_getthumbname($currentFolder.$_GET['DelFile'])));
	}
	$sServerDir = ServerMapFolder( $resourceType, $currentFolder, $Command ) ;
	if (!unlink($sServerDir.$_GET['DelFile']))
		echo '<Error number="1" originalDescription="Ошибка при удалении файла" />' ;
}

function FolderDelete($resourceType, $currentFolder, $Command) {
	global $Config;
	$thumb = 1;//($resourceType=='Image' && $Config['ThumbList']) ? true : false;
	$sServerDir = ServerMapFolder( $resourceType, $currentFolder, $Command ) ;
	if (
		!filemanager_deldir($_SERVER['DOCUMENT_ROOT'].GetResourceTypePath($resourceType, 'Delete'),
			$currentFolder.$_GET['DelFolder'].'/', $thumb)
		|| !rmdir($sServerDir.$_GET['DelFolder'].'/')
	)
		echo '<Error number="1" originalDescription="Ошибка при удалении папки" />' ;
}

function filemanager_dirsize($dir,$size=0) {
	$hdl=opendir($dir);
	while (false !== ($file = readdir($hdl))) {
		if (($file != ".") && ($file != "..")) {
			if (is_dir($dir."/".$file)) {
				return filemanager_dirsize($dir."/".$file,$size);
			} else {
				$size += filesize($dir."/".$file);
			}
		}
	}
	closedir($hdl);
	return filemanager_size($size);
}
function filemanager_size($size) {
	if ($size < 1024)
		return $size.' Б';
	elseif ($size < 1048576)
		return ceil($size/1024)." КБ";
	else
		return round($size/1048576)." МБ";
}

function translit($string)
{ static $trans=array();
  if(!$trans)
  { $ch1 = "абвгдеёзийклмнопрстуфхцыэ";
    $ch2 = "abvgdeeziyklmnoprstufhcye";
    for($i=0;$i<mb_strlen($ch1);$i++)
    $trans[mb_substr($ch1,$i,1)]=mb_substr($ch2,$i,1);
    $trans["ж"] = "zh"; $trans["ч"] = "ch"; $trans["ш"] = "sh"; $trans["щ"] = "sch";
	$trans["ъ"] = ""; $trans["ь"] = ""; $trans["ю"] = "yu"; $trans["я"] = "ya";
  }
  return preg_replace("/[^a-zA-Z0-9-]+/iu","_",str_replace(array_keys($trans),array_values($trans),mb_strtolower(trim($string))));
}

function escapeFileName($name,&$ext=null,&$basename=null)
{
  $path_parts=pathinfo($name);
  $ext=preg_replace("/[^a-z0-9]+/i","",strtolower($path_parts['extension']));
  if($ext && preg_match("/^(.+)\.".$ext."$/i",mb_strtolower(end(explode('/',$name))),$matches))
  { $basename=preg_replace("/[^a-zA-Zа-яА-Я0-9.-]+/iu","_",$matches[1]);
    return translit($basename).'.'.$ext;
  }
  else
  return false;
}

function filemanager_translit($input_string) {

 return escapeFileName($input_string);
}

function filemanager_deldir($root, $del, $thumb=0) {
	$cont = glob(CombinePaths($root, $del)."*");
	$rootLen = mb_strlen($root);
	$ok = 1;
	foreach ($cont as $val) {
		if (is_dir($val)) {
			$ok *= filemanager_deldir($root, mb_substr($val, $rootLen-1)."/", $thumb);
			$ok *= rmdir($val)?1:0;
		} else {
			$ok *= unlink($val)?1:0;
			if ($thumb) {
				$rootThumb = $_SERVER['DOCUMENT_ROOT'].GetResourceTypePath('ImageThumb', 'Delete');
				@unlink($rootThumb.filemanager_getthumbname(mb_substr($val, $rootLen-1)));
			}
		}
	}
	return $ok;
}
function filemanager_getthumbname($path) {
	if (mb_substr($path,0,1)=='/') $path = mb_substr($path,1);
	return str_replace('/', '_-_', $path);
}
function filemanager_imagemagick_check() {
	exec('convert -version', $out, $ret);
	return $ret?false:true;
}
function filemanager_gd2_check() {
	return function_exists('gd_info')?true:false;
}
function filemanager_debug($str) {
	$f = fopen($_SERVER['DOCUMENT_ROOT'].'/debug.txt', 'a');
	fwrite($f, $str."\n");
	fclose($f);
}
?>