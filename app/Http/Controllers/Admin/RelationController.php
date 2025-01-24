<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RelationType;
use Illuminate\Http\Request;

class RelationController extends Controller
{
    public function index()
    {
        $relations = RelationType::paginate(8);
        return view('admin.relation.index', compact('relations'));
    }

    public function edit(Request $request, $id)
    {
        if ($request->isMethod('post')) {
            $this->validate($request, [
                'name' => ['required', 'string', 'max:255'],
                'description' => ['required', 'string', 'max:255'],
            ]);
            $data = $request->all();
            RelationType::where('id', $id)->update($data);
            return redirect()->route('admin.relation-types')->with('success', 'Relation updated successfully');
        }
        $relation = RelationType::find($id);
        return view('admin.relation.edit', compact('relation'));
    }
}
