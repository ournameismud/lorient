<?php
/**
 * Lorient plugin for Craft CMS 3.x
 *
 * Custom functionality for Lorient CMS
 *
 * @link      http://ournameismud.co.uk/
 * @copyright Copyright (c) 2018 @cole007
 */

namespace ournameismud\lorient\records;

use ournameismud\lorient\Lorient;

use Craft;
use craft\db\ActiveRecord;

/**
 * @author    @cole007
 * @package   Lorient
 * @since     0.0.1
 */
class Favourites extends ActiveRecord
{
    // Public Static Methods
    // =========================================================================

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%lorient_favourites}}';
    }
}
