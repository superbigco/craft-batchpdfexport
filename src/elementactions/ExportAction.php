<?php
/**
 * Batch PDF Export plugin for Craft CMS 3.x
 *
 * Mass export PDF invoices in one go.
 *
 * @link      https://superbig.co
 * @copyright Copyright (c) 2018 Superbig
 */

namespace superbig\batchpdfexport\elementactions;

use craft\base\ElementAction;
use craft\elements\db\ElementQueryInterface;
use craft\helpers\Json;
use craft\helpers\StringHelper;
use superbig\batchpdfexport\BatchPdfExport;

use Craft;
use craft\base\Model;

/**
 * @author    Superbig
 * @package   BatchPdfExport
 * @since     1.0.0
 */
class ExportAction extends ElementAction
{
    // Public Properties
    // =========================================================================

    public $label;
    public $template;

    public function init()
    {
        parent::init();

        if (!$this->label) {
            $this->label = BatchPdfExport::$plugin->batchPdfExportService->getDefaultLabel();
        }

        if (!$this->template) {
            $this->template = BatchPdfExport::$plugin->batchPdfExportService->getDefaultTemplate();
        }
    }

    /**
     * @inheritdoc
     */
    public function getTriggerLabel(): string
    {
        return $this->label;
    }

    public function getTriggerHtml()
    {
        $type     = Json::encode(static::class);
        $template = $this->template;
        $js       = <<<EOD
(function()
{
    var trigger = new BatchPDFActionTrigger({
        type: {$type},
        batch: true,
        label: '{$this->label}',
        activate: function(\$selectedItems)
        {
            var idInputs = \$selectedItems
                .map(function(index, element) {
                    return '<input type="hidden" name="ids[]" value="' + \$(element).data('id') + '" />';
                })
                .get()
                .join('');
                
            var form = $('<form method="post" target="_blank" action="">' +
            '<input type="hidden" name="action" value="batch-pdf-export/default/index" />' +
            idInputs +
            '<input type="hidden" name="{csrfName}" value="{csrfValue}" />' +
            '<input type="hidden" name="template" value="{$template}" />' +
            '<input type="submit" value="Submit" />' +
            '</form>');
            
            form.appendTo('body');
            form.submit();
            form.remove();
        }
    });
})();
EOD;

        $js = \str_replace([
            '{csrfName}',
            '{csrfValue}',
        ], [
            Craft::$app->getConfig()->getGeneral()->csrfTokenName,
            Craft::$app->getRequest()->getCsrfToken(),
        ], $js);

        Craft::$app->getView()->registerJs($js);
    }

    /**
     * @inheritdoc
     */
    /*public function performAction(ElementQueryInterface $query): bool
    {
        BatchPdfExport::$plugin->batchPdfExportService->generatePdfs($query->all());

        return true;
    }*/
}
