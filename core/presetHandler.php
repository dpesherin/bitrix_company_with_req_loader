<?php
require_once('./config/config.php');
$file = DOMAIN.'/core/templates/company.xlsx';

echo('<a href='.$file.' id="btn-download" class="btn btn-primary" onclick="hideHandler(event)">Скачать</a>');

