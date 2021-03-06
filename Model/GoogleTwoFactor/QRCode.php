<?php
/**
 * A two factor authentication module that protects both the admin and customer logins
 * Copyright (C) 2017  Ross Mitchell
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

namespace Rossmitchell\Twofactor\Model\GoogleTwoFactor;

use PragmaRX\Google2FA\Google2FA;

class QRCode
{
    /**
     * @var Google2FA
     */
    private $google2FA;

    /**
     * QRCode constructor.
     *
     * @param Google2FA $google2FA
     */
    public function __construct(Google2FA $google2FA)
    {
        $this->google2FA = $google2FA;
    }

    public function generateQRCode($companyName, $email, $secret)
    {
        $qrCode = $this->google2FA->getQRCodeInline($companyName, $email, $secret);

        return $qrCode;
    }

    public function displayCurrentCode($secret)
    {
        $timeStamp = $this->google2FA->getTimestamp();
        $seed = $this->google2FA->base32Decode($secret);
        $code = $this->google2FA->oathHotp($seed, $timeStamp);

        return $code;
    }
}
