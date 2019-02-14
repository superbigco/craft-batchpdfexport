<?php
/**
 * Batch PDF Export plugin for Craft CMS 3.x
 *
 * Mass export PDF invoices in one go.
 *
 * @link      https://superbig.co
 * @copyright Copyright (c) 2018 Superbig
 */

namespace superbig\batchpdfexport\controllers;

use craft\commerce\elements\Order;
use superbig\batchpdfexport\BatchPdfExport;

use Craft;
use craft\web\Controller;
use yii\base\InvalidConfigException;

/**
 * @author    Superbig
 * @package   BatchPdfExport
 * @since     1.0.0
 */
class DefaultController extends Controller
{

    // Public Methods
    // =========================================================================

    /**
     * @return mixed
     */
    public function actionIndex()
    {
        $ids      = Craft::$app->getRequest()->getRequiredParam('ids');
        $template = Craft::$app->getRequest()->getRequiredParam('template');

        if (empty($ids)) {
            throw new InvalidConfigException('No order ids provided');
        }

        $orders = Order::find()
                       ->limit(null)
                       ->id($ids)
                       ->all();

        $output   = BatchPdfExport::$plugin->batchPdfExportService->generatePdfs($orders, $ids, $template);
        $filename = $output['filename'];

        Craft::$app->getResponse()->sendContentAsFile($output['output'], $filename, [
            'mimeType' => 'application/pdf',
        ]);

        Craft::$app->end();
    }
}
