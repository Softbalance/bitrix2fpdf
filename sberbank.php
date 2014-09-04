<?
require($_SERVER["DOCUMENT_ROOT"] . "/bitrix/modules/main/include/prolog_before.php");

CModule::IncludeModule("sale");

global $APPLICATION;
$APPLICATION->RestartBuffer();




$ORDER_ID = urldecode(urldecode($_REQUEST["ORDER_ID"]));
$arOrder = CSaleOrder::GetByID($ORDER_ID);
$dbOrder = CSaleOrder::GetList(
	array("DATE_UPDATE" => "DESC"),
	array(
		"LID" => SITE_ID,
		"ID" => $ORDER_ID
	)
);
$arOrder = $dbOrder->GetNext();
CSalePaySystemAction::InitParamArrays($arOrder);



//проверяем наличие класса
if (!CSalePdf::isPdfAvailable())
	die();

$pdf = new CSalePdf("P", "pt", "A4");
$pdf->AddFont("Font", "", "pt_sans-regular.ttf", true);
$pdf->AddFont("Font", "B", "pt_sans-bold.ttf", true);
$fontFamily = "Font";
$fontSize   = 9;

$pdf->AddPage();

$lh15 = 15;
$lh13 = 13;
$lh10 = 10;

$mcol_1 =160;
$mcol_2 =340;

$mcol_sep =20;
$mcol_sep2 =40;
$mcol_4 = 120;
$mcol_5 = 180;
$mcol_6 = 210;
$mcol_7 = 110;
$mcol_8 = 160;
$mcol_9 = 90;
$mcol_10 = 250;


$pdf->Line(30,30,560,30);   //top line
$pdf->Line(30,30,30,525);   //left line
$pdf->Line(200,30,200,525); //left second line
$pdf->Line(30,275,560,275); //top second line
$pdf->Line(560,30,560,525); //right line
$pdf->Line(30,525,560,525); //bottom line

for($z0;$z<2;++$z)
{
	//line 1 ******************
	$pdf->SetFont($fontFamily, "B", $fontSize-1);
	$pdf->Cell($mcol_1,$lh15,"Извещение", 0, 0, "C");

	$pdf->SetFont($fontFamily, "", $fontSize-1);
	$pdf->Cell($mcol_2,$lh15,"Форма № ПД-4", 0, 0, "R");
	$pdf->Ln();

	//line 2 ******************
	$pdf->SetFont($fontFamily,"",$fontSize);
	$pdf->Cell($mcol_1,$lh13,"",0,0,"C");

	$pdf->SetFont($fontFamily, "", $fontSize-1);
	$pdf->Cell($mcol_sep,$lh13,"", 0, 0, "L");
	$pdf->Cell($mcol_2,13,CSalePaySystemAction::GetParamValue("COMPANY_NAME"), 0, 0, "L");
	$pdf->Ln();

	$gX=$pdf->GetX();
	$gY=$pdf->GetY();
	$pdf->Line($gX+$mcol_sep+$mcol_1,$gY,$gX+$mcol_1+$mcol_sep+$mcol_2,$gY);

	//new line *****************
	$pdf->SetFont($fontFamily, "", $fontSize-3);
	$pdf->Cell($mcol_1,$lh10,"",0,0,"C");
	$pdf->Cell($mcol_2,$lh10,"(наименование получателя платежа)", 0, 0, "C");;
	$pdf->Ln();

	//new line *****************
	$pdf->SetFont($fontFamily, "", $fontSize);
	$pdf->Cell($mcol_1,$lh13,"",0,0,"");
	$pdf->Cell($mcol_sep,$lh13,"",0,0,"");

	$pdf->Cell($mcol_4,$lh13,(CSalePaySystemAction::GetParamValue("INN"))."/".(CSalePaySystemAction::GetParamValue("KPP")),0,0,"L");
	$pdf->Cell($mcol_sep2,$lh13,"",0,0,"");
	$pdf->Cell($mcol_5,$lh13,CSalePaySystemAction::GetParamValue("SETTLEMENT_ACCOUNT"),0,0,"");
	$pdf->Ln();
	$gX=$pdf->GetX();
	$gY=$pdf->GetY();
	$pdf->Line($gX+$mcol_sep+$mcol_1,$gY,$gX+$mcol_sep+$mcol_1+$mcol_4,$gY);
	$pdf->Line($gX+$mcol_sep+$mcol_1+$mcol_4+$mcol_sep2,$gY,$gX+$mcol_sep+$mcol_1+$mcol_4+$mcol_sep2+$mcol_5,$gY);


	//new line *****************
	$pdf->SetFont($fontFamily, "", $fontSize-3);
	$pdf->Cell($mcol_1,$lh10,"",0,0,"");
	$pdf->Cell($mcol_sep,$lh10,"",0,0,"");
	$pdf->Cell($mcol_4,$lh10,"(ИНН получателя платежа)",0,0,"C");
	$pdf->Cell($mcol_sep2,10,"",0,0,"");
	$pdf->Cell($mcol_5,$lh10,"(номер счета получателя платежа)",0,0,"C");
	$pdf->Ln();

	//new line *****************
	$pdf->SetFont($fontFamily, "", $fontSize);
	$pdf->Cell($mcol_1,$lh13,"",0,0,"");
	$pdf->Cell($mcol_sep,$lh13,"",0,0,"");
	$pdf->Cell($mcol_6,$lh13,CSalePaySystemAction::GetParamValue("BANK_NAME"),0,0,"L");
	$pdf->Cell($mcol_sep,$lh13,"",0,0,"");
	$pdf->Cell($mcol_7,$lh13,CSalePaySystemAction::GetParamValue("BANK_BIC"),0,0,"");
	$pdf->Ln();
	$gX=$pdf->GetX();
	$gY=$pdf->GetY();
	$pdf->Line($gX+$mcol_sep+$mcol_1,$gY,$gX+$mcol_sep+$mcol_1+$mcol_6,$gY);
	$pdf->Line($gX+$mcol_sep+$mcol_1+$mcol_6+$mcol_sep,$gY,$gX+$mcol_sep+$mcol_1+$mcol_6+$mcol_sep+$mcol_7,$gY);

	//new line *****************
	$pdf->SetFont($fontFamily, "", $fontSize-3);
	$pdf->Cell($mcol_1,$lh10,"",0,0,"");
	$pdf->Cell($mcol_sep,$lh10,"",0,0,"");
	$pdf->Cell($mcol_6,$lh10,"(наименование банка получателя платежа)",0,0,"C");
	$pdf->Cell($mcol_sep2,10,"",0,0,"");
	$pdf->Cell($mcol_5,$lh10,"",0,0,"C");
	$pdf->Ln();

	//new line *****************
	$pdf->SetFont($fontFamily, "", $fontSize);
	$pdf->Cell($mcol_1,$lh15,"",0,0,"");
	$pdf->Cell($mcol_sep,$lh15,"",0,0,"");
	$pdf->Cell($mcol_2,$lh15,"Номер кор./сч. банка получателя платежа   ".CSalePaySystemAction::GetParamValue("BANK_COR_ACCOUNT"),0,0,"L");
	$pdf->Ln();
	$gX=$pdf->GetX();
	$gY=$pdf->GetY();
	$pdf->Line($gX+$mcol_sep+$mcol_1+$mcol_5-10,$gY,$gX+$mcol_sep+$mcol_1+$mcol_6+$mcol_sep+$mcol_7,$gY);

	//new line *****************
	$pdf->SetFont($fontFamily, "", $fontSize);
	$pdf->Cell($mcol_1,$lh13,"",0,0,"");
	$pdf->Cell($mcol_sep,$lh13,"",0,0,"");
	$pdf->Cell($mcol_8,$lh13,"Оплата заказа № ".CSalePaySystemAction::GetParamValue("ORDER_ID")." от ".CSalePaySystemAction::GetParamValue("DATE_INSERT"),0,0,"L");
	$pdf->Ln();
	$gX=$pdf->GetX();
	$gY=$pdf->GetY();
	$pdf->Line($gX+$mcol_sep+$mcol_1,$gY,$gX+$mcol_sep+$mcol_1+$mcol_8,$gY);
	$pdf->Line($gX+$mcol_sep+$mcol_1+$mcol_8+$mcol_sep,$gY,$gX+$mcol_sep+$mcol_1+$mcol_8+$mcol_sep+$mcol_8,$gY);

	//new line *****************
	$pdf->SetFont($fontFamily, "", $fontSize-3);
	$pdf->Cell($mcol_1,$lh10,"",0,0,"");
	$pdf->Cell($mcol_sep,$lh10,"",0,0,"");

	$pdf->Cell($mcol_8,$lh10,"(наименование платежа)",0,0,"C");
	$pdf->Cell($mcol_sep,10,"",0,0,"");
	$pdf->Cell($mcol_8,$lh10,"(номер лицевого счета (код) плательщика)",0,0,"C");
	$pdf->Ln();


	//new line *****************
	$pdf->SetFont($fontFamily, "", $fontSize);
	$pdf->Cell($mcol_1,$lh15,"",0,0,"");
	$pdf->Cell($mcol_sep,$lh15,"",0,0,"");
	$pdf->Cell($mcol_2,$lh15,"Ф.И.О. плательщика    ".CSalePaySystemAction::GetParamValue("PAYER_CONTACT_PERSON"),0,0,"L");
	$pdf->Ln();
	$gX=$pdf->GetX();
	$gY=$pdf->GetY();
	$pdf->Line($gX+$mcol_sep+$mcol_1+$mcol_7-20,$gY,$gX+$mcol_sep+$mcol_1+$mcol_6+$mcol_sep+$mcol_7,$gY);
	$pdf->Ln(10);

	//new line *****************
	$pdf->SetFont($fontFamily, "", $fontSize);
	$pdf->Cell($mcol_1,$lh13,"",0,0,"");
	$pdf->Cell($mcol_sep,$lh13,"",0,0,"");
	$pdf->Cell($mcol_9,$lh10,"Адрес плательщика",0,0,"L");
	/***************/
	$sAddrFact = "";
	(CSalePaySystemAction::GetParamValue("PAYER_ZIP_CODE"));
	if(strlen(CSalePaySystemAction::GetParamValue("PAYER_ZIP_CODE"))>0)
		$sAddrFact .= ($sAddrFact<>""? ", ":"").(CSalePaySystemAction::GetParamValue("PAYER_ZIP_CODE"));
	if(strlen(CSalePaySystemAction::GetParamValue("PAYER_COUNTRY"))>0)
		$sAddrFact .= ($sAddrFact<>""? ", ":"").(CSalePaySystemAction::GetParamValue("PAYER_COUNTRY"));
	if(strlen(CSalePaySystemAction::GetParamValue("PAYER_REGION"))>0)
		$sAddrFact .= ($sAddrFact<>""? ", ":"").(CSalePaySystemAction::GetParamValue("PAYER_REGION"));
	if(strlen(CSalePaySystemAction::GetParamValue("PAYER_CITY"))>0)
	{
		$g = substr(CSalePaySystemAction::GetParamValue("PAYER_CITY"), 0, 2);
		$sAddrFact .= ($sAddrFact<>""? ", ":"").($g<>"г." && $g<>"Г."? "г. ":"").(CSalePaySystemAction::GetParamValue("PAYER_CITY"));
	}
	if(strlen(CSalePaySystemAction::GetParamValue("PAYER_ADDRESS_FACT"))>0)
		$sAddrFact .= ($sAddrFact<>""? ", ":"").(CSalePaySystemAction::GetParamValue("PAYER_ADDRESS_FACT"));
	/**************/
	$pdf->MultiCell($mcol_10,$lh10,$sAddrFact,0,"L");
	$gY=$pdf->GetY();
	$gX=$pdf->GetX();
	$pdf->Ln();
	$pdf->Line($gX+$mcol_sep+$mcol_1+$mcol_9,$gY,$gX+$mcol_sep+$mcol_1+$mcol_4+$mcol_sep2+$mcol_5,$gY);


	//new line *****************
	$pdf->SetFont($fontFamily, "", $fontSize);
	$pdf->Cell($mcol_1,$lh13,"",0,0,"");
	$pdf->Cell($mcol_sep,$lh13,"",0,0,"");
	/****************/
	if(strpos(CSalePaySystemAction::GetParamValue("SHOULD_PAY"), ".")!==false)
		$a = explode(".", (CSalePaySystemAction::GetParamValue("SHOULD_PAY")));
	else
		$a = explode(",", (CSalePaySystemAction::GetParamValue("SHOULD_PAY")));

	if ($a[1] <= 9 && $a[1] > 0)
		$a[1] = $a[1]."0";
	elseif ($a[1] == 0)
		$a[1] = "00";
	/****************/
	$pdf->Cell($mcol_8,$lh13,"Сумма платежа  ".$a[0]."  руб.  ".$a[1]."  коп.                  Сумма платы за услуги        руб.      коп.",0,0,"L");
	$pdf->Ln();
	$gX=$pdf->GetX();
	$gY=$pdf->GetY();
	$pdf->Line($gX+$mcol_sep+$mcol_1+65,$gY,$gX+$mcol_sep+$mcol_1+90,$gY);
	$pdf->Line($gX+$mcol_sep+$mcol_1+112,$gY,$gX+$mcol_sep+$mcol_1+125,$gY);
	$pdf->Line($gX+$mcol_sep+$mcol_1+277,$gY,$gX+$mcol_sep+$mcol_1+295,$gY);
	$pdf->Line($gX+$mcol_sep+$mcol_1+310,$gY,$gX+$mcol_sep+$mcol_1+325,$gY);
	$pdf->Ln(5);

	//new line *****************
	$pdf->SetFont($fontFamily, "B", $fontSize);
	$pdf->Cell($mcol_1,$lh13,"Кассир",0,0,"C");
	$pdf->SetFont($fontFamily, "", $fontSize);
	$pdf->Cell($mcol_sep,$lh13,"",0,0,"");
	$pdf->Cell($mcol_8,$lh13,"Итого             руб.        коп.",0,0,"L");
	$pdf->Cell($mcol_5+5,$lh13,"«       »                           201     г",0,0,"R");
	$pdf->Ln();
	$gX=$pdf->GetX();
	$gY=$pdf->GetY();
	$pdf->Line($gX+$mcol_sep+$mcol_1+25,$gY,$gX+$mcol_sep+$mcol_1+57,$gY);
	$pdf->Line($gX+$mcol_sep+$mcol_1+77,$gY,$gX+$mcol_sep+$mcol_1+92,$gY);
	$pdf->Line($gX+$mcol_sep+$mcol_1+227,$gY,$gX+$mcol_sep+$mcol_1+245,$gY);
	$pdf->Line($gX+$mcol_sep+$mcol_1+255,$gY,$gX+$mcol_sep+$mcol_1+310,$gY);
	$pdf->Line($gX+$mcol_sep+$mcol_1+325,$gY,$gX+$mcol_sep+$mcol_1+340,$gY);
	$pdf->Ln(5);

	//new line *****************
	$pdf->SetFont($fontFamily, "", $fontSize-2);
	$pdf->Cell($mcol_1,$lh13,"",0,0,"");
	$pdf->Cell($mcol_sep,$lh13,"",0,0,"");
	$pdf->MultiCell($mcol_2,$lh10,"С условиями приема указанной в платежном документе суммы, в т.ч. с суммой взимаемой платы за услуги банка, ознакомлен и согласен.",0,"L");


	//new line *****************
	$pdf->SetFont($fontFamily, "B", $fontSize);
	$pdf->Cell($mcol_1,$lh13,"",0,0,"");
	$pdf->Cell($mcol_5,$lh13,"",0,0,"");
	$pdf->Cell($mcol_2,$lh10,"Подпись плательщика",0,0,"L");
	$pdf->Ln();
	$gX=$pdf->GetX();
	$gY=$pdf->GetY();
	$pdf->Line($gX+$mcol_sep+$mcol_1+255,$gY,$gX+$mcol_sep+$mcol_1+340,$gY);

	$pdf->Ln(5);
}

$pdf->Ln(30);
$pdf->SetFont($fontFamily, "B", $fontSize+3);
$pdf->Cell(0,20,"Внимание! В стоимость заказа не включена комиссия банка",0,0,"L");
$pdf->Ln(25);

$pdf->Cell(0,20,"Метод оплаты:",0,0,"L");
$pdf->Ln(20);
$pdf->SetFont($fontFamily,"",$fontSize);
$pdf->MultiCell(0,12,"          1. Распечатайте квитанцию. Если у вас нет принтера, перепишите верхнюю часть квитанции и заполните по этому",0,"L");
$pdf->MultiCell(0,12,"              образцу стандартный бланк квитанции в вашем банке.",0,"L");
$pdf->MultiCell(0,12,"          2. Вырежьте по контуру квитанцию.",0,"L");
$pdf->MultiCell(0,12,"          3. Оплатите квитанцию в любом отделении банка, принимающего платежи от частных лиц.",0,"L");
$pdf->MultiCell(0,12,"          4. Сохраните квитанцию до подтверждения исполнения заказа.",0,"L");

$pdf->SetFont($fontFamily, "B", $fontSize+3);
$pdf->Cell(0,12,"Условия поставки:",0,0,"L");
$pdf->Ln(20);
$pdf->SetFont($fontFamily,"",$fontSize);
$pdf->MultiCell(0,12,"          - тгрузка оплаченного товара производится после подтверждения факта платежа.",0,"L");
$pdf->MultiCell(0,12,"          - Идентификация платежа производится по квитанции, поступившей в наш банк.",0,"L");
$pdf->Ln(20);
$pdf->SetFont($fontFamily,"B",$fontSize);
$pdf->Cell(30,12,"Примечание:",0,0,"L");
$pdf->Ln();
$pdf->SetFont($fontFamily,"",$fontSize);
$pdf->MultiCell(0,12,CSalePaySystemAction::GetParamValue("COMPANY_NAME")." не может гарантировать конкретные сроки проведения вашего платежа. За дополнительной информацией о сроках доставки квитанции в банк получателя, обращайтесь в свой банк.",0,"L");


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