<?php
/**
 * Batch PDF Export plugin for Craft CMS 3.x
 *
 * Mass export PDF invoices in one go.
 *
 * @link      https://superbig.co
 * @copyright Copyright (c) 2018 Superbig
 */

namespace superbig\batchpdfexport\services;

use craft\commerce\elements\Order;
use craft\helpers\FileHelper;
use craft\helpers\StringHelper;
use iio\libmergepdf\Merger;
use superbig\batchpdfexport\BatchPdfExport;
use craft\commerce\Plugin as Commerce;

use Craft;
use craft\base\Component;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;

/**
 * @author    Superbig
 * @package   BatchPdfExport
 * @since     1.0.0
 */
class BatchPdfExportService extends Component
{
    // Public Methods
    // =========================================================================

    public function generatePdfs($orders = null, $ids = [], $option = null)
    {
        if (!$orders) {
            return false;
        }

        $view    = Craft::$app->getView();
        $oldMode = $view->getTemplateMode();

        // Set the config options
        $mergePaths        = [];
        $pathService       = Craft::$app->getPath();
        $tempPath          = $pathService->getTempPath() . '/batchpdfs/';
        $fileNameMerged    = 'Orders-' . implode('-', $ids) . '.pdf';
        $fileNameMergePath = $tempPath . '/' . $fileNameMerged;
        $filenameFormat    = Commerce::getInstance()->getSettings()->orderPdfFilenameFormat;

        FileHelper::createDirectory($tempPath);

        /** @var Order $order */
        foreach ($orders as $order) {
            $pdfOutput = Commerce::getInstance()->getPdf()->renderPdfForOrder($order, $option);
            $fileName  = $view->renderObjectTemplate($filenameFormat, $order);

            if (empty($fileName)) {
                $fileName = "Order-" . $order->number;
            }

            // Append random suffix and pdf ending
            $fileName   = rtrim($fileName, '.pdf') . '-' . StringHelper::randomString(8) . '.pdf';
            $outputPath = $tempPath . '/' . $fileName;

            // Write temp file
            FileHelper::writeToFile($outputPath, $pdfOutput);

            $mergePaths[] = $outputPath;
        }


        // Merge PDF
        $m = new Merger();

        foreach ($mergePaths as $mergePath) {
            $m->addFile($mergePath);
        }

        $output = [
            'output'   => $m->merge(),
            'filename' => $fileNameMerged,
        ];

        return $output;
    }
}
