<?php
/**
 * Batch PDF Export plugin for Craft CMS 3.x
 *
 * Mass export PDF invoices in one go.
 *
 * @link      https://superbig.co
 * @copyright Copyright (c) 2018 Superbig
 */

namespace superbig\batchpdfexport\models;

use superbig\batchpdfexport\BatchPdfExport;

use Craft;
use craft\base\Model;

/**
 * @author    Superbig
 * @package   BatchPdfExport
 * @since     1.0.0
 */
class Settings extends Model
{
    // Public Properties
    // =========================================================================

    /**
     * @var bool
     */
    public $useCustomActions = false;

    /**
     * @var string
     */
    public $defaultLabel = 'Generate Invoices PDF';

    /**
     * @var array
     */
    public $actions = [];

    // Public Methods
    // =========================================================================

    public function init()
    {
        parent::init();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['actions', 'default', 'value' => []],
        ];
    }
}
