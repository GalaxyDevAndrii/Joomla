<?php
/*------------------------------------------------------------------------
# com_guru
# ------------------------------------------------------------------------
# author    iJoomla
# copyright Copyright (C) 2013 ijoomla.com. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.ijoomla.com
# Technical Support:  Forum - http://www.ijoomla.com.com/forum/index/
-------------------------------------------------------------------------*/

defined( '_JEXEC' ) or die( 'Restricted access' );

$db = JFactory::getDBO();

class qqUploadedFileXhr {
    
    function save($path){
		$input = fopen("php://input", "r");
        $temp = tmpfile();
		
        $realSize = stream_copy_to_stream($input, $temp);
        fclose($input);
        
		if ($realSize != $this->getSize()){            
            return false;
        }
		
		/////////////////////////////////////////////////////
		$target = fopen(JPATH_SITE."/tmp/".basename($path), "w");        
        fseek($temp, 0, SEEK_SET);
		stream_copy_to_stream($temp, $target);
        fclose($target);
		$extension =  explode(".",$path);
		$extension_file = $extension[count($extension)-1];
		
		if($extension_file == 'jpg'|| $extension_file == 'jpeg' || $extension_file == 'png' || $extension_file == 'gif' || $extension_file == 'JPG'|| $extension_file == 'JPEG' || $extension_file ==  'PNG' || $extension_file == 'GIF'){
			$image_name = basename($path);
			$image_name = str_replace("%20", " ", $image_name);
			$image_size = getimagesize(JPATH_SITE."/tmp/".$image_name);
			
			if($image_size === FALSE){
				unlink(JPATH_SITE."/tmp/".basename($path));
				return false;
			}
		}
		
		unlink(JPATH_SITE."/tmp/".basename($path));
		/////////////////////////////////////////////////////
		
        $target = fopen($path, "w");        
        fseek($temp, 0, SEEK_SET);
		stream_copy_to_stream($temp, $target);
        fclose($target);
        
        return true;
    }
	
    function getName() {
        $data_get = JFactory::getApplication()->input->get->getArray();
		return $data_get['qqfile'];
    }
	
    function getSize() {
        if (isset($_SERVER["CONTENT_LENGTH"])){
            return (int)$_SERVER["CONTENT_LENGTH"];            
        } else {
            throw new Exception('Getting content length is not supported.');
        }      
    }   
}

class qqUploadedFileForm {  
   
    function save($path) {
        $file_request = JFactory::getApplication()->input->files->get( 'qqfile', NULL);
		
		if(!move_uploaded_file($file_request['tmp_name'], $path)){
            return false;
        }
        return true;
    }
	
    function getName() {
        $file_request = JFactory::getApplication()->input->files->get( 'qqfile', NULL);
		return $file_request['name'];
    }
	
    function getSize() {
        $file_request = JFactory::getApplication()->input->files->get( 'qqfile', NULL);
		return $file_request['size'];
    }
}

class qqFileUploader {
    private $allowedExtensions = array();
    private $sizeLimit = 10485760;
    private $file;
	private $size;
	private $type;
	

    function __construct(array $allowedExtensions = array(), $sizeLimit = 10485760/*,$size=200,$type='w'*/){
        $allowedExtensions = array_map("strtolower", $allowedExtensions);
        $file_request = JFactory::getApplication()->input->files->get( 'qqfile', NULL);
		    
        $this->allowedExtensions = $allowedExtensions;        
        $this->sizeLimit = $sizeLimit;    
		//$this->size=$size;
		//$this->type=$type;
		$data_get = JFactory::getApplication()->input->get->getArray();
		
        if (isset($data_get['qqfile'])) {
            $this->file = new qqUploadedFileXhr();
        } elseif (isset($file_request)) {
            $this->file = new qqUploadedFileForm();
        } else {
            $this->file = false; 
        }
    }
    
    function handleUpload($uploadDirectory, $replaceOldFile = FALSE){
		if (!is_writable($uploadDirectory)){
            return array('error' => "Server error. Upload directory isn't writable.");
        }
        
        if (!$this->file){
            return array('error' => 'No files were uploaded.');
        }
        
        $size = $this->file->getSize();
        
        if ($size == 0) {
            return array('error' => 'File is empty');
        }
        
        if ($size > $this->sizeLimit) {
            return array('error' => 'File is too large');
        }
        
        $pathinfo = pathinfo($this->file->getName());
        //$filename = $pathinfo['filename'];
		$data_get = JFactory::getApplication()->input->get->getArray();
        $filename = $data_get['qqfile'];
		$ext = $pathinfo['extension'];
		$filename = str_replace(".".$ext, "", $filename);

        if($this->allowedExtensions && !in_array(strtolower($ext), $this->allowedExtensions)){
            $these = implode(', ', $this->allowedExtensions);
            return array('error' => 'File has an invalid extension, it should be one of '. $these . '.');
        }
        
        if(!$replaceOldFile){
			/// don't overwrite previous files that were uploaded
            while (file_exists($uploadDirectory . $filename . '.' . $ext)) {
                $filename .= rand(10, 99);
            }
        }
        
        if ($this->file->save($uploadDirectory . $filename . '.' . $ext)){
			// security for uploading not valid images  -----------------------------------------------
			if(strtoupper($ext) == "JPG" || strtoupper($ext) == "JPEG" || strtoupper($ext) == "PNG" || strtoupper($ext) == "GIF"){
				if(($imginfo = getimagesize($uploadDirectory . $filename . '.' . $ext)) === false){
					unlink($uploadDirectory . $filename . '.' . $ext);
					return array('error'=> 'Not a valid image!');
				}
			}
			// security for uploading not valid images  -----------------------------------------------
			
			// security for uploading not valid file  -----------------------------------------------
			$accepted_files = array("doc", "docx", "txt", "pdf", "csv", "htm", "html", "xhtml", "sxi", "xml", "sxw", "rtf", "odt", "css", "odp", "pps", "ppt", "pptx", "ax", "amv", "asf", "avi", "mp4", "fla", "swf", "mov", "mpg", "mpeg", "rm", "aac", "aob", "ada", "au", "mid", "midi", "mp3", "ogg", "wav", "jpg", "jpeg", "png", "gif", "zip", "m4a", "xls", "xlsx");
			
			if(!in_array(strtolower($ext), $accepted_files)){
				unlink($uploadDirectory . $filename . '.' . $ext);
				return array('error'=> 'Not a valid file!');
			}
			// security for uploading not valid file  -----------------------------------------------
			return array('success'=>true);
        }
		else{
            return array('error'=> 'Could not save uploaded file.' .
                'The upload was cancelled, or server error encountered');
        }
        
    }
	
	function getName(){
		return $this->file->getName();
	}
	
	function getNewSize(){
		return $this->size;
	}
	
	function getType(){
		return $this->type;
	}
	
	function createThumb($folder,$new_size,$type){
		$mosConfig_absolute_path = JPATH_ROOT;
		$mosConfig_live_site = JURI :: base();
		$images=$this->getName();
		$folder=str_replace("/",DS,$folder);
		
		if(intval($new_size) > 0){	
			if(file_exists(JPATH_SITE.DIRECTORY_SEPARATOR.$folder.DIRECTORY_SEPARATOR.$images)){
				$old_size=@getimagesize(JPATH_SITE.DIRECTORY_SEPARATOR.$folder.DIRECTORY_SEPARATOR.$images);
				$width_old=$old_size[0];
				$height_old=$old_size[1];
				
				if(intval($width_old)==0 || intval($height_old)==0){
					return "";
				}
				
				if($type=='w'){
					//get the correct height
					
					if($width_old<$new_size) $width_new=$width_old;
			 		else $width_new=$new_size;
 			 		$height_new =intval($width_new*$height_old/$width_old); 
				}
				else{
					if($height_old<$new_size) $height_new=$height_old;
					else $height_new=$new_size;
					$width_new =intval($height_new*$width_old/$height_old); 
				}
				if(!is_dir(JPATH_ROOT.DIRECTORY_SEPARATOR.$folder.DIRECTORY_SEPARATOR."thumbs")) 
					mkdir(JPATH_ROOT.DIRECTORY_SEPARATOR.$folder.DIRECTORY_SEPARATOR."thumbs", 0777);
				
				$images = trim($images);
				//get dir name and file name
				$get_path = explode('/',$images);
				$nr = (count($get_path) - 1);
		
				$photo_name = $get_path[$nr];

				unset($get_path[$nr]);
				$path = implode("/",$get_path);
				
				//see if thumbnails is created and it have same size return his name
				if(file_exists(JPATH_ROOT.DIRECTORY_SEPARATOR.$folder.DIRECTORY_SEPARATOR."thumbs".DIRECTORY_SEPARATOR.$photo_name)){					
					$img_size_thumb = @getimagesize(JPATH_ROOT.DIRECTORY_SEPARATOR.$folder."thumbs".DIRECTORY_SEPARATOR.$photo_name);
					$width_thumb = $img_size_thumb[0];
					$height_thumb = $img_size_thumb[1];
					if($width_thumb == intval($width_new) || $height_thumb == intval($height_new)){
						return true;
					}
				}
		 
				$name_array = explode('.',$photo_name);
				$extension = $name_array[count($name_array)-1];
				
				switch($extension){
					case "jpg":				
						$gdimg = @imagecreatefromjpeg(JPATH_SITE.DIRECTORY_SEPARATOR.$folder.DIRECTORY_SEPARATOR.$images);
						break;
					case "jpeg":				
						$gdimg = @imagecreatefromjpeg(JPATH_SITE.DIRECTORY_SEPARATOR.$folder.DIRECTORY_SEPARATOR.$images);
						break;
					case "gif": 
						$gdimg = @imagecreatefromgif(JPATH_SITE.DIRECTORY_SEPARATOR.$folder.DIRECTORY_SEPARATOR.$images);
						break;
					case "png":
						$gdimg = @imagecreatefrompng(JPATH_SITE.DIRECTORY_SEPARATOR.$folder.DIRECTORY_SEPARATOR.$images);
						break;
					case "JPG":				
						$gdimg = @imagecreatefromjpeg(JPATH_SITE.DIRECTORY_SEPARATOR.$folder.DIRECTORY_SEPARATOR.$images);
						break;
					case "JPEG":				
						$gdimg = @imagecreatefromjpeg(JPATH_SITE.DIRECTORY_SEPARATOR.$folder.DIRECTORY_SEPARATOR.$images);
						break;
					case "GIF": 
						$gdimg = @imagecreatefromgif(JPATH_SITE.DIRECTORY_SEPARATOR.$folder.DIRECTORY_SEPARATOR.$images);
						break;
					case "PNG":
						$gdimg = @imagecreatefrompng(JPATH_SITE.DIRECTORY_SEPARATOR.$folder.DIRECTORY_SEPARATOR.$images);	
						break;
				}

				if($extension == "png"){
					$image_p = @imagecreatetruecolor($width_new, $height_new);
					@imagealphablending($image_p, false);
					@imagesavealpha($image_p, true);
					$source = @imagecreatefrompng(JPATH_SITE.DIRECTORY_SEPARATOR.$folder.DIRECTORY_SEPARATOR.$images);
					@imagealphablending($source, true);
					@imagecopyresampled($image_p, $source, 0, 0, 0, 0, $width_new, $height_new, $width_old, $height_old);
				}
				elseif($extension != 'gif'){
					$image_p = @imagecreatetruecolor($width_new, $height_new);
					$trans = @imagecolorallocate($image_p, 0,0,0);
					@imagecolortransparent($image_p, $trans);
					@imagecopyresampled($image_p, $gdimg, 0, 0, 0, 0, $width_new, $height_new, $width_old, $height_old);
				}
				else{ 	
					$image_p = @imagecreate($width_new, $height_new);
					$trans = @imagecolorallocate($image_p,0,0,0);
					@imagecolortransparent($image_p,$trans);
					@imagecopyresized($image_p, $gdimg, 0, 0, 0, 0, $width_new, $height_new, $width_old, $height_old);				
				}
		
				if($extension == "jpg" || $extension == "JPG"){
					$upload_th = @imagejpeg($image_p, JPATH_ROOT.DIRECTORY_SEPARATOR.$folder.DIRECTORY_SEPARATOR."thumbs".DIRECTORY_SEPARATOR.$photo_name, 100);
				}
				if($extension == "jpeg" || $extension == "JPEG"){
					$upload_th = @imagejpeg($image_p, JPATH_ROOT.DIRECTORY_SEPARATOR.$folder.DIRECTORY_SEPARATOR."thumbs".DIRECTORY_SEPARATOR.$photo_name, 100);			
				}
				if($extension == "gif" || $extension == "GIF"){
					$upload_th = @imagegif($image_p, JPATH_ROOT.DIRECTORY_SEPARATOR.$folder.DIRECTORY_SEPARATOR."thumbs".DIRECTORY_SEPARATOR.$photo_name, 100); 
				}	
				if($extension == "png" || $extension == "PNG"){
					$upload_th = @imagepng($image_p, JPATH_ROOT.DIRECTORY_SEPARATOR.$folder.DIRECTORY_SEPARATOR."thumbs".DIRECTORY_SEPARATOR.$photo_name);
				}
				
				if($upload_th){
					return true;
				}
				else{
					return false;
				}
			}
		}	
	}    
}

// list of valid extensions, ex. array("jpeg", "xml", "bmp")
$allowedExtensions = array();
// max file size in bytes
$sizeLimit = 888 * 1024 * 1024;

$mediaType = $_REQUEST['mediaType'];
$base_url = JPATH_SITE;

switch ($mediaType){
	case "image":
		$sql = "SELECT imagesin FROM #__guru_config LIMIT 1";
		$db->setQuery($sql);
		$res = $db->loadResult();
		$uploadPath=$base_url.DIRECTORY_SEPARATOR.$res.DIRECTORY_SEPARATOR.$_REQUEST['folder'].DIRECTORY_SEPARATOR;
		break;
	case "video":
		$sql = "SELECT videoin FROM #__guru_config LIMIT 1";
		$db->setQuery($sql);
		$res = $db->loadResult();
		$uploadPath=$base_url.DIRECTORY_SEPARATOR.$res.DIRECTORY_SEPARATOR;
		break;
	case "audio":
		$sql = "SELECT audioin FROM #__guru_config LIMIT 1";
		$db->setQuery($sql);
		$res = $db->loadResult();
		$uploadPath=$base_url.DIRECTORY_SEPARATOR.$res.DS;
		break;
	case "doc":
		$sql = "SELECT docsin FROM #__guru_config LIMIT 1";
		$db->setQuery($sql);
		$res = $db->loadResult();
		$uploadPath=$base_url.DIRECTORY_SEPARATOR.$res.DIRECTORY_SEPARATOR;
		break;
	case "file":
		$sql = "SELECT filesin FROM #__guru_config LIMIT 1";
		$db->setQuery($sql);
		$res = $db->loadResult();
		$uploadPath=$base_url.DIRECTORY_SEPARATOR.$res.DIRECTORY_SEPARATOR;
		break;
}

$uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
$result = $uploader->handleUpload($uploadPath, true);

if(isset($_REQUEST['size']))
	$size=$_REQUEST['size'];
else $size=200;

if (isset($_REQUEST['type']))
	$type=$_REQUEST['type'];
else $size="w";


if(isset($result['success'])&&($result['success'] == true)) {
	//start images; we need to create thumbs
	if($mediaType=="image"){
		$res=$res."/".$_REQUEST['folder'];
		if($uploader->createThumb($res,$size,$type)){
			$result['locate'] = $res."/thumbs";
		}
		else
			$result['locate'] = $res;
	}
	//end images
	//start others types; don't need to do thumbs
	else{
		$result['locate'] = $res;
	}
	//end 
	
} 
else {
	$result['locate'] = false;
}
// to pass data through iframe you will need to encode all html tags
echo htmlspecialchars(json_encode($result), ENT_NOQUOTES);