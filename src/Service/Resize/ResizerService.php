<?php

namespace App\Service\Resize;

use App\Exception;
use App\Helper\ImageTrait;

class ResizerService implements ResizerInterface {
    use ImageTrait;

    /**
     * @var int
     */
    private $side;
    /**
     * @var string
     */
    private $bgColor;
    /**
     * @var bool
     */
    private $delOrigin;
    /**
     * @var array
     */
    private $checkedDirs = [];

    /**
     * @param array $config
     * @throws Exception\ConfigException
     */
    public function __construct(array $config = []) {
        $this->side = !empty($config['side']) ? (int)$config['side'] : self::DEFAULT_SIZE;
        $this->bgColor = !empty($config['bg-color']) ? $config['bg-color'] : self::DEFAULT_COLOR;
        $this->delOrigin = isset($config['delete-origin']) ? $config['delete-origin'] : true;

        if (!$this->side) {
            throw new Exception\ConfigException("Wrong target size for image. It must be a non-negative integer value");
        }
    }

    /**
     * @param string $file
     * @return string
     * @throws Exception\RunTimeException
     */
    public function resize($file) {
        if (!is_file($file) || !is_readable($file)) {
            throw new Exception\RunTimeException("File $file must exist and be readable");
        }

        $targetDir = $this->getTargetDirectory($file);
        $fileName = pathinfo($file, PATHINFO_FILENAME) .'.jpg';
        $targetFile = "$targetDir/$fileName";

        try {
            $this->resizeImage($this->openImage($file), $this->side, $this->bgColor)
                 ->save($targetFile);
        } catch (\Exception $e) {
            throw new Exception\RunTimeException($e->getMessage(), $e->getCode());
        }

        if ($this->delOrigin) {
            unlink($file);
        }

        return $targetFile;
    }

    /**
     * @param string $file
     * @return string
     * @throws Exception\RunTimeException
     */
    protected function getTargetDirectory($file) {
        $targetDir = dirname($file) . '_resized';

        if (!isset($this->checkedDirs[$targetDir])) {
            if (!file_exists($targetDir)) {
                $parentDir = dirname($targetDir);
                if (!is_writable($parentDir)) {
                    throw new Exception\RunTimeException("Directory $parentDir must be writable to create target directory for resized images");
                }
                mkdir($targetDir);
            } elseif (!is_dir($targetDir) || !is_writable($targetDir)) {
                throw new Exception\RunTimeException("The target directory `$targetDir` must be writable");
            }
        }

        return $targetDir;
    }
}