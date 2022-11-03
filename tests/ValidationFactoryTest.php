<?php

namespace Illum\Tests\Validation;

use Illuminate\Contracts\Translation\Translator as TranslatorInterface;
use Illum\Validation\Factory;
use Illum\Validation\Validator;
use Mockery as m;
use PHPUnit\Framework\TestCase;

class ValidationFactoryTest extends TestCase
{
    protected function tearDown(): void
    {
        m::close();
    }

    public function testMakeMethodCreatesValidValidator()
    {
        $translator = m::mock(TranslatorInterface::class);
        $factory = new Factory($translator);
        $validator = $factory->make(['foo' => 'bar'], ['baz' => 'boom']);
        $this->assertEquals($translator, $validator->getTranslator());
        $this->assertEquals(['foo' => 'bar'], $validator->getData());
        $this->assertEquals(['baz' => ['boom']], $validator->getRules());

        $noop1 = function () {
            //
        };
        $noop2 = function () {
            //
        };
        $noop3 = function () {
            //
        };
        $factory->extend('foo', $noop1);
        $factory->extendImplicit('implicit', $noop2);
        $factory->extendDependent('dependent', $noop3);
        $factory->replacer('replacer', $noop3);
        $validator = $factory->make([], []);
        $this->assertEquals(['foo' => $noop1, 'implicit' => $noop2, 'dependent' => $noop3], $validator->extensions);
        $this->assertEquals(['replacer' => $noop3], $validator->replacers);

        $factory->extend('foo', $noop1, 'foo!');
        $factory->extendImplicit('implicit', $noop2, 'implicit!');
        $factory->extendImplicit('dependent', $noop3, 'dependent!');
        $validator = $factory->make([], []);
        $this->assertEquals(['foo' => $noop1, 'implicit' => $noop2, 'dependent' => $noop3], $validator->extensions);
        $this->assertEquals(['foo' => 'foo!', 'implicit' => 'implicit!', 'dependent' => 'dependent!'], $validator->fallbackMessages);
    }

    public function testValidateCallsValidateOnTheValidator()
    {
        $validator = m::mock(Validator::class);
        $translator = m::mock(TranslatorInterface::class);
        $factory = m::mock(Factory::class.'[make]', [$translator]);

        $factory->shouldReceive('make')->once()
                ->with(['foo' => 'bar', 'baz' => 'boom'], ['foo' => 'required'], [], [])
                ->andReturn($validator);

        $validator->shouldReceive('validate')->once()->andReturn(['foo' => 'bar']);

        $validated = $factory->validate(
            ['foo' => 'bar', 'baz' => 'boom'],
            ['foo' => 'required']
        );

        $this->assertEquals(['foo' => 'bar'], $validated);
    }

    public function testCustomResolverIsCalled()
    {
        unset($_SERVER['__validator.factory']);
        $translator = m::mock(TranslatorInterface::class);
        $factory = new Factory($translator);
        $factory->resolver(function ($translator, $data, $rules) {
            $_SERVER['__validator.factory'] = true;

            return new Validator($translator, $data, $rules);
        });
        $validator = $factory->make(['foo' => 'bar'], ['baz' => 'boom']);

        $this->assertTrue($_SERVER['__validator.factory']);
        $this->assertEquals($translator, $validator->getTranslator());
        $this->assertEquals(['foo' => 'bar'], $validator->getData());
        $this->assertEquals(['baz' => ['boom']], $validator->getRules());
        unset($_SERVER['__validator.factory']);
    }

    public function testValidateMethodCanBeCalledPublicly()
    {
        $translator = m::mock(TranslatorInterface::class);
        $factory = new Factory($translator);
        $factory->extend('foo', function ($attribute, $value, $parameters, $validator) {
            return $validator->validateArray($attribute, $value);
        });

        $validator = $factory->make(['bar' => ['baz']], ['bar' => 'foo']);
        $this->assertTrue($validator->passes());
    }
}
