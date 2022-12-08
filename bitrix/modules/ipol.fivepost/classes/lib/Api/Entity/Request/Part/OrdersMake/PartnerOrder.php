<?php
namespace Ipol\Fivepost\Api\Entity\Request\Part\OrdersMake;

use Ipol\Fivepost\Api\Entity\AbstractEntity;

/**
 * Class PartnerOrder
 * @package Ipol\Fivepost\Api
 * @subpackage Entity
 */
class PartnerOrder extends AbstractEntity
{
    /**
     * @var string order identifier in partner's system
     */
    protected $senderOrderId;

    /**
     * @var string used as track number to inform the buyer
     */
    protected $clientOrderId;

    /**
     * @var string|null
     */
    protected $brandName;

    /**
     * @var string
     */
    protected $clientName;

    /**
     * @var string format variants: +79XXXXXXXXX | 79XXXXXXXXX | 89XXXXXXXXX | 9XXXXXXXXX
     */
    protected $clientPhone;

    /**
     * @var string|null
     */
    protected $clientEmail;

    /**
     * @var string warehouse partnerLocationId
     */
    protected $senderLocation;

    /**
     * @var string|null
     */
    protected $returnLocation;

    /**
     * @var string|null uuid, required if receiverLocationMDM not set
     */
    protected $receiverLocation;

    /**
     * @var string|null required if receiverLocation uuid not set
     */
    protected $receiverLocationMDM;

    /**
     * @var string (RETURN | UTILIZATION)
     */
    protected $undeliverableOption;

    /**
     * @var string|null "2019-05-07T14:02:14Z" Z - UTC 0
     */
    protected $senderCreateDate;

    /**
     * @var string|null "2019-05-07T14:02:14Z" Z - UTC 0
     */
    protected $shipmentDate;

    /**
     * @var string|null "2019-05-07T14:02:14Z" Z - UTC 0
     */
    protected $plannedReceiveDate;

    /**
     * @var string|null
     */
    protected $rateTypeCode;

    /**
     * @var Vendor|null
     */
    protected $vendor;

    /**
     * @var Cost
     */
    protected $cost;

    /**
     * @var CargoList
     */
    protected $cargoes;

    /**
     * @return string
     */
    public function getSenderOrderId(): string
    {
        return $this->senderOrderId;
    }

    /**
     * @param string $senderOrderId
     * @return PartnerOrder
     */
    public function setSenderOrderId(string $senderOrderId): PartnerOrder
    {
        $this->senderOrderId = $senderOrderId;
        return $this;
    }

    /**
     * @return string
     */
    public function getClientOrderId(): string
    {
        return $this->clientOrderId;
    }

    /**
     * @param string $clientOrderId
     * @return PartnerOrder
     */
    public function setClientOrderId(string $clientOrderId): PartnerOrder
    {
        $this->clientOrderId = $clientOrderId;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getBrandName(): ?string
    {
        return $this->brandName;
    }

    /**
     * @param string|null $brandName
     * @return PartnerOrder
     */
    public function setBrandName(?string $brandName): PartnerOrder
    {
        $this->brandName = $brandName;
        return $this;
    }

    /**
     * @return string
     */
    public function getClientName(): string
    {
        return $this->clientName;
    }

    /**
     * @param string $clientName
     * @return PartnerOrder
     */
    public function setClientName(string $clientName): PartnerOrder
    {
        $this->clientName = $clientName;
        return $this;
    }

    /**
     * @return string
     */
    public function getClientPhone(): string
    {
        return $this->clientPhone;
    }

    /**
     * @param string $clientPhone
     * @return PartnerOrder
     */
    public function setClientPhone(string $clientPhone): PartnerOrder
    {
        $this->clientPhone = $clientPhone;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getClientEmail(): ?string
    {
        return $this->clientEmail;
    }

    /**
     * @param string|null $clientEmail
     * @return PartnerOrder
     */
    public function setClientEmail(?string $clientEmail): PartnerOrder
    {
        $this->clientEmail = $clientEmail;
        return $this;
    }

    /**
     * @return string
     */
    public function getSenderLocation(): string
    {
        return $this->senderLocation;
    }

    /**
     * @param string $senderLocation
     * @return PartnerOrder
     */
    public function setSenderLocation(string $senderLocation): PartnerOrder
    {
        $this->senderLocation = $senderLocation;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getReturnLocation(): ?string
    {
        return $this->returnLocation;
    }

    /**
     * @param string|null $returnLocation
     * @return PartnerOrder
     */
    public function setReturnLocation(?string $returnLocation): PartnerOrder
    {
        $this->returnLocation = $returnLocation;
        return $this;
    }

    /**
     * @return string
     */
    public function getReceiverLocation(): string
    {
        return $this->receiverLocation;
    }

    /**
     * @param string $receiverLocation
     * @return PartnerOrder
     */
    public function setReceiverLocation(string $receiverLocation): PartnerOrder
    {
        $this->receiverLocation = $receiverLocation;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getReceiverLocationMDM(): ?string
    {
        return $this->receiverLocationMDM;
    }

    /**
     * @param string|null $receiverLocationMDM
     * @return PartnerOrder
     */
    public function setReceiverLocationMDM(?string $receiverLocationMDM): PartnerOrder
    {
        $this->receiverLocationMDM = $receiverLocationMDM;
        return $this;
    }

    /**
     * @return string
     */
    public function getUndeliverableOption(): string
    {
        return $this->undeliverableOption;
    }

    /**
     * @param string $undeliverableOption
     * @return PartnerOrder
     */
    public function setUndeliverableOption(string $undeliverableOption): PartnerOrder
    {
        $this->undeliverableOption = $undeliverableOption;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSenderCreateDate(): ?string
    {
        return $this->senderCreateDate;
    }

    /**
     * @param string|null $senderCreateDate
     * @return PartnerOrder
     */
    public function setSenderCreateDate(?string $senderCreateDate): PartnerOrder
    {
        $this->senderCreateDate = $senderCreateDate;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getShipmentDate(): ?string
    {
        return $this->shipmentDate;
    }

    /**
     * @param string|null $shipmentDate
     * @return PartnerOrder
     */
    public function setShipmentDate(?string $shipmentDate): PartnerOrder
    {
        $this->shipmentDate = $shipmentDate;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPlannedReceiveDate(): ?string
    {
        return $this->plannedReceiveDate;
    }

    /**
     * @param string|null $plannedReceiveDate
     * @return PartnerOrder
     */
    public function setPlannedReceiveDate(?string $plannedReceiveDate): PartnerOrder
    {
        $this->plannedReceiveDate = $plannedReceiveDate;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getRateTypeCode(): ?string
    {
        return $this->rateTypeCode;
    }

    /**
     * @param string|null $rateTypeCode
     * @return PartnerOrder
     */
    public function setRateTypeCode(?string $rateTypeCode): PartnerOrder
    {
        $this->rateTypeCode = $rateTypeCode;
        return $this;
    }

    /**
     * @return Vendor|null
     */
    public function getVendor(): ?Vendor
    {
        return $this->vendor;
    }

    /**
     * @param Vendor|null $vendor
     * @return PartnerOrder
     */
    public function setVendor(?Vendor $vendor): PartnerOrder
    {
        $this->vendor = $vendor;
        return $this;
    }

    /**
     * @return Cost
     */
    public function getCost(): Cost
    {
        return $this->cost;
    }

    /**
     * @param Cost $cost
     * @return PartnerOrder
     */
    public function setCost(Cost $cost): PartnerOrder
    {
        $this->cost = $cost;
        return $this;
    }

    /**
     * @return CargoList
     */
    public function getCargoes(): CargoList
    {
        return $this->cargoes;
    }

    /**
     * @param CargoList $cargoes
     * @return PartnerOrder
     */
    public function setCargoes(CargoList $cargoes): PartnerOrder
    {
        $this->cargoes = $cargoes;
        return $this;
    }
}