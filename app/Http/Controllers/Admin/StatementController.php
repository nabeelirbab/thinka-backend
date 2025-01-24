<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StatementType;
use Illuminate\Http\Request;

class StatementController extends Controller
{

    public function index()
    {
        $statements = StatementType::paginate(8);
        return view('admin.statement.index', compact('statements'));
    }

    public function edit(Request $request, $id)
    {
        if ($request->isMethod('POST')) {
            $this->validate($request, [
                'explanation' => ['required', 'string'],
                'description' => ['required', 'string'],
            ]);
            $data = $request->all();
            StatementType::where('id', $id)->update($data);
            return redirect()->route('admin.statemnt-types')->with('success', 'Statement updated successfully.');
        }

        $statement = StatementType::find($id);
        return view('admin.statement.edit', compact('statement'));
    }
}
