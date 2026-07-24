<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use Illuminate\View\View;

class AuditoriaController extends Controller
{
    public function index(): View
    {
        $logs = AuditLog::with('usuario')->orderBy('created_at', 'desc')->paginate(25);
        return view('admin.auditoria.index', compact('logs'));
    }
}
