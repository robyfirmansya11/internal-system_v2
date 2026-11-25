<?php

return [
    'required' => ':attribute 为必填项。',
    'email' => ':attribute 必须是有效的电子邮箱地址。',
    'unique' => ':attribute 已被使用。',
    'confirmed' => ':attribute 确认不匹配。',
    'string' => ':attribute 必须是文本。',
    'boolean' => ':attribute 必须为真或假。',
    'in' => '所选的 :attribute 无效。',
    'numeric' => ':attribute 必须是数字。',
    'integer' => ':attribute 必须是整数。',
    'date' => ':attribute 必须是有效日期。',
    'exists' => '所选的 :attribute 无效。',
    'min' => ['string' => ':attribute 至少需要 :min 个字符。', 'numeric' => ':attribute 不能小于 :min。', 'array' => ':attribute 至少包含 :min 项。', 'file' => ':attribute 至少为 :min KB。'],
    'max' => ['string' => ':attribute 最多允许 :max 个字符。', 'numeric' => ':attribute 不能大于 :max。', 'array' => ':attribute 最多包含 :max 项。', 'file' => ':attribute 不能超过 :max KB。'],
    'password' => ['letters' => ':attribute 必须包含至少一个字母。', 'numbers' => ':attribute 必须包含至少一个数字。'],
    'attributes' => ['name' => '姓名', 'email' => '电子邮箱', 'password' => '密码', 'locale' => '语言'],
];
