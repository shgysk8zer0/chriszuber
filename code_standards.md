# Project Code Standards

Author: [Chris Zuber](<mailto:shgysk8zer0@gmail.com>)  
Created: 2015-01-01  
Modified: 2014-01-02
* * *
## Overview
Please see
+ [PSR-1](<http://www.php-fig.org/psr/psr-1/> "Basic Coding Standard")
+ [PSR-2](<http://www.php-fig.org/psr/psr-2/> "Coding Style Guide")
+ [PSR-3](<http://www.php-fig.org/psr/psr-3/> "Logger Interface")
+ [PSR-4](<http://www.php-fig.org/psr/psr-4/> "Autoloader")

The purpose of this document is to apply the specifications as stated by
[PHP-FIG](<http://www.php-fig.org/> "PHP Framework Interop Group") with as little
alteration as necessary in order to provide better compatibility with autoloaders
such as [`spl_autoload()`](<http://php.net/manual/en/function.spl-autoload.php>)
natively in PHP as well as provided by [Composer](https://getcomposer.org/).

This document also aims to correct the grammar used &mdash; E.G. curly braces
<q>**SHALL**</q> be placed on a new line after function declarations rather than
<q>**MUST**</q> as defined in [Terminology](<#terminology>).

It is *not* meant to compete against any <abbr title="PHP Standard Recommendation">PSR</abbr>
 given by  <abbr title="PHP Framework Interop Group">PHP-FIG</abbr>, but rather
 to address the same issues under different circumstances.

## Terminology
> The key words "MUST", "MUST NOT", "REQUIRED", "SHALL", "SHALL NOT", "SHOULD",
 "SHOULD NOT", "RECOMMENDED", "MAY", and "OPTIONAL" in this document are to be
 interpreted as described in
[RFC 2119](<http://tools.ietf.org/html/rfc2119> "Key words for use in RFCs to Indicate Requirement Levels").

I make the following exception to distinguish between **SHALL** and **MUST**:

+ **MUST**, **MUST NOT**, and **REQUIRED** signify that the reason is functional
and to ensure compatibility.
+ **SHALL** and **SHALL NOT** mean that, while still a requirement, the reason is
preference rather than pure necessity.

## Naming Things

### &#8729; Files

Files **MUST** be named such that they are compatible on all Operating Systems.
They **SHALL** contain *all* lower case or *all* upper case alpha-numeric characters,
and underscores in their names.  
Names **SHOULD** be all lower case, except for those which are automatically
generated and/or are of special significance such as <samp>README.md</samp>  
All files **SHOULD** have a name portion, except for files of special significance
such as <samp>.htaccess</samp>  
All extensions **MUST** be preceded by a <q>.</q>, **SHALL** be lower case and
alpha-numeric only, and **SHOULD NOT** have a depth greater than two extensions.
They **SHALL NOT** contain special characters, spaces, or a combination of upper
and lower case characters.
They **SHOULD** contain one or two extension as appropriate to the
mime-type.  
`([A-Z\d_]+|[a-z\d_]+)?(\.[a-z\d]+){0,2}`

### &#8729; Functions

Functions **SHALL** contain at minimal documentation as defined in
[Documentation](<#documentation>) containing a brief description, a list of
parameters including type along with a description, and the return including type.

Any function not accepting parameters or without a return should declare <q>void</q>
for these.
```PHP
    /**
     * {my_fucntion description}
     *
     * @param array                 $array   [{description}]
     * @param string                $string  [{description}]
     * @param bool                  $bool    [{description}]
     * @param int                   $int     [{description}]
     * @param float                 $float   [{description}]
     * @param \vendor\package\class $class   [{description}]
     *
     * @return {type}                        [{description}]
     */
    function my_function(
            array $array = [],
            $string = '',
            $bool = false,
            $int = 9000,
            $float = 3.14,
            \vendor\package\class $class
    )
    {
        //Function code... Do stuff
        return $results;
    }

    /**
     * {second_function description}
     *
     * @param void
     * @return void
     */
    function second_function()
    {
        \\Do Stuff
    }
```

Functions **SHALL** be named in all lower case, with words separated by <q>\_</q>
`[a-z\d]+(_[a-z\d]+)*`  

Functions **SHALL** maintain as small a scope as necessary. Specifically,
functions **SHALL NOT** make use of
+ `global`
+ `$_GLOBALS`
+ `$_REQUEST`
+ `$_SESSION`
+ `$_COOKIE`

unless that is their sole purpose.

Any function of sufficient complexity **SHOULD** use `array_(map|filter|walk)` in
favor of `foreach` for loops, using `use()` as necessary.

### &#8729; Namespaces
Classes **SHOULD** contain only lowercase alpha-numeric characters and underscores
aside from namespace separators <q>\</q> unless you are willing to update your
`composer.json` or other autoloader configuration file with every new
class/namespace to accomodate the lower case file name requirement.  
`(\[a-z\d_]+)+`  

This is for best compatibility between autoloaders since `spl_autoload` loads
files from `include_path` as lower case, whereas Composer is case sensitive. In
order for namespaces to work with both, namepspaces are required to be entirely
lower case.

## Code Format

## Limiting component scope

## Documentation

## Images, Fonts, & other Media

All images, fonts, and other forms of media **MUST** use [Creative Commons](<http://creativecommons.org/>)
or similar license allowing adaption, modification, and redistribution.

## Minimal Class Structure

Class Doc Comments **MUST** be included after namespace declaration. This is to
ensure compatibility with `ReflectionClass`.

All method documentation **SHALL** include at minimal
+ Method description
+ `@param`
+ `@return`

```PHP
namespace {namespace};
/**
 * {Class description}
 *
 * @author {First} {Last} <{user@example.com}>
 * @package {similar to namespace}
 * @version {semantic version, E.G. 1.2.3}
 * @copyright {YYYY}, {First} {Last}
 * @license {GPL-3 compatible license}
 */
[final|abstract] (class|interface|trait) {classname} [extends {child class}] [implements {interface}]
{
    /**
     * {method description}
     * @param {type}    [{description}]
     * @return {type}   [{description}]
     */
    [final] {visibility} {method name}([type hint [param[ = {default value}]]])
    {
        // Method code
    }
}
```

## Contributing
