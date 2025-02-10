<?php

namespace App\Http\Controllers;
// require 'vendor/autoload.php'; 
use Illuminate\Http\Request;
use App\Models\Location;
use Carbon\Carbon;
class LocationController extends Controller
{
public function store(Request $request)
{

    $request->validate([
        'document_id' => 'required|exists:documents,id',
        'location'    => 'required|string',
        'receiver'    => 'required|string',
        'dispatcher'  => 'required|string',  // Ensure this matches your form field name
        'timestamp'   => 'required|date',
    ]);

    $location = Location::create([
        'document_id' => $request->document_id,
        'location'    => $request->location,
        'receiver'    => $request->receiver,
        'dispatcher'  => $request->dispatcher, // Note: use 'dispatcher' here
        'timestamp'   => $request->timestamp,
        'created_by'  => auth()->id(),
        'created_at'  => Carbon::now('Asia/Manila')->format('Y-m-d H:i'),
        'updated_by'  => auth()->id(),
        'updated_at'  => null,
    ]);

    // Check if the request expects JSON (AJAX)
    if ($request->ajax()) {
        return response()->json(['success' => true, 'message' => 'Location added successfully!','data' => $location]);
    }

    return redirect()->route('documents.index')->with('success', 'Location added successfully!');
}

    
    
    public function update(Request $request, Location $location)
    {
        $validatedData = $request->validate([
            'location' => 'required|string',
            'receiver' => 'required|string',
            'dispatcher' => 'required|string',
            'timestamp' => 'required|date',
        ]);
    
        $location->update($validatedData);
        $location->update([
            ...$validatedData,
            'updated_by' => auth()->id(),
            'updated_at' => Carbon::now('Asia/Manila')->format('Y-m-d H:i'),
        ]);
        return response()->json([
            'success' => true,
            'message' => 'Routing history updated successfully!',
            'location' => $location
        ]);
    }
    
    
    
    public function destroy(Request $request, $id)
    {
        try {
            $location = Location::findOrFail($id); // Ensure the location exists
            $location->delete(); // Delete the location
    
            return response()->json([
                'success' => true,
                'message' => 'Routing history deleted successfully!'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete routing history: ' . $e->getMessage()
            ], 500);
        }
    }
    
    
    
}
