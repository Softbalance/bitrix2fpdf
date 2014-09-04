<?
//вот тут хорошая статья с пошаговым разбором
//http://ruseller.com/lessons.php?rub=37&id=712

//чтобы посмотреть как это работает - создайте файл с именем /bitrix/admin/reports/order2pdf.php
//и вставьте в него код. После чего откройте любой заказ и нажмите на "Печать заказа", выберите order2pdf.php, все.

require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

$MESS["SB_CUSTOM_PRINT_FORM_ORDER_TABLE_TITLE"]="ТОВАРНЫЙ ЧЕК №";
$MESS["SB_CUSTOM_PRINT_FORM_ORDER_TABLE_DATE"]="ДАТА:";
$MESS["SB_CUSTOM_PRINT_FORM_ORDER_TABLE_WHO"]="КОМУ:";
$MESS["SB_CUSTOM_PRINT_FORM_ORDER_TABLE_CONTACT"]="E-mail/телефон:";
$MESS["SB_CUSTOM_PRINT_FORM_ORDER_TABLE_LOCATION"]="Местоположение:";
$MESS["SB_CUSTOM_PRINT_FORM_ORDER_TABLE_ADRESS"]="Адрес:";

$MESS["SB_CUSTOM_PRINT_FORM_PRODUCT_TABLE_SUMMA"]="Сумма:";
$MESS["SB_CUSTOM_PRINT_FORM_PRODUCT_TABLE_DELIVERY"]="Стоимость доставки:";
$MESS["SB_CUSTOM_PRINT_FORM_PRODUCT_TABLE_BONUS_PAYED"]="Оплачено бонусами:";
$MESS["SB_CUSTOM_PRINT_FORM_PRODUCT_TABLE_TOTAL"]="Итого:";

$MESS["SB_CUSTOM_PRINT_FORM_NUMBER"]="№";
$MESS["SB_CUSTOM_PRINT_FORM_ARTICUL"]="Артикул";
$MESS["SB_CUSTOM_PRINT_FORM_PRODUCT_NAME"]="Наименование";
$MESS["SB_CUSTOM_PRINT_FORM_PRODUCT_QUANTITY"]="Количество";
$MESS["SB_CUSTOM_PRINT_FORM_PRODUCT_PRICE"]="Цена, руб.";
$MESS["SB_CUSTOM_PRINT_FORM_PRODUCT_SUMM"]="Сумма, руб.";

CModule::IncludeModule("sale");

global $APPLICATION;
$APPLICATION->RestartBuffer();

$order=$ORDER_ID;
$PAYER_NAME = "";
$db_order = CSaleOrder::GetList(
	array("DATE_UPDATE" => "DESC"),
	array("ID"=>$order)
);
if ($getOrder = $db_order->Fetch())
{
	$comment = $getOrder["USER_DESCRIPTION"] ? $getOrder["USER_DESCRIPTION"] : "";
	$db_props = CSaleOrderProps::GetList(
		array("NAME" => "ASC"),
		array(
			"PERSON_TYPE_ID" => $getOrder["PERSON_TYPE_ID"],
			"IS_PAYER" => "Y"
		)
	);
	if($arProps = $db_props->Fetch())
	{
		$db_vals = CSaleOrderPropsValue::GetList(
			array("NAME" => "ASC"),
			array(
				"ORDER_ID" => $order,
				"ORDER_PROPS_ID" =>array()
			)
		);
		while($arVals = $db_vals->Fetch()){
			$props[$arVals["CODE"]]=$arVals;
		}
	}
}
$location=CSaleLocation::GetByID($props["location"]["VALUE"],LANGUAGE_ID);
$locationFormat=$location["COUNTRY_NAME"]." ".$location["REGION_NAME"]." ".$location["CITY_NAME"];

//проверяем наличие класса
if (!CSalePdf::isPdfAvailable())
	die();

$pdf = new CSalePdf("P", "pt", "A4");
$pdf->AddFont("Font", "", "pt_sans-regular.ttf", true);
$pdf->AddFont("Font", "B", "pt_sans-bold.ttf", true);
$fontFamily = "Font";
$fontSize   = 9;


$pdf->AddPage();
$debug = "";//"(".$arOrder["ID"].")";

/********* DEBUG ****************************/
$pdf->SetFont($fontFamily, "", $fontSize-1);
$pdf->Cell(0, 15,$debug, 0, 0, "L");
$pdf->Ln();
/********************************************/


/********************/
$table_tc=array(45,200,250,25);
$pdf->SetFillColor(241,241,241);
$pdf->SetFont($fontFamily,"B",$fontSize);

$pdf->Cell($table_tc[0],15,"",0,0,"L");
$pdf->Cell($table_tc[1],15,GetMessage("SB_CUSTOM_PRINT_FORM_ORDER_TABLE_TITLE"),0,0,"L");
$pdf->Cell($table_tc[2],15,$ORDER_ID,0,0,"L");
$pdf->Cell($table_tc[3],15,"",0,0,"L");
$pdf->Ln();
$pdf->Ln();

$pdf->Cell($table_tc[0],15,"",0,0,"L");
$pdf->Cell($table_tc[1],15,GetMessage("SB_CUSTOM_PRINT_FORM_ORDER_TABLE_DATE"),0,0,"L");
$pdf->Cell($table_tc[2],15,$arOrder["DATE_INSERT_FORMAT"],0,0,"L");
$pdf->Cell($table_tc[3],15,"",0,0,"L");
$pdf->Ln();

$pdf->Cell($table_tc[0],15,"",0,0,"L");
$pdf->Cell($table_tc[1],15,GetMessage("SB_CUSTOM_PRINT_FORM_ORDER_TABLE_WHO"),0,0,"L",true);
$pdf->Cell(250,15,$props["fio"]["VALUE"],0,0,"L",true);
$pdf->Cell($table_tc[3],15,"",0,0,"L");
$pdf->Ln();

$pdf->Cell($table_tc[0],15,"",0,0,"L");
$pdf->Cell($table_tc[1],15,GetMessage("SB_CUSTOM_PRINT_FORM_ORDER_TABLE_CONTACT"),0,0,"L");
$pdf->Cell($table_tc[2],15,$props["email"]["VALUE"]." / ".$props["phone"]["VALUE"],0,0,"L");
$pdf->Cell($table_tc[3],15,"",0,0,"L");
$pdf->Ln();

$pdf->Cell($table_tc[0],15,"",0,0,"L");
$pdf->Cell($table_tc[1],15,GetMessage("SB_CUSTOM_PRINT_FORM_ORDER_TABLE_LOCATION"),0,0,"L",true);
$pdf->Cell($table_tc[2],15,$locationFormat,0,0,"L",true);
$pdf->Cell($table_tc[3],15,"",0,0,"L");
$pdf->Ln();

$pdf->Cell($table_tc[0],15,"",0,0,"L");
$pdf->Cell($table_tc[1],15,GetMessage("SB_CUSTOM_PRINT_FORM_ORDER_TABLE_ADRESS"),0,0,"L");
$pdf->Cell($table_tc[2],15,$props["zip"]["VALUE"]." ".$props["address"]["VALUE"],0,0,"L");
$pdf->Cell($table_tc[3],15,"",0,0,"L");
$pdf->Ln();

$pdf->Ln();
/*******************/



$pdf->SetFont($fontFamily, "", $fontSize);
$columnLabels = array(
	GetMessage("SB_CUSTOM_PRINT_FORM_NUMBER"),
	GetMessage("SB_CUSTOM_PRINT_FORM_ARTICUL"),
	GetMessage("SB_CUSTOM_PRINT_FORM_PRODUCT_NAME"),
	GetMessage("SB_CUSTOM_PRINT_FORM_PRODUCT_QUANTITY"),
	GetMessage("SB_CUSTOM_PRINT_FORM_PRODUCT_PRICE"),
	GetMessage("SB_CUSTOM_PRINT_FORM_PRODUCT_SUMM")
);
$customTableColumSize = array(
	21,
	120,
	220,
	60,
	60,
	60
);
$customTableColumSizeFoot = intval($customTableColumSize[0]+$customTableColumSize[1]+$customTableColumSize[2]+$customTableColumSize[3]+$customTableColumSize[4]);

$pdf->SetTextColor(0,0,0);
$pdf->SetFillColor(241,241,241);
foreach($columnLabels as $i=>$col){
	$pdf->Cell($customTableColumSize[$i],12,$col,1,0,"C",true);
}
$pdf->Ln();


/**************************BASKET LIST*************************/
$priceTotal = 0;
$bUseVat = false;
$arBasketOrder = array();
for ($i = 0; $i < count($arBasketIDs); $i++)
{

	$arBasketTmp = CSaleBasket::GetByID($arBasketIDs[$i]);

	$res = CIBlockElement::GetByID($arBasketTmp["PRODUCT_ID"]);
	if($ar_res = $res->GetNext())
		$db_props = CIBlockElement::GetProperty($ar_res["IBLOCK_ID"],$ar_res["ID"],array(),array("CODE"=>"CML2_ARTICLE"));

	if($ar_props = $db_props->Fetch())
		$arBasketTmp["CML2_ARTICLE"]=$ar_props["VALUE"];


	if (floatval($arBasketTmp["VAT_RATE"]) > 0 )
		$bUseVat = true;

	$priceTotal += $arBasketTmp["PRICE"]*$arBasketTmp["QUANTITY"];

	$arBasketTmp["PROPS"] = array();
	if (isset($_GET["PROPS_ENABLE"]) && $_GET["PROPS_ENABLE"] == "Y")
	{
		$dbBasketProps = CSaleBasket::GetPropsList(
			array("SORT" => "ASC", "NAME" => "ASC"),
			array("BASKET_ID" => $arBasketTmp["ID"]),
			false,
			false,
			array("ID", "BASKET_ID", "NAME", "VALUE", "CODE", "SORT")
		);
		while ($arBasketProps = $dbBasketProps->GetNext())
			$arBasketTmp["PROPS"][$arBasketProps["ID"]] = $arBasketProps;
	}

	$arBasketOrder[] = $arBasketTmp;
}


//разбрасываем скидку на заказ по товарам
if (floatval($arOrder["DISCOUNT_VALUE"]) > 0)
{
	$arBasketOrder = GetUniformDestribution($arBasketOrder, $arOrder["DISCOUNT_VALUE"], $priceTotal);
}

//налоги
$arTaxList = array();
$db_tax_list = CSaleOrderTax::GetList(array("APPLY_ORDER"=>"ASC"), Array("ORDER_ID"=>$ORDER_ID));
$iNds = -1;
$i = 0;
while ($ar_tax_list = $db_tax_list->Fetch())
{
	$arTaxList[$i] = $ar_tax_list;
	// определяем, какой из налогов - НДС
	// НДС должен иметь код NDS, либо необходимо перенести этот шаблон
	// в каталог пользовательских шаблонов и исправить
	if ($arTaxList[$i]["CODE"] == "NDS")
		$iNds = $i;
	$i++;
}

$i = 0;
$total_sum = 0;
foreach ($arBasketOrder as $cell=>$arBasket)
{
	$nds_val = 0;
	$taxRate = 0;

	if (floatval($arQuantities[$i]) <= 0)
		$arQuantities[$i] = DoubleVal($arBasket["QUANTITY"]);

	$b_AMOUNT = DoubleVal($arBasket["PRICE"]);

	//определяем начальную цену
	$item_price = $b_AMOUNT;

	if(DoubleVal($arBasket["VAT_RATE"]) > 0)
	{
		$nds_val = ($b_AMOUNT - DoubleVal($b_AMOUNT/(1+$arBasket["VAT_RATE"])));
		$item_price = $b_AMOUNT - $nds_val;
		$taxRate = $arBasket["VAT_RATE"]*100;
	}
	elseif(!$bUseVat)
	{
		$basket_tax = CSaleOrderTax::CountTaxes($b_AMOUNT*$arQuantities[$i], $arTaxList, $arOrder["CURRENCY"]);

		for ($mi = 0; $mi < count($arTaxList); $i++)
		{
			if ($arTaxList[$mi]["IS_IN_PRICE"] == "Y")
			{
				$item_price -= $arTaxList[$mi]["TAX_VAL"];
			}
			$nds_val += DoubleVal($arTaxList[$mi]["TAX_VAL"]);
			$taxRate += ($arTaxList[$mi]["VALUE"]);
		}
	}

	$pdf->Cell($customTableColumSize[0],15,++$cell,1,0,"C",($cell%2==0)?true:false);
	$pdf->Cell($customTableColumSize[1],15,$arBasket["CML2_ARTICLE"],1,0,"L",($cell%2==0)?true:false);
	$pdf->Cell($customTableColumSize[2],15,htmlspecialcharsbx($arBasket["NAME"]),1,0,"L",($cell%2==0)?true:false);
	$pdf->Cell($customTableColumSize[3],15,$arQuantities[$i],1,0,"C",($cell%2==0)?true:false);
	$pdf->Cell($customTableColumSize[4],15,number_format($arBasket["PRICE"], 2, ",", " "),1,0,"R",($cell%2==0)?true:false);
	$pdf->Cell($customTableColumSize[5],15,number_format($arBasket["PRICE"]*$arQuantities[$i], 2, ",", " "),1,0,"R",($cell%2==0)?true:false);
	$pdf->Ln();

	$total_sum += $arBasket["PRICE"]*$arQuantities[$i];
	$total_nds += $nds_val*$arQuantities[$i];

	$i++;
}

++$cell;
$pdf->Cell($customTableColumSizeFoot,15,GetMessage("SB_CUSTOM_PRINT_FORM_PRODUCT_TABLE_SUMMA"),1,0,"R",($cell%2==0)?true:false);
$pdf->Cell($customTableColumSize[5],15,number_format($total_sum, 2, ",", " "),1,0,"R",($cell%2==0)?true:false);
$pdf->Ln();

++$cell;
$pdf->Cell($customTableColumSizeFoot,15,GetMessage("SB_CUSTOM_PRINT_FORM_PRODUCT_TABLE_DELIVERY"),1,0,"R",($cell%2==0)?true:false);
$pdf->Cell($customTableColumSize[5],15,$getOrder["PRICE_DELIVERY"],1,0,"R",($cell%2==0)?true:false);
$pdf->Ln();

if($getOrder["SUM_PAID"]>0){
	++$cell;
	$pdf->Cell($customTableColumSizeFoot,15,GetMessage("SB_CUSTOM_PRINT_FORM_PRODUCT_TABLE_BONUS_PAYED"),1,0,"R",($cell%2==0)?true:false);
	$pdf->Cell($customTableColumSize[5],15,$getOrder["SUM_PAID"],1,0,"R",($cell%2==0)?true:false);
	$pdf->Ln();
}

++$cell;
$pdf->Cell($customTableColumSizeFoot,15,GetMessage("SB_CUSTOM_PRINT_FORM_PRODUCT_TABLE_TOTAL"),1,0,"R",($cell%2==0)?true:false);
$pdf->Cell($customTableColumSize[5],15,number_format(($total_sum+$getOrder["PRICE_DELIVERY"]), 2, ",", " "),1,0,"R",($cell%2==0)?true:false);
$pdf->Ln();

$dest = "I";
if ($_REQUEST["GET_CONTENT"] == "Y")
	$dest = "S";
else if ($_REQUEST["DOWNLOAD"] == "Y")
	$dest = "D";

return $pdf->Output(
	sprintf(
		"Fspinning.D".$arOrder["DATE_INSERT_FORMAT"].".N".$ORDER_ID.".pdf",
		$GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["ACCOUNT_NUMBER"],
		ConvertDateTime($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["DATE_INSERT"], "YYYY-MM-DD")
	), $dest
);
?>