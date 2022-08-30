<?php

namespace entity;
use tools\Request;


class Individual extends Entity
{
    protected $surnameIP;
    protected $nameIP;
    protected $lastnameIP;
    protected $ogrnip;

    public function __construct($name, $phone, $email, $shortName, $fullName, $inn, $surnameIP, $nameIP, $lastnameIP, $ogrnip, $address, $owner)
    {
        $this->name = $name;
        $this->phone = $phone;
        $this->email = $email;
        $this->shortName = $shortName;
        $this->fullName = $fullName;
        $this->inn = $inn;
        $this->surnameIP = $surnameIP;
        $this->nameIP = $nameIP;
        $this->lastnameIP = $lastnameIP;
        $this->ogrnip = $ogrnip;
        $this->address = $address;
        $this->owner = $owner;
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
                "PRESET_ID" => 2,
                "NAME" => "Реквизит ". $this->name,
                "RQ_COMPANY_NAME" => $this->shortName,
                "RQ_COMPANY_FULL_NAME" => $this->fullName,
                "RQ_FIRST_NAME" => $this->nameIP,
                "RQ_LAST_NAME" => $this->surnameIP,
                "RQ_NAME" => $this->surnameIP." ".$this->nameIP." ".$this->lastnameIP,
                "RQ_INN" => $this->inn,
                "RQ_OGRNIP" => $this->ogrnip
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