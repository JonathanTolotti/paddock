<?php

namespace App\Http\Controllers;

use App\Http\Requests\Users\StoreUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;
use Illuminate\Routing\Controller as BaseController;


class UserController extends BaseController
{
    public function __construct()
    {
        $this->middleware('permission:manage_users');
    }
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $users = User::with('roles')->paginate(15);

        return Inertia::render('Users/Index', [
            'users' => $users,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): Response
    {
        return Inertia::render('Users/Create', [
            'roles' => Role::all(['id', 'name']),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreUserRequest $request, UserService $userService): RedirectResponse
    {
        $userService->create($request->validated());
        return redirect()->route('users.index')->with('success', 'Usuário criado com sucesso.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user): Response
    {
        $user->load('roles');
        return Inertia::render('Users/Edit', [
            'user' => $user,
            'roles' => Role::all(['id', 'name']),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateUserRequest $request, User $user, UserService $userService): RedirectResponse
    {
        $userService->update($user, $request->validated());
        return redirect()->route('users.index')->with('success', 'Usuário atualizado com sucesso.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user, UserService $userService): RedirectResponse
    {
        try {
            $userService->delete($user);
        } catch (\Exception $e) {
            return redirect()->route('users.index')->with('error', $e->getMessage());
        }

        return redirect()->route('users.index')->with('success', 'Usuário excluído com sucesso.');
    }
}
