<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\Location;
use Illuminate\Support\Facades\Storage;

class DocumentController extends Controller
{
    public function index()
    {
        $documents = Document::with(['locations' => function ($query) {
            $query->orderBy('timestamp', 'asc'); // Sort locations by timestamp (earliest first)
        }])
        ->orderBy('created_at', 'desc')
        ->get();

        return view('documents.index', compact('documents'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'drafter' => 'required|string|max:255',
            'category' => 'required|string',
            'purpose' => 'required|string|max:255',
            'file' => 'nullable|file|mimes:pdf',
            'location' => 'required|string|max:255',
            'receiver' => 'required|string|max:255',
            'timestamp' => 'required|date',
        ]);
    
        $filePath = null;

        if ($request->hasFile('file')) {
            $filePath = $request->file('file')->store('documents', 'public');
        }
        $document = Document::create([
            ...$validated,
            'file_path' => $filePath,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);
        $location = $document->locations()->create([
            'location' => $validated['location'],
            'receiver' => $validated['receiver'],
            'timestamp' => $validated['timestamp'],
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);



        return response()->json([
            'success' => true,
            'document' => $document->load('locations')  // Ensure locations are included
        ]);
    
    }
    
public function update(Request $request, $id)
{
    $data = $request->validate([
        'category' => 'required|string',
        'name'     => 'required|string',
        'drafter'  => 'required|string',
        'purpose'  => 'required|string',
    ]);

    $document = Document::findOrFail($id);
    $document->update($data);
    $document->update([
        ...$data,
        'updated_by' => auth()->id(),
    ]);
    return response()->json(['success' => true, 'document' => $document]);
}

    public function show($id)
    {
        $document = Document::with('locations')->findOrFail($id);
        return view('documents.show', compact('document'));
    }

    public function destroy($id)
    {
        $document = Document::findOrFail($id);
        if ($document->file_path) {
            \Storage::delete('public/' . $document->file_path);
        }
        $document->delete();

        return redirect()->route('documents.index')->with('success', 'Document deleted successfully!');
    }

    public function updateFile(Request $request, $id)
    {
        $request->validate([
            'file' => 'required|mimes:pdf|max:20480', // 20MB max, only allow PDFs
        ]);

        $document = Document::findOrFail($id);

        // If there's an existing file, you might want to delete it (optional)
        if ($document->file_path && Storage::disk('public')->exists($document->file_path)) {
            Storage::disk('public')->delete($document->file_path);
        }

        // Store the new file in the "documents" directory
        $path = $request->file('file')->store('documents', 'public');

        // Update the document's file_path
        $document->file_path = $path;
        $document->save();

        return redirect()->back()->with('success', 'Document file updated successfully!');
    }

}
