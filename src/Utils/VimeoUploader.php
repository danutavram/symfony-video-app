<?php

namespace App\Utils;

use App\Entity\User;
use App\Utils\Interfaces\UploaderInterface;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;


class VimeoUploader implements UploaderInterface
{
    public $vimeoToken;

    public function __construct(Security $security)
    {
        $user = $security->getUser();

        if ($user instanceof User && $user->getVimeoApiKey()) {
            $this->vimeoToken = $user->getVimeoApiKey();
        } else {
            throw new ServiceUnavailableHttpException('User does not have a Vimeo API key.');
        }
    }

    public function upload($file)
    {
    }

    public function delete($path)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_URL => "https://api.vimeo.com/videos/$path",
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'DELETE',
          CURLOPT_POSTFIELDS => 'upload.approach%0A=post',
          CURLOPT_HTTPHEADER => array(
            'Accept: application/vnd.vimeo.*+json;version=3.4',
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: Bearer f79938646fc19e8f29d2850774a879d3',
            'Cookie: __cf_bm=wvAaYxF.EsCuPXXnEnqqcc30D4xtvDPzA8B3RaPrx3A-1712175762-1.0.1.1-Ayqv5RHbOl.hZulQJg9a2tCJSZpRcxZEUz4Tkmz4xlSMZJF8ATiWzu_MSKeQ.ZypA93vZ8.tLvOlRfowcdnavg; _abexps=%7B%223278%22%3A%22variant%22%7D; _cfuvid=OblGub78Oa3Y3xdk.52c6Bis2g9XrQrpfPPEV5LxTko-1712155445660-0.0.1.1-604800000; vuid=12831574.2119914439'
          ),
        ));
        
        $response = curl_exec($curl);
        
        curl_close($curl);
        echo $response;
    }
}