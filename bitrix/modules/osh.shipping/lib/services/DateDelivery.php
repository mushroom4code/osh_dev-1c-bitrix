<?php

namespace Osh\Delivery\Services;

use Bitrix\Main\ArgumentException;
use Bitrix\Main\ArgumentNullException;
use Bitrix\Main\ObjectPropertyException;
use Bitrix\Main\SystemException;
use Bitrix\Sale\Delivery\ExtraServices\Base as ExtraServicesBase,
    Bitrix\Sale\Delivery\ExtraServices\Table as DEST,
    Bitrix\Main\Localization\Loc,
    Bitrix\Main\Page\Asset,
    Bitrix\Main\Loader;

Loader::includeModule("sale");
Loc::loadMessages(__FILE__);
//use Bitrix\Sale\Delivery\CalculationResult;
//use Bitrix\Sale\Delivery\Services\Base;

class DateDelivery extends ExtraServicesBase
{
    /**
     * @param $id
     * @param array $structure
     * @param $currency
     * @param null $value
     * @param array $additionalParams
     * @throws ArgumentNullException
     */
    public function __construct($id, array $structure = [], $currency = '', $value = null, array $additionalParams = array())
    {
        $structure["PARAMS"]["ONCHANGE"] = $this->createJSOnchange($id, $structure["PARAMS"]["PRICE"]);
        $structure["PARAMS"]["ONCLICK"] = $this->createJSOnclick($id, $structure["PARAMS"]["PRICE"]);
        parent::__construct($id, $structure, $currency, $value);
        $this->params["ID"] = "_sh_date_delivery";
        $this->params["TYPE"] = "STRING";
        $this->params["READONLY"] = true;
        $this->params["TITLE"] = Loc::getMessage("OSH_DATE_DELIVERY_LIMITS");
        $this->initial = date("d.m.Y", strtotime("+1day"));
        $this->value = date("d.m.Y", strtotime("+1day"));
        if (empty($this->value)) {
            $this->value = $this->initial;
        }
    }

    /**
     * @return string
     */
    public static function getClassTitle(): string
    {
        return Loc::getMessage("OSH_DATE_DELIVERY_NAME");
    }

    /**
     * @return string
     */
    public static function getAdminParamsName(): string
    {
        return Loc::getMessage("OSH_DATE_DELIVERY_COST");
    }

    /**
     * @param $value
     */
    public function setValue($value)
    {
        $this->value = (string)($value);
    }

    /**
     * @param $name
     * @param array $params
     * @param string $currency
     * @return string
     */
    public static function getAdminParamsControl($name, array $params = array(), $currency = "")
    {
        if (!empty($params["PARAMS"]["PRICE"]))
            $price = roundEx(floatval($params["PARAMS"]["PRICE"]), SALE_VALUE_PRECISION);
        else
            $price = 0;

        return '<input type="text" name="' . $name . '[PARAMS][PRICE]" value="' . $price . '">' .
            (strlen($currency) > 0 ? " (" . htmlspecialcharsbx($currency) . ")" : "");
    }

    /**
     * @param string $prefix
     * @param false $value
     * @return string
     */
    public function getEditControl($prefix = "", $value = false)
    {
        $result = parent::getEditControl($prefix, $value);
        $arDaysOff = $this->getDaysOff();
        $sDaysOff = "['" . implode("','", $arDaysOff) . "']";
        $restrictText = Loc::getMessage("OSH_DATE_DELIVERY_TIME_RESTRICTION");
        $cAsset = Asset::getInstance();
        $scriptJs = <<<JS
            <script type="text/javascript">
                BX.ready(function(){
                    var shDaysOff = {$sDaysOff},
                        eTimeDelivery = document.querySelector('#_sh_time_delivery'),
                        eDateDelivery = document.querySelector('#_sh_date_delivery');
                    if(!!eDateDelivery && !!eTimeDelivery){
                        if(shDaysOff.indexOf(eDateDelivery.value) !== -1){
                            eTimeDelivery.disabled = true;
                            eTimeDelivery.title = '{$restrictText}';
                            eTimeDelivery.value = 0;
                        }else{
                            eTimeDelivery.disabled = false;
                            eTimeDelivery.title = '';
                        }
                    }
                });
            </script>
        JS;
        $cAsset->addString($scriptJs);
        return $result;
    }

    /**
     * @param $currency
     */
    public function setOperatingCurrency($currency)
    {
        $this->params["ONCHANGE"] = $this->createJSOnchange($this->id, $this->getPrice());
        parent::setOperatingCurrency($currency);
    }

    /**
     * @param $id
     * @param $price
     * @return string
     */
    protected function createJSOnchange($id, $price)
    {
        $arDaysOff = $this->getDaysOff();
        $sDaysOff = "['" . implode("','", $arDaysOff) . "']";
        $restrictText = Loc::getMessage("OSH_DATE_DELIVERY_TIME_RESTRICTION");
        return <<<JS
        var shDaysOff = {$sDaysOff},
            eTimeDelivery = document.querySelector('#_sh_time_delivery');
        if(!!eTimeDelivery){
            if(shDaysOff.indexOf(this.value) !== -1){
                eTimeDelivery.disabled = true;
                eTimeDelivery.title = '{$restrictText}';
                eTimeDelivery.value = 0;
            }else{
                eTimeDelivery.disabled = false;
                eTimeDelivery.title = '';
            }
        }
        BX.onCustomEvent('onDeliveryExtraServiceValueChange', [
            {'id' : '{$id}', 'value': this.value, 'price': this.value ? '{$price}' : '0'}]);
        JS;
    }

    /**
     * @param $id
     * @param $price
     * @return string
     */
    protected function createJSOnclick($id, $price)
    {
        return <<<JS
        BX.calendar({node:this,field:this,bTime:false,value: new Date(),callback:function(date){
                var dayAhead = new Date();
                dayAhead.setHours(0,0,0,0);
                dayAhead.setDate(dayAhead.getDate() + 1);
            var sevenDaysAhead = new Date();
                sevenDaysAhead.setHours(0,0,0,0);
                sevenDaysAhead.setDate(sevenDaysAhead.getDate() + 8);
            var picked = date.getTime();
            if(date.getTime() < dayAhead.getTime()){
                var val = dayAhead.getDate()+'.'+(dayAhead.getMonth()+1)+'.'+dayAhead.getFullYear();
                this.Close();
                BX.onCustomEvent('onDeliveryExtraServiceValueChange', 
                [{'id' : '{$id}', 'value': val, 'price': val ? '{$price}' : '0'}]);
                return false;
            }
            if(date.getTime() > sevenDaysAhead.getTime()){
                var val = dayAhead.getDate()+'.'+(dayAhead.getMonth()+1)+'.'+dayAhead.getFullYear();
                this.Close();
                BX.onCustomEvent('onDeliveryExtraServiceValueChange', 
                [{'id' : '{$id}', 'value': val, 'price': val ? '{$price}' : '0'}]);
                return false;
            }
        }});
       JS;
    }

    /**
     * @param $deliveryId
     * @return int
     * @throws ArgumentException
     * @throws ObjectPropertyException
     * @throws SystemException
     */
    public function getId($deliveryId = 0): int
    {
        $destParams = array(
            "filter" => array("CLASS_NAME" => "%Service%DateDelivery", "ACTIVE" => "Y", "DELIVERY_ID" => $deliveryId),
            "select" => array("ID")
        );
        $res = DEST::getList($destParams)->fetch();
        return intval($res["ID"]);
    }

    /**
     * @return mixed
     */
    public function getDaysOff()
    {
        $from = date("Y-m-d", strtotime("+1day"));
        $till = date("Y-m-d", strtotime("+10day"));
        $arDaysOff = [$from,$till];
//        $arDaysOff = \COshDeliveryHelper::getDaysOff($from, $till);
        if (!empty($arDaysOff)) {
            array_walk($arDaysOff, function (&$item, $key) {
                $item = date("d.m.Y", strtotime($item));
            });
        }
        return $arDaysOff;
    }
}