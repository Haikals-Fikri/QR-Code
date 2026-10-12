<?php

namespace App\Http\Controllers\Admin;

use App\Exports\StudentTemplateExport;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ImportStudentsRequest;
use App\Imports\StudentsImport;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class StudentImportController extends Controller
{
    /**
     * Tampilkan form import siswa.
     */
    public function create(): View
    {
        return view('admin.students.import');
    }

    /**
     * Proses file import siswa.
     */
    public function store(ImportStudentsRequest $request): RedirectResponse
    {
        $import = new StudentsImport;

        Excel::import($import, $request->file('file'));

        $failures = $import->failures();

        if ($failures->isNotEmpty()) {
            $messages = $failures->map(function ($failure) {
                return "Baris {$failure->row()}: ".implode(', ', $failure->errors());
            })->all();

            return redirect()
                ->route('admin.students.import.create')
                ->with('status', "{$import->importedCount} siswa berhasil diimport, ".count($messages).' baris gagal.')
                ->with('import_errors', $messages);
        }

        return redirect()
            ->route('admin.students.index')
            ->with('status', "{$import->importedCount} siswa berhasil diimport.");
    }

    /**
     * Unduh template file import.
     */
    public function template(): BinaryFileResponse
    {
        return Excel::download(new StudentTemplateExport, 'template-import-siswa.xlsx');
    }
}
