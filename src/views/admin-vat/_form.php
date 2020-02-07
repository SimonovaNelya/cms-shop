<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 28.08.2015
 */

use skeeks\cms\modules\admin\widgets\form\ActiveFormUseTab as ActiveForm;

/* @var $this yii\web\View */
?>

<?php $form = ActiveForm::begin(); ?>

<?= $form->fieldSet(\skeeks\cms\shop\Module::t('app', 'Main')); ?>


<?= $form->field($model, 'name')->textInput(['maxlength' => 255]); ?>
<?= $form->field($model, 'active')->checkbox([
    'uncheck' => \skeeks\cms\components\Cms::BOOL_N,
    'value'   => \skeeks\cms\components\Cms::BOOL_Y,
]); ?>

<?= $form->field($model, 'rate')->textInput(); ?>
<?= $form->field($model, 'priority'); ?>

<?= $form->fieldSetEnd(); ?>

<?= $form->buttonsStandart($model); ?>
<?php ActiveForm::end(); ?>
