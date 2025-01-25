<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;

use function PHPSTORM_META\map;

class UserController extends Controller
{
    public function register(Request $request)
    {
        if ($request->isMethod('post')) {

            $request->validate([
                'username' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'string', 'min:6', 'confirmed'],
                'first_name' => ['required', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
            ]);

            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'password' => Hash::make($request->password), // Hashing the password
                'status' => 1, // Hashing the password
                'pin' => 1234, // Hashing the password
            ]);

            // Is user ke sath related basic information create karein
            $user->user_basic_information()->create([
                'first_name' => $request->first_name,
                'last_name' => $request->last_name,
            ]);

            return redirect()->route('admin.users')->with('success', 'User created successfully!');
        } else {
            return view('admin.users.add');
        }
    }
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
        $users = User::with('user_profile_photo', 'user_basic_information')->orderBy('id', 'desc')->paginate(8); // Retrieve all users from the database
        return view('admin.users.index', compact('users'));
    }

    public function userDetails($id)
    {
        $user = User::with('user_profile_photo', 'user_basic_information')->where('id', $id)->first();
        return view('admin.users.details', compact('user'));
    }
    public function editUser(Request $request, $id)
    {
        if ($request->isMethod('POST')) {
            // Validation
            $request->validate([
                'username' => ['required', 'string', 'max:255'],
                'first_name' => ['required', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
            ]);

            // Find the user by ID
            $user = User::findOrFail($id);

            // Update user data
            $user->update([
                'username' => $request->username,
                'status' => $request->status ?? $user->status, // Keep existing status if not provided
                'pin' => $request->pin ?? $user->pin, // Keep existing pin if not provided
            ]);

            // Update or create user basic information
            $user->user_basic_information()->updateOrCreate(
                [],
                [
                    'first_name' => $request->first_name,
                    'middle_name' => $request->middle_name,
                    'last_name' => $request->last_name,
                ]
            );
            return redirect()->route('admin.users')->with('success', 'User Update successfully!');
        } else {
            $user = User::with('user_profile_photo', 'user_basic_information')->find($id);
            return view('admin.users.edit', compact('user'));
        }
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

    public function searchUser(Request $request)
    {
        $keyword = $request->input('search');
        if ($keyword == '') {
            $users = User::with('user_profile_photo', 'user_basic_information')->orderBy('id', 'desc')->paginate(8);
        } else {
            $users = User::with('user_profile_photo', 'user_basic_information')->where(function ($query) use ($keyword) {
                $query->orWhere('id', 'like', '%' . $keyword . '%')
                    ->orWhere('email', 'like', '%' . $keyword . '%');
            })->orWhereHas('user_basic_information', function ($query) use ($keyword) {
                $query->where('first_name', 'like', '%' . $keyword . '%') // Replace 'name' with the relevant column in user_basic_information
                    ->orWhere('last_name', 'like', '%' . $keyword . '%'); // Add more fields as needed
            })->paginate(10);
        }

        return view('admin.users.search', compact('users'));
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
