<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use App\Models\Location;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Exceptions\HttpResponseException;

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

        try{
            
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
        

        } catch (\Throwable $e) {

            return $e;

        }



    }

public function uploadChunk(Request $request)
{
    // Check if the request contains file chunk data
    if (!$request->filled('chunkData')) {
        // No file is being uploaded; validate only the non-file fields
        $validated = $request->validate([
            'name'      => 'required|string|max:255',
            'drafter'   => 'required|string|max:255',
            'category'  => 'required|string',
            'purpose'   => 'required|string|max:255',
            'location'  => 'required|string|max:255',
            'receiver'  => 'required|string|max:255',
            'timestamp' => 'required|date',
        ]);

        // Create the document record with no file (file_path is null)
        $document = Document::create([
            'name'       => $validated['name'],
            'drafter'    => $validated['drafter'],
            'category'   => $validated['category'],
            'purpose'    => $validated['purpose'],
            'file_path'  => null,
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        // Create an associated location record
        $document->locations()->create([
            'location'   => $validated['location'],
            'receiver'   => $validated['receiver'],
            'timestamp'  => $validated['timestamp'],
            'created_by' => auth()->id(),
            'updated_by' => auth()->id(),
        ]);

        return response()->json([
            'success'  => true,
            'document' => $document->load('locations')
        ]);
    }

    // Otherwise, we assume this is a chunked file upload.
    $validated = $request->validate([
        'name'         => 'required|string|max:255',
        'drafter'      => 'required|string|max:255',
        'category'     => 'required|string',
        'purpose'      => 'required|string|max:255',
        'location'     => 'required|string|max:255',
        'receiver'     => 'required|string|max:255',
        'timestamp'    => 'required|date',
        'fileName'     => 'required|string',
        'fileType'     => 'required|string',
        'fileSize'     => 'required|integer',
        'chunkData'    => 'required|string',
        'chunkIndex'   => 'required|integer',
        'totalChunks'  => 'required|integer',
    ]);

    // Set up a temporary directory for storing chunks
    $tempDir = storage_path('app/uploads/chunks/');
    if (!is_dir($tempDir)) {
        mkdir($tempDir, 0777, true);
    }

    // Create a unique identifier for this file upload (based on fileName and user ID)
    $uniqueFileName = preg_replace('/\s+/', '_', $validated['fileName']) . '_' . auth()->id();
    $chunkIndex = $validated['chunkIndex'];
    $chunkFilePath = $tempDir . $uniqueFileName . '_' . $chunkIndex . '.part';

    // Remove data URL prefix if present
    $chunkData = $validated['chunkData'];
    if (preg_match('/^data:(.*);base64,/', $chunkData, $matches)) {
        $chunkData = substr($chunkData, strpos($chunkData, ',') + 1);
    }

    $decodedData = base64_decode($chunkData);
    if ($decodedData === false) {
        return response()->json(['success' => false, 'message' => 'Invalid chunk data'], 400);
    }

    // Save the chunk to a temporary file
    file_put_contents($chunkFilePath, $decodedData);

    // Check if all chunks have been uploaded by counting part files
    $totalChunks = $validated['totalChunks'];
    $chunks = glob($tempDir . $uniqueFileName . '_*.part');
    if (count($chunks) == $totalChunks) {
        // All chunks received; reassemble the file
        // Here, we're assuming the file is a PDF. Adjust the extension as needed.
        $finalFileName = time() . '_' . preg_replace('/\s+/', '_', $validated['fileName']) . '.pdf';
        $finalFilePath = storage_path('app/public/documents/') . $finalFileName;
        $finalFile = fopen($finalFilePath, 'wb');

        // Sort the chunk files in natural order by chunk index
        natsort($chunks);
        foreach ($chunks as $chunk) {
            fwrite($finalFile, file_get_contents($chunk));
        }
        fclose($finalFile);

        // Clean up temporary chunk files
        foreach ($chunks as $chunk) {
            unlink($chunk);
        }
        $filePath = 'documents/' . $finalFileName;
    } else {
        // Not all chunks have been uploaded yet; return success for this chunk upload
        return response()->json(['success' => true, 'chunk_uploaded' => true]);
    }

    // Create the document record with the assembled file path
    $document = Document::create([
        'name'       => $validated['name'],
        'drafter'    => $validated['drafter'],
        'category'   => $validated['category'],
        'purpose'    => $validated['purpose'],
        'file_path'  => $filePath,
        'created_by' => auth()->id(),
        'updated_by' => auth()->id(),
    ]);

    // Create an associated location record
    $document->locations()->create([
        'location'   => $validated['location'],
        'receiver'   => $validated['receiver'],
        'timestamp'  => $validated['timestamp'],
        'created_by' => auth()->id(),
        'updated_by' => auth()->id(),
    ]);

    return response()->json([
        'success'  => true,
        'document' => $document->load('locations')
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

public function downloadFile($id)
{
    $document = Document::findOrFail($id);
    $path = storage_path('app/public/' . $document->file_path);

    if (!file_exists($path)) {
        abort(404, 'File not found.');
    }

    // Set the chunk size to 100 * 1024 bytes (i.e., 100KB)
    $chunkSize = 100 * 1024;

    $stream = function() use ($path, $chunkSize) {
        $handle = fopen($path, 'rb');
        if ($handle === false) {
            return;
        }
        while (!feof($handle)) {
            echo fread($handle, $chunkSize);
            // Flush any output buffers
            ob_flush();
            flush();
        }
        fclose($handle);
    };

    return response()->stream($stream, 200, [
        'Content-Type' => mime_content_type($path),
        'Content-Length' => filesize($path),
        'Content-Disposition' => 'inline; filename="' . basename($path) . '"',
        'Accept-Ranges' => 'bytes',
    ]);
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
