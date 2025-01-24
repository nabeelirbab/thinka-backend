<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

class UserController extends Controller
{
    public function login(Request $request)
    {
        if ($request->isMethod('post')) {

            $request->validate([
                'email' => 'required|email',
                'password' => 'required|min:6',
            ]);

            $user = User::where('email', $request->email)->first();

            if ($user && Hash::check($request->password, $user->password)) {
                // Store the entire user object in the session
                Session::put('user', $user);

                return redirect()->route('admin.dashboard')->with('success', 'Logged in successfully!');
            }

            return back()->withErrors([
                'email' => 'Invalid credentials.',
            ])->withInput();
        }

        return view('admin.login');
    }


    public function index()
    {
        return view('admin.index');
    }

    /**
     * Display the list of all users (example functionality).
     */
    public function manageUsers()
    {
        $users = User::with('user_profile_photo', 'user_basic_information')->paginate(8); // Retrieve all users from the database
        return view('admin.users.index', compact('users'));
    }

    public function userDetails($id)
    {
        $user = User::with('user_profile_photo', 'user_basic_information')->where('id', $id)->first();
        return view('admin.users.details', compact('user'));
    }
    public function editUser($id)
    {
        $user = User::find($id);
        return view('admin.users.edit', compact('user'));
    }

    public function updateUserDetails(Request $request, $id)
    {
        $this->validate($request, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $id],
        ]);

        $user = User::find($id);
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->save();

        return redirect()->route('admin.users.details', $id)->with('success', 'User details updated successfully.');
    }

    public function updateUserPassword(Request $request, $id)
    {
        if ($request->isMethod('post')) {
            $this->validate($request, [
                'password' => ['required', 'string', 'min:6', 'confirmed'],
            ]);

            $user = User::find($id);
            $user->password = Hash::make($request->password);
            $user->save();

            return redirect()->route('admin.users')->with('success', 'User password updated successfully.');
        } else {
            return view('admin.users.update_password', compact('id'));
        }
    }

    public function createUser()
    {
        return view('admin.users.create');
    }
    public function storeUser(Request $request)
    {
        $this->validate($request, [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ]);

        $user = new User();
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->save();

        return redirect()->route('admin.users.index')->with('success', 'User created successfully.');
    }


    public function changeStatus(Request $request, $id)
    {
        User::where('id', $id)->update(['status' => $request->get('status')]);
        Session::flash('success', 'Account status change successfully!!');
        return Redirect::back();
    }

    /**
     * Delete a specific user (example functionality).
     */
    public function deleteUser($id)
    {
        $user = User::find($id);

        if (!$user) {
            return redirect()->back()->with('error', 'User not found.');
        }

        $user->delete();
        return redirect()->back()->with('success', 'User deleted successfully.');
    }


    public function logout()
    {
        // Auth::logout();
        Session::flush();
        return redirect()->route('admin.login')->with('success', 'Logged out successfully!');
    }
}
