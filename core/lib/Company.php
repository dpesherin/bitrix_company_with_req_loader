<?php

namespace entity;
use tools\Request;


class Company extends Entity
{
    protected $kpp;
    protected $ogrn;
    protected $regDate;

    public function __construct($name, $shortName, $fullName, $phone, $email, $inn, $kpp, $ogrn, $regDate, $address, $owner)
    {
        $this->name= $name;
        $this->shortName = $shortName;
        $this->fullName = $fullName;
        $this->inn = $inn;
        $this->kpp = $kpp;
        $this->ogrn = $ogrn;
        $this->regDate = $regDate;
        $this->address = $address;
        $this->owner = $owner;
        $this->phone = $phone;
        $this->email = $email;
    }

    public function send(Request $bitrix)
    {
        $userID = self::getUserID($bitrix, $this->owner);
        $data = [
            "fields"=>[
                "TITLE" => $this->name,
                "COMPANY_TYPE" => "CUSTOMER",
                "ASSIGNED_BY_ID" => $userID,
                "PHONE" => [
                    [
                        "VALUE" => $this->phone,
                        "VALUE_TYPE" => "WORK"
                    ]
                ],
                "EMAIL" => [
                    [
                        "VALUE" => $this->email,
                        "VALUE_TYPE" => "WORK"
                    ]
                ]
            ],
            "params" => ["REGISTER_SONET_EVENT" => "Y"]
        ];
        $company = $bitrix->send($data, "POST", "crm.company.add");
        $companyID = $company->result;
        $data = [
            "fields"=>[
                "ENTITY_TYPE_ID" => 4,
                "ENTITY_ID" => $companyID,
                "PRESET_ID" => 1,
                "NAME" => "Реквизит ". $this->name,
                "RQ_COMPANY_NAME" => $this->shortName,
                "RQ_COMPANY_FULL_NAME" => $this->fullName,
                "RQ_COMPANY_REG_DATE" => date('m/d/Y', ($this->regDate/1000)),
                "RQ_INN" => $this->inn,
                "RQ_KPP" => $this->kpp,
                "RQ_OGRN" => $this->ogrn
                ]
        ];
        $requisite = $bitrix->send($data, "POST", "crm.requisite.add");
        $requisiteID = $requisite->result;
        $data = [
            "fields"=>[
                "TYPE_ID" => 6,
                "ENTITY_TYPE_ID" => 8,
                "ENTITY_ID" => $requisiteID,
                "ADDRESS_1" => $this->address
            ]
        ];

        $address = $bitrix->send($data, "POST", "crm.address.add");
        return json_encode([
            "status" => "finished",
            "entities"=>[
                "company_id" => $company,
                "requisite_id" => $requisite,
                "adress" => $address
            ]
        ]);
    }

}