<?php


use yii\helpers\Html;
use skeeks\cms\modules\admin\widgets\form\ActiveFormUseTab as ActiveForm;
use skeeks\cms\models\Tree;
use skeeks\cms\modules\admin\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model \skeeks\cms\models\CmsContentElement */
/* @var $relatedModel \skeeks\cms\relatedProperties\models\RelatedPropertiesModel */
/* @var $shopProduct \skeeks\cms\shop\models\ShopProduct */
?>

<?/*= $this->render('@skeeks/cms/views/admin-cms-content-element/_form', [
    'model' => $model
])*/?>

<?php

/* @var $this yii\web\View */
/* @var $model \skeeks\cms\models\CmsContentElement */
/* @var $relatedModel \skeeks\cms\relatedProperties\models\RelatedPropertiesModel */

 if ($model->isNewRecord)
 {
     if ($tree_id = \Yii::$app->request->get("tree_id"))
     {
         $model->tree_id = $tree_id;
     }

     if ($parent_content_element_id = \Yii::$app->request->get("parent_content_element_id"))
     {
         $model->parent_content_element_id = $parent_content_element_id;
     }
 }
?>

<?php $form = ActiveForm::begin(); ?>

<? if ($model->isNewRecord) : ?>
    <? if ($content_id = \Yii::$app->request->get("content_id")) : ?>
        <? $contentModel = \skeeks\cms\models\CmsContent::findOne($content_id); ?>
        <? $model->content_id = $content_id; ?>
        <?= $form->field($model, 'content_id')->hiddenInput(['value' => $content_id])->label(false); ?>
    <? endif; ?>
<? else : ?>
    <? $contentModel = $model->cmsContent; ?>
<? endif; ?>

<? if ($contentModel && $contentModel->parentContent) : ?>
    <?= Html::activeHiddenInput($contentModel, 'parent_content_is_required'); ?>
<? endif; ?>

<?= $form->fieldSet(\Yii::t('app','Main')); ?>


    <?= $form->fieldRadioListBoolean($model, 'active'); ?>
    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'published_at')->widget(\kartik\datecontrol\DateControl::classname(), [
                //'displayFormat' => 'php:d-M-Y H:i:s',
                'type' => \kartik\datecontrol\DateControl::FORMAT_DATETIME,
            ]); ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'published_to')->widget(\kartik\datecontrol\DateControl::classname(), [
                //'displayFormat' => 'php:d-M-Y H:i:s',
                'type' => \kartik\datecontrol\DateControl::FORMAT_DATETIME,
            ]); ?>
        </div>
    </div>
    <?= $form->field($model, 'name')->textInput(['maxlength' => 255]) ?>
    <?= $form->field($model, 'code')->textInput(['maxlength' => 255])->hint(\Yii::t('app',"This parameter affects the address of the page")); ?>

    <? if ($contentModel->parent_content_id) : ?>

        <?= $form->field($model, 'parent_content_element_id')->widget(
            \skeeks\cms\modules\admin\widgets\formInputs\CmsContentElementInput::className()
        )->label($contentModel->parentContent->name_one) ?>
    <? endif; ?>

    <? if ($model->relatedProperties) : ?>
        <?= \skeeks\cms\modules\admin\widgets\BlockTitleWidget::widget([
            'content' => \Yii::t('app', 'Additional properties')
        ]); ?>
        <? if ($properties = $model->relatedProperties) : ?>
            <? foreach ($properties as $property) : ?>
                <?= $property->renderActiveForm($form, $model)?>
            <? endforeach; ?>
        <? endif; ?>

    <? else : ?>
        <?/*= \Yii::t('app','Additional properties are not set')*/?>
    <? endif; ?>
<?= $form->fieldSetEnd()?>






<?= $form->fieldSet(\skeeks\cms\shop\Module::t('app', 'Prices and availability')); ?>

    <?= \skeeks\cms\modules\admin\widgets\BlockTitleWidget::widget([
        'content' => \skeeks\cms\shop\Module::t('app', 'Setting prices')
    ]); ?>

    <?= $form->fieldSelect($shopProduct, 'vat_id', \yii\helpers\ArrayHelper::map(
        \skeeks\cms\shop\models\ShopVat::find()->all(), 'id', 'name'
    )); ?>

    <?= $form->fieldRadioListBoolean($shopProduct, 'vat_included'); ?>

    <?= \skeeks\cms\modules\admin\widgets\BlockTitleWidget::widget([
            'content' => \skeeks\cms\shop\Module::t('app', 'Main prices')
        ])?>

    <div class="row">
        <div class="col-md-3">
            <?= $form->field($shopProduct, 'purchasing_price')->textInput(); ?>
        </div>
        <div class="col-md-2">
            <?= $form->fieldSelect($shopProduct, 'purchasing_currency', \yii\helpers\ArrayHelper::map(
                \Yii::$app->money->activeCurrencies, 'code', 'code'
            )); ?>
        </div>
    </div>


    <div class="row">
        <div class="col-md-3">
            <?= $form->field($shopProduct, 'baseProductPriceValue')->textInput()->label(\skeeks\cms\shop\Module::t('app', 'Base price')." (".\skeeks\cms\shop\Module::t('app', 'Price type')." \"{$shopProduct->baseProductPrice->typePrice->name}\")"); ?>
        </div>
        <div class="col-md-2">
            <?= $form->fieldSelect($shopProduct, 'baseProductPriceCurrency', \yii\helpers\ArrayHelper::map(
                \Yii::$app->money->activeCurrencies, 'code', 'code'
            ))->label(\skeeks\cms\shop\Module::t('app', 'Currency base price')); ?>
        </div>

        <div class="col-md-2">
            <label>&nbsp;</label>
            <p>
                <?= \skeeks\cms\shop\widgets\admin\PropductPriceChangeAdminWidget::widget([
                    'productPrice' => $shopProduct->baseProductPrice
                ])?>
            </p>
        </div>
    </div>

    <? if ($productPrices) : ?>
        <?= \skeeks\cms\modules\admin\widgets\BlockTitleWidget::widget([
            'content' => \skeeks\cms\shop\Module::t('app', 'Additional costs')
        ])?>

        <? foreach ($productPrices as $productPrice) : ?>

            <div class="row">
                <div class="col-md-3">
                    <label><?= $productPrice->typePrice->name; ?></label>
                    <?= Html::textInput("prices[" . $productPrice->typePrice->id . "][price]", $productPrice->price, [
                        'class' => 'form-control'
                    ]); ?>
                </div>
                <div class="col-md-2">
                    <label>Валюта</label>

                    <?= \skeeks\widget\chosen\Chosen::widget([
                        'name' => "prices[" . $productPrice->typePrice->id . "][currency_code]",
                        'value' => $productPrice->currency_code,
                        'allowDeselect' => false,
                        'items' => \yii\helpers\ArrayHelper::map(
                            \Yii::$app->money->activeCurrencies, 'code', 'code'
                        )
                    ])?>
                </div>

                <div class="col-md-2">
                    <label>&nbsp;</label>
                    <p>
                        <?= \skeeks\cms\shop\widgets\admin\PropductPriceChangeAdminWidget::widget([
                            'productPrice' => $productPrice
                        ])?>
                    </p>
                </div>
            </div>

        <? endforeach; ?>

    <? endif; ?>

    <?= \skeeks\cms\modules\admin\widgets\BlockTitleWidget::widget([
        'content' => \skeeks\cms\shop\Module::t('app', 'The number and account')
    ]); ?>

        <?= $form->field($shopProduct, 'quantity')->textInput(); ?>
        <?= $form->field($shopProduct, 'quantity_reserved')->textInput(); ?>

        <?= $form->fieldSelect($shopProduct, 'measure_id', \yii\helpers\ArrayHelper::map(
            \skeeks\cms\measure\models\Measure::find()->all(), 'id', 'name'
        )); ?>

        <?= $form->field($shopProduct, 'measure_ratio')->textInput(); ?>

    <?= \skeeks\cms\modules\admin\widgets\BlockTitleWidget::widget([
        'content' => \skeeks\cms\shop\Module::t('app', 'Weight and size')
    ]); ?>

        <?= $form->fieldInputInt($shopProduct, 'weight'); ?>
        <?= $form->fieldInputInt($shopProduct, 'length'); ?>
        <?= $form->fieldInputInt($shopProduct, 'width'); ?>
        <?= $form->fieldInputInt($shopProduct, 'height'); ?>

    <?= \skeeks\cms\modules\admin\widgets\BlockTitleWidget::widget([
        'content' => \skeeks\cms\shop\Module::t('app', 'Options')
    ]); ?>

        <?= $form->fieldRadioListBoolean($shopProduct, 'quantity_trace'); ?>
        <?= $form->fieldRadioListBoolean($shopProduct, 'can_buy_zero'); ?>
        <?= $form->fieldRadioListBoolean($shopProduct, 'negative_amount_trace'); ?>
        <?= $form->fieldRadioListBoolean($shopProduct, 'subscribe'); ?>

<?= $form->fieldSetEnd() ?>




<?= $form->fieldSet(\Yii::t('app','Announcement')); ?>
    <?= $form->field($model, 'image_id')->widget(
        \skeeks\cms\widgets\formInputs\StorageImage::className()
    ); ?>

    <?= $form->field($model, 'description_short')->widget(
        \skeeks\cms\widgets\formInputs\comboText\ComboTextInputWidget::className(),
        [
            'modelAttributeSaveType' => 'description_short_type',
        ]);
    ?>

<?= $form->fieldSetEnd() ?>

<?= $form->fieldSet(\Yii::t('app','In detal')); ?>

    <?= $form->field($model, 'image_full_id')->widget(
        \skeeks\cms\widgets\formInputs\StorageImage::className()
    ); ?>

    <?= $form->field($model, 'description_full')->widget(
        \skeeks\cms\widgets\formInputs\comboText\ComboTextInputWidget::className(),
        [
            'modelAttributeSaveType' => 'description_full_type',
        ]);
    ?>

<?= $form->fieldSetEnd() ?>

<?= $form->fieldSet(\Yii::t('app','Sections')); ?>


    <? if ($contentModel->root_tree_id) : ?>

        <? if ($contentModel->is_allow_change_tree == \skeeks\cms\components\Cms::BOOL_Y) : ?>
            <?= $form->fieldSelect($model, 'tree_id', \yii\helpers\ArrayHelper::map(
                \skeeks\cms\helpers\TreeOptions::findOne($contentModel->root_tree_id)->getMultiOptions(), 'id', 'name'), [
                    'allowDeselect' => true
                ]
            );
            ?>
        <? endif; ?>

        <?= $form->fieldSelectMulti($model, 'treeIds', \yii\helpers\ArrayHelper::map(
                \skeeks\cms\helpers\TreeOptions::findOne($contentModel->root_tree_id)->getMultiOptions(), 'id', 'name')
            );
        ?>

    <? else : ?>
        <?
            $mode = \skeeks\cms\widgets\formInputs\selectTree\SelectTree::MOD_COMBO;
            if ($contentModel->is_allow_change_tree != \skeeks\cms\components\Cms::BOOL_Y)
            {
                $mode = \skeeks\cms\widgets\formInputs\selectTree\SelectTree::MOD_MULTI;
            }
        ?>
        <?= $form->field($model, 'treeIds')->label(\Yii::t('app','Sections of the site'))->widget(
            \skeeks\cms\widgets\formInputs\selectTree\SelectTree::className(),
            [
                "attributeMulti" => "treeIds",
                "mode" => $mode
            ])->hint(\Yii::t('app','Specify sections of the site, which would like to see this publication'));
        ?>
    <? endif; ?>



<?= $form->fieldSetEnd()?>



<?= $form->fieldSet(\Yii::t('app','SEO')); ?>
    <?= $form->field($model, 'meta_title')->textarea(); ?>
    <?= $form->field($model, 'meta_description')->textarea(); ?>
    <?= $form->field($model, 'meta_keywords')->textarea(); ?>
<?= $form->fieldSetEnd() ?>


<?= $form->fieldSet(\Yii::t('app','Images')); ?>

    <?= $form->field($model, 'images')->widget(
        \skeeks\cms\widgets\formInputs\ModelStorageFiles::className()
    ); ?>

<?= $form->fieldSetEnd()?>


<?= $form->fieldSet(\Yii::t('app','Files')); ?>

    <?= $form->field($model, 'files')->widget(
        \skeeks\cms\widgets\formInputs\ModelStorageFiles::className()
    ); ?>

<?= $form->fieldSetEnd()?>








<? if (!$model->isNewRecord) : ?>
    <?= $form->fieldSet(\Yii::t('app','Additionally')); ?>
        <?= $form->fieldSelect($model, 'content_id', \skeeks\cms\models\CmsContent::getDataForSelect()); ?>
        <?= $form->fieldInputInt($model, 'priority'); ?>

    <?= $form->fieldSetEnd() ?>

    <? if ($model->cmsContent->access_check_element == "Y") : ?>
        <?= $form->fieldSet(\Yii::t('app','Access')); ?>
            <?= \skeeks\cms\widgets\rbac\PermissionForRoles::widget([
                'permissionName'                => $model->permissionName,
                'permissionDescription'         => 'Доступ к этому элементу: ' . $model->name,
                'label'                         => 'Доступ к этому элементу',
            ]); ?>
        <?= $form->fieldSetEnd() ?>
    <? endif; ?>

    <? if ($model->cmsContent->childrenContents) : ?>

        <?
        $columnsFile = \Yii::getAlias('@skeeks/cms/views/admin-cms-content-element/_columns.php');
        /**
         * @var $content \skeeks\cms\models\CmsContent
         */
        ?>
        <? foreach($model->cmsContent->childrenContents as $childContent) : ?>
            <?= $form->fieldSet($childContent->name); ?>



                <?= \skeeks\cms\modules\admin\widgets\RelatedModelsGrid::widget([
                    'label'             => $childContent->name,
                    'parentModel'       => $model,
                    'relation'          => [
                        'content_id'                    => $childContent->id,
                        'parent_content_element_id'     => $model->id
                    ],

                    'sort'              => [
                        'defaultOrder' =>
                        [
                            'priority' => 'published_at'
                        ]
                    ],

                    'controllerRoute'   => 'cms/admin-cms-content-element',
                    'gridViewOptions'   => [
                        'columns' => (array) include $columnsFile
                    ],
                ]); ?>


            <?= $form->fieldSetEnd() ?>
        <? endforeach; ?>
    <? endif; ?>

<? endif; ?>



<?= $form->buttonsStandart($model); ?>
<?php ActiveForm::end(); ?>
