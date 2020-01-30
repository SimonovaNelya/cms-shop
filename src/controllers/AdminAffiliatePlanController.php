<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 28.08.2015
 */

namespace skeeks\cms\shop\controllers;

use skeeks\cms\models\CmsAgent;
use skeeks\cms\modules\admin\controllers\AdminModelEditorController;
use skeeks\cms\shop\models\ShopAffiliatePlan;

/**
 * @author Semenov Alexander <semenov@skeeks.com>
 */
class AdminAffiliatePlanController extends AdminModelEditorController
{
    public function init()
    {
        $this->name = \Yii::t('skeeks/shop/app', 'Plans affiliate commissions');
        $this->modelShowAttribute = "name";
        $this->modelClassName = ShopAffiliatePlan::class;

        parent::init();
    }

}
