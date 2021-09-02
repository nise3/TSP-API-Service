<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | The following language lines contain the default error messages used by
    | the validator class. Some of these rules have multiple versions such
    | as the size rules. Feel free to tweak each of these messages here.
    |
    */
    'accepted' => [
        'code' => 1000,
        'message' => 'The :attribute must be accepted.',
    ],
    'active_url' => [
        'code' => 2000,
        'message' => 'The :attribute is not a valid URL.',
    ],
    'after' => [
        'code' => 3000,
        'message' => 'The :attribute must be a date after :date.',
    ],
    'after_or_equal' => [
        'code' => 4000,
        'message' => 'The :attribute must be a date after or equal to :date.',
    ],
    'alpha' => [
        'code' => 5000,
        'message' => 'The :attribute may only contain letters.',
    ],
    'alpha_dash' => [
        'code' => 6000,
        'message' => 'The :attribute may only contain letters, numbers, dashes and underscores.',
    ],
    'alpha_num' => [
        'code' => 7000,
        'message' => 'The :attribute may only contain letters and numbers.',
    ],
    'array' => [
        'code' => 8000,
        'message' => 'The :attribute must be an array.',
    ],
    'before' => [
        'code' => 9000,
        'message' => 'The :attribute must be a date before :date.',
    ],
    'before_or_equal' => [
        'code' => 10000,
        'message' => 'The :attribute must be a date before or equal to :date.',
    ],
    'between' => [
        'code' => 11000,
        'numeric' => [
            'code' => 11001,
            'message' => 'The :attribute must be between :min and :max.',
        ],
        'file' => [
            'code' => 11002,
            'message' => 'The :attribute must be between :min and :max kilobytes.',
        ],
        'string' => [
            'code' => 11003,
            'message' => 'The :attribute must be between :min and :max characters.',
        ],
        'array' => [
            'code' => 11004,
            'message' => 'The :attribute must have between :min and :max items.',
        ],
    ],
    'boolean' => [
        'code' => 12000,
        'message' => 'The :attribute field must be true or false.',
    ],
    'confirmed' => [
        'code' => 13000,
        'message' => 'The :attribute confirmation does not match.',
    ],
    'date' => [
        'code' => 14000,
        'message' => 'The :attribute is not a valid date.',
    ],
    'date_equals' => [
        'code' => 15000,
        'message' => 'The :attribute must be a date equal to :date.',
    ],
    'date_format' => [
        'code' => 16000,
        'message' => 'The :attribute does not match the format :format.',
    ],
    'different' => [
        'code' => 17000,
        'message' => 'The :attribute and :other must be different.',
    ],
    'digits' => [
        'code' => 18000,
        'message' => 'The :attribute must be :digits digits.',
    ],
    'digits_between' => [
        'code' => 19000,
        'message' => 'The :attribute must be between :min and :max digits.',
    ],
    'dimensions' => [
        'code' => 20000,
        'message' => 'The :attribute has invalid image dimensions.',
    ],
    'distinct' => [
        'code' => 21000,
        'message' => 'The :attribute field has a duplicate value.',
    ],
    'email' => [
        'code' => 22000,
        'message' => 'The :attribute must be a valid email address.',
    ],
    'ends_with' => [
        'code' => 23000,
        'message' => 'The :attribute must end with one of the following: :values',
    ],
    'exists' => [
        'code' => 24000,
        'message' => 'The selected :attribute is invalid.',
    ],
    'file' => [
        'code' => 25000,
        'message' => 'The :attribute must be a file.',
    ],
    'filled' => [
        'code' => 26000,
        'message' => 'The :attribute field must have a value.',
    ],
    'gt' => [
        'code' => 27000,
        'numeric' => [
            'code' => 27001,
            'message' => 'The :attribute must be greater than :value.',
        ],
        'file' => [
            'code' => 27002,
            'message' => 'The :attribute must be greater than :value kilobytes.',
        ],
        'string' => [
            'code' => 27003,
            'message' => 'The :attribute must be greater than :value characters.',
        ],
        'array' => [
            'code' => 27004,
            'message' => 'The :attribute must have more than :value items.',
        ],
    ],
    'gte' => [
        'code' => 28000,
        'numeric' => [
            'code' => 28001,
            'message' => 'The :attribute must be greater than or equal :value.',
        ],
        'file' => [
            'code' => 28002,
            'message' => 'The :attribute must be greater than or equal :value kilobytes.',
        ],
        'string' => [
            'code' => 28003,
            'message' => 'The :attribute must be greater than or equal :value characters.',
        ],
        'array' => [
            'code' => 28004,
            'message' => 'The :attribute must have :value items or more.',
        ],
    ],
    'image' => [
        'code' => 29000,
        'message' => 'The :attribute must be an image.',
    ],
    'in' => [
        'code' => 30000,
        'message' => 'The selected :attribute is invalid.',
    ],
    'in_array' => [
        'code' => 31000,
        'message' => 'The :attribute field does not exist in :other.',
    ],
    'integer' => [
        'code' => 32000,
        'message' => 'The :attribute must be an integer.',
    ],
    'ip' => [
        'code' => 33000,
        'message' => 'The :attribute must be a valid IP address.',
    ],
    'ipv4' => [
        'code' => 34000,
        'message' => 'The :attribute must be a valid IPv4 address.',
    ],
    'ipv6' => [
        'code' => 35000,
        'message' => 'The :attribute must be a valid IPv6 address.',
    ],
    'json' => [
        'code' => 36000,
        'message' => 'The :attribute must be a valid JSON string.',
    ],
    'lt' => [
        'code' => 37000,
        'numeric' => [
            'code' => 37001,
            'message' => 'The :attribute must be less than :value.',
        ],
        'file' => [
            'code' => 37002,
            'message' => 'The :attribute must be less than :value kilobytes.',
        ],
        'string' => [
            'code' => 37003,
            'message' => 'The :attribute must be less than :value characters.',
        ],
        'array' => [
            'code' => 37004,
            'message' => 'The :attribute must have less than :value items.',
        ],
    ],
    'lte' => [
        'code' => 38000,
        'numeric' => [
            'code' => 38001,
            'message' => 'The :attribute must be less than or equal :value.',
        ],
        'file' => [
            'code' => 38002,
            'message' => 'The :attribute must be less than or equal :value kilobytes.',
        ],
        'string' => [
            'code' => 38003,
            'message' => 'The :attribute must be less than or equal :value characters.',
        ],
        'array' => [
            'code' => 38004,
            'message' => 'The :attribute must not have more than :value items.',
        ],
    ],
    'max' => [
        'code' => 39000,
        'numeric' => [
            'code' => 39001,
            'message' => 'The :attribute may not be greater than :max.',
        ],
        'file' => [
            'code' => 39002,
            'message' => 'The :attribute may not be greater than :max kilobytes.',
        ],
        'string' => [
            'code' => 39003,
            'message' => 'The :attribute may not be greater than :max characters.',
        ],
        'array' => [
            'code' => 39004,
            'message' => 'The :attribute may not have more than :max items.',
        ],
    ],
    'mimes' => [
        'code' => 40000,
        'message' => 'The :attribute must be a file of type: :values.',
    ],
    'mimetypes' => [
        'code' => 41000,
        'message' => 'The :attribute must be a file of type: :values.',
    ],
    'min' => [
        'code' => 42000,
        'numeric' => [
            'code' => 42001,
            'message' => 'The :attribute must be at least :min.',
        ],
        'file' => [
            'code' => 42002,
            'message' => 'The :attribute must be at least :min kilobytes.',
        ],
        'string' => [
            'code' => 42003,
            'message' => 'The :attribute must be at least :min characters.',
        ],
        'array' => [
            'code' => 42004,
            'message' => 'The :attribute must have at least :min items.',
        ],
    ],
    'not_in' => [
        'code' => 43000,
        'message' => 'The selected :attribute is invalid.',
    ],
    'not_regex' => [
        'code' => 44000,
        'message' => 'The :attribute format is invalid.',
    ],
    'numeric' => [
        'code' => 45000,
        'message' => 'The :attribute must be a number.',
    ],
    'password' => [
        'code' => 46000,
        'message' => 'The password is incorrect.',
    ],
    'present' => [
        'code' => 47000,
        'message' => 'The :attribute field must be present.',
    ],
    'regex' => [
        'code' => 48000,
        'message' => 'The :attribute format is invalid.',
    ],
    'required' => [
        'code' => 49000,
        'message' => 'The :attribute field is required.',
    ],
    'required_if' => [
        'code' => 50000,
        'message' => 'The :attribute field is required when :other is :value.',
    ],
    'required_unless' => [
        'code' => 51000,
        'message' => 'The :attribute field is required unless :other is in :values.',
    ],
    'required_with' => [
        'code' => 52000,
        'message' => 'The :attribute field is required when :values is present.',
    ],
    'required_with_all' => [
        'code' => 53000,
        'message' => 'The :attribute field is required when :values are present.',
    ],
    'required_without' => [
        'code' => 54000,
        'message' => 'The :attribute field is required when :values is not present.',
    ],
    'required_without_all' => [
        'code' => 55000,
        'message' => 'The :attribute field is required when none of :values are present.',
    ],
    'same' => [
        'code' => 56000,
        'message' => 'The :attribute and :other must match.',
    ],
    'size' => [
        'code' => 57000,
        'numeric' => [
            'code' => 57001,
            'message' => 'The :attribute must be :size.',
        ],
        'file' => [
            'code' => 57002,
            'message' => 'The :attribute must be :size kilobytes.',
        ],
        'string' => [
            'code' => 57003,
            'message' => 'The :attribute must be :size characters.',
        ],
        'array' => [
            'code' => 57004,
            'message' => 'The :attribute must contain :size items.',
        ],
    ],
    'starts_with' => [
        'code' => 58000,
        'message' => 'The :attribute must start with one of the following: :values',
    ],
    'string' => [
        'code' => 59000,
        'message' => 'The :attribute must be a string.',
    ],
    'timezone' => [
        'code' => 60000,
        'message' => 'The :attribute must be a valid zone.',
    ],
    'unique' => [
        'code' => 61000,
        'message' => 'The :attribute has already been taken.',
    ],
    'uploaded' => [
        'code' => 62000,
        'message' => 'The :attribute failed to upload.',
    ],
    'url' => [
        'code' => 63000,
        'message' => 'The :attribute format is invalid.',
    ],
    'uuid' => [
        'code' => 64000,
        'message' => 'The :attribute must be a valid UUID.',
    ],
    /*
    |--------------------------------------------------------------------------
    | Custom Validation Language Lines
    |--------------------------------------------------------------------------
    |
    | Here you may specify custom validation messages for attributes using the
    | convention "attribute.rule" to name the lines. This makes it quick to
    | specify a specific custom language line for a given attribute rule.
    |
    */

    'custom' => [
        'attribute-name' => [
            'rule-name' => 'custom-message',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Custom Validation Attributes
    |--------------------------------------------------------------------------
    |
    | The following language lines are used to swap our attribute placeholder
    | with something more reader friendly such as "E-Mail Address" instead
    | of "email". This simply helps us make our message more expressive.
    |
    */

    'attributes' => [],

];
