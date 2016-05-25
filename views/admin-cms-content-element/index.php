<?php
/**
 * @author Semenov Alexander <semenov@skeeks.com>
 * @link http://skeeks.com/
 * @copyright 2010 SkeekS (СкикС)
 * @date 02.06.2015
 */
/* @var $this yii\web\View */
/* @var $searchModel \skeeks\cms\models\Search */
/* @var $dataProvider yii\data\ActiveDataProvider */


$dataProvider->setSort(['defaultOrder' => ['published_at' => SORT_DESC]]);

$cmsContent = null;
if ($content_id = \Yii::$app->request->get('content_id'))
{
    $dataProvider->query->andWhere(['content_id' => $content_id]);
    /**
     * @var $cmsContent \skeeks\cms\models\CmsContent
     */
    $cmsContent = \skeeks\cms\models\CmsContent::findOne($content_id);
}
$columns = \skeeks\cms\shop\controllers\AdminCmsContentElementController::getColumns($cmsContent, $dataProvider);
?>

<? $pjax = \yii\widgets\Pjax::begin(); ?>

    <? $form = \skeeks\cms\modules\admin\widgets\filters\AdminFiltersForm::begin([
        'action' => '/' . \Yii::$app->request->pathInfo,
        'namespace' => \Yii::$app->controller->uniqueId . "_" . $content_id
    ]); ?>

        <?= \yii\helpers\Html::hiddenInput('content_id', $content_id) ?>

        <?= $form->field($searchModel, 'id'); ?>

        <?= $form->field($searchModel, 'q')->textInput([
            'placeholder' => \Yii::t('skeeks/cms', 'Search name and description')
        ])->setVisible(); ?>

        <?= $form->field($searchModel, 'name')->textInput([
            'placeholder' => \Yii::t('skeeks/cms', 'Search by name')
        ]) ?>

        <?= $form->field($searchModel, 'active')->listBox(\yii\helpers\ArrayHelper::merge([
            '' => ' - '
        ], \Yii::$app->cms->booleanFormat()), [
            'size' => 1
        ]); ?>

        <?= $form->field($searchModel, 'section')->listBox(\yii\helpers\ArrayHelper::merge([
            '' => ' - '
        ], \skeeks\cms\helpers\TreeOptions::getAllMultiOptions()),
        [
            'unselect' => ' - ',
            'size' => 1
        ]); ?>


        <?= $form->field($searchModel, 'has_image')->checkbox(\Yii::$app->formatter->booleanFormat, false); ?>
        <?= $form->field($searchModel, 'has_full_image')->checkbox(\Yii::$app->formatter->booleanFormat, false); ?>


        <?= $form->field($searchModel, 'created_by')->widget(\skeeks\cms\modules\admin\widgets\formInputs\SelectModelDialogUserInput::className()); ?>
        <?= $form->field($searchModel, 'updated_by')->widget(\skeeks\cms\modules\admin\widgets\formInputs\SelectModelDialogUserInput::className()); ?>


        <?= $form->field($searchModel, 'created_at_from')->widget(
            \kartik\datetime\DateTimePicker::className()
        ); ?>
        <?= $form->field($searchModel, 'created_at_to')->widget(
            \kartik\datetime\DateTimePicker::className()
        ); ?>

        <?= $form->field($searchModel, 'updated_at_from')->widget(
            \kartik\datetime\DateTimePicker::className()
        ); ?>
        <?= $form->field($searchModel, 'updated_at_to')->widget(
            \kartik\datetime\DateTimePicker::className()
        ); ?>

        <?= $form->field($searchModel, 'published_at_from')->widget(
            \kartik\datetime\DateTimePicker::className()
        ); ?>
        <?= $form->field($searchModel, 'published_at_to')->widget(
            \kartik\datetime\DateTimePicker::className()
        ); ?>

        <?= $form->field($searchModel, 'code'); ?>


    <? $form::end(); ?>


    <?= \skeeks\cms\modules\admin\widgets\GridViewStandart::widget([
        'dataProvider'      => $dataProvider,
        'filterModel'       => $searchModel,
        'autoColumns'       => false,
        'pjax'              => $pjax,
        'adminController'   => $controller,
        'settingsData'  =>
        [
            'namespace' => \Yii::$app->controller->action->getUniqueId() . $content_id
        ],
        'columns' => $columns
    ]); ?>

<? $pjax::end() ?>

<? \yii\bootstrap\Alert::begin([
    'options' => [
        'class' => 'alert-info',
    ],
]); ?>
    <?= \Yii::t('skeeks/shop/app','Change the properties and rights of access to information block you can'); ?> <?= \yii\helpers\Html::a(\Yii::t('skeeks/shop/app','Content Settings'), \skeeks\cms\helpers\UrlHelper::construct([
        '/cms/admin-cms-content/update', 'pk' => $content_id
    ])->enableAdmin()->toString()); ?>.
<? \yii\bootstrap\Alert::end(); ?>
