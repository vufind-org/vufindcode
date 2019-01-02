<?php
/**
 * ISBN Test Class
 *
 * PHP version 7
 *
 * Copyright (C) Villanova University 2010.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License version 2,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301  USA
 *
 * @category VuFind
 * @package  Tests
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:testing:unit_tests Wiki
 */
use VuFindCode\ISBN;
require_once __DIR__ . '/../src/VuFindCode/ISBN.php';

/**
 * ISBN Test Class
 *
 * @category VuFind
 * @package  Tests
 * @author   Demian Katz <demian.katz@villanova.edu>
 * @author   Chris Hallberg <challber@villanova.edu>
 * @license  http://opensource.org/licenses/gpl-2.0.php GNU General Public License
 * @link     https://vufind.org/wiki/development:testing:unit_tests Wiki
 */
class ISBNTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Test that $raw results in valid $isbn10 and valid $isbn13.
     *
     * @dataProvider validISBN10
     * @return void
     */
    public function testValidISBN10($raw, $isbn10, $isbn13)
    {
        $isbn = new ISBN($raw);
        $this->assertEquals($isbn10, $isbn->get10());
        $this->assertEquals($isbn13, $isbn->get13());
        $this->assertTrue($isbn->isValid());
    }

    /**
     * Data provider for testValidISBN10().
     *
     * @return array
     */
    public function validISBN10() {
        return [
            'ISBN-10 plain'  => ['0123456789',        '0123456789', '9780123456786'],
            'ISBN-10 dashes' => ['0-12-345678-9',     '0123456789', '9780123456786'],
            'ISBN-10 spaces' => ['0 12 345678 9',     '0123456789', '9780123456786'],
            'ISBN-13 plain'  => ['9780123456786',     '0123456789', '9780123456786'],
            'ISBN-13 dashes' => ['978-0-12-345678-6', '0123456789', '9780123456786'],
            'ISBN-13 spaces' => ['978 0 12 345678 6', '0123456789', '9780123456786'],
            'ISBN-10 with x' => ['012345672x',        '012345672X', '9780123456724'],
            'ISBN-10 with X' => ['012345672X',        '012345672X', '9780123456724'],
        ];
    }

    /**
     * Test Valid ISBN-13 that is not part of the Bookland EAN.
     *
     * @return void
     */
    public function testValidISBN13OutsideOfBooklandEAN()
    {
        // Valid ISBN-13 outside of Bookland EAN:
        $isbn = new ISBN('9790123456785');
        $this->assertFalse($isbn->get10());
        $this->assertEquals('9790123456785', $isbn->get13());
        $this->assertTrue($isbn->isValid());
    }

    /**
     * Test Invalid ISBN.
     *
     * @dataProvider invalidISBN
     * @return void
     */
    public function testInvalidISBN($raw)
    {
        $isbn = new ISBN($raw);
        $this->assertFalse($isbn->get10());
        $this->assertFalse($isbn->get13());
        $this->assertFalse($isbn->isValid());
    }

    /**
     * Data provider for testInvalidISBN().
     *
     * @return array
     */
    public function invalidISBN() {
        return [
            'empty'                  => [''],
            'ISBN-10 wrong checksum' => ['2314346323'],
            'ISBN-13 wrong checksum' => ['9780123456787'],
            '10 times X'             => ['XXXXXXXXXX'],
            '13 times X'             => ['XXXXXXXXXXXXX'],
            'ISBN-10 with X inside'  => ['01234567X3'],
            'ISBN-13 with X inside'  => ['97901234567X9'],
        ];
    }
}
