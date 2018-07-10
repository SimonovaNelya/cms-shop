<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 * @author Semenov Alexander <semenov@skeeks.com>
 */

use yii\helpers\Html;
use skeeks\cms\modules\admin\widgets\form\ActiveFormUseTab as ActiveForm;

/* @var $this yii\web\View */
/* @var $action \skeeks\cms\backend\actions\BackendModelUpdateAction */
$action = $this->context->action;
?>

<?php $form = $action->beginDynamicActiveForm(); ?>
<?= $form->errorSummary($model); ?>
<?= $form->fieldSet(\Yii::t('skeeks/shop/app', 'Main')); ?>

<?= $form->fieldCheckboxBoolean($model, 'active'); ?>
<?= $form->field($model, 'name')->textInput(); ?>

<?= $form->fieldSelect($model, 'site_id', \yii\helpers\ArrayHelper::map(
    \skeeks\cms\models\CmsSite::find()->all(), 'id', 'name'
)); ?>

<?= $form->fieldSelect($model, 'value_type', \skeeks\cms\shop\models\ShopDiscount::getValueTypes()); ?>
<?= $form->field($model, 'value')->textInput(); ?>

<?= $form->fieldSelect($model, 'currency_code', \yii\helpers\ArrayHelper::map(
    \skeeks\cms\money\models\MoneyCurrency::find()->andWhere(['is_active' => true])->all(), 'code', 'code'
)); ?>

<?= $form->field($model, 'max_discount')->textInput(); ?>

<?= $form->fieldInputInt($model, 'priority'); ?>
<?= $form->fieldCheckboxBoolean($model, 'last_discount'); ?>
<?= $form->field($model, 'notes')->textarea(['rows' => 3]); ?>

<?= $form->fieldSetEnd(); ?>

<?= $form->fieldSet(\Yii::t('skeeks/shop/app', 'Conditions')); ?>

<?= $form->field($model, 'conditions')->widget(
    \skeeks\cms\shop\widgets\discount\DiscountConditionsWidget::class,
    [
        'options' => [
            $action->reloadFieldParam => 'true'
        ]
    ]
); ?>

<?= $form->fieldSetEnd(); ?>


<?= $form->fieldSet(\Yii::t('skeeks/shop/app', 'Limitations')); ?>

<?= $form->field($model, 'typePrices')->checkboxList(\yii\helpers\ArrayHelper::map(
    \skeeks\cms\shop\models\ShopTypePrice::find()->all(), 'id', 'name'
))->hint(\Yii::t('skeeks/shop/app', 'if nothing is selected, it means all')); ?>


<? \yii\bootstrap\Alert::begin([
    'options' => [
        'class' => 'alert-warning',
    ],
]); ?>
<?= \Yii::t('skeeks/shop/app',
    '<b> Warning! </b> Permissions are stored in real time. Thus, these settings are independent of site or user.'); ?>
<? \yii\bootstrap\Alert::end() ?>

<?= \skeeks\cms\rbac\widgets\adminPermissionForRoles\AdminPermissionForRolesWidget::widget([
    'permissionName' => $model->permissionName,
    'notClosedRoles' => [],
    'permissionDescription' => \Yii::t('skeeks/shop/app',
            'Groups of users who can benefit from discounted rates') . ": '{$model->name}'",
    'label' => \Yii::t('skeeks/shop/app', 'Groups of users who can benefit from discounted rates'),
]); ?>

<?= $form->fieldSetEnd(); ?>


<?= $form->buttonsCreateOrUpdate($model); ?>
<?= $form->errorSummary($model); ?>
<?php $action->endActiveForm(); ?>
