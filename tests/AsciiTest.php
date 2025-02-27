<?php

declare(strict_types=1);

namespace voku\tests;

use voku\helper\ASCII;

/**
 * @internal
 */
final class AsciiTest extends \PHPUnit\Framework\TestCase
{
    public function testUtf8()
    {
        $str = 'testiñg';
        static::assertFalse(ASCII::is_ascii($str));
    }

    public function testAscii()
    {
        $str = 'testing';
        static::assertTrue(ASCII::is_ascii($str));
    }

    public function testInvalidChar()
    {
        $str = "tes\xe9ting";
        static::assertFalse(ASCII::is_ascii($str));
    }

    public function testEmptyStr()
    {
        $str = '';
        static::assertTrue(ASCII::is_ascii($str));
    }

    public function testNewLine()
    {
        $str = "a\nb\nc";
        static::assertTrue(ASCII::is_ascii($str));
    }

    public function testTab()
    {
        $str = "a\tb\tc";
        static::assertTrue(ASCII::is_ascii($str));
    }

    public function testUtf8ToAscii()
    {
        $str = 'testiñg';
        static::assertSame('testing', ASCII::to_ascii($str));
    }

    public function testAsciiToAscii()
    {
        $str = 'testing';
        static::assertSame('testing', ASCII::to_ascii($str));
    }

    public function testToAscii()
    {
        $testsStrict = [
            ' '                                        => ' ',
            ''                                         => '',
            'أبز'                                      => 'abz',
            "\xe2\x80\x99"                             => '\'',
            'Ɓtest'                                    => 'Btest',
            '  -ABC-中文空白-  '                           => '  -ABC--  ',
            "      - abc- \xc2\x87"                    => '      - abc- ',
            'abc'                                      => 'abc',
            'deja vu'                                  => 'deja vu',
            'déjà vu'                                  => 'deja vu',
            'déjà σσς iıii'                            => 'deja sss iiii',
            "test\x80-\xBFöäü"                         => 'test-oau',
            'Internationalizaetion'                    => 'Internationalizaetion',
            "中 - &#20013; - %&? - \xc2\x80"            => ' - &#20013; - %&? - ',
            'Un été brûlant sur la côte'               => 'Un ete brulant sur la cote',
            'Αυτή είναι μια δοκιμή'                    => 'Auth inai mia dokimh',
            'أحبك'                                     => 'ahbk',
            'キャンパス'                                    => '',
            'биологическом'                            => 'biologiceskom',
            '정, 병호'                                    => ', ',
            'ますだ, よしひこ'                                => ', ',
            'मोनिच'                                    => 'MaNaCa',
            'क्षȸ'                                     => 'KaShha',
            'أحبك 😀'                                   => 'ahbk ',
            'ذرزسشصضطظعغػؼؽؾؿ 5.99€'                   => 'thrzsshsdtthaagh 5.99',
            'ذرزسشصضطظعغػؼؽؾؿ £5.99'                   => 'thrzsshsdtthaagh 5.99',
            '׆אבגדהוזחטיךכלםמן $5.99'                  => ' $5.99',
            '日一国会人年大十二本中長出三同 ¥5990'                    => ' 5990',
            '5.99€ 日一国会人年大十 $5.99'                     => '5.99  $5.99',
            'בגדה@ضطظعغػ.com'                          => '@dtthaagh.com',
            '年大十@ضطظعغػ'                               => '@dtthaagh',
            'בגדה & 年大十'                               => ' & ',
            '国&ם at ضطظعغػ.הוז'                        => '& at dtthaagh.',
            'my username is @בגדה'                     => 'my username is @',
            'The review gave 5* to ظعغػ'               => 'The review gave 5* to thaagh',
            'use 年大十@ضطظعغػ.הוז to get a 10% discount' => 'use @dtthaagh. to get a 10% discount',
            '日 = הط^2'                                 => ' = t^2',
            'ךכלם 国会 غػؼؽ 9.81 m/s2'                   => '  gh 9.81 m/s2',
            'The #会 comment at @בגדה = 10% of *&*'     => 'The # comment at @ = 10% of *&*',
            '∀ i ∈ ℕ'                                  => ' i  ',
            '👍 💩 😄 ❤ 👍 💩 😄 ❤أحبك'                      => '       ahbk',
        ];

        foreach ($testsStrict as $before => $after) {
            static::assertSame($after, ASCII::to_ascii($before, 'en', true), 'tested: ' . $before);
        }
    }

    public function testInvalidCharToAscii()
    {
        $str = "tes\xe9ting";
        static::assertSame('testing', ASCII::to_transliterate($str));

        // ---

        $str = "tes\xe9ting";
        static::assertSame('testing', ASCII::to_ascii($str));
    }

    public function testEmptyStrToAscii()
    {
        $str = '';
        static::assertSame('', ASCII::to_ascii($str));
    }

    public function testNulAndNon7Bit()
    {
        $str = "a\x00ñ\x00c";
        static::assertSame('anc', ASCII::to_ascii($str));
    }

    public function testNul()
    {
        $str = "a\x00b\x00c";
        static::assertSame('abc', ASCII::to_ascii($str));
    }

    public function testNewLineToAscii()
    {
        $str = "a\nb\nc";
        static::assertSame("a\nb\nc", ASCII::to_transliterate($str));

        // ---

        $str = "a\nb\nc";
        static::assertSame("a\nb\nc", ASCII::to_ascii($str, 'en', false));

        // ---

        $str = "a\nb\nc";
        static::assertSame('a b c', ASCII::to_ascii($str, 'en', true));
    }

    public function testTabToAscii()
    {
        $str = "a\tb\tc";
        static::assertSame("a\tb\tc", ASCII::to_transliterate($str));

        // ---

        $str = "a\tb\tc";
        static::assertSame("a\tb\tc", ASCII::to_ascii($str, 'en', false));

        // ---

        $str = "a\tb\tc";
        static::assertSame('a b c', ASCII::to_ascii($str, 'en', true));
    }
}
