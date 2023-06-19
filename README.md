# Cast Language
A simple domain-modelling friendly programming language.
The playground full of examples can be found here: https://cast-lang.eu.

## Quick Example
![Short example](https://cast-lang.eu/cast-pic.png?ver=1)

## Getting Started

### Highlights

- All types and functions are also values. They can be passed and returned to/from functions.
- All values represent pure data. On top of every type one can attach behaviour.
    Example: for the value `y = [a: 1, b: 2]` the expression `y.a` is `1` while `y->values` is `[1, 2]`
- All values are immutable except for values of type `Mutable<T>`. Therefore, all other types
    are covariant (e.g. `Array<Integer>` is a subtype of `Array<Real>`)
- Subtyping is supported for every type, even for types like `Integer` or any union type.
- Type aliases allow for structural typing as well as casts between different types.
- There is no special construct for interfaces but there is a way to use interfaces by 
    defining casts.
- The built in types `Integer`, `Real`, `String`, `Map`, `Record`, `Type` all
    support constraints (e.g. `Real<-3.14..3.14>`, `Array<1..10>`, `Map<String<..20>, 5..10>`)
    making them both domain-modelling and OpenApi friendly.
- In addition:
  - every tuple type is a subtype of an array type (e.g. `[Integer, String] <: Array<Integer|String>, 2..2>`), 
  - every record type is a subtype of a map type (e.g. `[x: Real, y: Real] <: Map<Real, 2..2>`)
  - every enumeration subtype is a subtype of a string type (e.g. `Status = :[Pending, Accepted, Rejected] <: String`)

### Installation
While the language itself is not bound to any specific
runtime environment, the current implementation requires
PHP. Therefore, it requires a PHP installation (8.2 or newer).
The easiest way to try out the language is to write
a file in the /cast-src folder and then execute /demo/index.php
either through a web-server or via the command line.  

#### Basic Syntax

Every `Cast` file starts with a module name followed by any number
of top-level expression. They could be `type definitions`, 
`constant values` and `type casts`.

Example:
``` 
Person <: [age: Integer<0..150>, name: String<1..>];
p = Person[age: 20, name: "John Doe"];
Person ==> String :: #.name;
```

The entry point is usually a function called `main`:
```
main = ^Array<String> => Any :: {
   {"Hello World!"}->PRINT
};
```


_The documentation below is under construction._

## Types

### Integer values and types

The Integer type contains all integer numbers.
Integer<a..b> denotes a subset of integers in the range from a to b.
If there is no lower limit, they range can be denoted as Integer<..b> and
similarly if there is no upper limit - as Integer<a..>
The main type Integer is a shorthand of Integer<..>.
Every type Integer<a..b> is a subtype of Real<a..b>.

```
{5}->type
// result: Integer<5..5>
```

As all other types the Integer can be aliased or subtyped:

Alias:
```PositiveInteger = Integer<1..>```\
Subtype:
```
PrimeNumber <: Integer<2..> @ NotAPrimeNumber :: {
    //make sure that the integer is a prime number 
    //and throw NotAPrimeNumber otherwise 
}
```

Subtyped values can be used as regular integers:
```
a = PrimeNumber(7);
x = {a} + 4                 
// result: 11 
```

---
### Real values and types

The Real type contains all real numbers.
Real<a..b> denotes a subset of real numbers in the range from a to b.
If there is no lower limit, they range can be denoted as Real<..b> and
similarly if there is no upper limit - as Real<a..>
The main type Real is a shorthand of Real<..>.
Every type Real<a..b> is a supertype of Integer<a..b>.

All integer numbers are also real numbers.

```
{3.14}->type
// result: Real<3.14..3.14>
```

As all other types the Real can be aliased or subtyped:

Alias:
```Radians = Real<-3.14..3.14>```\
Subtype:
```
TwoDigitDecimal <: Real @ NotADecimal :: {
    //make sure that the real is a decimal 
    //and throw NotADecimal otherwise 
}
```

Subtyped values can be used as regular real numbers:
```
a = TwoDigitDecimal(3.14);
x = {a} + 4
// result: 7.14 
```

---
### String values and types

The String type contains all strings.
String<a..b> denotes a subset of string in the length from a to b.
If there is no lower limit, they range can be denoted as String<..b> and
similarly if there is no upper limit - as String<a..>
The main type String is a shorthand of String<..>.

```
{"Hello"}->type
// result: String<5..5>
```

As all other types the String can be aliased or subtyped:

Alias:
```NonEmptyString = String<1..>```\
Subtype:
```
Uuid <: String<36..36> @ InvalidUuid :: {
    //make sure that the string is a UUID 
    //and throw InvalidUuid otherwise 
}
```

Subtyped values can be used as regular strings:
```
a = Uuid("00000000-1111-2222-3333-444444444444");
x = {"Value:"} + {a}
// result: "Value: 00000000-1111-2222-3333-444444444444" 
```



### Boolean values and types

The Boolean type contains two values - true and false.
The type `True` is a subtype of `Boolean` and contains the value `true`.
The type `False` is a subtype of `Boolean` and contains the value `false`.

```
{true}->type
// result: True
```

As all other types the Boolean can be aliased or subtyped:

Alias:
```ProductIsActive = Boolean```\



### Null value and type

The `Null` type contains a single value - `null`.
This is the default value passed to a function if no parameter is provided,
and it can be a return value if no result is expected

```
{null}->type
// result: Null
```

As all other types the Null can be aliased or subtyped:

Subtype:
```
UnknownError <: Null;
```



### Array and tuple values and types

A list of values is represented by the following syntax: ```[v1, v2, ... vn]```
The empty list is ```[]``` (to be distinguished from the empty map ```[:]```).

The array type represents a list of values. 
For every list of values there exists a tuple type that is accurately representing
the list. This tuple type is a subtype of the corresponding array type.

Notation: ```Array<T, a..b>``` where T is the type of the values and the range a..b
represents the minimum and the maximum length of the array. If T is the Any type it 
can be omitted. The range with no lower bound can be marked as ```..b``` and the range
with no upper bound - with ```a..```. The unbounded range ```..``` can be omitted,
so ```Array``` is a shorthand of ```Array<Any, 0..Infinity>.

```
t = {[1, "Hello", 4]}->type
// result: [Integer<1..1>, String<5..5>, Integer<4..4>]
t <: Array<(Integer|String), 3..3>
// result: true
```

As all other types the Array and Tuple types can be aliased or subtyped:

Alias:\
```Point2d = [Real, Real]```\
```ProductList = Array<Prouct>```

Tuple elements can be accessed with the dot syntax (0-based):
```
a = [1, 3.14, false];
a.1         
// result: 3.14  
```

The general way to access Array values is by using the ```item``` method:
```
a = [1, 3.14, false];
a->item(1);
// result: 3.14
```

The array type supports the ```Invokable``` contract and therefore 
a function call is also possible: 
```
a = [1, 3.14, false];
a(1)
// result: 3.14
```

Depending on the declared and inferred type definitions the item access may
as well throw and error ```ArrayItemNotFound```

An array or a tuple with N elements has indexes from 0 to N-1.

**Subtyping**:
- Any tuple type ```[T1, T2, ... Tn]``` is a subtype of Array<T1|T2|...Tn, n..n>.
- Any Array of type Array<T, a..b> is a subtype of Array<T', a'..b'> 
if T is subtype of T' and a..b is contained within a'..b' (covariant due to immutability).
- Any tuple type ```[T1, T2, ... Tn]``` is a subtype of another tuple type
```[T'1, T'2, ... T'm]``` if n >= m and every Ti is a subtype of T'i.



### Map and record values and types

A list of key/value pairs is represented by the following syntax: ```[k1: v1, k2: v2, ... kn: vn]```
The empty list is ```[:]``` (to be distinguished from the empty array ```[]```).

The map type represents a map of key/value pairs. 
For every list of key/value pairs there exists a record type that is accurately representing
the list. This record type is a subtype of the corresponding map type.

Notation: ```Map<T, a..b>``` where T is the type of the values and the range a..b
represents the minimum and the maximum length of the array. If T is the Any type it 
can be omitted. The range with no lower bound can be marked as ```..b``` and the range
with no upper bound - with ```a..```. The unbounded range ```..``` can be omitted,
so ```Map``` is a shorthand of ```Map<Any, 0..Infinity>.

```
t = {[a: 1, b: "Hello", c: 4]}->type
// result: [a: Integer<1..1>, b: String<5..5>, c: Integer<4..4>]
t <: Map<(Integer|String), 3..3>
// result: true
```

As all other types the Map and Record types can be aliased or subtyped:

Alias:\
```Point2d = [x: Real, y: Real]```\
```ProductsById = Map<Prouct>```

Record elements can be accessed with the dot syntax:
```
a = [x: 1, y: 3.14, z: false];
a.y
// result: 3.14  
```

The general way to access Map values is by using the ```item``` method:
```
a = [x: 1, y: 3.14, z: false];
a->item("y");
// result: 3.14
```

The map type supports the ```Invokable``` contract and therefore 
a function call is also possible: 
```
a = [x: 1, y: 3.14, z: false];
a("y")
// result: 3.14
```

Depending on the declared and inferred type definitions the item access may
as well throw and error ```MapItemNotFound```

The order of the elements is not significant.

**Subtyping**:
- Any record type ```[k1: T1, k2: T2, ... kn: Tn]``` is a subtype of Map<T1|T2|...Tn, n..n>.
- Any Map of type Map<T, a..b> is a subtype of Map<T', a'..b'> 
if T is subtype of T' and a..b is contained within a'..b'  (covariant due to immutability).
- Any record type ```[k1: T1, k2: T2, ... kn: Tn]``` is a subtype of another record type
```[k'1: T'1, k'2: T'2, ... k'm: T'm]``` if n >= m and every key ki exists k'j within k'1 ... k'm
and the corresponding type Ti is a subtype of T'j.



### Function values and types

Every function is a value, and it is composed of a parameter type, return type and a function body.
The syntax is ```^P => R :: body``` and the corresponding type is ```^P => R```.

**Subtyping**:\
A function type ```^P => R``` is a subtype of ```^P' => R'``` is P' is a subtype of
P (the only contravariance in the language) and R is a subtype of R' (regular covariance). 

### Type values (and types)
All types are also values. Their type is ```Type```. Several reflection methods are 
available. For every type ```T``` its type is ```Type<T>```

Example:
```
a = Integer<0..50>;
b = a->type;
/// the type of b is Type<Integer<0..50>>
```

The regular subtyping rules are valid - Type<T> is a subtype of Type<T'> if T is a subtype of T'.


### Mutable values
By default, all values are immutable, and it is encouraged to avoid mutable values.
In some occasions it is necessary to have mutable values. A mutable value is constructed
by specifying the expected type and the initial value - ```Mutable[T, val]```

Warning: the Mutable type is the only invariant type in the language. 
Therefore, if `T'` is a subtype of `T`, `Mutable<T'>` is **not** a subtype of `Mutable<T>`

Usage example:
```
x = Mutable[Integer, 10];
...
y = {x->value} + 4;
x->SET(y);
/// result the new value of x will be 7.
```

_Notice_:
Mutability withing a function local scope is generally possible without using Mutable.
Example:
```
x = 5;
x = {x} + 3
```



### Custom types - type aliases
Type aliases are defined as ```T = T'``` where ```T'``` is any possible type.
Besides convenience, the type aliases enable type casting from and to type aliases.

Example:
```
IntegerOrString = (Integer|String);

IntegerOrString ==> ProductId :: {
    // Define a way to create a ProductId from an Integer or a String value. 
};
```

A builtin type alias is ```JsonValue``` defined as 
```Null|Boolean|String|Integer|Real|Array<JsonValue>|Map<JsonValue>```

**Subtyping**:\
Every type alias is a subtype of its alias and vice versa:
```
IntegerOrString <: (Integer|String);
/// result: true
(Integer|String) <: IntegerOrString;
/// result: true
```

There is no constructor for aliased types but a value can be explicitly cast to it by using
```v->as(IntegerOrString)```

### Custom types - subtypes
Custom subtypes are defined as ```T <: T'``` where the base type ```T'``` is any possible type.
Normally the base type is a record, but it is also possible to subtype Integer or any other builtin
type, thus accessing its behaviour as well. Every subtype definition may provide a
function body which can validate the base value. The result of the function execution
is ignored except in the case when an exception is thrown.

Example:
```
PrimeNumber <: Integer<2..> @ NotAPrimeNumber :: {
    //make sure that the integer is a prime number 
    //and throw NotAPrimeNumber otherwise 
};
...
x = PrimeNumber(7);
```

In order to construct a value belonging to a custom subtype, its name must be used
like a regular function call.  

**Subtyping**:\
Every subtype is a subtype of its base type but not vice versa:
```
PrimeNumber <: Integer<2..>;
/// result: true
Integer<2..> <: PrimeNumber;
/// result: false
```


### Custom types - enumerations
An enumeration type is defined as ```T = :[V1, V2, ... Vn]``` where T.V1, T.V2, ... T.VN 
are all possible values of that type. For every value ```T.Vx``` there exists a corresponding
type ```T[Vx]``` which is a subtype of T.

Example:
```
Suit = :[Spades, Hearts, Diamonds, Clubs];
mySuit = Suit.Diamonds;
mySuitType = Suit[Diamonds];
```

---

***Other types***

There are several types that have no direct corresponding values:

- Any - the top type of the language. Every type T is a subtype of ```Any```. 
This type brings several methods available for every value and type.
- Nothing - the bottom type of the languages. Nothing is a subtype of every type.
The empty array value ```[]``` is of type Array<Nothing, 0..0> and the empty map value
```[:]``` is of type Map<Nothing, 0..0>.
- Union - a type (T1|T2|...|Tn) denotes a type where the value is of one 
of the listed types T1, T2, ..., Tn. Any given method is available on a value of a union
type only if it is available for all types that compose it.
- Intersection - a type (T1&T2&...&Tn) denotes a type where the value is of all the
listed types T1, T2, ..., Tn. Any given method is available on a value of an intersection
type only if it is available for one of the types that compose it.



### Function Parameters

Every function receives one value which is accessible through the variable ```#```.
Since this is not practical in most of the cases a function can be declared as expecting a
Record (or a Tuple) value thus in practice supporting multiple arguments.
When a function expects no parameters, the type Null should be used to denote this.

Examples:\
```fn1 = ^Integer => Integer :: {#} + 1;```\
```fn2 = ^[num: Integer] => Integer :: {#num} + 1;```\
```fn3 = ^[Integer, Integer] => Integer :: {#0} + {#1};```\
```fn4 = ^[x: Integer, y: Integer] => Integer :: {#x} + {#y};```
```fn5 = ^Null => Integer :: 42;```

A function is called by passing the parameter in round brackets. If the function can
be called without parameters (parameter type ```Null```) then an empty pair of brackets
is allowed. In order to have simpler and shorter code, when passing a Tuple or a Record 
literal, the brackets can be omitted. And in case a Record parameter is expected,
a Tuple with the same length can be passed, and it will be automatically converted.

Examples:\
```fn1(3);```\
```fn2([num: 3]);``` the same as ```fn2[num: 3];``` and ```fn2[3];```\
```fn3[1, 5];```\
```fn4[x: 1, y: 5];``` the same as ```fn4[1, 5];```\
```fn5(null)``` the same as ```fn5()```\

**New instances of custom types**\
The same syntax for functions is used for creating instances of custom types. 
The only difference is that instead of the function, the type name should be given.

Example:
```
PrimeNumber <: Integer @ NotAPrimeNumber :: { ... };
Point <: [x: Real, y: Real];
...
num = PrimeNumber(7);
p = Point([x: 3.14, y: 2.71]); //full syntax ... the same as
p = Point[x: 3.14, y: 2.71]; //shorter syntax ... the same as
p = Point[3.14, 2.71]; //shortest option possible
```

**Method calls**\
An extended, object-oriented-like way of calling function is supported via the special 
syntax ```target->method(parameter)```. The target object is accessible via the local 
variable named ```$```. The syntax is the same as for the regular function calls with
one addition - when no parameter is expected (type ```Null```) the brackets are not 
required.

Example:
```
x = "Hello world";
l = x->length; // 11
xr = x->contains("wor"); // true
sub = x->substring[start: 3, end: 8]; // "lo wor" 
```

---

**Function return type and exceptions**
There are several ways to specify a function return type:
- No exceptions possible ```^P => R```
- Specific exceptions possible ```^P => R @ E```
- Unknown exceptions possible ```^P => R @@```
- Specific and unknown exceptions possible ```^P => R @@ E```

**Execution flow within a function**
- _standard flow_: When a function is executed, by default it is evaluated as an expression and the expression value
is what the function returns.
- _direct flow_: at any place within a function if a return expression is evaluated ```=> E```
the value of E will be the function return value
- _error flow_: at any place within a function execution if a throw expression is evaluated ```@ E```
the function will not return a value but instead throw the value of the expression as an exception.
The same applies if a function called within the function receives a value as an exception. It will be
rethrown unless caught as specified bellow. The compiler will make sure the all specific exceptions
are listed in the function return type declaration or unknown exceptions are allowed.

**Catching exceptions**
For every expression the compiler will acquire the information about the expected exceptions within
the expression. They will be attached to the expression unless caught in one of the two possible ways:
- ```@ { E }``` - it will catch all specific exceptions inferred by the compiler, and it will rethrow
all unknown ones. The type of the resulting expression will be a union type of the expression type
and all the specific exceptions.
- ```@@ { E }``` - it will catch all exceptions and the type of the resulting expression will
be a union of the expression type and the builtin ```RuntimeError``` type which wraps the caught exception.

Example 1:
```
UnknownProduct <: Null;
getProductById = ^ProductId => Product @@ UnknownProduct :: { ... };
...
p = getProductById(productId); 
//the type of p is Product but an exception UnknownProduct or other might be thrown
...
p = @ {getProductById(productId) }; 
//the type of p is Product|UnknownProduct but an unspecified exception might be thrown
...
p = @@ {getProductById(productId) }; 
//the type of p is Product|RuntimeError
```

Example 2:
```
CannotCalculateSquareRoot <: Null;
calculateSquareRoot = ^Real => Real @ CannotCalculateSquareRoot :: { ... };
...
r = calculateSquareRoot(81); 
//the type of r is Real but an exception CannotCalculateSquareRoot might be thrown
...
r = @ {getProductById(productId) }; 
//the type of r is Real|CannotCalculateSquareRoot
...
r = @@ {getProductById(productId) }; 
//the type of r is Real|RuntimeError
```


### Defining casts

For every pair of named types (alias, custom subtype, enumeration) a cast method 
can be defined thus allowing a conversion from one type to another.
Example:
```
Point <: [x: Real, y: Real];
Point ==> String :: {
    {""}->concatList["{", {$.x}->asText, ",", {$.y}->asText, "}"]
};
...
p = [3.14, 2];
p->as(String); // "{3.14,2}"
```








***Behavior reference [WIP]***


---

```Boolean->binaryLogicAnd(Boolean) => Boolean```\
Example:\
```{true} && {false}``` &rArr; ```false```

---

```Boolean->binaryLogicOr(Boolean) => Boolean```\
Example:\
```{true} || {false}``` &rArr; ```true```

---

```Boolean->unaryLogicNot => Boolean```\
Example:\
```! {true}``` &rArr; ```false```

---

```Real->binaryPlus(Integer|Real) => Real```\
Example:\
```{3.14} + {8}``` &rArr; ```11.14```

---

```Real->binaryMinus(Integer|Real) => Real```\
Example:\
```{3.14} - {8}``` &rArr; ```-4.86```

---

```Real->binaryMultiplication(Integer|Real) => Real```\
Example:\
```{3.14} * {8}``` &rArr; ```25.12```


---

```Integer->binaryPlus(Integer) => Integer```\
Example:\
```{5} + {8}``` &rArr; ```13```

---

```Integer->binaryMinus(Integer) => Integer```\
Example:\
```{5} - {8}``` &rArr; ```-3```

---

```Integer->binaryMultiplication(Integer) => Integer```\
Example:\
```{5} * {8}``` &rArr; ```40```

---

```Integer->binaryPlus(Real) => Real```\
Example:\
```{5} + {3.14}``` &rArr; ```8.14```

---

```Integer->binaryMinus(Real) => Real```\
Example:\
```{5} - {3.14}``` &rArr; ```-1.86```

---

```Integer->binaryMultiplication(Real) => Real```\
Example:\
```{5} * {3.14}``` &rArr; ```15.7```

---

```Integer->upTo(Integer) => Array<Integer>```\
Examples:\
```{5}->upTo(8)``` &rArr; ```[5, 6, 7, 8]```\
```{8}->upTo(5)``` &rArr; ```[]```

---

```Integer->downTo(Integer) => Array<Integer>```\
Examples:\
```{5}->upTo(8)``` &rArr; ```[]```\
```{8}->upTo(5)``` &rArr; ```[8, 7, 6, 5]```

---

```Integer->binaryAnd(Integer) => Integer```\
Example:\
```{5} & {12}``` &rArr; ```4```

---

```Integer->binaryOr(Integer) => Integer```\
Example:\
```{5} | {12}``` &rArr; ```13```

---

```Integer->binaryXor(Integer) => Integer```\
Example:\
```{5} ^ {12}``` &rArr; ```9```

---

(todo unary not)

---

```String->PRINT => String```\
Example:\
```{"Hello World"}->PRINT``` &rArr; ```"Hello World"``` and printed to the standard output

---

```String->jsonStringToValue => JsonValue @ InvalidJsonValue```\
Example:\
```{"[1, false, null, {}]"}->jsonStringToValue``` &rArr; ```[1, false, null, [:]]```
---
```String->asIntegerNumber => Integer @ StringIsNoIntegerNumber```\
Examples:\
```{"42"}->asIntegerNumber``` &rArr; ```42```\
```{"3.14"}->asIntegerNumber``` &rArr; ```StringIsNoIntegerNumber(3.14)```\
```{"Hello World"}->asIntegerNumber``` &rArr; ```StringIsNoIntegerNumber("Hello World")```

---

```String->asRealNumber => Real @ StringIsNoRealNumber```\
Examples:\
```{"42"}->asRealNumber``` &rArr; ```42```\
```{"3.14"}->asRealNumber``` &rArr; ```3.14```\
```{"Hello World"}->asRealNumber``` &rArr; ```StringIsNoRealNumber("Hello World")```

---

```String->length => Integer```\
Example:\
```{"Hello World"}->length``` &rArr; ```11```

---

```String->reverse => String```\
Example:\
```{"Hello World"}->reverse``` &rArr; ```"dlroW olleH"```

---

```String->toLowerCase => String```\
Example:\
```{"Hello World"}->toLowerCase``` &rArr; ```"hello world"```

---

```String->toUpperCase => String```\
Example:\
```{"Hello World"}->toUpperCase``` &rArr; ```"HELLO WORLD"```

---

```String->trim => String```\
Example:\
```{"  hi!  "}->trim``` &rArr; ```"hi!"```

---

```String->trimLeft => String```\
Example:\
```{"  hi!  "}->trimLeft``` &rArr; ```"hi!   "```

---

```String->trimRight => String```\
Example:\
```{"  hi!  "}->trimRight``` &rArr; ```"   hi!"```

---

```String->concatList(Array<String>) => String```\
Example:\
```{"Start"}->concatList["To", "End"]``` &rArr; ```"StartToEnd"```

---

```String->concat(String) => String```\
Example:\
```{"Start"}->concat("End")``` &rArr; ```"StartEnd"```

---

```String->contains(String<1..>) => Boolean```\
Example:\
```{"Start"}->contains("Star")``` &rArr; ```true```\
```{"Start"}->contains("art")``` &rArr; ```true```\
```{"Start"}->contains("trap")``` &rArr; ```false```

---

```String->startsWith(String<1..>) => Boolean```\
Example:\
```{"Start"}->startsWith("Star")``` &rArr; ```true```\
```{"Start"}->startsWith("art")``` &rArr; ```false```\
```{"Start"}->startsWith("trap")``` &rArr; ```false```

---

```String->endsWith(String<1..>) => Boolean```\
Example:\
```{"Start"}->endsWith("Star")``` &rArr; ```false```\
```{"Start"}->endsWith("art")``` &rArr; ```true```\
```{"Start"}->endsWith("trap")``` &rArr; ```false```

---

```String->positionOf(String<1..>) => Integer @ SubstringNotInString```\
Example:\
```{"Start"}->positionOf("Star")``` &rArr; ```0```\
```{"Start"}->positionOf("t")``` &rArr; ```1```\
```{"Start"}->positionOf("trap")``` &rArr; ```SubstringNotInString()```

---

```String->lastPositionOf(String<1..>) => Integer @ SubstringNotInString```\
Examples:\
```{"Start"}->lastPositionOf("Star")``` &rArr; ```0```\
```{"Start"}->lastPositionOf("t")``` &rArr; ```4```\
```{"Start"}->lastPositionOf("trap")``` &rArr; ```SubstringNotInString()```

---

```String->split(String<1..>) => Array<String>```\
Example:\
```{"Start"}->split("t")``` &rArr; ```["S", "ar", ""]```

---

```String->chunk(Integer<1..>) => Array<String>```\
Example:\
```{"Start"}->chunk(2)``` &rArr; ```["St", "ar", "t"]```

---

```String->padLeft[length: Integer<0..>, padString: String<1..>] => String```\
Example:\
```{"Start"}->padLeft[length: 10, padString: "-*"]``` &rArr; ```"Start-*-*-"```

---

```String->padRight[length: Integer<0..>, padString: String<1..>] => String```\
Example:\
```{"Start"}->padRight[length: 10, padString: "-*"]``` &rArr; ```"-*-*-Start"```

---

```String->substring[start: Integer<0..>, end: Integer<0..>] => String```\
Example:\
```{"Start"}->substring[start: 1, end: 3]``` &rArr; ```"tar"```

---

```String->substring[start: Integer<0..>, length: Integer<0..>] => String```\
Example:\
```{"Start"}->substring[start: 2, length: 2]``` &rArr; ```"ar"```

---

```Array<[String, T]>->asMap => Map<T>```\
Example:\
```{[["k", "V"], ["n", 9]]}->asMap``` &rArr; ```[k: "V", n: 9]```

---

```Array->length => Integer```\
Example:\
```{[1, 2, 3.14]}->length``` &rArr; ```3```

---

```Array<T>->item(Integer) => T @ ArrayItemNotFound```\
Examples:\
```{[1, 2, 5]}->item(1)``` &rArr; ```2```\
```{[1, 2, 5]}->item(4)``` &rArr; ```ArrayItemNotFound(4)```

---

```Array<T>->contains(T) => Boolean```\
Examples:\
```{[1, 2, 5]}->contains(2)``` &rArr; ```true```\
```{[1, 2, 5]}->contains(7)``` &rArr; ```false```

---

```Array<T>->indexOf(T) => Integer @ ArrayItemNotFound```\
Examples:\
```{[1, 2, 5]}->indexOf(2)``` &rArr; ```1```\
```{[1, 2, 5]}->indexOf(7)``` &rArr; ```ArrayItemNotFound(7)```

---

```Array<T>->without(T) => T @ ArrayItemNotFound```\
Examples:\
```{[1, 2, 5]}->without(2)``` &rArr; ```[1, 5]```\
```{[1, 2, 5]}->without(7)``` &rArr; ```ArrayItemNotFound(7)```

---

```Array<String>->combineAsText => String```\
Example:\
```{["Hello", " ", "world", "!"]}->combineAsText``` &rArr; ```"Hello world!"```

---

```Array<String>->countValues => Map<Integer>```\
Example:\
```{["hello", "world", "world"]}->countValues``` &rArr; ```[hello: 1, world: 2]```

---

```Array<T>->withoutByIndex(Integer<0..>) => T @ ArrayItemNotFound```\
Examples:\
```{[1, 2, 5]}->withoutByIndex(1)``` &rArr; ```[1, 5]```\
```{[1, 2, 5]}->withoutByIndex(7)``` &rArr; ```ArrayItemNotFound(7)```

---

```Array<T>->withoutFirst => [array: Array<T>, element: T] @ ArrayItemNotFound```\
Examples:\
```{[1, 2, 5]}->withoutFirst``` &rArr; ```[array: [2, 5], element: 1]```\
```{[]}->withoutFirst``` &rArr; ```ArrayItemNotFound()```

---

```Array<T>->withoutLast => [array: Array<T>, element: T] @ ArrayItemNotFound```\
Examples:\
```{[1, 2, 5]}->withoutLast``` &rArr; ```[array: [1, 2], element: 5]```\
```{[]}->withoutLast``` &rArr; ```ArrayItemNotFound()```

---

```Array<T>->pad[length: Integer<1..>, value: S] => Array<T|S>```\
Example:\
```{[1, 2, 5]}->pad[length: 5, value: 3.14]``` &rArr; ```[1, 2, 5, 3.14, 3.14]```

---

```Array<T>->insertFirst(S) => Array<T|S>```\
Example:\
```{[1, 2, 5]}->insertFirst(3.14)``` &rArr; ```[3.14, 1, 2, 5]```

---

```Array<T>->insertLast(S) => Array<T|S>```\
Example:\
```{[1, 2, 5]}->insertLast(3.14)``` &rArr; ```[1, 2, 5, 3.14]```

---

```Array<T>->appendWith(Array<S>) => Array<T|S>```\
Example:\
```{[1, 2, 5]}->appendWith[3.14, "Hi!"]``` &rArr; ```[1, 2, 5, 3.14, "Hi"]```

---

```Array<T>->sort => Array<T>``` for ```T <: Integer|Real|String```\
Examples:\
```{[4, 11, 5]}->sort``` &rArr; ```[4, 5, 11]```\
```{["4", "11", "5"]}->sort``` &rArr; ```["11", "4", "5"]```

---

```Array<T>->unique => Array<T>``` for ```T <: Integer|Real|String```\
Examples:\
```{[4, 11, 5, 11]}->sort``` &rArr; ```[4, 11, 5]```\
```{["4", "11", "Hi!", "11"]}->sort``` &rArr; ```["4", "11", "Hi!"]```

---

```Array<T>->sum => Array<T>``` for ```T <: Integer|Real```\
Example:\
```{[3.14, 1.29, 7.81]}->sum``` &rArr; ```12.24```

---

```Array<T>->min => Array<T>``` for ```T <: Integer|Real```\
Example:\
```{[3.14, 1.29, 7.81]}->sum``` &rArr; ```1.29```

---

```Array<T>->max => Array<T>``` for ```T <: Integer|Real```\
Example:\
```{[3.14, 1.29, 7.81]}->sum``` &rArr; ```7.81```

---

```Array<String>->flip => Map<Integer>```\
Example:\
```{["B", "Hi", "x"]}->flip``` &rArr; ```[B: 0, Hi: 1, x: 2]```

---

```Array<T>->map(^T => S) => Array<S>```\
Example:\
```{["Hello", "!", "Hi"]}->map(^String => Integer :: #->length)``` &rArr; ```[5, 1, 2]```

---

```Map<T>->with(Map<S>) => Map<T|S>```\
Example:\
```{[a: 1, b: 2]}->with[b: 3, c: 4]``` &rArr; ```[a: 1, b: 3, c: 4]```

---

```Map<T>->values => Array<T>```
Example:\
```{[a: 1, b: 2]}->values``` &rArr; ```[1, 2]```

---

```Map<T>->keys => Array<String>```
Example:\
```{[a: 1, b: 2]}->keys``` &rArr; ```["a", "b"]```

---

```Map<String>->flip => Map<String>```\
Example:\
```{[a: "b", c: "d"]}->flip``` &rArr; ```[b: "a", d: "c"]```

---

```Map<T>->map(^T => S) => Map<S>```\
Example:\
```{[a: "Hello", b: "!", c: "Hi"]}->map(^String => Integer :: #->length)``` &rArr; ```[a: 5, b: 1, c: 2]```

---

```Map<T>->mapKeyValue(^[key: String, value: T] => [key: String, value: S]) => Map<S>```\
Example:\
```{[abc: "Hello", value: "World!"]}->mapKeyValue(^[key: String, value: T] => [key: String, value: S] :: [key: #->reverse, value: #->length])``` &rArr; ```[olleH: 5, dlroW: 6]```

---

```Map<T>->mergeWith(Map<S>) => Map<T|S>```\
Example:\
```{[a: 1, b: 2]}->with[b: 3, c: 4]``` &rArr; ```[a: 1, b: 3, c: 4]```

---

```Map<T>->withKeyValue[key: String, value: S] => Map<T|S>```\
Example:\
```{[a: 1, b: 2]}->withKeyValue[key: "c", value: 3.14]``` &rArr; ```[a: 1, b: 2, c: 3.14]```

---

```Map<T>->without(T) => T @ MapItemNotFound```\
Examples:\
```{[a: 1, b: 2, c: 5]}->without(2)``` &rArr; ```[a: 1, c: 5]```\
```{[a: 1, b: 2, c: 5]}->without(7)``` &rArr; ```MapItemNotFound(7)```

---

```Map<T>->withoutByKey(String) => T @ MapItemNotFound```\
Examples:\
```{[a: 1, b: 2, c: 5]}->withoutByIndex("b")``` &rArr; ```[a: 1, c: 5]```\
```{[a: 1, b: 2, c: 5]}->withoutByIndex("f")``` &rArr; ```MapItemNotFound("f")```

---

```Map<T>->item(String) => T @ MapItemNotFound```\
Examples:\
```{[a: 1, b: 2, c: 5]}->item("b")``` &rArr; ```2```\
```{[a: 1, b: 2, c: 5]}->item("f")``` &rArr; ```MapItemNotFound("f")```

---

```Map->keyExists(String) => Boolean```\
Examples:\
```{[a: 1, b: 2, c: 5]}->keyExists("b")``` &rArr; ```true```\
```{[a: 1, b: 2, c: 5]}->keyExists("f")``` &rArr; ```false```

---

```Map<T>->contains(T) => Boolean```\
Examples:\
```{[a: 1, b: 2, c: 5]}->contains(2)``` &rArr; ```true```\
```{[a: 1, b: 2, c: 5]}->contains(7)``` &rArr; ```false```

---

```Map<T>->keyOf(T) => String @ MapItemNotFound```\
Examples:\
```{[a: 1, b: 2, c: 5]}->keyOf(2)``` &rArr; ```"b"```\
```{[a: 1, b: 2, c: 5]}->keyOf(7)``` &rArr; ```MapItemNotFound(7)```

---

```EnumerationValue<T>->textValue => String```\
Example:\
```Suit = :[Spades, Hearts, Diamonds, Clubs];```\
```x = Suit.Diamonds;```\
```x->textValue``` &rArr; ```"Diamonds"```

---

```Enumeration<T>->values => Array<String>```\
Example:\
```Suit = :[Spades, Hearts, Diamonds, Clubs];```\
```Suit->values``` &rArr; ```["Spades", "Hearts", "Diamonds", "Clubs"]```

---


---

```Mutable<T>->value => T```\
Example:\
```x = Mutable<Integer, 10>;```\
```x->value``` &rArr; ```10```

---

```Mutable<T>->SET(T) => T```\
Example:\
```x = Mutable<Integer, 10>;```\
```x->SET(15)``` &rArr; ```15```

---

```Subtype<Record>->with(Record) => Subtype```\
Example:\
```Point <: [x: Real, y: Real];```\
```p = Point[x: 3.14, y: 42];```\
```p->with[y: -10]``` &rArr; ```Point[x: 3.14, y: -10]```

---

```Type<T>->refType => T```\
Example:\
```t = Integer<12..30>->type;```\
```t->refType``` &rArr; ```Integer<12..30>```

---

```Type<Array<T>>->itemType => T```\
Example:\
```t = Array<Integer<1..12>>;```\
```t->itemType``` &rArr; ```Integer<12..30>```

---

```Type<Map<T>>->itemType => T```\
Example:\
```t = Map<Integer<1..12>>;```\
```t->itemType``` &rArr; ```Integer<1..12>```

---

```Type<String>->minLength => Integer<0..>```\
Example:\
```t = String<5..10>;```\
```t->minLength``` &rArr; ```5```

---

```Type<String>->maxLength => Integer<0..>```\
Example:\
```t = String<5..10>;```\
```t->maxLength``` &rArr; ```10```

---

```Type<Array>->minLength => Integer<0..>```\
Example:\
```t = Array<Integer, 5..10>;```\
```t->minLength``` &rArr; ```5```

---

```Type<Array>->maxLength => Integer<0..>```\
Example:\
```t = Array<Integer, 5..10>;```\
```t->maxLength``` &rArr; ```10```

---

```Type<Map>->minLength => Integer<0..>```\
Example:\
```t = Map<Integer, 5..10>;```\
```t->minLength``` &rArr; ```5```

---

```Type<Map>->maxLength => Integer<0..>```\
Example:\
```t = Map<Integer, 5..10>;```\
```t->maxLength``` &rArr; ```10```

---

```Type<Integer>->minValue => Integer```\
Example:\
```t = Integer<12..30>;```\
```t->minValue``` &rArr; ```12```

---

```Type<Integer>->maxValue => Integer```\
Example:\
```t = Integer<12..30>;```\
```t->maxValue``` &rArr; ```30```

---

```Type<Real>->minValue => Real```\
Example:\
```t = Integer<3.14..30>;```\
```t->minValue``` &rArr; ```3.14```

---

```Type<Real>->maxValue => Real```\
Example:\
```t = Integer<3.14..30>;```\
```t->maxValue``` &rArr; ```30```

---

```Type<Enumeration<T>>->values => Array<T>```\
Example:\
```Suit = :[Spades, Hearts, Diamonds, Clubs];```\
```t = Suit;```\
```t->values``` &rArr; ```[Suit.Spades, Suit.Hearts, Suit.Diamonds, Suit.Clubs]```

---

```Type<Enumeration<T>>->value => T```\
Example:\
```Suit = :[Spades, Hearts, Diamonds, Clubs];```\
```t = {Suit.Spades}->type;```\
```t->value``` &rArr; ```Suit.Spades```

---

```Type<Enumeration<T>>->valueWithName(String) => T @ EnumItemNotFound```\
Example:\
```Suit = :[Spades, Hearts, Diamonds, Clubs];```\
```t = Suit;```\
```t->valueWithName("Spades")``` &rArr; ```Suit.Spades```\
```t->valueWithName("Ace")``` &rArr; ```EnumItemNotFound("Ace")```

---

```Any->DEBUG => Any```\
Example:\
```{[3.14, 42]}->DEBUG;``` &rArr; ```[3.14, 42]``` and printed to the standard output

---

```Any->binaryEqual(Any) => Boolean```\
Examples:\
```{[3.14, 42]} == {[3.14, 42]};``` &rArr; ```true```\
```{[3.14, 42]} == {"Hello"};``` &rArr; ```false```

---

```Any->binaryNotEqual(Any) => Boolean```\
Examples:\
```{[3.14, 42]} != {[3.14, 42]};``` &rArr; ```false```\
```{[3.14, 42]} != {"Hello"};``` &rArr; ```true```

---

```Any->binarySubtype(Type) => Boolean```\
Examples:\
```{[3.14, 42]} <: {[Real, Real]};``` &rArr; ```true```\
```{[3.14, 42]} <: Array<Real>;``` &rArr; ```true```\
```{[3.14, 42]} != {[Integer, Integer]};``` &rArr; ```false```
```{[3.14, 42]} <: Array<Integer>;``` &rArr; ```false```\

---

```T->type => Type<T>```\
Example:\
```{[3.14, 42]}->type;``` &rArr; ```[Real<3.14..3.14>, Integer<42..42>]```

---

```Any->as(Type<T>) => T @ CastNotAvailable```\
Examples:\
```{3.14}->as(String);``` &rArr; ```"3.14"```\
```{3.14}->as(Point);``` &rArr; ```CastNotAvailable()```

---

```JsonValue->asJson => String```\
Example:\
```{3.14}->asJson;``` &rArr; ```"3.14"```\

---

```T->asText => String``` where ```T <: Integer|Real|String|Boolean|Null|Type```\
Examples:\
```{3.14}->asText;``` &rArr; ```"3.14"```\
```{Integer<1..12>}->asText;``` &rArr; ```"Integer<1..12>"```\

---

```Any->asBoolean => Boolean```
Examples:\
```{3.14}->asBoolean;``` &rArr; ```true```\
```{0}->asBoolean;``` &rArr; ```false```\
```{"}->asBoolean;``` &rArr; ```false```\
```{"Hi!"}->asBoolean;``` &rArr; ```true```\

---
