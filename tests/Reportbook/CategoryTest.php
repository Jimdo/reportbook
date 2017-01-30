<?php

namespace Jimdo\Reports\Reportbook;

use PHPUnit\Framework\TestCase;

class CategoryTest extends TestCase
{
    /**
     * @test
     */
    public function itShouldHaveCategoryConstants()
    {
        $this->assertEquals('SCHOOL', Category::SCHOOL);
        $this->assertEquals('COMPANY', Category::COMPANY);
    }

    /**
     * @test
     */
    public function itShouldHaveConstruct()
    {
        $category = new Category(Category::SCHOOL);

        $this->assertEquals(Category::SCHOOL, $category->name());
    }
}
