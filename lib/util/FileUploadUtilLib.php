<?php

class FileUploadUtilLib extends ImageUtilLib {

    /**
     * 没有文件上传成功
     *
     * @var int
     */
    const ERROR_EMPTY = 1;

    /**
     * 文件只有部分上传
     *
     * @var int
     */
    const ERROR_NOT_FULL = 2;

    /**
     * 上传大小超过限制
     *
     * @var int
     */
    const ERROR_MAX_FILE_SIZE = 3;

    /**
     * 上传类型超过限制
     *
     * @var int
     */
    const ERROR_FILE_TYPE = 4;

    /**
     * 其他错误
     *
     * @var int
     */
    const ERROR_MORE = 5;

    private $allowType = array(
        'png',
        'jpg',
        'jpeg',
        'gif'
    );
    private $maxSize = 92160000;
    private $file_;
    private $fileNum;
    private $filePath = 'public/uploads';
    private $rename;
    private $errorno = array();
    private $uploadInfo = array();
    private $fileField;

    public function __construct($name = '', $rename = true) {
        if (!file_exists($this->filePath)) {
            mkdir($this->filePath);
        }
        $this->filePath .= '/' . date('Y') . '/' . date('m') . '/' . date('d');
        CommUtilLib::rMkdir($this->filePath);
        $this->fileField = $name;
        $this->rename = $rename;
        if (empty($_FILES [$name])) {
            $this->errorno [] = self::ERROR_EMPTY;
            return false;
        }

        $this->file_ = $_FILES [$name];
        $this->file_ ['name'] = is_array($this->file_ ['name']) ? $this->file_ ['name'] : array(
            $this->file_ ['name']
        );
        $this->file_ ['type'] = is_array($this->file_ ['type']) ? $this->file_ ['type'] : array(
            $this->file_ ['type']
        );
        $this->file_ ['tmp_name'] = is_array($this->file_ ['tmp_name']) ? $this->file_ ['tmp_name'] : array(
            $this->file_ ['tmp_name']
        );
        $this->file_ ['error'] = is_array($this->file_ ['error']) ? $this->file_ ['error'] : array(
            $this->file_ ['error']
        );
        $this->file_ ['size'] = is_array($this->file_ ['size']) ? $this->file_ ['size'] : array(
            $this->file_ ['size']
        );
    }

    public function getErrorMsg() {
        return $this->errorno;
    }

    public function setAllowType($types) {
        $this->allowType = $types;
        return $this;
    }

    public function setMaxSize($size) {
        $this->maxSize = $size;
        return $this;
    }

    public function setPath($path) {
        $this->filePath = $path;
        return $this;
    }

    public function upload() {
        $this->fileNum = count($this->file_ ['name']);
        for ($i = 0; $i < $this->fileNum; $i ++) {
            // 验证错误编号
            if (!$this->checkError($this->file_ ['error'] [$i], $i)) {
                continue;
            }
            // 验证文件大小
            if (!$this->checkSize($this->file_ ['size'] [$i], $i)) {
                continue;
            }
            // 验证文件后缀
            if (!$this->checkType($this->file_ ['name'] [$i], $i)) {
                continue;
            }
            $this->moveFile($this->file_ ['tmp_name'] [$i], $this->file_ ['name'] [$i], $i);
        }
        return $this->uploadInfo;
    }

    private function moveFile($tmpName, $fileName, $i) {
        $type = pathinfo($fileName, PATHINFO_EXTENSION);
        if ($this->rename) {
            $fileName = microtime(true) . rand(10000, 9999) . '.' . $type;
        }
        $pathFileName = rtrim($this->filePath, '/') . '/' . $fileName;
        if (move_uploaded_file($tmpName, $pathFileName)) {
            $this->uploadInfo [$i] = $pathFileName;
        }
    }

    private function checkType($fileName, $i) {
        $type = pathinfo($fileName, PATHINFO_EXTENSION);
        if ($this->allowType && !in_array(strtolower($type), $this->allowType)) {
            $this->errorno [$i] = self::ERROR_FILE_TYPE;
            return false;
        }
        return true;
    }

    private function checkSize($size, $i) {
        if ($size > $this->maxSize) {
            $this->errorno [$i] = self::ERROR_MAX_FILE_SIZE;
            return false;
        }
        return true;
    }

    private function checkError($number, $i) {
        switch ($number) {
            case 4 :
                $this->errorno [$i] = self::ERROR_EMPTY;
                return false;
            case 3 :
                $this->errorno [$i] = self::ERROR_NOT_FULL;
                return false;
            case 2 :
                $this->errorno [$i] = self::ERROR_MAX_FILE_SIZE;
                return false;
            case 1 :
                $this->errorno [$i] = self::ERROR_MAX_FILE_SIZE;
                return false;
        }
        return true;
    }

}
