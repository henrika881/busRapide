<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function getStats()
    {
        return response()->json(['stats' => 'fake stats']);
    }
    
    public function getRecentActivities()
    {
        return response()->json(['activities' => []]);
    }
}