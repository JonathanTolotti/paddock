<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function create(array $data): User
    {
        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);
        $user->assignRole($data['role']);
        return $user;
    }

    public function update(User $user, array $data): User
    {
        if (empty($data['password'])) {
            unset($data['password']);
        } else {
            $data['password'] = Hash::make($data['password']);
        }

        $user->update($data);
        $user->syncRoles($data['role']);
        return $user;
    }
    public function delete(User $user): void
    {
        if (auth()->id() === $user->id) {
            throw new \Exception('Você não pode excluir a si mesmo.');
        }

        if ($user->id === 1) {
            throw new \Exception('Não é possível excluir o usuário administrador principal.');
        }

        $user->delete();
    }

}
