<?php


namespace Ipol\Fivepost\Api\Methods;

use Ipol\Fivepost\Api\Adapter\CurlAdapter;
use Ipol\Fivepost\Api\ApiLevelException;
use Ipol\Fivepost\Api\BadResponseException;
use Ipol\Fivepost\Api\Entity\EncoderInterface;
use Ipol\Fivepost\Api\Entity\Response\ErrorResponse;
use Ipol\Fivepost\Api\Entity\Response\GetOrderHistory as ObjResponse;
use Ipol\Fivepost\Api\Entity\Request\GetOrderHistory as ObjRequest;


/**
 * Class GetOrderHistory
 * @package Ipol\Fivepost\Api\Methods
 */
class GetOrderHistory extends AbstractMethod
{
    /**
     * GetOrderHistory constructor.
     * @param ObjRequest $data
     * @param CurlAdapter $adapter
     * @param false|EncoderInterface $encoder
     * @throws BadResponseException
     */
    public function __construct(ObjRequest $data, CurlAdapter $adapter, $encoder = false)
    {
        parent::__construct($adapter, $encoder);

        $this->setData($this->getEntityFields($data));

        try
        {
            $response = new ObjResponse($this->request());
            $response->setRequestSuccess(true);
        } catch (ApiLevelException $e)
        {
            $response = new ErrorResponse($e->getAnswer());
            $response->setRequestSuccess(false);
        }

        $this->setResponse($this->reEncodeResponse($response));

        $this->setFields();
    }

    /**
     * @return ObjResponse|ErrorResponse
     */
    public function getResponse()
    {
        return parent::getResponse();
    }
}