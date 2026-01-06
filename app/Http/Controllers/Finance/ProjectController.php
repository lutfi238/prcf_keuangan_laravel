<?php

namespace App\Http\Controllers\Finance;

use App\Enums\ProjectStatus;
use App\Http\Controllers\Controller;
use App\Models\Proyek;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    /**
     * Display a listing of projects.
     */
    public function index(Request $request)
    {
        $query = Proyek::query();

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status_proyek', $request->status);
        }

        // Search by name or code
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama_proyek', 'like', "%{$search}%")
                  ->orWhere('kode_proyek', 'like', "%{$search}%")
                  ->orWhere('donor', 'like', "%{$search}%");
            });
        }

        $projects = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('finance.projects.index', [
            'projects' => $projects,
            'statuses' => ProjectStatus::cases(),
        ]);
    }

    /**
     * Show the form for creating a new project.
     */
    public function create()
    {
        return view('finance.projects.form', [
            'project' => null,
            'statuses' => ProjectStatus::cases(),
        ]);
    }

    /**
     * Store a newly created project.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode_proyek' => 'required|string|max:50|unique:proyek,kode_proyek',
            'nama_proyek' => 'required|string|max:255',
            'status_proyek' => 'required|in:' . implode(',', ProjectStatus::values()),
            'donor' => 'nullable|string|max:255',
            'nilai_anggaran' => 'nullable|numeric|min:0',
            'periode_mulai' => 'nullable|date',
            'periode_selesai' => 'nullable|date|after_or_equal:periode_mulai',
            'rekening_khusus' => 'nullable|string|max:100',
        ]);

        Proyek::create($request->only([
            'kode_proyek', 'nama_proyek', 'status_proyek', 'donor',
            'nilai_anggaran', 'periode_mulai', 'periode_selesai', 'rekening_khusus'
        ]));

        return redirect()->route('finance.projects.index')
            ->with('success', 'Proyek berhasil dibuat.');
    }

    /**
     * Display the specified project.
     */
    public function show(Proyek $project)
    {
        $project->load(['proposals', 'bankBookHeaders', 'receivableHeaders']);

        return view('finance.projects.show', [
            'project' => $project,
        ]);
    }

    /**
     * Show the form for editing the specified project.
     */
    public function edit(Proyek $project)
    {
        return view('finance.projects.form', [
            'project' => $project,
            'statuses' => ProjectStatus::cases(),
        ]);
    }

    /**
     * Update the specified project.
     */
    public function update(Request $request, Proyek $project)
    {
        $request->validate([
            'kode_proyek' => 'required|string|max:50|unique:proyek,kode_proyek,' . $project->kode_proyek . ',kode_proyek',
            'nama_proyek' => 'required|string|max:255',
            'status_proyek' => 'required|in:' . implode(',', ProjectStatus::values()),
            'donor' => 'nullable|string|max:255',
            'nilai_anggaran' => 'nullable|numeric|min:0',
            'periode_mulai' => 'nullable|date',
            'periode_selesai' => 'nullable|date|after_or_equal:periode_mulai',
            'rekening_khusus' => 'nullable|string|max:100',
        ]);

        $project->update($request->only([
            'kode_proyek', 'nama_proyek', 'status_proyek', 'donor',
            'nilai_anggaran', 'periode_mulai', 'periode_selesai', 'rekening_khusus'
        ]));

        return redirect()->route('finance.projects.index')
            ->with('success', 'Proyek berhasil diupdate.');
    }

    /**
     * Remove the specified project.
     */
    public function destroy(Proyek $project)
    {
        // Check if project has related data
        if ($project->proposals()->exists()) {
            return back()->with('error', 'Proyek tidak dapat dihapus karena masih memiliki proposal terkait.');
        }

        if ($project->bankBookHeaders()->exists()) {
            return back()->with('error', 'Proyek tidak dapat dihapus karena masih memiliki buku bank terkait.');
        }

        $project->delete();

        return redirect()->route('finance.projects.index')
            ->with('success', 'Proyek berhasil dihapus.');
    }
}
