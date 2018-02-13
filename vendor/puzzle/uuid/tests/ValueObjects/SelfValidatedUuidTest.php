<?php

declare(strict_types = 1);

namespace Puzzle\ValueObjects;

use PHPUnit\Framework\TestCase;

class SelfValidatedUuidTest extends TestCase
{
    public function testCompareNewUuid()
    {
        $uuid1 = new Uuid();
        $uuid2 = new Uuid();

        $this->assertFalse($uuid1 == $uuid2);
    }

    public function testCompareSameUuid()
    {
        $uuidValue = (string) \Ramsey\Uuid\Uuid::uuid4();

        $uuid1 = new Uuid($uuidValue);
        $uuid2 = new Uuid($uuidValue);

        $this->assertTrue($uuid1 == $uuid2);
    }

    public function testCompareDifferentUuid()
    {
        $uuidValue1 = (string) \Ramsey\Uuid\Uuid::uuid4();
        $uuidValue2 = (string) \Ramsey\Uuid\Uuid::uuid4();

        $uuid1 = new Uuid($uuidValue1);
        $uuid2 = new Uuid($uuidValue2);

        $this->assertFalse($uuid1 == $uuid2);
    }

    public function testToString()
    {
        $uuid1 = new Uuid();
        $uuid2 = new Uuid((string) $uuid1);

        $this->assertTrue($uuid1 == $uuid2);
    }

    public function testValue()
    {
        $uuid1 = new Uuid();

        $this->assertInternalType('string', $uuid1->value());
    }

    /**
     * @expectedException \Puzzle\ValueObjects\Exceptions\InvalidUuid
     */
    public function testInvalidUuid()
    {
        new Uuid('oui');
    }

    /**
     * @dataProvider providerTestEquals
     */
    public function testEquals(bool $expected, ?string $id1, ?string $id2)
    {
        $uuid1 = new Uuid($id1);
        $uuid2 = new Uuid($id2);

        $this->assertSame($expected, $uuid1->equals($uuid2));
    }

    public function providerTestEquals()
    {
        return [
            [false, null, null],
            [false, null, '1163c8e7-4a15-452a-8026-88bdc6e12a80'],
            [false, '1163c8e7-4a15-452a-8026-88bdc6e12a80', null],
            [false, '1163c8e7-4a15-452a-8026-88bdc6e12a80', '8c804f96-5e81-491c-9543-dca9212a76af'],
            [true, '1163c8e7-4a15-452a-8026-88bdc6e12a80', '1163c8e7-4a15-452a-8026-88bdc6e12a80'],
        ];
    }

    public function testJsonSerialize()
    {
        $uuid = new Uuid();
        $this->assertSame($uuid->value(), $uuid->jsonSerialize());
    }
}
