<?php

namespace Phpro\SoapClient\CodeGenerator\Config;

enum DefaultValuesStrategy
{
    /** No default values are applied. */
    case None;

    /** Only WSDL-optional/nullable properties receive = null as default. */
    case OptionalOnly;

    /**
     * All scalar types receive type-appropriate defaults ('', 0, false, 0.0, []), nullable types receive null.
     * Non-nullable complex types get no default since we cannot determine a sensible default value for them.
     */
    case All;

    public static function default(): self
    {
        return self::OptionalOnly;
    }
}
