<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    /**
     * @var string
     */
    protected $_destination;

    /**
     * @var string[]
     */
    protected $_fileNameOptions;

    public function __construct(string $destination, array $fileNameOptions)
    {
        $this->_destination = $destination;
        $this->_fileName = $fileNameOptions;
    }

    /**
     * @return string
     */
    public function getDestination()
    {
        return $this->_destination;
    }

    /**
     * @return string[]
     */
    public function getFileNameOptions()
    {
        return $this->_fileNameOptions;
    }

    /**
     * uploads file to tmp folder
     *
     * @param UploadedFile $file
     * @return void
     */
    public function upload(UploadedFile $file)
    {
        try {
            $file->move($this->_destination, $this->buildFileName());
        } catch (FileException $e) {

        }
    }

    /**
     * builds filename with prefix and date format
     *
     * @return string
     */
    private function buildFileName()
    {
        return sprintf("%s_%s.%s", $this->_fileNameOptions['prefix'], date($this->_fileNameOptions['date_format']), $this->_fileNameOptions['extension']);
    }
}