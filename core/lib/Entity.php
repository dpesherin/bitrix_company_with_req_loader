<?php

namespace entity;

use tools\Request;

class Entity
{
    protected $name;
    protected $shortName;
    protected $fullName;
    protected $inn;
    protected $address;
    protected $owner;
    protected $phone;
    protected $email;

    public static function createCompany( Request $bitrix, string $name, $phone, $email, $owner)
    {
        if($owner)
        {
            $userID = self::getUserID($bitrix, $owner);
        }
        else
        {
            $userID = 1;
        }
//
        $data = [
            "fields"=>[
                "TITLE" => $name,
                "COMPANY_TYPE" => "CUSTOMER",
                "ASSIGNED_BY_ID" => $userID,
                "PHONE" => [
                    [
                        "VALUE" => $phone,
                        "VALUE_TYPE" => "WORK"
                    ]
                ],
                "EMAIL" => [
                    [
                        "VALUE" => $email,
                        "VALUE_TYPE" => "WORK"
                    ]
                ]
            ],
            "params" => ["REGISTER_SONET_EVENT" => "Y"]
        ];
        return $bitrix->send($data, "POST", "crm.company.add");
    }

    public static function getUserID(Request $bitrix, string $user): int
    {
        if($user)
        {
            $user = explode(" ", $user);
            $data = [
                "NAME" => $user[0],
                "LAST_NAME" => $user[1]
            ];
            $userData = $bitrix->send($data, "POST", "user.get");
            return (int)$userData->result[0]->ID;
        }else{
            return 1;
        }
    }

}