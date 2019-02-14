<?php
/**
 * Batch PDF Export plugin for Craft CMS 3.x
 *
 * Mass export PDF invoices in one go.
 *
 * @link      https://superbig.co
 * @copyright Copyright (c) 2018 Superbig
 */

namespace superbig\batchpdfexport;

use craft\base\Element;
use craft\commerce\elements\Order;
use craft\events\RegisterElementActionsEvent;
use superbig\batchpdfexport\assetbundles\BatchPdfExport\BatchPdfExportAsset;
use superbig\batchpdfexport\elementactions\ExportAction;
use superbig\batchpdfexport\services\BatchPdfExportService;
use superbig\batchpdfexport\models\Settings;

use Craft;
use craft\base\Plugin;
use craft\services\Plugins;
use craft\events\PluginEvent;
use craft\console\Application as ConsoleApplication;
use craft\web\UrlManager;
use craft\events\RegisterUrlRulesEvent;

use yii\base\Event;

/**
 * Class BatchPdfExport
 *
 * @author    Superbig
 * @package   BatchPdfExport
 * @since     1.0.0
 *
 * @property  BatchPdfExportService $batchPdfExportService
 * @method  Settings getSettings()
 */
class BatchPdfExport extends Plugin
{
    // Static Properties
    // =========================================================================

    /**
     * @var BatchPdfExport
     */
    public static $plugin;

    // Public Properties
    // =========================================================================

    /**
     * @var string
     */
    public $schemaVersion = '1.0.0';

    // Public Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        self::$plugin = $this;

        $this->setComponents([
            'batchPdfExportService' => BatchPdfExportService::class,
        ]);

        Event::on(Order::class, Order::EVENT_REGISTER_ACTIONS, [self::$plugin->batchPdfExportService, 'registerActions']);

        if (Craft::$app->getRequest()->getIsCpRequest() && !Craft::$app->getRequest()->getIsActionRequest()) {
            Craft::$app->getView()->registerAssetBundle(BatchPdfExportAsset::class);
        }

        Craft::info(
            Craft::t(
                'batch-pdf-export',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }

    // Protected Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    protected function createSettingsModel()
    {
        return new Settings();
    }

    /**
     * @inheritdoc
     */
    protected function settingsHtml(): string
    {
        $settings  = $this->getSettings();
        $overrides = Craft::$app->getConfig()->getConfigFromFile(strtolower($this->handle));

        return Craft::$app->view->renderTemplate(
            'batch-pdf-export/settings',
            [
                'settings'  => $settings,
                'plugin'    => $this,
                'overrides' => array_keys($overrides),
            ]
        );
    }
}
