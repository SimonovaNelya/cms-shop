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
?>

<? $pjax = \skeeks\cms\modules\admin\widgets\Pjax::begin(); ?>

<?php echo $this->render('_search', [
    'searchModel' => $searchModel,
    'dataProvider' => $dataProvider
]); ?>

<?= \skeeks\cms\modules\admin\widgets\GridViewStandart::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'pjax' => $pjax,
    'adminController' => \Yii::$app->controller,

    'columns' =>
        [
            'id',

            [
                'attribute' => 'coupon',
            ],

            [
                'filter' => (array)\yii\helpers\ArrayHelper::map(\skeeks\cms\shop\models\ShopDiscount::find()->all(),
                    'id', 'name'),
                'attribute' => 'shop_discount_id',
                'value' => function (\skeeks\cms\shop\models\ShopDiscountCoupon $shopDiscountCoupon) {
                    return $shopDiscountCoupon->shopDiscount->name;
                },
            ],
            /*
                        [
                            'attribute'     => 'value',
                            'class'         => \yii\grid\DataColumn::className(),
                            'value' => function(\skeeks\cms\shop\models\ShopDiscount $shopDiscount)
                            {
                                if ($shopDiscount->value_type == \skeeks\cms\shop\models\ShopDiscount::VALUE_TYPE_P)
                                {
                                    return \Yii::$app->formatter->asPercent($shopDiscount->value / 100);
                                } else
                                {
                                    $money = \skeeks\modules\cms\money\Money::fromString((string) $shopDiscount->value, $shopDiscount->currency_code);
                                    return \Yii::$app->money->intlFormatter()->format($money);
                                }
                            },
                        ],*/

            [
                'attribute' => 'is_active',
                'class' => \skeeks\cms\grid\BooleanColumn::className(),
            ],

            [
                'attribute' => 'active_from',
                'class' => \skeeks\cms\grid\DateTimeColumnData::className(),
            ],

            [
                'attribute' => 'active_to',
                'class' => \skeeks\cms\grid\DateTimeColumnData::className(),
            ],

            [
                'class' => \skeeks\cms\grid\CreatedAtColumn::className()
            ],
        ]
]); ?>

<? $pjax::end(); ?>
