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

    'accepted' => 'The :attribute must be accepted.[1000]',
    'accepted_if' => 'The :attribute must be accepted when :other is :value.[1001]',
    'active_url' => 'The :attribute is not a valid URL.[2000]',
    'after' => 'The :attribute must be a date after :date.[3000]',
    'after_or_equal' => 'The :attribute must be a date after or equal to :date.[4000]',
    'alpha' => 'The :attribute may only contain letters.[5000]',
    'alpha_dash' => 'The :attribute may only contain letters, numbers, dashes and underscores.[6000]',
    'alpha_num' => 'The :attribute may only contain letters and numbers.[7000]',
    'array' => 'The :attribute must be an array.[8000]',
    'before' => 'The :attribute must be a date before :date.[9000]',
    'before_or_equal' => 'The :attribute must be a date before or equal to :date.[10000]',
    'between' => [
        'numeric' => 'The :attribute must be between :min and :max.[11001]',
        'file' => 'The :attribute must be between :min and :max kilobytes.[11002]',
        'string' => 'The :attribute must be between :min and :max characters.[11003]',
        'array' => 'The :attribute must have between :min and :max items.[11004]',
    ],
    'boolean' => 'The :attribute field must be true or false.[12000]',
    'confirmed' => 'The :attribute confirmation does not match.[13000]',
    'current_password' => 'The password is incorrect.[13001]',
    'date' => 'The :attribute is not a valid date.[14000]',
    'date_equals' => 'The :attribute must be a date equal to :date.[15000]',
    'date_format' => 'The :attribute does not match the format :format.[16000]',
    'different' => 'The :attribute and :other must be different.[17000]',
    'digits' => 'The :attribute must be :digits digits.[18000]',
    'digits_between' => 'The :attribute must be between :min and :max digits.[19000]',
    'dimensions' => 'The :attribute has invalid image dimensions.[20000]',
    'distinct' => 'The :attribute field has a duplicate value.[21000]',
    'email' => 'The :attribute must be a valid email address.[22000]',
    'ends_with' => 'The :attribute must end with one of the following: :values.[23000]',
    'exists' => 'The selected :attribute is invalid.[24000]',
    'file' => 'The :attribute must be a file.[25000]',
    'filled' => 'The :attribute field must have a value.[26000]',
    'gt' => [
        'numeric' => 'The :attribute must be greater than :value.[27001]',
        'file' => 'The :attribute must be greater than :value kilobytes.[27002]',
        'string' => 'The :attribute must be greater than :value characters.[27003]',
        'array' => 'The :attribute must have more than :value items.[27004]',
    ],
    'gte' => [
        'numeric' => 'The :attribute must be greater than or equal to :value.[28001]',
        'file' => 'The :attribute must be greater than or equal to :value kilobytes.[28002]',
        'string' => 'The :attribute must be greater than or equal to :value characters.[28003]',
        'array' => 'The :attribute must have :value items or more.[28004]',
    ],
    'image' => 'The :attribute must be an image.[29000]',
    'in' => 'The selected :attribute is invalid.[30000]',
    'in_array' => 'The :attribute field does not exist in :other.[31000]',
    'integer' => 'The :attribute must be an integer.[32000]',
    'ip' => 'The :attribute must be a valid IP address.[33000]',
    'ipv4' => 'The :attribute must be a valid IPv4 address.[34000]',
    'ipv6' => 'The :attribute must be a valid IPv6 address.[35000]',
    'json' => 'The :attribute must be a valid JSON string.[36000]',
    'lt' => [
        'numeric' => 'The :attribute must be less than :value.[37001]',
        'file' => 'The :attribute must be less than :value kilobytes.[37002]',
        'string' => 'The :attribute must be less than :value characters.[37003]',
        'array' => 'The :attribute must have less than :value items.[37004]',
    ],
    'lte' => [
        'numeric' => 'The :attribute must be less than or equal to :value.[38001]',
        'file' => 'The :attribute must be less than or equal to :value kilobytes.[38002]',
        'string' => 'The :attribute must be less than or equal to :value characters.[38003]',
        'array' => 'The :attribute must not have more than :value items.[38004]',
    ],
    'max' => [
        'numeric' => 'The :attribute must not be greater than :max.[39001]',
        'file' => 'The :attribute must not be greater than :max kilobytes.[39002]',
        'string' => 'The :attribute must not be greater than :max characters.[39003]',
        'array' => 'The :attribute must not have more than :max items.[39004]',
    ],
    'mimes' => 'The :attribute must be a file of type: :values.[40000]',
    'mimetypes' => 'The :attribute must be a file of type: :values.[41000]',
    'min' => [
        'numeric' => 'The :attribute must be at least :min.[42001]',
        'file' => 'The :attribute must be at least :min kilobytes.[42002]',
        'string' => 'The :attribute must be at least :min characters.[42003]',
        'array' => 'The :attribute must have at least :min items.[42004]',
    ],
    'multiple_of' => 'The :attribute must be a multiple of :value.[43000]',
    'not_in' => 'The selected :attribute is invalid.[44000]',
    'not_regex' => 'The :attribute format is invalid.[45000]',
    'numeric' => 'The :attribute must be a number.[46000]',
    'password' => 'The password is incorrect.[47000]',
    'present' => 'The :attribute field must be present.[48000]',
    'regex' => 'The :attribute format is invalid.[49000]',
    'required' => 'The :attribute field is required.[50000]',
    'required_if' => 'The :attribute field is required when :other is :value.[51000]',
    'required_unless' => 'The :attribute field is required unless :other is in :values.[52000]',
    'required_with' => 'The :attribute field is required when :values is present.[53000]',
    'required_with_all' => 'The :attribute field is required when :values are present.[54000]',
    'required_without' => 'The :attribute field is required when :values is not present.[55000]',
    'required_without_all' => 'The :attribute field is required when none of :values are present.[56000]',
    'prohibited' => 'The :attribute field is prohibited.[70000]',
    'prohibited_if' => 'The :attribute field is prohibited when :other is :value.[70001]',
    'prohibited_unless' => 'The :attribute field is prohibited unless :other is in :values.[70002]',
    'prohibits' => 'The :attribute field prohibits :other from being present.[70003]',
    'same' => 'The :attribute and :other must match.[57000]',
    'size' => [
        'numeric' => 'The :attribute must be :size.[58001]',
        'file' => 'The :attribute must be :size kilobytes.[58002]',
        'string' => 'The :attribute must be :size characters.[58003]',
        'array' => 'The :attribute must contain :size items.[58004]',
    ],
    'starts_with' => 'The :attribute must start with one of the following: :values.[59000]',
    'string' => 'The :attribute must be a string.[60000]',
    'timezone' => 'The :attribute must be a valid timezone.[61000]',
    'unique' => 'The :attribute has already been taken.[62000]',
    'uploaded' => 'The :attribute failed to upload.[63000]',
    'url' => 'The :attribute must be a valid URL.[64000]',
    'uuid' => 'The :attribute must be a valid UUID.[65000]',

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
