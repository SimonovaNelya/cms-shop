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
if ($content_id = \Yii::$app->request->get('content_id'))
{
    $dataProvider->query->andWhere(['content_id' => $content_id]);
}


$autoColumns = [];
$model = \skeeks\cms\models\CmsContentElement::find()->where(['content_id' => $content_id])->one();

if (is_array($model) || is_object($model))
{
    foreach ($model as $name => $value) {
        $autoColumns[] = [
            'attribute' => $name,
            'visible' => false,
            'format' => 'raw',
            'class' => \yii\grid\DataColumn::className(),
            'value' => function($model, $key, $index) use ($name)
            {
                if (is_array($model->{$name}))
                {
                    return implode(",", $model->{$name});
                } else
                {
                    return $model->{$name};
                }
            },
        ];
    }

    $searchRelatedPropertiesModel = new \skeeks\cms\models\searchs\SearchRelatedPropertiesModel();
    $searchRelatedPropertiesModel->initCmsContent($model->cmsContent);
    $searchRelatedPropertiesModel->load(\Yii::$app->request->get());
    $searchRelatedPropertiesModel->search($dataProvider);

     /**
     * @var $model \skeeks\cms\models\CmsContentElement
     */
    if ($model->relatedPropertiesModel)
    {
        foreach ($model->relatedPropertiesModel->attributeValues() as $name => $value) {


            $property = $model->relatedPropertiesModel->getRelatedProperty($name);
            $filter = '';

            if ($property->property_type == \skeeks\cms\relatedProperties\PropertyType::CODE_ELEMENT)
            {
                $propertyType = $property->createPropertyType();
                    $options = \skeeks\cms\models\CmsContentElement::find()->active()->andWhere([
                        'content_id' => $propertyType->content_id
                    ])->all();

                    $items = \yii\helpers\ArrayHelper::merge(['' => ''], \yii\helpers\ArrayHelper::map(
                        $options, 'id', 'name'
                    ));

                $filter = \yii\helpers\Html::activeDropDownList($searchRelatedPropertiesModel, $name, $items, ['class' => 'form-control']);

            } else if ($property->property_type == \skeeks\cms\relatedProperties\PropertyType::CODE_LIST)
            {
                $items = \yii\helpers\ArrayHelper::merge(['' => ''], \yii\helpers\ArrayHelper::map(
                    $property->enums, 'id', 'value'
                ));

                $filter = \yii\helpers\Html::activeDropDownList($searchRelatedPropertiesModel, $name, $items, ['class' => 'form-control']);

            } else if ($property->property_type == \skeeks\cms\relatedProperties\PropertyType::CODE_STRING)
            {
                $filter = \yii\helpers\Html::activeTextInput($searchRelatedPropertiesModel, $name, [
                    'class' => 'form-control'
                ]);
            }
            else if ($property->property_type == \skeeks\cms\relatedProperties\PropertyType::CODE_NUMBER)
            {
                $filter = "<div class='row'><div class='col-md-6'>" . \yii\helpers\Html::activeTextInput($searchRelatedPropertiesModel, $searchRelatedPropertiesModel->getAttributeNameRangeFrom($name), [
                                'class' => 'form-control',
                                'placeholder' => 'от'
                            ]) . "</div><div class='col-md-6'>" .
                                \yii\helpers\Html::activeTextInput($searchRelatedPropertiesModel, $searchRelatedPropertiesModel->getAttributeNameRangeTo($name), [
                                'class' => 'form-control',
                                'placeholder' => 'до'
                            ]) . "</div></div>"
                        ;
            }

            $autoColumns[] = [
                'attribute' => $name,
                'label' => \yii\helpers\ArrayHelper::getValue($model->relatedPropertiesModel->attributeLabels(), $name),
                'visible' => false,
                'format' => 'raw',
                'filter' => $filter,
                'class' => \yii\grid\DataColumn::className(),
                'value' => function($model, $key, $index) use ($name)
                {
                    /**
                     * @var $model \skeeks\cms\models\CmsContentElement
                     */
                    $value = $model->relatedPropertiesModel->getSmartAttribute($name);
                    if (is_array($value))
                    {
                        return implode(",", $value);
                    } else
                    {
                        return $value;
                    }
                },
            ];
        }
    }


}
$userColumns = include_once __DIR__ . "/_columns.php";

$columns = \yii\helpers\ArrayHelper::merge($userColumns, $autoColumns);

?>

<?= \skeeks\cms\modules\admin\widgets\GridViewStandart::widget([
    'dataProvider'      => $dataProvider,
    'filterModel'       => $searchModel,
    'autoColumns'       => false,
    'adminController'   => $controller,
    'settingsData'  =>
    [
        'namespace' => \Yii::$app->controller->action->getUniqueId() . $content_id
    ],
    'columns' => $columns
]); ?>



<? \yii\bootstrap\Alert::begin([
    'options' => [
        'class' => 'alert-info',
    ],
]); ?>
    Изменить свойства и права доступа к информационному блоку вы можете в <?= \yii\helpers\Html::a('Настройках контента', \skeeks\cms\helpers\UrlHelper::construct([
        '/cms/admin-cms-content/update', 'pk' => $content_id
    ])->enableAdmin()->toString()); ?>.
<? \yii\bootstrap\Alert::end(); ?>
