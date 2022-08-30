<?php

namespace tools;
use Exception;

class Request
{
    protected string $domain;
    protected int $post;
    protected array $headers;
    protected object $error;


    public function __construct(string $domain, array $headers = ["Content-Type: application/json"])
    {
        $this->domain = $domain;
        $this->headers = $headers;
    }

    public function send(array $data, string $type, string $method = "", array $extHeaders = [])
    {
        if($type == "GET")
        {
            $this->post = 0;
        }
        else
        {
            $this->post = 1;
        }
        $headers = array_merge($this->headers, $extHeaders);
        try
        {
            $queryData = json_encode($data);
            $curl = curl_init();
            curl_setopt_array($curl,[
                CURLOPT_SSL_VERIFYHOST=>0,
                CURLOPT_SSL_VERIFYPEER=>0,
                CURLOPT_POST=>$this->post,
                CURLOPT_RETURNTRANSFER=>1,
                CURLOPT_URL=>$this->domain.$method,
                CURLOPT_POSTFIELDS => $queryData,
                CURLOPT_HTTPHEADER => $headers
            ]);
            return json_decode(curl_exec($curl));
        }
        catch (Exception $e)
        {
            $this->error = $e;
            return(json_encode(["status" => "error", "message" => self::getError()]));
        }

    }

    /**
     * @return Request object
     */
    public function getError(){
        return $this->error;
    }


}