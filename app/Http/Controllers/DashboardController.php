<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Routing\Controller;

class DashboardController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     */
    public function index()
    {
        $user = Auth::user();
        
        // Data statistik untuk dashboard
        $stats = [
            'total_users' => User::count(),
            'active_users' => User::active()->count(),
            'admin_count' => User::where('role', 'admin')->count(),
            'staff_count' => User::where('role', 'staff')->count(),
        ];

        // Data untuk chart atau grafik (contoh)
        $monthlyData = [
            'labels' => ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
            'data' => [12, 19, 3, 5, 2, 3]
        ];

        return view('dashboard_index', compact('user', 'stats', 'monthlyData'));
    }

    /**
     * Show user profile
     */
    public function profile()
    {
        $user = Auth::user();
        return view('dashboard.profile', compact('user'));
    }

    /**
     * Update user profile
     */
    public function updateProfile(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'jabatan' => 'nullable|string|max:255',
            'divisi' => 'nullable|string|max:255',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'jabatan' => $request->jabatan,
            'divisi' => $request->divisi,
        ]);

        return redirect()->back()->with('success', 'Profile berhasil diperbarui.');
    }
}