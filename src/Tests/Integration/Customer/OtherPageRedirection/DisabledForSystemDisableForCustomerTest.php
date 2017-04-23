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

namespace Rossmitchell\Twofactor\Tests\Integration\Customer\OtherPageRedirection;

use Rossmitchell\Twofactor\Tests\Integration\Abstracts\AbstractTestClass;
use Rossmitchell\Twofactor\Tests\Integration\FixtureLoader\Traits\ConfigurationLoader;
use Rossmitchell\Twofactor\Tests\Integration\FixtureLoader\Traits\CustomerLoader;

class DisabledForSystemDisableForCustomerTest extends AbstractTestClass
{

    use CustomerLoader;
    use ConfigurationLoader;

    public static function getCustomerDataPath()
    {
        return __DIR__.'/../_files/customer.php';
    }

    public static function getConfigurationDataPath()
    {
        return __DIR__.'/../_files/two_factor_disabled.php';
    }

    /**
     * @magentoDbIsolation disabled
     * @magentoDataFixture   loadCustomer
     * @magentoDataFixture   loadConfiguration
     */
    public function testNoRedirectToVerification()
    {
        $this->login('not_enabled@example.com');
        $this->dispatch('/');

        $this->assertFalse($this->getResponse()->isRedirect());
    }

}
