<?php

namespace App\Http\Controllers;

use App\Jobs\ImportExcelJob;
use App\Models\ExcelField;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;

class ExcelFieldController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Fetch all ExcelField entries from the database and order them by date
        $excelFields = ExcelField::orderBy('date', 'desc')->paginate(20);

        // Return the view with the data
        return view('excelfield.index', compact('excelFields'));
    }

    public function importForm()
    {
        return view('excelfield.importForm');
    }
}
