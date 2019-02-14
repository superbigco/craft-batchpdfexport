<?php
/**
 * Batch PDF Export plugin for Craft CMS 3.x
 *
 * Mass export PDF invoices in one go.
 *
 * @link      https://superbig.co
 * @copyright Copyright (c) 2018 Superbig
 */

namespace superbig\batchpdfexport\assetbundles\BatchPdfExport;

use Craft;
use craft\web\AssetBundle;
use craft\web\assets\cp\CpAsset;

/**
 * @author    Superbig
 * @package   BatchPdfExport
 * @since     1.0.0
 */
class BatchPdfExportAsset extends AssetBundle
{
    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->sourcePath = "@superbig/batchpdfexport/assetbundles/batchpdfexport/dist";

        $this->depends = [
            CpAsset::class,
        ];

        $this->js = [
            'js/BatchPdfExport.js',
        ];

        $this->css = [];

        parent::init();
    }
}
