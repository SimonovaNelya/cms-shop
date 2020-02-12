<?php
/**
 * @link https://cms.skeeks.com/
 * @copyright Copyright (c) 2010 SkeekS
 * @license https://cms.skeeks.com/license/
 * @author Semenov Alexander <semenov@skeeks.com>
 */

namespace skeeks\cms\shop\models;

use skeeks\cms\base\ActiveRecord;
use skeeks\cms\models\CmsContentElement;
use skeeks\cms\models\CmsContentProperty;
use skeeks\cms\models\CmsContentPropertyEnum;
use yii\helpers\ArrayHelper;
/**
 *
 * This is the model class for table "shop_supplier_property_option".
 *
 * @property int                    $id
 * @property int                    $shop_supplier_property_id
 * @property string                 $name
 * @property int|null               $cms_content_property_enum_id
 * @property int|null               $cms_content_element_id
 *
 * @property CmsContentElement      $cmsContentElement
 * @property CmsContentPropertyEnum $cmsContentPropertyEnum
 * @property ShopSupplierProperty $shopSupplierProperty
 *
 * @author Semenov Alexander <semenov@skeeks.com>
 */
class ShopSupplierPropertyOption extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%shop_supplier_property_option}}';
    }


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [

            [['shop_supplier_property_id', 'name'], 'required'],
            [['shop_supplier_property_id', 'cms_content_property_enum_id', 'cms_content_element_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['shop_supplier_property_id', 'name'], 'unique', 'targetAttribute' => ['shop_supplier_property_id', 'name']],
            [['cms_content_element_id'], 'exist', 'skipOnError' => true, 'targetClass' => CmsContentElement::className(), 'targetAttribute' => ['cms_content_element_id' => 'id']],
            [['cms_content_property_enum_id'], 'exist', 'skipOnError' => true, 'targetClass' => CmsContentPropertyEnum::className(), 'targetAttribute' => ['cms_content_property_enum_id' => 'id']],

        ]);
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'id' => 'ID',
            'shop_supplier_property_id' => 'Свойство',
            'name' => 'Название',
            'cms_content_property_enum_id' => 'Cms Content Property Enum ID',
            'cms_content_element_id' => 'Cms Content Element ID',
        ]);
    }

    /**
     * Gets query for [[CmsContentElement]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCmsContentElement()
    {
        return $this->hasOne(CmsContentElement::className(), ['id' => 'cms_content_element_id']);
    }

    /**
     * Gets query for [[CmsContentPropertyEnum]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCmsContentPropertyEnum()
    {
        return $this->hasOne(CmsContentPropertyEnum::className(), ['id' => 'cms_content_property_enum_id']);
    }

    /**
     * Gets query for [[ShopSupplierProperty]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getShopSupplierProperty()
    {
        return $this->hasOne(ShopSupplierProperty::className(), ['id' => 'shop_supplier_property_id']);
    }
}