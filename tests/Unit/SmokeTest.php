<?php

namespace Tests\Unit;

use App\Models\FromTo;
use PHPUnit\Framework\TestCase;

class SmokeTest extends TestCase
{
    public function test_from_to_accepts_only_frm_to_and_ft_types(): void
    {
        $this->assertSame(['FRM', 'TO', 'FT'], FromTo::TYPES);
    }
}
