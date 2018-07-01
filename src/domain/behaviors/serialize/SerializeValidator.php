<?php
namespace yii2lab\db\domain\behaviors\serialize;

use yii\base\InvalidParamException;
use yii\db\BaseActiveRecord;
use yii\validators\Validator;

class SerializeValidator extends Validator
{
    /**
     * @var bool
     */
    public $merge = false;

    /**
     * Map json error constant to message
     * @see: http://php.net/manual/ru/json.constants.php
     * @var array
     */
    public $errorMessages = [];

    /**
     * @inheritdoc
     */
    public function validateAttribute($model, $attribute)
    {
        $value = $model->$attribute;
        if (!$value instanceof SerializeField) {
            try {
                $new = new SerializeField($value);
                if ($this->merge) {
                    /** @var BaseActiveRecord $model */
                    $old = new SerializeField($model->getOldAttribute($attribute));
                    $new = new SerializeField(array_merge($old->toArray(), $new->toArray()));
                }
                $model->$attribute = $new;
            } catch (InvalidParamException $e) {
                $this->addError($model, $attribute, $this->getErrorMessage($e));
                $model->$attribute = new SerializeField();
            }
        }
    }

    /**
     * @param \Exception $exception
     * @return string
     */
    protected function getErrorMessage($exception)
    {
        $code = $exception->getCode();
        if (isset($this->errorMessages[$code])) {
            return $this->errorMessages[$code];
        }
        return $exception->getMessage();
    }
}
