<?php

namespace App\Http\Controllers\Web\Admin;

use App\Actions\Jetstream\DeleteUser;
use App\Http\Controllers\Controller;
use App\Http\Requests\Web\User\UserFormRequest;
use App\Models\User\User;
use App\Repositories\User\UserRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class UserController extends Controller
{
    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * UserController Constructor
     *
     * @param UserRepository $userRepository
     *
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = $this->userRepository->get();

        return Inertia::render('Admin/Users/Index', [
            'users' => $users,
            'filters' => request()->all(['search']),
        ]);
    }

    /**
     * Edit resource.
     *
     * @param User $user
     * @return \Inertia\Response
     */
    public function edit(User $user)
    {
        $user = $this->userRepository->getById($user->id, false);

        return Inertia::render('Admin/Users/Form', [
            'isEdit' => true,
            'user' => $user,
        ]);
    }

    /**
     * Update resource.
     *
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UserFormRequest $request, User $user)
    {
        if($user->id == auth()->user()->getAuthIdentifier()) {

            if(request()->get('deactivated') || !request()->get('kyc_verified') || !request()->get('email_verified')) {
                return Redirect::back()->withErrors(['deactivated' => 'You can not deactivate yourself or remove kyc and email status!']);
            }
        }

        $data = [
            'deactivated' => $request->get('deactivated'),
        ];

        if(request()->get('email_verified') && !$user->email_verified_at) {
            $data['email_verified_at'] = Carbon::now();
        } elseif(!request()->get('email_verified') && $user->email_verified_at) {
            $data['email_verified_at'] = null;
        }

        if(request()->get('kyc_verified') && !$user->kyc_verified_at) {
            $data['kyc_verified_at'] = Carbon::now();
        } elseif(!request()->get('kyc_verified') && $user->kyc_verified_at) {
            $data['kyc_verified_at'] = null;
        }

        $this->userRepository->update($user->id, $data);

        return Redirect::route('admin.users');
    }

    /**
     * Destroy resource.
     *
     * @param User $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(User $user)
    {
        $deleted = (new DeleteUser())->delete($user);

        if(!$deleted) {
            return Redirect::back()->withErrors(['deactivated' => 'You can not delete admin user']);
        }

        return Redirect::route('admin.users');
    }
}
