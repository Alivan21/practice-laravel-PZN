<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Data\Foo;
use App\Data\Bar;
use App\Data\Person;
use App\Services\HelloService;
use App\Services\HelloServiceIndonesia;

class ServiceContainerTest extends TestCase
{
  // public function testDependency()
  // {
  //   $foo = $this->app->make(Foo::class); // new Foo()
  //   $foo2 = $this->app->make(Foo::class); // new Foo()

  //   self::assertEquals('Foo', $foo->foo());
  //   self::assertEquals('Foo', $foo2->foo());
  //   self::assertNotSame($foo, $foo2);
  // }

  public function testBind()
  {
    // $person = $this->app->make(Person::class); // new Person()
    // self::assertNotNull($person);

    $this->app->bind(Person::class, function ($app) {
      return new Person("Alivan", "Alfan");
    });

    $person = $this->app->make(Person::class); // closure() // new Person("Alivan", "Alfan)
    $person2 = $this->app->make(Person::class); // closure() // new Person("Alivan", "Alfan)

    self::assertEquals("Alivan", $person->firstName);
    self::assertEquals("Alfan", $person->lastName);

    self::assertNotSame($person, $person2);
  }

  public function testSingleton()
  {
    $this->app->singleton(Person::class, function ($app) {
      return new Person("Alivan", "Alfan");
    });

    $person = $this->app->make(Person::class);
    $person2 = $this->app->make(Person::class);

    self::assertEquals("Alivan", $person->firstName); // closure() // new Person("Alivan", "Alfan) // if not exist
    self::assertEquals("Alivan", $person2->firstName); // return existing 
    self::assertSame($person, $person2);
  }

  public function testInstance()
  {
    $person = new Person("Alivan", "Alfan");
    $this->app->instance(Person::class, $person);

    $person1 = $this->app->make(Person::class); // $person
    $person2 = $this->app->make(Person::class); // $person

    self::assertEquals("Alivan", $person1->firstName); // closure() // new Person("Alivan", "Alfan) // if not exist
    self::assertEquals("Alivan", $person2->firstName); // return existing 
    self::assertSame($person1, $person2);
  }

  public function testDependencyInjection()
  {
    $this->app->singleton(Foo::class, function ($app) {
      return new Foo();
    });

    $foo = $this->app->make(Foo::class);
    $bar = $this->app->make(Bar::class);

    self::assertSame($foo, $bar->foo);
  }

  public function testDependencyInjectionClosure()
  {
    $this->app->singleton(Foo::class, function ($app) {
      return new Foo();
    });

    $this->app->singleton(Bar::class, function ($app) {
      return new Bar($app->make(Foo::class));
    });

    $bar1 = $this->app->make(Bar::class);
    $bar2 = $this->app->make(Bar::class);
    self::assertSame($bar1, $bar2);
  }

  public function testInterfaceToClass()
  {
    // $this->app->singleton(HelloService::class, HelloServiceIndonesia::class);

    $this->app->singleton(HelloService::class, function ($app) {
      return new HelloServiceIndonesia();
    });

    $heloServices = $this->app->make(HelloService::class);
    self::assertEquals("Halo Alfan", $heloServices->hello("Alfan"));
  }
}
