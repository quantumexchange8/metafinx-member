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

    'accepted' => '必須接受 :attribute。',
    'accepted_if' => '當 :other 為 :value 時，必須接受 :attribute。',
    'active_url' => ':attribute 不是一個有效的 URL。',
    'after' => ':attribute 必須是 :date 之後的日期。',
    'after_or_equal' => ':attribute 必須是 :date 之後或等于 :date 的日期。',
    'alpha' => ':attribute 只能包含字母。',
    'alpha_dash' => ':attribute 只能包含字母、數字、破折號和下劃線。',
    'alpha_num' => ':attribute 只能包含字母和數字。',
    'array' => ':attribute 必須是一個數組。',
    'ascii' => ':attribute 只能包含單字節的字母數字字符和符號。',
    'before' => ':attribute 必須是 :date 之前的日期。',
    'before_or_equal' => ':attribute 必須是 :date 之前或等于 :date 的日期。',
    'between' => [
        'array' => ':attribute 必須包含 :min 到 :max 個項目。',
        'file' => ':attribute 必須在 :min 到 :max 千字節之間。',
        'numeric' => ':attribute 必須在 :min 到 :max 之間。',
        'string' => ':attribute 必須在 :min 到 :max 個字符之間。',
    ],
    'boolean' => ':attribute 字段必須是 true 或 false。',
    'confirmed' => ':attribute 確認不匹配。',
    'current_password' => '密碼不正確。',
    'date' => ':attribute 不是一個有效的日期。',
    'date_equals' => ':attribute 必須等于 :date。',
    'date_format' => ':attribute 與格式 :format 不匹配。',
    'decimal' => ':attribute 必須有 :decimal 位小數。',
    'declined' => ':attribute 必須被拒絕。',
    'declined_if' => '當 :other 為 :value 時，:attribute 必須被拒絕。',
    'different' => ':attribute 和 :other 必須不同。',
    'digits' => ':attribute 必須有 :digits 位數字。',
    'digits_between' => ':attribute 必須在 :min 到 :max 位數字之間。',
    'dimensions' => ':attribute 圖像尺寸無效。',
    'distinct' => ':attribute 字段有重複值。',
    'doesnt_end_with' => ':attribute 不能以以下內容之一結尾：:values。',
    'doesnt_start_with' => ':attribute 不能以以下內容之一開頭：:values。',
    'email' => ':attribute 必須是一個有效的電子郵件地址。',
    'ends_with' => ':attribute 必須以以下之一結尾：:values。',
    'enum' => '所選 :attribute 無效。',
    'exists' => '所選 :attribute 無效。',
    'file' => ':attribute 必須是一個文件。',
    'filled' => ':attribute 字段必須有一個值。',
    'gt' => [
        'array' => ':attribute 必須包含多于 :value 個項目。',
        'file' => ':attribute 必須大于 :value 千字節。',
        'numeric' => ':attribute 必須大于 :value。',
        'string' => ':attribute 必須多于 :value 個字符。',
    ],
    'gte' => [
        'array' => ':attribute 必須包含 :value 個項目或更多。',
        'file' => ':attribute 必須大于或等于 :value 千字節。',
        'numeric' => ':attribute 必須大于或等于 :value。',
        'string' => ':attribute 必須多于或等于 :value 個字符。',
    ],
    'image' => ':attribute 必須是一張圖片。',
    'in' => '所選 :attribute 無效。',
    'in_array' => ':attribute 字段不存在于 :other 中。',
    'integer' => ':attribute 必須是整數。',
    'ip' => ':attribute 必須是有效的 IP 地址。',
    'ipv4' => ':attribute 必須是有效的 IPv4 地址。',
    'ipv6' => ':attribute 必須是有效的 IPv6 地址。',
    'json' => ':attribute 必須是有效的 JSON 字符串。',
    'lowercase' => ':attribute 必須是小寫字母。',
    'lt' => [
        'array' => ':attribute 必須包含少于 :value 個項目。',
        'file' => ':attribute 必須小于 :value 千字節。',
        'numeric' => ':attribute 必須小于 :value。',
        'string' => ':attribute 必須少于 :value 個字符。',
    ],
    'lte' => [
        'array' => ':attribute 必須不包含多于 :value 個項目。',
        'file' => ':attribute 必須小于或等于 :value 千字節。',
        'numeric' => ':attribute 必須小于或等于 :value。',
        'string' => ':attribute 必須少于或等于 :value 個字符。',
    ],
    'mac_address' => ':attribute 必須是有效的 MAC 地址。',
    'max' => [
        'array' => ':attribute 不能包含多于 :max 個項目。',
        'file' => ':attribute 不能大于 :max 千字節。',
        'numeric' => ':attribute 不能大于 :max。',
        'string' => ':attribute 不能多于 :max 個字符。',
    ],
    'max_digits' => ':attribute 不能超過 :max 位數字。',
    'mimes' => ':attribute 必須是以下類型之一的文件：:values。',
    'mimetypes' => ':attribute 必須是以下類型之一的文件：:values。',
    'min' => [
        'array' => ':attribute 必須包含至少 :min 個項目。',
        'file' => ':attribute 必須至少為 :min 千字節。',
        'numeric' => ':attribute 必須至少為 :min。',
        'string' => ':attribute 必須至少為 :min 個字符。',
    ],
    'min_digits' => ':attribute 必須至少包含 :min 位數字。',
    'multiple_of' => ':attribute 必須是 :value 的倍數。',
    'not_in' => '所選 :attribute 無效。',
    'not_regex' => ':attribute 格式無效。',
    'numeric' => ':attribute 必須是一個數字。',
    'password' => [
        'letters' => ':attribute 必須包含至少一個字母。',
        'mixed' => ':attribute 必須包含至少一個大寫字母和一個小寫字母。',
        'numbers' => ':attribute 必須包含至少一個數字。',
        'symbols' => ':attribute 必須包含至少一個符號。',
        'uncompromised' => '給定的 :attribute 已經出現在數據泄漏中，請選擇其他 :attribute。',
    ],
    'present' => ':attribute 字段必須存在。',
    'prohibited' => ':attribute 字段禁止使用。',
    'prohibited_if' => '當 :other 為 :value 時，:attribute 字段禁止使用。',
    'prohibited_unless' => ':attribute 字段除非 :other 在 :values 中，否則禁止使用。',
    'prohibits' => ':attribute 字段禁止 :other 出現。',
    'regex' => ':attribute 格式無效。',
    'required' => ':attribute 字段是必需的。',
    'required_array_keys' => ':attribute 字段必須包含以下條目：:values。',
    'required_if' => '當 :other 為 :value 時，:attribute 字段是必需的。',
    'required_if_accepted' => '當 :other 被接受時，:attribute 字段是必需的。',
    'required_unless' => '除非 :other 在 :values 中，否則 :attribute 字段是必需的。',
    'required_with' => '當 :values 存在時，:attribute 字段是必需的。',
    'required_with_all' => '當 :values 都存在時，:attribute 字段是必需的。',
    'required_without' => '當 :values 不存在時，:attribute 字段是必需的。',
    'required_without_all' => '當 :values 都不存在時，:attribute 字段是必需的。',
    'same' => ':attribute 和 :other 必須匹配。',
    'size' => [
        'array' => ':attribute 必須包含 :size 個項目。',
        'file' => ':attribute 必須為 :size 千字節。',
        'numeric' => ':attribute 必須為 :size。',
        'string' => ':attribute 必須為 :size 個字符。',
    ],
    'starts_with' => ':attribute 必須以以下之一開頭：:values。',
    'string' => ':attribute 必須是一個字符串。',
    'timezone' => ':attribute 必須是有效的時區。',
    'unique' => ':attribute 已經被占用。',
    'uploaded' => ':attribute 上傳失敗。',
    'uppercase' => ':attribute 必須是大寫字母。',
    'url' => ':attribute 必須是有效的 URL。',
    'ulid' => ':attribute 必須是有效的 ULID。',
    'uuid' => ':attribute 必須是有效的 UUID。',

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
            'rule-name' => '自定義消息',
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
