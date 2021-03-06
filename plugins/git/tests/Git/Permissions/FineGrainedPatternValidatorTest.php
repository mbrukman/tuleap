<?php
/**
 * Copyright (c) Enalean, 2016. All Rights Reserved.
 *
 * This file is a part of Tuleap.
 *
 * Tuleap is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * Tuleap is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with Tuleap. If not, see <http://www.gnu.org/licenses/>.
 */

namespace Tuleap\Git\Permissions;

use TuleapTestCase;

require_once dirname(__FILE__).'/../../bootstrap.php';

class FineGrainedPatternValidatorTest extends TuleapTestCase
{
    public function itValidatesPattern()
    {
        $pattern_01 = '*';
        $pattern_02 = '/*';
        $pattern_03 = 'master';
        $pattern_04 = 'master*';
        $pattern_05 = 'master/*';
        $pattern_06 = 'master/*/*';
        $pattern_07 = 'master/dev';
        $pattern_08 = 'master/dev*';
        $pattern_09 = 'master*/dev';
        $pattern_10 = '';
        $pattern_11 = 'master*[dev';
        $pattern_12 = 'master dev';
        $pattern_13 = 'master?dev';

        $validator = new FineGrainedPatternValidator();

        $this->assertTrue($validator->isPatternValid($pattern_01));
        $this->assertFalse($validator->isPatternValid($pattern_02));
        $this->assertTrue($validator->isPatternValid($pattern_03));
        $this->assertFalse($validator->isPatternValid($pattern_04));
        $this->assertTrue($validator->isPatternValid($pattern_05));
        $this->assertFalse($validator->isPatternValid($pattern_06));
        $this->assertTrue($validator->isPatternValid($pattern_07));
        $this->assertFalse($validator->isPatternValid($pattern_08));
        $this->assertFalse($validator->isPatternValid($pattern_09));
        $this->assertFalse($validator->isPatternValid($pattern_10));
        $this->assertFalse($validator->isPatternValid($pattern_11));
        $this->assertFalse($validator->isPatternValid($pattern_12));
        $this->assertFalse($validator->isPatternValid($pattern_13));
    }
}
