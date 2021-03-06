<?php
/**
 * A Magento 2 module named Rossmitchell/Twofactor
 * Copyright (C) 2017
 *
 * This file is part of Rossmitchell/Twofactor.
 *
 * Rossmitchell/Twofactor is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Rossmitchell\Twofactor\Model\Api\Requests;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Framework\Exception\AuthenticationException;
use Rossmitchell\Twofactor\Api\Request\GetQrCodeManagementInterface;
use Rossmitchell\Twofactor\Api\Response\GetQrCodeInterface;
use Rossmitchell\Twofactor\Model\Api\ResponseBuilder\GetQrCode;
use Rossmitchell\Twofactor\Model\Customer\Customer;

class GetQrCodeManagement implements GetQrCodeManagementInterface
{
    /**
     * @var GetQrCode
     */
    private $responseBuilder;
    /**
     * @var Customer
     */
    private $customer;

    /**
     * GetQrCodeManagement constructor.
     *
     * @param Customer  $customer
     * @param GetQrCode $responseBuilder
     */
    public function __construct(
        Customer $customer,
        GetQrCode $responseBuilder
    ) {
        $this->responseBuilder = $responseBuilder;
        $this->customer = $customer;
    }

    /**
     * GET for getQrCode api
     *
     * @param string $customerId
     *
     * @return GetQrCodeInterface
     * @throws AuthenticationException
     */
    public function getQrCode($customerId)
    {
        $customer = $this->customer->getCustomerById($customerId);
        if (null === $customer->getId()) {
            throw new AuthenticationException(__('You must be logged in to access your QR Code'));
        }

        return $this->responseBuilder->buildResponseForCustomer($customer);
    }
}
