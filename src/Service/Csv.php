<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class Csv
{
    /**
     * @var UploadedFile
     */
    private $_csv;

    /**
     * @var string
     */
    private $_tmpDir;

    /**
     * @var string
     */
    private $_tmpFileName;

    /**
     * @var RewriteRule[]
     */
    private $_rewriterules;

    /**
     * @var int
     */
    private $_invalidUrls;

    public function __construct(UploadedFile $csv)
    {
        $this->_csv = $csv;
        $this->_invalidUrls = 0;
    }

    /**
     * builds unique tmp file name to avoid multiple files at same time
     * with equal file name
     *
     * @return string
     */
    private function buildTmpFileName()
    {
        $this->_tmpFileName = sprintf("%s_%s", time(), $this->_csv->getClientOriginalName());
        return $this->_tmpFileName;
    }


    /**
     * @param string $tmpDir
     */
    public function moveToTmpDir(string $tmpDir)
    {
        $this->_tmpDir = $tmpDir;

        try {
            $this->_csv->move($tmpDir, $this->buildTmpFileName());
        } catch (FileException $e) {
            // TODO: Error handling
        }
    }

    /**
     * removes csv from tmp folder
     */
    public function removeTmpFile() : void
    {
        unlink($this->_tmpDir . '/' . $this->_tmpFileName);
    }


    /**
     * check if file is a valid CSV file
     *
     * @return bool
     */
    public function isValid() : bool
    {
        return $this->_csv->getClientMimeType() !== 'text/csv' ? false : true;
    }

    /**
     * returns full file path to uploaded tmp file
     *
     * @return string
     */
    private function getFullFilePath()
    {
        return $this->_tmpDir . '/' . $this->_tmpFileName;
    }


    /**
     * @return RewriteRule[]
     */
    public function getRewriteRules() : array
    {
        if (($fileObj = fopen($this->getFullFilePath(), "r")) !== false) {
            while (($row = fgetcsv($fileObj, 0, ";"))) {

                /**
                 * Iterate over all rows
                 * validate URLs
                 * create new RewriteRule instance
                 * pass it to rules array
                 */
                for ($i = 0; $i < count($row); $i+=2) {
                    $from = $this->validateUrl($row[0]);
                    $to = $this->validateUrl($row[1]);

                    $this->_rewriterules[] = new RewriteRule($from, $to);
                }
            }
            fclose($fileObj);
        }

        return $this->_rewriterules;
    }

    /**
     * removes invalid symbols from URL
     *
     * @param string $url
     * @return string
     */
    private function validateUrl(string $url)
    {
        $url = str_replace(' ', '', $url);
        return preg_replace('/[(\(\)§\$%°\^\s\;\,"\\\'\{\}\[\]<>ß=\ã)]*/i', '', $url);
    }
}