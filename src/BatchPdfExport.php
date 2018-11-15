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

        if (Craft::$app instanceof ConsoleApplication) {
            $this->controllerNamespace = 'superbig\batchpdfexport\console\controllers';
        }

        Event::on(Order::class, Order::EVENT_REGISTER_ACTIONS, function(RegisterElementActionsEvent $event) {
            $event->actions[] = ExportAction::class;
        });

        Event::on(
            Plugins::class,
            Plugins::EVENT_AFTER_INSTALL_PLUGIN,
            function(PluginEvent $event) {
                if ($event->plugin === $this) {
                }
            }
        );

        Craft::info(
            Craft::t(
                'batch-pdf-export',
                '{name} plugin loaded',
                ['name' => $this->name]
            ),
            __METHOD__
        );
    }
}
