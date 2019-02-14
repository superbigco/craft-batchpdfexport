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
use craft\events\RegisterElementActionsEvent;
use craft\helpers\FileHelper;
use craft\helpers\StringHelper;
use iio\libmergepdf\Merger as Merger;
use superbig\batchpdfexport\BatchPdfExport;
use craft\commerce\Plugin as Commerce;

use Craft;
use craft\base\Component;
use superbig\batchpdfexport\elementactions\ExportAction;
use yii\base\InvalidArgumentException;
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

    public function registerActions(RegisterElementActionsEvent $event)
    {
        $settings         = BatchPdfExport::$plugin->getSettings();
        $useCustomActions = $settings->useCustomActions;
        $customActions    = $settings->actions;

        if ($useCustomActions && !empty($customActions)) {
            $elementsService = Craft::$app->getElements();
            foreach ($customActions as $action) {
                $label    = $action['label'] ?? null;
                $template = $action['template'] ?? null;

                if (empty($label) || empty($template)) {
                    throw new InvalidArgumentException('Both action label and template need to be specified.');
                }

                $event->actions[] = $elementsService->createAction([
                    'type'     => ExportAction::class,
                    'label'    => Craft::t('batch-pdf-export', $label),
                    'template' => $template,
                ]);
            }
        }
        else {
            $event->actions[] = ExportAction::class;
        }
    }

    public function generatePdfs($orders = null, array $ids = [], $template): array
    {
        if (!$orders) {
            return false;
        }

        $option = null;
        $view   = Craft::$app->getView();

        // Set the config options
        $mergePaths     = [];
        $pathService    = Craft::$app->getPath();
        $tempPath       = $pathService->getTempPath() . '/batchpdfs/';
        $fileNameMerged = 'Orders-' . implode('-', $ids) . '.pdf';
        $filenameFormat = Commerce::getInstance()->getSettings()->orderPdfFilenameFormat;

        FileHelper::createDirectory($tempPath);

        /** @var Order $order */
        foreach ($orders as $order) {
            $pdfOutput = Commerce::getInstance()->getPdf()->renderPdfForOrder($order, $option, $template);
            $fileName  = $view->renderObjectTemplate($filenameFormat, $order);

            if (empty($fileName)) {
                $fileName = 'Order-' . $order->number;
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

    public function getDefaultTemplate(): string
    {
        return Commerce::getInstance()->getSettings()->orderPdfPath;
    }

    public function getDefaultLabel(): string
    {
        $defaultLabel    = BatchPdfExport::$plugin->getSettings()->defaultLabel;
        $label           = !empty($defaultLabel) ? $defaultLabel : 'Generate Invoices PDF';
        $translatedLabel = Craft::t('batch-pdf-export', $label);

        return $translatedLabel;
    }
}
