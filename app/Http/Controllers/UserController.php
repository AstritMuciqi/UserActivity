<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getUsersByRole(Request $request)
    {
        $roleName = $request->query('roleName');

        $users = User::when($roleName, function ($query, $roleName) {
            return $query->where('role', $roleName);
        })->get();

        return response()->json(['users' => $users]);
    }

    public function switchRole(Request $request, User $user)
    {
        $newRole = $request->input('newRole');

        $user->role = $newRole;

        try {
            $user->save();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to switch role', 'message' => $e->getMessage()], 500);
        }

        return response()->json(['message' => 'Role switched successfully', 'user' => $user]);
    }

    public function deleteUser(User $user)
    {
        try {
            $user->delete();
            return response()->json(['message' => 'User deleted successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete user', 'message' => $e->getMessage()], 500);
        }
    }

    public function getUsersWithBalance($userEmail)
    {
        $userWithBalance = User::select('bilance')
            ->where('email', $userEmail)
            ->first();
        return response()->json(['users' => $userWithBalance]);
    }

    public function updateBalance(Request $request, User $user)
    {
        $request->validate([
            'editedBalance' => 'required|numeric',
        ]);

        $editedBalance = $request->input('editedBalance');


        $user->bilance = $editedBalance;

        try {
            $user->save();
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to update balance', 'message' => $e->getMessage()], 500);
        }

        return response()->json(['message' => 'User balance updated successfully']);



    }


    public function sendBalance(Request $request, $selectedUser)
    {

        $balance = $request->input('valueBilanci');
        $userAuth = $request->input('emailElement');



        $user = User::find($selectedUser);
        $user1 = User::where('email', $userAuth)->first();




//        dd($user);
        if ($user && $user1) {
            $user->bilance += $balance;
            $user->save();

            $user1->bilance = 0;
            $user1->save();
            return response()->json(['message' => 'Balance updated successfully']);
        } else {
            return response()->json(['message' => 'User not found'], 404);
        }
    }
}
