<?php
namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;


class AdminController extends Controller
{
    public function getUsers()
    {
        $user = auth()->user(); 
        $adminCompanyId = auth()->user()->company_id;

        $users = User::where('company_id', $adminCompanyId)
        ->with('roles') 
        ->get();

        return response()->json($users);
    }

    public function getUsersWithReports()
{
    $adminCompanyId = auth()->user()->company_id;

    $users = User::where('company_id', $adminCompanyId)
        ->with(['appointments' => function ($query) {
            $query->whereNotNull('report'); // Nur Termine mit Berichten
        }])
        ->get();

    return response()->json($users);
}
}