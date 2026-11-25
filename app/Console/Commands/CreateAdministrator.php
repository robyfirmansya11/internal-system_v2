<?php

namespace App\Console\Commands;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class CreateAdministrator extends Command
{
    protected $signature = 'app:create-administrator';

    protected $description = 'Interactively create a system administrator without default credentials';

    public function handle(): int
    {
        $data = [
            'name' => $this->ask('Administrator name'),
            'email' => $this->ask('Administrator email'),
            'password' => $this->secret('Password (at least 12 characters, letters, numbers and symbols)'),
            'password_confirmation' => $this->secret('Confirm password'),
        ];

        $validator = Validator::make($data, [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Password::min(12)->letters()->numbers()->symbols()],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return self::FAILURE;
        }

        User::create([
            'name' => $data['name'], 'email' => $data['email'],
            'password' => $data['password'], 'level' => Role::Superadmin, 'jabatan' => 'IT',
        ]);
        $this->info('Administrator created. Complete employee and department assignments in the admin panel.');

        return self::SUCCESS;
    }
}
