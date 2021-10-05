<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class UserPolicy
{
    use HandlesAuthorization;

    public function before(User $userAuth, $ability) {
        if($userAuth->hasRole('admin')) {
            return true;
        }
    }

    public function view(User $userAuth, $user) {
        return ($user->id === $userAuth->id);
    }

    public function update(User $userAuth, $user) {
        return ($user->id === $userAuth->id);
    }

    public function delete(User $userAuth, $user) {
        return false;
    }
    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }
}
