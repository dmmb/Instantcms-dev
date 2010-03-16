<?php
/*
 * FCKeditor - The text editor for Internet - http://www.fckeditor.net
 * Copyright (C) 2003-2007 Frederico Caldeira Knabben
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
 * Configuration file for the File Manager Connector for PHP.
 */

global $Config ;

// SECURITY: You must explicitelly enable this "connector". (Set it to "true").
// WARNING: don't just set "ConfigIsEnabled = true", you must be sure that only 
//		authenticated users can access this file or use some kind of session checking.
$Config['Enabled'] = true ;


// Path to user files relative to the document root.
$Config['UserFilesPath'] = '/upload/' ;

// Fill the following value it you prefer to specify the absolute path for the
// user files directory. Usefull if you are using a virtual directory, symbolic
// link or alias. Examples: 'C:\\MySite\\userfiles\\' or '/root/mysite/userfiles/'.
// Attention: The above 'UserFilesPath' must point to the same directory.
$Config['UserFilesAbsolutePath'] = $_SERVER['DOCUMENT_ROOT'].'/upload/' ;

// Due to security issues with Apache modules, it is reccomended to leave the
// following setting enabled.
$Config['ForceSingleExtension'] = true ;

// Perform additional checks for image files  - check whether uploaded images are valid image files
// 0 = turn off
// 1 = validate image files using getimagesize, should be enough to reject files that are not images at all
// 2 = most secure option, validate images also against MIME Type Detection bug that 
//     can lead to Cross Site Scripting attacks (when image contains HTML tags in the first 1KB, some browsers may render it as a HTML file). 
//     Attention: it may produce false positives in some situations
$Config['SecureImageUploads'] = 2;

// What the user can do with this connector
$Config['ConfigAllowedCommands'] = array('QuickUpload', 'FileUpload', 'GetFolders', 'GetFoldersAndFiles', 'CreateFolder') ;

// Allowed Resource Types
$Config['ConfigAllowedTypes'] = array('File', 'Image', 'Flash', 'Media') ;

/*
	Configuration settings for each Resource Type

	- AllowedExtensions: the possible extensions that can be allowed. 
		If it is empty then any file type can be uploaded.
	- DeniedExtensions: The extensions that won't be allowed. 
		If it is empty then no restrictions are done here.

	For a file to be uploaded it has to fullfil both the AllowedExtensions
	and DeniedExtensions (that's it: not being denied) conditions.

	- FileTypesPath: the virtual folder relative to the document root where
		these resources will be located. 
		Attention: It must start and end with a slash: '/'

	- FileTypesAbsolutePath: the physical path to the above folder. It must be
		an absolute path. 
		If it's an empty string then it will be autocalculated.
		Usefull if you are using a virtual directory, symbolic link or alias. 
		Examples: 'C:\\MySite\\userfiles\\' or '/root/mysite/userfiles/'.
		Attention: The above 'FileTypesPath' must point to the same directory.
		Attention: It must end with a slash: '/'


	 - QuickUploadPath: the virtual folder relative to the document root where
		these resources will be uploaded using the Upload tab in the resources 
		dialogs.
		Attention: It must start and end with a slash: '/'

	 - QuickUploadAbsolutePath: the physical path to the above folder. It must be
		an absolute path. 
		If it's an empty string then it will be autocalculated.
		Usefull if you are using a virtual directory, symbolic link or alias. 
		Examples: 'C:\\MySite\\userfiles\\' or '/root/mysite/userfiles/'.
		Attention: The above 'QuickUploadPath' must point to the same directory.
		Attention: It must end with a slash: '/'

*/

$Config['AllowedExtensions']['File']	= array() ;
$Config['DeniedExtensions']['File']		= array('html','htm','php','php2','php3','php4','php5','phtml','pwml','inc','asp','aspx','ascx','jsp','cfm','cfc','pl','bat','exe','com','dll','vbs','js','reg','cgi','htaccess','asis','sh','shtml','shtm','phtm') ;
$Config['FileTypesPath']['File']		= '/upload/' ;
$Config['FileTypesAbsolutePath']['File']= $_SERVER['DOCUMENT_ROOT'].'/upload/' ;
$Config['QuickUploadPath']['File']		= '/upload/';
$Config['QuickUploadAbsolutePath']['File']= $_SERVER['DOCUMENT_ROOT'].'/upload/' ;

$Config['AllowedExtensions']['Image']	= array('jpg','gif','jpeg','png') ;
$Config['DeniedExtensions']['Image']	= array() ;
$Config['FileTypesPath']['Image']		= '/images/' ;
$Config['FileTypesAbsolutePath']['Image']= $_SERVER['DOCUMENT_ROOT'].'/images/' ;
$Config['QuickUploadPath']['Image']		= '/images/' ;
$Config['QuickUploadAbsolutePath']['Image']= $_SERVER['DOCUMENT_ROOT'].'/images/' ;

$Config['AllowedExtensions']['Flash']	= array('swf','fla') ;
$Config['DeniedExtensions']['Flash']	= array() ;
$Config['FileTypesPath']['Flash']		= '/images/swf/' ;
$Config['FileTypesAbsolutePath']['Flash']= $_SERVER['DOCUMENT_ROOT'].'/images/swf/' ;
$Config['QuickUploadPath']['Flash']		= '/images/swf/' ;
$Config['QuickUploadAbsolutePath']['Flash']= $_SERVER['DOCUMENT_ROOT'].'/images/swf/' ;

$Config['AllowedExtensions']['Media']	= array('swf','fla','jpg','gif','jpeg','png','avi','mpg','mpeg') ;
$Config['DeniedExtensions']['Media']	= array() ;
$Config['FileTypesPath']['Media']		= '/images/media/' ;
$Config['FileTypesAbsolutePath']['Media']= $_SERVER['DOCUMENT_ROOT'].'/images/media/';
$Config['QuickUploadPath']['Media']		= '/images/media/' ;
$Config['QuickUploadAbsolutePath']['Media']= $_SERVER['DOCUMENT_ROOT'].'/images/media/' ;

?>