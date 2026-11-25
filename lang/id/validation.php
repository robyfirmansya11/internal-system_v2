<?php

return [
    'required' => ':attribute wajib diisi.',
    'email' => ':attribute harus berupa alamat email yang valid.',
    'unique' => ':attribute sudah digunakan.',
    'confirmed' => 'Konfirmasi :attribute tidak cocok.',
    'string' => ':attribute harus berupa teks.',
    'boolean' => ':attribute harus bernilai benar atau salah.',
    'in' => ':attribute yang dipilih tidak valid.',
    'numeric' => ':attribute harus berupa angka.',
    'integer' => ':attribute harus berupa bilangan bulat.',
    'date' => ':attribute harus berupa tanggal yang valid.',
    'exists' => ':attribute yang dipilih tidak valid.',
    'min' => ['string' => ':attribute minimal :min karakter.', 'numeric' => ':attribute minimal :min.', 'array' => ':attribute minimal :min item.', 'file' => ':attribute minimal :min kilobyte.'],
    'max' => ['string' => ':attribute maksimal :max karakter.', 'numeric' => ':attribute maksimal :max.', 'array' => ':attribute maksimal :max item.', 'file' => ':attribute maksimal :max kilobyte.'],
    'password' => ['letters' => ':attribute harus mengandung minimal satu huruf.', 'numbers' => ':attribute harus mengandung minimal satu angka.'],
    'attributes' => ['name' => 'Nama', 'email' => 'Email', 'password' => 'Kata sandi', 'locale' => 'Bahasa'],
];
