<?php

namespace Startimes\Aloza;

/**
 * @author Mohammed Elbouhali
 * @author Yassine Belkaid
 *
 * @package Aloza 1.0.5 
 */
class Uploader
{
	public $_fileName;
	public $_fileSize;
	public $_fileExtension;
	public $_tmpName;

	private $_allowedExt = array();
	private $_size = 51200; // 5 MB
	private $_directoryLocation = 'uploads';
	private $_inputName = 'file';

	private $_errors = array();
	const ALLOWED_EXTENTIONS = ['png', 'jpeg', 'jpg', 'gif'];

	public function __construct(array $configs = array())
	{
		$this->_allowedExt = ((isset($configs['allowed_extensions']) && is_array($configs['allowed_extensions']) && count($configs['allowed_extensions'])) ? $configs['allowed_extensions'] : self::ALLOWED_EXTENTIONS);
		$this->_inputName = (isset($configs['input_name']) && !empty($configs['input_name']) ? $configs['input_name'] : $this->_inputName);
		$this->_directoryLocation= ((isset($configs['location_dir']) && !empty($configs['location_dir'])) ? $configs['location_dir'] : $this->_directoryLocation);
		$this->_size = ((isset($configs['file_size']) && !empty($configs['file_size'])) ? $configs['file_size'] : $this->_size);

		$this->_fileName = trim($_FILES[$this->_inputName]['name']);
		$this->_fileSize = $_FILES[$this->_inputName]['size'];
		$this->_tmpName  = $_FILES[$this->_inputName]['tmp_name'];
	}

	private function _checkFile() {
		// if directory doesn't exist, we create it
		if (!is_dir($this->_directoryLocation)) {
			@mkdir($this->_directoryLocation);

			$this->setPermission($this->_directoryLocation, 0755);
		}

		if (!isset($this->_fileName) || !$this->isFileAllowed($this->_fileName)) {
			if ($file_ext = $this->getfileExtension($this->_fileName)) {
				$this->_errors[] = sprintf('The file type "%s" is not supported!', $file_ext);
			} else {
				$this->_errors[] = 'The file type is unknow!';
			}
		}

		return true;
	}

	public function isFileAllowed($file) {
		if (empty($file) || !($file_ext = $this->getfileExtension($file))) {
			return false;
		}

		return is_array($this->_allowedExt) && in_array($file_ext, $this->_allowedExt);
	}

	public function checkPermission($file) {
		return substr(sprintf('%o', fileperms($file)), -4);
	}

	public function setPermission($directory, $permission = 0755) {
		return chmod($directory, $permission);
	}

	public function getfileExtension($file) {
		if (empty($file)) {
			return false;
		}

		return pathinfo($file, PATHINFO_EXTENSION);
	}

	private function _checkFileSize() {
		if ($this->_fileSize <= $this->_size) {
			return true;
		} else{
			$this->_errors[] = sprintf('The file size "%s" is too big. Would you please provide a file with no more than "%s"', $this->_fileSize, $this->_size);
			return false;
		}
	}

	public function uploadFile() {
		if ($this->_checkFile() && $this->_checkFileSize() == true) {
			if (@move_uploaded_file($this->_tmpName, $this->_directoryLocation. '/'. $this->_fileName)) {
				return 'The file has been successfully uploaded!';
			} else {
				return 'File couldn\'t be uploaded!';
			}
		} else {
			$errors = array();
			foreach($this->_errors as $error) {
				$errors[] = $error;
			}

			return $errors;
		}
	}

}