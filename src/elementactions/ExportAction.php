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

    /**
     * @inheritdoc
     */
    public function getTriggerLabel(): string
    {
        return Craft::t('batch-pdf-export', 'Export PDFs');
    }

    public function getTriggerHtml()
    {
        $type = Json::encode(static::class);
        $js = <<<EOD
(function()
{
    var trigger = new Craft.ElementActionTrigger({
        type: {$type},
        batch: true,
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
