<?php
use tools\Request;
use entity\Company;
use entity\Individual;
use entity\Entity;

require_once($_SERVER['DOCUMENT_ROOT'] . "/core/lib/PHPExcel-1.8/Classes/PHPExcel/IOFactory.php");
require_once($_SERVER['DOCUMENT_ROOT'] . '/core/lib/Request.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/core/lib/Entity.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/core/lib/Individual.php');
require_once($_SERVER['DOCUMENT_ROOT'] . '/core/lib/Company.php');


$file =$_FILES['file']['tmp_name'];
$path = $_SERVER['DOCUMENT_ROOT'].'/core/uploads/file.xlsx';
move_uploaded_file($file, $path);


$xls = PHPExcel_IOFactory::load($path);
$xls->setActiveSheetIndex(0);
$sheet = $xls->getActiveSheet();
$sheetData = $sheet->toArray();

$headers = $sheetData[0];
unset($sheetData[0]);

$dadata = new Request("https://suggestions.dadata.ru/suggestions/api/4_1/rs/findById/party");
$bitrix = new Request("https://test24.ctrl.lc/rest/1/p132m079k5vyqm2c/");
 
foreach ($sheetData as $row)
{
    $data = [];
    $rowCompany= array_combine($headers, $row);
    $query = ["query" => $rowCompany['ИНН']];
    $data = $dadata->send($query, "POST","",["Authorization: Token e2b60cde9f4e69ae93a14f40504f8f53be401fff"]);

    if($data->suggestions[0]->data)
    {
        $info = $data->suggestions[0]->data;
        if($info->type == "LEGAL")
        {
            $company = new Company($rowCompany["Название компании"], $info->name->short_with_opf, $info->name->full_with_opf, $rowCompany["Телефон"], $rowCompany["email"], $info->inn, $info->kpp, $info->ogrn, $info->state->registration_date, $info->address->value, $rowCompany["Ответственный"]);
            var_dump($company->send($bitrix));
            echo('\n');

        }
        else
        {
            $individual = new Individual($rowCompany["Название компании"], $rowCompany["Телефон"], $rowCompany["email"], $info->name->short_with_opf, $info->name->full_with_opf, $info->inn, $info->fio->surname, $info->fio->name, $info->fio->patronymic, $info->ogrn, $info->address->value, $rowCompany["Ответственный"]);
            var_dump($individual->send($bitrix));
            echo('\n');
        }

    }
    else
    {
        var_dump(Entity::createCompany($bitrix, $rowCompany["Название компании"], $rowCompany["Телефон"], $rowCompany["email"], $rowCompany["Ответственный"]));
        echo('\n');
    }

}

echo('Компании загружены');
