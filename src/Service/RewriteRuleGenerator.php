<?php

namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;

class RewriteRuleGenerator
{
    /**
     * @var bool
     */
    private $_rewriteEngineOn;

    /**
     * @var int
     */
    private $_statusCode;

    /**
     * @var array
     */
    private $_rewriteRules;

    /**
     * @var string
     */
    private $_fileTemplate;

    /**
     * @var string
     */
    private $_additionalFlags;

    /**
     * @return mixed
     */
    public function getAdditionalFlags()
    {
        return $this->_additionalFlags;
    }

    /**
     * @param mixed $additionalFlags
     */
    public function setAdditionalFlags($additionalFlags): void
    {
        $this->_additionalFlags = $additionalFlags;
    }

    /**
     * @param bool $rewriteEngineOn
     */
    public function setRewriteEngineOn(bool $rewriteEngineOn) : void
    {
        $this->_rewriteEngineOn = $rewriteEngineOn;
    }

    /**
     * @return bool
     */
    public function isRewriteEngineOn() : bool
    {
        return $this->_rewriteEngineOn;
    }

    /**
     * @param int $statusCode
     */
    public function setStatusCode(int $statusCode) : void
    {
        $this->_statusCode = $statusCode;
    }

    /**
     * @return int
     */
    public function getStatusCode() : int
    {
        return $this->_statusCode;
    }

    /**
     * @param array $rewriteRules
     */
    public function setRewriteRules(array $rewriteRules) : void
    {
        $this->_rewriteRules = $rewriteRules;
    }

    /**
     * @return array
     */
    public function getRewriteRules() : array
    {
        return $this->_rewriteRules;
    }

    /**
     * @param string $fileTemplate
     */
    public function setFileTemplate(string $fileTemplate) : void
    {
        $this->_fileTemplate = $fileTemplate;
    }

    /**
     * @return string
     */
    public function getFileTemplate() : string
    {
        return $this->_fileTemplate;
    }

    /**
     * @return array
     */
    public function toArray() : array
    {
        return [
            'rewrite_engine_on' => $this->_rewriteEngineOn,
            'rewrite_rules' => $this->_rewriteRules,
            'status_code' => $this->_statusCode,
            'additional_flags' => $this->_additionalFlags
        ];
    }

    /**
     * writes template string with included rewrite rules to new file
     *
     * @param string $uploadDirectory
     * @param string $filename
     *
     * @return string download link
     */
    public function exportFile(string $uploadDirectory, string $filename) : string
    {
        /**
         * create filesystem instance
         * create upload directory if it doesnt exist
         */
        $fs = new Filesystem();
        if (!$fs->exists($uploadDirectory)) {
            $fs->mkdir($uploadDirectory);
        }

        $fs->dumpFile($uploadDirectory . '/' . $filename, $this->_fileTemplate);

        return $filename;
    }
}